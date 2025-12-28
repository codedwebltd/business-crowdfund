<template>
  <form @submit.prevent="save">
    <!-- AI Task Generation Configuration -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-purple-500 to-indigo-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">AI Task Generation</h2>
            <p class="text-purple-100 text-xs sm:text-sm">Configure AI-powered automatic task generation</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <!-- Enable/Disable AI Generation -->
        <div class="flex items-center justify-between p-4 bg-purple-50 rounded-xl">
          <div class="flex-1">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
              Enable AI Task Generation
              <Tooltip text="Automatically generate task templates using AI when inventory is low" />
            </h3>
            <p class="text-xs text-gray-600 mt-1">Let AI create surveys, video tasks, and reviews automatically</p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" v-model="form.ai_task_generation_enabled" class="sr-only peer">
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
          </label>
        </div>

        <!-- Groq API Configuration -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            Groq API Configuration
            <Tooltip text="Groq provides fast, cost-effective AI generation using Llama models" />
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <label class="text-xs text-gray-600 mb-1 block">API Key</label>
              <input
                v-model="form.ai_configuration.groq_api_key"
                type="text"
                placeholder="gsk_xxxxxxxxxxxxxxxxxxxxxxxx"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500 font-mono"
              />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                Model
                <Tooltip text="llama-3.1-8b-instant is fast and cheap for task generation" />
              </label>
              <select v-model="form.ai_configuration.groq_model" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500">
                <option value="llama-3.1-8b-instant">llama-3.1-8b-instant (Fast)</option>
                <option value="llama-3.1-70b-versatile">llama-3.1-70b-versatile (Better Quality)</option>
                <option value="mixtral-8x7b-32768">mixtral-8x7b-32768 (Long Context)</option>
              </select>
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                API Endpoint
                <Tooltip text="Groq OpenAI-compatible endpoint" />
              </label>
              <input
                v-model="form.ai_configuration.groq_endpoint"
                type="url"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500 font-mono"
              />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                Temperature (0.0 - 1.0)
                <Tooltip text="Higher = more creative, Lower = more predictable. 0.7 is balanced." />
              </label>
              <input
                v-model.number="form.ai_configuration.temperature"
                type="number"
                min="0"
                max="1"
                step="0.1"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500"
              />
            </div>
            <div class="sm:col-span-2">
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                YouTube Data API Key
                <Tooltip text="Google Cloud API key with YouTube Data API v3 enabled for fetching trending videos" />
              </label>
              <input
                v-model="form.ai_configuration.youtube_api_key"
                type="text"
                placeholder="AIzaSyA-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500 font-mono"
              />
            </div>
          </div>
        </div>

        <!-- Max Tokens Per Task Type -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            Max Tokens Per Task Type
            <Tooltip text="Higher tokens = more detailed questions but slower and more expensive" />
          </h3>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Survey Tasks</label>
              <input v-model.number="form.ai_configuration.max_tokens.survey" type="number" min="500" max="4000" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Video Questions</label>
              <input v-model.number="form.ai_configuration.max_tokens.video_questions" type="number" min="300" max="2000" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Product Names</label>
              <input v-model.number="form.ai_configuration.max_tokens.product_names" type="number" min="100" max="1000" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500" />
            </div>
          </div>
        </div>

        <!-- Tasks to Generate Per Run -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            Tasks to Generate Per Run
            <Tooltip text="How many of each task type to create when AI generator runs" />
          </h3>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Surveys</label>
              <input v-model.number="form.ai_configuration.tasks_to_generate.surveys" type="number" min="1" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Videos</label>
              <input v-model.number="form.ai_configuration.tasks_to_generate.videos" type="number" min="1" max="50" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Syncs</label>
              <input v-model.number="form.ai_configuration.tasks_to_generate.syncs" type="number" min="1" max="20" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Reviews</label>
              <input v-model.number="form.ai_configuration.tasks_to_generate.reviews" type="number" min="1" max="50" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500" />
            </div>
          </div>
        </div>

        <!-- Reward Amount Ranges -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            Reward Amount Ranges (₦)
            <Tooltip text="Min and max reward amounts for each task type" />
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Survey Min</label>
              <input v-model.number="form.ai_configuration.reward_ranges.survey.min" type="number" min="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Survey Max</label>
              <input v-model.number="form.ai_configuration.reward_ranges.survey.max" type="number" min="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Video Min</label>
              <input v-model.number="form.ai_configuration.reward_ranges.video.min" type="number" min="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Video Max</label>
              <input v-model.number="form.ai_configuration.reward_ranges.video.max" type="number" min="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Sync Amount</label>
              <input v-model.number="form.ai_configuration.reward_ranges.sync.fixed" type="number" min="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Review Min</label>
              <input v-model.number="form.ai_configuration.reward_ranges.review.min" type="number" min="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Review Max</label>
              <input v-model.number="form.ai_configuration.reward_ranges.review.max" type="number" min="10" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500" />
            </div>
          </div>
        </div>

        <!-- Generation Schedule & Thresholds -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
              Generation Frequency (hours)
              <Tooltip text="How often to check if tasks need to be generated. Default: 168 (weekly)" />
            </label>
            <input
              v-model.number="form.ai_generation_frequency_hours"
              type="number"
              min="1"
              max="720"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500"
            />
            <p class="text-xs text-gray-500 mt-1">{{ frequencyInDays }}</p>
          </div>
          <div>
            <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
              Minimum Task Templates Threshold
              <Tooltip text="Generate new tasks when total active templates fall below this number" />
            </label>
            <input
              v-model.number="form.min_task_templates_threshold"
              type="number"
              min="10"
              max="500"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-purple-500"
            />
          </div>
        </div>

        <!-- Info Banner -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="text-xs text-blue-800">
              <p class="font-semibold mb-1">How AI Task Generation Works:</p>
              <ul class="list-disc list-inside space-y-1 text-blue-700">
                <li>Cron job runs every {{ Math.round(form.ai_generation_frequency_hours / 24) }} days</li>
                <li>Checks if total active task templates < {{ form.min_task_templates_threshold }}</li>
                <li>If yes, generates surveys, videos, and reviews using Groq AI</li>
                <li>Tasks are created with country-specific content ({{ settings.country_of_operation }})</li>
                <li>Admin can review and activate generated tasks before assigning to users</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save AI Settings' }}
          </button>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Tooltip from '@/Components/Tooltip.vue';

