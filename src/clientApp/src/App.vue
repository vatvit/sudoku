<script setup lang="ts">
import SudokuTable from './components/Sudoku/Table.vue'
import MercureSubscribe from './components/MercureSubscribe.vue'
import {CellDTO as SudokuCellDTO, TableStateDTO as SudokuTableStateDTO} from "./components/Sudoku/DTO.ts";
import {CellGroupTypes} from "./components/Sudoku/CellGroup.ts";

declare module 'vue' {
  interface ComponentCustomProperties {
    $sudoku: object
  }
}

const sudokuTableStateDTO: SudokuTableStateDTO = {
  cells: []
};

for (let row = 0; row < 9; row++) {
  if (typeof sudokuTableStateDTO.cells[row] === 'undefined') {
    sudokuTableStateDTO.cells[row] = []
  }

  for (let col = 0; col < 9; col++) {
    const squareId = (Math.floor(col / 3) + 1) + (Math.floor(row / 3) * 3)
    const cell: SudokuCellDTO = {
      row: row + 1,
      col: col + 1,
      groups: [
        // {id: col + 1, type: CellGroupTypes.COL},
        // {id: row + 1, type: CellGroupTypes.ROW},
        {id: squareId, type: CellGroupTypes.SQR},
      ],
      value: col + 1
    }
    sudokuTableStateDTO.cells[row][col] = cell
  }
}

</script>

<template>
  <SudokuTable :stateDTO="sudokuTableStateDTO"/>
  <MercureSubscribe />
</template>

<style scoped>
</style>
