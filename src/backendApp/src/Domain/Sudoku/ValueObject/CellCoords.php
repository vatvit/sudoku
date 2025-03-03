<?php

namespace App\Domain\Sudoku\ValueObject;

use App\Domain\ValueObject\ValueObjectInterface;

class CellCoords implements ValueObjectInterface
{
    public const SEPARATOR = ':';

    public function __construct(
        public readonly int $row,
        public readonly int $col
    ) {
        if ($this->row < 0 || $this->col < 0) {
            throw new \InvalidArgumentException('Row and column values must be positive integers.');
        }
    }

    /**
     * @return array<int>
     */
    public function getCoords(): array
    {
        return [$this->row, $this->col];
    }

    public function equals(ValueObjectInterface $valueObject): bool
    {
        return $this->col === $valueObject->col && $this->row === $valueObject->row;
    }

    public static function fromString(string $value): static
    {
        if (!preg_match('/^\d+:\d+$/', $value)) {
            throw new \InvalidArgumentException('Invalid coordinate format. Expected format: "row' . self::SEPARATOR . 'col" with integer values.');
        }
        $value = explode(self::SEPARATOR, $value);
        return new self((int)$value[0], (int)$value[1]);
    }

    public function __toString(): string
    {
        return $this->row . self::SEPARATOR . $this->col;
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}