<?php

namespace App\Application\Traits;

use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait WithValidator
{
    #[Ignore]
    protected ?ValidatorInterface $_validator = null; // @phpcs:ignore

    #[Ignore]
    protected function getValidator(): ValidatorInterface
    {
        if ($this->_validator === null) {
            $this->_validator = $this->getDefaultValidator();
        }
        return $this->_validator;
    }

    public function setValidator(ValidatorInterface $validator): void
    {
        $this->_validator = $validator;
    }

    #[Ignore]
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

    #[Ignore]
    protected function getDefaultValidator(): ValidatorInterface
    {
        static $validator;
        if ($validator === null) {
            $validator = Validation::createValidatorBuilder()
                ->enableAttributeMapping()
                ->getValidator();
        }
        return $validator;
    }

}