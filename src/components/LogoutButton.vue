<script>
import { logout } from '@/lib/microsoftGraph'
import { useUserStore } from '@/stores/userStore'
import BaseButton from "@/components/BaseButton.vue";


export default {
  components: {BaseButton},
  setup() {
    const userStore = useUserStore()
    const signOut = async () => {
      userStore.clearUser()
      await logout()
    }

    return { signOut }
  }
}
</script>

<template>
  <BaseButton @click="signOut"><font-awesome-icon icon="right-from-bracket" /> Sign Out</BaseButton>
</template>

<style scoped>

</style>