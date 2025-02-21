<?php

namespace App\Domain\Sudoku\Service\Dto;

use App\Application\Service\Dto\AbstractActionEffectDto;

class ActionEffectDto extends AbstractActionEffectDto
{
    public string $coords;
    public int $value;
    /** @var int[] */
    public array $notes;
}
