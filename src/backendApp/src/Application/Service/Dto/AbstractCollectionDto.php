<?php

namespace App\Application\Service\Dto;

use Symfony\Component\Serializer\Attribute\Ignore;
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
     * Overwrite the PHPDoc to specify a type of elements accordingly to validateValueType method logic
     *
     * @var array<mixed> $collection
     */
    protected array $collection;

    /**
     * The $itemClass variable represents the class name of the DTO item.
     *
     * @var string
     */
    #[Ignore]
    protected static string $itemClass;

    public function __construct(?array $data = [])
    {
        $this->initCollection();
        parent::__construct($data);
    }

    protected function initCollection(): void
    {
        $this->collection = [];
    }

    /**
     * @param mixed $item
     * @return AbstractDto|mixed
     */
    protected function normalizeItem(mixed $item): mixed
    {
        $itemDtoClass = static::$itemClass;
        if (is_subclass_of($itemDtoClass, AbstractDto::class)) {
            if (!is_a($item, $itemDtoClass)) {
                $item = $itemDtoClass::hydrate($item);
            }
        }
        return $item;
    }

    /**
     * @param array<mixed> $data
     * @return void
     */
    protected function hydrateData(array $data): void
    {
        foreach ($data as $item) {
            $item = $this->normalizeItem($item);
            $this->collection[] = $item;
        }
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $array = array_map(function ($item) {
            return $item->toArray();
        }, $this->collection);

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
    protected function assertValidType(mixed $value): void
    {
        if (is_a($value, static::$itemClass)) {
            return;
        }
        throw new CollectionDtoWrongValueException('Wrong value type "' . gettype($value) . '" for ' . (gettype($this))); // phpcs:ignore Generic.Files.LineLength.TooLong -- The following line exceeds the maximum length allowed by PHPCS, but it's a log string and it is more readable this way.
    }

    #[Ignore]
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
        $this->assertValidType($value);
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
