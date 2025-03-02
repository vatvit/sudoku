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
        $SudokuGridEntity = $this->entityFactory->create(SudokuGrid::class);
        $SudokuGridEntity->setSize($command->size);
        $SudokuGridEntity->setGrid(json_encode($command->grid));
        $SudokuGridEntity->setBlocks([]);

        $this->entityManager->persist($SudokuGridEntity);

        return $SudokuGridEntity;
    }
}