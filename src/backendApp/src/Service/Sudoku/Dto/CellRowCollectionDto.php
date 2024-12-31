<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractCollectionDto;

final class CellRowCollectionDto extends AbstractCollectionDto
{
    /**
     * @var CellDto[]
     */
    protected array $collection;

    static protected string $itemClass = CellDto::class;

}