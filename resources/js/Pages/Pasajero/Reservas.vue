<script setup>
import { ref, computed, onMounted } from 'vue'
import PasajeroLayout from '../../Layouts/PasajeroLayout.vue'
import pagoModal from '../../Components/pagoModal.vue'
import { useTripStore } from '../../stores/viajeStore'

const viajeStore = useTripStore()

// Filtros
const filtroSeleccionado = ref('all')
const consultaBusqueda = ref('')

// Modal de valoraciÃƒÂ³n
const showRatingModal = ref(false)
const ratingviaje = ref(null)
const ratingValue = ref(0)
const ratingComment = ref('')

// Modal de pago
const showpagoModal = ref(false)
const pagoviaje = ref(null)

// Filtrar viajes
const filteredviajes = computed(() => {
  let viajes = [...viajeStore.viajesPasajero]
  
  // Filtrar por estado
  if (filtroSeleccionado.value !== 'all') {
    viajes = viajes.filter(t => t.estado === filtroSeleccionado.value)
  }
  
  // Filtrar por bÃƒÂºsqueda
  if (consultaBusqueda.value) {
    const query = consultaBusqueda.value.toLowerCase()
    viajes = viajes.filter(t => 
      t.pickup_address?.toLowerCase().includes(query) ||
      t.dropoff_address?.toLowerCase().includes(query) ||
      t.conductor?.name?.toLowerCase().includes(query)
    )
  }
  
  // Ordenar por fecha (mÃƒÂ¡s reciente primero)
  return viajes.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

// EstadÃƒÂ­sticas
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
    'pendiente': { class: 'bg-yellow-100 text-yellow-800', label: 'Pendiente', icon: 'Ã¢ÂÂ³' },
    'accepted': { class: 'bg-blue-100 text-blue-800', label: 'Aceptado', icon: 'Ã¢Å“â€¦' },
    'in_progress': { class: 'bg-green-100 text-green-800', label: 'En curso', icon: 'Ã°Å¸Å¡â€”' },
    'completed': { class: 'bg-gray-100 text-gray-800', label: 'Completado', icon: 'Ã°Å¸ÂÂ' },
    'cancelled': { class: 'bg-red-100 text-red-800', label: 'Cancelado', icon: 'Ã¢ÂÅ’' }
  }
  return badges[estado] || badges.pendiente
}

const cancelTrip = async (viajeId) => {
  if (confirm('Ã‚Â¿EstÃƒÂ¡s seguro de que deseas cancelar este viaje?')) {
    await viajeStore.cancelTrip(viajeId)
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
    alert('Por favor selecciona una valoraciÃƒÂ³n')
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
  })
}

onMounted(() => {
  viajeStore.fetchTrips()
})
</script>

