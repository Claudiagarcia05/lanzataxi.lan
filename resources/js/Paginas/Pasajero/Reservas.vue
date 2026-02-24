<template>
  <DisposicionPasajero>
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-neutral-dark">Mis Reservas</h1>
        <p class="text-neutral-slate mt-1">Historial y seguimiento de tus viajes</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-5">
          <p class="text-3xl font-bold text-neutral-dark">{{ viajeStats.all }}</p>
          <p class="text-sm text-neutral-slate">Total reservas</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
          <p class="text-3xl font-bold text-yellow-600">{{ viajeStats.pendiente + viajeStats.activo }}</p>
          <p class="text-sm text-neutral-slate">Pendientes</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
          <p class="text-3xl font-bold text-green-600">{{ viajeStats.completed }}</p>
          <p class="text-sm text-neutral-slate">Completadas</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
          <p class="text-3xl font-bold text-lanzarote-blue">{{ totalGastado.toFixed(2) }}€</p>
          <p class="text-sm text-neutral-slate">Total gastado</p>
        </div>
      </div>

      <!-- Historial de Viajes -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-lg font-semibold text-neutral-dark">Historial de Viajes</h2>
          <span class="text-sm text-neutral-slate">{{ filteredviajes.length }} reservas</span>
        </div>

        <div class="space-y-4">
          <div
            v-for="viaje in filteredviajes"
            :key="viaje.id"
            class="border border-neutral-volcanic rounded-xl p-5 hover:shadow-md transition-shadow"
          >
            <!-- Header del viaje -->
            <div class="flex flex-col md:flex-row md:items-start justify-between mb-4">
              <div>
                <h3 class="text-lg font-semibold text-neutral-dark">{{ viaje.dropoff_address }}</h3>
                <p class="text-sm text-neutral-slate">→ {{ viaje.pickup_address }}</p>
              </div>
              <div class="flex items-center gap-3 mt-2 md:mt-0">
                <span class="text-sm text-neutral-slate">{{ formatDate(viaje.created_at) }}</span>
                <span class="text-sm font-medium text-neutral-dark">{{ viaje.conductor?.usuario?.name || 'Pendiente' }}</span>
              </div>
            </div>

            <!-- Detalles del viaje -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
              <div>
                <p class="text-xs text-neutral-slate">Precio</p>
                <p class="text-lg font-bold text-lanzarote-blue">{{ viaje.price?.toFixed(2) }}€</p>
              </div>
              <div>
                <p class="text-xs text-neutral-slate">Distancia</p>
                <p class="font-medium text-neutral-dark">{{ viaje.distance?.toFixed(1) }} km</p>
              </div>
              <div>
                <p class="text-xs text-neutral-slate">Pasajeros</p>
                <p class="font-medium text-neutral-dark">{{ viaje.pasajeros || 1 }}</p>
              </div>
              <div>
                <p class="text-xs text-neutral-slate">Estado</p>
                <span :class="['px-2 py-1 rounded-full text-xs font-medium inline-block', getStatusBadge(viaje.estado).class]">
                  {{ getStatusBadge(viaje.estado).label }}
                </span>
              </div>
            </div>

            <!-- Estado de pago -->
            <div v-if="viaje.estado === 'completed'" class="border-t border-neutral-volcanic pt-4 mt-2">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="text-sm text-neutral-slate">Pago:</span>
                  <span v-if="viaje.pago" class="text-green-600 font-medium">Pagado</span>
                  <span v-else class="text-yellow-600 font-medium">Pendiente de pago</span>
                </div>
                <div class="flex gap-2">
                  <button
                    v-if="!viaje.valoracion"
                    @click="openRatingModal(viaje)"
                    class="text-sm text-lanzarote-blue hover:text-lanzarote-yellow"
                  >
                    Valorar viaje
                  </button>
                  <button class="text-sm text-neutral-slate hover:text-lanzarote-blue">
                    Ver detalles
                  </button>
                </div>
              </div>

              <!-- Valoración si existe -->
              <div v-if="viaje.valoracion" class="flex items-center gap-2 mt-2">
                <div class="flex">
                  <span
                    v-for="i in 5"
                    :key="i"
                    :class="i <= viaje.valoracion ? 'text-yellow-400' : 'text-gray-300'"
                  >★</span>
                </div>
                <span v-if="viaje.comment" class="text-sm text-neutral-slate">"{{ viaje.comment }}"</span>
              </div>
            </div>

            <!-- Viaje pendiente/activo -->
            <div v-else-if="viaje.estado === 'pendiente'" class="border-t border-neutral-volcanic pt-4 mt-2">
              <div class="flex justify-end">
                <button
                  @click="cancelTrip(viaje.id)"
                  class="text-sm text-red-600 hover:text-red-800"
                >
                  Cancelar reserva
                </button>
              </div>
            </div>

            <div v-else-if="['accepted', 'in_progress'].includes(viaje.estado)" class="border-t border-neutral-volcanic pt-4 mt-2">
              <div class="flex justify-end">
                <button class="text-sm text-lanzarote-blue hover:text-lanzarote-yellow">
                  Ver seguimiento en tiempo real
                </button>
              </div>
            </div>
          </div>

          <!-- Mensaje si no hay resultados -->
          <div v-if="filteredviajes.length === 0" class="text-center py-12">
            <p class="text-neutral-slate">No hay viajes que mostrar</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de valoración -->
    <div v-if="showRatingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl p-6 max-w-md w-full">
        <h3 class="text-xl font-bold text-neutral-dark mb-4">Valorar viaje</h3>

        <div class="mb-4">
          <p class="text-sm text-neutral-slate">{{ ratingviaje?.pickup_address }} → {{ ratingviaje?.dropoff_address }}</p>
        </div>

        <div class="mb-4">
          <p class="text-sm font-medium text-neutral-dark mb-2">Puntuación</p>
          <div class="flex gap-1">
            <button v-for="i in 5" :key="i" @click="ratingValue = i" class="text-2xl">
              <span :class="i <= ratingValue ? 'text-yellow-400' : 'text-gray-300'">★</span>
            </button>
          </div>
        </div>

        <div class="mb-4">
          <textarea
            v-model="ratingComment"
            rows="3"
            class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue resize-none"
            placeholder="Comentario (opcional)"
          ></textarea>
        </div>

        <div class="flex gap-3">
          <button @click="submitRating" class="flex-1 bg-lanzarote-blue text-white py-2 rounded-lg hover:bg-lanzarote-yellow hover:text-black">
            Enviar
          </button>
          <button @click="showRatingModal = false" class="px-4 border border-neutral-volcanic py-2 rounded-lg hover:bg-neutral-soft">
            Cancelar
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de pago -->
    <ModalPago
      :show="showpagoModal"
      :viaje="pagoviaje"
      @close="showpagoModal = false"
      @success="handlepagoSuccess"
    />
  </DisposicionPasajero>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import DisposicionPasajero from '../../Disposiciones/DisposicionPasajero.vue'
