<template>
  <AdminLayout title="Task Content Pool" :settings="settings">
    <!-- Breadcrumbs -->
    <nav class="flex mb-4" aria-label="Breadcrumb">
      <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
          <Link href="/admin/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-purple-600">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
            </svg>
            Dashboard
          </Link>
        </li>
        <li>
          <div class="flex items-center">
            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Task Management</span>
          </div>
        </li>
        <li aria-current="page">
          <div class="flex items-center">
            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">Content Pool</span>
          </div>
        </li>
      </ol>
    </nav>

    <div class="mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Task Content Pool</h1>
      <p class="text-gray-600 mt-1">Manage pre-generated task content for instant assignment</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Total Pool</p>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ stats.total }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Active</p>
            <p class="text-2xl sm:text-3xl font-bold text-green-600 mt-1">{{ stats.active }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Unused</p>
            <p class="text-2xl sm:text-3xl font-bold text-yellow-600 mt-1">{{ stats.unused }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Videos</p>
            <p class="text-2xl sm:text-3xl font-bold text-purple-600 mt-1">{{ stats.videos }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4 mb-6">
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <select v-model="filters.type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
          <option value="">All Types</option>
          <option value="SURVEY">Surveys</option>
          <option value="VIDEO">Videos</option>
          <option value="REVIEW">Reviews</option>
        </select>
        <select v-model="filters.source" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
          <option value="">All Sources</option>
          <option value="AI_GENERATED">AI Generated</option>
          <option value="TRIVIA_API">Trivia API</option>
          <option value="MANUAL">Manual</option>
        </select>
        <select v-model="filters.status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
        <input v-model="filters.search" type="text" placeholder="Search title..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
      </div>
      <button @click="applyFilters" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
        Apply Filters
      </button>
    </div>

    <!-- Content Pool Table -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-indigo-500 to-purple-600">
        <h2 class="text-lg font-bold text-white">Content Pool Items</h2>
        <p class="text-indigo-100 text-sm mt-1">{{ pools.total }} items in pool</p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b-2 border-gray-200">
            <tr>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Content</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Source</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Usage</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="pool in pools.data" :key="pool.id" class="hover:bg-indigo-50 transition-colors">
              <td class="px-4 py-4">
                <div class="flex items-start gap-3">
                  <div :class="getTypeIcon(pool.type)" class="p-2 rounded-lg flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="getTypeIconSvg(pool.type)"></svg>
                  </div>
                  <div>
                    <div class="font-semibold text-gray-900 text-sm">{{ pool.title }}</div>
                    <div class="text-xs text-gray-500 mt-1">
                      {{ pool.last_used_at ? 'Last used: ' + formatDate(pool.last_used_at) : 'Never used' }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <span :class="getTypeBadge(pool.type)" class="px-3 py-1.5 rounded-lg text-xs font-bold inline-flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-current"></span>
                  {{ pool.type }}
                </span>
              </td>
              <td class="px-4 py-4">
                <span :class="getSourceBadge(pool.source)" class="px-2 py-1 rounded text-xs font-semibold">
                  {{ pool.source }}
                </span>
              </td>
              <td class="px-4 py-4">
                <div class="flex items-center gap-2">
                  <div class="font-semibold text-sm text-gray-900">{{ pool.times_used }} times</div>
                  <button v-if="pool.times_used > 0" @click="resetUsage(pool.id)" class="text-xs text-blue-600 hover:text-blue-700 font-medium" title="Reset usage">
                    Reset
                  </button>
                </div>
              </td>
              <td class="px-4 py-4">
                <span :class="pool.is_active ? 'bg-gradient-to-r from-green-500 to-green-600 text-white' : 'bg-gray-100 text-gray-700'" class="px-3 py-1.5 rounded-lg text-xs font-bold inline-flex items-center gap-1.5 shadow-sm">
                  <span :class="pool.is_active ? 'w-2 h-2 bg-white rounded-full animate-pulse' : 'w-2 h-2 bg-gray-400 rounded-full'"></span>
                  {{ pool.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-4 py-4">
                <div class="flex items-center justify-end gap-1.5">
                  <button @click="viewPool(pool)" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="View">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                  <button @click="toggleActive(pool.id)" :class="pool.is_active ? 'text-orange-600 hover:bg-orange-100' : 'text-green-600 hover:bg-green-100'" class="p-2 rounded-lg transition-colors" :title="pool.is_active ? 'Deactivate' : 'Activate'">
                    <svg v-if="pool.is_active" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </button>
                  <button @click="deletePool(pool.id)" class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Delete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="text-sm text-gray-600 text-center sm:text-left">
          Showing {{ pools.from }} to {{ pools.to }} of {{ pools.total }} items
        </div>
        <div class="flex gap-1 sm:gap-2 flex-wrap justify-center">
          <button v-for="link in pools.links" :key="link.label" @click="changePage(link.url)" :disabled="!link.url" :class="link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'" class="px-2 sm:px-3 py-1 rounded border text-xs sm:text-sm disabled:opacity-50 min-w-[32px]" v-html="link.label"></button>
        </div>
      </div>
    </div>

    <!-- View Modal -->
    <div v-if="viewingPool" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="viewingPool = null">
      <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b bg-gradient-to-r from-blue-500 to-blue-600">
          <div class="flex justify-between items-start">
            <div class="text-white">
              <h3 class="text-xl font-bold">{{ viewingPool.title }}</h3>
              <p class="text-blue-100 text-sm mt-1">{{ viewingPool.type }} - {{ viewingPool.source }}</p>
            </div>
            <button @click="viewingPool = null" class="text-white hover:bg-white/20 rounded-lg p-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <div><strong>Type:</strong> {{ viewingPool.type }}</div>
          <div><strong>Source:</strong> {{ viewingPool.source }}</div>
          <div><strong>Times Used:</strong> {{ viewingPool.times_used }}</div>
          <div><strong>Last Used:</strong> {{ viewingPool.last_used_at ? formatDateTime(viewingPool.last_used_at) : 'Never' }}</div>
          <div><strong>Status:</strong> {{ viewingPool.is_active ? 'Active' : 'Inactive' }}</div>
          <div v-if="viewingPool.content"><strong>Content:</strong> <pre class="bg-gray-100 p-3 rounded text-xs mt-2 overflow-auto">{{ JSON.stringify(viewingPool.content, null, 2) }}</pre></div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({ pools: Object, stats: Object, settings: Object, filters: Object });

const filters = reactive({
  type: props.filters?.type || '',
  source: props.filters?.source || '',
  status: props.filters?.status || '',
  search: props.filters?.search || ''
});
const viewingPool = ref(null);

const getTypeBadge = (type) => ({
  'SURVEY': 'bg-blue-100 text-blue-700 border border-blue-200',
  'VIDEO': 'bg-purple-100 text-purple-700 border border-purple-200',
  'REVIEW': 'bg-yellow-100 text-yellow-700 border border-yellow-200'
}[type] || 'bg-gray-100 text-gray-700 border border-gray-200');

const getTypeIcon = (type) => ({
  'SURVEY': 'bg-gradient-to-br from-blue-500 to-blue-600',
  'VIDEO': 'bg-gradient-to-br from-purple-500 to-purple-600',
  'REVIEW': 'bg-gradient-to-br from-yellow-500 to-yellow-600'
}[type] || 'bg-gradient-to-br from-gray-500 to-gray-600');

const getTypeIconSvg = (type) => ({
  'SURVEY': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>',
  'VIDEO': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
  'REVIEW': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>'
}[type] || '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>');

const getSourceBadge = (source) => ({
  'AI_GENERATED': 'bg-purple-100 text-purple-700',
  'TRIVIA_API': 'bg-blue-100 text-blue-700',
  'MANUAL': 'bg-green-100 text-green-700'
}[source] || 'bg-gray-100 text-gray-700');

const formatDate = (date) => new Date(date).toLocaleDateString();
const formatDateTime = (date) => new Date(date).toLocaleString();

const viewPool = (pool) => viewingPool.value = pool;
const applyFilters = () => router.get('/admin/content-pool', filters, { preserveState: true });
const changePage = (url) => url && router.visit(url);

const toggleActive = (id) => {
  router.post(`/admin/content-pool/${id}/toggle`, {}, { preserveScroll: true });
};

const resetUsage = (id) => {
  Swal.fire({
    title: 'Reset usage count?',
    text: 'This will mark the item as unused',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, reset'
  }).then((result) => {
    if (result.isConfirmed) router.post(`/admin/content-pool/${id}/reset-usage`, {}, { preserveScroll: true });
  });
};

const deletePool = (id) => {
  Swal.fire({
    title: 'Delete permanently?',
    text: 'This cannot be undone!',
    icon: 'error',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete'
  }).then((result) => {
    if (result.isConfirmed) router.delete(`/admin/content-pool/${id}`, { preserveScroll: true });
  });
};
</script>
