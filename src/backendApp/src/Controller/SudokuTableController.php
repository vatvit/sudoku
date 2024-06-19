<?php

namespace App\Controller;

use App\Service\Sudoku\Dto\CellDto;
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

        $table = $tableStateDto->toArray();

        $response = $this->json($table);

        return $response;
    }

}
