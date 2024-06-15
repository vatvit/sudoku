<script setup lang="ts">
import {ref, onMounted} from 'vue'
import axios from "axios"
import SudokuTable from './components/Sudoku/Table.vue'
import MercureSubscribe from './components/MercureSubscribe.vue'
import {TableStateDto as SudokuTableStateDTO} from "./components/Sudoku/Dto.ts";

declare module 'vue' {
  interface ComponentCustomProperties {
    $sudoku: object
  }
}

const sudokuTableStateDTO = ref<SudokuTableStateDTO>({cells: []})

onMounted(async () => {
  await loadSudokuTable()
})

async function loadSudokuTable() {
  const response = await axios.get('/api/sudoku/table/load')
  sudokuTableStateDTO.value = response.data as SudokuTableStateDTO
}

</script>

<template>
  <SudokuTable :stateDto="sudokuTableStateDTO" @newGameEvent="loadSudokuTable"/>
  <MercureSubscribe />
</template>

<style scoped>
</style>
