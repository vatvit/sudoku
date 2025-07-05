<?php

namespace App\Domain\Sudoku\Service\Dto;

use App\Application\Service\Dto\AbstractDto;
use App\Domain\Sudoku\ValueObject\CellCoords;
use Symfony\Component\Validator\Constraints as Assert;

final class CellDto extends AbstractDto
{
    public CellCoords $coords;

    // TODO: WHAT IS CELL??? should it contain Value and Notes?

    #[Assert\Range(min: 0, max: 9)]
    public int $value;

    /** @var int[] */
    public array $notes = [];

    public bool $protected;
}
