<?php

namespace App\Application\UseCase\Sudoku;

use App\Application\Service\Sudoku\InstanceCreator;
use App\Application\UseCase\Sudoku\Dto\CreateInstanceInputDto;
use App\Application\UseCase\Sudoku\Dto\CreateInstanceOutputDto;

class CreateInstanceUseCase
{
    public function __construct(
        private readonly InstanceCreator $instanceCreator
    ) {
    }

    public function execute(CreateInstanceInputDto $inputDto): CreateInstanceOutputDto
    {
        $sudokuGameInstanceDto = $this->instanceCreator->create($inputDto->size);

        $outputDto = new CreateInstanceOutputDto();
        $outputDto->instance = $sudokuGameInstanceDto;

        return $outputDto;
    }
}