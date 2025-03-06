<?php

namespace App\Application\Service\Dto;

abstract class AbstractActionDto extends AbstractDto
{
    public string $id;

    public int $timeDiff;

    /**
     * @var mixed[]
     */
    public array $effects;
}
