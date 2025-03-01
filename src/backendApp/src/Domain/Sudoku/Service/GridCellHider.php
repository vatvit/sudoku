<?php

namespace App\Domain\Sudoku\Service;

class GridCellHider
{
    /**
     * @param array<mixed> $grid // TODO: use DTO
     * @param int $count
     * @return array<mixed> // TODO: use DTO
     */
    public function hideCells(array $grid, int $count): array
    {
        // get Total number of cells
        $totalCount = 0;
        foreach ($grid['cells'] as $subArray) {
            $totalCount += count($subArray);
        }

        // size of the table
        $size = count($grid['cells']);

        // prepare a random array of cell indexes
        $cellsRange = range(1, $totalCount);
        shuffle($cellsRange);

        for ($i = 0; $i < $count; $i++) {
            // get random cell index
            $randomCell = array_shift($cellsRange) - 1;

            // calculate row/col by index
            $row = (int)floor($randomCell / $size);
            $col = $randomCell % $size;

            // remove value
            $grid['cells'][$row][$col]['value'] = 0;
        }

        return $grid;
    }

    public function generateHiddenCells(array $grid, int $count): array
    {
        $hiddenCells = [];

        // get Total number of cells
        $totalCount = 0;
        foreach ($grid['cells'] as $subArray) {
            $totalCount += count($subArray);
        }

        // size of the table
        $size = count($grid['cells']);

        // prepare a random array of cell indexes
        $cellsRange = range(1, $totalCount);
        shuffle($cellsRange);

        for ($i = 0; $i < $count; $i++) {
            // get random cell index
            $randomCell = array_shift($cellsRange) - 1;

            // calculate row/col by index
            $row = (int)floor($randomCell / $size);
            $col = $randomCell % $size;

            // remove value
            $hiddenCells[] = $row . ':' . $col;
        }

        return $hiddenCells;
    }
}
