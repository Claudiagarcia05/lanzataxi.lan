<script setup>
const currentYear = new Date().getFullYear();

const footerSections = {
  empresa: {
    title: 'Empresa',
    links: [
      { name: 'Sobre nosotros', url: '#' },
      { name: 'Como funciona', url: '#' },
      { name: 'Sala de prensa', url: '#' },
      { name: 'Blog', url: '#' },
      { name: 'Empleo', url: '#' },
    ],
  },
  productos: {
    title: 'Productos',
    links: [
      { name: 'Pasajeros', url: '#' },
      { name: 'Taxistas', url: '#' },
      { name: 'Empresas', url: '#' },
      { name: 'Flotas', url: '#' },
      { name: 'API para desarrolladores', url: '#' },
    ],
  },
  soporte: {
    title: 'Soporte',
    links: [
      { name: 'Ayuda', url: '#' },
      { name: 'Seguridad', url: '#' },
      { name: 'Privacidad', url: '#' },
      { name: 'Terminos', url: '#' },
      { name: 'Accesibilidad', url: '#' },
    ],
  },
  legal: {
    title: 'Legal',
    links: [
      { name: 'Aviso legal', url: '#' },
      { name: 'Politica de cookies', url: '#' },
      { name: 'Proteccion de datos', url: '#' },
      { name: 'Condiciones generales', url: '#' },
      { name: 'Defensa del consumidor', url: '#' },
    ],
  },
};

const socialLinks = [
  { name: 'Facebook', icon: 'f', url: '#' },
  { name: 'Twitter', icon: 't', url: '#' },
  { name: 'Instagram', icon: 'i', url: '#' },
  { name: 'LinkedIn', icon: 'in', url: '#' },
  { name: 'YouTube', icon: 'yt', url: '#' },
];

const contactInfo = {
  email: 'info@lanzataxi.com',
  phone: '+34 928 123 456',
  address: 'Calle Real, 1 - Arrecife, Lanzarote',
};
</script>

<template>
  <footer id="contacto-footer" class="bg-lanzarote-blue text-white mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="mb-12">
        <div class="flex items-center space-x-2 mb-4">
          <span class="text-4xl">🚕</span>
          <span class="text-3xl font-bold">LanzaTaxi</span>
        </div>
        <p class="text-white/80 max-w-2xl">
          La forma mas inteligente de moverte por Lanzarote. Reserva tu taxi con seguimiento en tiempo real y llega a
          cualquier municipio de la isla de forma segura.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <div v-for="section in footerSections" :key="section.title">
          <h3 class="font-semibold text-lg mb-4">{{ section.title }}</h3>
          <ul class="space-y-3">
            <li v-for="link in section.links" :key="link.name">
              <a :href="link.url" class="text-white/70 hover:text-white transition-colors text-sm">
                {{ link.name }}
              </a>
            </li>
          </ul>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pt-8 border-t border-white/20">
        <div>
          <h3 class="font-semibold text-lg mb-4">Contacto</h3>
          <ul class="space-y-3 text-white/80 text-sm">
            <li>{{ contactInfo.email }}</li>
            <li>{{ contactInfo.phone }}</li>
            <li>{{ contactInfo.address }}</li>
          </ul>
        </div>

        <div>
          <h3 class="font-semibold text-lg mb-4">Siguenos</h3>
          <div class="flex space-x-4 mb-6">
            <a
              v-for="social in socialLinks"
              :key="social.name"
              :href="social.url"
              class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-lanzarote-yellow hover:text-black transition-colors text-sm font-bold"
            >
              {{ social.icon }}
            </a>
          </div>

          <h3 class="font-semibold text-lg mb-3">Descarga la app</h3>
          <div class="flex space-x-3">
            <div class="bg-black/30 px-4 py-2 rounded-lg text-sm hover:bg-black/50 cursor-pointer transition-colors">
              App Store
            </div>
            <div class="bg-black/30 px-4 py-2 rounded-lg text-sm hover:bg-black/50 cursor-pointer transition-colors">
              Google Play
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-black/20 py-4">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center text-sm text-white/60">
        <p>© {{ currentYear }} LanzaTaxi. Todos los derechos reservados.</p>
        <div class="flex space-x-6 mt-2 md:mt-0">
          <a href="#" class="hover:text-white transition-colors">Privacidad</a>
          <a href="#" class="hover:text-white transition-colors">Terminos</a>
          <a href="#" class="hover:text-white transition-colors">Cookies</a>
        </div>
      </div>
    </div>
  </footer>
</template>
