<template>
  <div class="min-h-screen flex items-center justify-center px-4">
    <ModalAutenticacion v-model="show" />
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router as inertiaRouter } from '@inertiajs/vue3'
import ModalAutenticacion from '../../Componentes/Autenticacion/ModalAutenticacion.vue'

const show = ref(true)

watch(show, (isOpen) => {
  if (!isOpen) inertiaRouter.visit('/')
})
</script>
