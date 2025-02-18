<?php

namespace App\CQRS\Query;

use App\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GetUsersHandler
{
    public function __construct(private UserRepository $userRepository)
    {
    }
    public function __invoke(GetUsersQuery $query): array
    {
        $allUsersEntities = $this->userRepository->findBy([], ['createdAt' => 'DESC']);

        return array_map(fn($user) => [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'createdAt' => $user->getCreatedAt(),
        ], $allUsersEntities);
    }
}