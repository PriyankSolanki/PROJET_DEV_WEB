<script setup>
import { useRoute } from 'vue-router'
import { useUserStore } from '@/stores/userStore';
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

const store = useUserStore();
const route = useRoute();
</script>

<template>
  <header class="main-header fade-down">
    <nav class="nav-container">
      <router-link to="/" class="nav-tab" :class="{ active: route.path === '/' }">
        <FontAwesomeIcon :icon="['fas', 'home']" />
        <span>Accueil</span>
      </router-link>

      <router-link v-if="store.user" to="/mails" class="nav-tab" :class="{ active: route.path.startsWith('/mails') }">
        <FontAwesomeIcon :icon="['fas', 'envelope']" />
        <span>Mails</span>
      </router-link>
      <router-link v-if="store.user" to="/stats" class="nav-tab" :class="{ active: route.path.startsWith('/stats') }">
        <FontAwesomeIcon :icon="['fas', 'chart-bar']" />
        <span>Stats</span>
      </router-link>

    </nav>

    <div class="brand">
      📬 <span>SyMaila</span>
    </div>


    <div class="nav-right">
      <slot />
    </div>
  </header>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

.main-header {
  background: linear-gradient(to right, #209cff, #68e0cf);
  color: #fff;
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-family: 'Inter', sans-serif;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  border-radius: 0 0 1.5rem 1.5rem;
  position: sticky;
  top: 0;
  z-index: 100;
  flex-wrap: wrap;
}

.nav-container {
  display: flex;
  gap: 1rem;
  align-items: center;
  flex: 1;
}

.nav-tab {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  color: #fff;
  text-decoration: none;
  font-weight: 500;
  border-radius: 0.75rem;
  transition: background 0.3s, transform 0.3s;
}

.nav-tab:hover {
  background-color: rgba(255, 255, 255, 0.15);
  transform: scale(1.03);
}

.nav-tab.active {
  background-color: rgba(255, 255, 255, 0.25);
  font-weight: 700;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.brand {
  font-size: 1.25rem;
  font-weight: 700;
  flex: 1;
  text-align: center;
}

.nav-right {
  flex: 1;
  display: flex;
  justify-content: flex-end;
}

/* Animation */
.fade-down {
  animation: fadeDown 0.8s ease forwards;
  opacity: 0;
}

@keyframes fadeDown {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }

  to {
    transform: translateY(0);
    opacity: 1;
  }
}
</style>
