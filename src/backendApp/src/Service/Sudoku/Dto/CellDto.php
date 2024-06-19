<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractDto;

final class CellDto extends AbstractDto
{
        public string $coords;
        public string $value;
        public bool $protected;
}