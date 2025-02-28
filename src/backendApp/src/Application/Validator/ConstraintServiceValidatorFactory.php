<?php

namespace App\Application\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory as SymfonyConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorInterface;

class ConstraintServiceValidatorFactory extends SymfonyConstraintValidatorFactory
{

    /**
     * @var array<ConstraintValidatorInterface>
     */
    private array $serviceValidatorsMap = [];

    public function addServiceValidator(ConstraintValidatorInterface $serviceValidator): self
    {
        $this->serviceValidatorsMap[] = $serviceValidator;
        return $this;
    }
    public function getInstance(Constraint $constraint): ConstraintValidatorInterface
    {
        $validatedBy = $constraint->validatedBy();

        // Check if a lazy-mapped validator exists for this constraint
        if (isset($this->serviceValidatorsMap[$validatedBy])) {
            $validator = $this->serviceValidatorsMap[$validatedBy];

            return $validator;
        }

        return parent::getInstance($constraint);
    }

}