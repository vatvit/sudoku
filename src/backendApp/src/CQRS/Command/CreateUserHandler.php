<?php

namespace App\CQRS\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
readonly class CreateUserHandler
{


    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $user = new User();
        $user->setEmail($command->email);
        $user->setPassword($command->password);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}