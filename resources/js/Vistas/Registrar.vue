<template>
  <div class="min-h-screen bg-neutral-soft flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-lanzarote-blue">LanzaTaxi</h1>
        <p class="text-neutral-slate text-sm mt-2">Crea tu cuenta para comenzar</p>
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-neutral-slate mb-1">Nombre completo</label>
          <input
            v-model="form.name"
            type="text"
            autocomplete="name"
            required
            class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue focus:border-transparent"
            placeholder="Tu nombre"
          >
          <p v-if="errors.name" class="text-xs text-red-600 mt-1">{{ errors.name[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-neutral-slate mb-1">Email</label>
          <input
            v-model="form.email"
            type="email"
            autocomplete="email"
            required
            class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue focus:border-transparent"
            placeholder="ejemplo@correo.com"
          >
          <p v-if="errors.email" class="text-xs text-red-600 mt-1">{{ errors.email[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-neutral-slate mb-1">Teléfono (opcional)</label>
          <input
            v-model="form.phone"
            type="tel"
            autocomplete="tel"
            class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue focus:border-transparent"
            placeholder="+34 600 000 000"
          >
          <p v-if="errors.phone" class="text-xs text-red-600 mt-1">{{ errors.phone[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-neutral-slate mb-1">Contraseña</label>
          <input
            v-model="form.password"
            type="password"
            autocomplete="new-password"
            required
            minlength="6"
            class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue focus:border-transparent"
            placeholder="Más de 6 caracteres"
          >
          <p v-if="errors.password" class="text-xs text-red-600 mt-1">{{ errors.password[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-neutral-slate mb-1">Confirmar contraseña</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            autocomplete="new-password"
            required
            minlength="6"
            class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue focus:border-transparent"
            placeholder="Repite tu contraseña"
          >
          <p v-if="errors.password_confirmation" class="text-xs text-red-600 mt-1">{{ errors.password_confirmation[0] }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-neutral-slate mb-1">Tipo de cuenta</label>
          <select
            v-model="form.role"
            class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue focus:border-transparent"
          >
            <option value="pasajero">Pasajero</option>
            <option value="conductor">Taxista</option>
          </select>
        </div>

        <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-sm text-red-800 font-semibold">{{ error }}</p>
        </div>

        <button
          type="submit"
          :disabled="cargando"
          class="w-full bg-lanzarote-blue text-white py-3 px-4 rounded-lg font-semibold hover:bg-lanzarote-yellow hover:text-black transition-all disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="!cargando">Registrarse</span>
          <span v-else>Registrando...</span>
        </button>

        <div class="text-center mt-4">
          <p class="text-sm text-neutral-slate">
            ¿Ya tienes cuenta?
            <router-link to="/login" class="text-lanzarote-blue font-semibold hover:underline">
              Inicia sesión
            </router-link>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../Almacenes/almacenAutenticacion';

const router = useRouter();
const auth = useAuthStore();

const cargando = ref(false);
const error = ref('');
const errors = ref({});
const form = ref({
  name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  role: 'pasajero',
});

function validarEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

const handleRegister = async () => {
  error.value = '';
  errors.value = {};
  if (!validarEmail(form.value.email)) {
    error.value = 'Email no válido';
    return;
  }
  cargando.value = true;

  console.log('Intentando registro con:', { 
    name: form.value.name, 
    email: form.value.email,
    role: form.value.role 
  });

  const result = await auth.register(form.value);
  cargando.value = false;

  console.log('Resultado registro:', result);

  if (result.success) {
    // Limpiar el formulario
    form.value = {
      name: '',
      email: '',
      phone: '',
      password: '',
      password_confirmation: '',
      role: 'pasajero',
    };
    // Redirigir según el rol del usuario autenticado
    const rol = result.usuario?.role || form.value.role;
    if (rol === 'conductor') {
      router.push('/conductor/dashboard');
    } else {
      router.push('/dashboard');
    }
    return;
  }

  console.error('Error en registro:', result.error);
  error.value = result.error || 'No se pudo registrar el usuario';
  errors.value = result.errors || {};
};
</script>