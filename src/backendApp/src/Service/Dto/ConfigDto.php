<?php

namespace App\Service\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ConfigDto extends AbstractDto
{
    #[Assert\Length(min: 10, max: 500)]
    public string $mercurePublicUrl;

    public array $allUsers;

    #[Assert\DateTime]
    public string $cachedDatetime;
}