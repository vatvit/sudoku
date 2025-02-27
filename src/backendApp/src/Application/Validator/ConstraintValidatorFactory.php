<?php

namespace App\Application\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory as SymfonyConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorInterface;

class ConstraintValidatorFactory extends SymfonyConstraintValidatorFactory
{

    private array $validatorsMap = [];

    public function setValidatorsMap(array $validatorsMap)
    {
        $this->validatorsMap = $validatorsMap;
    }
    public function getInstance(Constraint $constraint): ConstraintValidatorInterface
    {
        $validatedBy = $constraint->validatedBy();

        // Check if a lazy-mapped validator exists for this constraint
        if (isset($this->validatorsMap[$validatedBy])) {
            $validator = $this->validatorsMap[$validatedBy];

            if (!$validator instanceof ConstraintValidatorInterface) {
                throw new \InvalidArgumentException(sprintf(
                    'The service "%s" must implement ConstraintValidatorInterface.',
                    $validatedBy
                ));
            }

            return $validator;
        }

        return parent::getInstance($constraint);
    }

}