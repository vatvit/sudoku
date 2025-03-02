<?php

namespace App\Application\CQRS\Command;

use App\Infrastructure\Entity\EntityFactory;
use App\Infrastructure\Entity\SudokuPuzzle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateSudokuPuzzleHandler
{
    public function __construct(
        private EntityFactory          $entityFactory,
        private EntityManagerInterface $entityManager,
    )
    {}

    public function __invoke(CreateSudokuPuzzleCommand $command): SudokuPuzzle
    {
        $sudokuPuzzleEntity = $this->entityFactory->create(SudokuPuzzle::class);
        $sudokuPuzzleEntity->setSudokuGrid($command->sudokuGridEntity);
        $sudokuPuzzleEntity->setHiddenCells($command->hiddenCells);

        $this->entityManager->persist($sudokuPuzzleEntity);

        return $sudokuPuzzleEntity;
    }
}