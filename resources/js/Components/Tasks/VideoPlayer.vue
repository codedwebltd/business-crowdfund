<template>
  <div class="space-y-4">
    <!-- Description -->
    <div class="bg-white/5 rounded-xl p-4 border border-white/10">
      <p class="text-gray-300 text-sm">{{ task.task_template.description }}</p>
    </div>

    <!-- Video Player -->
    <div class="relative bg-black rounded-xl overflow-hidden aspect-video">
      <div ref="playerContainer" class="w-full h-full"></div>

      <!-- Transparent Click Blocker - Prevents ALL video interactions -->
      <div
        v-if="!showPlayButton && !videoEnded"
        class="absolute inset-0 cursor-not-allowed select-none"
        style="
          z-index: 10;
          pointer-events: auto;
          user-select: none;
          -webkit-user-select: none;
          -moz-user-select: none;
          -ms-user-select: none;
          background: rgba(0, 0, 0, 0.01);
          touch-action: none;
        "
        @click.prevent.stop
        @mousedown.prevent.stop
        @mouseup.prevent.stop
        @dblclick.prevent.stop
        @contextmenu.prevent.stop
        @drag.prevent.stop
        @dragstart.prevent.stop
        @touchstart.prevent.stop
        @touchend.prevent.stop
        @touchmove.prevent.stop
        @pointerdown.prevent.stop
        @pointerup.prevent.stop
        @pointermove.prevent.stop
        title="Video controls are disabled during watch time verification"
      ></div>

      <!-- Play Button Overlay (shows if autoplay fails) -->
      <div v-if="showPlayButton" class="absolute inset-0 flex items-center justify-center bg-black/50 z-20">
        <button
          @click="startVideo"
          class="w-20 h-20 flex items-center justify-center rounded-full bg-red-600 hover:bg-red-700 transition-all transform hover:scale-110"
        >
          <svg class="w-10 h-10 text-white ml-1" fill="currentColor" viewBox="0 0 20 20">
            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
          </svg>
        </button>
      </div>

      <!-- Blur Overlay - Shows when video ends to hide YouTube end screen -->
      <div
        v-if="videoEnded"
        class="absolute inset-0 backdrop-blur-xl bg-black/60 flex items-center justify-center"
        style="z-index: 30; pointer-events: auto;"
      >
        <div class="text-center">
          <svg class="w-20 h-20 text-green-400 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <h3 class="text-2xl font-bold text-white mb-2">✓ Video Watched!</h3>
          <p class="text-gray-300">Scroll down to answer questions</p>
        </div>
      </div>

      <!-- Progress Bar Overlay (bottom only, doesn't block video) -->
      <div v-if="!canComplete && !videoEnded" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 via-black/70 to-transparent p-4 pointer-events-none" style="z-index: 11;">
        <div class="text-center text-white">
          <p class="text-sm font-bold mb-2">Watch Progress: {{ Math.floor(watchedSeconds) }}s / {{ requiredSeconds }}s ({{ Math.floor(watchProgress) }}%)</p>
          <div class="w-full bg-gray-700 rounded-full h-2 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-purple-600 h-full transition-all duration-300" :style="{ width: watchProgress + '%' }"></div>
          </div>
          <p class="text-xs text-gray-300 mt-2">Keep watching to unlock reward</p>
        </div>
      </div>
    </div>

    <!-- Post-Video Questions (shows after video watched) -->
    <div v-if="canComplete && questions.length > 0" class="bg-white/5 rounded-xl p-6 border border-white/10 mb-4">
      <h3 class="text-xl font-bold text-white mb-4">📝 Answer a few questions about the video</h3>
      <div class="space-y-4">
        <div v-for="(question, index) in questions" :key="question.id" class="bg-white/5 rounded-xl p-4 border border-white/10">
          <div class="flex items-start gap-3 mb-3">
            <div class="flex-shrink-0 w-7 h-7 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center font-bold text-white text-sm">
              {{ index + 1 }}
            </div>
            <div class="flex-1">
              <h4 class="text-white font-semibold text-sm">{{ question.text }}</h4>
              <p v-if="question.required" class="text-xs text-orange-400 mt-1">* Required</p>
            </div>
          </div>

          <!-- Single Choice -->
          <div v-if="question.type === 'single_choice'" class="space-y-2 ml-10">
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
              <span class="text-gray-300 group-hover:text-white transition-colors text-sm">{{ option }}</span>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- Minimum Time Warning -->
    <div v-if="!canSubmitByTime && canComplete" class="bg-orange-500/10 border border-orange-500/30 rounded-xl p-4 mb-4">
      <div class="flex items-center gap-3">
        <svg class="w-6 h-6 text-orange-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
        </svg>
        <div>
          <p class="text-orange-400 font-semibold">Please take your time to watch carefully</p>
          <p class="text-gray-300 text-sm">You can submit in {{ remainingTime }} seconds</p>
        </div>
      </div>
    </div>

    <!-- reCAPTCHA (only shows if user has recent fraud incidents) -->
    <div v-if="showRecaptcha && canComplete" class="bg-gradient-to-br from-orange-500/20 to-red-500/20 rounded-xl p-6 border-2 border-orange-500/50 mb-4">
      <div class="text-center mb-4">
        <h3 class="text-xl font-bold text-orange-400 mb-2">🔒 Security Verification Required</h3>
        <p class="text-sm text-gray-300">Please complete the CAPTCHA below to claim your reward</p>
      </div>
      <div id="recaptcha-container-video" class="flex justify-center mb-2"></div>
      <p v-if="recaptchaError" class="text-red-400 text-xs mt-2 text-center animate-pulse">
        ⚠️ Please complete the CAPTCHA verification above
      </p>
    </div>

    <!-- Complete Button -->
    <button
      @click="completeTask"
      :disabled="!canComplete || !canSubmitByTime || completing || (showRecaptcha && !recaptchaCompleted)"
      :class="[
        'w-full py-4 rounded-xl font-bold text-white transition-all flex items-center justify-center gap-2',
        canComplete && canSubmitByTime && !completing && (!showRecaptcha || recaptchaCompleted)
          ? 'bg-gradient-to-r from-green-500 to-emerald-600 hover:shadow-xl hover:shadow-green-500/50 cursor-pointer'
          : 'bg-gray-600 cursor-not-allowed opacity-50'
      ]">
      <svg v-if="!completing && (!showRecaptcha || recaptchaCompleted)" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <svg v-else-if="!completing && showRecaptcha && !recaptchaCompleted" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
      </svg>
      <svg v-else class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
      </svg>
      {{ completing ? 'Submitting...' : (showRecaptcha && !recaptchaCompleted) ? '🔒 Complete CAPTCHA First' : canComplete ? 'Complete & Claim Reward' : 'Watch Full Video First' }}
    </button>
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
const { shouldShow: showRecaptcha, recaptchaError, renderRecaptcha, getToken, recaptchaToken } = useRecaptcha();

