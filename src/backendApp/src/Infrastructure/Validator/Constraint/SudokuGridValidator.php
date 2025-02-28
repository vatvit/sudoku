<?php

namespace App\Infrastructure\Validator\Constraint;

use App\Domain\Sudoku\Service\Interface\SudokuGridStructureValidatorInterface;
use App\Infrastructure\Entity\SudokuGrid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SudokuGridValidator extends ConstraintValidator
{
    public function __construct(private readonly SudokuGridStructureValidatorInterface $sudokuGridStructureValidator)
    {
    }


    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof SudokuGrid) {
            throw new \InvalidArgumentException('Expected instance of SudokuGrid, got ' . get_debug_type($value));
        }

        $size = $value->getSize();
        $grid = $value->getGrid();

        if (is_string($grid)) {
            try {
                $grid = json_decode($grid, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->context->buildViolation('Invalid JSON string provided: ' . $e->getMessage())
                    ->addViolation();
                return;
            }
        }
        if (!is_array($grid)) {
            $this->context->buildViolation('The value must be an array.')
                ->addViolation();
            return;
        }

        $issues = $this->sudokuGridStructureValidator->validate($grid, $size);

        if (count($issues) > 0) {
            $this->context->buildViolation($constraint->message ?? 'Invalid grid JSON. Issues: {{ issues }}')
                ->setParameter('{{ issues }}', implode(', ', (array)$issues))
                ->addViolation();
        }
    }
}