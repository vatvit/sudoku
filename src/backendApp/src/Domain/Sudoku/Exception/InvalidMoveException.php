<?php

namespace App\Domain\Sudoku\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvalidMoveException extends BadRequestHttpException
{
    public function __construct(string $reason, ?\Throwable $previous = null)
    {
        $message = sprintf('Invalid move: %s', $reason);
        parent::__construct($message, $previous);
    }
}