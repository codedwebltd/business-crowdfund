<template>
  <form @submit.prevent="save">
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-yellow-500 to-orange-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">Star Rating System</h2>
            <p class="text-yellow-100 text-xs sm:text-sm">Configure user performance rating and withdrawal priority</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <!-- Info Banner -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-sm text-blue-900">
              <p class="font-semibold mb-1">How Star Rating Works</p>
              <ul class="list-disc list-inside space-y-1 text-xs">
                <li>Users are rated 1-5 stars based on weekly tasks and referrals</li>
                <li>5-star users (Generals 👑) get HIGHEST withdrawal priority</li>
                <li>Ratings decay automatically after inactivity period</li>
                <li>Higher stars = faster withdrawal processing</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Star Rating Requirements -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
            ⭐ Star Rating Requirements
            <span class="text-xs font-normal text-gray-500">(per week)</span>
          </h3>

          <div class="space-y-4">
            <!-- 5 Star -->
            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-300 rounded-xl p-4">
              <div class="flex items-center gap-2 mb-3">
                <span class="text-lg">⭐⭐⭐⭐⭐ 👑</span>
                <h4 class="text-sm font-bold text-gray-900">5-Star General (Highest Priority)</h4>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="text-xs text-gray-700 mb-1 block font-semibold">Tasks Per Week</label>
                  <input
                    v-model.number="form.star_requirements['5_star'].tasks_per_week"
                    type="number"
                    min="0"
                    class="w-full px-3 py-2 border border-yellow-300 rounded-lg text-sm focus:ring-2 focus:ring-yellow-400"
                    placeholder="8"
                  />
                </div>
                <div>
                  <label class="text-xs text-gray-700 mb-1 block font-semibold">Referrals Per Week</label>
                  <input
                    v-model.number="form.star_requirements['5_star'].referrals_per_week"
                    type="number"
                    min="0"
                    class="w-full px-3 py-2 border border-yellow-300 rounded-lg text-sm focus:ring-2 focus:ring-yellow-400"
                    placeholder="9"
                  />
                </div>
              </div>
            </div>

            <!-- 4 Star -->
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
              <div class="flex items-center gap-2 mb-3">
                <span class="text-lg">⭐⭐⭐⭐</span>
                <h4 class="text-sm font-bold text-gray-900">4-Star (High Priority)</h4>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="text-xs text-gray-700 mb-1 block">Tasks Per Week</label>
                  <input
                    v-model.number="form.star_requirements['4_star'].tasks_per_week"
                    type="number"
                    min="0"
                    class="w-full px-3 py-2 border border-orange-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-400"
                    placeholder="6"
                  />
                </div>
                <div>
                  <label class="text-xs text-gray-700 mb-1 block">Referrals Per Week</label>
                  <input
                    v-model.number="form.star_requirements['4_star'].referrals_per_week"
                    type="number"
                    min="0"
                    class="w-full px-3 py-2 border border-orange-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-400"
                    placeholder="5"
                  />
                </div>
              </div>
            </div>

            <!-- 3 Star -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
              <div class="flex items-center gap-2 mb-3">
                <span class="text-lg">⭐⭐⭐</span>
                <h4 class="text-sm font-bold text-gray-900">3-Star (Medium Priority)</h4>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="text-xs text-gray-700 mb-1 block">Tasks Per Week</label>
                  <input
                    v-model.number="form.star_requirements['3_star'].tasks_per_week"
                    type="number"
                    min="0"
                    class="w-full px-3 py-2 border border-yellow-200 rounded-lg text-sm focus:ring-2 focus:ring-yellow-400"
                    placeholder="4"
                  />
                </div>
                <div>
                  <label class="text-xs text-gray-700 mb-1 block">Referrals Per Week</label>
                  <input
                    v-model.number="form.star_requirements['3_star'].referrals_per_week"
                    type="number"
                    min="0"
                    class="w-full px-3 py-2 border border-yellow-200 rounded-lg text-sm focus:ring-2 focus:ring-yellow-400"
                    placeholder="3"
                  />
                </div>
              </div>
            </div>

            <!-- 2 Star -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
              <div class="flex items-center gap-2 mb-3">
                <span class="text-lg">⭐⭐</span>
                <h4 class="text-sm font-bold text-gray-900">2-Star (Low Priority)</h4>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="text-xs text-gray-700 mb-1 block">Tasks Per Week</label>
                  <input
                    v-model.number="form.star_requirements['2_star'].tasks_per_week"
                    type="number"
                    min="0"
                    class="w-full px-3 py-2 border border-blue-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-400"
                    placeholder="2"
                  />
                </div>
                <div>
                  <label class="text-xs text-gray-700 mb-1 block">Referrals Per Week</label>
                  <input
                    v-model.number="form.star_requirements['2_star'].referrals_per_week"
                    type="number"
                    min="0"
                    class="w-full px-3 py-2 border border-blue-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-400"
                    placeholder="1"
                  />
                </div>
              </div>
            </div>

            <!-- 1 Star -->
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
              <div class="flex items-center gap-2 mb-3">
                <span class="text-lg">⭐</span>
                <h4 class="text-sm font-bold text-gray-900">1-Star (Default/Minimum)</h4>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="text-xs text-gray-700 mb-1 block">Tasks Per Week</label>
                  <input
                    v-model.number="form.star_requirements['1_star'].tasks_per_week"
                    type="number"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-gray-400"
                    placeholder="0"
                  />
                </div>
                <div>
                  <label class="text-xs text-gray-700 mb-1 block">Referrals Per Week</label>
                  <input
                    v-model.number="form.star_requirements['1_star'].referrals_per_week"
                    type="number"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-gray-400"
                    placeholder="0"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Decay Settings -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            ⏱️ Star Rating Decay
          </h3>
          <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="text-xs text-gray-700 mb-1 block font-semibold">Decay After (Days)</label>
                <input
                  v-model.number="form.star_requirements.decay_days"
                  type="number"
                  min="1"
                  max="30"
                  class="w-full px-3 py-2 border border-red-200 rounded-lg text-sm focus:ring-2 focus:ring-red-400"
                  placeholder="4"
                />
                <p class="text-xs text-gray-500 mt-1">Users lose 1 star every X days of inactivity</p>
              </div>
              <div class="flex items-center">
                <div class="bg-white rounded-lg p-3 border border-red-200">
                  <p class="text-xs text-gray-600 mb-1">Example:</p>
                  <p class="text-xs text-gray-800">
                    <strong>Day 0:</strong> User is 5⭐<br>
                    <strong>Day {{ form.star_requirements.decay_days || 4 }}:</strong> Drops to 4⭐<br>
                    <strong>Day {{ (form.star_requirements.decay_days || 4) * 2 }}:</strong> Drops to 3⭐<br>
                    <span class="text-gray-500">...and so on</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end pt-4 border-t">
          <button
            type="submit"
            :disabled="saving"
            class="px-6 py-2.5 bg-gradient-to-r from-yellow-500 to-orange-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ saving ? 'Saving...' : 'Save Star Rating Settings' }}
          </button>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({ settings: Object });
const emit = defineEmits(['saved']);
const saving = ref(false);

const form = reactive({
  star_requirements: props.settings.star_requirements || {
    '5_star': { tasks_per_week: 8, referrals_per_week: 9 },
    '4_star': { tasks_per_week: 6, referrals_per_week: 5 },
    '3_star': { tasks_per_week: 4, referrals_per_week: 3 },
    '2_star': { tasks_per_week: 2, referrals_per_week: 1 },
    '1_star': { tasks_per_week: 0, referrals_per_week: 0 },
    decay_days: 4
  }
});

const save = () => {
  saving.value = true;
  router.post('/admin/settings/star-ratings', form, {
    preserveScroll: true,
    onSuccess: () => { saving.value = false; emit('saved'); },
    onError: () => { saving.value = false; }
  });
};
</script>
