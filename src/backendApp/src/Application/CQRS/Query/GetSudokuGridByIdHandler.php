<?php

namespace App\Application\CQRS\Query;

use App\Infrastructure\Entity\SudokuGrid;
use App\Infrastructure\Repository\SudokuGridRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GetSudokuGridByIdHandler
{
    public function __construct(private SudokuGridRepository $repository)
    {
    }
    public function __invoke(GetSudokuGridByIdQuery $query): ?SudokuGrid
    {
        $entity = $this->repository->find($query->id);

        return $entity;
    }
}