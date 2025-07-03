<template>
  <base-header>
    <div v-if="user.user">
      <LogoutButton @userChanged="handleUserRemoved"></LogoutButton>
    </div>
    <div v-else>
      <SigninButton @userChanged="handleUserChanged"></SigninButton>
    </div>
  </base-header>
  <BaseLayout>
    <router-view />
  </BaseLayout>
  <base-footer></base-footer>

</template>

<script>
import BaseFooter from "@/components/BaseFooter.vue";
import BaseHeader from "@/components/BaseHeader.vue";
import BaseLayout from "@/components/BaseLayout.vue";
import SigninButton from "@/components/SigninButton.vue";
import { useUserStore } from '@/stores/userStore';
import LogoutButton from "@/components/LogoutButton.vue";

export default {
  name: 'App',
  components: {
    SigninButton,
    BaseLayout,
    BaseHeader,
    BaseFooter,
    LogoutButton
  },
  data() {
    return {
      clickCount: 0,
      user: useUserStore()
    }
  },
  methods: {
    handleUserChanged(newUser) {
      this.user = newUser;
    },
    handleUserRemoved() {
      this.user = null;
    }
  }

}

</script>


<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
  padding: 0;
  margin: 0;
}
</style>
