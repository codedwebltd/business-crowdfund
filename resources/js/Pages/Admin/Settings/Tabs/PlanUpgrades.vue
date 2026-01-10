<template>
  <form @submit.prevent="save">
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-purple-500 to-pink-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
          </div>
          <div>
            <h2 class="text-lg sm:text-xl font-bold text-white">Plan Upgrade Settings</h2>
            <p class="text-xs sm:text-sm text-purple-100">Configure discount for plan upgrades</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <!-- Discount Percentage -->
        <div>
          <label class="block text-sm font-bold text-gray-700 mb-2">
            Upgrade Discount Percentage
            <span class="text-red-500">*</span>
          </label>
          <p class="text-xs text-gray-500 mb-3">
            Percentage discount applied when users upgrade their plan based on star rating qualification
          </p>

          <div class="flex items-center gap-4">
            <div class="flex-1 max-w-xs">
              <div class="relative">
                <input
                  type="number"
                  v-model.number="form.plan_upgrade_discount_percentage"
                  min="0"
                  max="100"
                  step="0.01"
                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent pr-12"
                  required
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                  <span class="text-gray-500 font-semibold">%</span>
                </div>
              </div>
            </div>

            <!-- Live Preview -->
            <div class="flex-1 p-4 bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200 rounded-xl">
              <p class="text-xs text-purple-600 font-semibold mb-1">Example (Silver Plan)</p>
              <div class="flex items-baseline gap-2">
                <span class="text-lg font-bold text-gray-800">₦{{ formatNumber(calculateDiscountedPrice(25000)) }}</span>
                <span class="text-xs text-gray-500 line-through">₦25,000</span>
                <span class="text-xs font-semibold text-green-600">
                  Save ₦{{ formatNumber(25000 - calculateDiscountedPrice(25000)) }}
                </span>
              </div>
            </div>
          </div>

          <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-xs text-blue-700">
              <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
              </svg>
              Users pay the full plan price with this discount applied, NOT the difference between plans.
            </p>
          </div>
        </div>

        <!-- Upgrade Qualification Rules -->
        <div class="border-t pt-6">
          <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            Star Rating → Qualified Plan
          </h3>

          <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
            <p class="text-xs text-amber-700 flex items-start gap-2">
              <svg class="w-4 h-4 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
              </svg>
              <span><strong>Note:</strong> Star ratings map to plan ORDER, not plan names. You can safely rename plans (e.g., "Platinum" to "Diamond"), but DO NOT change the order field in the plans table or the mapping will break!</span>
            </p>
          </div>

          <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg">
              <div class="flex items-center gap-3">
                <span class="text-2xl">⭐⭐⭐⭐⭐</span>
                <div>
                  <p class="text-sm font-bold text-gray-800">5-Star General</p>
                  <p class="text-xs text-gray-600">8 tasks/week + 9 referrals/week</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm font-bold text-purple-600">Platinum Plan</p>
                <p class="text-xs text-gray-500">₦100,000 → ₦{{ formatNumber(calculateDiscountedPrice(100000)) }}</p>
              </div>
            </div>

            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-orange-50 to-yellow-50 border border-orange-200 rounded-lg">
              <div class="flex items-center gap-3">
                <span class="text-2xl">⭐⭐⭐⭐</span>
                <div>
                  <p class="text-sm font-bold text-gray-800">4-Star Performer</p>
                  <p class="text-xs text-gray-600">6 tasks/week + 5 referrals/week</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm font-bold text-purple-600">Gold Plan</p>
                <p class="text-xs text-gray-500">₦50,000 → ₦{{ formatNumber(calculateDiscountedPrice(50000)) }}</p>
              </div>
            </div>

            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-yellow-50 to-green-50 border border-yellow-200 rounded-lg">
              <div class="flex items-center gap-3">
                <span class="text-2xl">⭐⭐⭐</span>
                <div>
                  <p class="text-sm font-bold text-gray-800">3-Star Active</p>
                  <p class="text-xs text-gray-600">4 tasks/week + 3 referrals/week</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm font-bold text-purple-600">Silver Plan</p>
                <p class="text-xs text-gray-500">₦25,000 → ₦{{ formatNumber(calculateDiscountedPrice(25000)) }}</p>
              </div>
            </div>

            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-lg">
              <div class="flex items-center gap-3">
                <span class="text-2xl">⭐⭐</span>
                <div>
                  <p class="text-sm font-bold text-gray-800">2-Star Starter</p>
                  <p class="text-xs text-gray-600">2 tasks/week + 1 referral/week</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-sm font-bold text-purple-600">Bronze Plan</p>
                <p class="text-xs text-gray-500">₦12,000 → ₦{{ formatNumber(calculateDiscountedPrice(12000)) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 bg-gray-50 border-t flex justify-end gap-3">
        <button
          type="submit"
          :disabled="saving"
          class="px-6 py-2.5 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-purple-500/30 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ saving ? 'Saving...' : 'Save Changes' }}
        </button>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  settings: Object,
});

const form = ref({
  plan_upgrade_discount_percentage: props.settings?.plan_upgrade_discount_percentage || 20,
});

const saving = ref(false);

const calculateDiscountedPrice = (originalPrice) => {
  const discount = form.value.plan_upgrade_discount_percentage || 0;
  return Math.round(originalPrice * (1 - discount / 100));
};

const formatNumber = (num) => {
  return num?.toLocaleString() || '0';
};

const save = () => {
  saving.value = true;
  router.post('/admin/settings/plan-upgrades', form.value, {
    onFinish: () => saving.value = false,
  });
};
</script>
