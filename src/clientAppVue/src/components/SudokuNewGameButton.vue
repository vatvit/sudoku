<script setup lang="ts">
import axios from "axios";
import {useRouter} from "vue-router";

const emit = defineEmits(['NewGameEvent'])

const router = useRouter();

async function createSudokuPuzzle() {
  const createResponse = await axios.post('/api/sudoku/puzzle') as {data: {puzzleId: string}};
  const puzzleId = createResponse.data.puzzleId as string;

  await router.push({name: 'SudokuPuzzle', params: {puzzleId: puzzleId}})

  emit('NewGameEvent', puzzleId)
}
</script>

<template>
  <button @click="createSudokuPuzzle">New game?</button>
</template>
