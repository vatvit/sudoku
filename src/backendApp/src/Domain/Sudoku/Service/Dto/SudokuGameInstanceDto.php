<?php

namespace App\Domain\Sudoku\Service\Dto;

use App\Application\Service\Dto\AbstractDto;
use App\Application\Service\Dto\Attribute\ArrayItemType;
use App\Domain\Sudoku\ValueObject\CellCoords;
use Symfony\Component\Validator\Constraints as Assert;

final class SudokuGameInstanceDto extends AbstractDto
{
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    public string $id;

    /**
     * @var array<CellRowCollectionDto> $cells
     */
    #[Assert\All([
        new Assert\Type(type: CellRowCollectionDto::class)
    ])]
    #[ArrayItemType(CellRowCollectionDto::class)]
    public array $cells;

    /**
     * @var array<CellGroupDto> $cellGroups
     */
    #[Assert\All([
        new Assert\Type(type: CellGroupDto::class)
    ])]
    #[ArrayItemType(CellGroupDto::class)]
    public array $cellGroups;
}
