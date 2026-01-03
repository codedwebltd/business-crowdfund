<template>
  <AdminLayout title="KYC Management" :settings="settings">
    <!-- Breadcrumbs -->
    <Breadcrumbs :crumbs="[
      { label: 'Dashboard', url: '/admin' },
      { label: 'Security', url: '#' },
      { label: 'KYC Verifications' }
    ]" class="mb-4" />

    <div class="mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">KYC Management</h1>
      <p class="text-gray-600 mt-1">Review and manage user identity verifications</p>
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
            <p class="text-xs sm:text-sm font-medium text-gray-600">Total KYC</p>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ stats.total }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4 mb-6">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <select v-model="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
          <option value="PENDING">Pending ({{ stats.pending }})</option>
          <option value="APPROVED">Approved ({{ stats.approved }})</option>
          <option value="REJECTED">Rejected ({{ stats.rejected }})</option>
        </select>
        <input v-model="searchQuery" type="text" placeholder="Search by user name or email..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-teal-500">
        <button @click="applySearch" class="px-4 py-2 bg-gradient-to-r from-teal-600 to-cyan-600 text-white rounded-lg text-sm font-semibold hover:shadow-lg hover:shadow-teal-500/50 transition-all">
          Apply Filter
        </button>
      </div>
    </div>

    <!-- KYC Table -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-teal-500 to-cyan-600">
        <h2 class="text-lg font-bold text-white">All KYC Verifications</h2>
        <p class="text-teal-100 text-sm mt-1">{{ filteredKyc.length }} verifications found</p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b-2 border-gray-200">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Documents</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Submitted</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="kyc in filteredKyc" :key="kyc.id" class="hover:bg-teal-50 transition-colors">
              <td class="px-6 py-5">
                <div class="font-semibold text-gray-900 text-sm">{{ kyc.user?.full_name || 'Unknown' }}</div>
                <div class="text-xs text-gray-500">{{ kyc.user?.email }}</div>
                <div class="text-xs text-gray-500">{{ kyc.user?.phone_number }}</div>
              </td>
              <td class="px-6 py-5">
                <div class="flex flex-wrap items-center gap-1.5">
                  <span v-if="kyc.nin_url" class="inline-flex items-center gap-1 px-2 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-md border border-orange-200">
                    🆔 NIN
                  </span>
                  <span v-if="kyc.utility_bill_url" class="inline-flex items-center gap-1 px-2 py-1 bg-purple-100 text-purple-700 text-xs font-semibold rounded-md border border-purple-200">
                    📄 Bill
                  </span>
                  <span v-if="kyc.selfie_url" class="inline-flex items-center gap-1 px-2 py-1 bg-teal-100 text-teal-700 text-xs font-semibold rounded-md border border-teal-200">
                    📸 Selfie
                  </span>
                </div>
              </td>
              <td class="px-6 py-5">
                <div class="text-sm text-gray-900 font-medium whitespace-nowrap">{{ formatDate(kyc.submitted_at) }}</div>
                <div class="text-xs text-gray-500">{{ formatTime(kyc.submitted_at) }}</div>
              </td>
              <td class="px-6 py-5">
                <span
                  :class="{
                    'bg-yellow-100 text-yellow-800 border-yellow-200': kyc.status === 'PENDING',
                    'bg-green-100 text-green-800 border-green-200': kyc.status === 'APPROVED',
                    'bg-red-100 text-red-800 border-red-200': kyc.status === 'REJECTED'
                  }"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border whitespace-nowrap"
                >
                  <span :class="{
                    'w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse': kyc.status === 'PENDING',
                    'w-1.5 h-1.5 bg-green-500 rounded-full': kyc.status === 'APPROVED',
                    'w-1.5 h-1.5 bg-red-500 rounded-full': kyc.status === 'REJECTED'
                  }"></span>
                  {{ kyc.status }}
                </span>
              </td>
              <td class="px-6 py-5">
                <div class="flex items-center justify-end gap-2">
                  <button @click="viewKyc(kyc)" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="View Details">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                  <button
                    v-if="kyc.status === 'PENDING'"
                    @click="approveKyc(kyc.id)"
                    class="p-2 text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg transition-all shadow-sm"
                    title="Approve"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </button>
                  <button
                    v-if="kyc.status === 'PENDING'"
                    @click="rejectKyc(kyc.id)"
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
    <div v-if="viewingKyc" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="viewingKyc = null">
      <div class="bg-white rounded-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b bg-gradient-to-r from-teal-500 to-cyan-600">
          <div class="flex justify-between items-start">
            <div class="text-white">
              <h3 class="text-xl font-bold">KYC Verification Details</h3>
              <p class="text-teal-100 text-sm mt-1">{{ viewingKyc.user?.full_name }}</p>
            </div>
            <button @click="viewingKyc = null" class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors">
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
            <div class="grid grid-cols-2 gap-3">
              <div>
                <p class="text-sm font-bold text-gray-900">{{ viewingKyc.user?.full_name }}</p>
                <p class="text-xs text-gray-500">{{ viewingKyc.user?.email }}</p>
                <p class="text-xs text-gray-500">{{ viewingKyc.user?.phone_number }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-500">Date of Birth: <span class="font-semibold text-gray-900">{{ viewingKyc.user?.date_of_birth || 'Not provided' }}</span></p>
                <p class="text-xs text-gray-500">NIN: <span class="font-semibold text-gray-900">{{ viewingKyc.user?.nin || 'Not provided' }}</span></p>
                <p class="text-xs text-gray-500">BVN: <span class="font-semibold text-gray-900">{{ viewingKyc.user?.bvn || 'Not provided' }}</span></p>
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">Submitted: {{ formatDate(viewingKyc.submitted_at) }} at {{ formatTime(viewingKyc.submitted_at) }}</p>
          </div>

          <!-- Documents -->
          <div class="space-y-3">
            <p class="text-xs font-semibold text-gray-600 uppercase">Uploaded Documents</p>

            <!-- NIN Document -->
            <div v-if="viewingKyc.nin_url" class="bg-orange-50 p-4 rounded-xl border-2 border-orange-200">
              <div class="flex items-start gap-3">
                <div class="p-2 bg-orange-100 rounded-lg">
                  <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-bold text-orange-900">National Identification Number (NIN)</p>
                  <p class="text-xs text-orange-700 mt-1">{{ viewingKyc.user?.nin || 'Number not provided' }}</p>
                  <a :href="viewingKyc.nin_url" target="_blank" class="inline-flex items-center gap-1 mt-2 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    View Document
                  </a>
                </div>
              </div>
            </div>

            <!-- Utility Bill -->
            <div v-if="viewingKyc.utility_bill_url" class="bg-purple-50 p-4 rounded-xl border-2 border-purple-200">
              <div class="flex items-start gap-3">
                <div class="p-2 bg-purple-100 rounded-lg">
                  <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-bold text-purple-900">Utility Bill</p>
                  <p class="text-xs text-purple-700 mt-1">Proof of address document</p>
                  <a :href="viewingKyc.utility_bill_url" target="_blank" class="inline-flex items-center gap-1 mt-2 px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    View Document
                  </a>
                </div>
              </div>
            </div>

            <!-- Selfie -->
            <div v-if="viewingKyc.selfie_url" class="bg-teal-50 p-4 rounded-xl border-2 border-teal-200">
              <div class="flex items-start gap-3">
                <div class="p-2 bg-teal-100 rounded-lg">
                  <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-bold text-teal-900">Selfie with ID</p>
                  <p class="text-xs text-teal-700 mt-1">User holding identification document</p>
                  <a :href="viewingKyc.selfie_url" target="_blank" class="inline-flex items-center gap-1 mt-2 px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    View Photo
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Status Badge -->
          <div class="flex items-center justify-center p-4 rounded-lg" :class="{
            'bg-yellow-50 border border-yellow-200': viewingKyc.status === 'PENDING',
            'bg-green-50 border border-green-200': viewingKyc.status === 'APPROVED',
            'bg-red-50 border border-red-200': viewingKyc.status === 'REJECTED'
          }">
            <span :class="{
              'bg-yellow-100 text-yellow-800 border-yellow-300': viewingKyc.status === 'PENDING',
              'bg-green-100 text-green-800 border-green-300': viewingKyc.status === 'APPROVED',
              'bg-red-100 text-red-800 border-red-300': viewingKyc.status === 'REJECTED'
            }" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold border">
              <span :class="{
                'w-2 h-2 bg-yellow-500 rounded-full animate-pulse': viewingKyc.status === 'PENDING',
                'w-2 h-2 bg-green-500 rounded-full': viewingKyc.status === 'APPROVED',
                'w-2 h-2 bg-red-500 rounded-full': viewingKyc.status === 'REJECTED'
              }"></span>
              {{ viewingKyc.status }}
            </span>
          </div>

          <!-- Rejection Reason -->
          <div v-if="viewingKyc.rejection_reason" class="bg-red-50 p-4 rounded-lg border border-red-200">
            <p class="text-xs font-semibold text-red-600 uppercase mb-1">Rejection Reason</p>
            <p class="text-sm text-gray-900">{{ viewingKyc.rejection_reason }}</p>
          </div>

          <!-- Review Info -->
          <div v-if="viewingKyc.reviewed_at" class="bg-gray-50 p-4 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Review Information</p>
            <p class="text-xs text-gray-500">Reviewed: {{ formatDate(viewingKyc.reviewed_at) }} at {{ formatTime(viewingKyc.reviewed_at) }}</p>
            <p v-if="viewingKyc.reviewer" class="text-xs text-gray-500">By: {{ viewingKyc.reviewer.full_name }}</p>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 pt-4 border-t">
            <button
              v-if="viewingKyc.status === 'PENDING'"
              @click="approveKyc(viewingKyc.id)"
              class="flex-1 py-3 px-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg font-semibold hover:shadow-lg hover:shadow-green-500/50 transition-all flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Approve KYC
            </button>
            <button
              v-if="viewingKyc.status === 'PENDING'"
              @click="rejectKyc(viewingKyc.id)"
              class="flex-1 py-3 px-4 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-semibold hover:shadow-lg hover:shadow-red-500/50 transition-all flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              Reject KYC
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
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  kycVerifications: Array,
  stats: Object,
  settings: Object,
});

