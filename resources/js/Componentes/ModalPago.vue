<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full">
      <h3 class="text-2xl font-bold text-neutral-dark mb-2">Pagar viaje</h3>
      <p class="text-sm text-neutral-slate mb-6">Selecciona tu método de pago preferido</p>

      <!-- Detalles del viaje -->
      <div class="bg-neutral-soft p-4 rounded-lg mb-6">
        <div class="flex justify-between mb-2">
          <span class="text-sm text-neutral-slate">Origen:</span>
          <span class="text-sm font-medium">{{ viaje?.pickup_address }}</span>
        </div>
        <div class="flex justify-between mb-2">
          <span class="text-sm text-neutral-slate">Destino:</span>
          <span class="text-sm font-medium">{{ viaje?.dropoff_address }}</span>
        </div>
        <div class="flex justify-between mb-2">
          <span class="text-sm text-neutral-slate">Distancia:</span>
          <span class="text-sm font-medium">{{ viaje?.distance }} km</span>
        </div>
        <div class="flex justify-between pt-2 border-t border-neutral-volcanic">
          <span class="font-semibold text-neutral-dark">Total:</span>
          <span class="text-xl font-bold text-lanzarote-blue">{{ viaje?.price?.toFixed(2) }} €</span>
        </div>
      </div>

      <!-- Métodos de pago -->
      <div class="space-y-3 mb-6">
        <button 
          v-for="method in pagoMethods" 
          :key="method.value"
          @click="selectedMethod = method.value"
          :class="[
            'w-full flex items-center gap-3 p-4 rounded-lg border-2 transition-all',
            selectedMethod === method.value 
              ? 'border-lanzarote-blue bg-lanzarote-blue/5' 
              : 'border-neutral-volcanic hover:border-lanzarote-blue/30'
          ]"
        >
          <span class="text-2xl">{{ method.icon }}</span>
          <div class="flex-1 text-left">
            <p class="font-medium text-neutral-dark">{{ method.label }}</p>
            <p class="text-xs text-neutral-slate">{{ method.description }}</p>
          </div>
          <span v-if="selectedMethod === method.value" class="text-lanzarote-blue">✓</span>
        </button>
      </div>

      <!-- Detalles adicionales según método -->
      <div v-if="selectedMethod === 'card' || selectedMethod === 'stripe'" class="mb-6 space-y-3">
        <div>
          <label class="block text-sm font-medium text-neutral-slate mb-2">Número de tarjeta</label>
          <input 
            v-model="cardDetails.number"
            type="text"
            placeholder="1234 5678 9012 3456"
            maxlength="19"
            class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
          />
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium text-neutral-slate mb-2">Vencimiento</label>
            <input 
              v-model="cardDetails.expiry"
              type="text"
              placeholder="MM/AA"
              maxlength="5"
              class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-neutral-slate mb-2">CVV</label>
            <input 
              v-model="cardDetails.cvv"
              type="text"
              placeholder="123"
              maxlength="3"
              class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
            />
          </div>
        </div>
      </div>

      <!-- Botones de acción -->
      <div class="flex gap-3">
        <button 
          @click="processpago"
          :disabled="processing"
          class="flex-1 bg-lanzarote-blue text-white font-semibold py-3 rounded-lg hover:bg-lanzarote-yellow hover:text-black transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="!processing">Confirmar pago</span>
          <span v-else class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Procesando...
          </span>
        </button>
        <button 
          @click="$emit('close')"
          :disabled="processing"
          class="px-6 border border-neutral-volcanic py-3 rounded-lg hover:bg-neutral-soft transition-colors disabled:opacity-50"
        >
          Cancelar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
  show: Boolean,
  viaje: Object
})

const emit = defineEmits(['close', 'success'])

const selectedMethod = ref('cash')
const processing = ref(false)

const cardDetails = ref({
  number: '',
  expiry: '',
  cvv: ''
})

const pagoMethods = [
  { value: 'cash', label: 'Efectivo', description: 'Pagar al conductor', icon: '💵' },
  { value: 'card', label: 'Tarjeta', description: 'Débito o crédito', icon: '💳' },
  { value: 'stripe', label: 'stripe', description: 'Pago online seguro', icon: '📁' },
  { value: 'paypal', label: 'PayPal', description: 'Cuenta PayPal', icon: '🔅' }
]

const processpago = async () => {
  processing.value = true
  
  try {
    let response
    
    if (selectedMethod.value === 'stripe') {
      response = await axios.post(`/api/viajes/${props.viaje.id}/pago/stripe`, {
        pago_method_id: 'pm_' + Math.random().toString(36).substr(2, 9),
        amount: props.viaje.price
      })
    } else if (selectedMethod.value === 'paypal') {
      response = await axios.post(`/api/viajes/${props.viaje.id}/pago/paypal`, {
        order_id: 'PAYPAL-' + Math.random().toString(36).substr(2, 9).toUpperCase(),
        amount: props.viaje.price
      })
    } else {
      response = await axios.post(`/api/viajes/${props.viaje.id}/pago`, {
        method: selectedMethod.value,
        amount: props.viaje.price,
        transaction_id: selectedMethod.value === 'card' ? 'CARD-' + Date.now() : null
      })
    }
    
    if (response.data.success !== false) {
      emit('success', response.data)
      emit('close')
    }
  } catch (error) {
    console.error('Error al procesar pago:', error)
    alert('Error al procesar el pago. Inténtalo de nuevo.')
  } finally {
    processing.value = false
  }
}
</script>

