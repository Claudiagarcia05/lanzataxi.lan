<template>
  <DisposicionPasajero>
    <div class="max-w-7xl mx-auto">
      <!-- Hero section como en la imagen -->
      <div class="bg-gradient-to-r from-lanzarote-blue to-blue-800 rounded-2xl p-8 mb-8 text-white">
        <h1 class="text-3xl font-bold mb-2">Nueva Reserva</h1>
        <p class="text-blue-100">Reserva tu taxi en Lanzarote de forma rápida y segura</p>
      </div>

      <!-- Alerta de viaje activo (más discreta) -->
      <div v-if="viajeActivo" class="mb-6 bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-yellow-800">
              Tienes un viaje en curso: {{ viajeActivo.pickup_address }} → {{ viajeActivo.dropoff_address }}
            </p>
            <p class="text-xs text-yellow-700 mt-1">Estado: {{ getEstadoText(viajeActivo.estado) }}</p>
          </div>
          <button 
            @click="irASeguimiento(viajeActivo.id)"
            class="text-sm bg-lanzarote-blue text-white px-4 py-2 rounded-lg hover:bg-lanzarote-yellow hover:text-black transition-colors"
          >
            Ver seguimiento
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulario principal -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-xl shadow-sm p-6">
            <!-- Título LanzaTaxi como en la imagen -->
            <div class="flex items-center space-x-2 mb-6">
              <span class="text-2xl">🚕</span>
              <h2 class="text-xl font-bold text-neutral-dark">LanzaTaxi</h2>
              <span class="text-neutral-slate">¿A dónde vamos?</span>
            </div>
            <!-- Mensaje de error superior -->
            <div v-if="errorMsg" class="mb-6 bg-red-50 border border-red-200 p-4 rounded-lg">
              <p class="text-sm font-medium text-red-700">{{ errorMsg }}</p>
            </div>

            <form @submit.prevent="submitBooking" class="space-y-6">
              <!-- Detalles del viaje -->
              <div class="border-b border-neutral-volcanic pb-6">
                <h3 class="font-semibold text-neutral-dark mb-4">Detalles del viaje</h3>
                
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-neutral-dark mb-1">
                      Origen - Dirección de recogida <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                      <input 
                        v-model="bookingForm.pickupAddress" 
                        type="text" 
                        required
                        class="flex-1 px-4 py-3 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                        placeholder="Ej: Calle Real 45, Arrecife"
                      />
                      <button type="button"
                        class="ml-1 px-3 py-2 rounded-lg bg-lanzarote-blue text-white hover:bg-lanzarote-yellow hover:text-black transition-colors text-xs"
                        title="Usar mi ubicación actual"
                        @click="obtenerUbicacionUsuario"
                      >
                        Mi ubicación
                      </button>
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-neutral-dark mb-1">
                      Destino - ¿A dónde vas? <span class="text-red-500">*</span>
                    </label>
                    <input 
                      v-model="bookingForm.dropoffAddress" 
                      type="text" 
                      required
                      class="w-full px-4 py-3 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                      placeholder="Ej: Aeropuerto César Manrique"
                    />
                  </div>
                </div>
              </div>

              <!-- Fecha y hora -->
              <div class="border-b border-neutral-volcanic pb-6">
                <h3 class="font-semibold text-neutral-dark mb-4">Fecha y hora</h3>
                
                <div class="space-y-4">
                  <div class="flex items-center gap-3">
                    <input 
                      v-model="bookingForm.isScheduled" 
                      type="checkbox" 
                      id="scheduled"
                      class="w-4 h-4 text-lanzarote-blue rounded"
                    />
                    <label for="scheduled" class="text-sm text-neutral-slate">
                      Programar para más tarde
                    </label>
                  </div>

                  <div v-if="bookingForm.isScheduled" class="grid grid-cols-2 gap-4">
                    <div>
                      <label class="block text-sm font-medium text-neutral-dark mb-1">
                        Fecha de recogida <span class="text-red-500">*</span>
                      </label>
                      <input 
                        v-model="bookingForm.viajeDate" 
                        type="date" 
                        :min="new Date().toISOString().split('T')[0]"
                        class="w-full px-4 py-3 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                      />
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-neutral-dark mb-1">
                        Hora de recogida <span class="text-red-500">*</span>
                      </label>
                      <input 
                        v-model="bookingForm.viajeTime" 
                        type="time" 
                        class="w-full px-4 py-3 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                      />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Opciones del viaje -->
              <div class="border-b border-neutral-volcanic pb-6">
                <h3 class="font-semibold text-neutral-dark mb-4">Opciones del viaje</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-neutral-dark mb-1">Pasajeros</label>
                    <select 
                      v-model.number="bookingForm.pasajeros" 
                      class="w-full px-4 py-3 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue bg-white"
                    >
                      <option v-for="n in 6" :key="n" :value="n">{{ n }} pasajero{{ n !== 1 ? 's' : '' }}</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-neutral-dark mb-1">Maletas</label>
                    <select 
                      v-model.number="bookingForm.luggage" 
                      class="w-full px-4 py-3 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue bg-white"
                    >
                      <option :value="0">Sin maletas</option>
                      <option :value="1">1 maleta</option>
                      <option :value="2">2 maletas</option>
                      <option :value="3">3 maletas</option>
                      <option :value="4">4+ maletas</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-neutral-dark mb-1">Método de pago</label>
                    <select 
                      v-model="bookingForm.pagoMethod" 
                      class="w-full px-4 py-3 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue bg-white"
                    >
                      <option value="efectivo">Efectivo</option>
                      <option value="wallet">Mi Cartera ({{ walletBalance.toFixed(2) }}€)</option>
                      <option value="tarjeta">Tarjeta</option>
                    </select>
                  </div>
                </div>

                <!-- Mensaje de saldo insuficiente para la cartera -->
                <div v-if="bookingForm.pagoMethod === 'wallet' && totalEstimatedPrice > walletBalance" 
                     class="mt-3 p-2 bg-red-50 border border-red-200 rounded-lg">
                  <p class="text-sm text-red-600">
                    Saldo insuficiente. Te faltan {{ (totalEstimatedPrice - walletBalance).toFixed(2) }}€
                  </p>
                </div>
              </div>

              <!-- Notas adicionales -->
              <div class="border-b border-neutral-volcanic pb-6">
                <h3 class="font-semibold text-neutral-dark mb-4">Notas adicionales</h3>
                <textarea 
                  v-model="bookingForm.notes" 
                  rows="3"
                  class="w-full px-4 py-3 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue resize-none"
                  placeholder="Ej: Necesito silla para bebé, vuelo número..."
                ></textarea>
              </div>

              <!-- Resumen y botón -->
              <div v-if="bookingForm.distance > 0" class="bg-neutral-soft p-4 rounded-lg">
                <div class="flex justify-between items-center mb-2">
                  <span class="text-neutral-slate">Distancia estimada:</span>
                  <span class="font-semibold">{{ bookingForm.distance }} km</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-neutral-volcanic">
                  <span class="text-lg font-bold text-neutral-dark">Precio estimado:</span>
                  <span class="text-2xl font-bold text-lanzarote-blue">{{ bookingForm.estimatedPrice.toFixed(2) }} €</span>
                </div>
                <div v-if="pendingDebt > 0" class="flex justify-between items-center mt-2">
                  <span class="text-sm text-neutral-slate">Deuda pendiente:</span>
                  <span class="font-semibold text-neutral-dark">{{ pendingDebt.toFixed(2) }} €</span>
                </div>
                <div v-if="pendingDebt > 0" class="flex justify-between items-center pt-2 border-t border-neutral-volcanic mt-2">
                  <span class="text-lg font-bold text-neutral-dark">Total a pagar:</span>
                  <span class="text-2xl font-bold text-lanzarote-blue">{{ totalEstimatedPrice.toFixed(2) }} €</span>
                </div>
              </div>

              <!-- Botón de confirmar reserva -->
              <button 
                type="submit"
                :disabled="!bookingForm.pickupAddress || !bookingForm.dropoffAddress || (bookingForm.pagoMethod === 'wallet' && totalEstimatedPrice > walletBalance)"
                class="w-full bg-lanzarote-blue text-white font-bold py-4 px-6 rounded-xl text-lg hover:bg-lanzarote-yellow hover:text-black transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Confirmar Reserva
              </button>
            </form>
          </div>
        </div>

        <!-- Mapa en tiempo real -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
            <h3 class="font-semibold text-neutral-dark mb-4">Taxis cerca de ti</h3>
            
            <MapaTaxi 
              :pickupLat="bookingForm.pickupLat"
              :pickupLng="bookingForm.pickupLng"
              :dropoffLat="bookingForm.dropoffLat"
              :dropoffLng="bookingForm.dropoffLng"
              @distance-calculated="(distance) => bookingForm.distance = distance"
              @location-found="handleUserLocation"
            />
            
            <div class="mt-4 space-y-2 text-sm">
              <div class="flex justify-between items-center py-2 border-b border-neutral-volcanic">
                <span class="text-neutral-slate">Taxis disponibles:</span>
                <span class="font-semibold text-lanzarote-blue">8 vehículos</span>
              </div>
              <div class="flex justify-between items-center py-2">
                <span class="text-neutral-slate">Tiempo estimado:</span>
                <span class="font-semibold">3-5 minutos</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DisposicionPasajero>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
