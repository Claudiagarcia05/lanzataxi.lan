<script setup>
import { ref } from 'vue'
import BarraNavegacion from '../Componentes/BarraNavegacion.vue'
import PieDePagina from '../Componentes/PieDePagina.vue'
import ModalAutenticacion from '../Componentes/Autenticacion/ModalAutenticacion.vue'

const showAuthModal = ref(false)

// Datos de municipios con teléfonos
const municipios = [
  { 
    nombre: 'Arrecife', 
    desc: 'Capital y centro económico',
    telefonos: ['928 800 806', '928 803 104', '928 812 710'],
  },
  { 
    nombre: 'San Bartolomé', 
    desc: 'Corazón geográfico de la isla',
    telefonos: ['928 520 176'],
  },
  { 
    nombre: 'Teguise', 
    desc: 'Historia y tradición',
    telefonos: ['928 524 223'],
  },
  { 
    nombre: 'Tías', 
    desc: 'Turismo y playas',
    telefonos: ['928 524 220'],
  },
  { 
    nombre: 'Yaiza', 
    desc: 'Naturaleza volcánica',
    telefonos: ['928 524 222'],
  },
  { 
    nombre: 'Tinajo', 
    desc: 'Paisajes volcánicos',
    telefonos: ['928 840 049'],
  },
  { 
    nombre: 'Haría', 
    desc: 'Valle de las mil palmeras',
    telefonos: ['928 524 223', '620 315 350'],
  }
]

// Información de interés (SIN ICONOS)
const infoInteres = [
  {
    titulo: 'Cobertura total',
    descripcion: 'Operamos en los 7 municipios de la isla, desde Arrecife hasta el norte en Haría.'
  },
  {
    titulo: 'Tiempo de espera',
    descripcion: 'El 85% de nuestros taxis llegan en menos de 10 minutos.'
  },
  {
    titulo: 'Compromiso ambiental',
    descripcion: 'Flota con vehículos híbridos y eléctricos. Reducimos tu huella de carbono.'
  },
  {
    titulo: 'Para todos',
    descripcion: 'Vehículos adaptados y conductores con formación en accesibilidad.'
  },
  {
    titulo: 'Precios cerrados',
    descripcion: 'Sin sorpresas. Calcula el precio antes de subir al taxi.'
  }
]

// Pasos para reservar
const pasosReserva = [
  {
    numero: '1',
    titulo: 'Solicita tu taxi',
    descripcion: 'Indica dónde te encuentras y tu destino. Elige el tipo de vehículo que necesitas.'
  },
  {
    numero: '2',
    titulo: 'Sigue tu taxi en tiempo real',
    descripcion: 'Una vez asignado, podrás ver en el mapa dónde está tu taxi y el tiempo estimado de llegada.'
  },
  {
    numero: '3',
    titulo: 'Disfruta del viaje',
    descripcion: 'Sube al taxi y viaja tranquilo. Pago seguro sin efectivo si lo prefieres.'
  }
]

// 6 TESTIMONIOS ESTILO IMAGEN (como en la captura)
const testimonios = ref([
  {
    nombre: 'Angie',
    valoracion: 5,
    texto: 'Agradezco mucho que me atendieran un sábado por la noche para asesorarme. Muy amables.',
    tieneMas: false,
    fecha: 'hace 2 días',
    verificado: true
  },
  {
    nombre: 'Carlos M.',
    valoracion: 5,
    texto: 'La app para ver por dónde viene el taxi funciona de lujo. Me salvó para llegar a tiempo a una reunión en Arrecife. Repetiré.',
    tieneMas: false,
    fecha: 'hace 1 semana',
    verificado: true
  },
  {
    nombre: 'María G.',
    valoracion: 4,
    texto: 'Muy buen servicio al aeropuerto. El conductor me ayudó con las maletas, que pesaban un quintal. Un pelín más caro que el bus pero merece la pena por la comodidad.',
    tieneMas: false,
    fecha: 'hace 3 días',
    verificado: true
  },
  {
    nombre: 'Roberto T.',
    valoracion: 5,
    texto: 'Puntuales. Es lo único que pido y siempre cumplen.',
    tieneMas: false,
    fecha: 'hace 5 días',
    verificado: true
  },
  {
    nombre: 'Laura S.',
    valoracion: 5,
    texto: 'Reservé por la web y el proceso es súper sencillo. Coche limpio y trato impecable por parte de David, el conductor.',
    tieneMas: false,
    fecha: 'hace 1 semana',
    verificado: true
  },
  {
    nombre: 'Javi Guerra',
    valoracion: 5,
    texto: 'Grandes profesionales, buen trabajo!!!',
    tieneMas: false,
    fecha: 'hace 2 semanas',
    verificado: true
  }
])

const scrollToPasos = () => {
  const pasos = document.getElementById('como-funciona-section')
  if (pasos) {
    pasos.scrollIntoView({ behavior: 'smooth' })
  }
}
</script>

