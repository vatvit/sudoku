<?php

namespace App\Domain\Sudoku\Service\Interface;

interface GridValidatorInterface
{
    public function validate(array $grid): array;
}