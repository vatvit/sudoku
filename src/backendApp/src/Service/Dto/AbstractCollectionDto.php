<?php

namespace App\Service\Dto;

use Traversable;

/**
 * @template TValue
 * @implements \ArrayAccess<int, TValue>
 * @implements \IteratorAggregate<int, TValue>
 */
abstract class AbstractCollectionDto extends AbstractDto implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Represents a collection.
     * Overwrite the PHPDoc to specify type of elements accordingly to validateValueType method logic
     *
     * @var array<mixed> $collection
     */
    protected array $collection = [];

    /**
     * The $itemClass variable represents the class name of the DTO item.
     *
     * @var string
     */
    protected static string $itemClass;

    /**
     * @param array<mixed> $data
     * @return void
     */
    protected function hydrateData(array $data): void
    {
        $this->collection = [];

        foreach ($data as $item) {
            if (!is_a($item, AbstractDto::class)) {
                $itemDtoClass = static::$itemClass;
                if (is_subclass_of($itemDtoClass, AbstractDto::class)) {
                    $item = $itemDtoClass::hydrate($item);
                } else {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Invalid item class: %s provided. Expected subclass of %s.',
                            $itemDtoClass,
                            AbstractDto::class
                        )
                    );
                }
            }

            $this->collection[] = $item;
        }
    }


    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $array = [];

        foreach ($this->collection as $key => $item) {
            $array[$key] = $item->toArray();
        }

        return $array;
    }


    /**
     * Validates the value for a collection DTO.
     *
     * This method ensures that the value passed as parameter is of the correct type for the collection DTO.
     *
     * @param mixed $value The value to be validated.
     *
     * @return void
     * @throws CollectionDtoWrongValueException If the value type is incorrect.
     */
    protected function validateValue(mixed $value): void
    {
        if (is_a($value, static::$itemClass)) {
            return;
        }
        throw new CollectionDtoWrongValueException('Wrong value type "' . gettype($value) . '" for ' . (gettype($this))); // phpcs:ignore Generic.Files.LineLength.TooLong -- The following line exceeds the maximum length allowed by PHPCS, but it's a log string and it is more readable this way.
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->collection);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->collection[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->collection[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->validateValue($value);
        $this->collection[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->collection[$offset]);
    }

    public function count(): int
    {
        return count($this->collection);
    }
}
