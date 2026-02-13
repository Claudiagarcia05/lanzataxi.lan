<script setup>
import { ref } from 'vue';
import AuthModal from './Auth/AuthModal.vue';

const showAuthModal = ref(false);
const isMenuOpen = ref(false);

const municipios = [
  { nombre: 'Arrecife' },
  { nombre: 'San Bartolome' },
  { nombre: 'Teguise' },
  { nombre: 'Tias' },
  { nombre: 'Yaiza' },
  { nombre: 'Tinajo' },
  { nombre: 'Haria' },
];

const scrollToFooter = () => {
  const footer = document.getElementById('contacto-footer');
  if (footer) {
    footer.scrollIntoView({ behavior: 'smooth' });
  }
};

const scrollToMunicipios = () => {
  const municipiosSection = document.getElementById('municipios-section');
  if (municipiosSection) {
    municipiosSection.scrollIntoView({ behavior: 'smooth' });
  }
};
</script>

<template>
  <nav class="bg-white shadow-sm fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-20">
        <div class="flex items-center space-x-2">
          <span class="text-3xl">🚕</span>
          <span class="text-2xl font-bold text-lanzarote-blue">LanzaTaxi</span>
        </div>

        <div class="hidden md:flex items-center space-x-8">
          <button
            @click="scrollToMunicipios"
            class="text-neutral-dark hover:text-lanzarote-blue transition-colors font-medium"
          >
            Municipios
          </button>

          <button
            @click="scrollToFooter"
            class="text-neutral-dark hover:text-lanzarote-blue transition-colors font-medium"
          >
            Contacto
          </button>

          <button
            @click="showAuthModal = true"
            class="bg-lanzarote-blue text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-lanzarote-yellow hover:text-black transition-all shadow-md"
          >
            Login
          </button>
        </div>

        <div class="md:hidden flex items-center">
          <button @click="isMenuOpen = !isMenuOpen" class="text-neutral-dark">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                v-if="!isMenuOpen"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"
              />
              <path
                v-else
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>
      </div>

      <div v-show="isMenuOpen" class="md:hidden py-4 border-t border-neutral-volcanic">
        <div class="flex flex-col space-y-4">
          <button
            @click="scrollToMunicipios; isMenuOpen = false"
            class="text-neutral-dark hover:text-lanzarote-blue font-medium text-left"
          >
            Municipios
          </button>
          <button
            @click="scrollToFooter; isMenuOpen = false"
            class="text-neutral-dark hover:text-lanzarote-blue font-medium text-left"
          >
            Contacto
          </button>
          <button
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
