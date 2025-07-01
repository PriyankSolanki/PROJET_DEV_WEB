<script setup>
import { useUserStore } from '@/stores/userStore'
import BaseButton from "@/components/BaseButton.vue";
const userStore = useUserStore()
</script>

<template>
  <div>

    <p v-if="userStore.user">Welcome <strong>{{ userStore.user.user.name }}</strong></p>
    <p v-else>Welcome, Guest</p>
    <p v-if="userStore.user">Mail <strong>{{userStore.user.user.username}}</strong></p>
    <div class="buttons">
      <router-link v-if="userStore.user" to="/mails"><BaseButton class="button-primary">Mails</BaseButton></router-link>
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