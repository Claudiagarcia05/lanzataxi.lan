<template>
  <div class="map-container">
    <div ref="mapElement" class="map"></div>

    <!-- Estado de búsqueda -->
    <div v-if="estado === 'pendiente'" class="searching-overlay">
      <div class="bg-white rounded-lg p-4 text-center">
        <div class="animate-spin text-3xl mb-2">🔄</div>
        <p class="font-medium">Buscando taxista disponible</p>
        <p class="text-sm text-neutral-slate mt-1">Espera mientras encontramos un taxi cerca de ti</p>
      </div>
    </div>

    <!-- Información de seguimiento -->
    <div v-if="estado === 'accepted' && taxiLocation" class="taxi-info">
      <div class="bg-white rounded-lg p-3 shadow-lg">
        <div class="flex items-center gap-2">
          <span class="text-2xl">🚕</span>
          <div>
            <p class="text-sm font-medium">Taxista en camino</p>
            <p class="text-xs text-neutral-slate">Llegada estimada: {{ tiempoLlegada }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick, computed, onUnmounted } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet-routing-machine/dist/leaflet-routing-machine.css'
import 'leaflet-routing-machine'
import '../../css/seguimiento.css'

const props = defineProps({
  pickupLat: { type: Number, required: true },
  pickupLng: { type: Number, required: true },
  dropoffLat: { type: Number, required: true },
  dropoffLng: { type: Number, required: true },
  taxiLat: { type: Number, default: null },
  taxiLng: { type: Number, default: null },
  estado: { type: String, default: 'pendiente' }
})

const emit = defineEmits(['taxi-aceptado'])

const mapElement = ref(null)
let map = null
let routingControl = null
let pickupMarker = null
let dropoffMarker = null
let taxiMarker = null

// Tiempo estimado de llegada (simulado)
const tiempoLlegada = computed(() => {
  if (!props.taxiLat || !props.taxiLng || !props.pickupLat) return 'calculando...'
  
  // Calcular distancia aproximada (esto debería venir de la ruta real)
  const distancia = Math.sqrt(
    Math.pow(props.taxiLat - props.pickupLat, 2) + 
    Math.pow(props.taxiLng - props.pickupLng, 2)
  ) * 111 // Aproximación a km
  
  const minutos = Math.round(distancia * 3) // Aprox 3 min por km
  return `${minutos} min`
})

// Iconos personalizados
const createIcon = (emoji, className = '') => {
  return L.divIcon({
    html: `<div class="marker-emoji ${className}">${emoji}</div>`,
    className: 'custom-marker',
    iconSize: [32, 32],
    popupAnchor: [0, -16]
  })
}

const icons = {
  pickup: createIcon('📍'),
  dropoff: createIcon('🏁'),
  taxi: createIcon('🚕', 'taxi-marker')
}

// Inicializar mapa
const initMap = async () => {
  if (!mapElement.value || map) return

  await nextTick()

  map = L.map(mapElement.value).setView([props.pickupLat, props.pickupLng], 13)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
  }).addTo(map)

  // Marcadores fijos
  pickupMarker = L.marker([props.pickupLat, props.pickupLng], { 
    icon: icons.pickup 
  }).addTo(map).bindPopup('Origen')

  dropoffMarker = L.marker([props.dropoffLat, props.dropoffLng], { 
    icon: icons.dropoff 
  }).addTo(map).bindPopup('Destino')

  // Calcular ruta
  calculateRoute()

  // Si hay ubicación del taxi, mostrarlo
  if (props.taxiLat && props.taxiLng) {
    addTaxiMarker()
  }
}

// Calcular ruta entre origen y destino
const calculateRoute = () => {
  if (routingControl) {
    map.removeControl(routingControl)
  }

  routingControl = L.Routing.control({
    waypoints: [
      L.latLng(props.pickupLat, props.pickupLng),
      L.latLng(props.dropoffLat, props.dropoffLng)
    ],
    router: L.Routing.osrmv1({
      serviceUrl: 'https://router.project-osrm.org/route/v1/',
      profile: 'car'
    }),
    lineOptions: {
      styles: [{ color: '#3b82f6', weight: 5 }],
      addWaypoints: false
    },
    show: false,
    createMarker: () => null
  }).addTo(map)

  routingControl.on('routesfound', function(e) {
    const bounds = e.routes[0].bounds
    map.fitBounds([
      [bounds.getSouth(), bounds.getWest()],
      [bounds.getNorth(), bounds.getEast()]
    ], { padding: [50, 50] })
  })
}

// Añadir marcador del taxi
const addTaxiMarker = () => {
  if (taxiMarker) {
    taxiMarker.setLatLng([props.taxiLat, props.taxiLng])
  } else {
    taxiMarker = L.marker([props.taxiLat, props.taxiLng], { 
      icon: icons.taxi,
      zIndexOffset: 1000
    }).addTo(map)
    
    // Emitir que el taxi ha sido aceptado/ubicado
    emit('taxi-aceptado', { lat: props.taxiLat, lng: props.taxiLng })
  }
}

// Watchers
watch(() => [props.taxiLat, props.taxiLng], ([newLat, newLng]) => {
  if (newLat && newLng) {
    addTaxiMarker()
  }
})

watch(() => [props.pickupLat, props.pickupLng, props.dropoffLat, props.dropoffLng], () => {
  if (map) {
    calculateRoute()
    if (pickupMarker) {
      pickupMarker.setLatLng([props.pickupLat, props.pickupLng])
    }
    if (dropoffMarker) {
      dropoffMarker.setLatLng([props.dropoffLat, props.dropoffLng])
    }
  }
})

// Lifecycle
onMounted(() => {
  initMap()
})

onUnmounted(() => {
  if (map) {
    map.remove()
  }
})
</script>
