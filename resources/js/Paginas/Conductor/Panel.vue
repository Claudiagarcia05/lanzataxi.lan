
<script setup>
import { computed } from 'vue'
import DisposicionConductor from '../../Disposiciones/DisposicionConductor.vue'
import { useAuthStore } from '../../Almacenes/almacenAutenticacion.js'
import { useTripStore } from '../../Almacenes/almacenViaje.js'
import { useConductorStore } from '../../Almacenes/almacenConductor.js'

const authStore = useAuthStore()
const viajeStore = useTripStore()
const conductorStore = useConductorStore()

const estadisticas = computed(() => {
  const viajesHoy = viajeStore.viajesHoy.filter(t => t.conductorId === authStore.usuario?.id)
  const earnings = viajesHoy.reduce((sum, t) => sum + (t.price || 0), 0)

  return [
    { label: 'Viajes hoy', value: viajesHoy.length, icon: '🚖', change: '+2 ayer' },
    { label: 'Ganancias hoy', value: `${earnings.toFixed(2)} €`, icon: '💰', change: `media ${(earnings / (viajesHoy.length || 1)).toFixed(2)} €` },
    { label: 'Valoración', value: conductorStore.formattedRating, icon: '⭐', change: 'de 5' },
    { label: 'Total viajes', value: conductorStore.estadisticas.totalviajes, icon: '🧾', change: 'todo el tiempo' }
  ]
})

const solicitudesPendientes = computed(() => {
  return viajeStore.viajesPendientes.filter(t => !t.conductorId)
})

const viajesAceptados = computed(() => {
  return viajeStore.viajesConductor.filter(t => ['accepted', 'in_progress'].includes(t.estado))
})

const viajesHoy = computed(() => {
  return viajeStore.viajesHoy
    .filter(t => t.conductorId === authStore.usuario?.id && t.estado === 'completed')
    .sort((a, b) => new Date(b.date) - new Date(a.date))
})

const aceptarViaje = async (viajeId) => {
  await conductorStore.aceptarViaje(viajeId)
}

const startTrip = async (viajeId) => {
  await viajeStore.startTrip(viajeId)
}

const completeTrip = async (viajeId) => {
  await viajeStore.completeTrip(viajeId)
  await conductorStore.actualizarEstadisticas()
}
</script>

