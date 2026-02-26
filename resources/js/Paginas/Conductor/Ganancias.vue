<template>
  <DisposicionConductor>
    <div class="max-w-7xl mx-auto">
      <!-- Header con gradiente como en Panel.vue -->
      <div class="bg-gradient-to-r from-lanzarote-blue to-blue-800 rounded-2xl p-8 mb-8 text-white">
        <h1 class="text-3xl font-bold mb-2">Mis Ganancias</h1>
        <p class="text-blue-100">Controla tus ingresos y estadísticas como taxista</p>
      </div>

      <!-- Resumen de ganancias - Cards principales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-neutral-slate text-sm">Hoy</span>
            <span class="text-2xl">📊</span>
          </div>
          <p class="text-3xl font-bold text-neutral-dark">245,50 €</p>
          <p class="text-xs text-green-600 mt-2">+12% vs ayer</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-neutral-slate text-sm">Esta semana</span>
            <span class="text-2xl">📅</span>
          </div>
          <p class="text-3xl font-bold text-neutral-dark">1.850,75 €</p>
          <p class="text-xs text-neutral-slate mt-2">15 viajes</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-neutral-slate text-sm">Este mes</span>
            <span class="text-2xl">📈</span>
          </div>
          <p class="text-3xl font-bold text-neutral-dark">6.320,25 €</p>
          <p class="text-xs text-neutral-slate mt-2">42 viajes</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-neutral-slate text-sm">Total histórico</span>
            <span class="text-2xl">🏆</span>
          </div>
          <p class="text-3xl font-bold text-lanzarote-blue">24.850,00 €</p>
          <p class="text-xs text-neutral-slate mt-2">Desde 2024</p>
        </div>
      </div>

      <!-- Gráfico y filtros (estructura visual) -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Gráfico principal -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-neutral-dark">Evolución de ingresos</h2>
            
            <!-- Selector de período como en el formulario de Panel.vue -->
            <select class="px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue bg-white text-sm">
              <option>Últimos 7 días</option>
              <option>Últimos 30 días</option>
              <option>Últimos 3 meses</option>
              <option>Este año</option>
            </select>
          </div>

          <!-- Placeholder del gráfico -->
          <div class="h-64 bg-neutral-soft rounded-lg flex items-center justify-center">
            <span class="text-neutral-slate">Gráfico de ingresos</span>
          </div>

          <!-- Estadísticas rápidas debajo del gráfico -->
          <div class="grid grid-cols-3 gap-4 mt-6">
            <div class="text-center">
              <p class="text-2xl font-bold text-neutral-dark">18</p>
              <p class="text-xs text-neutral-slate">Viajes</p>
            </div>
            <div class="text-center border-x border-neutral-volcanic">
              <p class="text-2xl font-bold text-neutral-dark">32,5€</p>
              <p class="text-xs text-neutral-slate">Promedio por viaje</p>
            </div>
            <div class="text-center">
              <p class="text-2xl font-bold text-neutral-dark">94%</p>
              <p class="text-xs text-neutral-slate">Tasa de aceptación</p>
            </div>
          </div>
        </div>

        <!-- Resumen de métricas clave (como el mapa en Panel.vue) -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
            <h3 class="font-semibold text-neutral-dark mb-4">Métricas clave</h3>
            
            <div class="space-y-4">
              <!-- Horas conectado -->
              <div class="flex justify-between items-center py-2 border-b border-neutral-volcanic">
                <span class="text-neutral-slate">Horas conectado</span>
                <span class="font-semibold text-neutral-dark">42h</span>
              </div>
              
              <!-- Km recorridos -->
              <div class="flex justify-between items-center py-2 border-b border-neutral-volcanic">
                <span class="text-neutral-slate">Kilómetros</span>
                <span class="font-semibold text-neutral-dark">1.245 km</span>
              </div>
              
              <!-- Ingreso por hora -->
              <div class="flex justify-between items-center py-2 border-b border-neutral-volcanic">
                <span class="text-neutral-slate">Ingreso/hora</span>
                <span class="font-semibold text-green-600">44,50 €</span>
              </div>
              
              <!-- Valoraciones -->
              <div class="flex justify-between items-center py-2 border-b border-neutral-volcanic">
                <span class="text-neutral-slate">Valoración media</span>
                <div class="flex items-center">
                  <span class="font-semibold text-neutral-dark mr-1">4.8</span>
                  <span class="text-yellow-400">★</span>
                </div>
              </div>
              
              <!-- Viajes completados -->
              <div class="flex justify-between items-center py-2">
                <span class="text-neutral-slate">Viajes completados</span>
                <span class="font-semibold text-lanzarote-blue">156</span>
              </div>
            </div>

            <!-- Botón de exportar como en Panel.vue -->
            <button class="w-full mt-6 bg-lanzarote-blue text-white font-medium py-3 px-4 rounded-lg hover:bg-lanzarote-yellow hover:text-black transition-colors">
              Exportar informe
            </button>
          </div>
        </div>
      </div>

      <!-- Tabla de ganancias recientes (como historial en Reservas.vue) -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-lg font-semibold text-neutral-dark">Ganancias recientes</h2>
          <span class="text-sm text-neutral-slate">Últimos 10 viajes</span>
        </div>

        <!-- Filtros rápidos como en Reservas.vue -->
        <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
          <button class="px-4 py-2 bg-lanzarote-blue text-white rounded-lg text-sm whitespace-nowrap">
            Todos
          </button>
          <button class="px-4 py-2 border border-neutral-volcanic rounded-lg text-sm text-neutral-slate hover:bg-neutral-soft whitespace-nowrap">
            Hoy
          </button>
          <button class="px-4 py-2 border border-neutral-volcanic rounded-lg text-sm text-neutral-slate hover:bg-neutral-soft whitespace-nowrap">
            Esta semana
          </button>
          <button class="px-4 py-2 border border-neutral-volcanic rounded-lg text-sm text-neutral-slate hover:bg-neutral-soft whitespace-nowrap">
            Este mes
          </button>
        </div>

        <!-- Tabla de viajes -->
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-neutral-soft rounded-lg">
              <tr>
                <th class="text-left p-4 text-sm font-medium text-neutral-slate">Fecha</th>
                <th class="text-left p-4 text-sm font-medium text-neutral-slate">Origen</th>
                <th class="text-left p-4 text-sm font-medium text-neutral-slate">Destino</th>
                <th class="text-left p-4 text-sm font-medium text-neutral-slate">Distancia</th>
                <th class="text-left p-4 text-sm font-medium text-neutral-slate">Ganancia</th>
                <th class="text-left p-4 text-sm font-medium text-neutral-slate">Método pago</th>
                <th class="text-left p-4 text-sm font-medium text-neutral-slate">Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="i in 5" :key="i" class="border-b border-neutral-volcanic hover:bg-neutral-soft/50">
                <td class="p-4 text-sm text-neutral-dark">15 Mar 2024, 14:30</td>
                <td class="p-4 text-sm text-neutral-dark">Aeropuerto</td>
                <td class="p-4 text-sm text-neutral-dark">Puerto del Carmen</td>
                <td class="p-4 text-sm text-neutral-dark">12.5 km</td>
                <td class="p-4 text-sm font-semibold text-lanzarote-blue">32,50 €</td>
                <td class="p-4 text-sm text-neutral-slate">Tarjeta</td>
                <td class="p-4">
                  <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Completado</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mensaje si no hay resultados (como en Reservas.vue) -->
        <!-- <div v-if="viajes.length === 0" class="text-center py-12">
          <p class="text-neutral-slate">No hay ganancias que mostrar</p>
        </div> -->

        <!-- Paginación -->
        <div class="flex justify-between items-center mt-6">
          <span class="text-sm text-neutral-slate">Mostrando 1-5 de 45 resultados</span>
          <div class="flex gap-2">
            <button class="px-3 py-1 border border-neutral-volcanic rounded-lg text-sm text-neutral-slate hover:bg-neutral-soft" disabled>
              Anterior
            </button>
            <button class="px-3 py-1 bg-lanzarote-blue text-white rounded-lg text-sm">1</button>
            <button class="px-3 py-1 border border-neutral-volcanic rounded-lg text-sm text-neutral-slate hover:bg-neutral-soft">2</button>
            <button class="px-3 py-1 border border-neutral-volcanic rounded-lg text-sm text-neutral-slate hover:bg-neutral-soft">3</button>
            <button class="px-3 py-1 border border-neutral-volcanic rounded-lg text-sm text-neutral-slate hover:bg-neutral-soft">Siguiente</button>
          </div>
        </div>
      </div>

      <!-- Resumen por método de pago (como sección adicional) -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
          <div class="flex items-center gap-3 mb-3">
            <span class="text-2xl">💶</span>
            <h3 class="font-semibold text-neutral-dark">Efectivo</h3>
          </div>
          <p class="text-2xl font-bold text-neutral-dark">1.245,50 €</p>
          <p class="text-sm text-neutral-slate mt-1">32 viajes</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
          <div className="flex items-center gap-3 mb-3">
            <span className="text-2xl">💳</span>
            <h3 className="font-semibold text-neutral-dark">Tarjeta</h3>
          </div>
          <p className="text-2xl font-bold text-neutral-dark">3.450,25 €</p>
          <p className="text-sm text-neutral-slate mt-1">58 viajes</p>
        </div>

        <div className="bg-white rounded-xl shadow-sm p-6">
          <div className="flex items-center gap-3 mb-3">
            <span className="text-2xl">📱</span>
            <h3 className="font-semibold text-neutral-dark">Cartera digital</h3>
          </div>
          <p className="text-2xl font-bold text-neutral-dark">1.624,50 €</p>
          <p className="text-sm text-neutral-slate mt-1">41 viajes</p>
        </div>
      </div>
    </div>
  </DisposicionConductor>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import DisposicionConductor from '../../Disposiciones/DisposicionConductor.vue'
// import { useEarningsStore } from '../../Almacenes/almacenGanancias.js' // Para cuando añadas la funcionalidad

// const earningsStore = useEarningsStore()

// Filtro de período para la tabla (como en Reservas.vue)
const filtroPeriodo = ref('all')

// Aquí irán los datos reales cuando implementes la funcionalidad
// const earnings = computed(() => earningsStore.earnings)

// Funciones placeholder (las implementarás después)
const exportInforme = () => {
  console.log('Exportando informe...')
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

onMounted(() => {
  // Aquí cargarás los datos cuando tengas el store
  // earningsStore.fetchEarnings()
  console.log('Ganancias component mounted')
})
</script>
