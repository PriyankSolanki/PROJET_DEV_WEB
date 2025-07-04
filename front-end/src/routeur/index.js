import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '@/pages/HomePage.vue'
import MailsIndexPage from '@/pages/MailsIndexPage.vue'
import StatsPage from '@/pages/StatsPage.vue'
import GenerateEmail from '@/pages/GenerateMail.vue'

const routes = [
    { path: '/', name: 'Home', component: HomePage },
    { path: '/mails', name: 'Mails', component: MailsIndexPage},
    { path: '/mailgen', name: 'Generator Mial', component: GenerateEmail},
    {
    path: '/stats',
    name: 'Stats',
    component: StatsPage,
  }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
