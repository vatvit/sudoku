<?php

namespace App\Application\CQRS\Query;

use Symfony\Component\Uid\Uuid;

class GetSudokuGridByIdQuery
{
    public function __construct(public Uuid $id)
    {}
}