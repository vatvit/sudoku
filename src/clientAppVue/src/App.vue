<script setup lang="ts">

declare module 'vue' {
  interface ComponentCustomProperties {
    $sudoku: object
  }
}

</script>

<template>
  <router-view />
</template>

<style scoped>
</style>
