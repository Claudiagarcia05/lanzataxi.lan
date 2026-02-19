<template>
  <PasajeroLayout>
    <div class="max-w-3xl mx-auto">
      <!-- Header con saldo -->
      <div class="bg-gradient-to-r from-lanzarote-blue to-lanzarote-yellow rounded-xl shadow-lg p-8 mb-6">
        <div class="text-center">
          <h1 class="text-white text-lg mb-2">Saldo de tu Cartera</h1>
          <div class="text-5xl font-bold text-white">{{ walletBalance.toFixed(2) }}â‚¬</div>
          <p class="text-white/80 text-sm mt-2">Cartera virtual para tus viajes</p>
        </div>
      </div>

      <!-- Botones de acciÃ³n -->
      <div class="grid grid-cols-2 gap-4 mb-6">
        <button 
          @click="showAddFundsModal = true"
          class="bg-lanzarote-blue text-white font-semibold py-3 rounded-lg hover:bg-lanzarote-yellow hover:text-black transition-colors"
        >
          ðŸ’³ AÃ±adir saldo
        </button>
        <button 
          v-if="walletBalance > 0"
          @click="showWithdrawModal = true"
          class="bg-neutral-volcanic text-white font-semibold py-3 rounded-lg hover:bg-neutral-dark transition-colors"
        >
          ðŸ¦ Retirar dinero
        </button>
        <button 
          v-else
          disabled
          class="bg-neutral-soft text-neutral-slate font-semibold py-3 rounded-lg cursor-not-allowed opacity-50"
        >
          ðŸ¦ Retirar dinero
        </button>
      </div>

      <!-- SecciÃ³n de movimientos -->
      <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-neutral-dark mb-4">Ãšltimos movimientos</h2>
        
        <div v-if="loadingTransactions" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-lanzarote-blue"></div>
          <p class="text-neutral-slate mt-2">Cargando movimientos...</p>
        </div>

        <div v-else-if="transactions.length === 0" class="text-center py-12">
          <div class="text-5xl mb-3">ðŸ’¤</div>
          <p class="text-neutral-slate">Sin movimientos aÃºn</p>
          <p class="text-neutral-slate text-sm mt-1">AÃ±ade saldo para comenzar a realizar viajes</p>
        </div>

        <div v-else class="space-y-3">
          <div 
            v-for="transaction in transactions" 
            :key="transaction.id"
            class="flex items-center justify-between p-4 border border-neutral-volcanic rounded-lg hover:bg-neutral-soft transition-colors"
          >
            <div class="flex items-center space-x-4">
              <div class="text-2xl">
                {{ transaction.type === 'income' ? 'ðŸ“¥' : 'ðŸ“¤' }}
              </div>
              <div>
                <p class="font-medium text-neutral-dark">{{ transaction.description }}</p>
                <p class="text-sm text-neutral-slate">{{ formatDate(transaction.created_at) }}</p>
              </div>
            </div>
            <div class="text-right">
              <p :class="[
                'font-bold text-lg',
                transaction.type === 'income' ? 'text-green-600' : 'text-red-600'
              ]">
                {{ transaction.type === 'income' ? '+' : '-' }}{{ Math.abs(transaction.amount).toFixed(2) }}â‚¬
              </p>
              <p class="text-xs text-neutral-slate">Saldo: {{ transaction.balance.toFixed(2) }}â‚¬</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal AÃ±adir saldo -->
      <div v-if="showAddFundsModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg p-8 max-w-md w-full mx-4">
          <h3 class="text-xl font-bold text-neutral-dark mb-4">AÃ±adir saldo a tu cartera</h3>
          
          <div class="mb-4">
            <label class="block text-sm font-medium text-neutral-dark mb-2">Cantidad a aÃ±adir (â‚¬)</label>
            <div class="flex">
              <span class="px-4 py-2 bg-neutral-soft border border-l border-neutral-volcanic rounded-l-lg">â‚¬</span>
              <input 
                v-model.number="addFundsForm.amount" 
                type="number" 
                step="0.01"
                min="0.01"
                max="1000"
                class="flex-1 px-4 py-2 border border-neutral-volcanic rounded-r-lg focus:ring-2 focus:ring-lanzarote-blue"
                placeholder="0.00"
              >
            </div>
            <p class="text-xs text-neutral-slate mt-1">MÃ­nimo: 0.50â‚¬ | MÃ¡ximo: 1000â‚¬</p>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-neutral-dark mb-2">MÃ©todo de pago</label>
            <select 
              v-model="addFundsForm.pagoMethod"
              class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
            >
              <option value="card">ðŸ’³ Tarjeta de crÃ©dito</option>
              <option value="paypal">ðŸ…¿ï¸ PayPal</option>
              <option value="bank_transfer">ðŸ¦ Transferencia bancaria</option>
            </select>
          </div>

          <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
            <p class="text-sm text-blue-700">
              Nueva cartera: <strong>{{ (walletBalance + (addFundsForm.amount || 0)).toFixed(2) }}â‚¬</strong>
            </p>
          </div>

          <div class="flex space-x-3">
            <button 
              @click="addFunds"
              :disabled="!addFundsForm.amount || addFundsForm.amount <= 0 || loadingAddFunds"
              class="flex-1 bg-lanzarote-blue text-white font-semibold py-2 rounded-lg hover:bg-lanzarote-yellow hover:text-black transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="!loadingAddFunds">âœ“ Agregar saldo</span>
              <span v-else>â³ Procesando...</span>
            </button>
            <button 
              @click="showAddFundsModal = false"
              class="flex-1 border border-neutral-volcanic text-neutral-dark font-semibold py-2 rounded-lg hover:bg-neutral-soft transition-colors"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>

      <!-- Modal Retirar dinero -->
      <div v-if="showWithdrawModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg p-8 max-w-md w-full mx-4">
          <h3 class="text-xl font-bold text-neutral-dark mb-4">Retirar dinero</h3>
          
          <div class="mb-4">
            <label class="block text-sm font-medium text-neutral-dark mb-2">Cantidad a retirar (â‚¬)</label>
            <div class="flex">
              <span class="px-4 py-2 bg-neutral-soft border border-l border-neutral-volcanic rounded-l-lg">â‚¬</span>
              <input 
                v-model.number="withdrawForm.amount" 
                type="number" 
                step="0.01"
                min="0.01"
                :max="walletBalance"
                class="flex-1 px-4 py-2 border border-neutral-volcanic rounded-r-lg focus:ring-2 focus:ring-lanzarote-blue"
                placeholder="0.00"
              >
            </div>
            <p class="text-xs text-neutral-slate mt-1">Disponible: {{ walletBalance.toFixed(2) }}â‚¬</p>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-neutral-dark mb-2">Cuenta bancaria</label>
            <select 
              v-model="withdrawForm.bankAccount"
              class="w-full px-4 py-2 border border-neutral-volcanic rounded-lg focus:ring-2 focus:ring-lanzarote-blue"
            >
              <option value="">Selecciona una cuenta...</option>
              <option value="account1">Cuenta principal (****1234)</option>
              <option value="account2">Otra cuenta (****5678)</option>
            </select>
          </div>

          <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 mb-4">
            <p class="text-sm text-orange-700">
              Saldo despuÃ©s: <strong>{{ (walletBalance - (withdrawForm.amount || 0)).toFixed(2) }}â‚¬</strong>
            </p>
          </div>

          <div class="flex space-x-3">
            <button 
              @click="withdrawFunds"
              :disabled="!withdrawForm.amount || !withdrawForm.bankAccount || withdrawForm.amount <= 0 || withdrawForm.amount > walletBalance || loadingWithdraw"
              class="flex-1 bg-neutral-volcanic text-white font-semibold py-2 rounded-lg hover:bg-neutral-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="!loadingWithdraw">âœ“ Retirar</span>
              <span v-else>â³ Procesando...</span>
            </button>
            <button 
              @click="showWithdrawModal = false"
              class="flex-1 border border-neutral-volcanic text-neutral-dark font-semibold py-2 rounded-lg hover:bg-neutral-soft transition-colors"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>
  </PasajeroLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import PasajeroLayout from '../../Layouts/PasajeroLayout.vue'
