<script setup>
import { useUserStore } from '@/stores/userStore';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
const store = useUserStore();
</script>

<template>
  <header>
    <div>
      <router-link to="/"><font-awesome-icon icon="home" /> Home</router-link>
      <router-link v-if="store.user" to="/mails">Mails</router-link>
    </div>
    <div>MAILS MANAGER</div>
    <slot />
  </header>
</template>

<style scoped>
header{
  background-color: #25a4ed;
  display: flex;
  justify-content: space-between;
  margin: 0;
  padding: 15px;
  top: 0;
  left: 0;
  color: black;
}
header div {
  display: flex;
  align-items: center;
  gap: 15px;
}

header div a {
  text-decoration: none;
  color: #333;
}
</style>