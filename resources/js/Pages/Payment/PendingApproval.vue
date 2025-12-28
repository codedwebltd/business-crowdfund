<template>
  <Head title="Payment Pending Approval" />

  <UserLayout title="Payment Confirmation">
    <!-- Success Header -->
    <div class="text-center mb-6">
      <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-500/20 to-emerald-500/20 backdrop-blur-xl rounded-2xl border border-green-500/30 mb-4 shadow-lg shadow-green-500/20">
        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center animate-pulse">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
          </svg>
        </div>
      </div>
      <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Payment Confirmation Received</h1>
      <p class="text-gray-400">Your transaction is under review by our team</p>
    </div>

    <!-- Transaction ID Card -->
    <div class="group relative bg-gradient-to-br from-purple-500/10 via-indigo-600/10 to-transparent backdrop-blur-xl rounded-2xl border border-purple-500/30 p-6 mb-6 hover:border-purple-500/50 hover:shadow-2xl hover:shadow-purple-500/20 transition-all duration-300 overflow-hidden">
      <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-purple-500/10 to-indigo-500/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-500"></div>

      <div class="relative z-10">
        <div class="flex items-center gap-3 mb-4">
          <div class="p-3 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl shadow-lg shadow-purple-500/30">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-white font-bold text-lg">Transaction Reference</h3>
            <p class="text-gray-400 text-xs">Save this ID for support inquiries</p>
          </div>
        </div>

        <div class="flex gap-2">
          <div class="flex-1 bg-white/10 border border-white/20 rounded-lg px-4 py-3">
            <p class="text-white text-xl font-mono font-bold">{{ transactionId }}</p>
          </div>
          <button @click="copyTransactionId" class="shrink-0 bg-gradient-to-r from-purple-500 to-purple-600 text-white px-4 py-3 rounded-lg font-semibold hover:from-purple-600 hover:to-purple-700 transition-all flex items-center gap-2 shadow-lg shadow-purple-500/30">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <span class="hidden sm:inline">{{ copied ? 'Copied!' : 'Copy' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Countdown Timer Card -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6 mb-6 hover:shadow-xl hover:shadow-white/10 transition-all duration-300">
      <div class="flex items-center gap-3 mb-6">
        <div class="p-2.5 rounded-lg bg-blue-500/20">
          <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-white font-bold">Estimated Review Time</h3>
          <p class="text-gray-400 text-xs">Typically 2-4 hours</p>
        </div>
      </div>

      <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="bg-white/5 border border-white/10 rounded-xl p-4 text-center">
          <div class="text-3xl md:text-4xl font-bold text-white mb-1">{{ hours }}</div>
          <div class="text-xs text-gray-400 uppercase tracking-wider">Hours</div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-xl p-4 text-center">
          <div class="text-3xl md:text-4xl font-bold text-white mb-1">{{ minutes }}</div>
          <div class="text-xs text-gray-400 uppercase tracking-wider">Minutes</div>
        </div>
        <div class="bg-white/5 border border-white/10 rounded-xl p-4 text-center">
          <div class="text-3xl md:text-4xl font-bold text-white mb-1">{{ seconds }}</div>
          <div class="text-xs text-gray-400 uppercase tracking-wider">Seconds</div>
        </div>
      </div>

      <div v-if="countdown > 0" class="flex items-center gap-2 text-sm text-gray-400">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        <span>Approval may occur sooner depending on verification speed</span>
      </div>

      <!-- Support Message when timer reaches 0 -->
      <div v-else class="mt-4 p-4 bg-gradient-to-br from-purple-500/10 to-pink-500/10 border border-purple-500/30 rounded-xl">
        <div class="flex items-start gap-3 mb-3">
          <div class="p-2 bg-purple-500/20 rounded-lg flex-shrink-0">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </div>
          <div>
            <h4 class="text-purple-300 font-bold text-sm mb-2">⏰ Review Time Elapsed</h4>
            <p class="text-gray-300 text-xs leading-relaxed mb-3">
              Your payment is still being reviewed. This delay could be due to high payment volume. Don't worry - your transaction is safe!
            </p>
            <p class="text-white text-xs font-semibold mb-2">Need faster assistance? Contact us with your Payment ID:</p>
            <div class="space-y-2">
              <div v-if="supportEmail" class="flex items-center gap-2 text-xs">
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <a :href="`mailto:${supportEmail}`" class="text-purple-300 hover:text-purple-200 underline">{{ supportEmail }}</a>
              </div>
              <div v-if="supportPhone" class="flex items-center gap-2 text-xs">
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <a :href="`tel:${supportPhone}`" class="text-purple-300 hover:text-purple-200">{{ supportPhone }}</a>
              </div>
              <div v-if="supportWhatsapp" class="flex items-center gap-2 text-xs">
                <svg class="w-4 h-4 text-purple-400" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
                <a :href="`https://wa.me/${supportWhatsapp.replace(/[^0-9]/g, '')}`" target="_blank" class="text-purple-300 hover:text-purple-200">WhatsApp Support</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Plan Info Card -->
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6 mb-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="p-3 bg-gradient-to-br from-pink-500 to-purple-500 rounded-xl shadow-lg shadow-pink-500/30">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
          </div>
          <div>
            <div class="text-xs text-gray-400 mb-1">Selected Plan</div>
            <div class="text-xl font-bold text-white">{{ planName }}</div>
          </div>
        </div>
        <div class="px-4 py-2 bg-green-500/20 border border-green-500/30 rounded-lg">
          <span class="text-xs text-green-400 font-semibold">PENDING</span>
        </div>
      </div>
    </div>

    <!-- Important Notice -->
    <div class="bg-yellow-500/10 backdrop-blur-xl rounded-2xl border border-yellow-500/30 p-6 mb-6">
      <div class="flex items-center gap-3 mb-4">
        <div class="p-2.5 rounded-lg bg-yellow-500/20">
          <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
        </div>
        <h3 class="text-white font-bold">Important Notice</h3>
      </div>

      <ul class="space-y-3 text-sm text-gray-300">
        <li class="flex items-start gap-3">
          <svg class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <span>Your payment is being reviewed by our team. This typically takes <strong class="text-white">2-4 hours</strong>.</span>
        </li>
        <li class="flex items-start gap-3">
          <svg class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
          <span><strong class="text-white">If you haven't made the payment yet</strong>, please complete it now using the provided details.</span>
        </li>
        <li class="flex items-start gap-3">
          <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          <span>Transactions without actual payment will <strong class="text-white">NOT be approved</strong> and will be auto-purged after 24 hours.</span>
        </li>
        <li class="flex items-start gap-3">
          <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
          </svg>
          <span>You'll receive an email notification once your account is activated.</span>
        </li>
      </ul>
    </div>

    <!-- Action Buttons -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <button @click="backToPlans" class="py-3.5 px-6 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold rounded-xl transition-all">
        View Payment Details
      </button>
      <button @click="refreshStatus" :disabled="checking" class="py-3.5 px-6 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-purple-500/30 disabled:opacity-50">
        {{ checking ? 'Checking...' : 'Refresh Status' }}
      </button>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
  transactionId: String,
  planName: String,
  createdAt: String,
  supportEmail: String,
  supportPhone: String,
  supportWhatsapp: String,
});

