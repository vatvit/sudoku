<script setup lang="ts">
import {ref, onMounted} from 'vue'
import axios from "axios"
import SudokuTable from './components/Sudoku/Table.vue'
import MercureSubscribe from './components/MercureSubscribe.vue'
import {CellDto, CellGroupDto, TableStateDto as SudokuTableStateDTO} from "./components/Sudoku/Dto.ts";

declare module 'vue' {
  interface ComponentCustomProperties {
    $sudoku: object
  }
}

const sudokuTableStateDTO = ref<SudokuTableStateDTO>({cells: [], groups: [] as CellGroupDto[]})

onMounted(async () => {
  await loadSudokuTable()
})

async function loadSudokuTable() {
  const response = await axios.get('/api/sudoku/table/load')
  const sudokuTableState = response.data as SudokuTableStateDTO
  for (let key in sudokuTableState.groups) {
    sudokuTableState.groups[key].cells = new Map<string, CellDto>(Object.entries(sudokuTableState.groups[key].cells))
  }
  sudokuTableStateDTO.value = sudokuTableState
}

</script>

<template>
  <SudokuTable :stateDto="sudokuTableStateDTO" @newGameEvent="loadSudokuTable"/>
  <MercureSubscribe />
</template>

<style scoped>
</style>
