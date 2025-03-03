<?php

namespace App\Application\Service\Sudoku\Dto;

use App\Application\Service\Dto\AbstractDto;
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
    /**
     * @var array<CellRowCollectionDto> $grid
     */
    // @phpstan-ignore-next-line missingType.iterableValue
    public array $grid;
    protected const PROP_GRID_TYPE = CellRowCollectionDto::class;

    #[Assert\All([
        new Assert\Type(type: CellGroupDto::class)
    ])]
    /**
     * @var array<CellGroupDto> $cellGroups
     */
    // @phpstan-ignore-next-line missingType.iterableValue
    public array $cellGroups;
    protected const PROP_CELL_GROUPS_TYPE = CellGroupDto::class;

    #[Assert\All([
        new Assert\Type(type: CellCoords::class)
    ])]
    /**
     * @var array<CellCoords> $hiddenCells
     */
    // @phpstan-ignore-next-line missingType.iterableValue
    public array $hiddenCells;
    protected const PROP_HIDDEN_CELLS_TYPE = CellCoords::class;

    public bool $isSolved;

    public \DateTimeImmutable $createdAt;
    public ?\DateTimeImmutable $startedAt;
    public ?\DateTimeImmutable $finishedAt;
}
