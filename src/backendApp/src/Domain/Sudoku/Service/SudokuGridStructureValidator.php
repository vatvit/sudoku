<?php

namespace App\Domain\Sudoku\Service;

use App\Domain\Sudoku\Service\Interface\SudokuGridStructureValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SudokuGridStructureValidator implements SudokuGridStructureValidatorInterface
{
    private ValidatorInterface $validator;

    public function __construct()
    {
        $this->validator = Validation::createValidator();
    }

    public function validate(array $grid): ConstraintViolationListInterface
    {
        $errors = $this->validateCommonStructure($grid);
        if (count($errors) > 0) {
            return $errors;
        }

        $errors = $this->validateHeader($grid['header']);
        if (count($errors) > 0) {
            return $errors;
        }

        $errors = $this->validateStructure($grid['cells'], $grid['header']['size']);
        return $errors;
    }

    private function validateCommonStructure(array $grid): ConstraintViolationListInterface
    {
        $constraints = new Assert\Collection([
            'fields' => [
                'cells' => [
                    new Assert\Type('array'),
                ],
                'header' => [
                    new Assert\Type('array'),
                ],
            ],
            'allowExtraFields' => false,
            'allowMissingFields' => false,
        ]);

        $errors = $this->validator->validate($grid, $constraints);
        return $errors;
    }

    private function validateHeader(array $header): ConstraintViolationListInterface
    {
        $constraints = new Assert\Collection([
            'fields' => [
                'size' => new Assert\Required([
                    new Assert\Type('integer'),
                    new Assert\Callback(function ($size, $context) {
                        if (!is_int($size) || $size <= 0 || sqrt($size) != floor(sqrt($size))) {
                            $context->buildViolation('The size must be a perfect square.')
                                ->addViolation();
                        }
                    }),
                ]),
            ],
            'allowExtraFields' => false,
            'allowMissingFields' => false,
        ]);
        $errors = $this->validator->validate($header, $constraints);
        return $errors;
    }

    public function validateStructure(array $gridCells, int $size): ConstraintViolationListInterface
    {
        $constraints = new Assert\Required([
            new Assert\Type('array'),
            new Assert\Count($size),
            new Assert\All([
                new Assert\Type('array'),
                new Assert\Count($size),
                new Assert\All([
                    new Assert\Collection([
                        'fields' => [
                            'value' => [
                                new Assert\Type('integer'),
                            ],
                        ],
                        'allowExtraFields' => false,
                        'allowMissingFields' => false,
                    ]),
                ]),
            ]),
        ]);
        $errors = $this->validator->validate($gridCells, $constraints);
        return $errors;
    }
}