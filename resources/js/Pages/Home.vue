<script setup>
import { ref } from 'vue';
import Navbar from '../components/Navbar.vue';
import Footer from '../components/Footer.vue';

const municipios = [
  {
    nombre: 'Arrecife',
    desc: 'Capital y centro economico',
    habitantes: '62.000',
    superficie: '22.7 km²',
    lugares: ['Charco de San Gines', 'Castillo de San Jose', 'Puerto de los Marmoles'],
  },
  {
    nombre: 'San Bartolome',
    desc: 'Corazon geografico de la isla',
    habitantes: '19.000',
    superficie: '40.9 km²',
    lugares: ['Monumento al Campesino', 'Bodegas', 'Campo de golf'],
  },
  {
    nombre: 'Teguise',
    desc: 'Historia y tradicion',
    habitantes: '23.000',
    superficie: '263.9 km²',
    lugares: ['Plaza de la Constitucion', 'Castillo de Santa Barbara', 'Jardin de Cactus'],
  },
  {
    nombre: 'Tias',
    desc: 'Turismo y playas',
    habitantes: '21.000',
    superficie: '64.6 km²',
    lugares: ['Puerto del Carmen', 'Playa Grande', 'Rancho Texas'],
  },
  {
    nombre: 'Yaiza',
    desc: 'Naturaleza volcanica',
    habitantes: '17.000',
    superficie: '211.8 km²',
    lugares: ['Parque Nacional de Timanfaya', 'Playa Blanca', 'Los Hervideros'],
  },
  {
    nombre: 'Tinajo',
    desc: 'Paisajes volcanicos',
    habitantes: '6.500',
    superficie: '135.3 km²',
    lugares: ['Parque Nacional de Timanfaya', 'La Santa', 'Cueva de los Verdes'],
  },
  {
    nombre: 'Haria',
    desc: 'Valle de las mil palmeras',
    habitantes: '5.500',
    superficie: '106.6 km²',
    lugares: ['Mirador del Rio', 'Cueva de los Verdes', 'Jameos del Agua'],
  },
];

const infoInteres = [
  {
    titulo: '🚕 Cobertura total',
    descripcion: 'Operamos en los 7 municipios de la isla, desde Arrecife hasta el norte en Haria.',
  },
  {
    titulo: '⏱️ Tiempo de espera',
    descripcion: 'El 85% de nuestros taxis llegan en menos de 10 minutos.',
  },
  {
    titulo: '🌍 Compromiso ambiental',
    descripcion: 'Flota con vehiculos hibridos y electricos. Reducimos tu huella de carbono.',
  },
  {
    titulo: '👥 Para todos',
    descripcion: 'Vehiculos adaptados y conductores con formacion en accesibilidad.',
  },
  {
    titulo: '💰 Precios cerrados',
    descripcion: 'Sin sorpresas. Calcula el precio antes de subir al taxi.',
  },
  {
    titulo: '📱 App gratuita',
    descripcion: 'Seguimiento en tiempo real y pago desde el movil.',
  },
];

const testimonios = ref([
  {
    nombre: 'Carlos M.',
    texto: 'Excelente servicio. Pude ver en todo momento donde estaba el taxi. Muy puntuales.',
    valoracion: 5,
    municipio: 'Arrecife',
  },
  {
    nombre: 'Ana G.',
    texto: 'Como turista, es un alivio saber que puedo moverme por toda la isla sin problemas.',
    valoracion: 5,
    municipio: 'Puerto del Carmen',
  },
  {
    nombre: 'Roberto T.',
    texto: 'Uso LanzaTaxi a diario para ir al trabajo. Siempre puntuales y profesionales.',
    valoracion: 5,
    municipio: 'Teguise',
  },
]);
</script>

