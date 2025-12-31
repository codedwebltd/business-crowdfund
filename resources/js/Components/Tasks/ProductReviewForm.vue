<template>
  <div class="space-y-4">
    <!-- Product Information -->
    <div class="bg-white/5 rounded-xl p-4 border border-white/10">
      <h3 class="text-lg font-bold text-white mb-2">{{ productName }}</h3>
      <p class="text-gray-300 text-sm">{{ task.task_template.description }}</p>

      <!-- Product Image if available -->
      <div v-if="productImage" class="mt-3">
        <img :src="productImage" :alt="productName" class="w-full max-w-md rounded-lg border border-white/20" />
      </div>
    </div>

    <!-- Star Rating -->
    <div class="bg-gradient-to-br from-yellow-500/10 to-orange-500/10 rounded-xl p-4 border border-yellow-500/30">
      <label class="text-sm font-semibold text-white mb-3 block">
        Rate this product <span class="text-red-400">*</span>
      </label>
      <div class="flex items-center gap-2">
        <button
          v-for="star in 5"
          :key="star"
          type="button"
          @click="rating = star"
          class="transition-all transform hover:scale-110"
        >
          <svg
            class="w-10 h-10"
            :class="star <= rating ? 'text-yellow-400 fill-current' : 'text-gray-500'"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"
            />
          </svg>
        </button>
        <span class="ml-2 text-white font-bold text-lg">{{ rating ? rating + '/5' : 'Select' }}</span>
      </div>
      <p v-if="!rating && showErrors" class="text-red-400 text-xs mt-2">⚠️ Please select a rating</p>
    </div>

    <!-- Written Review -->
    <div class="bg-white/5 rounded-xl p-4 border border-white/10">
      <label class="text-sm font-semibold text-white mb-2 block">
        Write your review <span class="text-red-400">*</span>
        <span class="text-gray-400 font-normal">(minimum {{ minLength }} characters)</span>
      </label>
      <textarea
        v-model="reviewText"
        rows="6"
        placeholder="Share your honest experience with this product... What did you like? What could be improved?"
        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
        @input="showErrors = false"
      ></textarea>
      <div class="flex justify-between items-center mt-2">
        <p
          :class="[
            'text-xs font-medium',
            reviewText.length >= minLength ? 'text-green-400' : 'text-gray-400'
          ]"
        >
          {{ reviewText.length }} / {{ minLength }} characters
        </p>
        <p v-if="reviewText.length < minLength && showErrors" class="text-red-400 text-xs">
          ⚠️ Review too short
        </p>
      </div>
    </div>

    <!-- Optional: Pros and Cons -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div class="bg-green-500/10 rounded-xl p-4 border border-green-500/30">
        <label class="text-sm font-semibold text-green-400 mb-2 block flex items-center gap-2">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          What did you like? (optional)
        </label>
        <input
          v-model="pros"
          type="text"
          placeholder="e.g., Great quality, Fast delivery"
          class="w-full px-3 py-2 bg-white/10 border border-green-500/30 rounded-lg text-white placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
        />
      </div>
      <div class="bg-red-500/10 rounded-xl p-4 border border-red-500/30">
        <label class="text-sm font-semibold text-red-400 mb-2 block flex items-center gap-2">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          Any issues? (optional)
        </label>
        <input
          v-model="cons"
          type="text"
          placeholder="e.g., Expensive, Packaging damaged"
          class="w-full px-3 py-2 bg-white/10 border border-red-500/30 rounded-lg text-white placeholder-gray-400 text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
        />
      </div>
    </div>

    <!-- Recommendation -->
    <div class="bg-white/5 rounded-xl p-4 border border-white/10">
      <label class="text-sm font-semibold text-white mb-3 block">
        Would you recommend this product? <span class="text-red-400">*</span>
      </label>
      <div class="flex gap-4">
        <button
          type="button"
          @click="wouldRecommend = true"
          :class="[
            'flex-1 py-3 px-4 rounded-lg font-semibold transition-all',
            wouldRecommend === true
              ? 'bg-green-500 text-white shadow-lg shadow-green-500/50'
              : 'bg-white/10 text-gray-300 hover:bg-white/20'
          ]"
        >
          👍 Yes
        </button>
        <button
          type="button"
          @click="wouldRecommend = false"
          :class="[
            'flex-1 py-3 px-4 rounded-lg font-semibold transition-all',
            wouldRecommend === false
              ? 'bg-red-500 text-white shadow-lg shadow-red-500/50'
              : 'bg-white/10 text-gray-300 hover:bg-white/20'
          ]"
        >
          👎 No
        </button>
      </div>
      <p v-if="wouldRecommend === null && showErrors" class="text-red-400 text-xs mt-2">⚠️ Please select an option</p>
    </div>

    <!-- reCAPTCHA (only shows if user has recent fraud incidents) -->
    <div v-if="showRecaptcha" class="mb-4">
      <div id="recaptcha-container-review" class="flex justify-center"></div>
      <p v-if="recaptchaError" class="text-red-400 text-xs mt-2 text-center">
        ⚠️ Please complete the CAPTCHA verification
      </p>
    </div>

    <!-- Minimum Time Warning -->
    <div v-if="!canSubmitByTime" class="bg-orange-500/10 border border-orange-500/30 rounded-xl p-4 mb-4">
      <div class="flex items-center gap-3">
        <svg class="w-6 h-6 text-orange-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
        </svg>
        <div>
          <p class="text-orange-400 font-semibold">Please take your time to write a thoughtful review</p>
          <p class="text-gray-300 text-sm">You can submit in {{ remainingTime }} seconds</p>
        </div>
      </div>
    </div>

    <!-- Complete Button -->
    <button
      @click="submitReview"
      :disabled="completing"
      :class="[
        'w-full py-4 rounded-xl font-bold text-white transition-all flex items-center justify-center gap-2',
        canSubmit && !completing
          ? 'bg-gradient-to-r from-green-500 to-emerald-600 hover:shadow-xl hover:shadow-green-500/50 cursor-pointer'
          : 'bg-gray-600 cursor-not-allowed opacity-50'
      ]"
    >
      <svg v-if="!completing && canSubmitByTime" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <svg v-else-if="!completing && !canSubmitByTime" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
      </svg>
      <svg v-else class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
      </svg>
      {{ completing ? 'Submitting...' : !canSubmitByTime ? `Wait ${remainingTime}s` : 'Submit Review & Claim Reward' }}
    </button>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import { useDeviceFingerprint } from '@/composables/useDeviceFingerprint';
