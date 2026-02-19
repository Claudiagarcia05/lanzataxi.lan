import { createRouter, createWebHistory } from 'vue-router';
const Home = () => import('../Pages/Home.vue');
const Login = () => import('../views/Login.vue');
const Register = () => import('../views/Register.vue');
const PanelPasajero = () => import('../Pages/pasajero/Dashboard.vue');
const conductorDashboard = () => import('../Pages/conductor/Dashboard.vue');
const AdminDashboard = () => import('../Pages/Admin/Dashboard.vue');
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
    path: '/register',
    name: 'register',
    component: Register,
  },
  {
    path: '/dashboard',
    name: 'pasajero-dashboard',
    component: PanelPasajero,
    meta: { requiresAuth: true },
  },
  {
    path: '/conductor/dashboard',
    name: 'conductor-dashboard',
    component: conductorDashboard,
    meta: { requiresAuth: true },
  },
  {
    path: '/admin/dashboard',
    name: 'admin-dashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Guard global para verificar autenticaciÃ³n
router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();

  // Inicializar autenticaciÃ³n si aÃºn no se ha hecho
  if (!auth.initialized) {
    await auth.checkAuth();
  }

  // Si la ruta requiere autenticaciÃ³n y no estÃ¡ autenticado
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { path: '/login' };
  }

  // Si estÃ¡ autenticado y trata de ir a login o register, redirigir al dashboard segÃºn su rol
  if (auth.isAuthenticated && (to.path === '/login' || to.path === '/register')) {
    const dashboardRoute = auth.getDashboardRoute();
    return { path: dashboardRoute };
  }

  return true;
});

export default router;

