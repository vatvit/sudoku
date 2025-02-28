<?php

namespace App\Infrastructure\Validator\Constraint;

use App\Domain\Sudoku\Service\Interface\SudokuGridStructureValidatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidSudokuGridJsonValidator extends ConstraintValidator
{
    public function __construct(private readonly SudokuGridStructureValidatorInterface $sudokuGridStructureValidator)
    {
    }


    public function validate(mixed $value, Constraint $constraint): void
    {
        if (is_string($value)) {
            try {
                $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->context->buildViolation('Invalid JSON string provided: ' . $e->getMessage())
                    ->addViolation();
                return;
            }
        }
        if (!is_array($value)) {
            $this->context->buildViolation('The value must be an array.')
                ->addViolation();
            return;
        }

        $issues = $this->sudokuGridStructureValidator->validate($value);

        if (count($issues) > 0) {
            $this->context->buildViolation($constraint->message ?? 'Invalid grid JSON. Issues: {{ issues }}')
                ->setParameter('{{ issues }}', implode(', ', $issues))
                ->addViolation();
        }
    }
}