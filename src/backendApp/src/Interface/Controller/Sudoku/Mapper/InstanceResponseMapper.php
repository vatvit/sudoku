<?php

namespace App\Interface\Controller\Sudoku\Mapper;

use App\Application\Service\Sudoku\Dto\SudokuGameInstanceDto;
use App\Interface\Controller\Sudoku\Dto\InstanceCreateResponseDto;
use App\Interface\Controller\Sudoku\Dto\InstanceGetResponseDto;

class InstanceResponseMapper
{
    public function mapCreateResponse(string $instanceId): InstanceCreateResponseDto
    {
        return InstanceCreateResponseDto::hydrate([
            'id' => $instanceId,
        ]);
    }

    public function mapGetResponse(SudokuGameInstanceDto $sudokuGameInstanceDto): InstanceGetResponseDto
    {
        $sudokuGameInstanceArray = $sudokuGameInstanceDto->toArray();
        $puzzle = $this->applyHiddenCells($sudokuGameInstanceArray['grid'], $sudokuGameInstanceArray['hiddenCells']);
        $data = [
            'id' => $sudokuGameInstanceArray['id'],
            'cells' => $puzzle,
            'groups' => $sudokuGameInstanceArray['cellGroups'],
        ];
        $sudokuGameInstanceDto = InstanceGetResponseDto::hydrate($data);
        return $sudokuGameInstanceDto;
    }

    private function applyHiddenCells(array $grid, array $hiddenCells): array
    {
        foreach ($hiddenCells as $hiddenCell) {
            [$rowIndex, $colIndex] = $hiddenCell->getCoords();

            if (isset($grid[$rowIndex][$colIndex])) {
                $grid[$rowIndex][$colIndex]['value'] = 0;
            }
        }

        return $grid;
    }
}