const props = defineProps({ settings: Object });
const emit = defineEmits(['saved']);
const saving = ref(false);

const defaultConfig = {
    groq_api_key: '',
    groq_model: 'llama-3.1-8b-instant',
    groq_endpoint: 'https://api.groq.com/openai/v1/chat/completions',
    temperature: 0.7,
    youtube_api_key: '',
    max_tokens: {
      survey: 2000,
      video_questions: 800,
      product_names: 300
    },
    tasks_to_generate: {
      surveys: 20,
      videos: 10,
      syncs: 5,
      reviews: 10
    },
    reward_ranges: {
      survey: { min: 30, max: 100 },
      video: { min: 150, max: 400 },
      sync: { fixed: 200 },
      review: { min: 50, max: 80 }
    }
};

const form = reactive({
  ai_task_generation_enabled: props.settings.ai_task_generation_enabled ?? true,
  ai_configuration: {
    ...defaultConfig,
    ...(props.settings.ai_configuration || {})
  },
  ai_generation_frequency_hours: props.settings.ai_generation_frequency_hours ?? 168,
  min_task_templates_threshold: props.settings.min_task_templates_threshold ?? 50
});

const frequencyInDays = computed(() => {
  const days = Math.round(form.ai_generation_frequency_hours / 24);
  return days === 1 ? '1 day' : `${days} days`;
});

const save = () => {
  saving.value = true;
  router.post('/admin/settings/ai', form, {
    preserveScroll: true,
    onSuccess: () => { saving.value = false; emit('saved'); },
    onError: () => { saving.value = false; }
  });
};
</script>
