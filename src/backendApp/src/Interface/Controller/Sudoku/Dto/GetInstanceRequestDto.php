<?php

namespace App\Interface\Controller\Sudoku\Dto;

use App\Application\Service\Dto\AbstractDto;
use Symfony\Component\Validator\Constraints as Assert;

class GetInstanceRequestDto extends AbstractDto
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Uuid]
    public string $instanceId;
}