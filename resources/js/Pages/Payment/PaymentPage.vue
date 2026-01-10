<template>
  <Head :title="`Payment - ${plan.display_name}`" />

  <UserLayout title="Complete Payment">
    <!-- Header -->
    <div class="text-center mb-6">
      <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-500/20 to-emerald-500/20 backdrop-blur-xl rounded-2xl border border-green-500/30 mb-4 shadow-lg shadow-green-500/20">
        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </div>
      </div>
      <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Complete Your Payment</h1>
      <p class="text-gray-400">
        {{ plan.display_name }} -
        <span v-if="isUpgrade && discountedPrice">
          <span class="line-through text-gray-500">{{ formatCurrency(originalPrice) }}</span>
          <span class="text-green-400 font-bold ml-2">{{ formatCurrency(discountedPrice) }}</span>
          <span class="text-xs text-green-400 ml-1">({{ discountPercentage }}% OFF)</span>
        </span>
        <span v-else>{{ formatCurrency(plan.price) }}</span>
      </p>
    </div>


    <!-- Bank Transfer Card -->
    <div v-if="paymentMethod === 'bank_transfer'" class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6 mb-6">
      <div class="flex items-center gap-3 mb-6">
        <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg shadow-green-500/30">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-white font-bold text-lg">Bank Transfer Details</h3>
          <p class="text-gray-400 text-xs">Transfer exact amount to account below</p>
        </div>
      </div>

      <div v-for="(account, index) in paymentAccounts" :key="index" class="mb-4 last:mb-0">
            <!-- QR Code -->
            <div class="flex justify-center mb-4">
              <div class="p-4 bg-white rounded-xl">
                <img :src="generateQRCode(account)" alt="Payment QR Code" class="w-48 h-48" />
              </div>
            </div>

            <!-- Account Details -->
            <div class="space-y-3">
              <div class="flex justify-between items-center gap-4">
                <div class="flex-1">
                  <p class="text-xs text-purple-300">Bank Name</p>
                  <p class="text-white font-semibold">{{ account.bank_name }}</p>
                </div>
                <button @click="copyToClipboard(account.bank_name)" class="px-3 py-1.5 bg-purple-500/20 hover:bg-purple-500/30 rounded-lg text-xs text-purple-200">
                  Copy
                </button>
              </div>

              <div class="flex justify-between items-center gap-4">
                <div class="flex-1">
                  <p class="text-xs text-purple-300">Account Number</p>
                  <p class="text-white font-mono font-bold text-lg">{{ account.account_number }}</p>
                </div>
                <button @click="copyToClipboard(account.account_number)" class="px-3 py-1.5 bg-purple-500/20 hover:bg-purple-500/30 rounded-lg text-xs text-purple-200">
                  Copy
                </button>
              </div>

              <div class="flex justify-between items-center gap-4">
                <div class="flex-1">
                  <p class="text-xs text-purple-300">Account Name</p>
                  <p class="text-white font-semibold">{{ account.account_name }}</p>
                </div>
                <button @click="copyToClipboard(account.account_name)" class="px-3 py-1.5 bg-purple-500/20 hover:bg-purple-500/30 rounded-lg text-xs text-purple-200">
                  Copy
                </button>
              </div>

              <div class="flex justify-between items-center gap-4">
                <div class="flex-1">
                  <p class="text-xs text-purple-300">Amount</p>
                  <p class="text-white font-bold text-xl">{{ formatCurrency(amountToPay) }}</p>
                </div>
                <button @click="copyToClipboard(amountToPay)" class="px-3 py-1.5 bg-purple-500/20 hover:bg-purple-500/30 rounded-lg text-xs text-purple-200">
                  Copy
                </button>
              </div>

              <div v-if="account.special_note" class="p-3 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                <p class="text-xs text-yellow-200">{{ account.special_note }}</p>
              </div>
            </div>
      </div>
    </div>

    <!-- Crypto Transfer Card -->
    <div v-if="paymentMethod === 'crypto_transfer'" class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6 mb-6">
      <div class="flex items-center gap-3 mb-6">
        <div class="p-3 bg-gradient-to-br from-orange-500 to-yellow-600 rounded-2xl shadow-lg shadow-orange-500/30">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-white font-bold text-lg">Cryptocurrency Payment</h3>
          <p class="text-gray-400 text-xs">Send crypto to wallet address below</p>
        </div>
      </div>

      <div v-for="(wallet, index) in paymentAccounts" :key="index" class="mb-4 last:mb-0">
            <!-- Amount Conversion Display -->
            <div v-if="cryptoConversion" class="mb-4 p-4 bg-gradient-to-br from-orange-500/10 to-yellow-500/10 border border-orange-500/30 rounded-xl">
              <div class="text-center">
                <p class="text-xs text-orange-300 mb-2">Amount to Send</p>
                <div class="text-2xl font-bold text-white mb-1">{{ cryptoConversion.usdtAmount?.toFixed(2) || '0.00' }} USDT</div>
                <p class="text-xs text-gray-400">{{ cryptoConversion.conversionDisplay }}</p>
              </div>
            </div>

            <!-- QR Code -->
            <div class="flex justify-center mb-4">
              <div class="p-4 bg-white rounded-xl">
                <img :src="generateQRCode(wallet)" alt="Wallet QR Code" class="w-48 h-48" />
              </div>
            </div>

            <!-- Wallet Details -->
            <div class="space-y-3">
              <div class="flex justify-between items-center gap-4">
                <div class="flex-1">
                  <p class="text-xs text-purple-300">Coin</p>
                  <p class="text-white font-semibold">{{ wallet.coin_name }} ({{ wallet.network }})</p>
                </div>
                <button @click="copyToClipboard(wallet.coin_name)" class="px-3 py-1.5 bg-purple-500/20 hover:bg-purple-500/30 rounded-lg text-xs text-purple-200">
                  Copy
                </button>
              </div>

              <div class="flex justify-between items-center gap-4">
                <div class="flex-1">
                  <p class="text-xs text-purple-300">Wallet Address</p>
                  <p class="text-white font-mono text-sm break-all">{{ wallet.address }}</p>
                </div>
                <button @click="copyToClipboard(wallet.address)" class="px-3 py-1.5 bg-purple-500/20 hover:bg-purple-500/30 rounded-lg text-xs text-purple-200 flex-shrink-0">
                  Copy
                </button>
              </div>

              <div v-if="wallet.special_note" class="p-3 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                <p class="text-xs text-yellow-200">{{ wallet.special_note }}</p>
              </div>
            </div>
      </div>
    </div>

    <!-- Instructions -->
    <div class="bg-blue-500/10 backdrop-blur-xl rounded-2xl border border-blue-500/30 p-6 mb-6">
      <h4 class="text-sm font-bold text-blue-300 mb-3">Payment Instructions:</h4>
      <ol class="text-sm text-gray-300 space-y-2 list-decimal list-inside">
        <li>Transfer the exact amount to the {{ paymentMethod === 'bank_transfer' ? 'account' : 'wallet' }} above</li>
        <li v-if="paymentMethod === 'bank_transfer'">Use your phone number as reference</li>
        <li>Click "I Have Paid" button below after completing payment</li>
        <li>Wait 2-4 hours for verification (may be faster)</li>
      </ol>
    </div>

    <!-- Action Button -->
    <button
      v-if="!existingTransaction"
      @click="confirmPayment"
      :disabled="processing"
      class="w-full py-4 bg-gradient-to-r from-pink-500 to-purple-500 hover:from-pink-600 hover:to-purple-600 text-white font-bold rounded-xl transition-all duration-300 shadow-lg shadow-pink-500/30 disabled:opacity-50 disabled:cursor-not-allowed mb-4"
    >
      {{ processing ? 'Processing...' : 'I Have Paid' }}
    </button>

    <button
      v-else
      @click="router.visit('/dashboard')"
      class="w-full py-4 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold rounded-xl transition-all duration-300 mb-4"
    >
      Back to Dashboard
    </button>

    <p class="text-center text-xs text-gray-400">
      Need help? Contact our support team for assistance
    </p>
  </UserLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
  plan: Object,
  paymentMethod: String,
  paymentAccounts: Array,
  currencySymbol: String,
  cryptoConversion: Object,
  existingTransaction: Boolean,
  isUpgrade: {
    type: Boolean,
    default: false
  },
  discountPercentage: Number,
  originalPrice: Number,
  discountedPrice: Number,
});

