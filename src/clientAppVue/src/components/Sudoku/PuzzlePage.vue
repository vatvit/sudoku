<script setup lang="ts">
import {ref, onMounted, defineAsyncComponent} from 'vue'
import type {CellDto, CellGroupDto, PuzzleStateDto as SudokuTableStateDTO} from "./Dto.ts";
import {useRoute, useRouter} from "vue-router";
import SudokuNewGameButton from "@/components/SudokuNewGameButton.vue";
import {Api} from "@/generated/Api.ts";

const api = new Api().api;

const sudokuTableStateDTO = ref<SudokuTableStateDTO>({id: '', puzzle: [], groups: [] as CellGroupDto[], cellValues: {}, notes: {}})

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
  const response = await api.getGetGameSudokuInstance(id)
  const responseData = response.data as any
  
  // Create puzzle grid from puzzle property (contains hidden cells)
  const puzzle: CellDto[][] = []
  for (let row = 0; row < 9; row++) {
    puzzle[row] = []
    for (let col = 0; col < 9; col++) {
      const coords = `${row}:${col}`
      const puzzleValue = responseData.puzzle?.[coords] || 0
      puzzle[row][col] = {
        coords: coords,
        value: puzzleValue,
        notes: responseData.notes?.[coords] || []
      }
    }
  }
  
  // Map backend response to frontend DTO
  const sudokuTableState: SudokuTableStateDTO = {
    id: responseData.id,
    puzzle: puzzle,
    groups: responseData.groups.map((group: any) => ({
      id: group.id,
      type: group.type,
      cells: new Set(group.cells)
    })),
    cellValues: responseData.cellValues || {},
    notes: responseData.notes || {}
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