const errorMsg = ref('')
import DisposicionPasajero from '../../Disposiciones/DisposicionPasajero.vue'
import MapaTaxi from '../../Componentes/MapaTaxi.vue'
import { useAuthStore } from '../../Almacenes/almacenAutenticacion.js'
import { useTripStore } from '../../Almacenes/almacenViaje.js'
import { useWalletStore } from '../../Almacenes/almacenCartera.js'
import axios from 'axios'

const authStore = useAuthStore()
const viajeStore = useTripStore()
const walletStore = useWalletStore()

// Formulario de reserva - simplificado

// Manejador de ubicación del usuario
const handleUserLocation = (location) => {
  if (location && location.address) {
    bookingForm.value.pickupAddress = location.address;
    if (location.lat && location.lng) {
      bookingForm.value.pickupLat = location.lat;
      bookingForm.value.pickupLng = location.lng;
    }
  }
}

// Función para obtener la ubicación actual del usuario y rellenar el campo de origen
const obtenerUbicacionUsuario = async () => {
  if (!navigator.geolocation) {
    alert('La geolocalización no está soportada en este navegador.');
    return;
  }
  navigator.geolocation.getCurrentPosition(async (position) => {
    const lat = position.coords.latitude;
    const lng = position.coords.longitude;
    // Llamada a un servicio de geocodificación inversa para obtener la dirección
    try {
      const response = await axios.get(`https://nominatim.openstreetmap.org/reverse`, {
        params: {
          lat,
          lon: lng,
          format: 'json',
        },
      });
      const address = response.data.display_name || `${lat}, ${lng}`;
      handleUserLocation({ address, lat, lng });
    } catch (e) {
      bookingForm.value.pickupAddress = `${lat}, ${lng}`;
      bookingForm.value.pickupLat = lat;
      bookingForm.value.pickupLng = lng;
    }
  }, (error) => {
    alert('No se pudo obtener la ubicación: ' + error.message);
  });
}

