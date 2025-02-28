<?php

namespace App\Infrastructure\Entity;

use App\Application\Validator\ConstraintServiceValidatorFactory;
use Symfony\Component\Validator\Validation;

readonly class EntityFactory
{

    public function __construct(private readonly ConstraintServiceValidatorFactory $constraintValidatorFactory)
    {}

    /**
     * @template T of AbstractEntity
     * @param class-string<T> $entityClass
     * @return T
     */
    public function create(string $entityClass): mixed
    {
        $entity = new $entityClass();
        if (!$entity instanceof AbstractEntity) {
            throw new \InvalidArgumentException(sprintf(
                'The provided class %s must be an instance of AbstractEntity.',
                $entityClass
            ));
        }

        $validator = Validation::createValidatorBuilder()
            ->setConstraintValidatorFactory($this->constraintValidatorFactory)
            ->enableAttributeMapping()
            ->getValidator();

        $entity->setValidator($validator);
        return $entity;
    }
}