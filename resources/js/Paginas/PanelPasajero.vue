<template>
  <div class="min-h-screen bg-neutral-soft">
    <header class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-lanzarote-blue">LanzaTaxi 🚕</h1>
        <div class="flex items-center gap-4">
          <span class="text-neutral-slate">Hola, {{ usuario?.name }}</span>
          <button @click="logout" class="text-sm text-neutral-slate hover:text-lanzarote-blue">
            Cerrar sesion
          </button>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
      <div class="grid md:grid-cols-3 gap-8">
        <div class="md:col-span-1">
          <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-neutral-dark mb-6">Reservar taxi</h2>

            <form @submit.prevent="bookTaxi" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-neutral-slate mb-1">
                  Donde te recogemos?
                </label>
                <div class="relative">
                  <input
                    v-model="booking.pickup"
                    type="text"
                    placeholder="Tu ubicacion actual"
                    class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue focus:border-transparent"
                  >
                  <button
                    type="button"
                    @click="useCurrentubicacion"
                    class="absolute right-2 top-2 text-neutral-slate hover:text-lanzarote-blue"
                    aria-label="Use current ubicacion"
                  >
                    📍
                  </button>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-neutral-slate mb-1">
                  A donde vas?
                </label>
                <input
                  v-model="booking.dropoff"
                  type="text"
                  placeholder="Direccion de destino"
                  class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue focus:border-transparent"
                >
              </div>

              <div class="bg-lanzarote-blue/5 p-4 rounded-lg">
                <p class="text-sm text-neutral-slate">Taxis disponibles</p>
                <p class="text-2xl font-bold text-lanzarote-blue">{{ availableTaxis }} 🚖</p>
              </div>

              <button
                type="submit"
                :disabled="!booking.dropoff || cargando"
                class="w-full bg-lanzarote-blue text-white py-3 px-4 rounded-lg font-semibold hover:bg-lanzarote-yellow hover:text-black transition-all disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span v-if="!cargando">Reservar taxi</span>
                <span v-else class="flex items-center justify-center">
                  <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                  </svg>
                  Buscando taxi...
                </span>
              </button>
            </form>

            <div v-if="lastviaje" class="mt-6 p-4 bg-success-jable/10 rounded-lg border border-success-jable/20">
              <h3 class="font-semibold text-success-jable mb-2">Impacto ambiental</h3>
              <p class="text-sm text-neutral-dark">
                En tu ultimo viaje ahorraste
                <span class="font-bold text-success-jable">{{ lastviaje.co2_saved }} kg de CO2</span>
              </p>
              <p class="text-xs text-neutral-slate mt-1">
                Equivalente a {{ (lastviaje.co2_saved * 0.4).toFixed(1) }} arboles plantados
              </p>
            </div>
          </div>
        </div>

        <div class="md:col-span-2">
          <TaxiMap :activo-viaje="viajeActivo" :conductor-ubicacion="ubicacionConductor" :markers="mapMarkers" />

          <div class="mt-6 bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold text-neutral-dark mb-4">Tus viajes</h3>
            <div class="space-y-3">
              <div
                v-for="viaje in recentviajes"
                :key="viaje.id"
                class="flex justify-between items-center p-3 hover:bg-neutral-soft rounded-lg transition-colors"
              >
                <div>
                  <p class="font-medium text-neutral-dark">{{ viaje.dropoff_address }}</p>
                  <p class="text-sm text-neutral-slate">{{ viaje.date }}</p>
                </div>
                <div class="text-right">
                  <p class="font-semibold text-lanzarote-blue">{{ viaje.price }}€</p>
                  <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">
                    {{ viaje.estado }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../Almacenes/almacenAutenticacion.js'
import { useTripStore } from '../Almacenes/almacenViaje.js'
import MapaTaxi from '../Componentes/MapaTaxi.vue'

const auth = useAuthStore()
const viajeStore = useTripStore()
const router = useRouter()

const usuario = computed(() => auth.usuario)
const cargando = ref(false)
const availableTaxis = ref(12)
const viajeActivo = computed(() => viajeStore.viajeActivo)
const ubicacionConductor = computed(() => viajeStore.ubicacionConductor)
const lastviaje = computed(() => viajeStore.viajes?.[0] ?? null)
const recentviajes = computed(() => viajeStore.viajes ?? [])

const booking = ref({
  pickup: '',
  dropoff: ''
})

const mapMarkers = ref([])
let trackingTimer

const bookTaxi = async () => {
  cargando.value = true
  try {
    await viajeStore.createTrip({
      pickup_lat: 28.963,
      pickup_lng: -13.55,
      dropoff_lat: 28.978,
      dropoff_lng: -13.561
    })
    startTracking()
  } finally {
    cargando.value = false
  }
}

const startTracking = () => {
  if (!viajeActivo.value) return

  clearInterval(trackingTimer)
  trackingTimer = setInterval(() => {
    viajeStore.fetchconductorubicacion(viajeActivo.value.id)
  }, 5000)
}

const useCurrentubicacion = () => {
  if (!navigator.geolocation) return

  navigator.geolocation.getCurrentPosition((position) => {
    booking.value.pickup = `${position.coords.latitude.toFixed(5)}, ${position.coords.longitude.toFixed(5)}`
  })
}

const logout = () => {
  auth.logout()
  router.push('/login')
}

onBeforeUnmount(() => {
  clearInterval(trackingTimer)
})
</script>


