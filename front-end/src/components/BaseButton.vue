<script setup lang="ts">

</script>

<template>
  <button><slot /></button>
</template>

<style scoped>

button{
  border: none;
  border-radius: 5px;
  padding: 8px;
  cursor: pointer;
  transition: 0.2s;
}
button:hover{
  opacity: 0.9;
}

.button-primary{
  background-color: #40ac73;
}

.button-warn{
  background-color: #ff7524;
}

.button-danger{
  background-color: #ff2f2f;
}

button:disabled{
  opacity: 0.9;
  cursor: not-allowed;
  transition: 0s;
}

</style>