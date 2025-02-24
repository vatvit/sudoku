<?php

namespace App\Domain\Sudoku\Service\Dto;

use App\Application\Service\Dto\AbstractDto;
use Symfony\Component\Validator\Constraints as Assert;

final class CellDto extends AbstractDto
{
    #[Assert\NotBlank]
    public string $coords;

    #[Assert\Range(min: 0, max: 9)]
    public int $value;

    /** @var int[] */
    public array $notes = [];

    public bool $protected;
}
