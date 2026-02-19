<script setup>
import { ref } from 'vue'
import AuthModal from './Auth/AuthModal.vue'

const showAuthModal = ref(false)
const isMenuOpen = ref(false)

const scrollToFooter = () => {
  const footer = document.getElementById('contacto-footer')
  if (footer) {
    footer.scrollIntoView({ behavior: 'smooth' })
  }
}

const scrollToMunicipios = () => {
  const municipios = document.getElementById('municipios-section')
  if (municipios) {
    municipios.scrollIntoView({ behavior: 'smooth' })
  }
}

const scrollToTestimonios = () => {
  const testimonios = document.getElementById('testimonios-section')
  if (testimonios) {
    testimonios.scrollIntoView({ behavior: 'smooth' })
  }
}

const scrollToPasos = () => {
  const pasos = document.getElementById('como-funciona-section')
  if (pasos) {
    pasos.scrollIntoView({ behavior: 'smooth' })
  }
}

const goToHome = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>

<template>
  <nav class="bg-white shadow-sm fixed w-full top-0 z-50" aria-label="Principal">
    <a
      href="#main-content"
      class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:bg-white focus:text-neutral-dark focus:px-4 focus:py-2 focus:rounded-lg"
    >
      Saltar al contenido principal
    </a>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-20">
        <button type="button" @click="goToHome" class="flex items-center space-x-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-lanzarote-blue">
          <img src="/images/logo.png" alt="LanzaTaxi" class="h-20 w-auto" cargando="lazy" decoding="async">
        </button>

        <div class="hidden md:flex items-center space-x-8">
          <button type="button" @click="scrollToPasos" class="text-neutral-dark hover:text-lanzarote-blue transition-colors font-medium">
            Como funciona
          </button>
          <button type="button" @click="scrollToMunicipios" class="text-neutral-dark hover:text-lanzarote-blue transition-colors font-medium">
            Municipios
          </button>
          <button type="button" @click="scrollToTestimonios" class="text-neutral-dark hover:text-lanzarote-blue transition-colors font-medium">
            Opiniones
          </button>
          <button type="button" @click="scrollToFooter" class="text-neutral-dark hover:text-lanzarote-blue transition-colors font-medium">
            Contacto
          </button>
          <button
            type="button"
            @click="showAuthModal = true"
            class="bg-lanzarote-blue text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-lanzarote-yellow hover:text-black transition-all shadow-md"
          >
            Login
          </button>
        </div>

        <div class="md:hidden flex items-center">
          <button
            type="button"
            @click="isMenuOpen = !isMenuOpen"
            class="text-neutral-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lanzarote-blue"
            :aria-expanded="isMenuOpen"
            aria-controls="mobile-menu"
            aria-label="Alternar menu"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!isMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <div id="mobile-menu" v-show="isMenuOpen" class="md:hidden py-4 border-t border-neutral-volcanic">
        <div class="flex flex-col space-y-4">
          <button type="button" @click="scrollToPasos; isMenuOpen = false" class="text-neutral-dark hover:text-lanzarote-blue font-medium text-left">
            Como funciona
          </button>
          <button type="button" @click="scrollToMunicipios; isMenuOpen = false" class="text-neutral-dark hover:text-lanzarote-blue font-medium text-left">
            Municipios
          </button>
          <button type="button" @click="scrollToTestimonios; isMenuOpen = false" class="text-neutral-dark hover:text-lanzarote-blue font-medium text-left">
            Opiniones
          </button>
          <button type="button" @click="scrollToFooter; isMenuOpen = false" class="text-neutral-dark hover:text-lanzarote-blue font-medium text-left">
            Contacto
          </button>
          <button
            type="button"
            @click="showAuthModal = true; isMenuOpen = false"
            class="bg-lanzarote-blue text-white px-4 py-2 rounded-lg font-semibold hover:bg-lanzarote-yellow hover:text-black w-full"
          >
            Login
          </button>
        </div>
      </div>
    </div>

    <AuthModal v-model="showAuthModal" />
  </nav>
</template>
