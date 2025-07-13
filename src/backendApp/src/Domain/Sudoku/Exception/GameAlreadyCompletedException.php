<?php

namespace App\Domain\Sudoku\Exception;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class GameAlreadyCompletedException extends ConflictHttpException
{
    public function __construct(string $gameId, ?\Throwable $previous = null)
    {
        $message = sprintf('Game instance with ID "%s" is already completed', $gameId);
        parent::__construct($message, $previous);
    }
}