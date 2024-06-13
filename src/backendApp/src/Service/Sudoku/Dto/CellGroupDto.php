<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractDto;

final class CellGroupDto extends AbstractDto
{
    public int $id;
    public string $type;
}