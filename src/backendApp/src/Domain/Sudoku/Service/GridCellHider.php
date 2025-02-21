<?php

namespace App\Domain\Sudoku\Service;

class GridCellHider
{
    /**
     * @param array<mixed> $table // TODO: use DTO
     * @param int $count
     * @return array<mixed> // TODO: use DTO
     */
    public function hideCells(array $table, int $count): array
    {
        // get Total number of cells
        $totalCount = 0;
        foreach ($table['cells'] as $subArray) {
            $totalCount += count($subArray);
        }

        // size of the table
        $size = count($table['cells']);

        // prepare a random array of cell indexes
        $cellsRange = range(1, $totalCount);
        shuffle($cellsRange);

        for ($i = 0; $i < $count; $i++) {
            // get random cell index
            $randomCell = array_shift($cellsRange) - 1;

            // calculate row/col by index
            $row = floor($randomCell / $size);
            $col = $randomCell % $size;

            // remove value
            $table['cells'][$row][$col]['value'] = 0;
        }

        return $table;
    }
}
