<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractDto;

final class CellGroupDto extends AbstractDto
{
    public int $id;
    public string $type;
    public CellCollectionDto $cells;

    public function __construct(array $data = [])
    {
        $this->cells = new CellCollectionDto([]);
        parent::__construct($data);
    }
}