<?php

namespace App\Application\CQRS\Command;

use App\Infrastructure\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateUserHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {}

    public function __invoke(CreateUserCommand $command): void
    {
        $userEntity = new User();
        $userEntity->setEmail($command->email);
        $userEntity->setPassword($command->password);
        
        $this->entityManager->persist($userEntity);
    }
}