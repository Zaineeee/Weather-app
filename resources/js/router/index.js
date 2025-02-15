import { createRouter, createWebHistory } from 'vue-router';
import SignIn from '../components/sign-in.vue';
import LandingPage from '../components/landingpage.vue';
import HomePage from '../components/Untiled-1.vue';
import Dashboard from '../components/dashboard.vue';
import Monitoring from '../components/monitoring.vue';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'Home',
            component: HomePage
        },
        {
            path: '/sign-in',
            name: 'SignIn',
            component: SignIn
        },
        {
            path: '/landingpage',
            name: 'LandingPage',
            component: LandingPage,
            meta: { requiresAuth: true }
        },
        {
            path: '/dashboard',
            name: 'Dashboard',
            component: Dashboard,
            meta: { requiresAuth: true, requiresAdmin: true }
        },
        {
            path: '/monitoring',
            name: 'Monitoring',
            component: Monitoring,
            meta: { requiresAuth: true }
        }
    ]
});

// Navigation guard
router.beforeEach((to, from, next) => {
    try {
        const isLoggedIn = localStorage.getItem('userEmail') !== null || 
                          sessionStorage.getItem('userEmail') !== null;
        const isAdmin = localStorage.getItem('isAdmin') === 'true';

        if (to.meta.requiresAuth && !isLoggedIn) {
            next('/sign-in');
        } else if (to.meta.requiresAdmin && !isAdmin) {
            next('/landingpage');
        } else {
            next();
        }
    } catch (error) {
        console.error('Auth check error:', error);
        next('/');
    }
});

export default router;