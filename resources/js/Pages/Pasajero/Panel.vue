<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import PasajeroLayout from '../../Layouts/PasajeroLayout.vue'
import TaxiMap from '../../Components/TaxiMap.vue'
import { useAuthStore } from '../../stores/authStore'
import { useTripStore } from '../../stores/viajeStore'
import { useWalletStore } from '../../stores/walletStore'
import axios from 'axios'

const authStore = useAuthStore()
const viajeStore = useTripStore()
const walletStore = useWalletStore()

// Formulario de reserva extendido
const bookingForm = ref({
  // Detalles del pasajero
  pasajeroName: authStore.usuario?.name || '',
  pasajeroPhone: authStore.usuario?.phone || '',
  pasajeroEmail: authStore.usuario?.email || '',
  
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
  pagoMethod: 'cash',
  
  // InformaciÃƒÂ³n adicional
  notes: '',
  
  // CÃƒÂ¡lculos
  distance: 0,
  estimatedPrice: 0
})

// Destinos populares
const popularDestinations = ref([
  { name: 'Aeropuerto CÃƒÂ©sar Manrique', address: 'Aeropuerto de Lanzarote', lat: 28.945, lng: -13.605, icon: 'Ã¢Å“Ë†Ã¯Â¸Â' },
  { name: 'Puerto del Carmen', address: 'Avenida de las Playas', lat: 28.918, lng: -13.661, icon: 'Ã°Å¸Ââ€“Ã¯Â¸Â' },
  { name: 'Playa Blanca', address: 'Centro de Playa Blanca', lat: 28.860, lng: -13.830, icon: 'Ã°Å¸Å’Å ' },
  { name: 'Arrecife Centro', address: 'Calle LeÃƒÂ³n y Castillo', lat: 28.963, lng: -13.550, icon: 'Ã°Å¸Ââ€ºÃ¯Â¸Â' },
  { name: 'Costa Teguise', address: 'Avenida del Mar', lat: 29.002, lng: -13.498, icon: 'Ã°Å¸ÂÂÃ¯Â¸Â' },
  { name: 'Teguise Pueblo', address: 'Plaza de la ConstituciÃƒÂ³n', lat: 29.060, lng: -13.563, icon: 'Ã°Å¸ÂËœÃ¯Â¸Â' }
])

// Favoritos del usuario desde el API
const userFavorites = ref([])
const loadingFavorites = ref(false)

const loadFavorites = async () => {
  loadingFavorites.value = true
  try {
    const response = await axios.get('/api/favorites')
    userFavorites.value = response.data
  } catch (error) {
    console.error('Error al cargar favoritos:', error)
  } finally {
    loadingFavorites.value = false
  }
}

// Calcular precio estimado
const calculateEstimatedPrice = () => {
  if (bookingForm.value.distance > 0) {
    const basePrice = 3.50 // Tarifa base
    const pricePerKm = 1.20
    const nightSurcharge = isNightTime() ? 1.25 : 1.0
    const luggageFee = bookingForm.value.luggage * 0.50
    
    const total = (basePrice + (bookingForm.value.distance * pricePerKm)) * nightSurcharge + luggageFee
    bookingForm.value.estimatedPrice = Math.round(total * 100) / 100
  }
}

const isNightTime = () => {
  const hour = parseInt(bookingForm.value.viajeTime.split(':')[0])
  return hour >= 22 || hour < 6
}

// Seleccionar destino popular
const selectDestination = (destination) => {
  bookingForm.value.dropoffAddress = destination.address
  bookingForm.value.dropoffLat = destination.lat
  bookingForm.value.dropoffLng = destination.lng
  
  // Calcular distancia aproximada (simulada)
  if (bookingForm.value.pickupLat && bookingForm.value.pickupLng) {
    const R = 6371 // Radio de la Tierra en km
    const dLat = (destination.lat - bookingForm.value.pickupLat) * Math.PI / 180
    const dLon = (destination.lng - bookingForm.value.pickupLng) * Math.PI / 180
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(bookingForm.value.pickupLat * Math.PI / 180) * 
              Math.cos(destination.lat * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2)
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a))
    bookingForm.value.distance = Math.round(R * c * 10) / 10
    calculateEstimatedPrice()
  }
}

