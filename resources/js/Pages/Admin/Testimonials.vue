<template>
  <AdminLayout title="Testimonial Management" :settings="settings">
    <div class="mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Testimonial Management</h1>
      <p class="text-gray-600 mt-1">Review and manage user testimonials with AI analysis</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Pending Review</p>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ stats.pending }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Approved</p>
            <p class="text-2xl sm:text-3xl font-bold text-green-600 mt-1">{{ stats.approved }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ stats.auto_approved }} AI-auto</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Rejected</p>
            <p class="text-2xl sm:text-3xl font-bold text-red-600 mt-1">{{ stats.rejected }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-red-500 to-red-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">AI Flags</p>
            <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">
              <span class="text-orange-600">{{ stats.trash }}</span> /
              <span class="text-red-600">{{ stats.negative }}</span> /
              <span class="text-yellow-600">{{ stats.complaint }}</span>
            </p>
            <p class="text-xs text-gray-500 mt-1">Trash / Neg / Comp</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4 mb-6">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <select v-model="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
          <option value="PENDING">Pending ({{ stats.pending }})</option>
          <option value="APPROVED">Approved ({{ stats.approved }})</option>
          <option value="REJECTED">Rejected ({{ stats.rejected }})</option>
        </select>
        <input v-model="searchQuery" type="text" placeholder="Search by user or message..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
        <button @click="applySearch" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg text-sm font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition-all">
          Apply Filter
        </button>
      </div>
    </div>

    <!-- Testimonials Table -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-purple-500 to-pink-600">
        <h2 class="text-lg font-bold text-white">All Testimonials</h2>
        <p class="text-purple-100 text-sm mt-1">{{ filteredTestimonials.length }} testimonials found</p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b-2 border-gray-200">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Message Preview</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">AI Flags</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="test in filteredTestimonials" :key="test.id" class="hover:bg-purple-50 transition-colors">
              <td class="px-6 py-5">
                <div class="font-semibold text-gray-900 text-sm">{{ test.user?.full_name || 'Unknown' }}</div>
                <div class="text-xs text-gray-500">{{ test.user?.email }}</div>
              </td>
              <td class="px-6 py-5">
                <div class="text-sm text-gray-900 max-w-xs overflow-x-auto whitespace-nowrap scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">{{ test.message.substring(0, 40) }}{{ test.message.length > 40 ? '...' : '' }}</div>
                <div v-if="test.auto_approved" class="text-xs text-blue-600 mt-1">🤖 AI Auto</div>
              </td>
              <td class="px-6 py-5">
                <div class="flex flex-wrap items-center gap-1">
                  <span v-if="test.trash_testimonial" class="inline-flex items-center gap-1 text-xs font-semibold text-orange-600 whitespace-nowrap">🗑️ Trash</span>
                  <span v-if="test.negative_testimonial" class="inline-flex items-center gap-1 text-xs font-semibold text-red-600 whitespace-nowrap">⚠️ Negative</span>
                  <span v-if="test.complaint_testimonial" class="inline-flex items-center gap-1 text-xs font-semibold text-yellow-600 whitespace-nowrap">📋 Complaint</span>
                  <span v-if="!test.trash_testimonial && !test.negative_testimonial && !test.complaint_testimonial && test.ai_processed_at" class="inline-flex items-center gap-1 text-xs font-semibold text-green-600 whitespace-nowrap">✓ Clean</span>
                  <span v-if="!test.ai_processed_at" class="text-xs text-gray-400">-</span>
                </div>
              </td>
              <td class="px-6 py-5">
                <div class="text-sm text-gray-900 font-medium whitespace-nowrap">{{ formatDate(test.created_at) }}</div>
                <div class="text-xs text-gray-500">{{ formatTime(test.created_at) }}</div>
              </td>
              <td class="px-6 py-5">
                <span
                  :class="{
                    'bg-yellow-100 text-yellow-800 border-yellow-200': test.status === 'PENDING',
                    'bg-green-100 text-green-800 border-green-200': test.status === 'APPROVED',
                    'bg-red-100 text-red-800 border-red-200': test.status === 'REJECTED'
                  }"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border whitespace-nowrap"
                >
                  <span :class="{
                    'w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse': test.status === 'PENDING',
                    'w-1.5 h-1.5 bg-green-500 rounded-full': test.status === 'APPROVED',
                    'w-1.5 h-1.5 bg-red-500 rounded-full': test.status === 'REJECTED'
                  }"></span>
                  {{ test.status }}
                </span>
              </td>
              <td class="px-6 py-5">
                <div class="flex items-center justify-end gap-2">
                  <button @click="viewTestimonial(test)" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="View Details">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                  <button
                    v-if="test.status === 'PENDING'"
                    @click="approveTestimonial(test.id)"
                    class="p-2 text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg transition-all shadow-sm"
                    title="Approve"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </button>
                  <button
                    v-if="test.status === 'PENDING'"
                    @click="rejectTestimonial(test.id)"
                    class="p-2 text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 rounded-lg transition-all shadow-sm"
                    title="Reject"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- View Modal -->
    <div v-if="viewingTestimonial" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="viewingTestimonial = null">
      <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b bg-gradient-to-r from-purple-500 to-pink-600">
          <div class="flex justify-between items-start">
            <div class="text-white">
              <h3 class="text-xl font-bold">Testimonial Details</h3>
              <p class="text-purple-100 text-sm mt-1">{{ viewingTestimonial.user?.full_name }}</p>
            </div>
            <button @click="viewingTestimonial = null" class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <!-- User Info -->
          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 uppercase mb-2">User Details</p>
            <p class="text-sm font-bold text-gray-900">{{ viewingTestimonial.user?.full_name }}</p>
            <p class="text-xs text-gray-500">{{ viewingTestimonial.user?.email }}</p>
            <p class="text-xs text-gray-500">{{ viewingTestimonial.user?.phone_number }}</p>
            <p class="text-xs text-gray-500 mt-2">Submitted: {{ formatDate(viewingTestimonial.created_at) }} at {{ formatTime(viewingTestimonial.created_at) }}</p>
          </div>

          <!-- Original Message -->
          <div class="bg-blue-50 p-5 rounded-xl border-2 border-blue-200">
            <p class="text-xs font-bold text-blue-700 uppercase mb-2">Original Message</p>
            <p class="text-sm text-gray-900 leading-relaxed whitespace-pre-wrap">{{ viewingTestimonial.message }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ countWords(viewingTestimonial.message) }} words</p>
          </div>

          <!-- AI Corrected Message -->
          <div v-if="viewingTestimonial.ai_corrected_message" class="bg-green-50 p-5 rounded-xl border-2 border-green-200">
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
              </svg>
              <p class="text-xs font-bold text-green-700 uppercase">AI Grammar-Corrected Version</p>
            </div>
            <p class="text-sm text-gray-900 leading-relaxed whitespace-pre-wrap">{{ viewingTestimonial.ai_corrected_message }}</p>
          </div>

          <!-- AI Flags -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div :class="viewingTestimonial.auto_approved ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200'" class="p-3 rounded-lg border-2 text-center">
              <p class="text-xs text-gray-600 mb-1">Auto Approved</p>
              <p :class="viewingTestimonial.auto_approved ? 'text-blue-600' : 'text-gray-400'" class="text-2xl font-bold">{{ viewingTestimonial.auto_approved ? '✓' : '✗' }}</p>
            </div>
            <div :class="viewingTestimonial.trash_testimonial ? 'bg-orange-50 border-orange-200' : 'bg-gray-50 border-gray-200'" class="p-3 rounded-lg border-2 text-center">
              <p class="text-xs text-gray-600 mb-1">Trash</p>
              <p :class="viewingTestimonial.trash_testimonial ? 'text-orange-600' : 'text-gray-400'" class="text-2xl font-bold">{{ viewingTestimonial.trash_testimonial ? '🗑️' : '✗' }}</p>
            </div>
            <div :class="viewingTestimonial.negative_testimonial ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200'" class="p-3 rounded-lg border-2 text-center">
              <p class="text-xs text-gray-600 mb-1">Negative</p>
              <p :class="viewingTestimonial.negative_testimonial ? 'text-red-600' : 'text-gray-400'" class="text-2xl font-bold">{{ viewingTestimonial.negative_testimonial ? '⚠️' : '✗' }}</p>
            </div>
            <div :class="viewingTestimonial.complaint_testimonial ? 'bg-yellow-50 border-yellow-200' : 'bg-gray-50 border-gray-200'" class="p-3 rounded-lg border-2 text-center">
              <p class="text-xs text-gray-600 mb-1">Complaint</p>
              <p :class="viewingTestimonial.complaint_testimonial ? 'text-yellow-600' : 'text-gray-400'" class="text-2xl font-bold">{{ viewingTestimonial.complaint_testimonial ? '📋' : '✗' }}</p>
            </div>
          </div>

          <!-- AI Analysis -->
          <div v-if="viewingTestimonial.ai_analysis" class="bg-purple-50 p-5 rounded-xl border-2 border-purple-200">
            <p class="text-xs font-bold text-purple-700 uppercase mb-3">AI Analysis</p>
            <div v-if="viewingTestimonial.ai_analysis.reason" class="bg-white p-4 rounded-lg mb-3">
              <p class="text-xs text-gray-600 font-semibold mb-1">AI Reasoning</p>
              <p class="text-sm text-gray-900">{{ viewingTestimonial.ai_analysis.reason }}</p>
            </div>
            <details class="bg-white p-3 rounded-lg">
              <summary class="text-xs font-semibold text-gray-700 cursor-pointer">View Full AI Analysis (JSON)</summary>
              <pre class="text-xs bg-gray-50 p-3 rounded border border-gray-200 overflow-x-auto text-gray-800 font-mono mt-2">{{ JSON.stringify(viewingTestimonial.ai_analysis, null, 2) }}</pre>
            </details>
          </div>

          <!-- Status Badge -->
          <div class="flex items-center justify-center p-4 rounded-lg" :class="{
            'bg-yellow-50 border border-yellow-200': viewingTestimonial.status === 'PENDING',
            'bg-green-50 border border-green-200': viewingTestimonial.status === 'APPROVED',
            'bg-red-50 border border-red-200': viewingTestimonial.status === 'REJECTED'
          }">
            <span :class="{
              'bg-yellow-100 text-yellow-800 border-yellow-300': viewingTestimonial.status === 'PENDING',
              'bg-green-100 text-green-800 border-green-300': viewingTestimonial.status === 'APPROVED',
              'bg-red-100 text-red-800 border-red-300': viewingTestimonial.status === 'REJECTED'
            }" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold border">
              <span :class="{
                'w-2 h-2 bg-yellow-500 rounded-full animate-pulse': viewingTestimonial.status === 'PENDING',
                'w-2 h-2 bg-green-500 rounded-full': viewingTestimonial.status === 'APPROVED',
                'w-2 h-2 bg-red-500 rounded-full': viewingTestimonial.status === 'REJECTED'
              }"></span>
              {{ viewingTestimonial.status }}
            </span>
          </div>

          <!-- Admin Notes -->
          <div v-if="viewingTestimonial.admin_notes" class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Admin Notes</p>
            <p class="text-sm text-gray-900">{{ viewingTestimonial.admin_notes }}</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 pt-4 border-t">
            <button
              v-if="viewingTestimonial.status === 'PENDING'"
              @click="approveTestimonial(viewingTestimonial.id)"
              class="flex-1 py-3 px-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg font-semibold hover:shadow-lg hover:shadow-green-500/50 transition-all flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Approve Testimonial
            </button>
            <button
              v-if="viewingTestimonial.status === 'PENDING'"
              @click="rejectTestimonial(viewingTestimonial.id)"
              class="flex-1 py-3 px-4 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-semibold hover:shadow-lg hover:shadow-red-500/50 transition-all flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              Reject Testimonial
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  testimonials: Array,
  stats: Object,
  settings: Object,
});

