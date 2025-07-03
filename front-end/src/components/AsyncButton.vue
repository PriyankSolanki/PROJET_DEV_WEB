<script>

import BaseButton from "@/components/BaseButton.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

export default {
  name: "AsyncButton",
  components: { FontAwesomeIcon, BaseButton },
  inheritAttrs: false,
  props: {
    color: {
      type: String,
      default: "primary",
    }
  },
  data() {
    return {
      isPending: false
    }
  },

  methods: {
    handleClick() {
      const originalOnClick = /** @type {() => Promise<void>} */ (this.$attrs.onClick)
      if (typeof originalOnClick !== 'function') return

      this.isPending = true
      originalOnClick().finally(() => { this.isPending = false })
    }
  }
}
</script>

<template>
  <base-button class="button-primary" :disabled="isPending" :color="color" @click.stop.prevent="handleClick">
    <font-awesome-icon v-if="isPending" :icon="['fas', 'circle-notch']" pulse />
    <slot />
  </base-button>
</template>

<style scoped></style>