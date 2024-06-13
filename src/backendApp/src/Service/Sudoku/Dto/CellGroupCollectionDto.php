<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractCollectionDto;

final class CellGroupCollectionDto extends AbstractCollectionDto
{
    /**
     * @var CellGroupDto[]
     */
    protected array $collection;

    static protected string $itemClass = CellGroupDto::class;

}