const processing = ref(false);
const copied = ref(false);

// Get the actual amount to pay (discounted price for upgrades)
const amountToPay = computed(() => {
  return props.isUpgrade && props.discountedPrice ? props.discountedPrice : props.plan.price;
});

const formatCurrency = (amount) => {
  return (props.currencySymbol || '₦') + parseFloat(amount).toLocaleString();
};

const generateQRCode = (account) => {
  let qrData = '';

  if (props.paymentMethod === 'bank_transfer') {
    qrData = `Bank: ${account.bank_name}\nAccount: ${account.account_number}\nName: ${account.account_name}\nAmount: ${amountToPay.value}`;
  } else {
    qrData = account.address;
  }

  return `/qr-code?data=${encodeURIComponent(qrData)}`;
};

const copyToClipboard = async (text) => {
  try {
    await navigator.clipboard.writeText(text);
    copied.value = true;
    setTimeout(() => copied.value = false, 2000);
  } catch (err) {
    console.error('Failed to copy:', err);
  }
};

const confirmPayment = () => {
  processing.value = true;

  // Use different endpoint for upgrades vs regular activation
  const endpoint = props.isUpgrade ? '/plan/upgrade/confirm' : '/payment/confirm';

  router.post(endpoint, {}, {
    onFinish: () => processing.value = false,
  });
};
</script>
