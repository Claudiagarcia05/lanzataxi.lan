<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../../stores/authStore'

const props = defineProps({
  modelValue: Boolean
})

const emit = defineEmits(['update:modelValue'])

const authStore = useAuthStore()
const isLogin = ref(true)
const cargando = ref(false)
const error = ref('')

const formData = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: 'pasajero'
})

const closeModal = () => {
  emit('update:modelValue', false)
  resetForm()
}

const resetForm = () => {
  formData.value = {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'pasajero'
  }
  error.value = ''
}

// Validar formulario antes de enviar
const validateForm = () => {
  if (isLogin.value) {
    if (!formData.value.email.trim() || !formData.value.password) {
      error.value = 'Email y contraseÃ±a son requeridos'
      return false
    }
  } else {
    if (!formData.value.name.trim()) {
      error.value = 'El nombre es requerido'
      return false
    }
    if (!formData.value.email.trim()) {
      error.value = 'El email es requerido'
      return false
    }
    if (!formData.value.password) {
      error.value = 'La contraseÃ±a es requerida'
      return false
    }
    if (!formData.value.password_confirmation) {
      error.value = 'Debes confirmar la contraseÃ±a'
      return false
    }
    if (formData.value.password !== formData.value.password_confirmation) {
      error.value = 'Las contraseÃ±as no coinciden'
      return false
    }
    if (formData.value.password.length < 6) {
      error.value = 'La contraseÃ±a debe tener al menos 6 caracteres'
      return false
    }
  }
  return true
}

const handleSubmit = async () => {
  // Validar primero
  if (!validateForm()) {
    return
  }

  cargando.value = true
  error.value = ''

  try {
    if (isLogin.value) {
      const result = await authStore.login({
        email: formData.value.email.trim(),
        password: formData.value.password
      })

      if (result.success) {
        console.log('âœ… Login exitoso')
        closeModal()
        
        // â­ Redirigir DIRECTAMENTE al dashboard
        // Ahora el token estÃ¡ en la cookie, Laravel lo reconocerÃ¡
        setTimeout(() => {
          window.location.href = '/dashboard/home'
        }, 100)
      } else {
        error.value = result.error || 'Error al iniciar sesion'
      }
    } else {
      // Modo registro
      const registerData = {
        name: formData.value.name.trim(),
        email: formData.value.email.trim(),
        password: formData.value.password,
        password_confirmation: formData.value.password_confirmation,
        role: formData.value.role,
        phone: formData.value.phone?.trim() || null
      }
      
      console.log('ðŸ“¤ Enviando registro con datos:', registerData)
      
      const result = await authStore.register(registerData)
      
      console.log('ðŸ“¥ Resultado del registro:', result)

      if (result.success) {
        console.log('âœ… Registro exitoso')
        closeModal()
        
        // â­ Redirigir DIRECTAMENTE al dashboard
        // Ahora el token estÃ¡ en la cookie, Laravel lo reconocerÃ¡
        setTimeout(() => {
          window.location.href = '/dashboard/home'
        }, 100)
      } else {
        error.value = result.error || 'Error al registrarse'
        console.error('âŒ Errores del servidor:', result.errors)
      }
    }
  } catch (e) {
    console.error('âŒ Error en autenticaciÃ³n:', e)
    error.value = 'Error en la autenticacion'
  } finally {
    cargando.value = false
  }
}
</script>

<template>
  <Teleport to="body">
    <div v-if="modelValue" class="fixed inset-0 z-50 overflow-y-auto" @keydown.esc="closeModal">
      <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity" @click="closeModal"></div>

      <div class="flex min-h-full items-center justify-center p-4">
        <div
          class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-8 transform transition-all"
          role="dialog"
          aria-modal="true"
          aria-labelledby="auth-modal-title"
          aria-describedby="auth-modal-desc"
          tabindex="-1"
        >
          <button type="button" @click="closeModal" class="absolute top-4 right-4 text-neutral-slate hover:text-neutral-dark" aria-label="Cerrar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <div class="text-center mb-8">
            <img src="/images/logo.png" alt="LanzaTaxi" class="h-20 mx-auto mb-2" cargando="lazy" decoding="async">
            <h2 id="auth-modal-title" class="text-2xl font-bold text-lanzarote-blue">LanzaTaxi</h2>
            <p id="auth-modal-desc" class="text-neutral-slate text-sm mt-1">
              {{ isLogin ? 'Bienvenido de nuevo' : 'Crea tu cuenta' }}
            </p>
          </div>

          <div v-if="error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm" aria-live="polite">
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
                placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢"
              >
            </div>

            <div v-if="!isLogin">
              <label class="block text-sm font-medium text-neutral-dark mb-1">Confirmar contrasena</label>
              <input
                v-model="formData.password_confirmation"
                type="password"
                required
                class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue focus:border-transparent"
                placeholder="â€¢â€¢â€¢â€¢â€¢â€¢â€¢â€¢"
              >
            </div>

            <div v-if="!isLogin">
              <label class="block text-sm font-medium text-neutral-dark mb-2">Tipo de usuario</label>
              <div class="grid grid-cols-3 gap-2">
                <button
                  type="button"
                  @click="formData.role = 'pasajero'"
                  :class="[
                    'p-2 rounded-lg border text-sm transition-all',
                    formData.role === 'pasajero'
                      ? 'bg-lanzarote-blue text-white border-lanzarote-blue'
                      : 'border-neutral-volcanic text-neutral-dark hover:border-lanzarote-blue'
                  ]"
                >
                  Pasajero
                </button>
                <button
                  type="button"
                  @click="formData.role = 'conductor'"
                  :class="[
                    'p-2 rounded-lg border text-sm transition-all',
                    formData.role === 'conductor'
                      ? 'bg-lanzarote-blue text-white border-lanzarote-blue'
                      : 'border-neutral-volcanic text-neutral-dark hover:border-lanzarote-blue'
                  ]"
                >
                  Taxista
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
                  Admin
                </button>
              </div>
            </div>

            <button
              type="submit"
              :disabled="cargando"
              class="w-full bg-lanzarote-blue text-white py-3 px-4 rounded-lg font-semibold hover:bg-lanzarote-yellow hover:text-black transition-all disabled:opacity-50 disabled:cursor-not-allowed mt-6"
              :aria-busy="cargando ? 'true' : 'false'"
            >
              <span v-if="!cargando">{{ isLogin ? 'Iniciar sesion' : 'Crear cuenta' }}</span>
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

