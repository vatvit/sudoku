<?php

namespace App\Application\CQRS\Command;

use App\Infrastructure\Entity\EntityFactory;
use App\Infrastructure\Entity\GameInstance;
use App\Infrastructure\Entity\SudokuGameInstance;
use App\Infrastructure\Repository\SudokuGameInstanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
readonly class CreateSudokuGameInstanceHandler
{
    public function __construct(
        private EntityFactory          $entityFactory,
        private EntityManagerInterface $entityManager,
    )
    {}

    public function __invoke(CreateSudokuGameInstanceCommand $command): Uuid
    {
        $sudokuGameInstanceEntity = $this->entityFactory->create(SudokuGameInstance::class);
        $sudokuGameInstanceEntity->setSudokuPuzzle($command->sudokuPuzzleEntity);

        $this->entityManager->persist($sudokuGameInstanceEntity);

        return $sudokuGameInstanceEntity->getId();
    }
}