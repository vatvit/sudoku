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

    public function validate(array $grid, int $size): ConstraintViolationListInterface
    {
        $errors = $this->validateStructure($grid, $size);
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