<template>
  <div class="min-h-screen bg-neutral-soft">
    <Navbar />

    <section class="pt-20 bg-lanzarote-blue text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="grid md:grid-cols-2 gap-12 items-center">
          <div>
            <h1 class="text-4xl md:text-5xl font-bold mb-6">
              Tu taxi en <span class="text-lanzarote-yellow">Lanzarote</span>
            </h1>
            <p class="text-xl text-white/90 mb-8 leading-relaxed">
              Reserva tu taxi con seguimiento en tiempo real. Llega a cualquier municipio de la isla de forma segura,
              rapida y con precios cerrados.
            </p>

            <div class="grid grid-cols-2 gap-4 mb-8">
              <div class="flex items-center space-x-2">
                <span class="text-2xl">📍</span>
                <span class="text-sm font-medium">7 municipios</span>
              </div>
              <div class="flex items-center space-x-2">
                <span class="text-2xl">🚖</span>
                <span class="text-sm font-medium">150+ taxis</span>
              </div>
              <div class="flex items-center space-x-2">
                <span class="text-2xl">⏰</span>
                <span class="text-sm font-medium">24/7 disponible</span>
              </div>
              <div class="flex items-center space-x-2">
                <span class="text-2xl">💰</span>
                <span class="text-sm font-medium">Precio cerrado</span>
              </div>
            </div>

            <div class="flex flex-wrap gap-4">
              <button class="bg-lanzarote-yellow text-black px-8 py-3 rounded-lg font-semibold hover:bg-opacity-90 transition-all shadow-lg">
                Reservar ahora
              </button>
              <button class="bg-white/20 backdrop-blur-sm text-white px-8 py-3 rounded-lg font-semibold hover:bg-white/30 transition-all">
                Ver como funciona
              </button>
            </div>
          </div>

          <div class="bg-white/10 backdrop-blur-sm p-8 rounded-2xl">
            <h3 class="text-2xl font-bold mb-4">Por que LanzaTaxi?</h3>
            <div class="space-y-4">
              <div v-for="info in infoInteres.slice(0, 4)" :key="info.titulo" class="flex items-start space-x-3">
                <span class="text-2xl">{{ info.titulo.split(' ')[0] }}</span>
                <div>
                  <h4 class="font-semibold">{{ info.titulo.substring(2) }}</h4>
                  <p class="text-sm text-white/80">{{ info.descripcion }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="municipios-section" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold text-neutral-dark mb-4">
            Los 7 municipios de <span class="text-lanzarote-blue">Lanzarote</span>
          </h2>
          <p class="text-xl text-neutral-slate max-w-3xl mx-auto">
            Operamos en toda la isla. Selecciona tu municipio y te recogemos donde estes.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="municipio in municipios"
            :key="municipio.nombre"
            class="group bg-neutral-soft rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 border border-neutral-volcanic"
          >
            <div class="h-2 bg-lanzarote-blue"></div>

            <div class="p-8">
              <h3 class="text-2xl font-bold text-neutral-dark mb-2">{{ municipio.nombre }}</h3>
              <p class="text-neutral-slate mb-4">{{ municipio.desc }}</p>

              <div class="flex justify-between mb-6 text-sm">
                <div>
                  <span class="text-neutral-slate">Habitantes</span>
                  <p class="font-semibold text-neutral-dark">{{ municipio.habitantes }}</p>
                </div>
                <div>
                  <span class="text-neutral-slate">Superficie</span>
                  <p class="font-semibold text-neutral-dark">{{ municipio.superficie }}</p>
                </div>
              </div>

              <div class="border-t border-neutral-volcanic pt-4">
                <span class="text-sm font-medium text-neutral-dark mb-2 block">Lugares destacados</span>
                <ul class="space-y-1">
                  <li v-for="lugar in municipio.lugares" :key="lugar" class="text-sm text-neutral-slate flex items-center space-x-2">
                    <span class="w-1 h-1 bg-lanzarote-blue rounded-full"></span>
                    <span>{{ lugar }}</span>
                  </li>
                </ul>
              </div>

              <button class="mt-6 text-lanzarote-blue font-semibold flex items-center space-x-2 group-hover:translate-x-2 transition-transform">
                <span>Ver taxis disponibles</span>
                <span>→</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="py-20 bg-neutral-soft">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl md:text-5xl font-bold text-neutral-dark mb-4">
            Lo que dicen nuestros <span class="text-lanzarote-blue">usuarios</span>
          </h2>
          <p class="text-xl text-neutral-slate">Mas de 5000 viajeros confian en nosotros</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div
            v-for="testimonio in testimonios"
            :key="testimonio.nombre"
            class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-md transition-shadow"
          >
            <div class="flex items-center space-x-1 mb-4">
              <span v-for="i in 5" :key="i" class="text-yellow-400 text-xl">★</span>
            </div>
            <p class="text-neutral-dark mb-6 italic">"{{ testimonio.texto }}"</p>
            <div class="flex items-center justify-between">
              <div>
                <p class="font-semibold text-neutral-dark">{{ testimonio.nombre }}</p>
                <p class="text-sm text-neutral-slate">{{ testimonio.municipio }}</p>
              </div>
              <span class="text-2xl">🚕</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="py-20 bg-lanzarote-yellow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold text-black mb-6">Listo para moverte por Lanzarote?</h2>
        <p class="text-xl text-black/80 mb-8 max-w-2xl mx-auto">
          Reserva tu taxi ahora y descubre la forma mas comoda de viajar por la isla.
        </p>
        <button class="bg-black text-white px-8 py-4 rounded-lg font-semibold hover:bg-lanzarote-blue transition-all text-lg shadow-lg">
          Descargar la app
        </button>
      </div>
    </section>

    <Footer />
  </div>
</template>

<style scoped>
section {
  scroll-margin-top: 80px;
}
</style>
