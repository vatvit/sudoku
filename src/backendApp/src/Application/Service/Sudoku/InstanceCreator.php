<?php

namespace App\Application\Service\Sudoku;

use App\Application\CQRS\Command\CreateSudokuGameInstanceCommand;
use App\Application\CQRS\Command\CreateSudokuGameInstanceHandler;
use App\Application\CQRS\Command\CreateSudokuGridCommand;
use App\Application\CQRS\Command\CreateSudokuGridHandler;
use App\Application\CQRS\Command\CreateSudokuPuzzleCommand;
use App\Application\CQRS\Command\CreateSudokuPuzzleHandler;
use App\Application\CQRS\Query\GetSudokuGridByIdHandler;
use App\Application\CQRS\Query\GetSudokuGridByIdQuery;
use App\Application\CQRS\Query\GetSudokuPuzzleByIdHandler;
use App\Application\CQRS\Query\GetSudokuPuzzleByIdQuery;
use App\Application\CQRS\Trait\HandleMultiplyTrait;
use App\Domain\Sudoku\Service\Dto\CellDto;
use App\Domain\Sudoku\Service\Dto\CellGroupDto;
use App\Domain\Sudoku\Service\Dto\PuzzleStateDto;
use App\Domain\Sudoku\Service\GridCellHider;
use App\Domain\Sudoku\Service\GridGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class InstanceCreator
{
    use HandleMultiplyTrait;

    private const DEFAULT_HIDDEN_CELLS_RATIO = 0.1;

    public function __construct(
        private readonly GridGenerator          $gridGenerator,
        private readonly GridCellHider          $gridCellHider,
        private readonly EntityManagerInterface $entityManager,
        MessageBusInterface            $messageBus,
    ) {
        $this->messageBus = $messageBus;
    }

    public function create(int $size = 9): PuzzleStateDto
    {
        $grid = $this->gridGenerator->generate($size);

        $sudokuGridEntityId = $this->handleAndGetResultByHandlerName(
            new CreateSudokuGridCommand($size, $grid),
            CreateSudokuGridHandler::class
        );

        $sudokuGridEntity = $this->handleAndGetResultByHandlerName(
            new GetSudokuGridByIdQuery($sudokuGridEntityId),
            GetSudokuGridByIdHandler::class
        );

        $hiddenCellsCount = $this->getHiddenCellsCount($size, self::DEFAULT_HIDDEN_CELLS_RATIO);
        $hiddenCells = $this->gridCellHider->generateHiddenCells($grid, $hiddenCellsCount);

        $sudokuPuzzleEntityId = $this->handleAndGetResultByHandlerName(
            new CreateSudokuPuzzleCommand($sudokuGridEntity, $hiddenCells),
            CreateSudokuPuzzleHandler::class
        );

        $sudokuPuzzleEntity = $this->handleAndGetResultByHandlerName(
            new GetSudokuPuzzleByIdQuery($sudokuPuzzleEntityId),
            GetSudokuPuzzleByIdHandler::class
        );

        $sudokuGameInstanceEntity = $this->handleAndGetResultByHandlerName(
            new CreateSudokuGameInstanceCommand($sudokuPuzzleEntity),
            CreateSudokuGameInstanceHandler::class
        );

        $this->entityManager->flush(); // commit unit of work

        $puzzleGrid = $this->applyHiddenCells($grid, $hiddenCells);
        $puzzleStateDto = $this->hydratePuzzleStateDto($puzzleGrid);

        return $puzzleStateDto;
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

    /**
     * @param array<mixed> $puzzleGrid // TODO: use DTO
     * @return PuzzleStateDto
     */
    private function hydratePuzzleStateDto(array $puzzleGrid): PuzzleStateDto
    {
        $groups = [];
        foreach ($puzzleGrid['cells'] as $rowIndex => $rowArray) {
            foreach ($rowArray as $colIndex => $cell) {
                $cellDto = $this->hydrateCellDto($rowIndex, $colIndex, $cell);
                $puzzleGrid['cells'][$rowIndex][$colIndex] = $cellDto;

                $groups = $this->hydrateCellGroups($groups, $cellDto);
            }
        }
        $puzzleGrid['groups'] = array_values($groups);

        $puzzleGrid['id'] = $this->generateId($puzzleGrid);

        return PuzzleStateDto::hydrate($puzzleGrid);
    }

    /**
     * @param array<mixed> $puzzleGrid // TODO: use DTO
     * @return string
     */
    private function generateId(array $puzzleGrid): string
    {
        return sha1(json_encode($puzzleGrid));
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
    private function hydrateCellGroups(array $groups, CellDto $cellDto): array
    {
        $cellGroups = $this->getCellGroups($cellDto);

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
    private function getCellGroups(CellDto $cellDto): array
    {
        $rowIndex = (int)explode(':', $cellDto->coords)[0] - 1;
        $colIndex = (int)explode(':', $cellDto->coords)[1] - 1;

        $squareId = $this->getBlockId($rowIndex, $colIndex);

        $cellGroupRowDto = ['id' => $rowIndex + 1, 'type' => CellGroupDto::TYPE_ROW, 'cells' => []];
        $cellGroupColDto = ['id' => $colIndex + 1, 'type' => CellGroupDto::TYPE_COLUMN, 'cells' => []];
        $cellGroupSqrDto = ['id' => $squareId, 'type' => CellGroupDto::TYPE_BLOCK, 'cells' => []];

        return [
            $cellGroupRowDto,
            $cellGroupColDto,
            $cellGroupSqrDto,
        ];
    }

    private function getBlockId(int $rowIndex, int $colIndex): int
    {
        $blockSize = (int)sqrt(count($this->gridGenerator->generate()['cells']));
        return (int)((floor($colIndex / $blockSize)) + (floor($rowIndex / $blockSize) * $blockSize) + 1);
    }

    public function getHiddenCellsCount(int $size, float $ratio): int
    {
        $hiddenCellsCount = (int)ceil($size * $size * $ratio);
        return $hiddenCellsCount;
    }
}
