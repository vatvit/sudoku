<?php

namespace App\Service\Sudoku\Dto;

use App\Service\Dto\AbstractDto;
use Symfony\Component\Validator\Constraints as Assert;

final class CellGroupDto extends AbstractDto
{
    public const TYPE_ROW = 'ROW';
    public const TYPE_COLUMN = 'COL';
    public const TYPE_BLOCK = 'BLC';

    #[Assert\NotBlank]
    public int $id;

    #[Assert\Choice([
        CellGroupDto::TYPE_ROW,
        CellGroupDto::TYPE_COLUMN,
        CellGroupDto::TYPE_BLOCK,
    ])]
    public string $type;

    #[Assert\NotBlank]
    #[Assert\All([
        new Assert\Type(type: CellDto::class)
    ])]
    /** @var static::PROP_CELLS_TYPE[] $cells */
    public array $cells;

    protected const PROP_CELLS_TYPE = CellDto::class;

}