<?php

namespace App\Application\UseCase\Sudoku;

use App\Application\Service\Sudoku\InstanceGetter;
use App\Application\UseCase\Sudoku\Dto\GetInstanceInputDto;
use App\Application\UseCase\Sudoku\Dto\GetInstanceOutputDto;

class GetInstanceUseCase
{
    public function __construct(
        private readonly InstanceGetter $instanceGetter
    ) {
    }

    public function execute(GetInstanceInputDto $inputDto): GetInstanceOutputDto
    {
        $outputDto = new GetInstanceOutputDto();
        $outputDto->instance = $this->instanceGetter->getById($inputDto->instanceId);
        return $outputDto;
    }
}