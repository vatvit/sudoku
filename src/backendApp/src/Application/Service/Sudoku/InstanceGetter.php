<?php

namespace App\Application\Service\Sudoku;

use App\Application\CQRS\Query\GetSudokuGameInstanceByIdHandler;
use App\Application\CQRS\Query\GetSudokuGameInstanceByIdQuery;
use App\Application\CQRS\Trait\HandleMultiplyTrait;
use App\Application\Service\Converter\JsonStringToArrayConverter;
use App\Application\Service\Sudoku\Dto\SudokuGameInstanceDto;
use App\Application\Service\Sudoku\Mapper\SudokuGameInstanceEntityToDtoMapper;
use App\Infrastructure\Entity\SudokuGameInstance;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

class InstanceGetter
{
    use HandleMultiplyTrait;

    public function __construct(
        private readonly SerializerInterface                 $serializer,
        private readonly SudokuGameInstanceEntityToDtoMapper $sudokuGameInstanceEntityToDtoMapper,
        MessageBusInterface                                  $messageBus
    )
    {
        $this->messageBus = $messageBus;
    }

    public function getById(Uuid $id): ?SudokuGameInstanceDto
    {
        $query = new GetSudokuGameInstanceByIdQuery($id);
        $sudokuGameInstanceEntity = $this->handleAndGetResultByHandlerName($query, GetSudokuGameInstanceByIdHandler::class);

        if ($sudokuGameInstanceEntity === null) {
            return null;
        }

        $sudokuGameInstanceDto = $this->sudokuGameInstanceEntityToDtoMapper->map($sudokuGameInstanceEntity);
        return $sudokuGameInstanceDto;
    }
}