import { createRouter, createWebHistory } from 'vue-router';
const Home = () => import('../Paginas/Inicio.vue');
const Login = () => import('../Vistas/IniciarSesion.vue');
const Register = () => import('../Paginas/Autenticacion/Registrar.vue');
const PanelPasajero = () => import('../Paginas/Pasajero/Panel.vue');
// const AdminDashboard = () => import('../Paginas/Admin/Dashboard.vue'); // No existe, comentar o crear si es necesario
import { useAuthStore } from '../Almacenes/almacenAutenticacion';

const routes = [
    {
      path: '/conductor/perfil',
      name: 'conductor-perfil',
      component: () => import('../Paginas/Conductor/Perfil.vue'),
      meta: { requiresAuth: true }
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
    path: '/dashboard/seguimiento/:id',
    name: 'pasajero.seguimiento',
    component: () => import('../Pages/Pasajero/PasajeroSeguimiento.vue'),
    meta: { requiresAuth: true, role: 'pasajero' }
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

// Guard global para verificar autenticación
router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore();

  // Inicializar autenticación si aún no se ha hecho
  if (!auth.initialized) {
    await auth.checkAuth();
  }

  // Si la ruta requiere autenticación y no está autenticado
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { path: '/login' };
  }

  // Si está autenticado y trata de ir a login o register, redirigir al dashboard según su rol
  if (auth.isAuthenticated && (to.path === '/login' || to.path === '/register')) {
    const dashboardRoute = auth.getDashboardRoute();
    return { path: dashboardRoute };
  }

  return true;
});

export default router;

