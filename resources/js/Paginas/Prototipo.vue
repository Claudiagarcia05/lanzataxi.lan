<script setup>
import { ref } from 'vue'
const palette = [
  { name: 'Lanzarote Blue', className: 'bg-lanzarote-blue', textClass: 'text-white' },
  { name: 'Lanzarote Yellow', className: 'bg-lanzarote-yellow', textClass: 'text-black' },
  { name: 'Neutral Dark', className: 'bg-neutral-dark', textClass: 'text-white' },
  { name: 'Neutral Soft', className: 'bg-neutral-soft', textClass: 'text-neutral-dark' }
]

</script>

<template>
  <main class="min-h-screen bg-neutral-soft">
    <section class="bg-white">
      <div class="max-w-6xl mx-auto px-6 py-12">
        <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
          <div>
            <p class="text-sm uppercase tracking-wider text-neutral-slate">Design Guide</p>
            <h1 class="text-3xl md:text-4xl font-bold text-neutral-dark">LanzaTaxi UI Kit</h1>
            <p class="text-neutral-slate mt-2 max-w-2xl">
              Guida visual y prototipo de alta fidelidad para validar interfaz, accesibilidad y tono.
            </p>
          </div>
          <div class="flex gap-3">
            <button class="px-4 py-2 rounded-lg bg-lanzarote-blue text-white font-semibold hover:bg-lanzarote-yellow hover:text-black transition-colors">
              Boton principal
            </button>
            <button class="px-4 py-2 rounded-lg border border-neutral-volcanic text-neutral-dark hover:bg-neutral-soft transition-colors">
              Boton secundario
            </button>
          </div>
        </div>
      </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 py-10">
      <h2 class="text-2xl font-semibold text-neutral-dark mb-4">Paleta y tipografia</h2>
      <div class="grid gap-4 md:grid-cols-4">
        <div v-for="item in palette" :key="item.name" class="rounded-xl overflow-hidden shadow-sm">
          <div :class="['h-16', item.className]"></div>
          <div class="bg-white p-4">
            <p class="text-sm font-semibold text-neutral-dark">{{ item.name }}</p>
            <p class="text-xs text-neutral-slate">Uso principal</p>
          </div>
        </div>
      </div>

      <div class="mt-10 grid gap-6 md:grid-cols-2">
        <div class="bg-white rounded-xl shadow-sm p-6">
          <h3 class="text-lg font-semibold text-neutral-dark mb-2">Tipografia</h3>
          <p class="text-4xl font-bold text-neutral-dark">Titulo principal</p>
          <p class="text-xl text-neutral-dark mt-2">Subtitulo y jerarquia</p>
          <p class="text-sm text-neutral-slate mt-3">Texto de apoyo y cuerpo.</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
          <h3 class="text-lg font-semibold text-neutral-dark mb-2">Estados</h3>
          <div class="flex flex-wrap gap-3">
            <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm">Exito</span>
            <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-sm">Aviso</span>
            <span class="px-3 py-1 rounded-full bg-red-100 text-red-800 text-sm">Error</span>
            <span class="px-3 py-1 rounded-full bg-neutral-100 text-neutral-slate text-sm">Neutral</span>
          </div>
        </div>
      </div>
    </section>

    <section class="bg-lanzarote-blue text-white">
      <div class="max-w-6xl mx-auto px-6 py-12">
        <div class="grid gap-8 md:grid-cols-2 md:items-center">
          <div>
            <h2 class="text-3xl font-bold">Prototipo: Hero + CTA</h2>
            <p class="text-white/80 mt-3">
              Vista principal con llamada a la accion y explicacion clara del valor.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
              <button class="px-6 py-3 rounded-lg bg-lanzarote-yellow text-black font-semibold hover:bg-white transition-colors">
                Reservar ahora
              </button>
              <button class="px-6 py-3 rounded-lg bg-white/10 text-white hover:bg-white/20 transition-colors">
                Ver detalles
              </button>
            </div>
          </div>
          <div class="bg-white/10 rounded-2xl p-6">
            <h3 class="text-xl font-semibold">Panel rapido</h3>
            <div class="mt-4 space-y-3">
              <div class="flex items-center justify-between">
                <span class="text-white/80">Tiempo medio</span>
                <span class="font-semibold">8 min</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-white/80">Cobertura</span>
                <span class="font-semibold">7 municipios</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-white/80">Vehiculos activos</span>
                <span class="font-semibold">120</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 py-12">
      <h2 class="text-2xl font-semibold text-neutral-dark mb-6">Componentes clave</h2>
      <div class="grid gap-6 md:grid-cols-3">
        <div class="bg-white rounded-xl shadow-sm p-6">
          <h3 class="font-semibold text-neutral-dark">Tarjeta de info</h3>
          <p class="text-sm text-neutral-slate mt-2">Descripcion corta con foco en claridad.</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
          <h3 class="font-semibold text-neutral-dark">Tabla accesible</h3>
          <div class="mt-3 text-xs text-neutral-slate">Columnas claras y ordenadas.</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
          <h3 class="font-semibold text-neutral-dark">Notificacion</h3>
          <div class="mt-3 text-sm text-green-700 bg-green-100 px-3 py-2 rounded-lg">
            Accion completada con exito.
          </div>
        </div>
      </div>
    </section>

    <section class="max-w-6xl mx-auto px-6 pb-16">
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-semibold text-neutral-dark">Checklist de accesibilidad</h2>
        <ul class="mt-4 space-y-2 text-sm text-neutral-slate">
          <li>Enlaces y botones con foco visible y navegables con teclado.</li>
          <li>Landmarks y ARIA para navegacion por lector de pantalla.</li>
          <li>Mensajes de error con aria-live en formularios.</li>
        </ul>
      </div>
    </section>
  </main>
</template>
