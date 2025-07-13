<?php

namespace App\Application\UseCase\Sudoku\Dto;

use App\Application\Service\Dto\AbstractDto;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class GetInstanceInputDto extends AbstractDto
{
    #[Assert\NotNull]
    #[Assert\Type(type: Uuid::class)]
    public Uuid $instanceId;
}