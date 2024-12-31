<script setup lang="ts">
import axios from "axios";
import {useRouter} from "vue-router";

const emit = defineEmits(['NewGameEvent'])

const router = useRouter();

async function createSudokuPuzzle() {
  const createResponse = await axios.post('/api/games/sudoku/instances') as {data: {id: string}};
  const id = createResponse.data.id as string;

  await router.push({name: 'SudokuPuzzle', params: {puzzleId: id}})

  emit('NewGameEvent', id)
}
</script>

<template>
  <button @click="createSudokuPuzzle">New game?</button>
</template>
