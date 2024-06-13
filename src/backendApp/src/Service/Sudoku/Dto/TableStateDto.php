<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractDto;

final class TableStateDto extends AbstractDto
{
    /**
     * @var CellCollectionDto|CellDto[]
     */
    public CellCollectionDto $cells;
}