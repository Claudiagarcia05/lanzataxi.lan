<script setup>
import { ref, computed, onMounted } from 'vue'
import { router as inertiaRouter, usePage } from '@inertiajs/vue3'
import { useAuthStore } from '../stores/authStore'
import { useTripStore } from '../stores/viajeStore'
import axios from 'axios'

const authStore = useAuthStore()
const viajeStore = useTripStore()
const page = usePage()

const isSidebarOpen = ref(true)
const showNotifications = ref(false)
const notificaciones = ref([])
const loadingNotifications = ref(false)

const loadNotifications = async () => {
  loadingNotifications.value = true
  try {
    const response = await axios.get('/api/notifications')
    notificaciones.value = response.data
  } catch (error) {
    console.error('Error al cargar notificaciones:', error)
    // Fallback a notificaciones de demostraciÃƒÂ³n
    notificaciones.value = [
      { id: 1, type: 'viaje_accepted', title: 'Viaje aceptado', message: 'Tu viaje ha sido aceptado por un conductor', read_at: null, created_at: new Date().toISOString() },
    ]
  } finally {
    loadingNotifications.value = false
  }
}

onMounted(() => {
  viajeStore.fetchTrips()
  loadNotifications()
  
  // Actualizar notificaciones cada 30 segundos
  setInterval(loadNotifications, 30000)
})

const rutaActual = computed(() => {
  const url = page.url || (typeof window !== 'undefined' ? window.location.pathname : '/')
  return String(url).split('?')[0]
})

const unreadNotifications = computed(() => notificaciones.value.filter(n => !n.read_at).length)

const userAvatar = computed(() => {
  return authStore.usuario?.avatar || null
})

const elementosMenu = computed(() => [
  {
    label: 'Nueva Reserva',
    icon: 'M12 6v6m0 0v6m0-6h6m-6 0H6',
    path: '/dashboard/home',
    activo: rutaActual.value === '/dashboard/home' || rutaActual.value === '/dashboard',
  },
  {
    label: 'Mis Reservas',
    icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
    path: '/dashboard/reservas',
    activo: rutaActual.value.includes('/dashboard/reservas'),
  },
  {

    label: 'Mi Perfil',
    icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    path: '/dashboard/perfil',
    activo: rutaActual.value === '/dashboard/perfil',
  },
])

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const navigateTo = (path) => {
  inertiaRouter.visit(path)
}

const markAsRead = async (id) => {
  try {
    await axios.post(`/api/notifications/${id}/read`)
    const notif = notificaciones.value.find(n => n.id === id)
    if (notif) notif.read_at = new Date().toISOString()
  } catch (error) {
    console.error('Error al marcar notificaciÃƒÂ³n:', error)
  }
}

const logout = async () => {
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
          <div v-if="isSidebarOpen" class="overflow-hidden">
            <p class="font-semibold text-neutral-dark truncate">{{ authStore.usuario?.name }}</p>
            <p class="text-xs text-neutral-slate">Pasajero</p>
          </div>
        </div>
      </div>

      <nav class="p-4">
        <ul class="space-y-1">
          <li v-for="item in elementosMenu" :key="item.label">
            <button
              @click="navigateTo(item.path)"
              :class="[
                'flex items-center space-x-3 p-3 rounded-lg w-full transition-colors',
                item.activo ? 'bg-lanzarote-blue/10 text-lanzarote-blue' : 'text-neutral-dark hover:bg-neutral-soft'
              ]"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
              </svg>
              <span v-if="isSidebarOpen" class="text-sm font-medium">{{ item.label }}</span>
            </button>
          </li>
        </ul>
      </nav>

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
            <h1 class="text-xl font-semibold text-neutral-dark">Panel Pasajero</h1>
            <p class="text-sm text-neutral-slate">{{ new Date().toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
          </div>

          <div class="flex items-center space-x-4">
            <div class="relative">
              <button @click="showNotifications = !showNotifications" class="p-2 rounded-lg hover:bg-neutral-soft relative">
                <svg class="w-5 h-5 text-neutral-slate" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span v-if="unreadNotifications > 0" class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                  {{ unreadNotifications }}
                </span>
              </button>

              <div v-if="showNotifications" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-neutral-volcanic">
                <div class="p-3 border-b border-neutral-volcanic flex justify-between items-center">
                  <h3 class="font-semibold text-neutral-dark">Notificaciones</h3>
                  <span v-if="unreadNotifications > 0" class="text-xs bg-lanzarote-blue text-white px-2 py-1 rounded-full">
                    {{ unreadNotifications }} nuevas
                  </span>
                </div>
                <div class="max-h-96 overflow-y-auto">
                  <div v-if="notificaciones.length === 0" class="p-6 text-center text-neutral-slate">
                    <span class="text-3xl block mb-2">Ã°Å¸â€â€</span>
                    <p class="text-sm">No tienes notificaciones</p>
                  </div>
                  <div v-for="notif in notificaciones" :key="notif.id" @click="markAsRead(notif.id)" :class="['p-3 border-b border-neutral-volcanic last:border-0 cursor-pointer hover:bg-neutral-soft transition-colors', !notif.read_at && 'bg-lanzarote-blue/5']">
                    <div class="flex items-start gap-2">
                      <span v-if="!notif.read_at" class="w-2 h-2 bg-lanzarote-blue rounded-full mt-1.5"></span>
                      <div class="flex-1">
                        <p class="text-sm font-medium text-neutral-dark">{{ notif.title }}</p>
                        <p class="text-xs text-neutral-slate mt-0.5">{{ notif.message }}</p>
                        <p class="text-xs text-neutral-slate/60 mt-1">{{ new Date(notif.created_at).toLocaleString('es-ES', { hour: '2-digit', minute: '2-digit', day: 'numeric', month: 'short' }) }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>


          </div>
        </div>
      </header>

      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>


