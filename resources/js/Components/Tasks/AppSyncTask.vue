<template>
  <div class="space-y-4">
    <!-- Instructions -->
    <div class="bg-white/5 rounded-xl p-4 border border-white/10">
      <h3 class="text-lg font-bold text-white mb-2">{{ task.task_template.title }}</h3>
      <p class="text-gray-300 text-sm mb-4">{{ task.task_template.description }}</p>

      <!-- App Info if available -->
      <div v-if="appName" class="flex items-center gap-3 bg-blue-500/10 p-3 rounded-lg border border-blue-500/30">
        <svg class="w-8 h-8 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
          <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z"/>
          <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z"/>
          <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z"/>
        </svg>
        <div>
          <p class="text-white font-semibold">{{ appName }}</p>
          <p class="text-gray-400 text-xs">{{ syncType || 'Data Sync Task' }}</p>
        </div>
      </div>
    </div>

    <!-- Sync Status Display -->
    <div class="bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-xl p-6 border border-indigo-500/30">
      <div class="text-center">
        <!-- Timer Circle -->
        <div class="relative inline-flex items-center justify-center mb-4">
          <svg class="w-32 h-32 transform -rotate-90">
            <circle
              cx="64"
              cy="64"
              r="56"
              stroke="currentColor"
              stroke-width="8"
              fill="none"
              class="text-gray-700"
            />
            <circle
              cx="64"
              cy="64"
              r="56"
              stroke="currentColor"
              stroke-width="8"
              fill="none"
              :stroke-dasharray="circumference"
              :stroke-dashoffset="dashOffset"
              class="text-indigo-500 transition-all duration-1000"
              stroke-linecap="round"
            />
          </svg>
          <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center">
              <div class="text-3xl font-bold text-white">{{ remainingTime }}</div>
              <div class="text-xs text-gray-400">seconds</div>
            </div>
          </div>
        </div>

        <!-- Status Messages -->
        <div v-if="syncStatus === 'idle'">
          <h3 class="text-xl font-bold text-white mb-2">Ready to Start Sync</h3>
          <p class="text-gray-300 text-sm">Click the button below to begin</p>
        </div>

        <div v-else-if="syncStatus === 'syncing'">
          <h3 class="text-xl font-bold text-indigo-400 mb-2 flex items-center justify-center gap-2">
            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Syncing Data...
          </h3>
          <p class="text-gray-300 text-sm">Please wait while we sync your data</p>

          <!-- Progress Bar -->
          <div class="mt-4 w-full bg-gray-700 rounded-full h-2 overflow-hidden">
            <div
              class="bg-gradient-to-r from-indigo-500 to-purple-600 h-full transition-all duration-1000"
              :style="{ width: progressPercentage + '%' }"
            ></div>
          </div>
          <p class="text-xs text-gray-400 mt-2">{{ Math.floor(progressPercentage) }}% Complete</p>
        </div>

        <div v-else-if="syncStatus === 'completed'">
          <h3 class="text-xl font-bold text-green-400 mb-2 flex items-center justify-center gap-2">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            Sync Complete!
          </h3>
          <p class="text-gray-300 text-sm">Click below to claim your reward</p>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div v-if="syncStatus === 'idle'">
      <button
        @click="startSync"
        class="w-full py-4 rounded-xl font-bold text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:shadow-xl hover:shadow-indigo-500/50 transition-all flex items-center justify-center gap-2"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        Start Data Sync
      </button>
    </div>

    <div v-else-if="syncStatus === 'completed'">
      <!-- reCAPTCHA (only shows if user has recent fraud incidents) -->
      <div v-if="showRecaptcha" class="bg-gradient-to-br from-orange-500/20 to-red-500/20 rounded-xl p-6 border-2 border-orange-500/50 mb-4">
        <div class="text-center mb-4">
          <h3 class="text-xl font-bold text-orange-400 mb-2">🔒 Security Verification Required</h3>
          <p class="text-sm text-gray-300">Please complete the CAPTCHA below to claim your reward</p>
        </div>
        <div id="recaptcha-container-appsync" class="flex justify-center mb-2"></div>
        <p v-if="recaptchaError" class="text-red-400 text-xs mt-2 text-center animate-pulse">
          ⚠️ Please complete the CAPTCHA verification above
        </p>
      </div>

      <button
        @click="completeTask"
        :disabled="completing || (showRecaptcha && !recaptchaCompleted)"
        :class="[
          'w-full py-4 rounded-xl font-bold text-white transition-all flex items-center justify-center gap-2',
          !completing && (!showRecaptcha || recaptchaCompleted)
            ? 'bg-gradient-to-r from-green-500 to-emerald-600 hover:shadow-xl hover:shadow-green-500/50 cursor-pointer'
            : 'bg-gray-600 cursor-not-allowed opacity-50'
        ]"
      >
        <svg v-if="!completing && (!showRecaptcha || recaptchaCompleted)" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <svg v-else-if="!completing && showRecaptcha && !recaptchaCompleted" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        <svg v-else class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        {{ completing ? 'Submitting...' : (showRecaptcha && !recaptchaCompleted) ? '🔒 Complete CAPTCHA First' : 'Complete & Claim Reward' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import { useDeviceFingerprint } from '@/composables/useDeviceFingerprint';
import { useRecaptcha } from '@/composables/useRecaptcha';
import Swal from 'sweetalert2';

const props = defineProps({
  task: Object
});

const emit = defineEmits(['completed']);
const { getFingerprint, attachToRequest } = useDeviceFingerprint();
const { shouldShow: showRecaptcha, recaptchaError, renderRecaptcha, getToken, reset: resetRecaptcha, recaptchaToken } = useRecaptcha();

const syncStatus = ref('idle'); // idle, syncing, completed
const elapsedTime = ref(0);
const totalDuration = ref(30); // Default 30 seconds
const completing = ref(false);
const startTime = ref(null);
const recaptchaCompleted = ref(false);
let syncInterval = null;

const appName = computed(() => {
  return props.task.task_template.data?.app_name || props.task.task_template.title || 'App Data Sync';
});

const syncType = computed(() => {
  return props.task.task_template.data?.sync_type || 'Background Sync';
});

const remainingTime = computed(() => {
  return Math.max(0, totalDuration.value - elapsedTime.value);
});

const progressPercentage = computed(() => {
  return Math.min(100, (elapsedTime.value / totalDuration.value) * 100);
});

const circumference = computed(() => {
  return 2 * Math.PI * 56; // radius = 56
});

const dashOffset = computed(() => {
  const progress = (elapsedTime.value / totalDuration.value);
  return circumference.value * (1 - progress);
});

onMounted(async () => {
  await getFingerprint();
});

const startSync = () => {
  syncStatus.value = 'syncing';
  elapsedTime.value = 0;
  startTime.value = Date.now();

  // Get duration from task template or use default
  totalDuration.value = props.task.task_template.data?.sync_duration ||
                        props.task.task_template.completion_time_seconds ||
                        30;

  syncInterval = setInterval(() => {
    elapsedTime.value++;

    if (elapsedTime.value >= totalDuration.value) {
      clearInterval(syncInterval);
      syncStatus.value = 'completed';

      // Show completion notification
      Swal.fire({
        icon: 'success',
        title: 'Sync Complete!',
        text: 'Click the button below to claim your reward.',
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#6366f1',
        timer: 2000
      });

      // Render reCAPTCHA immediately if needed
      console.log('[AppSyncTask] Sync complete - checking reCAPTCHA', {
        showRecaptcha: showRecaptcha.value,
        hasRecentFraud: showRecaptcha.value
      });

      if (showRecaptcha.value) {
        console.log('[AppSyncTask] Manually triggering reCAPTCHA render');
        setTimeout(() => {
          console.log('[AppSyncTask] About to call renderRecaptcha');
          renderRecaptcha('recaptcha-container-appsync');
        }, 500);
      } else {
        console.log('[AppSyncTask] NOT rendering reCAPTCHA - showRecaptcha is false');
      }
    }
  }, 1000);
};

const completeTask = async () => {
  completing.value = true;

  // If reCAPTCHA is required, wait for it to render and be completed
  if (showRecaptcha.value) {
    // Make sure reCAPTCHA is rendered
    await renderRecaptcha('recaptcha-container-appsync');

    // Get the token - if null, user hasn't completed it yet
    const token = getToken();
    if (!token) {
      completing.value = false;
      Swal.fire({
        icon: 'warning',
        title: 'CAPTCHA Required',
        text: 'Please complete the CAPTCHA verification below before submitting.',
        background: '#1f2937',
        color: '#fff'
      });
      return;
    }
  }

  const duration = Math.floor((Date.now() - startTime.value) / 1000);

  // Get reCAPTCHA token if triggered by fraud
  const recaptchaToken = showRecaptcha.value ? getToken() : null;

  const requestData = attachToRequest({
    response_data: {
      app_name: appName.value,
      sync_type: syncType.value,
      sync_duration: elapsedTime.value,
      sync_completed: true
    },
    duration: duration,
    recaptcha_token: recaptchaToken
  });

  router.post(`/tasks/${props.task.id}/complete`, requestData, {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: '🎉 Task Completed!',
        html: `<p class="text-lg">Data sync verified!</p><p class="text-lg">You earned <strong class="text-green-400">₦${props.task.reward_amount}</strong></p>`,
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#10b981'
      });
      emit('completed');
    },
    onError: (errors) => {
      completing.value = false;

      const errorMessage = errors?.error || errors?.message || 'Please try again';
      Swal.fire({
        icon: 'error',
        title: 'Submission Failed',
        text: errorMessage,
        background: '#1f2937',
        color: '#fff'
      });
    }
  });
};

onUnmounted(() => {
  if (syncInterval) {
    clearInterval(syncInterval);
  }
});

// Render reCAPTCHA when sync is completed (only if fraud detected)
watch(syncStatus, async (newStatus) => {
  if (newStatus === 'completed' && showRecaptcha.value) {
    await nextTick();
    renderRecaptcha('recaptcha-container-appsync');
  }
});

// Track when reCAPTCHA is completed
watch(recaptchaToken, (newToken) => {
  if (newToken) {
    recaptchaCompleted.value = true;
  }
});
</script>
