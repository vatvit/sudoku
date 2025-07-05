<?php

namespace App\Interface\Controller\Sudoku\Dto;

use App\Application\Service\Dto\AbstractDto;
use App\Application\Service\Dto\Attribute\ArrayItemType;
use App\Domain\Sudoku\Service\Dto\CellGroupDto;
use App\Domain\Sudoku\Service\Dto\CellRowCollectionDto;
use App\Domain\Sudoku\ValueObject\CellCoords;
use Symfony\Component\Validator\Constraints as Assert;

class InstanceGetResponseDto extends AbstractDto
{
    #[Assert\NotBlank]
    public string $id;

//    /**
//     * @var array<CellRowCollectionDto> $puzzle
//     */
//    #[Assert\All([
//        new Assert\Type(type: CellRowCollectionDto::class)
//    ])]
//    #[ArrayItemType(CellRowCollectionDto::class)]
    /** @var array<string, int> $puzzle */
    public array $puzzle;

    /**
     * @var array<CellGroupDto> $groups
     */
    #[Assert\All([
        new Assert\Type(type: CellGroupDto::class)
    ])]
    #[ArrayItemType(CellGroupDto::class)]
    public array $groups;

    /**
     * @var array<string, int> $cellValues
     */
    public array $cellValues;

    /**
     * @var array<string, array<int>> $notes
     */
    public array $notes;
}