import { useRecaptcha } from '@/composables/useRecaptcha';
import Swal from 'sweetalert2';

const props = defineProps({
  task: Object
});

const emit = defineEmits(['completed']);
const { getFingerprint, attachToRequest } = useDeviceFingerprint();
const { shouldShow: showRecaptcha, recaptchaError, renderRecaptcha, getToken } = useRecaptcha();

const rating = ref(0);
const reviewText = ref('');
const pros = ref('');
const cons = ref('');
const wouldRecommend = ref(null);
const completing = ref(false);
const showErrors = ref(false);
const startTime = ref(Date.now());
const elapsedTime = ref(0);
const timerInterval = ref(null);
const minLength = ref(20);
const minimumTime = ref(20000); // 20 seconds minimum

const productName = computed(() => {
  return props.task.task_template.data?.product_name || props.task.task_template.title || 'Product';
});

const productImage = computed(() => {
  return props.task.task_template.data?.product_image || null;
});

const remainingTime = computed(() => {
  const remaining = Math.ceil((minimumTime.value - elapsedTime.value) / 1000);
  return Math.max(0, remaining);
});

const canSubmitByTime = computed(() => {
  return elapsedTime.value >= minimumTime.value;
});

const canSubmit = computed(() => {
  return canSubmitByTime.value &&
         rating.value > 0 &&
         reviewText.value.trim().length >= minLength.value &&
         wouldRecommend.value !== null;
});

onMounted(async () => {
  await getFingerprint();
  minLength.value = props.task.task_template.data?.min_review_length || 20;

  // Start timer
  timerInterval.value = setInterval(() => {
    elapsedTime.value = Date.now() - startTime.value;
  }, 1000);

  // Render reCAPTCHA if fraud detected
  if (showRecaptcha.value) {
    await nextTick();
    renderRecaptcha('recaptcha-container-review');
  }
});

onUnmounted(() => {
  if (timerInterval.value) {
    clearInterval(timerInterval.value);
  }
});

const submitReview = async () => {
  if (!canSubmit.value) {
    showErrors.value = true;
    Swal.fire({
      icon: 'warning',
      title: 'Incomplete Review',
      text: 'Please fill in all required fields before submitting.',
      background: '#1f2937',
      color: '#fff'
    });
    return;
  }

  completing.value = true;

  // If reCAPTCHA is required, wait for it to render and be completed
  if (showRecaptcha.value) {
    // Make sure reCAPTCHA is rendered
    await renderRecaptcha('recaptcha-container-review');

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
      rating: rating.value,
      review: reviewText.value.trim(),
      pros: pros.value.trim(),
      cons: cons.value.trim(),
      would_recommend: wouldRecommend.value,
      product_name: productName.value,
      review_length: reviewText.value.trim().length
    },
    duration: duration,
    recaptcha_token: recaptchaToken
  });

  router.post(`/tasks/${props.task.id}/complete`, requestData, {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: '🎉 Review Submitted!',
        html: `<p class="text-lg">Thank you for your feedback!</p><p class="text-lg">You earned <strong class="text-green-400">₦${props.task.reward_amount}</strong></p>`,
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#10b981'
      });
      emit('completed');
    },
    onError: (errors) => {
      completing.value = false;

      const errorMessage = errors?.error || errors?.message || 'Please try again';
      const isDailyLimit = errorMessage.toLowerCase().includes('daily task limit');

      if (isDailyLimit) {
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
</script>
