<?php

namespace App\Controller\Sudoku\Dto;

use App\Service\Dto\AbstractDto;
use Symfony\Component\Validator\Constraints as Assert;

class InstanceCreateResponseDto extends AbstractDto
{
    #[Assert\NotBlank]
    public string $id;
}
