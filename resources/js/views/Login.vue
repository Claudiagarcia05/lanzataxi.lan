<template>
  <div class="min-h-screen bg-neutral-soft flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-lanzarote-blue">LanzaTaxi</h1>
        <p class="text-neutral-slate text-sm mt-2">Inicia sesiÃ³n para continuar</p>
      </div>

      <!-- Usuarios de prueba -->
      <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <p class="text-xs font-semibold text-blue-800 mb-2">Usuarios de prueba:</p>
        <div class="text-xs text-blue-700 space-y-1">
          <p><strong>Cliente:</strong> cliente@test.com</p>
          <p><strong>Taxista:</strong> taxista@test.com</p>
          <p><strong>Admin:</strong> admin@test.com</p>
          <p class="mt-2"><strong>ContraseÃ±a:</strong> password</p>
        </div>
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
            placeholder="ejemplo@correo.com"
          >
        </div>

        <div>
          <label class="block text-sm font-medium text-neutral-slate mb-1">ContraseÃ±a</label>
          <input
            v-model="form.password"
            type="password"
            autocomplete="current-password"
            required
            class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue focus:border-transparent"
            placeholder="Tu contraseÃ±a"
          >
        </div>

        <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-sm text-red-800 font-semibold">{{ error }}</p>
        </div>

        <button
          type="submit"
          :disabled="cargando"
          class="w-full bg-lanzarote-blue text-white py-3 px-4 rounded-lg font-semibold hover:bg-lanzarote-yellow hover:text-black transition-all disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="!cargando">Entrar</span>
          <span v-else>Entrando...</span>
        </button>
        <div class="text-center mt-4">
          <p class="text-sm text-neutral-slate">
            Â¿No tienes cuenta?
            <router-link to="/register" class="text-lanzarote-blue font-semibold hover:underline">
              RegÃ­strate
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
import { useAuthStore } from '../stores/authStore';

const router = useRouter();
const auth = useAuthStore();

const cargando = ref(false);
const error = ref('');
const form = ref({
  email: '',
  password: '',
});

const handleLogin = async () => {
  cargando.value = true;
  error.value = '';

  console.log('Intentando login con:', { email: form.value.email });

  const result = await auth.login(form.value);
  cargando.value = false;

  console.log('Resultado login:', result);

  if (result.success) {
    console.log('Login exitoso, limpiando formulario y redirigiendo...');
    
    // Limpiar el formulario
    form.value = {
      email: '',
      password: '',
    };

    // Redirigir segÃºn el rol del usuario
    const dashboardRoute = auth.getDashboardRoute();
    console.log('Ruta de redirecciÃ³n:', dashboardRoute);
    router.push(dashboardRoute);
    return;
  }

  console.error('Error en login:', result.error);
  error.value = result.error || 'No se pudo iniciar sesiÃ³n';
};
</script>
