import axios from 'axios';
window.axios = axios;

// Configuración base de axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.headers.common['Content-Type'] = 'application/json';

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
            // Si hay un error 401, limpiar el almacenamiento local
            localStorage.removeItem('token');
            localStorage.removeItem('usuario');
            delete window.axios.defaults.headers.common['Authorization'];
            
            // Redirigir al login solo si no estamos ya en una página de autenticación
            if (!window.location.pathname.includes('login') && !window.location.pathname.includes('register')) {
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);