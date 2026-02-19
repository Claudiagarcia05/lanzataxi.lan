<template>
  <div class="taxi-map">
    <div class="map-container" ref="mapContainer"></div>
    <div class="map-overlay">
      <div class="taxi-count">
        <span class="count-badge">{{ nearbyTaxis.length }}</span>
        <span class="count-label">Taxis disponibles</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onUnmounted } from 'vue'
import axios from 'axios'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const props = defineProps({
  pickupLat: Number,
  pickupLng: Number,
  dropoffLat: Number,
  dropoffLng: Number,
  radius: {
    type: Number,
    default: 5
  }
})

const nearbyTaxis = ref([])
const mapContainer = ref(null)
let map = null
let markers = {
  pickup: null,
  dropoff: null,
  taxis: []
}
let routeLine = null

// Iconos personalizados
const pickupIcon = L.divIcon({
  className: 'custom-marker',
  html: '<div style="font-size: 28px;">ðŸ“</div>',
  iconSize: [30, 30],
  iconAnchor: [15, 30]
})

const dropoffIcon = L.divIcon({
  className: 'custom-marker',
  html: '<div style="font-size: 28px;">ðŸŽ¯</div>',
  iconSize: [30, 30],
  iconAnchor: [15, 30]
})

const taxiIcon = L.divIcon({
  className: 'custom-marker',
  html: '<div style="font-size: 24px;">ðŸš–</div>',
  iconSize: [30, 30],
  iconAnchor: [15, 15]
})

const initMap = () => {
  if (map) return

  // Coordenadas por defecto: Arrecife, Lanzarote
  const defaultLat = props.pickupLat || 28.9633
  const defaultLng = props.pickupLng || -13.5475

  map = L.map(mapContainer.value).setView([defaultLat, defaultLng], 13)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'Â© OpenStreetMap contributors',
    maxZoom: 19
  }).addTo(map)

  updateMarkers()
}

const updateMarkers = () => {
  if (!map) return

  // Limpiar marcadores existentes
  if (markers.pickup) map.removeLayer(markers.pickup)
  if (markers.dropoff) map.removeLayer(markers.dropoff)
  if (routeLine) map.removeLayer(routeLine)
  markers.taxis.forEach(m => map.removeLayer(m))
  markers.taxis = []

  const bounds = []

  // Marcador de origen
  if (props.pickupLat && props.pickupLng) {
    markers.pickup = L.marker([props.pickupLat, props.pickupLng], { icon: pickupIcon })
      .addTo(map)
      .bindPopup('Punto de recogida')
    bounds.push([props.pickupLat, props.pickupLng])
  }

  // Marcador de destino
  if (props.dropoffLat && props.dropoffLng) {
    markers.dropoff = L.marker([props.dropoffLat, props.dropoffLng], { icon: dropoffIcon })
      .addTo(map)
      .bindPopup('Destino')
    bounds.push([props.dropoffLat, props.dropoffLng])

    // Dibujar lÃ­nea de ruta
    if (props.pickupLat && props.pickupLng) {
      routeLine = L.polyline([
        [props.pickupLat, props.pickupLng],
        [props.dropoffLat, props.dropoffLng]
      ], {
        color: '#0EA5E9',
        weight: 3,
        opacity: 0.7,
        dashArray: '10, 10'
      }).addTo(map)
    }
  }

  // Marcadores de taxis
  nearbyTaxis.value.forEach(taxi => {
    if (taxi.lat && taxi.lng) {
      const marker = L.marker([taxi.lat, taxi.lng], { icon: taxiIcon })
        .addTo(map)
        .bindPopup(`<b>${taxi.conductor_name}</b><br>${taxi.distance} km`)
      markers.taxis.push(marker)
      bounds.push([taxi.lat, taxi.lng])
    }
  })

  // Ajustar vista para mostrar todos los marcadores
  if (bounds.length > 0) {
    map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 })
  }
}

const fetchNearbyTaxis = async () => {
  try {
    const response = await axios.get('/api/nearby-conductors', {
      params: {
        lat: props.pickupLat,
        lng: props.pickupLng,
        radius: props.radius
      }
    })
    nearbyTaxis.value = response.data
    updateMarkers()
  } catch (error) {
    console.error('Error al cargar taxis cercanos:', error)
    // Datos de demostraciÃ³n cerca de Arrecife, Lanzarote
    nearbyTaxis.value = [
      { id: 1, conductor_name: 'Juan P.', distance: 1.2, lat: 28.9650, lng: -13.5450 },
      { id: 2, conductor_name: 'MarÃ­a G.', distance: 2.5, lat: 28.9600, lng: -13.5500 },
      { id: 3, conductor_name: 'Carlos R.', distance: 3.1, lat: 28.9680, lng: -13.5420 }
    ]
    updateMarkers()
  }
}

watch([() => props.pickupLat, () => props.pickupLng, () => props.dropoffLat, () => props.dropoffLng], () => {
  if (map) {
    updateMarkers()
  }
  if (props.pickupLat && props.pickupLng) {
    fetchNearbyTaxis()
  }
})

onMounted(() => {
  setTimeout(() => {
    initMap()
    if (props.pickupLat && props.pickupLng) {
      fetchNearbyTaxis()
    }
  }, 100)
})

onUnmounted(() => {
  if (map) {
    map.remove()
    map = null
  }
})
</script>

<style scoped>
.taxi-map {
  width: 100%;
  height: 100%;
  position: relative;
}

.map-container {
  width: 100%;
  height: 400px;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  z-index: 1;
}

.map-overlay {
  position: absolute;
  top: 16px;
  right: 16px;
  z-index: 1000;
  pointer-events: none;
}

.taxi-count {
  background: white;
  padding: 12px 16px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  display: flex;
  align-items: center;
  gap: 10px;
  pointer-events: auto;
}

.count-badge {
  background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
}

.count-label {
  font-size: 13px;
  color: #4a5568;
  font-weight: 500;
}

/* Estilos para marcadores personalizados */
:deep(.custom-marker) {
  background: transparent;
  border: none;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.1); }
}

/* Estilos para popups de Leaflet */
:deep(.leaflet-popup-content-wrapper) {
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

:deep(.leaflet-popup-content) {
  margin: 12px 16px;
  font-family: inherit;
}
</style>


