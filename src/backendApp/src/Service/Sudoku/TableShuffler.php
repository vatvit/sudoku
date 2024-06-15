<?php

namespace App\Service\Sudoku;

use App\Service\Sudoku\Dto\CellDto;
use App\Service\Sudoku\Dto\CellGroupDto;
use App\Service\Sudoku\Dto\TableStateDto;

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
        // Convert 1D array to 2D 9x9 matrix
        $cells2D = array_chunk($table['cells'], 9);

        $transpose = array();

        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                $transpose[$j][$i] = $cells2D[$i][$j];
            }
        }

        // Flattening the 2D $transpose array back to 1D
        $table['cells'] = array_reduce($transpose, 'array_merge', array());

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
                $temp = $table['cells'][9 * $i + $groupA + $j];
                $table['cells'][9 * $i + $groupA + $j] = $table['cells'][9 * $i + $groupB + $j];
                $table['cells'][9 * $i + $groupB + $j] = $temp;
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

        for ($j = 0; $j < 9; $j++) {
            for ($i = 0; $i < 3; $i++) {
                $temp = $table['cells'][($groupA + $i) * 9 + $j];
                $table['cells'][($groupA + $i) * 9 + $j] = $table['cells'][($groupB + $i) * 9 + $j];
                $table['cells'][($groupB + $i) * 9 + $j] = $temp;
            }
        }

        return $table;
    }

    public function switchRows(array $table): array
    {
        $group = rand (0, 2);
        $rows = range(0, 2);
        shuffle($rows);
        $rowA = array_shift($rows) + ($group * 3);
        $rowB = array_shift($rows) + ($group * 3);

        for ($i = 0; $i < 9; $i++) {
            $temp = $table['cells'][$rowA * 9 + $i];
            $table['cells'][$rowA * 9 + $i] = $table['cells'][$rowB * 9 + $i];
            $table['cells'][$rowB * 9 + $i] = $temp;
        }

        return $table;
    }

    public function switchCols(array $table): array
    {
//        $colA = rand(1, 9);
//        $colB = rand(1, 9);
//        while ($colB === $colA) {
//            $colB = rand(1, 9);
//        }

        $group = rand (0, 2);
        $cols = range(0, 2);
        shuffle($cols);
        $colA = array_shift($cols) + ($group * 3);
        $colB = array_shift($cols) + ($group * 3);

// Assuming we have a 9x9 matrix $matrix and we want to swap columns $colA and $colB
// Let's choose first and second columns just for example

        for ($i = 0; $i < 9; $i++) {
            $temp = $table['cells'][9 * $i + $colA];
            $table['cells'][9 * $i + $colA] = $table['cells'][9 * $i + $colB];
            $table['cells'][9 * $i + $colB] = $temp;
        }

        return $table;
    }

}