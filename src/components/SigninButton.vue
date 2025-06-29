<template>
  <BaseButton @click="login"><font-awesome-icon icon="user" /> Sign in</BaseButton>
</template>

<script>
//import { signInAndGetUser } from '@/lib/microsoftGraph';
import { useUserStore } from '@/stores/userStore';
import BaseButton from "@/components/BaseButton.vue";

export default {
  components: {BaseButton},
  setup() {
    const userStore = useUserStore()

    const login = async () => {
      const user = {name : 'priyank'}
      userStore.setUser(user)
    }

    return { login }
  }
}
</script>
