<script setup>
import { useUserStore } from '@/stores/userStore';
import { computed } from 'vue'
const store = useUserStore()

const user = computed(() => store.user)
const mails = computed(() => store.user.mails)
</script>

<template>
  <h1>Mails</h1>
  <div v-if="!user">Veuillez vous connecter</div>

  <div v-else>
    <p>Bienvenue {{ user.user.name }} ({{ user.user.username }})</p>

    <ul>
      <li v-for="mail in mails" :key="mail.id">
        <strong>Sujet :</strong> {{ mail.subject || '(Sans sujet)' }}<br />
        <strong>De :</strong> {{ mail.from?.emailAddress?.name }} ({{ mail.from?.emailAddress?.address }})
      </li>
    </ul>
  </div>
</template>

<style scoped>
div{
  display: inline-block;
}
</style>