import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '@/pages/HomePage.vue'
import MailsIndexPage from '@/pages/MailsIndexPage.vue'
import MailDetail from '@/pages/MailDetail.vue'

const routes = [
    { path: '/', name: 'Home', component: HomePage },
    { path: '/mails', name: 'Mails', component: MailsIndexPage },
    { path: '/mail/:id', name: 'MailDetail', component: MailDetail }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router