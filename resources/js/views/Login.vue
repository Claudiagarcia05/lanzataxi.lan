<template>
  <div class="min-h-screen bg-neutral-soft flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-lanzarote-blue">LanzaTaxi</h1>
        <p class="text-neutral-slate text-sm mt-2">Inicia sesion para continuar</p>
      </div>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-neutral-slate mb-1">Email</label>
          <input
            v-model="form.email"
            type="email"
            autocomplete="email"
            required
            class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue focus:border-transparent"
          >
        </div>

        <div>
          <label class="block text-sm font-medium text-neutral-slate mb-1">Contrasena</label>
          <input
            v-model="form.password"
            type="password"
            autocomplete="current-password"
            required
            class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue focus:border-transparent"
          >
        </div>

        <div v-if="error" class="text-sm text-red-600">
          {{ error }}
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-lanzarote-blue text-white py-3 px-4 rounded-lg font-semibold hover:bg-lanzarote-yellow hover:text-black transition-all disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="!loading">Entrar</span>
          <span v-else>Entrando...</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/authStore';

const router = useRouter();
const auth = useAuthStore();

const loading = ref(false);
const error = ref('');
const form = ref({
  email: '',
  password: '',
});

const handleLogin = async () => {
  loading.value = true;
  error.value = '';

  const result = await auth.login(form.value);
  loading.value = false;

  if (result.success) {
    router.push('/');
    return;
  }

  error.value = result.error || 'No se pudo iniciar sesion';
};
</script>
