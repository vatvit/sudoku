<?php

namespace App\Application\CQRS\Query;

use App\Infrastructure\Entity\SudokuGameInstance;
use App\Infrastructure\Repository\SudokuGameInstanceRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GetSudokuGameInstanceByIdHandler
{
    public function __construct(private SudokuGameInstanceRepository $repository)
    {}

    public function __invoke(GetSudokuGameInstanceByIdQuery $query): ?SudokuGameInstance
    {
        $entity = $this->repository->find($query->id);

        return $entity;
    }
}