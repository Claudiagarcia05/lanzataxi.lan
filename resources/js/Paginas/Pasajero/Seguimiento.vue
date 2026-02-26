<template>
  <DisposicionPasajero>
    <div class="max-w-7xl mx-auto">
      <!-- Cabecera -->
      <div class="bg-gradient-to-r from-lanzarote-blue to-blue-800 rounded-2xl p-8 mb-8 text-white">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold mb-2">Seguimiento del Viaje</h1>
            <p class="text-blue-100">Sigue tu taxi en tiempo real por las carreteras de Lanzarote</p>
          </div>
          <span class="bg-white/20 px-4 py-2 rounded-full text-sm">
            {{ getEstadoText(viaje?.estado) }}
          </span>
        </div>
      </div>

      <!-- Mapa de seguimiento -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Mapa principal -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="h-[500px] rounded-lg overflow-hidden">
              <MapaSeguimiento 
                ref="mapRef"
                :pickupLat="viaje?.pickup_lat"
                :pickupLng="viaje?.pickup_lng"
                :dropoffLat="viaje?.dropoff_lat"
                :dropoffLng="viaje?.dropoff_lng"
                :taxiLat="taxiLocation?.lat"
                :taxiLng="taxiLocation?.lng"
                :estado="viaje?.estado"
                @taxi-aceptado="handleTaxiAceptado"
              />
            </div>
          </div>
        </div>

        <!-- Panel de información -->
        <div class="lg:col-span-1 space-y-4">
          <!-- Estado del viaje -->
          <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-neutral-dark mb-4 flex items-center gap-2">
              <span class="text-xl">🚕</span>
              Estado del viaje
            </h3>
            
            <div class="space-y-4">
              <!-- Timeline de estados -->
              <div class="relative">
                <div class="absolute left-2 top-2 bottom-2 w-0.5 bg-neutral-volcanic"></div>
                
                <div class="relative pl-8 pb-4">
                  <div class="absolute left-0 w-4 h-4 rounded-full" 
                       :class="viaje?.estado === 'pendiente' ? 'bg-yellow-500 ring-4 ring-yellow-100' : 'bg-green-500'">
                  </div>
                  <p class="text-sm font-medium">Solicitud enviada</p>
                  <p class="text-xs text-neutral-slate">{{ formatDate(viaje?.created_at) }}</p>
                </div>

                <div class="relative pl-8 pb-4">
                  <div class="absolute left-0 w-4 h-4 rounded-full" 
                       :class="viaje?.estado === 'accepted' || viaje?.estado === 'in_progress' || viaje?.estado === 'completed' ? 'bg-green-500' : 'bg-neutral-volcanic'">
                  </div>
                  <p class="text-sm font-medium">Taxista asignado</p>
                  <p v-if="viaje?.taxista" class="text-xs text-neutral-slate">
                    {{ viaje.taxista.name }} - {{ viaje.taxista.vehicle_info }}
                  </p>
                </div>

                <div class="relative pl-8 pb-4">
                  <div class="absolute left-0 w-4 h-4 rounded-full" 
                       :class="viaje?.estado === 'in_progress' || viaje?.estado === 'completed' ? 'bg-green-500' : 'bg-neutral-volcanic'">
                  </div>
                  <p class="text-sm font-medium">Viaje en curso</p>
                  <p v-if="viaje?.estado === 'in_progress'" class="text-xs text-neutral-slate">
                    {{ taxiLocation ? 'Taxi en movimiento' : 'Esperando al taxi' }}
                  </p>
                </div>

                <div class="relative pl-8">
                  <div class="absolute left-0 w-4 h-4 rounded-full" 
                       :class="viaje?.estado === 'completed' ? 'bg-green-500' : 'bg-neutral-volcanic'">
                  </div>
                  <p class="text-sm font-medium">Viaje completado</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Información del viaje -->
          <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-neutral-dark mb-4">Detalles del viaje</h3>
            
            <div class="space-y-3">
              <div>
                <p class="text-xs text-neutral-slate">Origen</p>
                <p class="font-medium">{{ viaje?.pickup_address }}</p>
              </div>
              
              <div>
                <p class="text-xs text-neutral-slate">Destino</p>
                <p class="font-medium">{{ viaje?.dropoff_address }}</p>
              </div>

              <div class="grid grid-cols-2 gap-4 pt-2 border-t border-neutral-volcanic">
                <div>
                  <p class="text-xs text-neutral-slate">Distancia</p>
                  <p class="font-semibold text-lanzarote-blue">{{ viaje?.distance || 0 }} km</p>
                </div>
                <div>
                  <p class="text-xs text-neutral-slate">Precio</p>
                  <p class="font-semibold text-lanzarote-blue">{{ viaje?.price || 0 }} €</p>
                </div>
              </div>

              <!-- Botones de acción según estado -->
              <div v-if="viaje?.estado === 'pendiente'" class="mt-4">
                <button 
                  @click="cancelarViaje"
                  class="w-full bg-red-500 text-white py-3 rounded-lg hover:bg-red-600 transition-colors"
                >
                  Cancelar solicitud
                </button>
                <p class="text-xs text-center text-neutral-slate mt-2">
                  Buscando taxista disponible...
                </p>
              </div>

              <div v-if="viaje?.estado === 'completed'" class="mt-4">
                <button 
                  @click="irAValorar"
                  class="w-full bg-lanzarote-yellow text-black py-3 rounded-lg hover:bg-yellow-400 transition-colors"
                >
                  Valorar viaje
                </button>
              </div>
            </div>
          </div>

          <!-- Chat con taxista (cuando está asignado) -->
          <div v-if="viaje?.taxista" class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-neutral-dark mb-4">Contacto con taxista</h3>
            
            <div class="space-y-3">
              <button class="w-full flex items-center justify-center gap-2 bg-blue-50 text-lanzarote-blue py-3 rounded-lg hover:bg-blue-100 transition-colors">
                <span>📞</span>
                Llamar al taxista
              </button>
              <button class="w-full flex items-center justify-center gap-2 bg-green-50 text-green-600 py-3 rounded-lg hover:bg-green-100 transition-colors">
                <span>💬</span>
                Enviar mensaje
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DisposicionPasajero>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import DisposicionPasajero from '../../Disposiciones/DisposicionPasajero.vue'
import MapaSeguimiento from '../../Componentes/MapaSeguimiento.vue'
import { useTripStore } from '../../Almacenes/almacenViaje.js'
import axios from 'axios'
import '../../../css/seguimiento.css'

