<template>
  <DisposicionConductor>
    <div class="max-w-3xl mx-auto">
      <div class="bg-white rounded-xl shadow-sm p-8">
        <h1 class="text-2xl font-bold text-neutral-dark mb-6">Mi Perfil</h1>

        <!-- Avatar con carga de imagen -->
        <div class="flex justify-center mb-8">
          <div class="relative">
            <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-lanzarote-blue/20">
              <img 
                v-if="avatarPreview || userAvatar" 
                :src="avatarPreview || userAvatar" 
                :alt="authStore.usuario?.name"
                class="w-full h-full object-cover"
                @error="handleImageError"
                key="avatar-image"
              >
              <div v-else class="w-full h-full bg-lanzarote-blue text-white flex items-center justify-center text-4xl font-bold">
                {{ authStore.usuario?.name?.charAt(0) || 'U' }}
              </div>
            </div>
            <label class="absolute bottom-0 right-0 bg-lanzarote-blue rounded-full p-2 shadow-lg cursor-pointer hover:bg-lanzarote-yellow transition-colors">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <input type="file" class="hidden" accept="image/*" @change="handleAvatarUpload">
            </label>
          </div>
        </div>

        <!-- Información Personal (Solo lectura con botón editar) -->
        <div class="border-b border-neutral-volcanic pb-6 mb-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-neutral-dark">Información personal</h3>
            <button 
              v-if="!editingPersonal"
              @click="startEditingPersonal"
              class="text-sm text-lanzarote-blue flex items-center space-x-1"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
              </svg>
              <span>Editar información</span>
            </button>
          </div>
          
          <div v-if="!editingPersonal" class="space-y-3">
            <div class="flex">
              <span class="w-32 text-sm text-neutral-slate">Nombre:</span>
              <span class="text-neutral-dark font-medium">{{ authStore.usuario?.name || 'Sin nombre' }}</span>
            </div>
            <div class="flex">
              <span class="w-32 text-sm text-neutral-slate">Email:</span>
              <span class="text-neutral-dark">{{ authStore.usuario?.email || 'Sin email' }}</span>
            </div>
            <div class="flex">
              <span class="w-32 text-sm text-neutral-slate">Teléfono:</span>
              <span class="text-neutral-dark">{{ conductorStore.perfil?.phone || conductorStore.perfil?.user?.phone || authStore.usuario?.phone || 'No especificado' }}</span>
            </div>
              <div class="mt-6 flex">
                <button 
                  @click="toggleActividadLaboral"
                  :class="['px-6 py-2 rounded-lg font-semibold transition-colors', isActive ? 'bg-green-500 text-white hover:bg-green-600' : 'bg-red-500 text-white hover:bg-red-600']"
                >
                  {{ isActive ? 'Activado' : 'Desconectado' }}
                </button>
              </div>
          </div>

          <!-- Formulario edición información personal -->
          <div v-if="editingPersonal" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-neutral-dark mb-2">Nombre</label>
              <input 
                :value="conductorStore.perfil?.name || authStore.usuario?.name || ''" 
                type="text" 
                disabled
                class="w-full px-4 py-2 bg-neutral-soft border border-neutral-volcanic rounded-lg cursor-not-allowed"
              >
              <p class="text-xs text-neutral-slate mt-1">El nombre no se puede modificar</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-neutral-dark mb-2">Email</label>
              <input 
                v-model="form.email" 
                type="email" 
                class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                :class="{ 'border-red-500': emailError }"
              >
              <p v-if="emailError" class="text-xs text-red-500 mt-1">{{ emailError }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-neutral-dark mb-2">Teléfono</label>
              <input 
                v-model="form.phone" 
                type="tel" 
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="9"
                class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                placeholder="Ej: 628123456"
                :class="{ 'border-red-500': phoneError }"
                @input="form.phone = form.phone.replace(/\D/g, '').slice(0, 9)"
              >
              <p v-if="phoneError" class="text-xs text-red-500 mt-1">{{ phoneError }}</p>
            </div>
            <div class="flex space-x-3 pt-2">
              <button 
                @click="savePersonalInfo"
                class="px-4 py-2 bg-lanzarote-blue text-white rounded-lg hover:bg-lanzarote-yellow hover:text-black"
              >
                Guardar cambios
              </button>
              <button 
                @click="cancelEditingPersonal"
                class="px-4 py-2 border border-neutral-volcanic rounded-lg hover:bg-neutral-soft"
              >
                Cancelar
              </button>
            </div>
          </div>
        </div>

        <!-- Cambiar contraseña con validación fuerte -->
        <div class="border-b border-neutral-volcanic pb-6 mb-6">
          <h3 class="font-semibold text-neutral-dark mb-4">Cambiar contraseña</h3>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-neutral-dark mb-2">Nueva contraseña</label>
              <input 
                v-model="password.new" 
                type="password" 
                class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                :class="{ 'border-red-500': password.new && !isPasswordStrong }"
                @input="checkPasswordStrength"
              >
              
              <!-- Indicador de fortaleza -->
              <div v-if="password.new" class="mt-2">
                <div class="flex space-x-1 h-1 mb-2">
                  <div class="flex-1 h-full rounded" :class="strengthColor(1)"></div>
                  <div class="flex-1 h-full rounded" :class="strengthColor(2)"></div>
                  <div class="flex-1 h-full rounded" :class="strengthColor(3)"></div>
                  <div class="flex-1 h-full rounded" :class="strengthColor(4)"></div>
                </div>
                <p class="text-xs" :class="strengthTextColor">{{ strengthMessage }}</p>
              </div>

              <!-- Requisitos de contraseña -->
              <div class="mt-2 space-y-1">
                <p class="text-xs" :class="password.new?.length >= 8 ? 'text-success-jable' : 'text-neutral-slate'">
                  ✓ Mínimo 8 caracteres
                </p>
                <p class="text-xs" :class="/[A-Z]/.test(password.new) ? 'text-success-jable' : 'text-neutral-slate'">
                  ✓ Al menos una mayúscula
                </p>
                <p class="text-xs" :class="/[a-z]/.test(password.new) ? 'text-success-jable' : 'text-neutral-slate'">
                  ✓ Al menos una minúscula
                </p>
                <p class="text-xs" :class="/[0-9]/.test(password.new) ? 'text-success-jable' : 'text-neutral-slate'">
                  ✓ Al menos un número
                </p>
                <p class="text-xs" :class="/[!@#$%^&*]/.test(password.new) ? 'text-success-jable' : 'text-neutral-slate'">
                  ✓ Al menos un carácter especial (!@#$%^&*)
                </p>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-neutral-dark mb-2">Confirmar nueva contraseña</label>
              <input 
                v-model="password.confirm" 
                type="password" 
                class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                :class="{ 'border-red-500': password.confirm && password.new !== password.confirm }"
              >
              <p v-if="password.confirm && password.new !== password.confirm" class="text-xs text-red-500 mt-1">
                Las contraseñas no coinciden
              </p>
            </div>

            <button 
              @click="changePassword"
              :disabled="!canChangePassword"
              class="px-4 py-2 bg-lanzarote-blue text-white rounded-lg hover:bg-lanzarote-yellow hover:text-black disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Actualizar contraseña
            </button>
          </div>
        </div>

        <!-- Preferencias y notificaciones -->
        <div class="border-b border-neutral-volcanic pb-6 mb-6">
          <h3 class="font-semibold text-neutral-dark mb-4">Preferencias y notificaciones</h3>
          <div class="space-y-3">
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="preferences.email_notifications" class="w-4 h-4 text-lanzarote-blue">
              <span class="text-neutral-dark">Recibir notificaciones por email</span>
            </label>
            <label class="flex items-center space-x-3">
              <input type="checkbox" v-model="preferences.sms_notifications" class="w-4 h-4 text-lanzarote-blue">
              <span class="text-neutral-dark">Recibir notificaciones por SMS</span>
            </label>
          </div>
        </div>
      </div>
    </div>
  </DisposicionConductor>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import DisposicionConductor from '../../Disposiciones/DisposicionConductor.vue'
import { useConductorStore } from '../../Almacenes/almacenConductor.js'
import { useAuthStore } from '../../Almacenes/almacenAutenticacion.js'
const conductorStore = useConductorStore()
const authStore = useAuthStore()

const avatarPreview = ref(null)
const userAvatar = ref(null)
const showDeleteConfirm = ref(false)
const editingPersonal = ref(false)
const emailError = ref('')
const phoneError = ref('')
const form = reactive({
  email: '',
  phone: ''
})
const password = reactive({ new: '', confirm: '' })
const passwordStrength = ref(0)
const preferences = reactive({
  email_notifications: true,
  sms_notifications: false,
  marketing_emails: false
})
const isActive = computed(() => conductorStore.estaEnLinea)

// Computed para validaciones de tarjeta
const isValidCardNumber = computed(() => {
  const numbers = card.number.replace(/\s/g, '')
  return /^\d{16}$/.test(numbers)
})

const isValidExpiry = computed(() => {
  return /^(0[1-9]|1[0-2])\/([0-9]{2})$/.test(card.expiry)
})

const isValidCVV = computed(() => {
  return /^\d{3}$/.test(card.cvv)
})

const isValidCardName = computed(() => {
  return /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(card.name)
})

const canAddToWallet = computed(() => {
  const amount = customAmount.value ? parseFloat(customAmount.value) : walletTopUp.value
  if (!amount || amount < 5) return false
  
  if (!showpagoForm.value) return true
  
  return isValidCardNumber.value && isValidExpiry.value && isValidCVV.value && isValidCardName.value
})

const canWithdraw = computed(() => {
  const amount = parseFloat(withdrawAmount.value)
  return amount >= 5 && amount <= walletStore.balance
})

// Validaciones contraseña
const isPasswordStrong = computed(() => {
  return password.new?.length >= 8 &&
         /[A-Z]/.test(password.new) &&
         /[a-z]/.test(password.new) &&
         /[0-9]/.test(password.new) &&
         /[!@#$%^&*]/.test(password.new)
})

const canChangePassword = computed(() => {
  return isPasswordStrong.value &&
         password.new === password.confirm
})

// Wallet data
const walletBalance = computed(() => walletStore.balance)
const recentTransactions = computed(() => walletStore.transactions.slice(0, 5))

// Funciones de utilidad
const formatCurrency = (value) => {
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
}

const handleImageError = (event) => {
  userAvatar.value = null
  avatarPreview.value = null
}

const handleAvatarUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  const reader = new FileReader()
  reader.onload = (e) => {
    avatarPreview.value = e.target.result
  }
  reader.readAsDataURL(file)
  // Subir imagen al backend
  const formData = new FormData()
  formData.append('avatar', file)
  try {
    const response = await axios.post('/api/user/avatar', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    if (response.data.avatar) {
      userAvatar.value = `/storage/${response.data.avatar}`
      if (conductorStore.perfil) {
        conductorStore.perfil.avatar = response.data.avatar
      }
    }
  } catch (error) {
    alert('Error al subir la imagen: ' + (error?.response?.data?.message || error?.message || JSON.stringify(error)))
  }
}
const toggleActividadLaboral = async () => {
  // Cambia el estado laboral del conductor
  try {
    await conductorStore.establecerEstadoEnLinea(!isActive.value)
  } catch (error) {
    alert('Error al cambiar el estado laboral: ' + (error?.response?.data?.message || error?.message || JSON.stringify(error)))
  }
}

// Información personal
const startEditingPersonal = () => {
  form.email = conductorStore.perfil?.email || ''
  form.phone = conductorStore.perfil?.phone || ''
  editingPersonal.value = true
}

const cancelEditingPersonal = () => {
  editingPersonal.value = false
  emailError.value = ''
  phoneError.value = ''
}

const validateEmail = (email) => {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return re.test(email)
}

const validatePhone = (phone) => {
  const re = /^[0-9]{9}$/
  return re.test(phone)
}

const savePersonalInfo = async () => {
  emailError.value = ''
  phoneError.value = ''
  if (!validateEmail(form.email)) {
    emailError.value = 'Email no válido'
    return
  }
  if (form.phone && !validatePhone(form.phone)) {
    phoneError.value = 'Teléfono debe tener 9 dígitos'
    return
  }
  try {
    const response = await axios.put('/api/user/profile', {
      email: form.email,
      phone: form.phone
    })
    if (conductorStore.perfil) {
      conductorStore.perfil.email = form.email
      conductorStore.perfil.phone = form.phone
    }
    if (authStore.usuario) {
      authStore.usuario.email = form.email
      authStore.usuario.phone = form.phone
    }
    editingPersonal.value = false
    alert('Información actualizada correctamente')
  } catch (error) {
    alert('Error al actualizar la información: ' + (error?.response?.data?.message || error?.message || JSON.stringify(error)))
  }
}

// Contraseña
const checkPasswordStrength = () => {
  let strength = 0
  if (password.new?.length >= 8) strength++
  if (/[A-Z]/.test(password.new)) strength++
  if (/[a-z]/.test(password.new)) strength++
  if (/[0-9]/.test(password.new)) strength++
  if (/[!@#$%^&*]/.test(password.new)) strength++
  passwordStrength.value = strength
}

const strengthColor = (level) => {
  if (!password.new) return 'bg-neutral-volcanic'
  if (passwordStrength.value >= level) {
    if (passwordStrength.value <= 2) return 'bg-red-500'
    if (passwordStrength.value <= 3) return 'bg-yellow-500'
    return 'bg-success-jable'
  }
  return 'bg-neutral-volcanic'
}

const strengthMessage = computed(() => {
  if (!password.new) return ''
  if (passwordStrength.value <= 2) return 'Contraseña débil'
  if (passwordStrength.value <= 3) return 'Contraseña media'
  return 'Contraseña fuerte'
})

const strengthTextColor = computed(() => {
  if (!password.new) return ''
  if (passwordStrength.value <= 2) return 'text-red-500'
  if (passwordStrength.value <= 3) return 'text-yellow-600'
  return 'text-success-jable'
})

const changePassword = async () => {
  if (!canChangePassword.value) return
  try {
    await axios.put('/api/user/password', {
      new_password: password.new,
      new_password_confirmation: password.confirm
    })
    password.new = ''
    password.confirm = ''
    alert('Contraseña actualizada correctamente')
  } catch (error) {
    alert('Error al actualizar la contraseña: ' + (error?.response?.data?.message || error?.message || JSON.stringify(error)))
  }
}

// Validaciones tarjeta
const formatCardNumber = (e) => {
  let value = e.target.value.replace(/\D/g, '')
  value = value.substring(0, 16)
  
  // Formatear con espacios cada 4 dígitos
  const parts = []
  for (let i = 0; i < value.length; i += 4) {
    parts.push(value.substring(i, i + 4))
  }
  card.number = parts.join(' ')
}

const formatExpiry = (e) => {
  let value = e.target.value.replace(/\D/g, '')
  value = value.substring(0, 4)
  
  if (value.length >= 2) {
    card.expiry = value.substring(0, 2) + '/' + value.substring(2)
  } else {
    card.expiry = value
  }
}

const validateCVV = (e) => {
  card.cvv = e.target.value.replace(/\D/g, '').substring(0, 3)
}

const validateCardName = (e) => {
  card.name = e.target.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')
}

// Wallet functions
const processAddToWallet = async () => {
  const amount = customAmount.value ? parseFloat(customAmount.value) : walletTopUp.value
  
  if (amount < 5) {
    alert('El mínimo para añadir es 5€')
    return
  }

  if (!showpagoForm.value) {
    showpagoForm.value = true
    return
  }

  const result = await walletStore.addFunds(amount)
  
  if (result.success) {
    alert(`Se han añadido ${formatCurrency(amount)} a tu cartera`)
    showpagoForm.value = false
    card.number = ''
    card.expiry = ''
    card.cvv = ''
    card.name = ''
    customAmount.value = ''
    walletTopUp.value = 10
  } else {
    alert('Error al procesar el pago')
  }
}

const processWithdraw = async () => {
  const amount = parseFloat(withdrawAmount.value)
  
  if (amount < 5) {
    alert('El mínimo para retirar es 5€')
    return
  }

  if (amount > walletStore.balance) {
    alert('No tienes suficiente saldo')
    return
  }

  if (confirm(`¿Confirmas la retirada de ${formatCurrency(amount)}?`)) {
    const result = await walletStore.withdrawFunds(amount)
    if (result.success) {
      alert('Solicitud de retirada procesada. El dinero se transferirá a tu cuenta en 2-3 días hábiles.')
      withdrawAmount.value = ''
    }
  }
}

const deleteAccount = async () => {
  try {
    await axios.delete('/api/conductor')
    window.location.href = '/'
  } catch (error) {
    alert('Error al eliminar la cuenta')
  }
}

onMounted(async () => {
  if (!conductorStore.perfil) {
    await conductorStore.obtenerPerfilConductor()
  }
  // Cargar avatar si existe (preferir user.avatar si está disponible)
  let avatar = conductorStore.perfil?.user?.avatar || conductorStore.perfil?.avatar || authStore.usuario?.avatar
  if (avatar) {
    userAvatar.value = avatar.startsWith('/storage') || avatar.startsWith('http') 
      ? avatar 
      : `/storage/${avatar}`
  }
  // Cargar preferencias si existen
  if (conductorStore.perfil?.preferences) {
    Object.assign(preferences, conductorStore.perfil.preferences)
  }
})
</script>