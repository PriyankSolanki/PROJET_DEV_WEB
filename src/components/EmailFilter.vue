<script setup>
import {ref, computed, watch} from 'vue'

const props = defineProps({
  mails: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['filtered-mails'])

// États des filtres
const filters = ref({
  sender: '',
  keyword: '',
  dateFrom: '',
  dateTo: '',
  timeFrom: '',
  timeTo: ''
})

const showAdvancedFilters = ref(false)

// Fonction pour filtrer les emails
const filteredMails = computed(() => {
  let filtered = [...props.mails]

  // Filtre par expéditeur
  if (filters.value.sender.trim()) {
    const senderQuery = filters.value.sender.toLowerCase().trim()
    filtered = filtered.filter(mail => {
      const senderName = mail.from?.emailAddress?.name?.toLowerCase() || ''
      const senderEmail = mail.from?.emailAddress?.address?.toLowerCase() || ''
      return senderName.includes(senderQuery) || senderEmail.includes(senderQuery)
    })
  }

  // Filtre par mot-clé (sujet + contenu)
  if (filters.value.keyword.trim()) {
    const keywordQuery = filters.value.keyword.toLowerCase().trim()
    filtered = filtered.filter(mail => {
      const subject = mail.subject?.toLowerCase() || ''
      const bodyPreview = mail.bodyPreview?.toLowerCase() || ''
      return subject.includes(keywordQuery) || bodyPreview.includes(keywordQuery)
    })
  }

  // Filtre par date
  if (filters.value.dateFrom || filters.value.dateTo) {
    filtered = filtered.filter(mail => {
      if (!mail.receivedDateTime) return false

      const mailDate = new Date(mail.receivedDateTime)
      const mailDateOnly = new Date(mailDate.getFullYear(), mailDate.getMonth(), mailDate.getDate())

      let isInDateRange = true

      if (filters.value.dateFrom) {
        const fromDate = new Date(filters.value.dateFrom)
        isInDateRange = isInDateRange && mailDateOnly >= fromDate
      }

      if (filters.value.dateTo) {
        const toDate = new Date(filters.value.dateTo)
        isInDateRange = isInDateRange && mailDateOnly <= toDate
      }

      return isInDateRange
    })
  }

  // Filtre par heure
  if (filters.value.timeFrom || filters.value.timeTo) {
    filtered = filtered.filter(mail => {
      if (!mail.receivedDateTime) return false

      const mailDate = new Date(mail.receivedDateTime)
      const mailTime = mailDate.getHours() * 60 + mailDate.getMinutes()

      let isInTimeRange = true

      if (filters.value.timeFrom) {
        const [hours, minutes] = filters.value.timeFrom.split(':').map(Number)
        const fromTime = hours * 60 + minutes
        isInTimeRange = isInTimeRange && mailTime >= fromTime
      }

      if (filters.value.timeTo) {
        const [hours, minutes] = filters.value.timeTo.split(':').map(Number)
        const toTime = hours * 60 + minutes
        isInTimeRange = isInTimeRange && mailTime <= toTime
      }

      return isInTimeRange
    })
  }

  return filtered
})

// Fonction pour réinitialiser les filtres
const clearFilters = () => {
  filters.value = {
    sender: '',
    keyword: '',
    dateFrom: '',
    dateTo: '',
    timeFrom: '',
    timeTo: ''
  }
}

// Watcher pour émettre les résultats filtrés
watch(filteredMails, (newFilteredMails) => {
  emit('filtered-mails', newFilteredMails)
}, {immediate: true})

// Fonction pour obtenir la date d'aujourd'hui au format YYYY-MM-DD
const getTodayDate = () => {
  const today = new Date()
  return today.toISOString().split('T')[0]
}

// Fonction pour obtenir la date d'il y a 7 jours
const getWeekAgoDate = () => {
  const weekAgo = new Date()
  weekAgo.setDate(weekAgo.getDate() - 7)
  return weekAgo.toISOString().split('T')[0]
}

// Filtres rapides
const applyQuickFilter = (type) => {
  clearFilters()

  switch (type) {
    case 'today':
      filters.value.dateFrom = getTodayDate()
      filters.value.dateTo = getTodayDate()
      break
    case 'week':
      filters.value.dateFrom = getWeekAgoDate()
      filters.value.dateTo = getTodayDate()
      break
    case 'morning':
      filters.value.timeFrom = '06:00'
      filters.value.timeTo = '12:00'
      break
    case 'afternoon':
      filters.value.timeFrom = '12:00'
      filters.value.timeTo = '18:00'
      break
  }
}
</script>

<template>
  <div class="email-filter">
    <h3>Filtrer les emails</h3>

    <!-- Filtres principaux -->
    <div class="main-filters">
      <div class="filter-row">
        <div class="filter-group">
          <label>Expéditeur :</label>
          <input
              v-model="filters.sender"
              type="text"
              placeholder="Nom ou email de l'expéditeur"
              class="filter-input"
          />
        </div>

        <div class="filter-group">
          <label>Mot-clé :</label>
          <input
              v-model="filters.keyword"
              type="text"
              placeholder="Rechercher dans le sujet et contenu"
              class="filter-input"
          />
        </div>
      </div>
    </div>

    <!-- Filtres rapides -->
    <div class="quick-filters">
      <h4>Filtres rapides :</h4>
      <div class="quick-filter-buttons">
        <button @click="applyQuickFilter('today')" class="quick-btn">Aujourd'hui</button>
        <button @click="applyQuickFilter('week')" class="quick-btn">Cette semaine</button>
        <button @click="applyQuickFilter('morning')" class="quick-btn">Matin (6h-12h)</button>
        <button @click="applyQuickFilter('afternoon')" class="quick-btn">Après-midi (12h-18h)</button>
      </div>
    </div>

    <!-- Bouton pour afficher filtres avancés -->
    <div class="advanced-toggle">
      <button @click="showAdvancedFilters = !showAdvancedFilters" class="toggle-btn">
        {{ showAdvancedFilters ? 'Masquer' : 'Afficher' }} les filtres avancés
      </button>
    </div>

    <!-- Filtres avancés -->
    <div v-if="showAdvancedFilters" class="advanced-filters">
      <h4>Filtres avancés :</h4>

      <div class="filter-row">
        <div class="filter-group">
          <label>Date de :</label>
          <input
              v-model="filters.dateFrom"
              type="date"
              class="filter-input"
          />
        </div>

        <div class="filter-group">
          <label>Date à :</label>
          <input
              v-model="filters.dateTo"
              type="date"
              class="filter-input"
          />
        </div>
      </div>

      <div class="filter-row">
        <div class="filter-group">
          <label>Heure de :</label>
          <input
              v-model="filters.timeFrom"
              type="time"
              class="filter-input"
          />
        </div>

        <div class="filter-group">
          <label>Heure à :</label>
          <input
              v-model="filters.timeTo"
              type="time"
              class="filter-input"
          />
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="filter-actions">
      <button @click="clearFilters" class="clear-btn">Effacer tous les filtres</button>
      <span class="result-count">{{ filteredMails.length }} email(s) trouvé(s)</span>
    </div>
  </div>
</template>

<style scoped>
.email-filter {
  background-color: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  border: 1px solid #ddd;
}

.email-filter h3 {
  margin-top: 0;
  color: #0078d4;
  text-align: center;
}

.email-filter h4 {
  margin: 15px 0 10px 0;
  color: #333;
}

.main-filters {
  margin-bottom: 20px;
}

.filter-row {
  display: flex;
  gap: 15px;
  margin-bottom: 15px;
  flex-wrap: wrap;
}

.filter-group {
  flex: 1;
  min-width: 200px;
}

.filter-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
  color: #333;
}

