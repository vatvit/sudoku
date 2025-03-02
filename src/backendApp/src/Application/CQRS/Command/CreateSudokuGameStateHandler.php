<?php

namespace App\Application\CQRS\Command;

use App\Infrastructure\Entity\EntityFactory;
use App\Infrastructure\Entity\GameState;
use App\Infrastructure\Entity\SudokuGameState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateSudokuGameStateHandler
{
    public function __construct(
        private EntityFactory          $entityFactory,
        private EntityManagerInterface $entityManager,
    )
    {}

    public function __invoke(CreateSudokuGameStateCommand $command): SudokuGameState
    {
        $gameStateEntity = new GameState();
        $gameStateEntity->setGameInstance($command->gameInstanceEntity);

        if ($command->lastGameInstanceActionEntity) {
            $gameStateEntity->setLastGameInstanceAction($command->lastGameInstanceActionEntity);
        }

        $sudokuGameStateEntity = $this->entityFactory->create(SudokuGameState::class);
        $sudokuGameStateEntity->setGameState($gameStateEntity);
        $sudokuGameStateEntity->setFilledCells($command->filledCells);
        $sudokuGameStateEntity->setNotedCells($command->notedCells);

        $this->entityManager->persist($sudokuGameStateEntity);

        return $sudokuGameStateEntity;
    }
}