<template>
  <DisposicionConductor>
    <div class="flex justify-between items-start mb-8">
      <div>
        <h2 class="text-2xl font-bold text-neutral-dark">¡Hola, {{ authStore.usuario?.name }}! 👋</h2>
        <p class="text-neutral-slate">Panel de control de taxista</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div v-for="stat in estadisticas" :key="stat.label"
           class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
          <span class="text-2xl">{{ stat.icon }}</span>
          <span class="text-xs text-green-600">{{ stat.change }}</span>
        </div>
        <p class="text-2xl font-bold text-neutral-dark">{{ stat.value }}</p>
        <p class="text-sm text-neutral-slate">{{ stat.label }}</p>
      </div>
    </div>

    <div v-if="solicitudesPendientes.length > 0" class="mb-8">
      <h3 class="font-semibold text-neutral-dark mb-4 text-lg">🧾 Solicitudes pendientes ({{ solicitudesPendientes.length }})</h3>
      <div class="grid gap-4">
        <div v-for="solicitud in solicitudesPendientes" :key="solicitud.id"
             class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-lanzarote-yellow hover:shadow-md transition-shadow">
          <div class="flex justify-between items-start">
            <div>
              <div class="flex items-center space-x-2 mb-2">
                <span class="font-semibold text-neutral-dark">{{ solicitud.pasajeroName }}</span>
                <span class="text-xs bg-neutral-100 px-2 py-0.5 rounded-full">Nuevo</span>
              </div>
              <div class="space-y-2 text-sm">
                <p class="flex items-center text-neutral-slate">
                  <span class="w-16 text-neutral-dark">📍 Origen:</span>
                  {{ solicitud.pickup }}
                </p>
                <p class="flex items-center text-neutral-slate">
                  <span class="w-16 text-neutral-dark">🏁 Destino:</span>
                  {{ solicitud.dropoff }}
                </p>
              </div>
              <div class="flex space-x-4 mt-3 text-xs">
                <span class="bg-neutral-100 px-2 py-1 rounded-full">📏 {{ solicitud.distance }} km</span>
                <span class="bg-neutral-100 px-2 py-1 rounded-full">💶 {{ solicitud.price }} €</span>
              </div>
            </div>
            <div class="flex space-x-2">
              <button @click="aceptarViaje(solicitud.id)"
                      class="bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600 transition-colors">
                Aceptar
              </button>
              <button class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-600 transition-colors">
                Rechazar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="viajesAceptados.length > 0" class="mb-8">
      <h3 class="font-semibold text-neutral-dark mb-4 text-lg">🚕 Viaje en curso</h3>
      <div v-for="viaje in viajesAceptados" :key="viaje.id"
           class="bg-lanzarote-yellow/20 border border-lanzarote-yellow rounded-xl p-6">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h4 class="font-semibold text-neutral-dark">Pasajero: {{ viaje.pasajeroName }}</h4>
            <p class="text-sm text-neutral-slate">Estado: {{ viaje.estado === 'accepted' ? 'Aceptado' : 'En curso' }}</p>
          </div>
          <span class="bg-lanzarote-blue text-white px-3 py-1 rounded-full text-sm">
            {{ viaje.price }} €
          </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div class="bg-white/50 p-3 rounded-lg">
            <p class="text-xs text-neutral-slate">Origen</p>
            <p class="font-medium">{{ viaje.pickup }}</p>
          </div>
          <div class="bg-white/50 p-3 rounded-lg">
            <p class="text-xs text-neutral-slate">Destino</p>
            <p class="font-medium">{{ viaje.dropoff }}</p>
          </div>
        </div>

        <div class="flex space-x-3">
          <button v-if="viaje.estado === 'accepted'"
                  @click="startTrip(viaje.id)"
                  class="bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-600">
            Iniciar viaje
          </button>
          <button v-if="viaje.estado === 'in_progress'"
                  @click="completeTrip(viaje.id)"
                  class="bg-lanzarote-blue text-white px-4 py-2 rounded-lg text-sm hover:bg-lanzarote-yellow hover:text-black">
            Completar viaje
          </button>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm">
      <div class="p-6 border-b border-neutral-volcanic flex justify-between items-center">
        <h3 class="font-semibold text-neutral-dark">Viajes de hoy</h3>
        <span class="text-sm text-neutral-slate">Total: {{ viajesHoy.length }} viajes</span>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-neutral-soft">
            <tr>
              <th class="text-left p-4 text-sm font-medium text-neutral-slate">Hora</th>
              <th class="text-left p-4 text-sm font-medium text-neutral-slate">Pasajero</th>
              <th class="text-left p-4 text-sm font-medium text-neutral-slate">Origen</th>
              <th class="text-left p-4 text-sm font-medium text-neutral-slate">Destino</th>
              <th class="text-left p-4 text-sm font-medium text-neutral-slate">Distancia</th>
              <th class="text-left p-4 text-sm font-medium text-neutral-slate">Ganancia</th>
              <th class="text-left p-4 text-sm font-medium text-neutral-slate">Valoración</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="viaje in viajesHoy" :key="viaje.id" class="border-b border-neutral-volcanic hover:bg-neutral-soft">
              <td class="p-4 font-medium">{{ new Date(viaje.date).toLocaleTimeString() }}</td>
              <td class="p-4">{{ viaje.pasajeroName }}</td>
              <td class="p-4 text-neutral-slate">{{ viaje.pickup }}</td>
              <td class="p-4 text-neutral-slate">{{ viaje.dropoff }}</td>
              <td class="p-4">{{ viaje.distance }} km</td>
              <td class="p-4 font-medium text-green-600">{{ viaje.price }} €</td>
              <td class="p-4">
                <div class="flex items-center">
                  <span class="text-yellow-400">★</span>
                  <span class="ml-1">{{ viaje.valoracion || '–' }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="conductorStore.perfil" class="mt-8 bg-white rounded-xl shadow-sm p-6">
      <h3 class="font-semibold text-neutral-dark mb-4">Mi vehículo</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
          <p class="text-xs text-neutral-slate">Modelo</p>
          <p class="font-medium">{{ conductorStore.perfil.vehicle.model }}</p>
        </div>
        <div>
          <p class="text-xs text-neutral-slate">Matrícula</p>
          <p class="font-medium">{{ conductorStore.perfil.vehicle.plate }}</p>
        </div>
        <div>
          <p class="text-xs text-neutral-slate">Año</p>
          <p class="font-medium">{{ conductorStore.perfil.vehicle.year }}</p>
        </div>
        <div>
          <p class="text-xs text-neutral-slate">Capacidad</p>
          <p class="font-medium">{{ conductorStore.perfil.vehicle.capacity }} personas</p>
        </div>
      </div>
    </div>
  </DisposicionConductor>
</template>


