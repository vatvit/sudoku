<?php

namespace App\Service\Sudoku;

class TableGenerator
{
    /**
     * @return array<mixed> // TODO: use DTO
     */
    public function generate(): array
    {
        $table = [
            'cells' => [],
        ];

        for ($row = 0; $row < 9; $row++) {
            for ($col = 0; $col < 9; $col++) {
                $cell = [
                    'value' => $this->getCellValue($row, $col),
                ];

                if (!isset($table['cells'][$row])) {
                    $table['cells'][$row] = [];
                }
                $table['cells'][$row][$col] = $cell;
            }
        }

        return $table;
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
