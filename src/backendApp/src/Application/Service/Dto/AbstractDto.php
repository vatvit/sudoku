<?php

namespace App\Application\Service\Dto;

use App\Application\Service\Dto\Attribute\ArrayItemType;
use App\Application\Traits\WithValidator;
use App\Domain\ValueObject\ValueObjectInterface;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/** @phpstan-consistent-constructor */
abstract class AbstractDto
{
    use WithValidator;

    #[Ignore]
    /**
     * @var array<string> $_privateProperties
     */
    private array $_privateProperties = ['_privateProperties', '_validator']; // @phpcs:ignore

    /**
     * @param array<mixed>|null $data
     * @param ValidatorInterface|null $validator
     */
    public function __construct(?array $data = [], ?ValidatorInterface $validator = null)
    {
        if ($validator !== null) {
            $this->setValidator($validator);
        }

        if (isset($data)) {
            self::hydrate($data, $this);
        }
    }

    /**
     * @param array<mixed> $data
     * @param AbstractDto|null $dto
     * @param ValidatorInterface|null $validator
     * @return static
     */
    public static function hydrate(array $data, ?AbstractDto $dto = null, ?ValidatorInterface $validator = null): static
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

        $dto->assertValid();

        return $dto;
    }

    /**
     * @param array<mixed> $data
     * @return void
     */
    protected function hydrateData(array $data): void
    {
        foreach ($data as $propertyName => $value) {
            if (!property_exists($this, $propertyName)) {
                // Skip unknown property
                continue;
            }

            if (in_array($propertyName, $this->_privateProperties, true)) {
                throw new \InvalidArgumentException('The property "' . $propertyName . '" is private and cannot be populated.'); // phpcs:ignore Generic.Files.LineLength.TooLong -- The following line exceeds the maximum length allowed by PHPCS, but it's a log string and it is more readable this way.
            }

            $reflectionProperty = new \ReflectionProperty($this, $propertyName);
            $type = $reflectionProperty->getType();

            $this->checkPropertyType($type, $propertyName);

            $expectedPropertyType = $type->getName();

            if ($expectedPropertyType === 'self' || is_subclass_of($expectedPropertyType, AbstractDto::class)) {
                $this->$propertyName = $this->hydrateDto($value, $expectedPropertyType, $propertyName);
                continue;
            }

            if (is_subclass_of($expectedPropertyType, ValueObjectInterface::class)) {
                $this->$propertyName = $this->hydrateValueObject($value, $expectedPropertyType);
                continue;
            }

            if ($expectedPropertyType === 'array') {
                $attribute = $reflectionProperty->getAttributes(ArrayItemType::class)[0] ?? null;
                if ($attribute) {
                    /** @var ArrayItemType $attributeInstance */
                    $attributeInstance = $attribute->newInstance();
                    $arrayItemType = $attributeInstance->type;

                    foreach ($value as &$item) {
                        if (is_subclass_of($arrayItemType, AbstractDto::class)) {
                            $item = $this->hydrateDto($item, $arrayItemType, $propertyName);
                        }

                        if (is_subclass_of($arrayItemType, ValueObjectInterface::class)) {
                            $item = $this->hydrateValueObject($item, $arrayItemType);
                        }
                    }
                }

                $this->$propertyName = $value;
                continue;
            }

            $this->$propertyName = $value;
        }
    }

    /**
     * @return array<mixed>
     */
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
                    } else {
                        return $item;
                    }
                }, $value);
            } else {
                $array[$property] = $value;
            }
        }

        return $array;
    }

    private function checkPropertyType(mixed $type, string $propertyName): void
    {
        if (!$type) {
            throw new \RuntimeException('Undefined type for property: ' . $propertyName);
        }

        if (!$type instanceof \ReflectionNamedType) {
            throw new \RuntimeException(
                sprintf(
                    'Invalid type for property: %s. Expected type but got: %s.',
                    $propertyName,
                    get_debug_type($type)
                )
            );
        }
    }

    private function hydrateDto(mixed $value, string|AbstractDto $expectedDtoType, int|string $propertyName): AbstractDto
    {
        if (is_a($value, $expectedDtoType)) {
            return $value;
        }

        if (!is_array($value)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expected property "%s" to be an array, %s given.',
                    $propertyName,
                    gettype($value)
                )
            );
        }

        $value = $expectedDtoType::hydrate($value);

        if (!$value instanceof $expectedDtoType) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expected each item in the array to be an instance of %s, got %s.',
                    $expectedDtoType,
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        return $value;
    }

    private function hydrateValueObject(string $value, string $expectedPropertyType)
    {
        /** @var ValueObjectInterface::class $expectedPropertyType */
        $value = $expectedPropertyType::fromString($value);
        return $value;
    }
}
