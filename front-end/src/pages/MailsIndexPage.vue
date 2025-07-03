<script setup>
import { ref, computed } from 'vue'
import { useUserStore } from '@/stores/userStore'

const store = useUserStore()
const user = computed(() => store.user)
const mails = computed(() => store.user?.mails || [])

const selectedMail = ref(null)
const filter = ref('all')
const actionMenuOpenFor = ref(null)

function selectMail(mail) {
  selectedMail.value = mail
  actionMenuOpenFor.value = null
}

function preview(text, length = 80) {
  return text?.length > length ? text.slice(0, length) + '…' : text
}

const filteredMails = computed(() => {
  if (!mails.value) return []
  switch (filter.value) {
    case 'unread':
      return mails.value.filter(mail => !mail.isRead)
    case 'attachments':
      return mails.value.filter(mail => mail.hasAttachments)
    case 'favorites':
      return mails.value.filter(mail => mail.isFavorite)
    default:
      return mails.value
  }
})

const visibleFilteredMails = computed(() =>
  filteredMails.value.filter(mail => !mail.isHidden)
)

function toggleActionMenu(id) {
  actionMenuOpenFor.value = actionMenuOpenFor.value === id ? null : id
}

function toggleRead(mail) {
  mail.isRead = !mail.isRead
  store.updateMail(mail)
}

function toggleFavorite(mail) {
  mail.isFavorite = !mail.isFavorite
}

function toggleHidden(mail) {
  mail.isHidden = !mail.isHidden
  if (selectedMail.value?.id === mail.id && mail.isHidden) {
    selectedMail.value = null
  }
}
</script>

<template>
  <div class="mail-view-container">
    <div class="mail-list">
      <h2 class="section-title">📬 Vos Mails</h2>

      <div class="filter-menu">
        <button :class="{ active: filter === 'all' }" @click="filter = 'all'">Tous</button>
        <button :class="{ active: filter === 'unread' }" @click="filter = 'unread'">Non lus</button>
        <button :class="{ active: filter === 'attachments' }" @click="filter = 'attachments'">Avec pièces
          jointes</button>
        <button :class="{ active: filter === 'favorites' }" @click="filter = 'favorites'">Favoris</button>
      </div>

      <div v-if="!user" class="no-user">
        Veuillez vous connecter pour consulter vos mails.
      </div>

      <div v-else>
        <div v-for="mail in visibleFilteredMails" :key="mail.id" class="mail-preview" @click="selectMail(mail)"
          :class="{ selected: selectedMail?.id === mail.id }">
          <div class="mail-subject">{{ preview(mail.subject || '(Sans sujet)', 60) }}</div>
          <div class="mail-from">
            {{ mail.from?.emailAddress?.name }} ({{ mail.from?.emailAddress?.address }})
          </div>

          <div class="actions-menu-container" @click.stop>
            <button class="actions-toggle-btn" @click="toggleActionMenu(mail.id)">⋮</button>
            <div v-if="actionMenuOpenFor === mail.id" class="actions-menu">
              <button @click="toggleRead(mail)">
                {{ mail.isRead ? 'Marquer comme non lu' : 'Marquer comme lu' }}
              </button>
              <button @click="toggleFavorite(mail)">
                {{ mail.isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris' }}
              </button>
              <button @click="toggleHidden(mail)">
                {{ mail.isHidden ? 'Afficher le mail' : 'Masquer le mail' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mail-content" v-if="selectedMail">
      <h3 class="mail-title">{{ selectedMail.subject || '(Sans sujet)' }}</h3>
      <p class="mail-from-full">
        De : <strong>{{ selectedMail.from?.emailAddress?.name }}</strong>
        ({{ selectedMail.from?.emailAddress?.address }})
      </p>
      <div class="mail-body" v-html="selectedMail.body?.content || 'Contenu non disponible.'"></div>
    </div>

    <div class="mail-placeholder" v-else>
      <p>Sélectionnez un mail pour le lire.</p>
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

.mail-view-container {
  display: flex;
  min-height: 100vh;
  font-family: 'Inter', sans-serif;
  background-color: #f9fafb;
  color: #333;
  padding: 2rem;
  gap: 2rem;
  flex-wrap: wrap;
}

.mail-list {
  flex: 1;
  min-width: 300px;
  max-width: 400px;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.filter-menu {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}

.filter-menu button {
  padding: 0.4rem 0.8rem;
  background: transparent;
  border: 1.5px solid #ccc;
  border-radius: 0.5rem;
  cursor: pointer;
  font-weight: 600;
  color: #444;
  transition: background-color 0.3s, border-color 0.3s;
}

.filter-menu button.active,
.filter-menu button:hover {
  background-color: #3b82f6;
  border-color: #3b82f6;
  color: white;
}

.mail-preview {
  position: relative;
  background: #ffffff;
  border-radius: 0.75rem;
  padding: 1rem;
  margin-bottom: 1rem;
  cursor: pointer;
  border: 1px solid transparent;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
  transition: all 0.25s ease;
  display: flex;
  flex-direction: column;
}

.mail-preview:hover {
  background-color: #f1f5ff;
  border-color: #cddaff;
}

.mail-preview.selected {
  border-color: #3b82f6;
  background-color: #e8f1ff;
}

.mail-subject {
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 0.25rem;
  color: #111827;
}

.mail-from {
  font-size: 0.9rem;
  color: #6b7280;
}

/* Actions menu */
.actions-menu-container {
  position: absolute;
  top: 1rem;
  right: 1rem;
}

.actions-toggle-btn {
  background: transparent;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #666;
  padding: 0 4px;
  user-select: none;
  line-height: 1;
  transition: color 0.3s;
}

.actions-toggle-btn:hover {
  color: #3b82f6;
}

.actions-menu {
  position: absolute;
  top: 1.8rem;
  right: 0;
  background: white;
  border-radius: 0.5rem;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  min-width: 160px;
  z-index: 10;
}

.actions-menu button {
  background: transparent;
  border: none;
  text-align: left;
  padding: 0.5rem 1rem;
  font-weight: 600;
  color: #333;
  cursor: pointer;
  transition: background-color 0.25s;
}

.actions-menu button:hover {
  background-color: #e0e7ff;
}

.mail-content {
  flex: 2;
  background: #ffffff;
  border-radius: 1rem;
  padding: 2rem;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
  min-width: 320px;
  max-width: 100%;
}

.mail-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: #1f2937;
}

.mail-from-full {
  font-size: 0.95rem;
  margin-bottom: 1.5rem;
  color: #374151;
}

.mail-body {
  line-height: 1.6;
  font-size: 1rem;
  color: #111827;
}

.mail-placeholder {
  flex: 2;
  min-width: 320px;
  background-color: #ffffff;
  border-radius: 1rem;
  padding: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #999;
  font-style: italic;
}
</style>