const route = useRoute()
const router = useRouter()
const viajeStore = useTripStore()
const mapRef = ref(null)

// Estado
const viaje = ref(null)
const taxiLocation = ref(null)
let intervalId = null

// Cargar datos del viaje
const loadViaje = async () => {
  const viajeId = route.params.id
  if (!viajeId) {
    router.push('/pasajero/reservas')
    return
  }

  // Buscar el viaje en el store
  viaje.value = viajeStore.viajesPasajero.find(t => t.id === parseInt(viajeId))
  
  if (!viaje.value) {
    // Si no está en el store, cargarlo de la API
    try {
      const response = await axios.get(`/api/trips/${viajeId}`)
      viaje.value = response.data
    } catch (error) {
      console.error('Error cargando viaje:', error)
      router.push('/pasajero/reservas')
    }
  }
}

// Obtener texto del estado
const getEstadoText = (estado) => {
  const estados = {
    'pendiente': 'Buscando taxista',
    'accepted': 'Taxista en camino',
    'in_progress': 'Viaje en curso',
    'completed': 'Viaje completado',
    'cancelled': 'Cancelado'
  }
  return estados[estado] || estado
}

// Formatear fecha
const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleString('es-ES', {
    hour: '2-digit',
    minute: '2-digit',
    day: '2-digit',
    month: '2-digit'
  })
}

// Simular ubicación del taxi (para desarrollo)
const simularUbicacionTaxi = () => {
  if (!viaje.value || viaje.value.estado !== 'accepted') return

  // Si no hay ubicación del taxi, empezar desde el origen
  if (!taxiLocation.value) {
    taxiLocation.value = {
      lat: viaje.value.pickup_lat - 0.01, // Un poco antes del origen
      lng: viaje.value.pickup_lng
    }
  } else {
    // Mover el taxi hacia el origen
    const latDiff = viaje.value.pickup_lat - taxiLocation.value.lat
    const lngDiff = viaje.value.pickup_lng - taxiLocation.value.lng
    
    taxiLocation.value.lat += latDiff * 0.02
    taxiLocation.value.lng += lngDiff * 0.02

    // Si está muy cerca del origen, cambiar estado a in_progress
    if (Math.abs(latDiff) < 0.001 && Math.abs(lngDiff) < 0.001) {
      viaje.value.estado = 'in_progress'
    }
  }
}

// Manejar cuando el taxi acepta el viaje
const handleTaxiAceptado = (location) => {
  taxiLocation.value = location
  viaje.value.estado = 'accepted'
}

// Cancelar viaje
const cancelarViaje = async () => {
  if (!confirm('¿Estás seguro de que quieres cancelar este viaje?')) return
  
  try {
    await axios.post(`/api/trips/${viaje.value.id}/cancel`)
    viaje.value.estado = 'cancelled'
    router.push('/pasajero/reservas')
  } catch (error) {
    console.error('Error cancelando viaje:', error)
  }
}

// Ir a valorar
const irAValorar = () => {
  router.push(`/dashboard/valorar/${viaje.value.id}`)
}

// Lifecycle
onMounted(() => {
  loadViaje()

  // Simular actualizaciones en tiempo real (solo para desarrollo)
  // En producción, esto sería con WebSockets
  intervalId = setInterval(() => {
    simularUbicacionTaxi()
  }, 3000)
})

onUnmounted(() => {
  if (intervalId) {
    clearInterval(intervalId)
  }
})
</script>