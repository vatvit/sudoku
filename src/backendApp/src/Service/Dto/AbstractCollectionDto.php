<?php

namespace App\Service\Dto;

use Traversable;

abstract class AbstractCollectionDto extends AbstractDto implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Represents a collection.
     * Overwrite the PHPDoc to specify type of elements accordingly to validateValueType method logic
     *
     * @var array $collection
     */
    protected array $collection = [];

    /**
     * The $itemClass variable represents the class name of the item.
     *
     * @var string
     */
    static protected string $itemClass;

    public static function hydrate(array $data): static
    {
        /** @var AbstractDto $itemClass */
        $itemClass = static::$itemClass;

        $dto = new static();

        if (empty($data)) {
            $dto->collection = [];
            return $dto;
        }

        foreach ($data as $item) {
            if (!is_a($item, AbstractDto::class)) {
                $item = $itemClass::hydrate($item);
            }

            $dto->collection[] = $item;
        }

        return $dto;
    }

    public function toArray(): array
    {
        $array = [];

        foreach ($this->collection as $item) {
            $array[] = $item->toArray();
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
        throw new CollectionDtoWrongValueException('Wrong value type "' . gettype($value) . '" for ' . (gettype($this)));
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