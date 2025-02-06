<?php

namespace App\Service\Sudoku;

use App\Service\Sudoku\Dto\CellDto;
use App\Service\Sudoku\Dto\CellGroupDto;
use App\Service\Sudoku\Dto\PuzzleStateDto;

class PuzzleGenerator
{

    public function __construct(
        readonly private TableGenerator $tableGenerator,
        readonly private TableShuffler $tableShuffler,
        readonly private TableCellHider $tableCellHider,
    )
    {
    }

    public function generate(): PuzzleStateDto
    {
        $table = $this->tableGenerator->generate();

        $shuffledTable = $this->tableShuffler->shuffle($table);

        $puzzleTable = $this->tableCellHider->hideCells($shuffledTable, 3);

        $puzzleStateDto = $this->hydrateTableStateDto($puzzleTable);

        return $puzzleStateDto;

    }

    private function hydrateTableStateDto(array $puzzleTable): PuzzleStateDto
    {
        $groups = [];
        foreach ($puzzleTable['cells'] as $rowIndex => $rowArray) {
            foreach ($rowArray as $colIndex => $cell) {

                $cellDto = $this->hydrateCellDto($rowIndex, $colIndex, $cell);
                $puzzleTable['cells'][$rowIndex][$colIndex] = $cellDto;

                $groups = $this->hydrateCellGroups($rowIndex, $colIndex, $groups, $cellDto);
            }
        }
        $puzzleTable['groups'] = array_values($groups);

        $puzzleTable['id'] = $this->generateId($puzzleTable);

        return PuzzleStateDto::hydrate($puzzleTable);
    }

    private function generateId(array $puzzleTable): string
    {
        return sha1(json_encode($puzzleTable));
    }

    private function hydrateCellDto(int $rowIndex, int $colIndex, array $cell): CellDto
    {
        $cell['coords'] = $this->getCellCoords($rowIndex, $colIndex);
        $cell['protected'] = (bool)$cell['value'];

        $cellDto = CellDto::hydrate($cell);
        return $cellDto;
    }

    private function hydrateCellGroups(int $rowIndex, int $colIndex, array $groups, CellDto $cellDto): array
    {
        $cellGroups = $this->getCellGroups($rowIndex, $colIndex);

        foreach ($cellGroups as $group) {
            $groupId = $group['id'] . ':' . $group['type'];
            if (!isset($groups[$groupId])) {
                $groups[$groupId] = $group;
            }
            $groups[$groupId]['cells'][$this->getCellCoords($rowIndex, $colIndex)] = $cellDto;
        }
        return $groups;
    }

    private function getCellCoords(int $rowIndex, int $colIndex): string
    {
        return ($rowIndex + 1) . ':' . ($colIndex + 1);
    }

    private function getCellGroups(int $rowIndex, int $colIndex): array
    {
        $squareId = $this->getSquareId($rowIndex, $colIndex);

        $cellGroupRowDto = ['id' => $rowIndex + 1, 'type' => CellGroupDto::TYPE_ROW];
        $cellGroupColDto = ['id' => $colIndex + 1, 'type' => CellGroupDto::TYPE_COLUMN];
        $cellGroupSqrDto = ['id' => $squareId, 'type' => CellGroupDto::TYPE_BLOCK];

        return [
            $cellGroupRowDto,
            $cellGroupColDto,
            $cellGroupSqrDto,
        ];
    }

    private function getSquareId(int $rowIndex, int $colIndex): int
    {
        return (int)((floor($colIndex / 3)) + (floor($rowIndex / 3) * 3) + 1);
    }
}