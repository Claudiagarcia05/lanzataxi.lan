<script setup>
import { ref, computed, onMounted } from 'vue'
import { router as inertiaRouter, usePage } from '@inertiajs/vue3'
import { useAuthStore } from '../stores/authStore'
import { useTripStore } from '../stores/viajeStore'
import { useConductorStore } from '../stores/conductorStore'

const authStore = useAuthStore()
const viajeStore = useTripStore()
const conductorStore = useConductorStore()
const page = usePage()

const isSidebarOpen = ref(true)

onMounted(() => {
  viajeStore.fetchTrips()
  conductorStore.obtenerPerfilConductor()
})

const rutaActual = computed(() => {
  const url = page.url || (typeof window !== 'undefined' ? window.location.pathname : '/')
  return String(url).split('?')[0]
})

const elementosMenu = computed(() => [
  {
    label: 'Dashboard',
    icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    path: '/conductor/dashboard',
    activo: rutaActual.value === '/conductor/dashboard',
  },
  {
    label: 'Mis viajes',
    icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
    path: '/conductor/viajes',
    activo: rutaActual.value.includes('/conductor/viajes'),
  },
  {
    label: 'Ganancias',
    icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    path: '/conductor/ganancias',
    activo: rutaActual.value.includes('/conductor/ganancias'),
  },
  {
    label: 'Perfil',
    icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    path: '/perfil',
    activo: rutaActual.value === '/perfil',
  },
])

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const navigateTo = (path) => {
  inertiaRouter.visit(path)
}

const logout = async () => {
  if (conductorStore.isOnline) {
    await conductorStore.setOnlineStatus(false)
  }
  await authStore.logout()
  inertiaRouter.visit('/')
}
</script>

<template>
  <div class="min-h-screen bg-neutral-soft">
    <aside :class="[
      'fixed left-0 top-0 z-40 h-screen transition-all duration-300 bg-white border-r border-neutral-volcanic shadow-lg',
      isSidebarOpen ? 'w-64' : 'w-20'
    ]">
      <div class="flex items-center justify-between p-4 border-b border-neutral-volcanic h-20">
        <div v-if="isSidebarOpen" class="flex items-center space-x-2">
          <img src="/images/logo.png" alt="LanzaTaxi" class="h-10 w-auto">
          <span class="font-bold text-lanzarote-blue text-lg">LanzaTaxi</span>
        </div>
        <div v-else class="w-full flex justify-center">
          <img src="/images/logo.png" alt="LanzaTaxi" class="h-10 w-auto">
        </div>
        <button @click="toggleSidebar" class="p-1.5 rounded-lg hover:bg-neutral-soft transition-colors">
          <svg class="w-5 h-5 text-neutral-slate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path v-if="isSidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
          </svg>
        </button>
      </div>

      <div class="p-4 border-b border-neutral-volcanic">
        <div class="flex items-center space-x-3">
          <div class="w-12 h-12 rounded-full bg-lanzarote-blue text-white flex items-center justify-center font-bold text-lg">
            {{ authStore.usuario?.name?.charAt(0) }}
          </div>
          <div v-if="isSidebarOpen" class="overflow-hidden">
            <p class="font-semibold text-neutral-dark truncate">{{ authStore.usuario?.name }}</p>
            <p class="text-xs text-neutral-slate">Taxista</p>
            <p class="text-xs mt-1" :class="conductorStore.isOnline ? 'text-green-500' : 'text-neutral-slate'">
              Ã¢â€”Â {{ conductorStore.isOnline ? 'En lÃƒÂ­nea' : 'Desconectado' }}
            </p>
          </div>
        </div>
      </div>

      <nav class="p-4">
        <ul class="space-y-1">
          <li v-for="item in elementosMenu" :key="item.label">
            <button @click="navigateTo(item.path)" :class="[
              'flex items-center space-x-3 p-3 rounded-lg w-full transition-colors',
              item.activo ? 'bg-lanzarote-blue/10 text-lanzarote-blue' : 'text-neutral-dark hover:bg-neutral-soft'
            ]">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
              </svg>
              <span v-if="isSidebarOpen" class="text-sm font-medium">{{ item.label }}</span>
            </button>
          </li>
        </ul>
      </nav>

      <div v-if="isSidebarOpen" class="px-4 mt-2">
        <button @click="conductorStore.toggleOnlineStatus()" :class="[
          'w-full py-2 px-4 rounded-lg font-medium transition-colors flex items-center justify-center space-x-2',
          conductorStore.isOnline ? 'bg-green-500 text-white' : 'bg-neutral-slate text-white'
        ]">
          <span>Ã¢â€”Â</span>
          <span>{{ conductorStore.isOnline ? 'En lÃƒÂ­nea' : 'Desconectado' }}</span>
        </button>
      </div>

      <div class="absolute bottom-0 w-full p-4 border-t border-neutral-volcanic">
        <button @click="logout" class="flex items-center space-x-3 p-3 rounded-lg text-neutral-dark hover:bg-red-50 hover:text-red-600 w-full transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span v-if="isSidebarOpen" class="text-sm font-medium">Cerrar sesiÃƒÂ³n</span>
        </button>
      </div>
    </aside>

    <div :class="['transition-all duration-300', isSidebarOpen ? 'ml-64' : 'ml-20']">
      <header class="bg-white shadow-sm sticky top-0 z-30">
        <div class="flex justify-between items-center px-6 py-4">
          <div>
            <h1 class="text-xl font-semibold text-neutral-dark">Panel Taxista</h1>
            <p class="text-sm text-neutral-slate">{{ new Date().toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
          </div>
          <div class="w-10 h-10 rounded-full bg-lanzarote-blue text-white flex items-center justify-center font-bold">
            {{ authStore.usuario?.name?.charAt(0) }}
          </div>
        </div>
      </header>

      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>


