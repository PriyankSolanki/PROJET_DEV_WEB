<template>
  <base-header><SigninButton @userChanged="handleUserChanged"></SigninButton></base-header>
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

export default {
  name: 'App',
  components: {
    SigninButton,
    BaseLayout,
    BaseHeader,
    BaseFooter,
  },
  data() {
    return {
      clickCount: 0,
      user: null
    }
  },
  methods: {
    handleUserChanged(newUser) {
      this.user = newUser;
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
