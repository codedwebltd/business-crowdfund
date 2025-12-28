<template>
  <div class="max-h-[60vh] overflow-y-auto px-2">
    <!-- Task Description -->
    <div class="bg-white/5 rounded-xl p-4 mb-6 border border-white/10">
      <p class="text-gray-300 text-sm">{{ task.task_template.description }}</p>
      <p class="text-xs text-gray-400 mt-2">
        <span class="font-semibold">⏱️ Estimated Time:</span> {{ task.task_template.completion_time_seconds || 60 }} seconds
      </p>
    </div>

    <!-- Survey Questions -->
    <form @submit.prevent="submitSurvey" class="space-y-6">
      <div v-for="(question, index) in questions" :key="question.id" class="bg-white/5 rounded-xl p-5 border border-white/10">
        <div class="flex items-start gap-3 mb-4">
          <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center font-bold text-white text-sm">
            {{ index + 1 }}
          </div>
          <div class="flex-1">
            <h4 class="text-white font-semibold text-base mb-1">{{ question.text }}</h4>
            <p v-if="question.required" class="text-xs text-orange-400">* Required</p>
          </div>
        </div>

        <!-- Single Choice -->
        <div v-if="question.type === 'single_choice'" class="space-y-2 ml-11">
          <label
            v-for="option in question.options"
            :key="option"
            class="flex items-center gap-3 p-3 rounded-lg border border-white/10 hover:bg-white/5 cursor-pointer transition-all group"
            :class="answers[question.id] === option ? 'bg-blue-500/20 border-blue-500/50' : ''"
          >
            <input
              type="radio"
              :name="`question-${question.id}`"
              :value="option"
              v-model="answers[question.id]"
              class="w-4 h-4 text-blue-500 focus:ring-blue-500 focus:ring-2"
            />
            <span class="text-gray-300 group-hover:text-white transition-colors">{{ option }}</span>
          </label>
        </div>

        <!-- Multiple Choice -->
        <div v-else-if="question.type === 'multiple_choice'" class="space-y-2 ml-11">
          <label
            v-for="option in question.options"
            :key="option"
            class="flex items-center gap-3 p-3 rounded-lg border border-white/10 hover:bg-white/5 cursor-pointer transition-all group"
            :class="(answers[question.id] || []).includes(option) ? 'bg-blue-500/20 border-blue-500/50' : ''"
          >
            <input
              type="checkbox"
              :value="option"
              v-model="answers[question.id]"
              class="w-4 h-4 text-blue-500 rounded focus:ring-blue-500 focus:ring-2"
            />
            <span class="text-gray-300 group-hover:text-white transition-colors">{{ option }}</span>
          </label>
        </div>

        <!-- Text Input -->
        <div v-else-if="question.type === 'text'" class="ml-11">
          <input
            type="text"
            v-model="answers[question.id]"
            :placeholder="question.placeholder || 'Type your answer...'"
            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 transition-all"
          />
        </div>

        <!-- Textarea -->
        <div v-else-if="question.type === 'textarea'" class="ml-11">
          <textarea
            v-model="answers[question.id]"
            :placeholder="question.placeholder || 'Type your answer...'"
            rows="4"
            class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/50 transition-all resize-none"
          ></textarea>
        </div>

        <!-- Rating/Scale questions (frequency, frequency_scale, rating_scale, numerical_scale, likelihood_scale) -->
        <div v-else-if="['rating', 'frequency', 'frequency_scale', 'rating_scale', 'numerical_scale', 'likelihood_scale'].includes(question.type)" class="ml-11">
          <div v-if="question.type === 'rating'" class="space-y-2">
          <div class="flex items-center gap-2">
            <button
              v-for="star in 5"
              :key="star"
              type="button"
              @click="answers[question.id] = star"
              class="focus:outline-none transition-transform hover:scale-110"
            >
              <svg
                class="w-8 h-8"
                :class="(answers[question.id] || 0) >= star ? 'text-yellow-400 fill-yellow-400' : 'text-gray-600'"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
              </svg>
            </button>
            <span v-if="answers[question.id]" class="ml-2 text-sm text-gray-400">{{ answers[question.id] }}/5</span>
          </div>
          </div>

          <!-- All other scale types - render as radio buttons with options -->
          <div v-else class="space-y-2">
            <label
              v-for="option in question.options"
              :key="option"
              class="flex items-center gap-3 p-3 rounded-lg border border-white/10 hover:bg-white/5 cursor-pointer transition-all group"
              :class="answers[question.id] === option ? 'bg-blue-500/20 border-blue-500/50' : ''"
            >
              <input
                type="radio"
                :name="`question-${question.id}`"
                :value="option"
                v-model="answers[question.id]"
                class="w-4 h-4 text-blue-500 focus:ring-blue-500 focus:ring-2"
              />
              <span class="text-gray-300 group-hover:text-white transition-colors">{{ option }}</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="bg-red-500/20 border border-red-500/50 rounded-lg p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p class="text-red-400 text-sm">{{ errorMessage }}</p>
      </div>

      <!-- Progress Counter -->
      <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-xl p-4 mb-4 border border-white/20">
        <div class="flex items-center justify-between">
          <span class="text-sm font-semibold text-gray-300">Progress</span>
          <div class="flex items-center gap-2">
            <span class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500">
              {{ answeredCount }}
            </span>
            <span class="text-lg font-bold text-gray-500">/</span>
            <span class="text-2xl font-black text-white">{{ totalQuestions }}</span>
          </div>
        </div>
        <div class="w-full bg-gray-700 rounded-full h-2 mt-3">
          <div
            class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-300"
            :style="{ width: `${(answeredCount / totalQuestions) * 100}%` }"
          ></div>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="mt-6">
        <button
          type="submit"
          :disabled="isSubmitting || !canSubmit"
          class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-4 rounded-xl font-bold text-lg hover:shadow-xl hover:shadow-blue-500/50 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
        >
          <svg v-if="isSubmitting" class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ isSubmitting ? 'Submitting...' : 'Submit Survey' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const props = defineProps({
  task: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['completed']);

const answers = ref({});
const isSubmitting = ref(false);
const errorMessage = ref('');
const startTime = ref(Date.now());

// Get minimum time: Global settings -> Task template -> Default 30s
const getMinimumTime = () => {
  const globalMin = window.$page?.props?.settings?.task_validation_rules?.survey_min_time;
  const templateMin = props.task.task_template.min_completion_time;
  return ((globalMin || templateMin || 30) * 1000); // Convert to milliseconds
};

const minimumTime = ref(getMinimumTime());

// Parse questions from task template
const questions = computed(() => {
  try {
    const questionsData = props.task.task_template.questions || props.task.task_template.questions_json;
    if (typeof questionsData === 'string') {
      return JSON.parse(questionsData);
    }
    return questionsData || [];
  } catch (e) {
    console.error('Error parsing questions:', e);
    return [];
  }
});

const totalQuestions = computed(() => questions.value.length);

const answeredCount = computed(() => {
  return Object.keys(answers.value).filter(key => {
    const answer = answers.value[key];
    if (Array.isArray(answer)) return answer.length > 0;
    return answer !== null && answer !== undefined && answer !== '';
  }).length;
});

const canSubmit = computed(() => {
  // Check if all required questions are answered
  const requiredQuestions = questions.value.filter(q => q.required !== false);
  return requiredQuestions.every(q => {
    const answer = answers.value[q.id];
    if (Array.isArray(answer)) return answer.length > 0;
    return answer !== null && answer !== undefined && answer !== '';
  });
});

// Initialize answers object with empty values based on question type
onMounted(() => {
  questions.value.forEach(question => {
    if (question.type === 'multiple_choice') {
      answers.value[question.id] = [];
    } else {
      answers.value[question.id] = null;
    }
  });

  // Mark task as started
  markTaskAsStarted();
});

const markTaskAsStarted = async () => {
  try {
    // Use axios instead of Inertia for non-page requests
    await fetch(`/tasks/${props.task.id}/start`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      }
    });
  } catch (error) {
    console.error('Error starting task:', error);
  }
};

