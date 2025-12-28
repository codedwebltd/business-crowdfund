<template>
  <div class="space-y-4">
    <!-- Description -->
    <div class="bg-white/5 rounded-xl p-4 border border-white/10">
      <p class="text-gray-300 text-sm">{{ task.task_template.description }}</p>
    </div>

    <!-- Video Player -->
    <div class="relative bg-black rounded-xl overflow-hidden aspect-video">
      <div ref="playerContainer" class="w-full h-full"></div>

      <!-- Progress Bar Overlay (bottom only, doesn't block video) -->
      <div v-if="!canComplete" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 via-black/70 to-transparent p-4">
        <div class="text-center text-white">
          <p class="text-sm font-bold mb-2">Watch Progress: {{ Math.floor(watchedSeconds) }}s / {{ requiredSeconds }}s ({{ Math.floor(watchProgress) }}%)</p>
          <div class="w-full bg-gray-700 rounded-full h-2 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-purple-600 h-full transition-all duration-300" :style="{ width: watchProgress + '%' }"></div>
          </div>
          <p class="text-xs text-gray-300 mt-2">Keep watching to unlock reward</p>
        </div>
      </div>
    </div>

    <!-- Complete Button -->
    <button
      @click="completeTask"
      :disabled="!canComplete || completing"
      :class="[
        'w-full py-4 rounded-xl font-bold text-white transition-all flex items-center justify-center gap-2',
        canComplete && !completing
          ? 'bg-gradient-to-r from-green-500 to-emerald-600 hover:shadow-xl hover:shadow-green-500/50 cursor-pointer'
          : 'bg-gray-600 cursor-not-allowed opacity-50'
      ]">
      <svg v-if="!completing" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <svg v-else class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
      </svg>
      {{ completing ? 'Submitting...' : canComplete ? 'Complete & Claim Reward' : 'Watch Full Video First' }}
    </button>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useDeviceFingerprint } from '@/composables/useDeviceFingerprint';
import Swal from 'sweetalert2';

const props = defineProps({
  task: Object
});

const emit = defineEmits(['completed']);
const { getFingerprint, attachToRequest } = useDeviceFingerprint();

const playerContainer = ref(null);
let player = null;
const watchedSeconds = ref(0);
const requiredSeconds = ref(0);
const canComplete = ref(false);
const completing = ref(false);
const startTime = ref(Date.now());

const watchProgress = computed(() => {
  if (requiredSeconds.value === 0) return 0;
  return Math.min((watchedSeconds.value / requiredSeconds.value) * 100, 100);
});

const getYouTubeVideoId = (url) => {
  const regex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
  const match = url.match(regex);
  return match ? match[1] : null;
};

onMounted(async () => {
  await getFingerprint();

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
        fs: 0
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

  // Track watch time
  setInterval(() => {
    if (player && player.getPlayerState() === 1) { // Playing
      watchedSeconds.value++;

      if (watchedSeconds.value >= requiredSeconds.value) {
        canComplete.value = true;
      }
    }
  }, 1000);
};

const onPlayerStateChange = (event) => {
  // Prevent seeking
  if (event.data === YT.PlayerState.PLAYING) {
    const currentTime = player.getCurrentTime();
    if (currentTime > watchedSeconds.value + 2) {
      player.seekTo(watchedSeconds.value);
    }
  }
};

const completeTask = async () => {
  completing.value = true;

  const duration = Math.floor((Date.now() - startTime.value) / 1000);
  const videoUrl = props.task.task_template.video_url || props.task.task_template.data?.video_url;

  const requestData = attachToRequest({
    response_data: {
      video_id: getYouTubeVideoId(videoUrl),
      watched_seconds: watchedSeconds.value,
      required_seconds: requiredSeconds.value,
      completion_percentage: Math.floor(watchProgress.value)
    },
    duration: duration
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
});
</script>
