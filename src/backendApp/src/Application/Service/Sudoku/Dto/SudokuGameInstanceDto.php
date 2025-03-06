<?php

namespace App\Application\Service\Sudoku\Dto;

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

    /**
     * @var array<CellRowCollectionDto> $grid
     */
    #[Assert\All([
        new Assert\Type(type: CellRowCollectionDto::class)
    ])]
    #[ArrayItemType(CellRowCollectionDto::class)]
    public array $grid;

    /**
     * @var array<CellGroupDto> $cellGroups
     */
    #[Assert\All([
        new Assert\Type(type: CellGroupDto::class)
    ])]
    #[ArrayItemType(CellGroupDto::class)]
    public array $cellGroups;

    /**
     * @var array<CellCoords> $hiddenCells
     */
    #[Assert\All([
        new Assert\Type(type: CellCoords::class)
    ])]
    #[ArrayItemType(CellCoords::class)]
    public array $hiddenCells;

    public bool $isSolved;

    public \DateTimeImmutable $createdAt;
    public ?\DateTimeImmutable $startedAt;
    public ?\DateTimeImmutable $finishedAt;
}
