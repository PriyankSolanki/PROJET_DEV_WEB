<script setup>
import { useUserStore } from '@/stores/userStore';
import { useRouter } from 'vue-router'
import { computed, ref } from 'vue'
import EmailFilter from '@/components/EmailFilter.vue'

const store = useUserStore()
const router = useRouter()

const user = computed(() => store.user)
const mails = computed(() => store.user?.mails || [])

// État pour les emails filtrés
const filteredMails = ref([])

const openMail = (mailId) => {
  router.push({ name: 'MailDetail', params: { id: mailId } })
}

// Fonction appelée quand les filtres changent
const onMailsFiltered = (filtered) => {
  filteredMails.value = filtered
}

// Emails à afficher (filtrés ou tous)
const mailsToDisplay = computed(() => {
  return filteredMails.value.length > 0 || hasActiveFilters.value ? filteredMails.value : mails.value
})

// Pour savoir s'il y a des filtres actifs (optionnel, pour l'affichage)
const hasActiveFilters = computed(() => {
  // Cette logique pourrait être améliorée en recevant l'état des filtres du composant enfant
  return filteredMails.value.length !== mails.value.length
})
</script>

<template>
  <div>
    <h1>Mails</h1>

    <div v-if="!user">Veuillez vous connecter</div>

    <div v-else>
      <p>Bienvenue {{ user.user.name }} ({{ user.user.username }})</p>

      <!-- Composant de filtrage -->
      <EmailFilter
          :mails="mails"
          @filtered-mails="onMailsFiltered"
      />

      <!-- Liste des emails (filtrés ou non) -->
      <div v-if="mailsToDisplay.length === 0" class="no-results">
        Aucun email trouvé avec les filtres appliqués.
      </div>

      <ul v-else>
        <li v-for="mail in mailsToDisplay" :key="mail.id" @click="openMail(mail.id)" class="mail-item">
          <strong>Sujet :</strong> {{ mail.subject || '(Sans sujet)' }}<br />
          <strong>De :</strong> {{ mail.from?.emailAddress?.name }} ({{ mail.from?.emailAddress?.address }})<br />
          <strong>Date :</strong> {{ new Date(mail.receivedDateTime).toLocaleString('fr-FR') }}
        </li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
div {
  display: inline-block;
}

h1 {
  text-align: center;
}

.mail-item {
  cursor: pointer;
  padding: 15px;
  border: 1px solid #ddd;
  margin: 10px 0;
  border-radius: 4px;
  transition: all 0.2s;
  background-color: white;
}

.mail-item:hover {
  background-color: #f0f8ff;
  border-color: #0078d4;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.no-results {
  text-align: center;
  padding: 40px;
  color: #666;
  font-style: italic;
  background-color: #f8f9fa;
  border-radius: 8px;
  margin: 20px 0;
}
</style>