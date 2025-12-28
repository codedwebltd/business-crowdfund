<template>
  <form @submit.prevent="save">
    <!-- Referral Commission Rates -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b border-gray-100 bg-gradient-to-r from-purple-500 to-pink-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">Referral Commission Rates</h2>
            <p class="text-purple-100 text-xs sm:text-sm">Set commission percentages for each level</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <!-- Referral Depth -->
        <div>
          <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
            <span>Referral Levels Depth</span>
            <Tooltip text="Number of downline levels that earn commissions. Example: 20 levels means you earn from referrals 20 levels deep." />
          </label>
          <input v-model.number="form.referral_levels_depth" type="number" min="1" max="50" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm" />
        </div>

        <!-- Activation Commission -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            Activation Commission (%)
            <Tooltip text="Percentage earned when downline activates account. Example: Level 1 = 20% of activation fee" />
          </h3>
          <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3">
            <div v-for="level in 20" :key="level" class="flex items-center gap-2">
              <label class="text-xs text-gray-600 w-12">L{{ level }}:</label>
              <input v-model.number="form.commission_rates.activation[level]" type="number" step="0.5" min="0" max="100" class="flex-1 px-2 py-1.5 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-purple-500" />
            </div>
          </div>
        </div>

        <!-- Task Earnings Commission -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            Task Earnings Commission (%)
            <Tooltip text="Percentage earned from downline's task earnings. Usually lower than activation rates." />
          </h3>
          <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3">
            <div v-for="level in 10" :key="level" class="flex items-center gap-2">
              <label class="text-xs text-gray-600 w-12">L{{ level }}:</label>
              <input v-model.number="form.commission_rates.task_earnings[level]" type="number" step="0.5" min="0" max="100" class="flex-1 px-2 py-1.5 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-purple-500" />
            </div>
          </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-purple-500 to-pink-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save Referral Settings' }}
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
  referral_levels_depth: props.settings.referral_levels_depth || 20,
  commission_rates: props.settings.commission_rates || {
    activation: {},
    task_earnings: {}
  }
});

const save = () => {
  saving.value = true;
  router.post('/admin/settings/referral', form, {
    preserveScroll: true,
    onSuccess: () => { saving.value = false; emit('saved'); },
    onError: () => { saving.value = false; }
  });
};
</script>