const playerContainer = ref(null);
let player = null;
const watchedSeconds = ref(0);
const requiredSeconds = ref(0);
const canComplete = ref(false);
const completing = ref(false);
const startTime = ref(Date.now());
const showPlayButton = ref(false);
const recaptchaCompleted = ref(false);
const answers = ref({});
const videoEnded = ref(false);
const elapsedTime = ref(0);
const timerInterval = ref(null);
const minimumTime = ref(0);

const watchProgress = computed(() => {
  if (requiredSeconds.value === 0) return 0;
  return Math.min((watchedSeconds.value / requiredSeconds.value) * 100, 100);
});

const remainingTime = computed(() => {
  const remaining = Math.ceil((minimumTime.value - elapsedTime.value) / 1000);
  return Math.max(0, remaining);
});

const canSubmitByTime = computed(() => {
  return elapsedTime.value >= minimumTime.value;
});

const questions = computed(() => {
  try {
    const questionsData = props.task.task_template.questions;
    if (typeof questionsData === 'string') {
      return JSON.parse(questionsData);
    }
    return questionsData || [];
  } catch (e) {
    console.error('Error parsing questions:', e);
    return [];
  }
});

const getYouTubeVideoId = (url) => {
  const regex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
  const match = url.match(regex);
  return match ? match[1] : null;
};

