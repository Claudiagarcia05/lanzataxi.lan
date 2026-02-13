<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../../stores/authStore';

const props = defineProps({
  modelValue: Boolean,
});

const emit = defineEmits(['update:modelValue']);

const authStore = useAuthStore();
const isLogin = ref(true);
const loading = ref(false);
const error = ref('');

const formData = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'passenger',
});

const closeModal = () => {
  emit('update:modelValue', false);
  resetForm();
};

const resetForm = () => {
  formData.value = {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'passenger',
  };
  error.value = '';
};

const handleSubmit = async () => {
  loading.value = true;
  error.value = '';

  try {
    if (isLogin.value) {
      const result = await authStore.login({
        email: formData.value.email,
        password: formData.value.password,
      });

      if (result.success) {
        closeModal();
        if (authStore.isPassenger) window.location.href = '/dashboard';
        else if (authStore.isDriver) window.location.href = '/driver/dashboard';
        else window.location.href = '/admin/dashboard';
      } else {
        error.value = result.error || 'Error al iniciar sesion';
      }
    } else {
      console.log('Registro:', formData.value);
      isLogin.value = true;
      error.value = 'Registro exitoso. Ahora inicia sesion';
    }
  } catch (e) {
    error.value = 'Error en la autenticacion';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <Teleport to="body">
    <div v-if="modelValue" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>

      <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-8 transform transition-all">
          <button @click="closeModal" class="absolute top-4 right-4 text-neutral-slate hover:text-neutral-dark" aria-label="Close">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <div class="text-center mb-8">
            <div class="text-4xl mb-2">🚕</div>
            <h2 class="text-2xl font-bold text-lanzarote-blue">LanzaTaxi</h2>
            <p class="text-neutral-slate text-sm mt-1">
              {{ isLogin ? 'Bienvenido de nuevo' : 'Crea tu cuenta' }}
            </p>
          </div>

          <div v-if="error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm">
            {{ error }}
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-4">
            <div v-if="!isLogin">
              <label class="block text-sm font-medium text-neutral-dark mb-1">Nombre completo</label>
              <input
                v-model="formData.name"
                type="text"
                required
                class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue focus:border-transparent"
                placeholder="Tu nombre"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-neutral-dark mb-1">Email</label>
              <input
                v-model="formData.email"
                type="email"
                required
                class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue focus:border-transparent"
                placeholder="tucorreo@ejemplo.com"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-neutral-dark mb-1">Contrasena</label>
              <input
                v-model="formData.password"
                type="password"
                required
                class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue focus:border-transparent"
                placeholder="••••••••"
              >
            </div>

            <div v-if="!isLogin">
              <label class="block text-sm font-medium text-neutral-dark mb-1">Confirmar contrasena</label>
              <input
                v-model="formData.password_confirmation"
                type="password"
                required
                class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue focus:border-transparent"
                placeholder="••••••••"
              >
            </div>

            <div v-if="!isLogin">
              <label class="block text-sm font-medium text-neutral-dark mb-2">Tipo de usuario</label>
              <div class="grid grid-cols-3 gap-2">
                <button
                  type="button"
                  @click="formData.role = 'passenger'"
                  :class="[
                    'p-2 rounded-lg border text-sm transition-all',
                    formData.role === 'passenger'
                      ? 'bg-lanzarote-blue text-white border-lanzarote-blue'
                      : 'border-neutral-volcanic text-neutral-dark hover:border-lanzarote-blue'
                  ]"
                >
                  👤 Pasajero
                </button>
                <button
                  type="button"
                  @click="formData.role = 'driver'"
                  :class="[
                    'p-2 rounded-lg border text-sm transition-all',
                    formData.role === 'driver'
                      ? 'bg-lanzarote-blue text-white border-lanzarote-blue'
                      : 'border-neutral-volcanic text-neutral-dark hover:border-lanzarote-blue'
                  ]"
                >
                  🚕 Taxista
                </button>
                <button
                  type="button"
                  @click="formData.role = 'admin'"
                  :class="[
                    'p-2 rounded-lg border text-sm transition-all',
                    formData.role === 'admin'
                      ? 'bg-lanzarote-blue text-white border-lanzarote-blue'
                      : 'border-neutral-volcanic text-neutral-dark hover:border-lanzarote-blue'
                  ]"
                >
                  👑 Admin
                </button>
              </div>
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-lanzarote-blue text-white py-3 px-4 rounded-lg font-semibold hover:bg-lanzarote-yellow hover:text-black transition-all disabled:opacity-50 disabled:cursor-not-allowed mt-6"
            >
              <span v-if="!loading">{{ isLogin ? 'Iniciar sesion' : 'Crear cuenta' }}</span>
              <span v-else class="flex items-center justify-center">
                <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
                Procesando...
              </span>
            </button>

            <p class="text-center text-sm text-neutral-slate mt-4">
              {{ isLogin ? 'No tienes cuenta?' : 'Ya tienes cuenta?' }}
              <button
                type="button"
                @click="isLogin = !isLogin; error = ''"
                class="text-lanzarote-blue font-semibold hover:underline ml-1"
              >
                {{ isLogin ? 'Registrate' : 'Inicia sesion' }}
              </button>
            </p>
          </form>
        </div>
      </div>
    </div>
  </Teleport>
</template>
