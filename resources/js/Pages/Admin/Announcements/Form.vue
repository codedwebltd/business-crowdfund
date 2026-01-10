<template>
  <AdminLayout :title="announcement ? 'Edit Announcement' : 'Create Announcement'" :settings="settings" :breadcrumbs="breadcrumbs">
    <div class="mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ announcement ? 'Edit' : 'Create' }} Announcement</h1>
      <p class="text-gray-600 mt-1">{{ announcement ? 'Update' : 'Create a new' }} platform announcement</p>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
      <form @submit.prevent="submit">
        <div class="space-y-6">
          <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-xl p-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">AI Assistant</label>
            <div class="flex flex-col sm:flex-row gap-3">
              <input v-model="aiDescription" type="text" placeholder="Describe announcement (e.g., maintenance on Jan 15)" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" :disabled="generating">
              <button type="button" @click="generateWithAI" :disabled="!aiDescription || generating" class="px-4 sm:px-6 py-2 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg font-semibold hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 whitespace-nowrap">
                <svg v-if="!generating" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <svg v-else class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span class="hidden sm:inline">{{ generating ? 'Generating...' : 'Generate with AI' }}</span>
                <span class="sm:hidden">{{ generating ? 'Generating...' : 'Generate' }}</span>
              </button>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Title *</label>
              <input v-model="form.title" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-semibold text-gray-700 mb-2">Message *</label>
              <textarea v-model="form.message" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Type *</label>
              <select v-model="form.type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="info">Info</option>
                <option value="success">Success</option>
                <option value="warning">Warning</option>
                <option value="danger">Danger</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Target Audience *</label>
              <select v-model="form.target_audience" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="all">All Users</option>
                <option value="active">Active Only</option>
                <option value="pending">Pending Only</option>
                <option value="unverified">Unverified Only</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Priority (0-100) *</label>
              <input v-model.number="form.priority" type="number" min="0" max="100" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>

            <div class="flex items-center space-x-6">
              <label class="flex items-center space-x-2">
                <input v-model="form.is_active" type="checkbox" class="w-5 h-5 text-purple-600 rounded focus:ring-2 focus:ring-purple-500">
                <span class="text-sm font-medium text-gray-700">Active</span>
              </label>
              <label class="flex items-center space-x-2">
                <input v-model="form.is_dismissable" type="checkbox" class="w-5 h-5 text-purple-600 rounded focus:ring-2 focus:ring-purple-500">
                <span class="text-sm font-medium text-gray-700">Dismissable</span>
              </label>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
              <input v-model="form.start_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
              <p class="text-xs text-gray-500 mt-1">Announcement will be active from this date</p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
              <input v-model="form.end_date" type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
              <p class="text-xs text-gray-500 mt-1">Announcement will expire after this date</p>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Link URL</label>
              <input v-model="form.link_url" type="url" placeholder="https://..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Link Text</label>
              <input v-model="form.link_text" type="text" placeholder="Learn More" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>

            <div v-if="!announcement" class="md:col-span-2">
              <label class="flex items-center space-x-2">
                <input v-model="form.send_email" type="checkbox" class="w-5 h-5 text-purple-600 rounded focus:ring-2 focus:ring-purple-500">
                <span class="text-sm font-medium text-gray-700">Send email notification to users immediately</span>
              </label>
            </div>
          </div>

          <div class="flex flex-col sm:flex-row gap-3 justify-end pt-4 border-t">
            <Link href="/admin/announcements" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-all text-center">Cancel</Link>
            <button type="submit" :disabled="processing" class="px-6 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg font-semibold hover:shadow-lg transition-all disabled:opacity-50 whitespace-nowrap">
              {{ processing ? 'Saving...' : (announcement ? 'Update Announcement' : 'Create Announcement') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import axios from 'axios';

const props = defineProps({ announcement: Object, settings: Object });

const breadcrumbs = [
  { label: 'Dashboard', url: '/admin' },
  { label: 'Announcements', url: '/admin/announcements' },
  { label: props.announcement ? 'Edit' : 'Create' }
];

// Helper function to format date for input (YYYY-MM-DD)
const formatDateForInput = (dateString) => {
  if (!dateString) return '';
  // Just get the date part without time
  return dateString.split('T')[0];
};

const form = reactive({
  title: props.announcement?.title || '',
  message: props.announcement?.message || '',
  type: props.announcement?.type || 'info',
  priority: props.announcement?.priority || 10,
  is_active: props.announcement?.is_active ?? true,
  is_dismissable: props.announcement?.is_dismissable ?? true,
  target_audience: props.announcement?.target_audience || 'all',
  start_date: formatDateForInput(props.announcement?.start_date),
  end_date: formatDateForInput(props.announcement?.end_date),
  link_url: props.announcement?.link_url || '',
  link_text: props.announcement?.link_text || '',
  send_email: false,
});

const aiDescription = ref('');
const generating = ref(false);
const processing = ref(false);

const generateWithAI = async () => {
  generating.value = true;
  try {
    const { data } = await axios.post('/admin/announcements/generate-ai', { description: aiDescription.value });
    form.title = data.title;
    form.message = data.message;
  } catch (error) {
    alert('AI generation failed. Check API key in settings.');
  } finally {
    generating.value = false;
  }
};

const submit = () => {
  processing.value = true;

  // Send dates as-is (YYYY-MM-DD format) - no timezone conversion
  const submitData = { ...form };

  if (props.announcement) {
    router.put(`/admin/announcements/${props.announcement.id}`, submitData, {
      onFinish: () => processing.value = false
    });
  } else {
    router.post('/admin/announcements', submitData, {
      onFinish: () => processing.value = false
    });
  }
};
</script>