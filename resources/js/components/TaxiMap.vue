<template>
  <div class="relative">
    <div ref="mapContainer" class="h-[500px] w-full rounded-xl border border-neutral-volcanic"></div>

    <div class="absolute top-4 right-4 bg-white p-2 rounded-lg shadow-lg space-y-2">
      <button @click="centerOnUser" class="btn-secondary p-2" aria-label="Center on user">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
      </button>
    </div>

    <div v-if="activeTrip" class="absolute bottom-4 left-4 right-4 bg-white p-4 rounded-lg shadow-lg">
      <div class="flex justify-between items-center">
        <div>
          <h3 class="font-semibold text-neutral-dark">Viaje activo</h3>
          <p class="text-sm text-neutral-slate">Destino: {{ activeTrip.dropoff_address }}</p>
          <p v-if="driverLocation" class="text-sm text-success-jable">
            Taxi a {{ distance }} metros - {{ time }} min
          </p>
        </div>
        <span :class="statusClass" class="px-3 py-1 rounded-full text-sm">
          {{ tripStatus }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
  iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
});

const props = defineProps({
  center: { type: Array, default: () => [28.963, -13.55] },
  zoom: { type: Number, default: 13 },
  markers: { type: Array, default: () => [] },
  activeTrip: { type: Object, default: null },
  driverLocation: { type: Object, default: null },
});

const mapContainer = ref(null);
let map;
let driverMarker;

const tripStatus = computed(() => props.activeTrip?.status ?? '');
const statusClass = computed(() => {
  const status = props.activeTrip?.status;

  if (status === 'pending') return 'bg-yellow-100 text-yellow-800';
  if (status === 'accepted') return 'bg-blue-100 text-blue-800';
  if (status === 'in_progress') return 'bg-purple-100 text-purple-800';
  if (status === 'completed') return 'bg-green-100 text-green-800';
  if (status === 'cancelled') return 'bg-red-100 text-red-800';
  return 'bg-neutral-100 text-neutral-700';
});

const distance = computed(() => {
  if (!props.activeTrip?.distance) return 0;
  return Math.round(props.activeTrip.distance * 1000);
});

const time = computed(() => {
  if (!props.activeTrip?.distance) return 0;
  return Math.max(1, Math.round(props.activeTrip.distance * 3));
});

onMounted(() => {
  map = L.map(mapContainer.value).setView(props.center, props.zoom);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
  }).addTo(map);

  props.markers.forEach((marker) => {
    L.marker([marker.lat, marker.lng]).bindPopup(marker.popup).addTo(map);
  });
});

watch(
  () => props.driverLocation,
  (newLocation) => {
    if (newLocation && map) {
      if (driverMarker) {
        driverMarker.setLatLng([newLocation.lat, newLocation.lng]);
      } else {
        const taxiIcon = L.divIcon({
          html: '🚕',
          className: 'text-2xl',
          iconSize: [30, 30],
        });
        driverMarker = L.marker([newLocation.lat, newLocation.lng], { icon: taxiIcon })
          .bindPopup('Taxi asignado')
          .addTo(map);
      }
      map.panTo([newLocation.lat, newLocation.lng]);
    }
  },
);

const centerOnUser = () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition((position) => {
      map.setView([position.coords.latitude, position.coords.longitude], 15);
    });
  }
};
</script>
