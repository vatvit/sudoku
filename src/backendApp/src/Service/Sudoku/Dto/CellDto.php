<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractDto;

final class CellDto extends AbstractDto
{
        public string $coords;
        public int $value;
        /** @var int[]  */
        public array $notes = [];
        public bool $protected;
}