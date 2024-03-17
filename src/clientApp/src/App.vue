<script setup lang="ts">
import SudokuTable from './components/Sudoku/Table.vue'
import MercureSubscribe from './components/MercureSubscribe.vue'
import {CellDTO as SudokuCellDTO, TableStateDTO as SudokuTableStateDTO} from "./components/Sudoku/DTO.ts";

declare module 'vue' {
  interface ComponentCustomProperties {
    $sudoku: object
  }
}

const sudokuTableStateDTO: SudokuTableStateDTO = {
  cells: []
};

for (let col = 0; col < 9; col++) {
  if (typeof sudokuTableStateDTO.cells[col] === 'undefined') {
    sudokuTableStateDTO.cells[col] = []
  }

  for (let row = 0; row < 9; row++) {
    const cell: SudokuCellDTO = {
      col: col + 1,
      row: row + 1,
      groups: [],
      value: (col + 1) * 10 + row + 1
    }
    sudokuTableStateDTO.cells[col][row] = cell
  }
}

</script>

<template>
  <SudokuTable :stateDTO="sudokuTableStateDTO"/>
  <MercureSubscribe />
</template>

<style scoped>
</style>
