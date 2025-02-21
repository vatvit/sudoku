<?php

namespace App\Domain\Sudoku\Service\Dto;

use App\Application\Service\Dto\AbstractDto;
use Symfony\Component\Validator\Constraints as Assert;

final class CellGroupDto extends AbstractDto
{
    public const TYPE_ROW = 'ROW';
    public const TYPE_COLUMN = 'COL';
    public const TYPE_BLOCK = 'BLC';

    #[Assert\NotBlank]
    public int $id; // TODO: remove because it is a DTO

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
    /**
     * @var array<CellDto> $cells
     */
    // @phpstan-ignore-next-line missingType.iterableValue
    public array $cells;
    protected const PROP_CELLS_TYPE = CellDto::class;
}
