<?php

namespace App\Interface\Controller\Sudoku\Dto;

use App\Application\Service\Dto\AbstractDto;
use Symfony\Component\Validator\Constraints as Assert;

class CreateInstanceRequestDto extends AbstractDto
{
    #[Assert\Type('integer')]
    #[Assert\Range(min: 4, max: 16)]
    public int $size = 9;
}