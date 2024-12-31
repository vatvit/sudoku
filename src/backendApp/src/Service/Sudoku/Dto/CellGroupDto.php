<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractDto;

final class CellGroupDto extends AbstractDto
{
    public int $id;
    public string $type;

    /** @var static::PROP_CELLS_TYPE[]  */
    public array $cells;
    protected const PROP_CELLS_TYPE = CellDto::class;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
}