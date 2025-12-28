<template>
  <div class="bg-gray-900 rounded-2xl border border-white/20 p-6 max-w-md w-full">
    <h3 class="text-xl font-bold text-white mb-2">Two-Factor Authentication</h3>
    <p class="text-gray-300 mb-4">{{ message }}</p>

    <div v-if="!useBackupCode">
      <input
        v-model="code"
        type="text"
        placeholder="Enter 6-digit code"
        maxlength="6"
        class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 text-center text-lg tracking-widest mb-4 focus:outline-none focus:border-orange-500"
        @keyup.enter="verify"
      />
      <button
        @click="verify"
        :disabled="loading"
        class="w-full bg-gradient-to-r from-orange-500 to-purple-600 text-white py-3 rounded-lg font-semibold flex items-center justify-center gap-2 mb-3 disabled:opacity-50"
      >
        <svg v-if="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ loading ? 'Verifying...' : 'Verify' }}
      </button>
      <button @click="useBackupCode = true" class="w-full text-orange-400 text-sm hover:text-orange-300">
        Use backup code instead
      </button>
    </div>

    <div v-else>
      <input
        v-model="backupCodeInput"
        type="text"
        placeholder="XXXX-XXXX"
        maxlength="9"
        class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 text-center text-lg tracking-widest mb-4 focus:outline-none focus:border-orange-500"
        @keyup.enter="verifyBackup"
      />
      <button
        @click="verifyBackup"
        :disabled="loading"
        class="w-full bg-gradient-to-r from-orange-500 to-purple-600 text-white py-3 rounded-lg font-semibold flex items-center justify-center gap-2 mb-3 disabled:opacity-50"
      >
        <svg v-if="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ loading ? 'Verifying...' : 'Verify Backup Code' }}
      </button>
      <button @click="useBackupCode = false" class="w-full text-orange-400 text-sm hover:text-orange-300">
        Use authenticator code instead
      </button>
    </div>

    <button v-if="showCancel" @click="$emit('cancel')" class="w-full bg-gray-600 text-white py-2 rounded-lg font-semibold mt-3">
      Cancel
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  message: {
    type: String,
    default: 'Enter the 6-digit code from your authenticator app'
  },
  showCancel: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['verify', 'verifyBackup', 'cancel']);

const code = ref('');
const backupCodeInput = ref('');
const useBackupCode = ref(false);
const loading = ref(false);

const verify = () => {
  if (code.value.length !== 6) return;
  loading.value = true;
  emit('verify', code.value, () => loading.value = false);
};

const verifyBackup = () => {
  if (backupCodeInput.value.length < 8) return;
  loading.value = true;
  emit('verifyBackup', backupCodeInput.value, () => loading.value = false);
};

defineExpose({ loading });
</script>
