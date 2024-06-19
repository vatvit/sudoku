<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractCollectionDto;

final class CellRowCollectionDto extends AbstractCollectionDto
{
    /**
     * @var CellCollectionDto[]
     */
    protected array $collection;

    static protected string $itemClass = CellCollectionDto::class;

}