onMounted(async () => {
  await getFingerprint();

  // Set minimum time from task template (default 60 seconds if not set)
  minimumTime.value = (props.task.task_template.completion_time_seconds || 60) * 1000;

  // Start elapsed time tracker
  timerInterval.value = setInterval(() => {
    elapsedTime.value = Date.now() - startTime.value;
  }, 1000);

  const videoUrl = props.task.task_template.video_url || props.task.task_template.data?.video_url;
  const videoId = getYouTubeVideoId(videoUrl);

  if (!videoId) {
    Swal.fire({
      icon: 'error',
      title: 'Invalid Video',
      text: 'Could not load video. Please contact support.',
      background: '#1f2937',
      color: '#fff'
    });
    return;
  }

  // Load YouTube IFrame API
  if (!window.YT) {
    const tag = document.createElement('script');
    tag.src = 'https://www.youtube.com/iframe_api';
    const firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
  }

  window.onYouTubeIframeAPIReady = () => {
    player = new YT.Player(playerContainer.value, {
      videoId: videoId,
      playerVars: {
        autoplay: 1,
        controls: 0,
        disablekb: 1,
        modestbranding: 1,
        rel: 0,
        showinfo: 0,
        fs: 0,
        iv_load_policy: 3,
        loop: 1,
        playlist: videoId
      },
      events: {
        onReady: onPlayerReady,
        onStateChange: onPlayerStateChange
      }
    });
  };

  if (window.YT && window.YT.Player) {
    window.onYouTubeIframeAPIReady();
  }
});

const onPlayerReady = (event) => {
  requiredSeconds.value = Math.floor(event.target.getDuration() * 0.9); // 90% watch required

  // Check if video is playing after 2 seconds (autoplay might be blocked)
  setTimeout(() => {
    if (player && player.getPlayerState() !== 1) { // Not playing
      showPlayButton.value = true;
    }
  }, 2000);

  // Track watch time
  setInterval(() => {
    if (player && player.getPlayerState() === 1) { // Playing
      watchedSeconds.value++;

      if (watchedSeconds.value >= requiredSeconds.value && !canComplete.value) {
        canComplete.value = true;

        // Render reCAPTCHA immediately if needed
        if (showRecaptcha.value) {
          console.log('[VideoPlayer] Manually triggering reCAPTCHA render');
          setTimeout(() => {
            renderRecaptcha('recaptcha-container-video');
          }, 100);
        }
      }
    }
  }, 1000);
};

const onPlayerStateChange = (event) => {
  // Hide play button when video starts playing
  if (event.data === YT.PlayerState.PLAYING) {
    showPlayButton.value = false;

    // Prevent seeking
    const currentTime = player.getCurrentTime();
    if (currentTime > watchedSeconds.value + 2) {
      player.seekTo(watchedSeconds.value);
    }
  }

  // Show blur overlay when video ends to hide YouTube's end screen
  if (event.data === YT.PlayerState.ENDED) {
    videoEnded.value = true;
  }
};

const startVideo = () => {
  if (player && player.playVideo) {
    player.playVideo();
  }
};