import { useRouter } from 'vue-router'
const router = useRouter()
// Ir al seguimiento del viaje
const irASeguimiento = (viajeId) => {
  router.push({ name: 'pasajero.seguimiento', params: { id: viajeId } })
}

// Función para texto de estado
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
const bookingForm = ref({
  // Ubicaciones
  pickupAddress: '',
  pickupLat: 28.963,
  pickupLng: -13.550,
  dropoffAddress: '',
  dropoffLat: null,
  dropoffLng: null,
  
  // Fecha y hora
  viajeDate: new Date().toISOString().split('T')[0],
  viajeTime: new Date().toTimeString().slice(0, 5),
  isScheduled: false,
  
  // Opciones de viaje
  pasajeros: 1,
  luggage: 0,
  pagoMethod: 'efectivo',
  
  // Información adicional
  notes: '',
  
  // Cálculos
  distance: 0,
  estimatedPrice: 0
})


// Municipios y trayectos fijos
const municipios = [
  'Arrecife', 'Puerto del Carmen', 'Costa Teguise', 'Playa Blanca', 'Haria', 'Teguise', 'Aeropuerto', 'Puerto Calero'
]
const trayectosFijos = [
  { origen: 'Aeropuerto', destino: 'Arrecife', dia: 10, noche: 14 },
  { origen: 'Aeropuerto', destino: 'Puerto del Carmen', dia: 12, noche: 18 },
  { origen: 'Aeropuerto', destino: 'Costa Teguise', dia: 20, noche: 24 },
  { origen: 'Arrecife', destino: 'Playa Blanca', dia: 45, noche: 50 },
  { origen: 'Puerto Calero', destino: 'Aeropuerto', dia: 45.86, noche: null },
]

const isNightTime = () => {
  const hour = parseInt(bookingForm.value.viajeTime.split(':')[0])
  return hour >= 22 || hour < 6
}

const getMunicipio = (direccion) => {
  if (!direccion) return 'Arrecife';
  for (const m of municipios) {
    if (direccion.toLowerCase().includes(m.toLowerCase())) return m;
  }
  return 'Arrecife';
}

