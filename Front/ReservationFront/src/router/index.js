import { createRouter, createWebHistory } from 'vue-router'
import Login from '../Login.vue'
import Home from '../Home.vue'
import Reservation from '../Reservation.vue'

const routes = [
    {
        path: '/',
        name: 'Login',
        component: Login
    }, {
        path: '/home',
        name: 'Home',
        component: Home
    }, {
        path: '/reservation',
        name: 'Reservation',
        component: Reservation
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    const isLogged = localStorage.getItem('user');
    if (to.name !== 'Login' && !isLogged) {
        next({ name: 'Login' }); // Redirection si pas connecté
    } else {
        next(); // on continue
    }
})

export default router
