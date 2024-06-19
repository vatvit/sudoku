<?php

namespace App\Service\Dto;

abstract class AbstractDto
{

    public function __construct(array $data = [])
    {
        if (isset($data)) {
            self::hydrate($data, $this);
        }
    }

    public static function hydrate(array $data, $dto = null): static
    {
        if ($dto === null) {
            $dto = new static();
        }

        foreach ($data as $property => $value) {
            if (!property_exists($dto, $property)) {
                throw new \InvalidArgumentException('Invalid property: ' . $property);
            }

            $reflection = new \ReflectionProperty($dto, $property);
            $type = $reflection->getType();

            if (!$type) {
                throw new \RuntimeException('Undefined type for property: ' . $property);
            }

            $expectedType = $type->getName();

            if ($expectedType === 'self' || is_subclass_of($expectedType, AbstractDto::class)) {
                if (!is_a($value, AbstractDto::class)) {
                    $value = $expectedType::hydrate($value);
                }
            }

            $dto->$property = $value;
        }

        return $dto;
    }

    public function toArray(): array
    {
        $array = [];

        foreach (get_object_vars($this) as $property => $value) {
            if ($value instanceof self) {
                $array[$property] = $value->toArray();
            } else {
                $array[$property] = $value;
            }
        }

        return $array;
    }
}