<?php

namespace App\Controller;

use App\Service\Sudoku\Dto\TableStateDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SudokuTableController extends AbstractController
{
    #[Route('/api/sudoku/table/load')]
    public function load()
    {
        $table = [
            'cells' => [],
        ];
        for ($row = 0; $row < 9; $row++) {
//            if (!isset($table['cells'][$row])) {
//                $table['cells'][$row] = [];
//            }

            for ($col = 0; $col < 9; $col++) {
                $squareId = (floor($col / 3) + 1) + (floor($row / 3) * 3);
                $cell = [
                    'row' => $row + 1,
                    'col' => $col + 1,
                    'groups' => [
                        ['id' => $row + 1, 'type' => 'ROW'],
                        ['id' => $col + 1, 'type' => 'COL'],
                        ['id' => $squareId, 'type' => 'SQR'],
                    ],
                    'value' => $col + 1,
                ];
//                $table['cells'][$row][$col] = $cell;
                $table['cells'][] = $cell;
            }
        }

        $tableStateDto = TableStateDto::hydrate($table);

        $table = $this->tableStateDtoToResponseArray($tableStateDto);

        $response = $this->json($table);

        return $response;
    }

    private function tableStateDtoToResponseArray(TableStateDto $tableStateDto): array
    {
        $cells = [];
        foreach ($tableStateDto->cells as $cellDto) {
            $rowIndex = $cellDto->row - 1;
            $colIndex = $cellDto->col - 1;

            if (!isset($cells[$rowIndex])) {
                $cells[$rowIndex] = [];
            }
            $cells[$rowIndex][$colIndex] = $cellDto->toArray();
        }

        foreach ($cells as &$row) {
            ksort($row);
        }
        ksort($cells);

        $tableArray = $tableStateDto->toArray();
        $tableArray['cells'] = $cells;

        return $tableArray;
    }

}
