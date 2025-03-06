<?php

namespace App\Interface\Controller\Sudoku\Dto;

use App\Application\Service\Dto\AbstractDto;
use App\Application\Service\Dto\Attribute\ArrayItemType;
use App\Domain\Sudoku\Service\Dto\CellGroupDto;
use App\Domain\Sudoku\Service\Dto\CellRowCollectionDto;
use Symfony\Component\Validator\Constraints as Assert;

class InstanceGetResponseDto extends AbstractDto
{
    #[Assert\NotBlank]
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
     * @var array<CellGroupDto> $groups
     */
    public array $groups;
}
