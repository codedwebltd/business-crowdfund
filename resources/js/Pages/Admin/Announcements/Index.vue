<template>
  <AdminLayout title="Announcements" :settings="settings" :breadcrumbs="breadcrumbs">
    <div class="mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Announcements</h1>
      <p class="text-gray-600 mt-1">Manage platform announcements and notify users</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Total</p>
            <p class="text-3xl font-bold text-purple-600 mt-1">{{ stats.total }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Active</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ stats.active }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Inactive</p>
            <p class="text-3xl font-bold text-gray-600 mt-1">{{ stats.inactive }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Scheduled</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ stats.scheduled }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4 mb-6 flex flex-col sm:flex-row gap-3 sm:gap-4">
      <input v-model="search" type="text" placeholder="Search announcements..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
      <Link href="/admin/announcements/create" class="px-4 sm:px-6 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg font-semibold hover:shadow-lg transition-all flex items-center justify-center gap-2 whitespace-nowrap">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span class="hidden sm:inline">Create Announcement</span>
        <span class="sm:hidden">Create</span>
      </Link>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gradient-to-r from-purple-50 to-purple-100 border-b border-purple-200">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Announcement</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Audience</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Priority</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Schedule</th>
              <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="item in filtered" :key="item.id" class="hover:bg-purple-50 transition-colors">
              <td class="px-6 py-4">
                <div class="font-semibold text-gray-900">{{ item.title }}</div>
                <div class="text-sm text-gray-600 truncate max-w-md">{{ item.message }}</div>
              </td>
              <td class="px-6 py-4">
                <span :class="typeColor(item.type)" class="px-3 py-1 text-xs font-semibold rounded-full">{{ item.type }}</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-700">{{ item.target_audience }}</td>
              <td class="px-6 py-4">
                <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full">{{ item.priority }}</span>
              </td>
              <td class="px-6 py-4">
                <span :class="item.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'" class="px-3 py-1 text-xs font-semibold rounded-full">
                  {{ item.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">
                <div v-if="item.start_date">Start: {{ formatDate(item.start_date) }}</div>
                <div v-if="item.end_date">End: {{ formatDate(item.end_date) }}</div>
                <div v-if="!item.start_date && !item.end_date" class="text-gray-400">No schedule</div>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex flex-wrap justify-end gap-2">
                  <button @click="sendNotification(item.id)" class="text-blue-600 hover:text-blue-800 font-medium text-xs sm:text-sm whitespace-nowrap">Send Email</button>
                  <Link :href="`/admin/announcements/${item.id}/edit`" class="text-purple-600 hover:text-purple-800 font-medium text-xs sm:text-sm whitespace-nowrap">Edit</Link>
                  <button @click="deleteAnnouncement(item.id)" class="text-red-600 hover:text-red-800 font-medium text-xs sm:text-sm whitespace-nowrap">Delete</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({ announcements: Array, stats: Object, settings: Object });

const breadcrumbs = [
  { label: 'Dashboard', url: '/admin' },
  { label: 'Announcements' }
];

const search = ref('');

const filtered = computed(() => {
  if (!search.value) return props.announcements;
  return props.announcements.filter(a =>
    a.title.toLowerCase().includes(search.value.toLowerCase()) ||
    a.message.toLowerCase().includes(search.value.toLowerCase())
  );
});

const typeColor = (type) => ({
  info: 'bg-blue-100 text-blue-700',
  success: 'bg-green-100 text-green-700',
  warning: 'bg-yellow-100 text-yellow-700',
  danger: 'bg-red-100 text-red-700'
}[type]);

const formatDate = (date) => {
  if (!date) return '';

  const d = new Date(date);

  // Format: Jan 9, 2026 6:53 PM
  return d.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  });
};

const sendNotification = (id) => {
  if (confirm('Send email notification to all eligible users?')) {
    router.post(`/admin/announcements/${id}/send`);
  }
};

const deleteAnnouncement = (id) => {
  if (confirm('Delete this announcement?')) {
    router.delete(`/admin/announcements/${id}`);
  }
};
</script>
