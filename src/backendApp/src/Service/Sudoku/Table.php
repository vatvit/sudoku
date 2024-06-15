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

        $table = $this->tableCellHider->hideCells($table, 10);

        $table = $this->fulfillCells($table);

        $tableStateDto = TableStateDto::hydrate($table);

        return $tableStateDto;

    }

    public function fulfillCells(array $table): array
    {
        foreach ($table['cells'] as $i => $cell) {
            $cell['row'] = ((int)floor($i / 9)) + 1;
            $cell['col'] = ($i % 9) + 1;
            $cell['groups'] = $this->getCellGroups($cell['row'], $cell['col']);
            $cell['protected'] = (bool)$cell['value'];

            $table['cells'][$i] = $cell;
        }

        return $table;
    }

    /**
     * @param int $row
     * @param int $col
     * @return array[]
     */
    private function getCellGroups(int $row, int $col): array
    {
        $squareId = $this->getSquareId($row, $col);

        $cellGroupRowDto = CellGroupDto::hydrate(['id' => $row + 1, 'type' => 'ROW']);
        $cellGroupColDto = CellGroupDto::hydrate(['id' => $col + 1, 'type' => 'COL']);
        $cellGroupSqrDto = CellGroupDto::hydrate(['id' => $squareId, 'type' => 'SQR']);

        return [
            $cellGroupRowDto,
            $cellGroupColDto,
            $cellGroupSqrDto,
        ];
    }

    /**
     * @param int $col
     * @param int $row
     * @return int
     */
    private function getSquareId(int $col, int $row): int
    {
        return (int)((floor($col / 3) + 1) + (floor($row / 3) * 3));
    }
}