const statusFilter = ref('PENDING');
const searchQuery = ref('');
const viewingKyc = ref(null);

const filteredKyc = computed(() => {
  let filtered = props.kycVerifications.filter(k => k.status === statusFilter.value);

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(k =>
      k.user?.full_name?.toLowerCase().includes(query) ||
      k.user?.email?.toLowerCase().includes(query)
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

const viewKyc = (kyc) => viewingKyc.value = kyc;

const applySearch = () => {
  // Filter is reactive, just for UI feedback
};

const approveKyc = (id) => {
  Swal.fire({
    title: 'Approve KYC?',
    text: 'User will be able to proceed with withdrawals.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, approve',
    confirmButtonColor: '#10b981',
    cancelButtonColor: '#6b7280'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/kyc/${id}/approve`, {}, {
        preserveScroll: true,
        onSuccess: () => {
          viewingKyc.value = null;
          Swal.fire({
            icon: 'success',
            title: 'KYC Approved',
            text: 'User has been notified and can now withdraw.',
            confirmButtonColor: '#10b981'
          });
        }
      });
    }
  });
};

const rejectKyc = (id) => {
  Swal.fire({
    title: 'Reject KYC?',
    input: 'textarea',
    inputLabel: 'Rejection Reason',
    inputPlaceholder: 'Enter reason (e.g., unclear documents, mismatched info)...',
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
      router.post(`/admin/kyc/${id}/reject`, { reason: result.value }, {
        preserveScroll: true,
        onSuccess: () => {
          viewingKyc.value = null;
          Swal.fire({
            icon: 'success',
            title: 'KYC Rejected',
            text: 'User has been notified to resubmit.',
            confirmButtonColor: '#ef4444'
          });
        }
      });
    }
  });
};
</script>