const submitSurvey = async () => {
  errorMessage.value = '';

  // Validation: Check minimum time
  const timeSpent = Date.now() - startTime.value;
  if (timeSpent < minimumTime.value) {
    errorMessage.value = `Please take your time to read and answer carefully. Minimum time: ${minimumTime.value / 1000} seconds.`;
    return;
  }

  // Validation: Check all required questions answered
  if (!canSubmit.value) {
    errorMessage.value = 'Please answer all required questions before submitting.';
    return;
  }

  // Validation: Check for suspicious patterns (all same answer)
  const singleChoiceAnswers = questions.value
    .filter(q => q.type === 'single_choice')
    .map(q => answers.value[q.id]);

  if (singleChoiceAnswers.length > 3) {
    const uniqueAnswers = new Set(singleChoiceAnswers);
    if (uniqueAnswers.size === 1) {
      errorMessage.value = 'Please provide varied responses. Selecting the same option for all questions is not allowed.';
      return;
    }
  }

  isSubmitting.value = true;

  try {
    router.post(`/tasks/${props.task.id}/complete`, {
      response_data: {
        answers: answers.value
      },
      duration: Math.floor(timeSpent / 1000) // in seconds
    }, {
      preserveState: true,
      onSuccess: (page) => {
        isSubmitting.value = false;

        // Show success message
        Swal.fire({
          icon: 'success',
          title: '✅ Survey Completed!',
          html: `
            <div class="text-left">
              <p class="mb-3">Congratulations! You've earned <strong class="text-green-400">₦${props.task.reward_amount}</strong>!</p>
              <p class="text-sm text-gray-300">Your reward will be credited to your pending balance and will be available for withdrawal after the maturation period.</p>
            </div>
          `,
          background: '#1f2937',
          color: '#fff',
          confirmButtonColor: '#10b981',
          confirmButtonText: 'Great!',
        }).then(() => {
          emit('completed', { taskId: props.task.id });
        });
      },
      onError: (errors) => {
        isSubmitting.value = false;
        errorMessage.value = errors.message || 'Failed to submit survey. Please try again.';

        // Show error alert
        Swal.fire({
          icon: 'error',
          title: 'Submission Failed',
          text: errorMessage.value,
          background: '#1f2937',
          color: '#fff',
          confirmButtonColor: '#ef4444'
        });
      }
    });
  } catch (error) {
    isSubmitting.value = false;
    errorMessage.value = 'An unexpected error occurred. Please try again.';
    console.error('Survey submission error:', error);
  }
};
</script>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.3);
}
</style>
