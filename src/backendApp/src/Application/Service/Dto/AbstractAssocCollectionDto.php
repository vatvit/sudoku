<?php

namespace App\Application\Service\Dto;

/**
 * @template TValue
 * @template KValue
 * @implements \ArrayAccess<KValue, TValue>
 * @implements \IteratorAggregate<KValue, TValue>
 */
abstract class AbstractAssocCollectionDto extends AbstractCollectionDto
{
    protected function hydrateData(array $data): void
    {
        foreach ($data as $key => $item) {
            $item = $this->normalizeItem($item);
            $this->collection[$key] = $item;
        }
    }

}
