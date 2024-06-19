<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractDto;

final class TableStateDto extends AbstractDto
{
    public CellRowCollectionDto $cells;
    public CellGroupCollectionDto $groups;

    public function __construct(array $data = [])
    {
        $this->cells = new CellRowCollectionDto([]);
        $this->groups = new CellGroupCollectionDto([]);
        parent::__construct($data);
    }
}