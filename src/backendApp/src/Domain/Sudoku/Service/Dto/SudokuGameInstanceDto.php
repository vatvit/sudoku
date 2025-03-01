<?php

namespace App\Domain\Sudoku\Service\Dto;

use App\Application\Service\Dto\AbstractDto;
use Symfony\Component\Validator\Constraints as Assert;

final class SudokuGameInstanceDto extends AbstractDto
{
    #[Assert\NotBlank]
    public string $id;

    #[Assert\All([
        new Assert\Type(type: CellRowCollectionDto::class)
    ])]
    /**
     * @var array<CellRowCollectionDto> $cells
     */
    // @phpstan-ignore-next-line missingType.iterableValue
    public array $cells;
    protected const PROP_CELLS_TYPE = CellRowCollectionDto::class;

    #[Assert\All([
        new Assert\Type(type: CellGroupDto::class)
    ])]
    /**
     * @var array<CellGroupDto> $groups
     */
    // @phpstan-ignore-next-line missingType.iterableValue
    public array $groups;
    protected const PROP_GROUPS_TYPE = CellGroupDto::class;
}
