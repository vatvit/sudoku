<?php

namespace App\Interface\Controller\Core\Mapper;

use App\Interface\Controller\Core\Dto\ConfigResponseDto;

class ConfigResponseMapper
{
    public function mapIndex(string $mercurePublicUrl, array $allUsers, string $cachedDatetime): ConfigResponseDto
    {
        $responseDto = ConfigResponseDto::hydrate([
            'mercurePublicUrl' => $mercurePublicUrl,
            'allUsers' => $allUsers,
            'cachedDatetime' => $cachedDatetime,
        ]);

        return $responseDto;
    }
}