<?php

namespace App\Domain\Sudoku\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GameNotFoundException extends NotFoundHttpException
{
    public function __construct(string $gameId, ?\Throwable $previous = null)
    {
        $message = sprintf('Game instance with ID "%s" was not found', $gameId);
        parent::__construct($message, $previous);
    }
}