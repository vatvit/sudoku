<script setup lang="ts">

defineProps(['id', 'data'])

</script>
<template>
  <div class="message-item">
    ID: {{ id }}<br />
    Data: {{ data }}
  </div>
</template>

<style scoped>
.message-item {
  border: 1px red solid;
  padding: 5px;
  margin: 5px;
}
</style>
