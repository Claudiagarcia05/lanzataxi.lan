import { createRouter, createWebHistory } from 'vue-router';
import PassengerDashboard from '../views/PassengerDashboard.vue';
import Login from '../views/Login.vue';
import Home from '../pages/Home.vue';
import { useAuthStore } from '../stores/authStore';

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home,
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
  },
  {
    path: '/dashboard',
    name: 'passenger-dashboard',
    component: PassengerDashboard,
    meta: { requiresAuth: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to) => {
  const auth = useAuthStore();

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { path: '/login' };
  }

  if (auth.isAuthenticated && to.path === '/login') {
    return { path: '/dashboard' };
  }

  return true;
});

export default router;
