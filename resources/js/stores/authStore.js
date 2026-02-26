import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

export function useAuthStore() {
  const user = ref(null);
  const token = ref(null);

  // Devuelve la ruta del dashboard según el rol
  function getDashboardRoute() {
    if (!user.value) return '/';
    switch (user.value.role) {
      case 'conductor':
        return '/conductor/dashboard';
      case 'admin':
        return '/admin/dashboard';
      default:
        return '/pasajero/home';
    }
  }

  // Login seguro: nunca redirige si hay error
  async function login({ email, password }) {
    try {
      const response = await axios.post('/api/login', { email, password });
      if (response.data && response.data.token) {
        token.value = response.data.token;
        user.value = response.data.user;
        // Guardar token en localStorage si se desea persistencia
        localStorage.setItem('token', token.value);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
        return { success: true };
      }
      return { success: false, error: 'Respuesta inesperada del servidor.' };
    } catch (error) {
      // Si el backend responde con error 401, mostrar mensaje personalizado
      if (error.response && error.response.status === 401) {
        return {
          success: false,
          error: error.response.data.message || 'Credenciales inválidas. Por favor verifica tu email y contraseña.'
        };
      }
      // Otros errores
      return {
        success: false,
        error: error.response?.data?.message || 'Error al intentar iniciar sesión.'
      };
    }
  }

  // Registro seguro: valida email antes de enviar
  async function register({ name, email, phone, password, password_confirmation, role }) {
    // Validación extra de email en el store
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!re.test(email)) {
      return { success: false, error: 'Email no válido' };
    }
    try {
      const response = await axios.post('/api/register', {
        name,
        email,
        phone,
        password,
        password_confirmation,
        role
      });
      if (response.data && response.data.token) {
        token.value = response.data.token;
        user.value = response.data.user;
        localStorage.setItem('token', token.value);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
        return { success: true };
      }
      return { success: false, error: 'Respuesta inesperada del servidor.' };
    } catch (error) {
      return {
        success: false,
        error: error.response?.data?.message || 'Error al intentar registrar usuario',
        errors: error.response?.data?.errors || {}
      };
    }
  }

  return {
    user,
    token,
    login,
    register,
    getDashboardRoute
  };
}