const completeTask = async () => {
  completing.value = true;

  // Validate questions if present
  if (questions.value.length > 0) {
    const requiredQuestions = questions.value.filter(q => q.required !== false);
    const allAnswered = requiredQuestions.every(q => {
      const answer = answers.value[q.id];
      return answer !== null && answer !== undefined && answer !== '';
    });

    if (!allAnswered) {
      completing.value = false;
      Swal.fire({
        icon: 'warning',
        title: 'Questions Required',
        text: 'Please answer all questions about the video before submitting.',
        background: '#1f2937',
        color: '#fff'
      });
      return;
    }
  }

  // If reCAPTCHA is required, wait for it to render and be completed
  if (showRecaptcha.value) {
    // Make sure reCAPTCHA is rendered
    await renderRecaptcha('recaptcha-container-video');

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
  const videoUrl = props.task.task_template.video_url || props.task.task_template.data?.video_url;

  // Get reCAPTCHA token if triggered by fraud
  const recaptchaToken = showRecaptcha.value ? getToken() : null;

  const requestData = attachToRequest({
    response_data: {
      video_id: getYouTubeVideoId(videoUrl),
      watched_seconds: watchedSeconds.value,
      required_seconds: requiredSeconds.value,
      completion_percentage: Math.floor(watchProgress.value),
      answers: answers.value  // Include answers
    },
    duration: duration,
    recaptcha_token: recaptchaToken
  });

  router.post(`/tasks/${props.task.id}/complete`, requestData, {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: '🎉 Task Completed!',
        html: `<p class="text-lg">You earned <strong class="text-green-400">₦${props.task.reward_amount}</strong></p>`,
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#10b981'
      });
      emit('completed');
    },
    onError: (errors) => {
      completing.value = false;

      // Check if error is about daily limit
      const errorMessage = errors?.error || errors?.message || 'Please try again';
      const isDailyLimit = errorMessage.toLowerCase().includes('daily task limit');

      if (isDailyLimit) {
        // Show upgrade/referral encouragement
        Swal.fire({
          icon: 'warning',
          title: '🎯 Daily Task Limit Reached!',
          html: `
            <div class="text-left">
              <p class="mb-3">${errorMessage}</p>
              <p class="mb-4 text-gray-300">Want to earn even more?</p>
              <div class="space-y-3">
                <div class="flex items-start gap-2 bg-orange-500/10 p-3 rounded-lg border border-orange-500/30">
                  <span class="text-xl">✨</span>
                  <div>
                    <p class="font-bold text-orange-400">Upgrade Your Plan</p>
                    <p class="text-sm text-gray-300">Unlock more daily tasks and higher earnings</p>
                  </div>
                </div>
                <div class="flex items-start gap-2 bg-purple-500/10 p-3 rounded-lg border border-purple-500/30">
                  <span class="text-xl">👥</span>
                  <div>
                    <p class="font-bold text-purple-400">Refer Friends</p>
                    <p class="text-sm text-gray-300">Earn unlimited commissions from your team's tasks</p>
                  </div>
                </div>
              </div>
            </div>
          `,
          background: '#1f2937',
          color: '#fff',
          showCancelButton: true,
          confirmButtonText: '✨ Upgrade Plan',
          cancelButtonText: '👥 Refer & Earn',
          confirmButtonColor: '#f97316',
          cancelButtonColor: '#8b5cf6',
          reverseButtons: true
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = '/upgrade-plan';
          } else if (result.dismiss === Swal.DismissReason.cancel) {
            window.location.href = '/referrals';
          }
        });
      } else {
        // Regular error
        Swal.fire({
          icon: 'error',
          title: 'Submission Failed',
          text: errorMessage,
          background: '#1f2937',
          color: '#fff'
        });
      }
    }
  });
};

onUnmounted(() => {
  if (player && player.destroy) {
    player.destroy();
  }
  if (timerInterval.value) {
    clearInterval(timerInterval.value);
  }
});

// Render reCAPTCHA when video is completed (only if fraud detected)
watch(canComplete, async (newValue) => {
  if (newValue && showRecaptcha.value) {
    await nextTick();
    renderRecaptcha('recaptcha-container-video');
  }
});

// Track when reCAPTCHA is completed
watch(recaptchaToken, (newToken) => {
  if (newToken) {
    recaptchaCompleted.value = true;
  }
});
</script>