const copied = ref(false);
const checking = ref(false);
const countdown = ref(0);

const hours = computed(() => Math.floor(countdown.value / 3600));
const minutes = computed(() => Math.floor((countdown.value % 3600) / 60));
const seconds = computed(() => countdown.value % 60);

let interval = null;

const calculateCountdown = () => {
  const created = new Date(props.createdAt);
  const now = new Date();
  const fourHoursLater = new Date(created.getTime() + (4 * 60 * 60 * 1000));
  const diff = Math.floor((fourHoursLater - now) / 1000);
  countdown.value = diff > 0 ? diff : 0;
};

onMounted(() => {
  calculateCountdown();
  interval = setInterval(() => {
    if (countdown.value > 0) {
      countdown.value--;
    }
  }, 1000);
});

onUnmounted(() => {
  if (interval) clearInterval(interval);
});

const copyTransactionId = async () => {
  try {
    await navigator.clipboard.writeText(props.transactionId);
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
  } catch (err) {
    console.error('Failed to copy:', err);
  }
};

const backToPlans = () => {
  // Go back to payment page to see QR codes again
  router.get('/payment/view', {
    transaction_id: props.transactionId,
  });
};

const refreshStatus = () => {
  checking.value = true;
  router.reload({
    onFinish: () => checking.value = false,
  });
};
</script>
