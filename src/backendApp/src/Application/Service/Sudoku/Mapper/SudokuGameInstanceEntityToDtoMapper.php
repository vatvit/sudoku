<?php

namespace App\Application\Service\Sudoku\Mapper;

use App\Application\Service\Converter\JsonStringToArrayConverter;
use App\Application\Service\Sudoku\Dto\SudokuGameInstanceDto;
use App\Infrastructure\Entity\SudokuGameInstance;

class SudokuGameInstanceEntityToDtoMapper
{
    public function map(SudokuGameInstance $sudokuGameInstanceEntity): SudokuGameInstanceDto
    {
        $gridJson = $sudokuGameInstanceEntity->getSudokuPuzzle()->getSudokuGrid()->getGrid();
        $grid = JsonStringToArrayConverter::convert($gridJson);
        $cellGroupsJson = $sudokuGameInstanceEntity->getSudokuPuzzle()->getSudokuGrid()->getCellGroups();
        $cellGroups = JsonStringToArrayConverter::convert($cellGroupsJson);

        $hiddenCells = $sudokuGameInstanceEntity->getSudokuPuzzle()->getHiddenCells();
        $puzzle = $this->applyHiddenCells($grid, $hiddenCells);

        // TODO: this is hardcode. remove it
        $values = [];
        foreach ($hiddenCells as $hiddenCellCoords) {
            $values[$hiddenCellCoords] = 3;
        }

        $data = [
            'id' => $sudokuGameInstanceEntity->getId(),
            'grid' => $grid,
            'puzzle' => $puzzle,
            'cellGroups' => $cellGroups,
            'hiddenCells' => $hiddenCells,
            'cellValues' => $values,
            'isSolved' => $sudokuGameInstanceEntity->isSolved(),
            'createdAt' => $sudokuGameInstanceEntity->getCreatedAt(),
            'updatedAt' => $sudokuGameInstanceEntity->getUpdatedAt(),
            'startedAt' => $sudokuGameInstanceEntity->getStartedAt(),
            'finishedAt' => $sudokuGameInstanceEntity->getFinishedAt(),
        ];

        return SudokuGameInstanceDto::hydrate($data);
    }

    private function applyHiddenCells(array $grid, array $hiddenCells): array
    {
        $puzzle = [];
        foreach ($grid as $rowIndex => $row) {
            foreach ($row as $colIndex => $cell) {
                $coords = $rowIndex . ':' . $colIndex;
                $puzzle[$coords] = in_array($coords, $hiddenCells) ? 0 : $cell['value'];
            }
        }
        return $puzzle;
//        foreach ($hiddenCells as $hiddenCell) {
//            [$rowIndex, $colIndex] = explode(':', $hiddenCell);
//
//            if (isset($grid[$rowIndex][$colIndex])) {
//                $grid[$rowIndex][$colIndex]['value'] = 0;
//            }
//        }
//
//        return $grid;
    }
}