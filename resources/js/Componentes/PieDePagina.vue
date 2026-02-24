<script setup>
const currentYear = new Date().getFullYear()

const footerSections = {
  empresa: {
    title: 'Empresa',
    links: [
      { name: 'Sobre nosotros', url: '#' },
      { name: 'Cómo funciona', url: '#' },
      { name: 'Opiniones', url: '#', section: 'testimonios' }
    ]
  },
  productos: {
    title: 'Productos',
    links: [
      { name: 'Pasajeros', url: '#' },
      { name: 'Taxistas', url: '#' },
      { name: 'Empresas', url: '#' },
      { name: 'Flotas', url: '#' }
    ]
  },
  soporte: {
    title: 'Soporte',
    links: [
      { name: 'Ayuda', url: '#' },
      { name: 'Seguridad', url: '#' },
      { name: 'Privacidad', url: '#' },
      { name: 'Términos', url: '#' }
    ]
  },
  legal: {
    title: 'Legal',
    links: [
      { name: 'Aviso legal', url: '#' },
      { name: 'Política de cookies', url: '#' },
      { name: 'Protección de datos', url: '#' },
      { name: 'Condiciones generales', url: '#' }
    ]
  }
}

const scrollToSection = (sectionId) => {
  const element = document.getElementById(sectionId)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}

const contactInfo = {
  email: 'info@lanzataxi.com',
  phone: '+34 928 123 456',
  address: 'Arrecife, Lanzarote'
}
</script>

