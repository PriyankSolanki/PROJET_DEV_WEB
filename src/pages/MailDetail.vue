<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getMailById } from '@/lib/microsoftGraph'

const route = useRoute()
const router = useRouter()

const mail = ref(null)
const loading = ref(true)
const error = ref(null)

const mailId = computed(() => route.params.id)

const loadMail = async () => {
  try {
    loading.value = true
    error.value = null

    if (!mailId.value) {
      throw new Error('ID du mail non fourni')
    }

    mail.value = await getMailById(mailId.value)
  } catch (err) {
    console.error('Erreur lors du chargement du mail:', err)
    error.value = err.message || 'Erreur lors du chargement du mail'
  } finally {
    loading.value = false
  }
}

const goBack = () => {
  router.go(-1)
}

const formatDate = (dateString) => {
  if (!dateString) return 'Date inconnue'

  const date = new Date(dateString)
  return date.toLocaleString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  loadMail()
})
</script>

<template>
  <div>
    <h1>Détails du Mail</h1>

    <div class="back-section">
      <button @click="goBack" class="back-button">← Retour</button>
    </div>

    <div v-if="loading">
      <p>Chargement du mail...</p>
    </div>

    <div v-else-if="error">
      <p>Erreur : {{ error }}</p>
      <button @click="loadMail">Réessayer</button>
    </div>

    <div v-else-if="mail" class="mail-content">
      <div class="mail-info">
        <h2>{{ mail.subject || 'Sans objet' }}</h2>

        <p><strong>De :</strong> {{ mail.from?.emailAddress?.name || 'Expéditeur inconnu' }} ({{ mail.from?.emailAddress?.address }})</p>

        <p v-if="mail.toRecipients && mail.toRecipients.length > 0">
          <strong>À :</strong>
          <span v-for="(recipient, index) in mail.toRecipients" :key="index">
            {{ recipient.emailAddress?.name || recipient.emailAddress?.address }}
            <span v-if="index < mail.toRecipients.length - 1">, </span>
          </span>
        </p>

        <p><strong>Date :</strong> {{ formatDate(mail.receivedDateTime) }}</p>

        <div class="mail-body">
          <h3>Contenu :</h3>
          <div v-if="mail.body?.contentType === 'html'" v-html="mail.body.content"></div>
          <pre v-else>{{ mail.body?.content || 'Aucun contenu' }}</pre>
        </div>

        <div v-if="mail.attachments && mail.attachments.length > 0" class="attachments">
          <h3>Pièces jointes ({{ mail.attachments.length }}) :</h3>
          <ul>
            <li v-for="attachment in mail.attachments" :key="attachment.id">
              {{ attachment.name }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
h1 {
  text-align: center;
}

div {
  text-align: center;
}

.back-section {
  margin-bottom: 2rem;
}

.back-button {
  background-color: #0078d4;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
}

.back-button:hover {
  background-color: #106ebe;
}

.mail-content {
  max-width: 800px;
  margin: 0 auto;
  text-align: left;
}

.mail-info {
  background-color: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  margin: 20px 0;
}

.mail-info h2 {
  color: #0078d4;
  margin-top: 0;
}

.mail-body {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #ddd;
}

.mail-body pre {
  white-space: pre-wrap;
  word-wrap: break-word;
  background-color: white;
  padding: 15px;
  border-radius: 4px;
  border: 1px solid #ddd;
}

.attachments {
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #ddd;
}

.attachments ul {
  text-align: left;
  list-style-type: disc;
  padding-left: 20px;
}
</style>