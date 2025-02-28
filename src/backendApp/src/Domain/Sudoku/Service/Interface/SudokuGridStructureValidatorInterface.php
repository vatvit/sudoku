<?php

namespace App\Domain\Sudoku\Service\Interface;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface SudokuGridStructureValidatorInterface
{
    public function validate(array $grid, int $size): ConstraintViolationListInterface;
}