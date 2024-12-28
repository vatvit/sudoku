<script setup lang="ts">
import {ref, onMounted} from 'vue'
import axios from "axios"
import SudokuPuzzle from '@/components/Sudoku/Puzzle.vue'
import {CellDto, CellGroupDto, PuzzleStateDto as SudokuTableStateDTO} from "./components/Sudoku/Dto.ts";
import {useRoute, useRouter} from "vue-router";
import SudokuNewGameButton from "@/components/SudokuNewGameButton.vue";

const sudokuTableStateDTO = ref<SudokuTableStateDTO>({cells: [], groups: [] as CellGroupDto[]})

const route = useRoute()
const router = useRouter()

const puzzleId = route.params.puzzleId as string

onMounted(async () => {
  await loadSudokuTable(puzzleId)
})

async function NewGameEventHandler(puzzleId: string) {
  await loadSudokuTable(puzzleId)
}

async function loadSudokuTable(puzzleId: string) {
  const response = await axios.get('/api/sudoku/puzzle/' + puzzleId)
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
