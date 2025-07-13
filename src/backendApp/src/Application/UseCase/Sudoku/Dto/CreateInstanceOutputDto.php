<?php

namespace App\Application\UseCase\Sudoku\Dto;

use App\Application\Service\Dto\AbstractDto;
use App\Application\Service\Sudoku\Dto\SudokuGameInstanceDto;
use Symfony\Component\Validator\Constraints as Assert;

class CreateInstanceOutputDto extends AbstractDto
{
    #[Assert\NotNull]
    #[Assert\Type(type: SudokuGameInstanceDto::class)]
    public SudokuGameInstanceDto $instance;
}