<template>
  <footer id="contacto-footer" class="bg-lanzarote-blue text-white mt-16" role="contentinfo">
    <!-- Main Footer -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <!-- Logo and description -->
      <div class="mb-12">
        <div class="flex items-center space-x-2 mb-4">
          <span class="text-3xl font-bold">LanzaTaxi</span>
        </div>
        <p class="text-white/80 max-w-2xl">
          La forma más inteligente de moverte por Lanzarote. Reserva tu taxi con seguimiento en tiempo real y llega a cualquier municipio de la isla de forma segura.
        </p>
      </div>

      <!-- Links Grid -->
      <nav class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12" aria-label="Enlaces del sitio">
        <!-- Empresa -->
        <div>
          <h3 class="font-semibold text-lg mb-4">{{ footerSections.empresa.title }}</h3>
          <ul class="space-y-3">
            <li v-for="link in footerSections.empresa.links" :key="link.name">
              <a v-if="link.section" @click.prevent="scrollToSection(link.section)" href="#"
                 class="text-white/70 hover:text-white transition-colors text-sm cursor-pointer">
                {{ link.name }}
              </a>
              <span v-else class="text-white/70 text-sm">{{ link.name }}</span>
            </li>
          </ul>
        </div>

        <!-- Productos -->
        <div>
          <h3 class="font-semibold text-lg mb-4">{{ footerSections.productos.title }}</h3>
          <ul class="space-y-3">
            <li v-for="link in footerSections.productos.links" :key="link.name">
              <span class="text-white/70 text-sm">{{ link.name }}</span>
            </li>
          </ul>
        </div>

        <!-- Soporte -->
        <div>
          <h3 class="font-semibold text-lg mb-4">{{ footerSections.soporte.title }}</h3>
          <ul class="space-y-3">
            <li v-for="link in footerSections.soporte.links" :key="link.name">
              <span class="text-white/70 text-sm">{{ link.name }}</span>
            </li>
          </ul>
        </div>

        <!-- Legal -->
        <div>
          <h3 class="font-semibold text-lg mb-4">{{ footerSections.legal.title }}</h3>
          <ul class="space-y-3">
            <li v-for="link in footerSections.legal.links" :key="link.name">
              <span class="text-white/70 text-sm">{{ link.name }}</span>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Contact and Social -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pt-8 border-t border-white/20">
        <!-- Contact Info -->
        <div>
          <h3 class="font-semibold text-lg mb-4">Contacto</h3>
          <ul class="space-y-3">
            <li class="flex items-center space-x-3">
              <svg class="w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
              <span class="text-white/80">{{ contactInfo.email }}</span>
            </li>
            <li class="flex items-center space-x-3">
              <svg class="w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
              </svg>
              <span class="text-white/80">{{ contactInfo.phone }}</span>
            </li>
            <li class="flex items-center space-x-3">
              <svg class="w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span class="text-white/80">{{ contactInfo.address }}</span>
            </li>
          </ul>
        </div>

        <!-- Social -->
        <div>
          <h3 class="font-semibold text-lg mb-4">Síguenos</h3>
          <div class="flex space-x-4" aria-label="Redes sociales">
            <!-- Facebook -->
            <a href="#" class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-lanzarote-yellow hover:text-black transition-colors" aria-label="Facebook">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/>
              </svg>
            </a>
            <!-- Twitter -->
            <a href="#" class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-lanzarote-yellow hover:text-black transition-colors" aria-label="Twitter">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M23.44 4.83c-.8.37-1.5.38-2.22.02.93-.56.98-.96 1.32-2.02-.88.52-1.86.9-2.9 1.1-.82-.88-2-1.43-3.3-1.43-2.5 0-4.55 2.04-4.55 4.54 0 .36.03.7.1 1.04-3.77-.2-7.12-2-9.36-4.75-.4.67-.6 1.45-.6 2.3 0 1.56.8 2.95 2.03 3.77-.74-.03-1.44-.23-2.05-.57v.06c0 2.2 1.56 4.03 3.64 4.45-.38.1-.77.16-1.2.16-.3 0-.6-.03-.88-.08.6 1.9 2.35 3.28 4.42 3.32-1.62 1.27-3.66 2.03-5.88 2.03-.38 0-.76-.02-1.13-.07 2.1 1.34 4.6 2.13 7.3 2.13 8.75 0 13.53-7.25 13.53-13.53 0-.2 0-.4-.02-.6.93-.67 1.74-1.5 2.38-2.45z"/>
              </svg>
            </a>
            <!-- Instagram -->
            <a href="#" class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center hover:bg-lanzarote-yellow hover:text-black transition-colors" aria-label="Instagram">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.16c3.2 0 3.58.01 4.85.07 3.25.15 4.77 1.69 4.92 4.92.06 1.27.07 1.65.07 4.85 0 3.2-.01 3.58-.07 4.85-.15 3.23-1.66 4.77-4.92 4.92-1.27.06-1.65.07-4.85.07-3.2 0-3.58-.01-4.85-.07-3.26-.15-4.77-1.7-4.92-4.92-.06-1.27-.07-1.65-.07-4.85 0-3.2.01-3.58.07-4.85.15-3.23 1.66-4.77 4.92-4.92 1.27-.06 1.65-.07 4.85-.07zM12 0C8.74 0 8.33.01 7.05.07 2.7.27.27 2.7.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.2 4.35 2.63 6.78 6.98 6.98 1.28.06 1.67.07 4.95.07s3.67-.01 4.95-.07c4.35-.2 6.78-2.63 6.98-6.98.06-1.28.07-1.67.07-4.95s-.01-3.67-.07-4.95c-.2-4.35-2.63-6.78-6.98-6.98C15.67.01 15.26 0 12 0z"/>
                <path d="M12 5.84a6.16 6.16 0 100 12.32 6.16 6.16 0 000-12.32zM12 16a4 4 0 110-8 4 4 0 010 8z"/>
                <circle cx="18.41" cy="5.59" r="1.44"/>
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Bar -->
    <div class="bg-black/20 py-4">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center text-sm text-white/60">
        <p>© {{ currentYear }} LanzaTaxi. Todos los derechos reservados.</p>
        <div class="flex space-x-6 mt-2 md:mt-0">
          <span class="hover:text-white transition-colors cursor-default">Privacidad</span>
          <span class="hover:text-white transition-colors cursor-default">Términos</span>
          <span class="hover:text-white transition-colors cursor-default">Cookies</span>
        </div>
      </div>
    </div>
  </footer>
</template>