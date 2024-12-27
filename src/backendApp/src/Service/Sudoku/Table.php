<?php

namespace App\Service\Sudoku;

use App\Service\Sudoku\Dto\CellDto;
use App\Service\Sudoku\Dto\CellGroupDto;
use App\Service\Sudoku\Dto\TableStateDto;

class Table
{

    public function __construct(
        readonly private TableGenerator $tableGenerator,
        readonly private TableShuffler $tableShuffler,
        readonly private TableCellHider $tableCellHider,
    )
    {
    }

    public function generate(): TableStateDto
    {
        $table = $this->tableGenerator->generate();

        $table = $this->tableShuffler->shuffle($table);

        $table = $this->tableCellHider->hideCells($table, 3);

        $tableStateDto = $this->hydrateTableStateDto($table);

        return $tableStateDto;

    }

    public function hydrateTableStateDto(array $table): TableStateDto
    {
        $groups = [];
        foreach ($table['cells'] as $rowIndex => $rowArray) {
            foreach ($rowArray as $colIndex => $cell) {

                $cellDto = $this->hydrateCellDto($rowIndex, $colIndex, $cell);
                $table['cells'][$rowIndex][$colIndex] = $cellDto;

                $groups = $this->hydrateCellGroups($rowIndex, $colIndex, $groups, $cellDto);
            }
        }
        $table['groups'] = array_values($groups);

        return TableStateDto::hydrate($table);
    }

    public function hydrateCellDto(int $rowIndex, int $colIndex, array $cell): CellDto
    {
        $cell['coords'] = $this->getCellCoords($rowIndex, $colIndex);
        $cell['protected'] = (bool)$cell['value'];

        $cellDto = CellDto::hydrate($cell);
        return $cellDto;
    }

    public function hydrateCellGroups(int $rowIndex, int $colIndex, array $groups, CellDto $cellDto): array
    {
        $cellGroups = $this->getCellGroups($rowIndex, $colIndex);

        foreach ($cellGroups as $group) {
            $groupId = $group['id'] . ':' . $group['type'];
            if (!isset($groups[$groupId])) {
                $groups[$groupId] = CellGroupDto::hydrate($group);
            }
            $groups[$groupId]->cells[$this->getCellCoords($rowIndex, $colIndex)] = $cellDto;
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

        $cellGroupRowDto = ['id' => $rowIndex + 1, 'type' => 'ROW'];
        $cellGroupColDto = ['id' => $colIndex + 1, 'type' => 'COL'];
        $cellGroupSqrDto = ['id' => $squareId, 'type' => 'SQR'];

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