const statusFilter = ref('PENDING');
const searchQuery = ref('');
const viewingTestimonial = ref(null);

const filteredTestimonials = computed(() => {
  let filtered = props.testimonials.filter(t => t.status === statusFilter.value);

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(t =>
      t.user?.full_name?.toLowerCase().includes(query) ||
      t.user?.email?.toLowerCase().includes(query) ||
      t.message?.toLowerCase().includes(query)
    );
  }

  return filtered;
});

const formatDate = (date) => new Date(date).toLocaleDateString('en-US', {
  month: 'short',
  day: 'numeric',
  year: 'numeric'
});

const formatTime = (date) => new Date(date).toLocaleTimeString('en-US', {
  hour: '2-digit',
  minute: '2-digit'
});

const countWords = (text) => text ? text.split(/\s+/).filter(w => w.length > 0).length : 0;

const viewTestimonial = (test) => viewingTestimonial.value = test;

const applySearch = () => {
  // Filter is reactive, just for UI feedback
};

const approveTestimonial = (id) => {
  Swal.fire({
    title: 'Approve Testimonial?',
    text: 'User will be notified immediately.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, approve',
    confirmButtonColor: '#10b981',
    cancelButtonColor: '#6b7280'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/testimonials/${id}/approve`, {}, {
        preserveScroll: true,
        onSuccess: () => {
          viewingTestimonial.value = null;
          Swal.fire({
            icon: 'success',
            title: 'Testimonial Approved',
            text: 'User has been notified via all channels.',
            confirmButtonColor: '#10b981'
          });
        }
      });
    }
  });
};

const rejectTestimonial = (id) => {
  Swal.fire({
    title: 'Reject Testimonial?',
    input: 'textarea',
    inputLabel: 'Rejection Reason',
    inputPlaceholder: 'Enter reason for rejection...',
    showCancelButton: true,
    confirmButtonText: 'Reject',
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    inputValidator: (value) => {
      if (!value) {
        return 'You must provide a rejection reason!';
      }
    }
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/testimonials/${id}/reject`, { reason: result.value }, {
        preserveScroll: true,
        onSuccess: () => {
          viewingTestimonial.value = null;
          Swal.fire({
            icon: 'success',
            title: 'Testimonial Rejected',
            text: 'User has been notified.',
            confirmButtonColor: '#ef4444'
          });
        }
      });
    }
  });
};
</script>
