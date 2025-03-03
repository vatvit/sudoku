<?php

namespace App\Application\Service\Sudoku;

use App\Application\CQRS\Query\GetSudokuGameInstanceByIdHandler;
use App\Application\CQRS\Query\GetSudokuGameInstanceByIdQuery;
use App\Application\CQRS\Trait\HandleMultiplyTrait;
use App\Application\Service\Converter\JsonStringToArrayConverter;
use App\Application\Service\Sudoku\Dto\SudokuGameInstanceDto;
use App\Infrastructure\Entity\SudokuGameInstance;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;

class InstanceGetter
{
    use HandleMultiplyTrait;

    public function __construct(
        private readonly SerializerInterface $serializer,
        MessageBusInterface                  $messageBus
    )
    {
        $this->messageBus = $messageBus;
    }

    public function getById(Uuid $id): ?SudokuGameInstanceDto
    {
        // TODO: Change return type to DTO
        $query = new GetSudokuGameInstanceByIdQuery($id);
        $sudokuGameInstanceEntity = $this->handleAndGetResultByHandlerName($query, GetSudokuGameInstanceByIdHandler::class);

        if ($sudokuGameInstanceEntity === null) {
            return null;
        }

        $sudokuGameInstanceDto = $this->mapEntityToDTO($sudokuGameInstanceEntity);
        return $sudokuGameInstanceDto;
    }

    private function mapEntityToDTO(SudokuGameInstance $sudokuGameInstanceEntity): SudokuGameInstanceDto
    {
        $gridJson = $sudokuGameInstanceEntity->getSudokuPuzzle()->getSudokuGrid()->getGrid();
        $grid = JsonStringToArrayConverter::convert($gridJson);
        $cellGroupsJson = $sudokuGameInstanceEntity->getSudokuPuzzle()->getSudokuGrid()->getCellGroups();
        $cellGroups = JsonStringToArrayConverter::convert($cellGroupsJson);
        $data = [
            'id' => $sudokuGameInstanceEntity->getId(),
            'grid' => $grid,
            'cellGroups' => $cellGroups,
            'hiddenCells' => $sudokuGameInstanceEntity->getSudokuPuzzle()->getHiddenCells(),
            'isSolved' => $sudokuGameInstanceEntity->isSolved(),
            'createdAt' => $sudokuGameInstanceEntity->getCreatedAt(),
            'updatedAt' => $sudokuGameInstanceEntity->getUpdatedAt(),
            'startedAt' => $sudokuGameInstanceEntity->getStartedAt(),
            'finishedAt' => $sudokuGameInstanceEntity->getFinishedAt(),
        ];
        $sudokuGameInstanceDto = SudokuGameInstanceDto::hydrate($data);
        return $sudokuGameInstanceDto;
    }
}