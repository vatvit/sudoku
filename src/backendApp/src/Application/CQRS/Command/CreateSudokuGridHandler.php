<?php

namespace App\Application\CQRS\Command;

use App\Infrastructure\Entity\SudokuGrid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateSudokuGridHandler
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(CreateSudokuGridCommand $command): ?\Symfony\Component\Uid\Uuid
    {
        $entity = new SudokuGrid();
        $entity->setGrid(json_encode($command->grid));
        $entity->setBlocks([]);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity->getId();
    }
}