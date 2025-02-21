<?php

namespace App\Domain\Sudoku\Service;

class GridGenerator
{

    public function __construct(readonly private GridShuffler  $gridShuffler)
    {}

    /**
     * @return array<mixed> // TODO: use DTO
     */
    public function generate(): array
    {
        $grid = [
            'cells' => [],
        ];

        for ($row = 0; $row < 9; $row++) {
            for ($col = 0; $col < 9; $col++) {
                $cell = [
                    'value' => $this->getCellValue($row, $col),
                ];

                if (!isset($grid['cells'][$row])) {
                    $grid['cells'][$row] = [];
                }
                $grid['cells'][$row][$col] = $cell;
            }
        }

        $shuffledGrid = $this->gridShuffler->shuffle($grid);

        return $shuffledGrid;
    }

    public function getCellValue(int $row, int $col): int
    {
        $value = $col;
        $value = ($value + ($row * 3) * 2);
        $value = $value + (floor($row / 3) * 8);

        $value = ($value % 9) + 1;
        return (int)$value;
    }
}
