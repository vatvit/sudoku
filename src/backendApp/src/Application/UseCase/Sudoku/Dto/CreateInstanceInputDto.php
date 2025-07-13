<?php

namespace App\Application\UseCase\Sudoku\Dto;

use App\Application\Service\Dto\AbstractDto;
use Symfony\Component\Validator\Constraints as Assert;

class CreateInstanceInputDto extends AbstractDto
{
    #[Assert\Type('integer')]
    #[Assert\Range(min: 4, max: 16)]
    public int $size = 9;
}