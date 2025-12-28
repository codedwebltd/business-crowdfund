<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" @click="close"></div>

      <!-- Modal -->
      <div class="relative w-full max-w-md bg-gradient-to-br from-purple-900/90 to-indigo-900/90 backdrop-blur-xl rounded-2xl shadow-2xl border-2 border-purple-500/30 p-6">
        <!-- Close Button -->
        <button @click="close" class="absolute top-4 right-4 text-gray-400 hover:text-white transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>

        <!-- Header -->
        <div class="text-center mb-6">
          <h3 class="text-2xl font-bold text-white mb-2">Select Payment Method</h3>
          <p class="text-purple-200 text-sm">Choose how you'd like to pay for {{ selectedPlan?.display_name }} plan</p>
          <div class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-purple-500/20 rounded-full border border-purple-400/30">
            <span class="text-purple-200 text-sm">Amount:</span>
            <span class="text-xl font-bold text-white">{{ formatCurrency(selectedPlan?.price) }}</span>
          </div>
        </div>

        <!-- Payment Methods -->
        <div class="space-y-3 mb-6">
          <!-- Bank Transfer -->
          <button
            v-if="settings?.payment_gateways?.bank_transfer?.enabled"
            @click="selectMethod('bank_transfer')"
            :disabled="processing"
            class="w-full p-4 rounded-xl border-2 transition-all duration-300 text-left disabled:opacity-50"
            :class="processing ? 'border-purple-500/30 bg-purple-500/10' : 'border-purple-500/50 bg-purple-500/20 hover:bg-purple-500/30 hover:border-purple-400 hover:scale-[1.02]'"
          >
            <div class="flex items-center gap-4">
              <div class="p-3 bg-green-500/20 rounded-lg">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
              </div>
              <div class="flex-1">
                <h4 class="font-bold text-white">Bank Transfer</h4>
                <p class="text-sm text-purple-200">Pay via bank account transfer</p>
              </div>
              <svg class="w-5 h-5 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </div>
          </button>

          <!-- Bank Transfer Disabled -->
          <div
            v-else
            class="w-full p-4 rounded-xl border-2 border-gray-600/30 bg-gray-800/20 opacity-50 cursor-not-allowed"
          >
            <div class="flex items-center gap-4">
              <div class="p-3 bg-gray-500/20 rounded-lg">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
              </div>
              <div class="flex-1">
                <h4 class="font-bold text-gray-400">Bank Transfer</h4>
                <p class="text-sm text-gray-500">Currently unavailable</p>
              </div>
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </div>
          </div>

          <!-- Crypto Transfer -->
          <button
            v-if="settings?.payment_gateways?.crypto_transfer?.enabled"
            @click="selectMethod('crypto_transfer')"
            :disabled="processing"
            class="w-full p-4 rounded-xl border-2 transition-all duration-300 text-left disabled:opacity-50"
            :class="processing ? 'border-purple-500/30 bg-purple-500/10' : 'border-purple-500/50 bg-purple-500/20 hover:bg-purple-500/30 hover:border-purple-400 hover:scale-[1.02]'"
          >
            <div class="flex items-center gap-4">
              <div class="p-3 bg-orange-500/20 rounded-lg">
                <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div class="flex-1">
                <h4 class="font-bold text-white">Crypto Transfer</h4>
                <p class="text-sm text-purple-200">Pay with cryptocurrency (USDT, BTC, etc.)</p>
              </div>
              <svg class="w-5 h-5 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </div>
          </button>

          <!-- Crypto Transfer Disabled -->
          <div
            v-else
            class="w-full p-4 rounded-xl border-2 border-gray-600/30 bg-gray-800/20 opacity-50 cursor-not-allowed"
          >
            <div class="flex items-center gap-4">
              <div class="p-3 bg-gray-500/20 rounded-lg">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div class="flex-1">
                <h4 class="font-bold text-gray-400">Crypto Transfer</h4>
                <p class="text-sm text-gray-500">Currently unavailable</p>
              </div>
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </div>
          </div>
        </div>

        <!-- Warning if no methods enabled -->
        <div v-if="!settings?.payment_gateways?.bank_transfer?.enabled && !settings?.payment_gateways?.crypto_transfer?.enabled"
          class="p-4 bg-red-500/20 border border-red-500/50 rounded-xl">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div>
              <h4 class="font-bold text-red-300 text-sm mb-1">No Payment Methods Available</h4>
              <p class="text-red-200 text-xs">Please contact support for assistance with payment.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  show: Boolean,
  selectedPlan: Object,
  settings: Object,
  currencySymbol: String,
});

const emit = defineEmits(['close']);

const processing = ref(false);

const formatCurrency = (amount) => {
  return (props.currencySymbol || '₦') + parseFloat(amount).toLocaleString();
};

const selectMethod = (method) => {
  processing.value = true;
  router.post('/payment/initiate', {
    plan_id: props.selectedPlan.id,
    payment_method: method
  }, {
    onFinish: () => processing.value = false,
  });
};

const close = () => {
  if (!processing.value) {
    emit('close');
  }
};
</script>
