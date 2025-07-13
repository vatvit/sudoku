<?php

namespace App\Interface\Controller\Sudoku\Mapper;

use App\Application\UseCase\Sudoku\Dto\CreateInstanceInputDto;
use App\Application\UseCase\Sudoku\Dto\GetInstanceInputDto;
use App\Interface\Controller\Sudoku\Dto\CreateInstanceRequestDto;
use Symfony\Component\Uid\Uuid;

class RequestToUseCaseMapper
{
    public function mapToCreateInstanceInput(CreateInstanceRequestDto $requestDto): CreateInstanceInputDto
    {
        $inputDto = new CreateInstanceInputDto();
        $inputDto->size = $requestDto->size;

        return $inputDto;
    }

    public function mapToGetInstanceInput(string $instanceId): GetInstanceInputDto
    {
        $inputDto = new GetInstanceInputDto();
        $inputDto->instanceId = Uuid::fromString($instanceId);

        return $inputDto;
    }
}