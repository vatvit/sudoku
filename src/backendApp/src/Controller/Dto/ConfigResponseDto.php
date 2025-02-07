<?php

namespace App\Controller\Dto;

use App\Service\Dto\AbstractDto;
use Symfony\Component\Validator\Constraints as Assert;

class ConfigResponseDto extends AbstractDto
{
    #[Assert\Length(min: 10, max: 500)]
    public string $mercurePublicUrl;

    /**
     * @var mixed[]
     */
    public array $allUsers;

    #[Assert\DateTime]
    public string $cachedDatetime;
}
