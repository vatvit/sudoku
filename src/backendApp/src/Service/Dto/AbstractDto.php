<?php

namespace App\Service\Dto;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractDto
{

    private ValidatorInterface $_validator;

    private array $_privateProperties = ['_privateProperties', '_validator'];

    public function __construct(?array $data = [], ?ValidatorInterface $validator = null)
    {
        if ($validator === null) {
            $validator = $this->getDefaultValidator();
        }
        $this->_validator = $validator;

        if (isset($data)) {
            self::hydrate($data, $this);
        }
    }

    public static function hydrate(array $data, $dto = null, ?ValidatorInterface $validator = null): static
    {
        if ($dto === null) {
            $dto = new static(null, $validator);
        }

        if (!$dto instanceof static) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid DTO class: %s provided, expected instance of %s.',
                    get_class($dto),
                    static::class
                )
            );
        }

        $dto->hydrateData($data);

        $dto->validate();

        return $dto;
    }

    protected function hydrateData(array $data): void
    {
        foreach ($data as $propertyName => $value) {
            if (!property_exists($this, $propertyName)) {
                throw new \InvalidArgumentException('Invalid property: ' . $propertyName);
            }

            if (in_array($propertyName, $this->_privateProperties, true)) {
                throw new \InvalidArgumentException('The property "' . $propertyName . '" is private and cannot be populated.');
            }

            $reflection = new \ReflectionProperty($this, $propertyName);
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
                $arrayDtoConstName = 'PROP_' . mb_strtoupper($propertyName) . '_TYPE';
                $arrayDtoConstValue = defined('static::' . $arrayDtoConstName) ? constant('static::' . $arrayDtoConstName) : '';

                if (is_subclass_of($arrayDtoConstValue, AbstractDto::class)) {
                    foreach ($value as &$item) {
                        if (!is_a($item, AbstractDto::class)) {
                            $item = $arrayDtoConstValue::hydrate($item);
                        }
                    }
                }
            }

            $this->$propertyName = $value;
        }
    }

    public function toArray(): array
    {
        $array = [];

        foreach (get_object_vars($this) as $property => $value) {
            if (in_array($property, $this->_privateProperties)) {
                continue;
            }
            
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

    /**
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function validate(): bool
    {
        $violations = $this->_validator->validate($this);

        if (count($violations) > 0) {
            $messages = [];
            foreach ($violations as $violation) {
                $messages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            throw new \InvalidArgumentException(implode('; ', $messages));
        }

        return true;
    }

    /**
     * @return ValidatorInterface
     */
    private function getDefaultValidator(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }
}