<template>
  <div class="min-h-screen bg-neutral-soft">
    <BarraNavegacion />

    <main id="main-content">
      <!-- HERO SECTION - FONDO AZUL SIN LOS ICONOS ESTADÍSTICOS -->
      <section class="pt-20 bg-lanzarote-blue text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="grid md:grid-cols-2 gap-12 items-center">
          <!-- Left column -->
          <div>
            <h1 class="text-4xl md:text-5xl font-bold mb-6">
              Tu taxi en <span class="text-lanzarote-yellow">Lanzarote</span>
            </h1>
            <p class="text-xl text-white/90 mb-8 leading-relaxed">
              Reserva tu taxi con seguimiento en tiempo real. Llega a cualquier municipio de la isla de forma segura, rápida y con precios cerrados.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-wrap gap-4">
              <button @click="showAuthModal = true" 
                      class="bg-lanzarote-yellow text-black px-8 py-3 rounded-lg font-semibold hover:bg-opacity-90 transition-all shadow-lg">
                Reservar ahora
              </button>
            </div>
          </div>
          
          <!-- Right column - Información sin iconos -->
          <div class="bg-white/10 backdrop-blur-sm p-8 rounded-2xl">
            <h3 class="text-2xl font-bold mb-4">Por qué LanzaTaxi</h3>
            <div class="space-y-4">
              <div v-for="info in infoInteres" :key="info.titulo" class="border-b border-white/20 pb-3 last:border-0">
                <h4 class="font-semibold">{{ info.titulo }}</h4>
                <p class="text-sm text-white/80">{{ info.descripcion }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CÓMO FUNCIONA SECTION -->
    <section id="como-funciona-section" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold text-neutral-dark mb-4">
            Cómo funciona <span class="text-lanzarote-blue">LanzaTaxi</span>
          </h2>
          <p class="text-xl text-neutral-slate max-w-3xl mx-auto">
            Reservar tu taxi es muy sencillo. Sigue estos 3 pasos y estarás en tu destino.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
          <div v-for="paso in pasosReserva" :key="paso.numero" 
               class="text-center relative">
            <!-- Número grande de fondo -->
            <div class="text-8xl font-bold text-neutral-volcanic/30 absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/4">
              {{ paso.numero }}
            </div>
            
            <!-- Contenido -->
            <div class="relative pt-12">
              <div class="w-20 h-20 bg-lanzarote-blue/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="text-3xl text-lanzarote-blue">{{ paso.numero }}</span>
              </div>
              <h3 class="text-2xl font-bold text-neutral-dark mb-3">{{ paso.titulo }}</h3>
              <p class="text-neutral-slate">{{ paso.descripcion }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- MUNICIPIOS SECTION -->
    <section id="municipios-section" class="py-20 bg-neutral-soft">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold text-neutral-dark mb-4">
            Los 7 municipios de <span class="text-lanzarote-blue">Lanzarote</span>
          </h2>
          <p class="text-xl text-neutral-slate max-w-3xl mx-auto">
            Operamos en toda la isla. Selecciona tu municipio y te recogemos donde estés.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="municipio in municipios" :key="municipio.nombre"
               class="group bg-white rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 border border-neutral-volcanic">
            <div class="h-2 bg-lanzarote-blue"></div>
            <div class="p-8">
              <h3 class="text-2xl font-bold text-neutral-dark mb-2">{{ municipio.nombre }}</h3>
              <p class="text-neutral-slate mb-4">{{ municipio.desc }}</p>
              
              <!-- TELÉFONOS DEL RADIO TAXI -->
              <div>
                <span class="text-sm font-medium text-neutral-dark mb-2 block">Radio taxi</span>
                <div class="space-y-2">
                  <a v-for="telefono in municipio.telefonos" :key="telefono"
                     :href="`tel:${telefono.replace(/\s/g, '')}`"
                     class="flex items-center space-x-2 text-lanzarote-blue hover:text-lanzarote-blue/80 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span class="font-medium">{{ telefono }}</span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- TESTIMONIOS SECTION - FONDO AMARILLO CON 6 OPINIONES ESTILO IMAGEN -->
    <section id="testimonios-section" class="py-20 bg-lanzarote-yellow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col items-center mb-16">
          <div class="flex items-center gap-2 mb-2">
            <span class="font-bold text-neutral-dark text-sm uppercase tracking-widest">EXCELENTE</span>
            <div class="flex text-orange-500 text-lg">
              <template v-for="i in 5">★</template>
            </div>
            <span class="text-neutral-dark font-medium ml-1">18 reseñas</span>
            <img src="https://upload.wikimedia.org/wikipedia/commons/2/2f/Google_2015_logo.svg" alt="Google" class="h-5 ml-1">
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="testimonio in testimonios" :key="testimonio.nombre"
              class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col h-fit">
            
            <div class="flex justify-between items-start mb-1">
              <h4 class="font-bold text-gray-900 text-[15px] leading-tight">{{ testimonio.nombre }}</h4>
              <div class="w-5 h-5 flex-shrink-0">
                <svg viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
              </div>
            </div>

            <div class="flex items-center gap-1 mb-3">
              <div class="flex text-yellow-400 text-[15px]">
                <template v-for="i in 5">★</template>
              </div>
              <div v-if="testimonio.verificado" class="flex items-center justify-center w-4 h-4 rounded-full bg-blue-500">
                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
            </div>

            <div class="text-gray-700 text-[14px] leading-relaxed">
              <p v-if="testimonio.texto">{{ testimonio.texto }}</p>
              <p v-else class="italic text-gray-500 text-xs">Este usuario solo dejó una calificación.</p>
              
              <span v-if="testimonio.tieneMas" class="inline-block mt-2 text-gray-400 text-xs cursor-pointer hover:underline">
                Leer más
              </span>
            </div>
          </div>
        </div>
      </div>
    </section>

      <!-- Auth Modal -->
      <ModalAutenticacion v-model="showAuthModal" />
      <PieDePagina />
    </main>
  </div>
</template>
