<template>
  <form @submit.prevent="save">
    <!-- Token Settings -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-orange-500 to-red-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">Token System</h2>
            <p class="text-orange-100 text-xs sm:text-sm">Control token pricing and fluctuation</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Token Price (₦)</span>
              <Tooltip text="Price per CROW token. Users pay this when withdrawing. Example: ₦850 means 1 CROW = ₦850" />
            </label>
            <input v-model.number="form.token_settings.token_price" type="number" step="0.01" min="0" placeholder="850" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm" />
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Withdrawal Rate</span>
              <Tooltip text="Multiplier applied to withdrawal amount. 0.68 = user gets 68% of requested amount. Controls platform profit." />
            </label>
            <input v-model.number="form.withdrawal_rate" type="number" step="0.01" min="0" max="1" placeholder="0.68" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm" />
          </div>

          <div class="sm:col-span-2">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
              <div>
                <span class="text-sm font-semibold text-gray-900">Enable Fluctuation</span>
                <p class="text-xs text-gray-500 mt-1">Allow token price to change dynamically (like Bitcoin). Discourages withdrawals when rate is low.</p>
              </div>
              <button type="button" @click="form.token_settings.fluctuation_enabled = !form.token_settings.fluctuation_enabled" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.token_settings.fluctuation_enabled ? 'bg-green-500' : 'bg-gray-300']">
                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.token_settings.fluctuation_enabled ? 'translate-x-6' : 'translate-x-1']"></span>
              </button>
            </div>
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-red-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save Token Settings' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Liquidity Settings -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-pink-500 to-rose-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">Liquidity Management</h2>
            <p class="text-pink-100 text-xs sm:text-sm">Platform sustainability thresholds</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Healthy Burn Rate</span>
              <Tooltip text="Maximum healthy ratio of withdrawals to deposits. Below this = green status. Example: 0.7 = withdrawals at 70% of deposits" />
            </label>
            <input v-model.number="form.liquidity_settings.healthy_burn_rate" type="number" step="0.01" min="0" max="1" placeholder="0.7" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-500 text-sm" />
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Caution Burn Rate</span>
              <Tooltip text="Warning threshold. Between healthy and this = yellow status. Example: 0.9 = withdrawals at 90% of deposits" />
            </label>
            <input v-model.number="form.liquidity_settings.caution_burn_rate" type="number" step="0.01" min="0" max="1" placeholder="0.9" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-500 text-sm" />
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Critical Burn Rate</span>
              <Tooltip text="Critical threshold. Above this = red status. Example: 1.2 = withdrawals exceeding deposits by 20%" />
            </label>
            <input v-model.number="form.liquidity_settings.critical_burn_rate" type="number" step="0.01" min="0" placeholder="1.2" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-500 text-sm" />
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-pink-500 to-rose-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save Liquidity Settings' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Balance Maturation -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-violet-500 to-purple-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">Balance Maturation</h2>
            <p class="text-violet-100 text-xs sm:text-sm">Control when earnings become withdrawable</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <div>
          <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
            <span>Maturation Period (Hours)</span>
            <Tooltip text="Hours before pending balance becomes withdrawable. Reduces immediate withdrawal pressure. Example: 72 = 3 days, 48 = 2 days" />
          </label>
          <input v-model.number="form.pending_balance_maturation_hours" type="number" min="0" step="1" placeholder="72" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 text-sm" />
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-violet-500 to-purple-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save Maturation Settings' }}
          </button>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
import Tooltip from '@/Components/Tooltip.vue';

const props = defineProps({ settings: Object });
const emit = defineEmits(['saved']);
const saving = ref(false);

const form = reactive({
  token_settings: props.settings.token_settings || {
    token_price: 850,
    fluctuation_enabled: false
  },
  withdrawal_rate: props.settings.withdrawal_rate || 0.68,
  liquidity_settings: props.settings.liquidity_settings || {
    healthy_burn_rate: 0.7,
    caution_burn_rate: 0.9,
    critical_burn_rate: 1.2
  },
  pending_balance_maturation_hours: props.settings.pending_balance_maturation_hours || 72,
});

const save = () => {
  saving.value = true;
  router.post('/admin/settings/token', form, {
    preserveScroll: true,
    onSuccess: () => { saving.value = false; emit('saved'); },
    onError: () => { saving.value = false; }
  });
};
</script>