import ModalPago from '../../Componentes/ModalPago.vue'
import { useTripStore } from '../../Almacenes/almacenViaje.js'
import { useWalletStore } from '../../Almacenes/almacenCartera.js'

// const viajeStore = useTripStore()
const viajeStore = useTripStore()
// const walletStore = useWalletStore()
const walletStore = useWalletStore()

// Filtros
const filtroSeleccionado = ref('all')

// Modal de valoración
const showRatingModal = ref(false)
const ratingviaje = ref(null)
const ratingValue = ref(0)
const ratingComment = ref('')

// Modal de pago
const showpagoModal = ref(false)
const pagoviaje = ref(null)

// Calcular total gastado: solo cobros reales (sin doble conteo de deuda arrastrada)
const totalGastado = computed(() => {
  // 1. Sumar todos los viajes completados y cancelados pagados (sin deuda generada)
  let total = 0;
  let deudaPagada = 0;
  // Buscar viajes que pagaron deuda arrastrada
  viajeStore.viajesPasajero.forEach(t => {
    // Si el viaje tiene un campo "debt_paid" (deuda arrastrada pagada en este viaje), la restamos del total
    if (t.estado === 'completed' || (t.estado === 'cancelled' && (!t.debt || t.debt === 0))) {
      total += t.price || 0;
      if (t.debt_paid) deudaPagada += t.debt_paid;
    }
  });
  // Restar la deuda pagada para evitar doble conteo
  return total - deudaPagada;
})

// Filtrar viajes
const filteredviajes = computed(() => {
  let viajes = [...viajeStore.viajesPasajero]

  // Filtrar por estado
  if (filtroSeleccionado.value !== 'all') {
    viajes = viajes.filter(t => t.estado === filtroSeleccionado.value)
  }

  // Ordenar por fecha (más reciente primero)
  return viajes.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

// Estadísticas
const viajeStats = computed(() => {
  const all = viajeStore.viajesPasajero.length
  const completed = viajeStore.viajesPasajero.filter(t => t.estado === 'completed').length
  const pendiente = viajeStore.viajesPasajero.filter(t => t.estado === 'pendiente').length
  const cancelled = viajeStore.viajesPasajero.filter(t => t.estado === 'cancelled').length
  const activo = viajeStore.viajesPasajero.filter(t => ['accepted', 'in_progress'].includes(t.estado)).length

  return { all, completed, pendiente, cancelled, activo }
})

// Funciones
const getStatusBadge = (estado) => {
  const badges = {
    'pendiente': { class: 'bg-yellow-100 text-yellow-800', label: 'Pendiente' },
    'accepted': { class: 'bg-blue-100 text-blue-800', label: 'Aceptado' },
    'in_progress': { class: 'bg-green-100 text-green-800', label: 'En curso' },
    'completed': { class: 'bg-gray-100 text-gray-800', label: 'Completado' },
    'cancelled': { class: 'bg-red-100 text-red-800', label: 'Cancelado' }
  }
  return badges[estado] || badges.pendiente
}

const cancelTrip = async (viajeId) => {
  if (confirm('¿Estás seguro de que deseas cancelar este viaje? Se cobrará el importe completo y, si no hay saldo suficiente en tu cartera, se generará una deuda.')) {
    await viajeStore.cancelTrip(viajeId)
    await walletStore.fetchBalance()
    await walletStore.fetchDebtSummary()
  }
}

const openRatingModal = (viaje) => {
  ratingviaje.value = viaje
  ratingValue.value = viaje.valoracion || 0
  ratingComment.value = viaje.comment || ''
  showRatingModal.value = true
}

const openpagoModal = (viaje) => {
  pagoviaje.value = viaje
  showpagoModal.value = true
}

const handlepagoSuccess = async (pagoData) => {
  console.log('Pago exitoso:', pagoData)
  await viajeStore.fetchTrips()
  alert('Pago procesado correctamente')
}

const submitRating = async () => {
  if (ratingValue.value === 0) {
    alert('Por favor selecciona una valoración')
    return
  }

  await viajeStore.rateTrip(ratingviaje.value.id, ratingValue.value, ratingComment.value)
  showRatingModal.value = false
  ratingviaje.value = null
  ratingValue.value = 0
  ratingComment.value = ''
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).replace(',', '')
}

onMounted(() => {
  viajeStore.fetchTrips()
  walletStore.fetchBalance()
  walletStore.fetchDebtSummary()
})
</script>
