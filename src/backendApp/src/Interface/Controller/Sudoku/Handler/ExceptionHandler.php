<?php

namespace App\Interface\Controller\Sudoku\Handler;

use App\Application\UseCase\Sudoku\Dto\GetInstanceOutputDto;
use App\Domain\Sudoku\Exception\GameNotFoundException;

/**
 * Handles exception logic that was previously embedded in controllers.
 * This service encapsulates the business rules for when exceptions should be thrown.
 */
class ExceptionHandler
{
    /**
     * Handles the case when a game instance is not found and throws appropriate exception
     */
    public function handleInstanceNotFound(string $instanceId, GetInstanceOutputDto $outputDto): void
    {
        if (!$outputDto->instance) {
            throw new GameNotFoundException($instanceId);
        }
    }

    /**
     * Handles cache miss for game actions and throws appropriate exception
     */
    public function handleGameCacheMiss(string $gameId): void
    {
        throw new GameNotFoundException($gameId);
    }
}
