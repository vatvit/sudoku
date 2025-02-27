<?php

namespace App\Application\Traits;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait WithValidator
{
    protected ?ValidatorInterface $validator = null;

    protected function getValidator(): ValidatorInterface
    {
        if ($this->validator === null) {
            $this->validator = Validation::createValidatorBuilder()
                ->enableAttributeMapping()
                ->getValidator();
        }
        return $this->validator;
    }

    public function setValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    public function getValidationViolations(): ConstraintViolationListInterface
    {
        return $this->getValidator()->validate($this);
    }

    public function assertValid(): true
    {
        $violations = $this->getValidationViolations();

        if (count($violations) > 0) {
            $messages = [];
            foreach ($violations as $violation) {
                $messages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            throw new \InvalidArgumentException(implode('; ', $messages));
        }

        return true;
    }

}