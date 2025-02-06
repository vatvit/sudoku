<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractDto;
use Symfony\Component\Validator\Constraints as Assert;

final class PuzzleStateDto extends AbstractDto
{
    #[Assert\NotBlank]
    public string $id;

    #[Assert\All([
        new Assert\Type(type: CellRowCollectionDto::class)
    ])]
    /** @var static::PROP_CELLS_TYPE[] $cells */
    public array $cells;
    protected const PROP_CELLS_TYPE = CellRowCollectionDto::class;

    #[Assert\All([
        new Assert\Type(type: CellGroupDto::class)
    ])]
    /** @var static::PROP_GROUPS_TYPE[] $groups */
    public array $groups;
    protected const PROP_GROUPS_TYPE = CellGroupDto::class;
}