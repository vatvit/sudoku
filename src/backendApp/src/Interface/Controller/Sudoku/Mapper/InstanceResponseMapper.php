<?php

namespace App\Interface\Controller\Sudoku\Mapper;

use App\Application\Service\Sudoku\Dto\SudokuGameInstanceDto;
use App\Interface\Controller\Sudoku\Dto\InstanceCreateResponseDto;
use App\Interface\Controller\Sudoku\Dto\InstanceGetResponseDto;

class InstanceResponseMapper
{
    public function mapCreateResponse(SudokuGameInstanceDto $sudokuGameInstanceDto): InstanceCreateResponseDto
    {
        return InstanceCreateResponseDto::hydrate([
            'id' => $sudokuGameInstanceDto->id,
        ]);
    }

    public function mapGetResponse(SudokuGameInstanceDto $sudokuGameInstanceDto): InstanceGetResponseDto
    {
        $data = [
            'id' => $sudokuGameInstanceDto->id,
            'puzzle' => $sudokuGameInstanceDto->puzzle,
            'groups' => $sudokuGameInstanceDto->cellGroups,
            'cellValues' => $this->extractCellValues($sudokuGameInstanceDto),
            'notes' => $this->extractNotes($sudokuGameInstanceDto),
        ];
        $sudokuGameInstanceDto = InstanceGetResponseDto::hydrate($data);
        return $sudokuGameInstanceDto;
    }

    private function extractCellValues(SudokuGameInstanceDto $dto): array
    {
        // TODO: Return player actions when they are stored
        // Currently returns empty array since player actions are not persisted yet
        return [];
    }

    private function extractNotes(SudokuGameInstanceDto $dto): array
    {
        // TODO: Return player notes when they are stored
        // Currently returns empty array since player notes are not persisted yet
        return [];
    }
}