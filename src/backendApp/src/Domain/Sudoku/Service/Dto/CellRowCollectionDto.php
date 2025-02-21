<?php

namespace App\Domain\Sudoku\Service\Dto;

use App\Application\Service\Dto\AbstractCollectionDto;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @extends AbstractCollectionDto<CellDto>
 */
final class CellRowCollectionDto extends AbstractCollectionDto
{
    #[Assert\All([
        new Assert\Type(type: CellDto::class)
    ])]
    /**
     * @var CellDto[] $collection
     */
    protected array $collection;

    protected static string $itemClass = CellDto::class;
}
