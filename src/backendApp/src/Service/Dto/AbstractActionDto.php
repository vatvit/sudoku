<?php

namespace App\Service\Dto;

abstract class AbstractActionDto extends AbstractDto
{
    public string $id;
    public int $timeDiff;
    /**
     * @var mixed[]
     */
    public array $effects;
    protected const PROP_EFFECTS_TYPE = AbstractActionEffectDto::class;
}
