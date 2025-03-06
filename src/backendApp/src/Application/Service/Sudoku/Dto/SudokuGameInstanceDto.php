<?php

namespace App\Application\Service\Sudoku\Dto;

use App\Application\Service\Dto\AbstractActionEffectDto;
use App\Application\Service\Dto\AbstractDto;
use App\Application\Service\Dto\Attribute\ArrayItemType;
use App\Domain\Sudoku\Service\Dto\CellGroupDto;
use App\Domain\Sudoku\Service\Dto\CellRowCollectionDto;
use App\Domain\Sudoku\ValueObject\CellCoords;
use Symfony\Component\Validator\Constraints as Assert;

class SudokuGameInstanceDto extends AbstractDto
{
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    public string $id;

    #[Assert\All([
        new Assert\Type(type: CellRowCollectionDto::class)
    ])]
    #[ArrayItemType(CellRowCollectionDto::class)]
    /**
     * @var array<CellRowCollectionDto> $grid
     */
    public array $grid;

    #[Assert\All([
        new Assert\Type(type: CellGroupDto::class)
    ])]
    #[ArrayItemType(CellGroupDto::class)]
    /**
     * @var array<CellGroupDto> $cellGroups
     */
    public array $cellGroups;

    #[Assert\All([
        new Assert\Type(type: CellCoords::class)
    ])]
    #[ArrayItemType(CellCoords::class)]
    /**
     * @var array<CellCoords> $hiddenCells
     */
    public array $hiddenCells;

    public bool $isSolved;

    public \DateTimeImmutable $createdAt;
    public ?\DateTimeImmutable $startedAt;
    public ?\DateTimeImmutable $finishedAt;
}
