<?php

namespace App\Domain\Sudoku\Service\Dto;

use App\Application\Service\Dto\AbstractActionDto;

/** @property static::PROP_EFFECTS_TYPE[] $effects */
class ActionDto extends AbstractActionDto
{
    protected const PROP_EFFECTS_TYPE = ActionEffectDto::class;
}
