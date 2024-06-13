<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractCollectionDto;

final class CellCollectionDto extends AbstractCollectionDto
{
    /**
     * @var CellDto[]
     */
    protected array $collection;

    static protected string $itemClass = CellDto::class;

}