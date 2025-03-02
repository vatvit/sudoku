<?php

namespace App\Interface\Controller\Sudoku\Mapper;

use App\Domain\Sudoku\Service\Dto\CellDto;
use App\Domain\Sudoku\Service\Dto\CellGroupDto;
use App\Infrastructure\Entity\SudokuGameInstance;
use App\Interface\Controller\Sudoku\Dto\InstanceCreateResponseDto;
use App\Interface\Controller\Sudoku\Dto\InstanceGetResponseDto;

class InstanceResponseMapper
{
    public function mapCreate(string $instanceId): InstanceCreateResponseDto
    {
        return InstanceCreateResponseDto::hydrate([
            'id' => $instanceId,
        ]);
    }

    public function mapGet(array|SudokuGameInstance $table): InstanceGetResponseDto // TODO: remove Entity
    {
        $table = $table instanceof SudokuGameInstance ? $this->hydratePuzzleStateDto($table) : $table;
        return $table;
//        return InstanceGetResponseDto::hydrate($table);
    }

    private function applyHiddenCells(array $grid, array $hiddenCells): array
    {
        foreach ($hiddenCells as $hiddenCell) {
            [$rowIndex, $colIndex] = explode(':', $hiddenCell);

            if (isset($grid['cells'][$rowIndex][$colIndex])) {
                $grid['cells'][$rowIndex][$colIndex]['value'] = 0;
            }
        }

        return $grid;
    }

    private function hydratePuzzleStateDto(SudokuGameInstance $sudokuGameInstance): InstanceGetResponseDto
    {
        $sudokuGridJson = $sudokuGameInstance->getSudokuPuzzle()->getSudokuGrid()->getGrid();
        $size = $sudokuGameInstance->getSudokuPuzzle()->getSudokuGrid()->getSize();

        try {
            $sudokuGrid = json_decode($sudokuGridJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException(sprintf('Failed to decode Sudoku grid JSON for Sudoku Game Instance ID: %s. Error: %s', $sudokuGameInstance->getId()->toString() ?? 'unknown', $e->getMessage()), 0, $e);
        }

        $hiddenCells = $sudokuGameInstance->getSudokuPuzzle()->getHiddenCells();
        $puzzleGrid = $this->applyHiddenCells($sudokuGrid, $hiddenCells);

        $groups = [];
        foreach ($puzzleGrid['cells'] as $rowIndex => $rowArray) {
            foreach ($rowArray as $colIndex => $cell) {
                $cellDto = $this->hydrateCellDto($rowIndex, $colIndex, $cell);
                $puzzleGrid['cells'][$rowIndex][$colIndex] = $cellDto;

                $groups = $this->hydrateCellGroups($groups, $cellDto, $size);
            }
        }
        $puzzleGrid['groups'] = array_values($groups);

        $puzzleGrid['id'] = $sudokuGameInstance->getId()->toString();

        return InstanceGetResponseDto::hydrate($puzzleGrid);
    }

    /**
     * @param int $rowIndex
     * @param int $colIndex
     * @param array<mixed> $cell // TODO: use DTO
     * @return CellDto
     */
    private function hydrateCellDto(int $rowIndex, int $colIndex, array $cell): CellDto
    {
        $cell['coords'] = $this->getCellCoords($rowIndex, $colIndex);
        $cell['protected'] = (bool)$cell['value'];

        $cellDto = CellDto::hydrate($cell);
        return $cellDto;
    }

    /**
     * @param array<int, array{id: int, type: string, cells: array<string, CellDto>}> $groups // TODO: use DTO
     * @param CellDto $cellDto
     * @return array<int, array{id: int, type: string, cells: array<string, CellDto>}> // TODO: use DTO
     */
    private function hydrateCellGroups(array $groups, CellDto $cellDto, int $size): array
    {
        $cellGroups = $this->getCellGroups($cellDto, $size);

        foreach ($cellGroups as $group) {
            $groupId = $group['id'] . ':' . $group['type'];
            if (!isset($groups[$groupId])) {
                $groups[$groupId] = $group;
            }
            $groups[$groupId]['cells'][$cellDto->coords] = $cellDto;
        }
        return $groups;
    }

    private function getCellCoords(int $rowIndex, int $colIndex): string
    {
        return ($rowIndex + 1) . ':' . ($colIndex + 1);
    }

    /**
     * @param CellDto $cellDto
     * @return array<int, array{id: int, type: string, cells: array{}}> // TODO: use DTO
     */
    private function getCellGroups(CellDto $cellDto, int $size): array
    {
        $rowIndex = (int)explode(':', $cellDto->coords)[0] - 1;
        $colIndex = (int)explode(':', $cellDto->coords)[1] - 1;

        $squareId = $this->getBlockId($rowIndex, $colIndex, $size);

        $cellGroupRowDto = ['id' => $rowIndex + 1, 'type' => CellGroupDto::TYPE_ROW, 'cells' => []];
        $cellGroupColDto = ['id' => $colIndex + 1, 'type' => CellGroupDto::TYPE_COLUMN, 'cells' => []];
        $cellGroupSqrDto = ['id' => $squareId, 'type' => CellGroupDto::TYPE_BLOCK, 'cells' => []];

        return [
            $cellGroupRowDto,
            $cellGroupColDto,
            $cellGroupSqrDto,
        ];
    }

    private function getBlockId(int $rowIndex, int $colIndex, int $size): int
    {
        $blockSize = (int)sqrt($size);
        return (int)((floor($colIndex / $blockSize)) + (floor($rowIndex / $blockSize) * $blockSize) + 1);
    }

    public function getHiddenCellsCount(int $size, float $ratio): int
    {
        $hiddenCellsCount = (int)ceil($size * $size * $ratio);
        return $hiddenCellsCount;
    }
}