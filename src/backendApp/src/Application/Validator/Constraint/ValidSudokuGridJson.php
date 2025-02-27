<?php

namespace App\Application\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidSudokuGridJson extends Constraint
{
    public string $message = 'Invalid grid JSON. Issues: {{ issues }}';

    public function validatedBy(): string
    {
        return 'app.validator.constraint.sudoku_grid_validator';
    }
}