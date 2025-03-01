<?php

namespace App\Application\CQRS\Query;

use Symfony\Component\Uid\Uuid;

class GetSudokuPuzzleByIdQuery
{
    public function __construct(public Uuid $id)
    {}
}