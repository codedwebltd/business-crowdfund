<template>
  <form @submit.prevent="save">
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-yellow-500 to-orange-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">Rank System</h2>
            <p class="text-yellow-100 text-xs sm:text-sm">Configure rank criteria and bonuses</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <!-- Rank Criteria -->
        <div v-for="(criteria, rank) in form.rank_criteria" :key="rank">
          <h3 class="text-sm font-bold text-gray-900 mb-3 capitalize">{{ rank }} Rank</h3>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Direct Referrals</label>
              <input v-model.number="criteria.direct_referrals" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Team Size</label>
              <input v-model.number="criteria.team_size" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Monthly Volume</label>
              <input v-model.number="criteria.monthly_volume" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
            </div>
          </div>
        </div>

        <!-- Commission Multipliers -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            Commission Multipliers
            <Tooltip text="Multiply commissions by this amount. 1.05 = 5% bonus on all commissions" />
          </h3>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div v-for="(value, rank) in form.rank_commission_multipliers" :key="rank">
              <label class="text-xs text-gray-600 mb-1 block capitalize">{{ rank }}</label>
              <input v-model.number="form.rank_commission_multipliers[rank]" type="number" step="0.01" min="1" max="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
            </div>
          </div>
        </div>

        <!-- Diamond Leadership Bonus -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <span>Diamond Leadership Bonus</span>
            <Tooltip text="Monthly bonus for Diamond rank users based on team performance" />
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Base Bonus Amount</label>
              <input v-model.number="form.diamond_leadership_bonus.base_amount" type="number" min="0" step="1000" placeholder="50000" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Performance Multiplier</label>
              <input v-model.number="form.diamond_leadership_bonus.performance_multiplier" type="number" step="0.1" min="1" placeholder="1.5" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
            </div>
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-yellow-500 to-orange-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save Rank Settings' }}
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
  rank_criteria: props.settings.rank_criteria || {},
  rank_commission_multipliers: props.settings.rank_commission_multipliers || {},
  diamond_leadership_bonus: props.settings.diamond_leadership_bonus || {
    base_amount: 50000,
    performance_multiplier: 1.5
  }
});

const save = () => {
  saving.value = true;
  router.post('/admin/settings/ranks', form, {
    preserveScroll: true,
    onSuccess: () => { saving.value = false; emit('saved'); },
    onError: () => { saving.value = false; }
  });
};
</script>
