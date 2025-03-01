<?php

namespace App\Application\CQRS\Command;

use App\Infrastructure\Entity\SudokuPuzzle;

class CreateSudokuGameInstanceCommand
{
    public function __construct(
        public SudokuPuzzle $sudokuPuzzleEntity,
    )
    {}
}