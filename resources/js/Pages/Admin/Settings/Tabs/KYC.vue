<template>
  <form @submit.prevent="save">
    <!-- KYC Requirements -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-teal-500 to-cyan-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">KYC Requirements</h2>
            <p class="text-teal-100 text-xs sm:text-sm">Know Your Customer verification settings</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <div>
          <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
            <span>KYC Withdrawal Threshold (₦)</span>
            <Tooltip text="Minimum withdrawal amount requiring KYC verification. Example: 50000 = withdrawals above ₦50k need KYC" />
          </label>
          <input v-model.number="form.kyc_withdrawal_threshold" type="number" min="0" step="1000" placeholder="50000" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-sm" />
        </div>

        <!-- Enable KYC on First Withdrawal -->
        <div class="flex items-center justify-between p-4 bg-teal-50 rounded-lg border border-teal-200">
          <div>
            <span class="text-sm font-semibold text-gray-900">Require KYC on First Withdrawal</span>
            <p class="text-xs text-gray-500 mt-1">Force users to complete KYC before their first withdrawal regardless of amount</p>
          </div>
          <button type="button" @click="form.enable_kyc_on_first_withdrawal = !form.enable_kyc_on_first_withdrawal" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.enable_kyc_on_first_withdrawal ? 'bg-green-500' : 'bg-gray-300']">
            <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.enable_kyc_on_first_withdrawal ? 'translate-x-6' : 'translate-x-1']"></span>
          </button>
        </div>

        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3">Document Requirements</h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div>
                <span class="text-sm font-medium text-gray-900">NIN (National ID)</span>
                <p class="text-xs text-gray-500">Require National Identification Number</p>
              </div>
              <button type="button" @click="form.kyc_requirements.nin_required = !form.kyc_requirements.nin_required" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.kyc_requirements.nin_required ? 'bg-green-500' : 'bg-gray-300']">
                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.kyc_requirements.nin_required ? 'translate-x-6' : 'translate-x-1']"></span>
              </button>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div>
                <span class="text-sm font-medium text-gray-900">BVN (Bank Verification)</span>
                <p class="text-xs text-gray-500">Require Bank Verification Number</p>
              </div>
              <button type="button" @click="form.kyc_requirements.bvn_required = !form.kyc_requirements.bvn_required" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.kyc_requirements.bvn_required ? 'bg-green-500' : 'bg-gray-300']">
                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.kyc_requirements.bvn_required ? 'translate-x-6' : 'translate-x-1']"></span>
              </button>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div>
                <span class="text-sm font-medium text-gray-900">Utility Bill</span>
                <p class="text-xs text-gray-500">Require recent utility bill upload</p>
              </div>
              <button type="button" @click="form.kyc_requirements.utility_bill_required = !form.kyc_requirements.utility_bill_required" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.kyc_requirements.utility_bill_required ? 'bg-green-500' : 'bg-gray-300']">
                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.kyc_requirements.utility_bill_required ? 'translate-x-6' : 'translate-x-1']"></span>
              </button>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
              <div>
                <span class="text-sm font-medium text-gray-900">Selfie Verification</span>
                <p class="text-xs text-gray-500">Require selfie with ID document</p>
              </div>
              <button type="button" @click="form.kyc_requirements.selfie_required = !form.kyc_requirements.selfie_required" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.kyc_requirements.selfie_required ? 'bg-green-500' : 'bg-gray-300']">
                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.kyc_requirements.selfie_required ? 'translate-x-6' : 'translate-x-1']"></span>
              </button>
            </div>
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-teal-500 to-cyan-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save KYC Settings' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Testimonial Settings -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-amber-500 to-yellow-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">Testimonial Settings</h2>
            <p class="text-amber-100 text-xs sm:text-sm">Force testimonials for social proof</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
          <div>
            <span class="text-sm font-semibold text-gray-900">Require Testimonial on First Withdrawal</span>
            <p class="text-xs text-gray-500 mt-1">Force users to submit testimonial before first withdrawal is processed. Builds social proof.</p>
          </div>
          <button type="button" @click="form.require_testimonial_first_withdrawal = !form.require_testimonial_first_withdrawal" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.require_testimonial_first_withdrawal ? 'bg-green-500' : 'bg-gray-300']">
            <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.require_testimonial_first_withdrawal ? 'translate-x-6' : 'translate-x-1']"></span>
          </button>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-yellow-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save Testimonial Settings' }}
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
import Swal from 'sweetalert2';

const props = defineProps({ settings: Object });
const emit = defineEmits(['saved']);
const saving = ref(false);

const form = reactive({
  kyc_withdrawal_threshold: props.settings.kyc_withdrawal_threshold || 50000,
  enable_kyc_on_first_withdrawal: props.settings.enable_kyc_on_first_withdrawal ?? false,
  kyc_requirements: props.settings.kyc_requirements || {
    nin_required: false,
    bvn_required: false,
    utility_bill_required: false,
    selfie_required: false
  },
  require_testimonial_first_withdrawal: props.settings.require_testimonial_first_withdrawal ?? false,
});

const save = () => {
  saving.value = true;
  router.post('/admin/settings/kyc', form, {
    preserveScroll: true,
    onSuccess: () => {
      saving.value = false;
      Swal.fire({
        icon: 'success',
        title: 'Settings Saved!',
        text: 'KYC settings have been updated successfully.',
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
      });
    },
    onError: () => {
      saving.value = false;
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: 'Failed to save settings. Please try again.',
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
      });
    }
  });
};
</script>
