<?php

namespace App\Application\CQRS\Command;

use App\Infrastructure\Entity\EntityFactory;
use App\Infrastructure\Entity\SudokuGrid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateSudokuGridHandler
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly EntityFactory $entityFactory)
    {
    }

    public function __invoke(CreateSudokuGridCommand $command): ?\Symfony\Component\Uid\Uuid
    {
        $entity = $this->entityFactory->create(SudokuGrid::class);
        $entity->setGrid(json_encode($command->grid));
        $entity->setBlocks([]);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity->getId();
    }
}