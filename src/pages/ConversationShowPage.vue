<!-- src/pages/ConversationShowPage.vue -->
<template>
  <div>
    <h1>Conversation ID: {{ conversationId }}</h1>
  </div>
</template>

<script>
import { useRoute } from 'vue-router'

export default {
  setup() {
    const route = useRoute()
    const conversationId = route.params.id

    return { conversationId }
  }
}
</script>