import { useWalletStore } from '../../stores/walletStore'
import axios from 'axios'

const walletStore = useWalletStore()

const walletBalance = ref(0)
const transactions = ref([])
const loadingTransactions = ref(false)
const loadingAddFunds = ref(false)
const loadingWithdraw = ref(false)

const showAddFundsModal = ref(false)
const showWithdrawModal = ref(false)

const addFundsForm = ref({
  amount: null,
  pagoMethod: 'card'
})

const withdrawForm = ref({
  amount: null,
  bankAccount: ''
})

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const loadWalletData = async () => {
  try {
    loadingTransactions.value = true
    
    // Cargar saldo
    const balanceResponse = await axios.get('/api/wallet/balance')
    walletBalance.value = balanceResponse.data.balance || 0
    
    // Cargar transacciones
    const transactionsResponse = await axios.get('/api/wallet/transactions')
    transactions.value = transactionsResponse.data || []
  } catch (error) {
    console.error('Error al cargar datos de la cartera:', error)
  } finally {
    loadingTransactions.value = false
  }
}

const addFunds = async () => {
  if (!addFundsForm.value.amount || addFundsForm.value.amount <= 0) return
  
  try {
    loadingAddFunds.value = true
    await axios.post('/api/wallet/add', {
      amount: addFundsForm.value.amount,
      pago_method: addFundsForm.value.pagoMethod
    })
    
    // Recargar datos
    await loadWalletData()
    showAddFundsModal.value = false
    addFundsForm.value = { amount: null, pagoMethod: 'card' }
  } catch (error) {
    console.error('Error al aÃ±adir saldo:', error)
  } finally {
    loadingAddFunds.value = false
  }
}

const withdrawFunds = async () => {
  if (!withdrawForm.value.amount || !withdrawForm.value.bankAccount || withdrawForm.value.amount <= 0) return
  
  try {
    loadingWithdraw.value = true
    await axios.post('/api/wallet/withdraw', {
      amount: withdrawForm.value.amount,
      bank_account: withdrawForm.value.bankAccount
    })
    
    // Recargar datos
    await loadWalletData()
    showWithdrawModal.value = false
    withdrawForm.value = { amount: null, bankAccount: '' }
  } catch (error) {
    console.error('Error al retirar dinero:', error)
  } finally {
    loadingWithdraw.value = false
  }
}

onMounted(() => {
  loadWalletData()
})
</script>

