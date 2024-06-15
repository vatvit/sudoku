<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractDto;

final class CellDto extends AbstractDto
{
        public int $row;
        public int $col;
        public string $value;
        public CellGroupCollectionDto $groups;
        public bool $protected;
}