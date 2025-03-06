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

    #[Assert\All([
        new Assert\Type(type: CellRowCollectionDto::class)
    ])]
    #[ArrayItemType(CellRowCollectionDto::class)]
    /**
     * @var array<CellRowCollectionDto> $cells
     */
    public array $cells;

    #[Assert\All([
        new Assert\Type(type: CellGroupDto::class)
    ])]
    #[ArrayItemType(CellGroupDto::class)]
    /**
     * @var array<CellGroupDto> $cellGroups
     */
    public array $cellGroups;
}
