import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '@/pages/HomePage.vue'
import ConversationsIndexPage from '@/pages/ConversationsIndexPage.vue'
import {useUserStore} from "@/stores/userStore";
import ConversationShowPage from "@/pages/ConversationShowPage.vue";

const routes = [
    { path: '/', name: 'Home', component: HomePage, meta: { requiresAuth: false } },
    { path: '/conversations', name: 'Conversations', component: ConversationsIndexPage, meta: { requiresAuth: false } },
    { path: '/conversations/:id', name: 'ConversationID', component: ConversationShowPage, meta: { requiresAuth: true }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    const userStore = useUserStore()
    if (to.meta.requiresAuth && !userStore.user) {
        next('/')
    }else {
        next()
    }
})

export default router
