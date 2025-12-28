<template>
  <form @submit.prevent="save">
    <!-- Email Configuration -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-cyan-500 to-blue-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">Email Configuration</h2>
            <p class="text-cyan-100 text-xs sm:text-sm">SMTP settings for sending emails</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Mail Mailer</span>
              <Tooltip text="Email driver to use. smtp = standard mail server, sendmail = local server, mailgun/ses = third party. Example: smtp" />
            </label>
            <select v-model="form.mail_mailer" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 text-sm">
              <option value="smtp">SMTP</option>
              <option value="sendmail">Sendmail</option>
              <option value="mailgun">Mailgun</option>
              <option value="ses">Amazon SES</option>
            </select>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Mail Host</span>
              <Tooltip text="SMTP server address. Example: smtp.gmail.com, smtp.mailtrap.io, mail.yourdomain.com" />
            </label>
            <input v-model="form.mail_host" type="text" placeholder="smtp.gmail.com" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 text-sm font-mono" />
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Mail Port</span>
              <Tooltip text="SMTP port number. Common: 587 (TLS), 465 (SSL), 25 (unencrypted). Use 587 for most providers." />
            </label>
            <input v-model.number="form.mail_port" type="number" placeholder="587" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 text-sm" />
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Mail Encryption</span>
              <Tooltip text="Encryption method. tls = modern secure (port 587), ssl = legacy secure (port 465), null = no encryption" />
            </label>
            <select v-model="form.mail_encryption" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 text-sm">
              <option value="tls">TLS</option>
              <option value="ssl">SSL</option>
              <option value="">None</option>
            </select>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Mail Username</span>
              <Tooltip text="SMTP username, usually your full email address. Example: noreply@crowdpower.com" />
            </label>
            <input v-model="form.mail_username" type="text" placeholder="noreply@yourplatform.com" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 text-sm" />
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Mail Password</span>
              <Tooltip text="SMTP password or app-specific password. For Gmail, use App Password not account password." />
            </label>
            <input v-model="form.mail_password" type="password" placeholder="••••••••" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 text-sm" />
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>From Email</span>
              <Tooltip text="Email address shown as sender. Example: noreply@crowdpower.com" />
            </label>
            <input v-model="form.mail_from_address" type="email" placeholder="noreply@yourplatform.com" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 text-sm" />
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>From Name</span>
              <Tooltip text="Name displayed as sender. Example: CrowdPower Team, TaskEarn Support" />
            </label>
            <input v-model="form.mail_from_name" type="text" placeholder="CrowdPower" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 text-sm" />
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save Email Settings' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Pusher Configuration -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-purple-500 to-indigo-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">WebSocket Configuration (Pusher)</h2>
            <p class="text-purple-100 text-xs sm:text-sm">Real-time notifications via Pusher/Laravel WebSockets</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Pusher App ID</span>
              <Tooltip text="Your Pusher application ID. Get from pusher.com dashboard or use local websocket value. Example: 123456" />
            </label>
            <input v-model="form.pusher_app_id" type="text" placeholder="123456" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm font-mono" />
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Pusher App Key</span>
              <Tooltip text="Public key for connecting to Pusher. Shown in frontend code. Example: a1b2c3d4e5f6g7h8i9j0" />
            </label>
            <input v-model="form.pusher_app_key" type="text" placeholder="a1b2c3d4e5f6" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm font-mono" />
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Pusher App Secret</span>
              <Tooltip text="Secret key for server-side Pusher authentication. Keep confidential! Example: 9j8h7g6f5e4d3c2b1a" />
            </label>
            <input v-model="form.pusher_app_secret" type="password" placeholder="••••••••" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm font-mono" />
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Pusher Cluster</span>
              <Tooltip text="Server region for lowest latency. Example: us2, eu, ap1, ap2, ap3, ap4. Choose closest to users." />
            </label>
            <select v-model="form.pusher_app_cluster" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm">
              <option value="mt1">mt1 (Multi-tenant)</option>
              <option value="us2">us2 (US East)</option>
              <option value="us3">us3 (US West)</option>
              <option value="eu">eu (Europe)</option>
              <option value="ap1">ap1 (Asia Pacific)</option>
              <option value="ap2">ap2 (Asia Pacific)</option>
              <option value="ap3">ap3 (Asia Pacific)</option>
              <option value="ap4">ap4 (Asia Pacific)</option>
            </select>
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save WebSocket Settings' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Support Contacts -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-green-500 to-emerald-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">Support Contacts</h2>
            <p class="text-green-100 text-xs sm:text-sm">Customer support contact information</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-6">
        <div class="grid grid-cols-1 gap-4">
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Support Email</span>
              <Tooltip text="Email for customer support inquiries. Shown in footer and contact page. Example: support@crowdpower.com" />
            </label>
            <input v-model="form.support_email" type="email" placeholder="support@yourplatform.com" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 text-sm" />
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Support Phone</span>
              <Tooltip text="Phone number with country code. Example: +234 803 123 4567" />
            </label>
            <input v-model="form.support_phone" type="tel" placeholder="+234 803 123 4567" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 text-sm" />
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Support WhatsApp</span>
              <Tooltip text="WhatsApp number with country code (no + or spaces). Example: 2348031234567. Creates wa.me link." />
            </label>
            <input v-model="form.support_whatsapp" type="text" placeholder="2348031234567" class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-mono" />
          </div>
        </div>

        <div class="flex justify-end pt-4 border-t">
          <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:shadow-lg transition-all disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save Support Settings' }}
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
  mail_mailer: props.settings.mail_mailer || 'smtp',
  mail_host: props.settings.mail_host || '',
  mail_port: props.settings.mail_port || 587,
  mail_username: props.settings.mail_username || '',
  mail_password: props.settings.mail_password || '',
  mail_encryption: props.settings.mail_encryption || 'tls',
  mail_from_address: props.settings.mail_from_address || '',
  mail_from_name: props.settings.mail_from_name || '',
  pusher_app_id: props.settings.pusher_app_id || '',
  pusher_app_key: props.settings.pusher_app_key || '',
  pusher_app_secret: props.settings.pusher_app_secret || '',
  pusher_app_cluster: props.settings.pusher_app_cluster || 'mt1',
  support_email: props.settings.support_email || '',
  support_phone: props.settings.support_phone || '',
  support_whatsapp: props.settings.support_whatsapp || '',
});

const save = () => {
  saving.value = true;
  router.post('/admin/settings/integrations', form, {
    preserveScroll: true,
    onSuccess: () => { saving.value = false; emit('saved'); },
    onError: () => { saving.value = false; }
  });
};
</script>
