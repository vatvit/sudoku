<script>
export default {
  props: {
    id: String,
    data: Object,
  },
}
</script>
<template>
  <div class="message-item">
    ID: {{ id }}<br />
    Data: {{ data }}
  </div>
</template>

<style>
.message-item {
  border: 1px red;
  padding: 5px;
}
</style>