.filter-input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.filter-input:focus {
  outline: none;
  border-color: #0078d4;
  box-shadow: 0 0 0 2px rgba(0, 120, 212, 0.2);
}

.quick-filters {
  margin-bottom: 20px;
}

.quick-filter-buttons {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.quick-btn {
  background-color: #e3f2fd;
  color: #0078d4;
  border: 1px solid #0078d4;
  padding: 6px 12px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
  transition: all 0.2s;
}

.quick-btn:hover {
  background-color: #0078d4;
  color: white;
}

.advanced-toggle {
  text-align: center;
  margin-bottom: 15px;
}

.toggle-btn {
  background-color: transparent;
  color: #0078d4;
  border: 1px solid #0078d4;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.toggle-btn:hover {
  background-color: #f0f8ff;
}

.advanced-filters {
  background-color: white;
  padding: 15px;
  border-radius: 4px;
  border: 1px solid #ddd;
  margin-bottom: 20px;
}

.filter-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
}

.clear-btn {
  background-color: #dc3545;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
}

.clear-btn:hover {
  background-color: #c82333;
}

.result-count {
  font-weight: bold;
  color: #0078d4;
}

@media (max-width: 768px) {
  .filter-row {
    flex-direction: column;
  }

  .filter-group {
    min-width: auto;
  }

  .quick-filter-buttons {
    justify-content: center;
  }

  .filter-actions {
    flex-direction: column;
    text-align: center;
  }
}
</style>