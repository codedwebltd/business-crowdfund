<template>
  <form @submit.prevent="save">
    <!-- App Information Card -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300 mb-6">
      <div class="p-4 sm:p-6 border-b border-gray-100 bg-gradient-to-r from-blue-500 to-blue-600">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-white/20 rounded-xl backdrop-blur-sm">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-base sm:text-lg font-bold text-white">App Information</h2>
            <p class="text-blue-100 text-xs sm:text-sm">Basic application details</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
        <!-- App Name -->
        <div>
          <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
            <span>App Name</span>
            <Tooltip text="The official name of your platform displayed throughout the app. Example: CrowdPower, TaskEarn, etc." />
          </label>
          <input
            v-model="form.app_name"
            type="text"
            required
            placeholder="e.g., CrowdPower"
            class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
          />
        </div>

        <!-- App URL -->
        <div>
          <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
            <span>App URL</span>
            <Tooltip text="Full URL where your platform is hosted. Must include https://. Example: https://crowdpower.com" />
          </label>
          <input
            v-model="form.app_url"
            type="url"
            required
            placeholder="https://your-domain.com"
            class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-mono"
          />
        </div>

        <!-- Country & Currency (2 columns on desktop) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Country</span>
              <Tooltip text="Primary country of operation. Determines default phone formats, currency, and compliance rules." />
            </label>
            <select
              v-model="form.country_of_operation"
              @change="updateCurrency"
              required
              class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
            >
              <option v-for="country in props.countries" :key="country.code" :value="country.code">
                {{ country.name }} ({{ country.code }})
              </option>
            </select>
          </div>

          <div>
            <label class="text-sm font-semibold text-gray-700 mb-2 flex items-start gap-2">
              <span>Currency Symbol</span>
              <Tooltip text="Auto-filled based on selected country. You can manually override if needed." />
            </label>
            <input
              v-model="form.platform_currency"
              type="text"
              required
              maxlength="5"
              placeholder="e.g., ₦"
              class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
            />
          </div>
        </div>

        <!-- App Description -->
        <div>
          <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
            App Description
            <Tooltip text="Short description shown on landing page and marketing materials. Keep under 200 characters for best SEO." />
          </label>
          <textarea
            v-model="form.app_description"
            rows="3"
            required
            placeholder="Earn money by sharing your browsing data and completing simple tasks..."
            class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none"
          ></textarea>
        </div>

        <!-- Marketing Stats (3 columns on desktop) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
              Daily Earning Average
              <Tooltip text="Average amount users can earn per day. Shown on landing page. Example: 850 means ₦850/day" />
            </label>
            <input
              v-model.number="form.daily_earning_average"
              type="number"
              required
              min="0"
              step="50"
              placeholder="850"
              class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
            />
          </div>

          <div>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
              Time Required
              <Tooltip text="Average time needed daily for tasks. Shown on landing page. Example: 5min, 10min, 15-30min" />
            </label>
            <input
              v-model="form.time_required"
              type="text"
              required
              placeholder="5min"
              class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
            />
          </div>

          <div>
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
              Anonymity Level
              <Tooltip text="Privacy level claim for marketing. Example: 100%, 95%, Full Privacy. Shown on landing page." />
            </label>
            <input
              v-model="form.anonymity_level"
              type="text"
              required
              placeholder="100%"
              class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
            />
          </div>
        </div>

        <!-- Total Users Display -->
        <div>
          <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
            Total Users (Display)
            <Tooltip text="Number shown on landing page for social proof. Can be rounded/estimated. Example: 10,000+, 25K+, 100,000+" />
          </label>
          <input
            v-model="form.total_users"
            type="text"
            required
            placeholder="10,000+"
            class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
          />
        </div>

        <!-- Site Logo Upload -->
        <div>
          <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
            Site Logo
            <Tooltip text="Upload platform logo. Recommended size: 200x50px PNG with transparent background. Max 2MB." />
          </label>
          <input
            ref="logoInput"
            type="file"
            accept="image/*"
            @change="handleLogoUpload"
            :disabled="uploading"
            class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm disabled:opacity-50"
          />

          <!-- Upload Progress -->
          <div v-if="uploading" class="mt-3">
            <div class="flex items-center justify-between mb-1">
              <span class="text-xs font-semibold text-blue-600">Uploading to Backblaze...</span>
              <span class="text-xs font-semibold text-blue-600">{{ uploadProgress }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all" :style="{ width: uploadProgress + '%' }"></div>
            </div>
          </div>

          <!-- Logo Preview -->
          <div v-if="form.site_logo && !uploading" class="mt-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
            <img :src="form.site_logo" alt="Logo preview" class="h-12 object-contain" />
          </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end pt-4 border-t border-gray-200">
          <button
            type="submit"
            :disabled="saving"
            class="px-4 sm:px-6 py-2 sm:py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg hover:shadow-xl"
          >
            {{ saving ? 'Saving...' : 'Save App Config' }}
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

const props = defineProps({
  settings: Object,
  countries: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['saved']);

const saving = ref(false);
const uploading = ref(false);
const uploadProgress = ref(0);
const logoInput = ref(null);

const form = reactive({
  app_name: props.settings.app_name || '',
  app_url: props.settings.app_url || '',
  country_of_operation: props.settings.country_of_operation || 'NG',
  platform_currency: props.settings.platform_currency || '₦',
  app_description: props.settings.app_description || '',
  daily_earning_average: props.settings.daily_earning_average || 850,
  time_required: props.settings.time_required || '5min',
  anonymity_level: props.settings.anonymity_level || '100%',
  total_users: props.settings.total_users || '10,000+',
  site_logo: props.settings.site_logo || '',
});

const handleLogoUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  if (file.size > 2 * 1024 * 1024) {
    alert('Logo must be less than 2MB');
    if (logoInput.value) logoInput.value.value = '';
    return;
  }

  uploading.value = true;
  uploadProgress.value = 0;

  try {
    const formData = new FormData();
    formData.append('logo_file', file);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    await new Promise((resolve, reject) => {
      const xhr = new XMLHttpRequest();

      xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
          uploadProgress.value = Math.round((e.loaded / e.total) * 100);
        }
      });

      xhr.addEventListener('load', () => {
        if (xhr.status >= 200 && xhr.status < 300) {
          const data = JSON.parse(xhr.responseText);
          if (data.success) {
            form.site_logo = data.url;
            uploadProgress.value = 100;
            setTimeout(() => {
              uploading.value = false;
              uploadProgress.value = 0;
            }, 1000);
            resolve(data);
          } else {
            reject(new Error(data.message || 'Upload failed'));
          }
        } else {
          reject(new Error(`Upload failed with status ${xhr.status}`));
        }
      });

      xhr.addEventListener('error', () => reject(new Error('Network error')));

      xhr.open('POST', '/admin/settings/upload-logo');
      xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
      xhr.setRequestHeader('Accept', 'application/json');
      xhr.send(formData);
    });
  } catch (err) {
    alert('Logo upload failed: ' + err.message);
    uploading.value = false;
    uploadProgress.value = 0;
    if (logoInput.value) logoInput.value.value = '';
  }
};

const updateCurrency = () => {
  const selectedCountry = props.countries.find(c => c.code === form.country_of_operation);
  if (selectedCountry) {
    form.platform_currency = selectedCountry.symbol;
  }
};

const save = () => {
  saving.value = true;

  router.post('/admin/settings/app-config', form, {
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
