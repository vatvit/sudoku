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

        foreach ($data as $propertyName => $value) {
            if (!property_exists($dto, $propertyName)) {
                throw new \InvalidArgumentException('Invalid property: ' . $propertyName);
            }

            $reflection = new \ReflectionProperty($dto, $propertyName);
            $type = $reflection->getType();

            if (!$type) {
                throw new \RuntimeException('Undefined type for property: ' . $propertyName);
            }

            $expectedPropertyType = $type->getName();

            if ($expectedPropertyType === 'self' || is_subclass_of($expectedPropertyType, AbstractDto::class)) {
                if (!is_a($value, AbstractDto::class)) {
                    $value = $expectedPropertyType::hydrate($value);
                }
            }

            if ($expectedPropertyType === 'array') {
                $value = self::hydrateArrayOfDtoValue((string)$propertyName, $value);
            }

            $dto->$propertyName = $value;
        }

        return $dto;
    }

    protected static function hydrateArrayOfDtoValue(string $propertyName, array $value): array
    {
        $arrayDtoConstName = 'PROP_' . mb_strtoupper($propertyName) . '_TYPE';
        $arrayDtoConstValue = defined('static::' . $arrayDtoConstName) ? constant('static::' . $arrayDtoConstName) : '';

        if (!is_subclass_of($arrayDtoConstValue, AbstractDto::class)) {
            throw new \RuntimeException('Invalid array DTO type "' . $arrayDtoConstValue . '" for property: ' . $propertyName);
        }

        foreach ($value as &$item) {
            if (!is_a($item, AbstractDto::class)) {
                $item = $arrayDtoConstValue::hydrate($item);
            }
        }
        return $value;
    }

    public function toArray(): array
    {
        $array = [];

        foreach (get_object_vars($this) as $property => $value) {
            if ($value instanceof self) {
                $array[$property] = $value->toArray();
            } elseif (is_array($value)) {
                $array[$property] = array_map(function ($item) {
                    if ($item instanceof self) {
                        return $item->toArray();
                    }
                }, $value);
            } else {
                $array[$property] = $value;
            }
        }

        return $array;
    }
}