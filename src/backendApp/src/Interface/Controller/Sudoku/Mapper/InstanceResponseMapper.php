<?php

namespace App\Interface\Controller\Sudoku\Mapper;

use App\Interface\Controller\Sudoku\Dto\InstanceCreateResponseDto;
use App\Interface\Controller\Sudoku\Dto\InstanceGetResponseDto;

class InstanceResponseMapper
{
    public function mapCreate(string $instanceId): InstanceCreateResponseDto
    {
        return InstanceCreateResponseDto::hydrate([
            'id' => $instanceId,
        ]);
    }

    public function mapGet(array $table): InstanceGetResponseDto
    {
        return InstanceGetResponseDto::hydrate($table);
    }
}