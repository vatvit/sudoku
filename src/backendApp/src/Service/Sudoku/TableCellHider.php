<?php

namespace App\Service\Sudoku;

class TableCellHider
{

    public function hideCells(array $table, int $count): array
    {
        $cellsRange = range(0, count($table['cells']));
        shuffle($cellsRange);

        for ($i = 0; $i < $count; $i++) {
            $randomCell = array_shift($cellsRange);
            $table['cells'][$randomCell]['value'] = 0;
        }

        return $table;
    }

}