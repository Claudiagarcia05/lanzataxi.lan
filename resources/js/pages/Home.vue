<script setup>
import { ref } from 'vue';
import Navbar from '../components/Navbar.vue';
import Footer from '../components/Footer.vue';

const municipios = [
  { nombre: 'Arrecife', desc: 'Capital de la isla', icon: '🏛️', color: '#244194' },
  { nombre: 'San Bartolome', desc: 'Corazon de la isla', icon: '⛰️', color: '#FDD342' },
  { nombre: 'Teguise', desc: 'Historia y cultura', icon: '🏰', color: '#244194' },
  { nombre: 'Tias', desc: 'Puerto del Carmen', icon: '⛱️', color: '#FDD342' },
  { nombre: 'Yaiza', desc: 'Parque Nacional', icon: '🌋', color: '#244194' },
  { nombre: 'Tinajo', desc: 'Volcanes y vino', icon: '🍷', color: '#FDD342' },
  { nombre: 'Haria', desc: 'Valle de las mil palmeras', icon: '🌴', color: '#244194' },
];

const ofertas = ref([
  {
    titulo: '10% descuento en trayectos a playas',
    descripcion: 'Todos los fines de semana',
    icon: '🌊',
    color: 'bg-gradient-to-r from-lanzarote-blue to-blue-600',
  },
  {
    titulo: 'Viaje grupal - 20% off',
    descripcion: 'A partir de 4 personas',
    icon: '👨‍👩‍👧‍👦',
    color: 'bg-gradient-to-r from-lanzarote-yellow to-yellow-400',
  },
  {
    titulo: 'Trayectos nocturnos seguros',
    descripcion: 'Conductoras disponibles',
    icon: '🌙',
    color: 'bg-gradient-to-r from-success-jable to-green-600',
  },
]);

const stats = ref([
  { valor: '5000+', label: 'Viajes realizados', icon: '🚕' },
  { valor: '150+', label: 'Taxis en la isla', icon: '🚖' },
  { valor: '7', label: 'Municipios', icon: '📍' },
  { valor: '24/7', label: 'Disponibilidad', icon: '⏰' },
]);
</script>

<template>
  <div class="min-h-screen bg-neutral-soft">
    <Navbar />

    <section class="pt-20 bg-gradient-to-b from-white to-neutral-soft">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="text-center">
          <h1 class="text-4xl md:text-6xl font-bold text-neutral-dark mb-6">
            Tu taxi en
            <span class="text-lanzarote-blue">Lanzarote</span>
          </h1>
          <p class="text-xl text-neutral-slate mb-8 max-w-2xl mx-auto">
            Reserva tu taxi con seguimiento en tiempo real.
            Llega a cualquier municipio de la isla de forma segura y rapida.
          </p>
          <button
            @click="$refs.municipiosSection?.scrollIntoView({ behavior: 'smooth' })"
            class="bg-lanzarote-yellow text-black px-8 py-4 rounded-lg font-semibold hover:bg-lanzarote-blue hover:text-white transition-all transform hover:scale-105 shadow-lg"
          >
            Ver municipios 🚕
          </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-16">
          <div
            v-for="stat in stats"
            :key="stat.label"
            class="bg-white p-6 rounded-xl shadow-sm text-center hover:shadow-md transition-shadow"
          >
            <span class="text-3xl">{{ stat.icon }}</span>
            <p class="text-2xl font-bold text-lanzarote-blue mt-2">{{ stat.valor }}</p>
            <p class="text-sm text-neutral-slate">{{ stat.label }}</p>
          </div>
        </div>
      </div>
    </section>

    <section id="municipios-section" class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold text-neutral-dark mb-4">
            Los <span class="text-lanzarote-yellow bg-lanzarote-blue px-3 py-1 rounded-lg">7 municipios</span> de Lanzarote
          </h2>
          <p class="text-lg text-neutral-slate max-w-2xl mx-auto">
            Estamos en toda la isla. Selecciona tu municipio y te recogemos donde estes.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div
            v-for="municipio in municipios"
            :key="municipio.nombre"
            class="group relative bg-neutral-soft rounded-2xl p-6 hover:shadow-xl transition-all duration-300 cursor-pointer border-2 border-transparent hover:border-lanzarote-blue"
            :style="{ borderTopColor: municipio.color }"
          >
            <div class="flex items-start space-x-4">
              <span class="text-4xl">{{ municipio.icon }}</span>
              <div>
                <h3 class="text-xl font-bold text-neutral-dark mb-1">{{ municipio.nombre }}</h3>
                <p class="text-neutral-slate text-sm">{{ municipio.desc }}</p>
              </div>
            </div>
            <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
              <span class="text-lanzarote-blue">🚕</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold text-neutral-dark mb-4">
            Ofertas <span class="text-lanzarote-yellow">especiales</span>
          </h2>
          <p class="text-lg text-neutral-slate">Aprovecha nuestros descuentos exclusivos</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div
            v-for="oferta in ofertas"
            :key="oferta.titulo"
            class="rounded-2xl overflow-hidden shadow-lg transform hover:scale-105 transition-all duration-300"
          >
            <div :class="oferta.color" class="p-8 text-white">
              <span class="text-5xl">{{ oferta.icon }}</span>
              <h3 class="text-xl font-bold mt-4">{{ oferta.titulo }}</h3>
              <p class="text-white/90 mt-2">{{ oferta.descripcion }}</p>
              <button class="mt-4 bg-white text-black px-4 py-2 rounded-lg font-semibold hover:bg-opacity-90">
                Reservar
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="py-16 bg-lanzarote-blue text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12 items-center">
          <div>
            <h2 class="text-3xl md:text-4xl font-bold mb-6">
              Por que elegir LanzaTaxi?
            </h2>
            <div class="space-y-4">
              <div class="flex items-start space-x-4">
                <span class="text-2xl bg-white/20 p-2 rounded-lg">📍</span>
                <div>
                  <h3 class="font-semibold text-xl">Seguimiento en tiempo real</h3>
                  <p class="text-white/80">Sabras donde esta tu taxi y cuanto tarda en llegar</p>
                </div>
              </div>
              <div class="flex items-start space-x-4">
                <span class="text-2xl bg-white/20 p-2 rounded-lg">💰</span>
                <div>
                  <h3 class="font-semibold text-xl">Precios justos</h3>
                  <p class="text-white/80">Sin sorpresas, calculadora de precio antes de reservar</p>
                </div>
              </div>
              <div class="flex items-start space-x-4">
                <span class="text-2xl bg-white/20 p-2 rounded-lg">🌍</span>
                <div>
                  <h3 class="font-semibold text-xl">Eco-friendly</h3>
                  <p class="text-white/80">Calculamos tu ahorro de CO2 en cada viaje</p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-white/10 p-8 rounded-2xl">
            <div class="text-6xl mb-4">🚕</div>
            <h3 class="text-2xl font-bold mb-4">Descarga nuestra app</h3>
            <p class="mb-6 text-white/90">Proximamente disponible en iOS y Android</p>
            <div class="flex space-x-4">
              <div class="bg-black/30 px-4 py-2 rounded-lg">App Store</div>
              <div class="bg-black/30 px-4 py-2 rounded-lg">Google Play</div>
            </div>
          </div>
        </div>
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
