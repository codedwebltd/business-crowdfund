<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="close"></div>

        <!-- Modal -->
        <div class="flex min-h-screen items-center justify-center p-4">
          <div class="relative w-full max-w-4xl bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 rounded-2xl shadow-2xl border border-white/20 overflow-hidden">

            <!-- Header -->
            <div class="relative bg-gradient-to-r from-orange-500 to-purple-600 px-6 py-4">
              <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
              <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div :class="[
                    'p-2.5 rounded-xl',
                    task.task_template.category === 'VIDEO' ? 'bg-purple-500/30' :
                    task.task_template.category === 'SURVEY' ? 'bg-blue-500/30' :
                    task.task_template.category === 'APP_SYNC' ? 'bg-green-500/30' :
                    'bg-yellow-500/30'
                  ]">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path v-if="task.task_template.category === 'VIDEO'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                      <path v-if="task.task_template.category === 'VIDEO'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      <path v-if="task.task_template.category === 'SURVEY'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                      <path v-if="task.task_template.category === 'APP_SYNC'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                      <path v-if="task.task_template.category === 'PRODUCT_REVIEW'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-white/80 text-xs font-medium uppercase tracking-wider">{{ task.task_template.category.replace('_', ' ') }}</p>
                    <h2 class="text-white text-xl font-bold">{{ task.task_template.title }}</h2>
                  </div>
                </div>
                <button @click="close" class="text-white/80 hover:text-white transition-colors">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Stats Bar -->
            <div class="bg-white/5 backdrop-blur-xl px-6 py-3 flex items-center justify-between border-b border-white/10">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-500">₦{{ task.reward_amount }}</span>
              </div>
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-bold text-orange-400">{{ timeRemaining }}</span>
              </div>
            </div>

            <!-- Content -->
            <div class="p-6">
              <!-- Video Task -->
              <VideoPlayer v-if="task.task_template.category === 'VIDEO'" :task="task" @completed="handleComplete" />

              <!-- Survey Task -->
              <SurveyForm v-else-if="task.task_template.category === 'SURVEY'" :task="task" @completed="handleComplete" />

              <!-- App Sync Task -->
              <AppSyncTask v-else-if="task.task_template.category === 'APP_SYNC'" :task="task" @completed="handleComplete" />

              <!-- Product Review Task -->
              <ProductReviewForm v-else-if="task.task_template.category === 'PRODUCT_REVIEW'" :task="task" @completed="handleComplete" />
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import VideoPlayer from './Tasks/VideoPlayer.vue';
import SurveyForm from './Tasks/SurveyForm.vue';
import AppSyncTask from './Tasks/AppSyncTask.vue';
import ProductReviewForm from './Tasks/ProductReviewForm.vue';

const props = defineProps({
  show: Boolean,
  task: Object
});

const emit = defineEmits(['close', 'completed']);

const currentTime = ref(Date.now());
let timerInterval = null;

const timeRemaining = computed(() => {
  const expiry = new Date(props.task.expires_at).getTime();
  const diff = expiry - currentTime.value;

  if (diff <= 0) return 'Expired';

  const hours = Math.floor(diff / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((diff % (1000 * 60)) / 1000);

  if (hours > 0) return `${hours}h ${minutes}m ${seconds}s`;
  if (minutes > 0) return `${minutes}m ${seconds}s`;
  return `${seconds}s`;
});

onMounted(() => {
  timerInterval = setInterval(() => {
    currentTime.value = Date.now();
  }, 1000);
});

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval);
});

const close = () => {
  emit('close');
};

const handleComplete = (data) => {
  emit('completed', data);
};
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style>
