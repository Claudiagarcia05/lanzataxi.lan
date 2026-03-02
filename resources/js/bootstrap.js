import axios from 'axios';
window.axios = axios;

// Configuración base de axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.headers.common['Content-Type'] = 'application/json';

// CSRF para rutas web (POST /logout, etc.)
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

// Configurar el token de autenticación si existe
const token = localStorage.getItem('token');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Interceptor para manejar errores 401 (no autorizado)
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            const requestUrl = error.config?.url || '';
            const isAuthRequest =
                requestUrl.includes('/api/login') ||
                requestUrl.includes('/api/register') ||
                requestUrl.includes('/api/me') ||
                requestUrl.includes('/api/logout');

            // Si el 401 viene del propio login/registro/checkAuth, NO redirigir.
            // El UI debe poder mostrar el mensaje de error sin cambiar de ruta.
            if (!isAuthRequest) {
                const hadToken = !!localStorage.getItem('token');

                // Si hay un error 401, limpiar el almacenamiento local
                localStorage.removeItem('token');
                localStorage.removeItem('usuario');
                delete window.axios.defaults.headers.common['Authorization'];
                document.cookie = 'token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT; SameSite=Lax';

                // Redirigir al login solo si veníamos autenticados (token previo) y no estamos ya en auth.
                if (
                    hadToken &&
                    !window.location.pathname.includes('login') &&
                    !window.location.pathname.includes('register')
                ) {
                    window.location.href = '/login';
                }
            }
        }
        
        return Promise.reject(error);
    }
);