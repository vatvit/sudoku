<?php

namespace App\Domain\Sudoku\Service;

use App\Domain\Sudoku\Service\Interface\GridValidatorInterface;

class GridValidator implements GridValidatorInterface
{
    public function validate(array $grid): array
    {
        $this->validateStructure($grid);
        $mistakes = $this->validateMistakes($grid);
        return $mistakes;
    }

    private function validateStructure(array $grid): void
    {
    }

    private function validateMistakes(array $grid): array
    {
        return [];
    }

}