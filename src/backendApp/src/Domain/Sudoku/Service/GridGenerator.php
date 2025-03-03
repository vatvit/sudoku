<?php

namespace App\Domain\Sudoku\Service;

use App\Domain\Sudoku\Service\Dto\CellGroupDto;
use App\Domain\Sudoku\ValueObject\CellCoords;

class GridGenerator
{
    public function __construct(readonly private GridShuffler $gridShuffler)
    {}

    /**
     * @return array<mixed> // TODO: use DTO
     */
    public function generate(int $size = 9): array
    {
        if ($size <= 0 || (int)(floor(sqrt($size)) * floor(sqrt($size))) !== $size) {
            throw new \InvalidArgumentException('Grid size must be a perfect square number');
        }

        $boxSize = (int)sqrt($size);
        $grid = [
            'cells' => [],
            'cellGroups' => $this->generateCellGroups($size),
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

    private function generateCellGroups(int $size): array
    {
        $cellGroups = [];

        $groupId = 1;

        // Generate ROW groups
        for ($row = 1; $row <= $size; $row++) {
            $rowGroup = [
                'id' => $groupId++,
                'cells' => [],
                'type' => CellGroupDto::TYPE_ROW,
            ];
            for ($col = 1; $col <= $size; $col++) {
                $rowGroup['cells'][] = (string)(new CellCoords($row, $col));
            }
            $cellGroups[] = $rowGroup;
        }

        // Generate COL groups
        for ($col = 1; $col <= $size; $col++) {
            $colGroup = [
                'id' => $groupId++,
                'cells' => [],
                'type' => CellGroupDto::TYPE_COLUMN,
            ];
            for ($row = 1; $row <= $size; $row++) {
                $colGroup['cells'][] = (string)(new CellCoords($row, $col));
            }
            $cellGroups[] = $colGroup;
        }

        // Generate BLC (block) groups
        $boxSize = (int)sqrt($size);
        for ($blockRow = 0; $blockRow < $boxSize; $blockRow++) {
            for ($blockCol = 0; $blockCol < $boxSize; $blockCol++) {
                $blockGroup = [
                    'id' => $groupId++,
                    'cells' => [],
                    'type' => CellGroupDto::TYPE_BLOCK,
                ];
                for ($row = 1; $row <= $boxSize; $row++) {
                    for ($col = 1; $col <= $boxSize; $col++) {
                        $blockRowStart = $blockRow * $boxSize;
                        $blockColStart = $blockCol * $boxSize;
                        $coordsString = (string)(new CellCoords($blockRowStart + $row, $blockColStart + $col));
                        $blockGroup['cells'][] = $coordsString;
                    }
                }
                $cellGroups[] = $blockGroup;
            }
        }

        return $cellGroups;
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
