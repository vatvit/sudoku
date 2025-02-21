<?php

namespace App\Application\CQRS\Command;

class CreateSudokuGridCommand
{
    public function __construct(
        public array $grid, // TODO: use DTO or Valued Object
    ) {}

}