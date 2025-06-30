import { createApp } from 'vue'
import App from './App.vue'
import { FontAwesomeIcon } from './lib/fontAwesome.js'
import { createPinia } from 'pinia'
import router from '@/routeur'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)
const app = createApp(App)

app.use(pinia)
app.use(router)
app.component('font-awesome-icon', FontAwesomeIcon)
app.mount('#app')
