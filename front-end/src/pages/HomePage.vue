<script setup>
import { useUserStore } from '@/stores/userStore'
import BaseButton from "@/components/BaseButton.vue";
const userStore = useUserStore()
</script>

<template>
  <div class="page-container fade-in">
    <section class="intro-text">
      <h2>Bienvenue dans votre gestionnaire de mails</h2>
      <p>
        Gérez facilement vos emails : consultation, lecture, favoris, filtres, statistiques, et bien plus.
        Connectez-vous pour découvrir toutes les fonctionnalités et simplifier votre messagerie.
      </p>
    </section>

    <div class="container">
      <div class="welcome-box">
        <h1 class="title">
          {{ userStore.user ? `Bienvenue, ${userStore.user.user.name}` : "Bienvenue" }}
        </h1>
        <p v-if="userStore.user" class="info">Email : <strong>{{ userStore.user.user.username }}</strong></p>

        <div class="buttons">
          <router-link v-if="userStore.user" to="/mails">
            <BaseButton class="button-primary">📬 Voir mes mails</BaseButton>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

* {
  box-sizing: border-box;
}

.page-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #f0f4ff, #ffffff);
  font-family: 'Inter', sans-serif;
  color: #333;
  padding: 4rem 2rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.intro-text {
  max-width: 720px;
  margin-bottom: 4rem;
  color: #444;
}

.intro-text h2 {
  font-weight: 700;
  font-size: 2.4rem;
  margin-bottom: 1rem;
}

.intro-text p {
  font-size: 1.25rem;
  line-height: 1.6;
  max-width: 600px;
  margin: 0 auto;
}

.container {
  width: 100%;
  max-width: 480px;
}

.welcome-box {
  background-color: #fff;
  border-radius: 1.25rem;
  padding: 3rem 3.5rem;
  box-shadow: 0 12px 30px rgba(59, 130, 246, 0.15);
  transition: box-shadow 0.3s ease;
}

.welcome-box:hover {
  box-shadow: 0 20px 40px rgba(59, 130, 246, 0.25);
}

.title {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: #1e293b;
}

.info {
  font-size: 1rem;
  color: #64748b;
  margin-bottom: 2.5rem;
}

.buttons {
  display: flex;
  justify-content: center;
}

.button-primary {
  background-color: #3b82f6;
  color: white;
  font-weight: 600;
  padding: 0.85rem 2rem;
  border-radius: 0.75rem;
  border: none;
  cursor: pointer;
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4);
  font-size: 1.125rem;
}

.button-primary:hover {
  background-color: #2563eb;
  box-shadow: 0 10px 25px rgba(37, 99, 235, 0.5);
}

.fade-in {
  animation: fadeIn 1.2s ease forwards;
  opacity: 0;
}

@keyframes fadeIn {
  to {
    opacity: 1;
  }
}
</style>
