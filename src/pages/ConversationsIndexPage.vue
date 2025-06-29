<script>
export default {
  name: 'ConversationsIndexPage'
}
</script>

<template>
  <h1>Conversations</h1>
  <div>
      <li> <router-link to="/conversations/1">conversations 1</router-link></li>
      <li> <router-link to="/conversations/2">conversations 2</router-link></li>
      <li> <router-link to="/conversations/3">conversations 3</router-link></li>
      <li> <router-link to="/conversations/4">conversations 4</router-link></li>
  </div>
</template>

<style scoped>
div{
  display: inline-block;
}
</style>