<template>
  <form @submit.prevent="save">
    <!-- Notification Channels -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-blue-500 to-indigo-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">Notification Channels</h2>
            <p class="text-blue-100 text-xs sm:text-sm">Configure notification delivery methods</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <div class="space-y-4">
          <!-- Email Notifications -->
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div>
              <span class="text-sm font-semibold text-gray-900">Email Notifications</span>
              <p class="text-xs text-gray-500 mt-1">Send notifications via email (requires SMTP configuration)</p>
            </div>
            <button type="button" @click="form.email_notifications_enabled = !form.email_notifications_enabled" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.email_notifications_enabled ? 'bg-green-500' : 'bg-gray-300']">
              <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.email_notifications_enabled ? 'translate-x-6' : 'translate-x-1']"></span>
            </button>
          </div>

          <!-- SMS Notifications -->
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div>
              <span class="text-sm font-semibold text-gray-900">SMS Notifications</span>
              <p class="text-xs text-gray-500 mt-1">Send notifications via SMS (requires SMS gateway)</p>
            </div>
            <button type="button" @click="form.sms_notifications_enabled = !form.sms_notifications_enabled" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.sms_notifications_enabled ? 'bg-green-500' : 'bg-gray-300']">
              <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.sms_notifications_enabled ? 'translate-x-6' : 'translate-x-1']"></span>
            </button>
          </div>

          <!-- Firebase Push Notifications -->
          <div class="p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center justify-between mb-3">
              <div>
                <span class="text-sm font-semibold text-gray-900">Firebase Push Notifications</span>
                <p class="text-xs text-gray-500 mt-1">Push notifications for mobile apps via Firebase Cloud Messaging</p>
              </div>
              <button type="button" @click="form.notification_channels.firebase.enabled = !form.notification_channels.firebase.enabled" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.notification_channels.firebase.enabled ? 'bg-green-500' : 'bg-gray-300']">
                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.notification_channels.firebase.enabled ? 'translate-x-6' : 'translate-x-1']"></span>
              </button>
            </div>
            <div v-if="form.notification_channels.firebase.enabled" class="space-y-3 pt-3 border-t border-gray-200">
              <div>
                <label class="text-xs font-medium text-gray-700 mb-1 flex items-start gap-1">
                  <span>Server Key</span>
                  <Tooltip text="Firebase Cloud Messaging server key. Get from Firebase Console > Project Settings > Cloud Messaging" />
                </label>
                <input v-model="form.notification_channels.firebase.server_key" type="password" placeholder="AAAAxxxxxxx:APA91..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono" />
              </div>
            </div>
          </div>

          <!-- WhatsApp Notifications -->
          <div class="p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center justify-between mb-3">
              <div>
                <span class="text-sm font-semibold text-gray-900">WhatsApp Notifications</span>
                <p class="text-xs text-gray-500 mt-1">Send notifications via WhatsApp Business API</p>
              </div>
              <button type="button" @click="form.notification_channels.whatsapp.enabled = !form.notification_channels.whatsapp.enabled" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.notification_channels.whatsapp.enabled ? 'bg-green-500' : 'bg-gray-300']">
                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.notification_channels.whatsapp.enabled ? 'translate-x-6' : 'translate-x-1']"></span>
              </button>
            </div>
            <div v-if="form.notification_channels.whatsapp.enabled" class="space-y-3 pt-3 border-t border-gray-200">
              <div>
                <label class="text-xs font-medium text-gray-700 mb-1 flex items-start gap-1">
                  <span>API Endpoint</span>
                  <Tooltip text="WhatsApp Business API endpoint URL. Example: https://api.whatsapp.com/send" />
                </label>
                <input v-model="form.notification_channels.whatsapp.api_endpoint" type="url" placeholder="https://api.whatsapp.com/send" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono" />
              </div>
              <div>
                <label class="text-xs font-medium text-gray-700 mb-1 flex items-start gap-1">
                  <span>API Token</span>
                  <Tooltip text="WhatsApp Business API authentication token" />
                </label>
                <input v-model="form.notification_channels.whatsapp.api_token" type="password" placeholder="••••••••" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono" />
              </div>
            </div>
          </div>

          <!-- Telegram Notifications -->
          <div class="p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center justify-between mb-3">
              <div>
                <span class="text-sm font-semibold text-gray-900">Telegram Notifications</span>
                <p class="text-xs text-gray-500 mt-1">Send notifications to Telegram group/channel</p>
              </div>
              <button type="button" @click="form.notification_channels.telegram.enabled = !form.notification_channels.telegram.enabled" :class="['relative inline-flex h-6 w-11 items-center rounded-full transition-colors', form.notification_channels.telegram.enabled ? 'bg-green-500' : 'bg-gray-300']">
                <span :class="['inline-block h-4 w-4 transform rounded-full bg-white transition-transform', form.notification_channels.telegram.enabled ? 'translate-x-6' : 'translate-x-1']"></span>
              </button>
            </div>
            <div v-if="form.notification_channels.telegram.enabled" class="space-y-3 pt-3 border-t border-gray-200">
              <div>
                <label class="text-xs font-medium text-gray-700 mb-1 flex items-start gap-1">
                  <span>Bot Token</span>
                  <Tooltip text="Telegram bot token from @BotFather. Example: 123456789:ABCdefGHIjklMNOpqrsTUVwxyz" />
                </label>
                <input v-model="form.notification_channels.telegram.bot_token" type="password" placeholder="123456789:ABC..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono" />
              </div>
              <div>
                <label class="text-xs font-medium text-gray-700 mb-1 flex items-start gap-1">
                  <span>Chat ID</span>
                  <Tooltip text="Telegram chat/channel ID. Use @userinfobot to get ID. Example: -1001234567890" />
                </label>
                <input v-model="form.notification_channels.telegram.chat_id" type="text" placeholder="-1001234567890" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono" />
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save Notification Settings' }}
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
  email_notifications_enabled: props.settings.email_notifications_enabled ?? true,
  sms_notifications_enabled: props.settings.sms_notifications_enabled ?? false,
  notification_channels: props.settings.notification_channels || {
    firebase: { enabled: false, server_key: '' },
    whatsapp: { enabled: false, api_endpoint: '', api_token: '' },
    telegram: { enabled: false, bot_token: '', chat_id: '' }
  }
});

const save = () => {
  saving.value = true;
  router.post('/admin/settings/notifications', form, {
    preserveScroll: true,
    onSuccess: () => { saving.value = false; emit('saved'); },
    onError: () => { saving.value = false; }
  });
};
</script>
