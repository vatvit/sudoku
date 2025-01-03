<?php

namespace App\Service\Sudoku;

use App\Service\Sudoku\Dto\CellDto;
use App\Service\Sudoku\Dto\CellGroupDto;
use App\Service\Sudoku\Dto\PuzzleStateDto;

class TableShuffler
{

    public function shuffle(array $table, int $iterations = 10): array
    {
        $actions = ['transposeTable', 'switchCols', 'switchRows', 'switchColsGroup', 'switchRowsGroup'];

        for($i = 0; $i < $iterations; $i++) {
            $randomAction = $actions[array_rand($actions)];
            $table = $this->$randomAction($table);
        }

        return $table;
    }


    /**
     * This method take an array with 81 elements as input
     * and returns its transpose.
     *
     * @param array $table
     * @return array
     */
    public function transposeTable(array $table): array
    {
        $transpose = [];

        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                $transpose[$j][$i] = $table['cells'][$i][$j];
            }
        }

        $table['cells'] = $transpose;

        return $table;
    }

    public function switchColsGroup(array $table): array
    {
        $groups = [0, 3, 6];
        shuffle($groups);
        $groupA = array_shift($groups);
        $groupB = array_shift($groups);

        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $temp = $table['cells'][$i][$groupA + $j];
                $table['cells'][$i][$groupA + $j] = $table['cells'][$i][$groupB + $j];
                $table['cells'][$i][$groupB + $j] = $temp;
            }
        }

        return $table;
    }

    public function switchRowsGroup(array $table): array
    {
        $groups = [0, 3, 6];
        shuffle($groups);
        $groupA = array_shift($groups);
        $groupB = array_shift($groups);

        for ($i = 0; $i < 3; $i++) {
            $temp = $table['cells'][$groupA + $i];
            $table['cells'][$groupA + $i] = $table['cells'][$groupB + $i];
            $table['cells'][$groupB + $i] = $temp;
        }

        return $table;
    }

    public function switchRows(array $table): array
    {
        $group = rand(0, 2);
        $rows = range(0, 2);
        shuffle($rows);
        $rowA = array_shift($rows) + ($group * 3);
        $rowB = array_shift($rows) + ($group * 3);


        $temp = $table['cells'][$rowA];
        $table['cells'][$rowA] = $table['cells'][$rowB];
        $table['cells'][$rowB] = $temp;

        return $table;
    }

    public function switchCols(array $table): array
    {
        $group = rand(0, 2);
        $cols = range(0, 2);
        shuffle($cols);
        $colA = array_shift($cols) + ($group * 3);
        $colB = array_shift($cols) + ($group * 3);

        for ($i = 0; $i < 9; $i++) {
            $temp = $table['cells'][$i][$colA];
            $table['cells'][$i][$colA] = $table['cells'][$i][$colB];
            $table['cells'][$i][$colB] = $temp;
        }

        return $table;
    }

}