<?php

namespace App\Application\CQRS\Command;

use App\Infrastructure\Entity\SudokuGrid;

readonly class CreateSudokuPuzzleCommand
{
    public function __construct(
        public SudokuGrid $sudokuGridEntity,
        public array      $hiddenCells,
    )
    {}
}