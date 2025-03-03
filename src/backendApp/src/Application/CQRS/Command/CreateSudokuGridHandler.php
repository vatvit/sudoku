<?php

namespace App\Application\CQRS\Command;

use App\Infrastructure\Entity\EntityFactory;
use App\Infrastructure\Entity\SudokuGrid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateSudokuGridHandler
{
    public function __construct(
        private EntityFactory          $entityFactory,
        private EntityManagerInterface $entityManager,
    )
    {}

    public function __invoke(CreateSudokuGridCommand $command): SudokuGrid
    {
        $sudokuGridEntity = $this->entityFactory->create(SudokuGrid::class);
        $sudokuGridEntity->setSize($command->size);
        $sudokuGridEntity->setGrid(json_encode($command->grid));
        $sudokuGridEntity->setCellGroups(json_encode($command->cellGroups));

        $this->entityManager->persist($sudokuGridEntity);

        return $sudokuGridEntity;
    }
}