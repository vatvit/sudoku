<script setup lang="ts">
import {ref, onMounted, defineAsyncComponent} from 'vue'
import axios from "axios"
// import SudokuPuzzle from '@/components/Sudoku/Puzzle.vue'
import {CellDto, CellGroupDto, PuzzleStateDto as SudokuTableStateDTO} from "./components/Sudoku/Dto.ts";
import {useRoute, useRouter} from "vue-router";
import SudokuNewGameButton from "@/components/SudokuNewGameButton.vue";

const sudokuTableStateDTO = ref<SudokuTableStateDTO>({id: '', cells: [], groups: [] as CellGroupDto[]})

const route = useRoute()
const router = useRouter()

const puzzleId = route.params.puzzleId as string

const SudokuPuzzle = defineAsyncComponent(async () => {
  await loadSudokuTable(puzzleId)
  return import('@/components/Sudoku/Puzzle.vue')
})

async function NewGameEventHandler(puzzleId: string) {
  await loadSudokuTable(puzzleId)
}

async function loadSudokuTable(id: string) {
  const response = await axios.get('/api/games/sudoku/instances/' + id)
  const sudokuTableState = response.data as SudokuTableStateDTO
  for (const key in sudokuTableState.groups) {
    sudokuTableState.groups[key].cells = new Map<string, CellDto>(Object.entries(sudokuTableState.groups[key].cells))
  }
  sudokuTableStateDTO.value = sudokuTableState
}
</script>

<template>
  <SudokuNewGameButton @NewGameEvent="NewGameEventHandler"></SudokuNewGameButton>
  <SudokuPuzzle :stateDto="sudokuTableStateDTO"/>
</template>

<style scoped>
</style>
