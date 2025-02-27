<?php

namespace App\Infrastructure\Entity;

use App\Application\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Validation;

readonly class EntityFactory
{

    public function __construct(private readonly ConstraintValidatorFactory $constraintValidatorFactory)
    {}
    

    public function create($entityClass): AbstractEntity
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