const calculateEstimatedPrice = () => {
  const origen = getMunicipio(bookingForm.value.pickupAddress)
  const destino = getMunicipio(bookingForm.value.dropoffAddress)
  const distance = bookingForm.value.distance || 5.5
  const isNoche = isNightTime()

  // Buscar trayecto fijo
  for (const t of trayectosFijos) {
    if ((t.origen === origen && t.destino === destino) || (t.destino === origen && t.origen === destino)) {
      bookingForm.value.estimatedPrice = isNoche && t.noche ? t.noche : t.dia
      return
    }
  }

  // Si es dentro de Arrecife
  if (origen === 'Arrecife' && destino === 'Arrecife') {
    const bajada = isNoche ? 3.65 : 3.05
    const precioKm = isNoche ? 0.92 : 0.80
    bookingForm.value.estimatedPrice = Math.round((bajada + (distance * precioKm)) * 100) / 100
    return
  }

  // Si uno de los dos es Arrecife
  if (origen === 'Arrecife' || destino === 'Arrecife') {
    const bajada = isNoche ? 3.65 : 3.05
    const precioKm = isNoche ? 0.92 : 0.80
    bookingForm.value.estimatedPrice = Math.round((bajada + (distance * precioKm)) * 100) / 100
    return
  }

  // Otros municipios
  const bajada = 3.50
  const precioKm = 1.10
  bookingForm.value.estimatedPrice = Math.round((bajada + (distance * precioKm)) * 100) / 100
}

// Watch para recalcular precio
watch([() => bookingForm.value.distance, () => bookingForm.value.luggage, () => bookingForm.value.viajeTime], () => {
  calculateEstimatedPrice()
})

// Enviar reserva
const submitBooking = async () => {
  errorMsg.value = ''
  if (!bookingForm.value.pickupAddress || !bookingForm.value.dropoffAddress) {
    errorMsg.value = 'Por favor completa los datos de origen y destino.'
    return
  }

  if (bookingForm.value.pagoMethod === 'wallet' && totalEstimatedPrice.value > walletStore.balance) {
    errorMsg.value = 'No tienes suficiente saldo en tu cartera virtual. Añade dinero o elige otro método de pago.'
    return
  }
  
  const datosViaje = {
    pickup_address: bookingForm.value.pickupAddress,
    pickup_lat: bookingForm.value.pickupLat,
    pickup_lng: bookingForm.value.pickupLng,
    dropoff_address: bookingForm.value.dropoffAddress,
    dropoff_lat: bookingForm.value.dropoffLat || 28.978,
    dropoff_lng: bookingForm.value.dropoffLng || -13.561,
    distance: bookingForm.value.distance || 5.5,
    scheduled_for: bookingForm.value.isScheduled 
      ? `${bookingForm.value.viajeDate} ${bookingForm.value.viajeTime}` 
      : null,
    pasajeros: bookingForm.value.pasajeros,
    luggage: bookingForm.value.luggage,
    pago_method: bookingForm.value.pagoMethod,
    notes: bookingForm.value.notes
  }
  
  const result = await viajeStore.createTrip(datosViaje)

  console.log('Create trip result:', result)

  if (result.success) {
    console.log('Trip created successfully:', result.viaje)
    if (bookingForm.value.pagoMethod === 'wallet') {
      await walletStore.useFunds(result.viaje.price, result.viaje.id)
    }
    // alert('¡Reserva confirmada con éxito!')
    console.log('Fetching trips...')
    await viajeStore.fetchTrips()
    console.log('Viajes loaded:', viajeStore.viajesPasajero)
    await walletStore.fetchDebtSummary()
    resetForm()
    window.location.href = '/pasajero/reservas'
  } else {
    console.error('Error creating trip:', result.error)
    if (result.message) {
      errorMsg.value = result.message
    } else {
      errorMsg.value = 'Error no dispone de suficiente saldo en la Cartera'
    }
  }
}

const resetForm = () => {
  bookingForm.value = {
    pickupAddress: '',
    pickupLat: 28.963,
    pickupLng: -13.550,
    dropoffAddress: '',
    dropoffLat: null,
    dropoffLng: null,
    viajeDate: new Date().toISOString().split('T')[0],
    viajeTime: new Date().toTimeString().slice(0, 5),
    isScheduled: false,
    pasajeros: 1,
    luggage: 0,
    pagoMethod: 'efectivo',
    notes: '',
    distance: 0,
    estimatedPrice: 0
  }
}

// Viaje activo
const viajeActivo = computed(() => {
  return viajeStore.viajesPasajero.find(t => ['pendiente', 'accepted', 'in_progress'].includes(t.estado))
})

const walletBalance = computed(() => walletStore.balance)
const pendingDebt = computed(() => walletStore.pendingDebt)
const totalEstimatedPrice = computed(() => bookingForm.value.estimatedPrice + pendingDebt.value)

onMounted(() => {
  walletStore.fetchBalance()
  walletStore.fetchDebtSummary()
})
</script>