<?php

namespace App\Application\CQRS\Command;

use App\Infrastructure\Entity\GameInstance;
use App\Infrastructure\Entity\GameInstanceAction;

class CreateSudokuGameStateCommand
{
    public function __construct(
        public readonly GameInstance        $gameInstanceEntity,
        public readonly array               $filledCells,
        public readonly array               $notedCells,
        public readonly ?GameInstanceAction $lastGameInstanceActionEntity
    )
    {}
}