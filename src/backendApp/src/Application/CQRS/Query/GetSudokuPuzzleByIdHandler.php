<?php

namespace App\Application\CQRS\Query;

use App\Infrastructure\Entity\SudokuPuzzle;
use App\Infrastructure\Repository\SudokuPuzzleRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class GetSudokuPuzzleByIdHandler
{
    public function __construct(private SudokuPuzzleRepository $repository)
    {
    }
    public function __invoke(GetSudokuPuzzleByIdQuery $query): SudokuPuzzle
    {
        $entity = $this->repository->find($query->id);

        return $entity;
    }
}