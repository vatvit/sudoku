<?php

namespace App\Infrastructure\EntityEventListener;

use App\Application\Validator\ConstraintServiceValidatorFactory;
use App\Infrastructure\Entity\AbstractEntity;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Validator\Validation;

readonly class PostLoadListener implements EventSubscriber
{
    public function __construct(private ConstraintServiceValidatorFactory $constraintValidatorFactory)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [Events::postLoad];
    }

    public function postLoad(PostLoadEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof AbstractEntity) {
            // TODO: move it to external service. Use the same validator Object in all Entities
            $validator = Validation::createValidatorBuilder()
                ->setConstraintValidatorFactory($this->constraintValidatorFactory)
                ->enableAttributeMapping()
                ->getValidator();

            $entity->setValidator($validator);
        }
    }
}