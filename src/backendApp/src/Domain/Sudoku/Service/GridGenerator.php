<?php

namespace App\Domain\Sudoku\Service;

class GridGenerator
{
    public function __construct(readonly private GridShuffler $gridShuffler)
    {}

    /**
     * @return array<mixed> // TODO: use DTO
     */
    public function generate(int $size = 9): array
    {
        if ($size <= 0 || floor(sqrt($size)) * floor(sqrt($size)) !== $size) {
            throw new \InvalidArgumentException('Grid size must be a perfect square number');
        }

        $boxSize = (int)sqrt($size);
        $grid = [
            'cells' => [],
        ];

        for ($row = 0; $row < $size; $row++) {
            for ($col = 0; $col < $size; $col++) {
                $cell = [
                    'value' => $this->getCellValue($row, $col, $size, $boxSize),
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

    private function getCellValue(int $row, int $col, int $size, int $boxSize): int
    {
        $value = $col;
        $value = ($value + ($row * $boxSize) * 2);
        $value = $value + (floor($row / $boxSize) * ($size - 1));

        $value = ($value % $size) + 1;
        return (int)$value;
    }
}
