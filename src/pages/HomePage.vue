<script setup>
import { useUserStore } from '@/stores/userStore'
const userStore = useUserStore()
</script>

<template>
  <div>
    <h1> Welcome to our new mailing project</h1>
    <h1 v-if="userStore.user">Welcome, {{ userStore.user.name }}</h1>
    <h1 v-else>Welcome, Guest</h1>
    <div class="buttons">
      <slot />
    </div>
  </div>
</template>

<style scoped>
h1{
  text-align: center;
}
div{
  text-align: center;
}

.buttons{
  display: flex;
  flex-direction: column;
  gap: 1rem;
  align-items: center;
}
</style>