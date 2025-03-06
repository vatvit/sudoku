<?php

namespace App\Application\Service\Dto\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ArrayItemType
{
    public function __construct(
        public string $type
    )
    {
    }
}
