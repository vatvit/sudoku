<?php

namespace App\Infrastructure\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidSudokuGridEntity extends Constraint
{
    public string $message = 'Invalid Sudoku grid Entity. Issues: {{ issues }}';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return 'app.validator.constraint.sudoku_grid_validator';
    }
}