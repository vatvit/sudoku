<?php

namespace App\Application\CQRS\Command;

use App\Domain\Core\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

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