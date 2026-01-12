import { createRouter, createWebHistory } from 'vue-router'
import login from '../login.vue'
import alumno from '../alumno/alumno.vue'
const routes = [
    {
        path: '/',
        name: 'login',
        component: login
    },
    {
        path: '/alumno',
        name: 'alumno',
        component: alumno
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router