// Seleccionar favorito del usuario
const selectUserFavorite = (favorite) => {
  bookingForm.value.dropoffAddress = favorite.address
  bookingForm.value.dropoffLat = favorite.lat
  bookingForm.value.dropoffLng = favorite.lng
  
  if (bookingForm.value.pickupLat && bookingForm.value.pickupLng) {
    const R = 6371
    const dLat = (favorite.lat - bookingForm.value.pickupLat) * Math.PI / 180
    const dLon = (favorite.lng - bookingForm.value.pickupLng) * Math.PI / 180
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(bookingForm.value.pickupLat * Math.PI / 180) * 
              Math.cos(favorite.lat * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2)
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a))
    bookingForm.value.distance = Math.round(R * c * 10) / 10
    calculateEstimatedPrice()
  }
}

// Enviar reserva
const submitBooking = async () => {
  if (!bookingForm.value.pickupAddress || !bookingForm.value.dropoffAddress) {
    alert('Por favor completa los datos de origen y destino')
    return
  }
  
  const datosViaje = {
    pickup_address: bookingForm.value.pickupAddress,
    pickup_lat: bookingForm.value.pickupLat,
    pickup_lng: bookingForm.value.pickupLng,
    dropoff_address: bookingForm.value.dropoffAddress,
    dropoff_lat: bookingForm.value.dropoffLat,
    dropoff_lng: bookingForm.value.dropoffLng,
    distance: bookingForm.value.distance,
    scheduled_for: bookingForm.value.isScheduled 
      ? `${bookingForm.value.viajeDate} ${bookingForm.value.viajeTime}` 
      : null,
    pasajeros: bookingForm.value.pasajeros,
    luggage: bookingForm.value.luggage,
    pago_method: bookingForm.value.pagoMethod,
    notes: bookingForm.value.notes
  }
  
  const result = await viajeStore.createTrip(datosViaje)
  
  if (result.success) {
    alert('Ã‚Â¡Reserva creada con ÃƒÂ©xito! Un conductor aceptarÃƒÂ¡ tu viaje pronto.')
    resetForm()
  } else {
    alert('Error al crear la reserva. IntÃƒÂ©ntalo de nuevo.')
  }
}

const resetForm = () => {
  bookingForm.value = {
    pasajeroName: authStore.usuario?.name || '',
    pasajeroPhone: authStore.usuario?.phone || '',
    pasajeroEmail: authStore.usuario?.email || '',
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
    pagoMethod: 'cash',
    notes: '',
    distance: 0,
    estimatedPrice: 0
  }
}

// Watch para recalcular precio
watch([() => bookingForm.value.distance, () => bookingForm.value.luggage, () => bookingForm.value.viajeTime], () => {
  calculateEstimatedPrice()
})

// Viaje activo
const viajeActivo = computed(() => {
  return viajeStore.viajesPasajero.find(t => ['pendiente', 'accepted', 'in_progress'].includes(t.estado))
})

const walletBalance = computed(() => walletStore.balance)

onMounted(() => {
  loadFavorites()
  walletStore.fetchBalance()
})
</script>

