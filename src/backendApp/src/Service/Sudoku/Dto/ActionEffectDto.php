<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractActionEffectDto;

class ActionEffectDto extends AbstractActionEffectDto
{
    public string $coords;
    public int $value;
    /** @var int[] */
    public array $notes;
}
