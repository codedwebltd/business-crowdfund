<template>
  <form @submit.prevent="save">
    <!-- Withdrawal Limits by Rank -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-indigo-500 to-blue-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">Financial Settings</h2>
            <p class="text-indigo-100 text-xs sm:text-sm">Withdrawal limits and payment gateways</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <!-- Withdrawal Limits by Rank -->
        <div v-for="(limits, rankId) in form.withdrawal_limits_by_rank" :key="rankId">
          <h3 class="text-sm font-bold text-gray-900 mb-3">Rank {{ rankId }} Limits</h3>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Min Withdrawal</label>
              <input v-model.number="limits.min" type="number" min="0" step="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Max Withdrawal</label>
              <input v-model.number="limits.max" type="number" min="0" step="1000" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block">Per Day</label>
              <input v-model.number="limits.per_day" type="number" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
            </div>
          </div>
        </div>

        <!-- Payment Gateways -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3">Payment Gateways</h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <span class="text-sm font-medium">Bank Transfer</span>
              <button type="button" @click="form.payment_gateways.bank_transfer.enabled = !form.payment_gateways.bank_transfer.enabled" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.payment_gateways.bank_transfer.enabled ? 'bg-green-500' : 'bg-gray-300']">
                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.payment_gateways.bank_transfer.enabled ? 'translate-x-6' : 'translate-x-1']"></span>
              </button>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <span class="text-sm font-medium">Crypto Transfer</span>
              <button type="button" @click="form.payment_gateways.crypto_transfer.enabled = !form.payment_gateways.crypto_transfer.enabled" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.payment_gateways.crypto_transfer.enabled ? 'bg-green-500' : 'bg-gray-300']">
                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.payment_gateways.crypto_transfer.enabled ? 'translate-x-6' : 'translate-x-1']"></span>
              </button>
            </div>
          </div>
        </div>

        <!-- Global Withdrawal Settings -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3">Global Withdrawal Settings</h3>
          <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                <span>Minimum Withdrawal</span>
                <Tooltip text="Global minimum withdrawal amount across all ranks. Example: 1000" />
              </label>
              <input v-model.number="form.minimum_withdrawal" type="number" min="0" step="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                <span>Maximum Withdrawal</span>
                <Tooltip text="Global maximum withdrawal amount across all ranks. Example: 500000" />
              </label>
              <input v-model.number="form.maximum_withdrawal" type="number" min="0" step="1000" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                <span>Withdrawals Per Day</span>
                <Tooltip text="Global maximum withdrawals allowed per day. Example: 1" />
              </label>
              <input v-model.number="form.withdrawals_per_day" type="number" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                <span>Referral Threshold</span>
                <Tooltip text="Minimum active referrals required to withdraw. Set to 0 to disable. Example: 3" />
              </label>
              <input v-model.number="form.referral_threshold" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
            </div>
          </div>
        </div>

        <!-- Withdrawal Processing Times -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
            <span>Withdrawal Processing Times</span>
            <Tooltip text="Expected time to process withdrawals by rank. Example: bronze: 48-72 hours, diamond: instant" />
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div v-for="(time, rankId) in form.withdrawal_processing_times" :key="rankId">
              <label class="text-xs text-gray-600 mb-1 block capitalize">Rank {{ rankId }}</label>
              <input v-model="form.withdrawal_processing_times[rankId]" type="text" placeholder="24-48 hours" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
            </div>
          </div>
        </div>

        <!-- Bank Accounts -->
        <div>
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
              <span>Bank Accounts</span>
              <Tooltip text="Add your company bank accounts for manual transfers. Users will see these when paying." />
            </h3>
            <button type="button" @click="addBankAccount" class="text-xs px-3 py-1.5 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600">
              + Add Account
            </button>
          </div>
          <div v-for="(account, index) in form.bank_accounts" :key="index" class="bg-gray-50 rounded-lg p-4 mb-3 relative">
            <button type="button" @click="removeBankAccount(index)" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="text-xs text-gray-600 mb-1 block">Bank Name</label>
                <input v-model="account.bank_name" type="text" placeholder="GTBank" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
              </div>
              <div>
                <label class="text-xs text-gray-600 mb-1 block">Account Number</label>
                <input v-model="account.account_number" type="text" placeholder="0123456789" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
              </div>
              <div>
                <label class="text-xs text-gray-600 mb-1 block">Account Name</label>
                <input v-model="account.account_name" type="text" placeholder="QivioTalk Limited" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
              </div>
              <div>
                <label class="text-xs text-gray-600 mb-1 block">Special Note (Optional)</label>
                <input v-model="account.special_note" type="text" placeholder="Use your phone as reference" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
              </div>
            </div>
          </div>
          <p v-if="!form.bank_accounts || form.bank_accounts.length === 0" class="text-sm text-gray-500 italic">No bank accounts added yet.</p>
        </div>

        <!-- Crypto Wallets -->
        <div>
          <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
              <span>Crypto Wallets</span>
              <Tooltip text="Add your crypto wallet addresses. QR codes will be generated automatically." />
            </h3>
            <button type="button" @click="addCryptoWallet" class="text-xs px-3 py-1.5 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600">
              + Add Wallet
            </button>
          </div>
          <div v-for="(wallet, index) in form.crypto_wallets" :key="index" class="bg-gray-50 rounded-lg p-4 mb-3 relative">
            <button type="button" @click="removeCryptoWallet(index)" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="text-xs text-gray-600 mb-1 block">Coin Name</label>
                <input v-model="wallet.coin_name" type="text" placeholder="USDT" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
              </div>
              <div>
                <label class="text-xs text-gray-600 mb-1 block">Network</label>
                <input v-model="wallet.network" type="text" placeholder="TRC20" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
              </div>
              <div class="sm:col-span-2">
                <label class="text-xs text-gray-600 mb-1 block">Wallet Address</label>
                <input v-model="wallet.address" type="text" placeholder="TYourWalletAddressHere" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono" />
              </div>
              <div class="sm:col-span-2">
                <label class="text-xs text-gray-600 mb-1 block">Special Note (Optional)</label>
                <input v-model="wallet.special_note" type="text" placeholder="Minimum: $10" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
              </div>
            </div>
          </div>
          <p v-if="!form.crypto_wallets || form.crypto_wallets.length === 0" class="text-sm text-gray-500 italic">No crypto wallets added yet.</p>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-indigo-500 to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save Financial Settings' }}
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
  withdrawal_limits_by_rank: props.settings.withdrawal_limits_by_rank || {},
  payment_gateways: props.settings.payment_gateways || {},
  bank_accounts: props.settings.bank_accounts || [],
  crypto_wallets: props.settings.crypto_wallets || [],
  minimum_withdrawal: props.settings.minimum_withdrawal || 1000,
  maximum_withdrawal: props.settings.maximum_withdrawal || 500000,
  withdrawals_per_day: props.settings.withdrawals_per_day || 1,
  referral_threshold: props.settings.referral_threshold || 0,
  withdrawal_processing_times: props.settings.withdrawal_processing_times || {}
});

const addBankAccount = () => {
  form.bank_accounts.push({
    bank_name: '',
    account_number: '',
    account_name: '',
    special_note: ''
  });
};

const removeBankAccount = (index) => {
  form.bank_accounts.splice(index, 1);
};

const addCryptoWallet = () => {
  form.crypto_wallets.push({
    coin_name: '',
    network: '',
    address: '',
    special_note: ''
  });
};

const removeCryptoWallet = (index) => {
  form.crypto_wallets.splice(index, 1);
};

const save = () => {
  saving.value = true;
  router.post('/admin/settings/financial', form, {
    preserveScroll: true,
    onSuccess: () => { saving.value = false; emit('saved'); },
    onError: () => { saving.value = false; }
  });
};
</script>