<template>
  <PasajeroLayout>
    <div class="mb-8">
      <h2 class="text-3xl font-bold text-neutral-dark">Nueva Reserva Ã°Å¸Å¡â€“</h2>
      <p class="text-neutral-slate mt-2">Completa el formulario para solicitar tu taxi</p>
    </div>

    <!-- Alerta de viaje activo -->
    <div v-if="viajeActivo" class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <span class="text-2xl">Ã¢Å¡Â Ã¯Â¸Â</span>
        </div>
        <div class="ml-3">
          <p class="text-sm font-medium text-yellow-800">
            Ya tienes un viaje activo desde {{ viajeActivo.pickup_address }} a {{ viajeActivo.dropoff_address }}
          </p>
          <p class="text-xs text-yellow-700 mt-1">
            Estado: {{ viajeActivo.estado }}
          </p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Formulario principal (columna izquierda y central) -->
      <div class="lg:col-span-2">
        <form @submit.prevent="submitBooking" class="bg-white rounded-xl shadow-sm p-6 space-y-6">
          
          <!-- SecciÃƒÂ³n: Datos del pasajero -->
          <div>
            <h3 class="text-lg font-semibold text-neutral-dark mb-4 flex items-center">
              Ã°Å¸â€˜Â¤ Datos del pasajero
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-neutral-slate mb-2">Nombre completo</label>
                <input 
                  v-model="bookingForm.pasajeroName" 
                  type="text" 
                  required
                  class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                  placeholder="Juan PÃƒÂ©rez"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-neutral-slate mb-2">TelÃƒÂ©fono</label>
                <input 
                  v-model="bookingForm.pasajeroPhone" 
                  type="tel" 
                  required
                  class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                  placeholder="+34 600 123 456"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-neutral-slate mb-2">Email</label>
                <input 
                  v-model="bookingForm.pasajeroEmail" 
                  type="email" 
                  required
                  class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                  placeholder="correo@ejemplo.com"
                />
              </div>
            </div>
          </div>

          <!-- SecciÃƒÂ³n: Ubicaciones -->
          <div>
            <h3 class="text-lg font-semibold text-neutral-dark mb-4 flex items-center">
              Ã°Å¸â€œÂ Ubicaciones
            </h3>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-neutral-slate mb-2">Punto de recogida</label>
                <input 
                  v-model="bookingForm.pickupAddress" 
                  type="text" 
                  required
                  class="w-full px-4 py-3 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                  placeholder="Ej: Avenida Fred Olsen, Arrecife"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-neutral-slate mb-2">Destino</label>
                <input 
                  v-model="bookingForm.dropoffAddress" 
                  type="text" 
                  required
                  class="w-full px-4 py-3 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                  placeholder="Ej: Aeropuerto CÃƒÂ©sar Manrique"
                />
              </div>

              <!-- Destinos populares -->
              <div>
                <p class="text-sm font-medium text-neutral-slate mb-3">Destinos populares:</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                  <button 
                    v-for="destination in popularDestinations" 
                    :key="destination.name"
                    type="button"
                    @click="selectDestination(destination)"
                    class="flex items-center gap-2 px-3 py-2 bg-neutral-soft hover:bg-lanzarote-blue/10 rounded-lg text-sm transition-colors"
                  >
                    <span>{{ destination.icon }}</span>
                    <span class="truncate">{{ destination.name }}</span>
                  </button>
                </div>
              </div>

              <!-- Mis favoritos -->
              <div v-if="userFavorites.length > 0">
                <p class="text-sm font-medium text-neutral-slate mb-3">Ã¢Â­Â Mis favoritos:</p>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                  <button 
                    v-for="favorite in userFavorites" 
                    :key="favorite.id"
                    type="button"
                    @click="selectUserFavorite(favorite)"
                    class="flex items-center gap-2 px-3 py-2 bg-lanzarote-yellow/10 hover:bg-lanzarote-yellow/20 rounded-lg text-sm transition-colors border border-lanzarote-yellow/30"
                  >
                    <span>Ã¢Â­Â</span>
                    <span class="truncate font-medium">{{ favorite.name }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- SecciÃƒÂ³n: Fecha y hora -->
          <div>
            <h3 class="text-lg font-semibold text-neutral-dark mb-4 flex items-center">
              Ã°Å¸â€œâ€¦ Fecha y hora
            </h3>
            <div class="space-y-4">
              <div class="flex items-center gap-3">
                <input 
                  v-model="bookingForm.isScheduled" 
                  type="checkbox" 
                  id="scheduled"
                  class="w-4 h-4 text-lanzarote-blue rounded"
                />
                <label for="scheduled" class="text-sm font-medium text-neutral-slate">
                  Programar para mÃƒÂ¡s tarde
                </label>
              </div>

              <div v-if="bookingForm.isScheduled" class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-neutral-slate mb-2">Fecha</label>
                  <input 
                    v-model="bookingForm.viajeDate" 
                    type="date" 
                    :min="new Date().toISOString().split('T')[0]"
                    class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-neutral-slate mb-2">Hora</label>
                  <input 
                    v-model="bookingForm.viajeTime" 
                    type="time" 
                    class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                  />
                </div>
              </div>
              <p v-else class="text-sm text-neutral-slate bg-lanzarote-blue/5 px-4 py-2 rounded-lg">
                Ã°Å¸Å¡â‚¬ Viaje inmediato - El taxi serÃƒÂ¡ asignado en los prÃƒÂ³ximos minutos
              </p>
            </div>
          </div>

          <!-- SecciÃƒÂ³n: Opciones del viaje -->
          <div>
            <h3 class="text-lg font-semibold text-neutral-dark mb-4 flex items-center">
              Ã¢Å¡â„¢Ã¯Â¸Â Opciones del viaje
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-medium text-neutral-slate mb-2">NÃƒÂºmero de pasajeros</label>
                <select 
                  v-model.number="bookingForm.pasajeros" 
                  class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                >
                  <option :value="1">1 pasajero</option>
                  <option :value="2">2 pasajeros</option>
                  <option :value="3">3 pasajeros</option>
                  <option :value="4">4 pasajeros</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-neutral-slate mb-2">Equipaje</label>
                <select 
                  v-model.number="bookingForm.luggage" 
                  class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                >
                  <option :value="0">Sin equipaje</option>
                  <option :value="1">1 maleta</option>
                  <option :value="2">2 maletas</option>
                  <option :value="3">3 maletas</option>
                  <option :value="4">4+ maletas</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-neutral-slate mb-2">MÃƒÂ©todo de pago</label>
                <select 
                  v-model="bookingForm.pagoMethod" 
                  class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
                >
                  <option value="cash">Efectivo</option>
                  <option value="card">Tarjeta</option>
                  <option value="wallet">Mi Cartera ({{ walletBalance.toFixed(2) }}Ã¢â€šÂ¬)</option>
                  <option value="app">App / Digital</option>
                </select>
                <div v-if="bookingForm.pagoMethod === 'wallet' && bookingForm.estimatedPrice > 0" class="mt-2 p-2 rounded-lg text-sm" :class="walletBalance >= bookingForm.estimatedPrice ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600'">
                  <span v-if="walletBalance >= bookingForm.estimatedPrice">Ã°Å¸â€™Â° Saldo despuÃƒÂ©s del viaje: {{ (walletBalance - bookingForm.estimatedPrice).toFixed(2) }} Ã¢â€šÂ¬</span>
                  <span v-else>Ã¢Å¡Â Ã¯Â¸Â Saldo insuficiente. AÃƒÂ±ade saldo en tu perfil.</span>
                </div>
              </div>
            </div>
          </div>

          <!-- SecciÃƒÂ³n: Notas -->
          <div>
            <h3 class="text-lg font-semibold text-neutral-dark mb-4 flex items-center">
              Ã°Å¸â€œÂ Notas o comentarios
            </h3>
            <textarea 
              v-model="bookingForm.notes" 
              rows="3"
              class="w-full px-4 py-3 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue resize-none"
              placeholder="Ej: Llamar al llegar, necesito silla de bebÃƒÂ©, etc."
            ></textarea>
          </div>

          <!-- Resumen del precio -->
          <div v-if="bookingForm.distance > 0" class="bg-lanzarote-blue/5 rounded-xl p-4">
            <div class="flex justify-between items-center mb-2">
              <span class="text-neutral-slate">Distancia estimada:</span>
              <span class="font-semibold">{{ bookingForm.distance }} km</span>
            </div>
            <div class="flex justify-between items-center mb-2">
              <span class="text-neutral-slate">Cargo por equipaje:</span>
              <span class="font-semibold">{{ (bookingForm.luggage * 0.50).toFixed(2) }} Ã¢â€šÂ¬</span>
            </div>
            <div v-if="isNightTime()" class="flex justify-between items-center mb-2">
              <span class="text-neutral-slate">Suplemento nocturno:</span>
              <span class="font-semibold text-yellow-600">+25%</span>
            </div>
            <div class="flex justify-between items-center pt-2 border-t border-neutral-volcanic">
              <span class="text-lg font-bold text-neutral-dark">Precio estimado:</span>
              <span class="text-2xl font-bold text-lanzarote-blue">{{ bookingForm.estimatedPrice.toFixed(2) }} Ã¢â€šÂ¬</span>
            </div>
          </div>

          <!-- BotÃƒÂ³n de envÃƒÂ­o -->
          <div class="flex gap-3">
            <button 
              type="submit"
              class="flex-1 bg-lanzarote-blue text-white font-semibold py-3 rounded-lg hover:bg-lanzarote-yellow hover:text-black transition-colors shadow-lg"
            >
              Ã°Å¸Å¡â€“ Confirmar reserva
            </button>
            <button 
              type="button"
              @click="resetForm"
              class="px-6 border border-neutral-volcanic text-neutral-slate py-3 rounded-lg hover:bg-neutral-soft transition-colors"
            >
              Limpiar
            </button>
          </div>
        </form>
      </div>

      <!-- Mapa en tiempo real (columna derecha) -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
          <h3 class="text-lg font-semibold text-neutral-dark mb-4 flex items-center">
            Ã°Å¸â€”ÂºÃ¯Â¸Â Taxis disponibles
          </h3>
          <TaxiMap 
            :pickupLat="bookingForm.pickupLat"
            :pickupLng="bookingForm.pickupLng"
            :dropoffLat="bookingForm.dropoffLat"
            :dropoffLng="bookingForm.dropoffLng"
          />
          
          <div class="mt-4 space-y-2">
            <div class="flex items-center justify-between text-sm">
              <span class="text-neutral-slate">Tiempo de espera estimado:</span>
              <span class="font-semibold text-lanzarote-blue">3-5 min</span>
            </div>
            <div class="flex items-center justify-between text-sm">
              <span class="text-neutral-slate">Taxis en la zona:</span>
              <span class="font-semibold">8 disponibles</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </PasajeroLayout>
</template>


