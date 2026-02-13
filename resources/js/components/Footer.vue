<script setup>
const currentYear = new Date().getFullYear();

const contactInfo = {
  email: 'info@lanzataxi.com',
  phone: '+34 928 123 456',
  address: 'Calle Real, 1 - Arrecife, Lanzarote',
};

const socialLinks = [
  { name: 'Facebook', icon: '📘', url: '#' },
  { name: 'Twitter', icon: '🐦', url: '#' },
  { name: 'Instagram', icon: '📷', url: '#' },
  { name: 'WhatsApp', icon: '📱', url: '#' },
];
</script>

<template>
  <footer id="contacto-footer" class="bg-white border-t border-neutral-volcanic mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="col-span-1 md:col-span-2">
          <div class="flex items-center space-x-2 mb-4">
            <span class="text-3xl">🚕</span>
            <span class="text-2xl font-bold text-lanzarote-blue">LanzaTaxi</span>
          </div>
          <p class="text-neutral-slate mb-4">
            Tu taxi en Lanzarote, cuando y donde lo necesites.
            Seguimiento en tiempo real y precios justos.
          </p>
          <div class="flex space-x-4">
            <a
              v-for="social in socialLinks"
              :key="social.name"
              :href="social.url"
              class="w-10 h-10 bg-neutral-soft rounded-full flex items-center justify-center hover:bg-lanzarote-yellow transition-colors"
            >
              <span class="text-xl">{{ social.icon }}</span>
            </a>
          </div>
        </div>

        <div>
          <h3 class="font-semibold text-neutral-dark mb-4">Enlaces rapidos</h3>
          <ul class="space-y-2">
            <li><a href="#" class="text-neutral-slate hover:text-lanzarote-blue">Sobre nosotros</a></li>
            <li><a href="#" class="text-neutral-slate hover:text-lanzarote-blue">Como funciona</a></li>
            <li><a href="#" class="text-neutral-slate hover:text-lanzarote-blue">Preguntas frecuentes</a></li>
            <li><a href="#" class="text-neutral-slate hover:text-lanzarote-blue">Terminos y condiciones</a></li>
          </ul>
        </div>

        <div>
          <h3 class="font-semibold text-neutral-dark mb-4">Contacto</h3>
          <ul class="space-y-3">
            <li class="flex items-start space-x-2">
              <span class="text-lanzarote-blue">📧</span>
              <span class="text-neutral-slate">{{ contactInfo.email }}</span>
            </li>
            <li class="flex items-start space-x-2">
              <span class="text-lanzarote-blue">📞</span>
              <span class="text-neutral-slate">{{ contactInfo.phone }}</span>
            </li>
            <li class="flex items-start space-x-2">
              <span class="text-lanzarote-blue">📍</span>
              <span class="text-neutral-slate">{{ contactInfo.address }}</span>
            </li>
          </ul>
        </div>
      </div>

      <div class="border-t border-neutral-volcanic mt-8 pt-8 text-center text-neutral-slate text-sm">
        <p>© {{ currentYear }} LanzaTaxi. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>
</template>
