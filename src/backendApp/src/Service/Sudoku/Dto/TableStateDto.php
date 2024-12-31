<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractDto;

final class TableStateDto extends AbstractDto
{
    public string $id;

    /** @var static::PROP_CELLS_TYPE[]  */
    public array $cells;
    protected const PROP_CELLS_TYPE = CellRowCollectionDto::class;

    /** @var static::PROP_GROUPS_TYPE[]  */
    public array $groups;
    protected const PROP_GROUPS_TYPE = CellGroupDto::class;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }
}