<?php

namespace App\Domain\ValueObject;

interface ValueObjectInterface extends \JsonSerializable, \Stringable
{
    public static function fromString(string $value): static;

    public function equals(ValueObjectInterface $valueObject): bool;
}