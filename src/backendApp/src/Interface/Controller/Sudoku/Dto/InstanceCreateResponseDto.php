<?php

namespace App\Interface\Controller\Sudoku\Dto;

use App\Application\Service\Dto\AbstractDto;
use Symfony\Component\Validator\Constraints as Assert;

class InstanceCreateResponseDto extends AbstractDto
{
    #[Assert\NotBlank]
    public string $id;
}
