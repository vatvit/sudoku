<?php

namespace App\Controller;

use App\Service\Sudoku\Dto\TableStateDto;
use App\Service\Sudoku\Table;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SudokuTableController extends AbstractController
{
    #[Route('/api/sudoku/table/load')]
    public function load(Table $table)
    {
        $tableStateDto = $table->generate();

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
