<template>
  <form @submit.prevent="save">
    <!-- Task Distribution -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-green-500 to-emerald-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">Task Configuration</h2>
            <p class="text-green-100 text-xs sm:text-sm">Configure task types, limits, and validation</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <!-- Task Distribution -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            Task Distribution (%)
            <Tooltip text="Percentage of each task type shown to users. Total must equal 100%." />
          </h3>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div v-for="(value, key) in form.task_distribution_percentages" :key="key">
              <label class="text-xs text-gray-600 mb-1 block capitalize">{{ key }}</label>
              <input v-model.number="form.task_distribution_percentages[key]" type="number" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-green-500" />
            </div>
          </div>
        </div>

        <!-- Daily Task Limits by Plan -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            Daily Task Limits
            <Tooltip text="Maximum tasks per day for each subscription tier." />
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div v-for="(value, key) in form.daily_task_limits" :key="key">
              <label class="text-xs text-gray-600 mb-1 block capitalize">{{ key }} Plan</label>
              <input v-model.number="form.daily_task_limits[key]" type="number" min="1" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-green-500" />
            </div>
          </div>
        </div>

        <!-- Task Validation Rules -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3">Task Validation Rules</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                Survey Min Time (seconds)
                <Tooltip text="Minimum time to complete survey (prevents rushing)" />
              </label>
              <input v-model.number="form.task_validation_rules.survey_min_time" type="number" min="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-green-500" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                Survey Max Time (seconds)
                <Tooltip text="Maximum time allowed for survey completion" />
              </label>
              <input v-model.number="form.task_validation_rules.survey_max_time" type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-green-500" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                Video Min Watch (%)
                <Tooltip text="Minimum percentage of video user must watch" />
              </label>
              <input v-model.number="form.task_validation_rules.video_min_watch_percentage" type="number" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-green-500" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                Sync Min Duration (seconds)
                <Tooltip text="Minimum time for data sync task" />
              </label>
              <input v-model.number="form.task_validation_rules.sync_min_duration" type="number" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-green-500" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                Review Min Characters
                <Tooltip text="Minimum characters required for review tasks" />
              </label>
              <input v-model.number="form.task_validation_rules.review_min_characters" type="number" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-green-500" />
            </div>
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save Task Settings' }}
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
  task_distribution_percentages: props.settings.task_distribution_percentages || {},
  daily_task_limits: props.settings.daily_task_limits || {},
  task_validation_rules: props.settings.task_validation_rules || {}
});

const save = () => {
  saving.value = true;
  router.post('/admin/settings/tasks', form, {
    preserveScroll: true,
    onSuccess: () => { saving.value = false; emit('saved'); },
    onError: () => { saving.value = false; }
  });
};
</script>
