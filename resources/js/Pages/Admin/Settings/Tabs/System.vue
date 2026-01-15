<template>
  <form @submit.prevent="save">
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-gray-500 to-gray-700">
        <h2 class="text-base sm:text-lg font-bold text-white">System Controls</h2>
      </div>
      <div class="p-4 sm:p-6 space-y-4">
        <!-- Maintenance Mode -->
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
          <span class="text-sm font-medium">Maintenance Mode</span>
          <button type="button" @click="form.maintenance_mode = !form.maintenance_mode" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.maintenance_mode ? 'bg-orange-500' : 'bg-gray-300']">
            <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.maintenance_mode ? 'translate-x-6' : 'translate-x-1']"></span>
          </button>
        </div>

        <!-- Maintenance End Date (shown when maintenance is ON) -->
        <div v-if="form.maintenance_mode" class="space-y-2 p-4 bg-orange-50 border border-orange-200 rounded-lg">
          <label class="text-sm font-medium text-gray-700">Maintenance End Date & Time</label>
          <input v-model="form.maintenance_end_at" type="datetime-local" class="w-full px-3 py-2 border border-orange-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500" />
          <p class="text-xs text-gray-500">This will be shown to users on the maintenance page</p>
        </div>

        <!-- Other System Controls -->
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
          <span class="text-sm font-medium">New Registrations Enabled</span>
          <button type="button" @click="form.new_registrations_enabled = !form.new_registrations_enabled" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.new_registrations_enabled ? 'bg-green-500' : 'bg-gray-300']">
            <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.new_registrations_enabled ? 'translate-x-6' : 'translate-x-1']"></span>
          </button>
        </div>

        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
          <span class="text-sm font-medium">Withdrawals Enabled</span>
          <button type="button" @click="form.withdrawals_enabled = !form.withdrawals_enabled" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.withdrawals_enabled ? 'bg-green-500' : 'bg-gray-300']">
            <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.withdrawals_enabled ? 'translate-x-6' : 'translate-x-1']"></span>
          </button>
        </div>

        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
          <span class="text-sm font-medium">Referral Bonuses Enabled</span>
          <button type="button" @click="form.referral_bonuses_enabled = !form.referral_bonuses_enabled" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.referral_bonuses_enabled ? 'bg-green-500' : 'bg-gray-300']">
            <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.referral_bonuses_enabled ? 'translate-x-6' : 'translate-x-1']"></span>
          </button>
        </div>

        <!-- Total Members -->
        <div class="space-y-2">
          <label class="text-sm font-medium text-gray-700">Total Members (Display)</label>
          <input v-model="form.total_members" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
        </div>

        <!-- Admin IP Whitelist -->
        <div class="space-y-2 p-4 bg-blue-50 border border-blue-200 rounded-lg">
          <label class="text-sm font-medium text-gray-700">Admin IP Whitelist</label>
          <textarea v-model="ipWhitelistText" rows="3" placeholder="Enter IP addresses (one per line) or CIDR ranges&#10;Example:&#10;192.168.1.1&#10;10.0.0.0/24" class="w-full px-3 py-2 border border-blue-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 font-mono"></textarea>
          <p class="text-xs text-gray-500">Only these IPs can access admin panel. Leave empty to allow all. Use <code class="bg-gray-200 px-1 rounded">?admin_token=Galaxys24ultras.</code> for recovery</p>
        </div>

        <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-gray-500 to-gray-700 text-white font-semibold rounded-xl hover:from-gray-600 hover:to-gray-800 transition-all">
          {{ saving ? 'Saving...' : 'Save System Settings' }}
        </button>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({ settings: Object });
const emit = defineEmits(['saved']);
const saving = ref(false);

const form = reactive({
  maintenance_mode: props.settings.maintenance_mode ?? false,
  maintenance_end_at: props.settings.maintenance_end_at || '',
  admin_ip_whitelist: props.settings.admin_ip_whitelist || [],
  new_registrations_enabled: props.settings.new_registrations_enabled ?? true,
  withdrawals_enabled: props.settings.withdrawals_enabled ?? true,
  referral_bonuses_enabled: props.settings.referral_bonuses_enabled ?? true,
  total_members: props.settings.total_members || '0',
});

// Convert IP whitelist array to textarea text
const ipWhitelistText = computed({
  get() {
    return Array.isArray(form.admin_ip_whitelist) ? form.admin_ip_whitelist.join('\n') : '';
  },
  set(value) {
    form.admin_ip_whitelist = value.split('\n').map(ip => ip.trim()).filter(ip => ip.length > 0);
  }
});

const save = () => {
  saving.value = true;
  router.post('/admin/settings/system', form, {
    preserveScroll: true,
    onSuccess: () => {
      saving.value = false;
      emit('saved');
    },
    onError: () => {
      saving.value = false;
    }
  });
};
</script>
