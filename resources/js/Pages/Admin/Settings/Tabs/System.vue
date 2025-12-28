<template>
  <form @submit.prevent="save">
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-gray-500 to-gray-700">
        <h2 class="text-base sm:text-lg font-bold text-white">System Controls</h2>
      </div>
      <div class="p-4 sm:p-6 space-y-4">
        <div v-for="(value, key) in form" :key="key">
          <div v-if="typeof value === 'boolean'" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <span class="text-sm font-medium capitalize">{{ key.replace(/_/g, ' ') }}</span>
            <button type="button" @click="form[key] = !form[key]" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form[key] ? 'bg-green-500' : 'bg-gray-300']">
              <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form[key] ? 'translate-x-6' : 'translate-x-1']"></span>
            </button>
          </div>
          <div v-else class="space-y-2">
            <label class="text-sm font-medium capitalize text-gray-700">{{ key.replace(/_/g, ' ') }}</label>
            <input v-model="form[key]" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" />
          </div>
        </div>
        <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-gray-500 to-gray-700 text-white font-semibold rounded-xl">{{ saving ? 'Saving...' : 'Save' }}</button>
      </div>
    </div>
  </form>
</template>
<script setup>
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';
const props = defineProps({ settings: Object });
const emit = defineEmits(['saved']);
const saving = ref(false);
const form = reactive({
  maintenance_mode: props.settings.maintenance_mode ?? false,
  new_registrations_enabled: props.settings.new_registrations_enabled ?? true,
  withdrawals_enabled: props.settings.withdrawals_enabled ?? true,
  referral_bonuses_enabled: props.settings.referral_bonuses_enabled ?? true,
  total_members: props.settings.total_members || '0',
});
const save = () => {
  saving.value = true;
  router.post('/admin/settings/system', form, { preserveScroll: true, onSuccess: () => { saving.value = false; emit('saved'); }, onError: () => { saving.value = false; } });
};
</script>