<template>
  <PasajeroLayout>
    <div class="mb-8">
      <h2 class="text-3xl font-bold text-neutral-dark">Mis Reservas Ã°Å¸â€œâ€¹</h2>
      <p class="text-neutral-slate mt-2">Historial completo de tus viajes</p>
    </div>

    <!-- EstadÃƒÂ­sticas rÃƒÂ¡pidas -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
      <button 
        @click="filtroSeleccionado = 'all'"
        :class="['bg-white rounded-lg p-4 text-center hover:shadow-md transition-shadow', filtroSeleccionado === 'all' ? 'ring-2 ring-lanzarote-blue' : '']"
      >
        <p class="text-2xl font-bold text-neutral-dark">{{ viajeStats.all }}</p>
        <p class="text-xs text-neutral-slate">Todos</p>
      </button>
      <button 
        @click="filtroSeleccionado = 'completed'"
        :class="['bg-white rounded-lg p-4 text-center hover:shadow-md transition-shadow', filtroSeleccionado === 'completed' ? 'ring-2 ring-lanzarote-blue' : '']"
      >
        <p class="text-2xl font-bold text-green-600">{{ viajeStats.completed }}</p>
        <p class="text-xs text-neutral-slate">Completados</p>
      </button>
      <button 
        @click="filtroSeleccionado = 'accepted'"
        :class="['bg-white rounded-lg p-4 text-center hover:shadow-md transition-shadow', filtroSeleccionado === 'accepted' ? 'ring-2 ring-lanzarote-blue' : '']"
      >
        <p class="text-2xl font-bold text-blue-600">{{ viajeStats.activo }}</p>
        <p class="text-xs text-neutral-slate">Activos</p>
      </button>
      <button 
        @click="filtroSeleccionado = 'pendiente'"
        :class="['bg-white rounded-lg p-4 text-center hover:shadow-md transition-shadow', filtroSeleccionado === 'pendiente' ? 'ring-2 ring-lanzarote-blue' : '']"
      >
        <p class="text-2xl font-bold text-yellow-600">{{ viajeStats.pendiente }}</p>
        <p class="text-xs text-neutral-slate">Pendientes</p>
      </button>
      <button 
        @click="filtroSeleccionado = 'cancelled'"
        :class="['bg-white rounded-lg p-4 text-center hover:shadow-md transition-shadow', filtroSeleccionado === 'cancelled' ? 'ring-2 ring-lanzarote-blue' : '']"
      >
        <p class="text-2xl font-bold text-red-600">{{ viajeStats.cancelled }}</p>
        <p class="text-xs text-neutral-slate">Cancelados</p>
      </button>
    </div>

    <!-- Barra de bÃƒÂºsqueda -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
      <div class="flex items-center gap-3">
        <span class="text-2xl">Ã°Å¸â€Â</span>
        <input 
          v-model="consultaBusqueda"
          type="text"
          placeholder="Buscar por origen, destino o conductor..."
          class="flex-1 px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
        />
        <button 
          v-if="consultaBusqueda"
          @click="consultaBusqueda = ''"
          class="px-4 py-2 text-sm text-neutral-slate hover:text-neutral-dark"
        >
          Limpiar
        </button>
      </div>
    </div>

    <!-- Lista de viajes -->
    <div class="space-y-4">
      <div 
        v-for="viaje in filteredviajes" 
        :key="viaje.id"
        class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow"
      >
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
          <!-- Info principal -->
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-3">
              <span class="text-2xl">{{ getStatusBadge(viaje.estado).icon }}</span>
              <div>
                <span :class="['px-3 py-1 rounded-full text-xs font-medium', getStatusBadge(viaje.estado).class]">
                  {{ getStatusBadge(viaje.estado).label }}
                </span>
                <p class="text-xs text-neutral-slate mt-1">{{ formatDate(viaje.created_at) }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div class="flex items-start gap-2">
                <span class="text-green-500 text-lg">Ã°Å¸â€œÂ</span>
                <div class="flex-1">
                  <p class="text-xs text-neutral-slate">Origen</p>
                  <p class="font-medium text-neutral-dark">{{ viaje.pickup_address }}</p>
                </div>
              </div>
              <div class="flex items-start gap-2">
                <span class="text-red-500 text-lg">Ã°Å¸Å½Â¯</span>
                <div class="flex-1">
                  <p class="text-xs text-neutral-slate">Destino</p>
                  <p class="font-medium text-neutral-dark">{{ viaje.dropoff_address }}</p>
                </div>
              </div>
            </div>

            <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-neutral-slate">
              <span v-if="viaje.conductor">Ã°Å¸â€˜Â¤ {{ viaje.conductor.usuario.name }}</span>
              <span v-if="viaje.distance">Ã°Å¸â€œÂ {{ viaje.distance }} km</span>
              <span v-if="viaje.price" class="font-semibold text-lanzarote-blue">Ã°Å¸â€™Â° {{ viaje.price.toFixed(2) }} Ã¢â€šÂ¬</span>
              <span v-if="viaje.pasajeros">Ã°Å¸â€˜Â¥ {{ viaje.pasajeros }} pasajero(s)</span>
              <span v-if="viaje.luggage && viaje.luggage > 0">Ã°Å¸Â§Â³ {{ viaje.luggage }} maleta(s)</span>
            </div>

            <div v-if="viaje.valoracion" class="mt-3 flex items-center gap-2">
              <div class="flex">
                <span v-for="i in 5" :key="i" :class="i <= viaje.valoracion ? 'text-yellow-400' : 'text-gray-300'">Ã¢Ëœâ€¦</span>
              </div>
              <span v-if="viaje.comment" class="text-sm text-neutral-slate italic">"{{ viaje.comment }}"</span>
            </div>
          </div>

          <!-- Acciones -->
          <div class="flex md:flex-col gap-2">
            <button 
              v-if="viaje.estado === 'pendiente'"
              @click="cancelTrip(viaje.id)"
              class="px-4 py-2 text-sm bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors"
            >
              Cancelar
            </button>
            <button 
              v-if="viaje.estado === 'completed' && !viaje.pago"
              @click="openpagoModal(viaje)"
              class="px-4 py-2 text-sm bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors"
            >
              Ã°Å¸â€™Â³ Pagar
            </button>
            <button 
              v-if="viaje.estado === 'completed' && !viaje.valoracion"
              @click="openRatingModal(viaje)"
              class="px-4 py-2 text-sm bg-lanzarote-blue text-white rounded-lg hover:bg-lanzarote-yellow hover:text-black transition-colors"
            >
              Valorar
            </button>
            <button 
              v-if="['accepted', 'in_progress'].includes(viaje.estado)"
              class="px-4 py-2 text-sm bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors"
            >
              Seguimiento
            </button>
            <button class="px-4 py-2 text-sm bg-neutral-soft text-neutral-slate rounded-lg hover:bg-neutral-volcanic transition-colors">
              Detalles
            </button>
          </div>
        </div>
      </div>

      <!-- Mensaje si no hay resultados -->
      <div v-if="filteredviajes.length === 0" class="text-center py-12">
        <span class="text-6xl mb-4 block">Ã°Å¸â€Â</span>
        <p class="text-neutral-slate">No se encontraron viajes con los filtros seleccionados</p>
      </div>
    </div>

    <!-- Modal de valoraciÃƒÂ³n -->
    <div v-if="showRatingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-2xl p-8 max-w-md w-full">
        <h3 class="text-xl font-bold text-neutral-dark mb-4">Valorar viaje</h3>
        
        <div class="mb-6">
          <p class="text-sm text-neutral-slate mb-2">De {{ ratingviaje?.pickup_address }} a {{ ratingviaje?.dropoff_address }}</p>
          <p class="text-xs text-neutral-slate">Conductor: {{ ratingviaje?.conductor?.usuario?.name }}</p>
        </div>

        <div class="mb-6">
          <p class="text-sm font-medium text-neutral-dark mb-3">PuntuaciÃƒÂ³n</p>
          <div class="flex justify-center gap-2">
            <button 
              v-for="i in 5" 
              :key="i"
              @click="ratingValue = i"
              class="text-4xl transition-all hover:scale-110"
              :class="i <= ratingValue ? 'text-yellow-400' : 'text-gray-300'"
            >
              Ã¢Ëœâ€¦
            </button>
          </div>
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-neutral-dark mb-2">Comentario (opcional)</label>
          <textarea 
            v-model="ratingComment"
            rows="3"
            class="w-full px-4 py-3 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue resize-none"
            placeholder="CuÃƒÂ©ntanos tu experiencia..."
          ></textarea>
        </div>

        <div class="flex gap-3">
          <button 
            @click="submitRating"
            class="flex-1 bg-lanzarote-blue text-white font-semibold py-3 rounded-lg hover:bg-lanzarote-yellow hover:text-black transition-colors"
          >
            Enviar valoraciÃƒÂ³n
          </button>
          <button 
            @click="showRatingModal = false"
            class="px-6 border border-neutral-volcanic py-3 rounded-lg hover:bg-neutral-soft transition-colors"
          >
            Cancelar
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de pago -->
    <pagoModal 
      :show="showpagoModal"
      :viaje="pagoviaje"
      @close="showpagoModal = false"
      @success="handlepagoSuccess"
    />
  </PasajeroLayout>
</template>


