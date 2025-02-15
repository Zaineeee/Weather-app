import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';

// Import App.vue component
import App from './App.vue';

// Import route components
import HomePage from './components/Untiled-1.vue';
import LandingPage from './components/landingpage.vue';
import SignIn from './components/sign-in.vue';
import SignUp from './components/sign-up.vue';
import Dashboard from './components/dashboard.vue';

// Import CSS
import '../css/app.css';
import '../css/dashboard.css';

// Create router
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
            path: '/sign-up', 
            name: 'SignUp',
            component: SignUp 
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
            component: () => import('./components/monitoring.vue'),
            meta: { requiresAuth: true }
        }
    ]
});

// Navigation guard
router.beforeEach((to, from, next) => {
    let isLoggedIn = false;
    let isAdmin = false;
    
    try {
        isLoggedIn = localStorage.getItem('userEmail') !== null;
        isAdmin = localStorage.getItem('isAdmin') === 'true';
    } catch (error) {
        console.error('Storage access error:', error);
        // Handle the error gracefully
        isLoggedIn = false;
        isAdmin = false;
    }
    
    if (to.meta.requiresAuth && !isLoggedIn) {
        next('/sign-in');
    } else if (to.meta.requiresAdmin && !isAdmin) {
        next('/landingpage');
    } else {
        next();
    }
});

// Create Vue app instance
const app = createApp(App);

// Use router
app.use(router);

// Mount app
app.mount('#app'); 