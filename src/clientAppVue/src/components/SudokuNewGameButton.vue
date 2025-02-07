<script setup lang="ts">
import {useRouter} from "vue-router";

import {Api} from "@/generated/Api";

const api = new Api().api;

const emit = defineEmits(['NewGameEvent'])

const router = useRouter();

async function createSudokuPuzzle() {
  const response = await api.postCreateGameSudokuInstance();
  const id = response.data.id;

  await router.push({name: 'SudokuPuzzle', params: {puzzleId: id}})

  emit('NewGameEvent', id)
}
</script>

<template>
  <button @click="createSudokuPuzzle">New game?</button>
</template>
