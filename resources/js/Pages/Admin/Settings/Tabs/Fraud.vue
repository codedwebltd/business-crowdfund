<template>
  <form @submit.prevent="save">
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-red-500 to-pink-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">Fraud Detection & Security</h2>
            <p class="text-red-100 text-xs sm:text-sm">Protect your platform from abuse</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <!-- Detection Thresholds -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3">Detection Thresholds</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                Max Tasks/Hour
                <Tooltip text="Maximum tasks user can complete per hour (velocity abuse detection)" />
              </label>
              <input v-model.number="form.fraud_detection_rules.max_tasks_per_hour" type="number" min="1" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-red-500" />
            </div>
            <div>
              <label class="text-xs text-gray-600 mb-1 block flex items-center gap-1">
                Min Task Time (seconds)
                <Tooltip text="Minimum seconds to complete task (bot speed detection)" />
              </label>
              <input v-model.number="form.fraud_detection_rules.min_task_completion_time" type="number" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-red-500" />
            </div>
          </div>
        </div>

        <!-- Progressive Penalties -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3">Progressive Penalties</h3>
          <div class="space-y-3 bg-gradient-to-br from-orange-50 to-red-50 p-4 rounded-lg border border-red-200">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center text-xs font-bold">1</div>
              <div>
                <p class="text-sm font-semibold text-gray-900">First Offense</p>
                <p class="text-xs text-gray-600">Warning email sent, task rejected</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 bg-orange-500 text-white rounded-full flex items-center justify-center text-xs font-bold">2</div>
              <div>
                <p class="text-sm font-semibold text-gray-900">Second Offense</p>
                <p class="text-xs text-gray-600">48-hour task ban (automated)</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center text-xs font-bold">3</div>
              <div>
                <p class="text-sm font-semibold text-gray-900">Third Offense</p>
                <p class="text-xs text-gray-600">7-day ban requiring manual review</p>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 bg-gray-900 text-white rounded-full flex items-center justify-center text-xs font-bold">∞</div>
              <div>
                <p class="text-sm font-semibold text-gray-900">Persistent Violations</p>
                <p class="text-xs text-gray-600">Permanent ban (admin action required)</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Active Fraud Detection Features -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3">Active Detection Methods</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
              <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-semibold text-gray-900">Bot Speed Detection</span>
              </div>
              <p class="text-xs text-gray-600">Tasks completed too quickly</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
              <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-semibold text-gray-900">Velocity Abuse</span>
              </div>
              <p class="text-xs text-gray-600">Too many tasks per hour</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
              <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-semibold text-gray-900">Pattern Abuse</span>
              </div>
              <p class="text-xs text-gray-600">Survey answer patterns (all same)</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
              <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-semibold text-gray-900">Video Validation</span>
              </div>
              <p class="text-xs text-gray-600">90% minimum watch time required</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
              <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-semibold text-gray-900">Review Length Check</span>
              </div>
              <p class="text-xs text-gray-600">Minimum character requirements</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
              <div class="flex items-center gap-2 mb-1">
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-semibold text-gray-900">Ban Enforcement</span>
              </div>
              <p class="text-xs text-gray-600">Active task_ban_until checks</p>
            </div>
          </div>
        </div>

        <!-- Monitoring Toggles -->
        <div>
          <h3 class="text-sm font-bold text-gray-900 mb-3">Additional Monitoring</h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
              <div>
                <span class="text-sm font-medium text-gray-900">Device Fingerprinting</span>
                <p class="text-xs text-gray-500">Track unique device identifiers (future implementation)</p>
              </div>
              <button type="button" @click="form.fraud_detection_rules.device_fingerprinting_enabled = !form.fraud_detection_rules.device_fingerprinting_enabled" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.fraud_detection_rules.device_fingerprinting_enabled ? 'bg-green-500' : 'bg-gray-300']">
                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.fraud_detection_rules.device_fingerprinting_enabled ? 'translate-x-6' : 'translate-x-1']"></span>
              </button>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
              <div>
                <span class="text-sm font-medium text-gray-900">IP Monitoring</span>
                <p class="text-xs text-gray-500">Detect multiple accounts from same IP (future implementation)</p>
              </div>
              <button type="button" @click="form.fraud_detection_rules.ip_monitoring_enabled = !form.fraud_detection_rules.ip_monitoring_enabled" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.fraud_detection_rules.ip_monitoring_enabled ? 'bg-green-500' : 'bg-gray-300']">
                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.fraud_detection_rules.ip_monitoring_enabled ? 'translate-x-6' : 'translate-x-1']"></span>
              </button>
            </div>
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-red-500 to-pink-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50">
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
  fraud_detection_rules: props.settings.fraud_detection_rules || {}
});

const save = () => {
  saving.value = true;
  router.post('/admin/settings/fraud', form, {
    preserveScroll: true,
    onSuccess: () => { saving.value = false; emit('saved'); },
    onError: () => { saving.value = false; }
  });
};
</script>
