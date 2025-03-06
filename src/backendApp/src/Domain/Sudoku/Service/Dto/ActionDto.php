<?php

namespace App\Domain\Sudoku\Service\Dto;

use App\Application\Service\Dto\AbstractActionDto;
use App\Application\Service\Dto\Attribute\ArrayItemType;

class ActionDto extends AbstractActionDto
{
    /**
     * @var mixed[]
     */
    #[ArrayItemType(ActionEffectDto::class)]
    public array $effects;
}
