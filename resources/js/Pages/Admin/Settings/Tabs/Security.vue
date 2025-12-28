<template>
  <form @submit.prevent="save">
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-blue-500 to-indigo-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">Security & reCAPTCHA</h2>
            <p class="text-blue-100 text-xs sm:text-sm">Configure Google reCAPTCHA v2 for fraud prevention</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <!-- reCAPTCHA Status -->
        <div class="flex items-center justify-between p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
          <div>
            <span class="text-sm font-bold text-gray-900">Enable reCAPTCHA Protection</span>
            <p class="text-xs text-gray-600 mt-1">Protect against automated bots and fraud</p>
          </div>
          <button type="button" @click="form.recaptcha_enabled = !form.recaptcha_enabled" :class="['relative inline-flex h-7 w-14 items-center rounded-full transition-colors', form.recaptcha_enabled ? 'bg-green-500' : 'bg-gray-300']">
            <span :class="['inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform', form.recaptcha_enabled ? 'translate-x-8' : 'translate-x-1']"></span>
          </button>
        </div>

        <!-- reCAPTCHA API Keys -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3">Google reCAPTCHA v2 Keys</h3>
          <div class="space-y-4">
            <div>
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                Site Key (Public Key)
                <Tooltip text="Get this from Google reCAPTCHA Admin Console" />
              </label>
              <input
                v-model="form.recaptcha_site_key"
                type="text"
                placeholder="6LdRdjYsAAAAAE3zA_OUJ-imHst0J1sb3cgz-_TT"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
              />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                Secret Key (Private Key)
                <Tooltip text="Keep this secret - used for server-side verification" />
              </label>
              <input
                v-model="form.recaptcha_secret_key"
                type="password"
                placeholder="6LdRdjYsAAAAADjg_ltQkejrEccBk6eUfxcjyEsn"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono"
              />
            </div>
          </div>
        </div>

        <!-- Trigger Settings -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3">When to Show reCAPTCHA</h3>
          <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
            <div>
              <span class="text-sm font-medium text-gray-900">Trigger on Fraud Detection</span>
              <p class="text-xs text-gray-500">Show CAPTCHA when suspicious activity detected</p>
            </div>
            <button type="button" @click="form.recaptcha_trigger_on_fraud = !form.recaptcha_trigger_on_fraud" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.recaptcha_trigger_on_fraud ? 'bg-green-500' : 'bg-gray-300']">
              <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.recaptcha_trigger_on_fraud ? 'translate-x-6' : 'translate-x-1']"></span>
            </button>
          </div>
        </div>

        <!-- Setup Instructions -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 border border-gray-200">
          <h3 class="text-sm font-bold text-gray-900 mb-2 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            How to Get reCAPTCHA Keys
          </h3>
          <ol class="text-xs text-gray-700 space-y-2 ml-6 list-decimal">
            <li>Visit <a href="https://www.google.com/recaptcha/admin" target="_blank" class="text-blue-600 hover:underline font-medium">Google reCAPTCHA Admin Console</a></li>
            <li>Click "+" to register a new site</li>
            <li>Choose <strong>reCAPTCHA v2</strong> → "I'm not a robot" Checkbox</li>
            <li>Add your domain (e.g., business.qiviotalk.online)</li>
            <li>Copy the <strong>Site Key</strong> and <strong>Secret Key</strong> here</li>
          </ol>
        </div>

        <!-- Fraud Integration Info -->
        <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-lg p-4 border border-orange-200">
          <h3 class="text-sm font-bold text-gray-900 mb-2 flex items-center gap-2">
            <svg class="w-4 h-4 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            reCAPTCHA Triggers
          </h3>
          <p class="text-xs text-gray-700 mb-2">reCAPTCHA will automatically appear when:</p>
          <ul class="text-xs text-gray-600 space-y-1 ml-6 list-disc">
            <li>User completes tasks too quickly (bot speed detected)</li>
            <li>Too many tasks completed in one hour (velocity abuse)</li>
            <li>Suspicious survey answer patterns detected</li>
            <li>After multiple fraud warnings</li>
            <li>Any fraud incident triggers requiring verification</li>
          </ul>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save Security Settings' }}
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
  recaptcha_site_key: props.settings.recaptcha_site_key || '',
  recaptcha_secret_key: props.settings.recaptcha_secret_key || '',
  recaptcha_enabled: props.settings.recaptcha_enabled || false,
  recaptcha_trigger_on_fraud: props.settings.recaptcha_trigger_on_fraud !== undefined ? props.settings.recaptcha_trigger_on_fraud : true
});

const save = () => {
  saving.value = true;
  router.post('/admin/settings/security', form, {
    preserveScroll: true,
    onSuccess: () => { saving.value = false; emit('saved'); },
    onError: () => { saving.value = false; }
  });
};
</script>
