import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '@/pages/HomePage.vue'
import MailsIndexPage from '@/pages/MailsIndexPage.vue'
import StatsPage from '@/pages/StatsPage.vue'

const routes = [
    { path: '/', name: 'Home', component: HomePage },
    { path: '/mails', name: 'Mails', component: MailsIndexPage},
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
