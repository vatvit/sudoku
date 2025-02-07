<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractActionDto;

/** @property static::PROP_EFFECTS_TYPE[] $effects */
class ActionDto extends AbstractActionDto
{
    protected const PROP_EFFECTS_TYPE = ActionEffectDto::class;
}
