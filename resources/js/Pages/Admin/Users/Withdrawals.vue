<template>
  <AdminLayout title="User Withdrawals" :settings="settings">
    <!-- Breadcrumbs -->
    <Breadcrumbs :crumbs="[
      { label: 'Dashboard', url: '/admin/dashboard' },
      { label: 'Users', url: '/admin/users' },
      { label: user.full_name, url: `/admin/users/${user.id}` },
      { label: 'Withdrawals' }
    ]" />

    <div class="mb-6 mt-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Withdrawals</h1>
          <p class="text-gray-600 mt-1">{{ user.full_name }}'s withdrawal history</p>
        </div>
        <Link :href="`/admin/users/${user.id}`" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition-all">
          ← Back to User
        </Link>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-600">Total</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total }}</p>
          </div>
          <div class="p-2 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-600">Pending</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ stats.pending }}</p>
          </div>
          <div class="p-2 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-600">Approved</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ stats.approved }}</p>
          </div>
          <div class="p-2 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-600">Completed</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ stats.completed }}</p>
          </div>
          <div class="p-2 bg-gradient-to-br from-green-500 to-green-600 rounded-xl">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-600">Rejected</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ stats.rejected }}</p>
          </div>
          <div class="p-2 bg-gradient-to-br from-red-500 to-red-600 rounded-xl">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 hover:shadow-lg transition-all col-span-2 lg:col-span-1">
        <div>
          <p class="text-xs font-medium text-gray-600">Total Amount</p>
          <p class="text-xl font-bold text-purple-600 mt-1">₦{{ formatNumber(stats.total_amount) }}</p>
          <p class="text-xs text-gray-500 mt-1">Completed only</p>
        </div>
      </div>
    </div>

    <!-- Withdrawals Table -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-green-500 to-emerald-600">
        <h2 class="text-lg font-bold text-white">All Withdrawals</h2>
        <p class="text-green-100 text-sm mt-1">{{ withdrawals.total }} withdrawals found</p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b-2 border-gray-200">
            <tr>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Bank Details</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Requested</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Processed</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="withdrawal in withdrawals.data" :key="withdrawal.id" class="hover:bg-green-50 transition-colors">
              <td class="px-4 py-4">
                <div class="font-bold text-lg text-gray-900">₦{{ formatNumber(withdrawal.amount_requested) }}</div>
                <div v-if="withdrawal.fee_amount > 0" class="text-xs text-gray-500 mt-1">
                  Fee: ₦{{ formatNumber(withdrawal.fee_amount) }}
                </div>
              </td>
              <td class="px-4 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ withdrawal.bank_name }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ withdrawal.account_number }}</div>
                <div class="text-xs text-gray-500">{{ withdrawal.account_name }}</div>
              </td>
              <td class="px-4 py-4">
                <span :class="getStatusClass(withdrawal.status)" class="px-3 py-2 rounded-lg text-xs font-bold inline-flex items-center gap-2 shadow-sm w-32 justify-center">
                  <span class="w-2 h-2 bg-current rounded-full"></span>
                  {{ withdrawal.status }}
                </span>
              </td>
              <td class="px-4 py-4">
                <div class="text-xs text-gray-600">{{ formatDate(withdrawal.created_at) }}</div>
              </td>
              <td class="px-4 py-4">
                <div class="text-xs text-gray-600">{{ withdrawal.processed_at ? formatDate(withdrawal.processed_at) : '-' }}</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="text-sm text-gray-600">
          Showing {{ withdrawals.from }} to {{ withdrawals.to }} of {{ withdrawals.total }} withdrawals
        </div>
        <div class="flex gap-2">
          <button
            v-for="link in withdrawals.links"
            :key="link.label"
            @click="changePage(link.url)"
            :disabled="!link.url"
            :class="link.active ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
            class="px-3 py-1 rounded border text-sm disabled:opacity-50"
            v-html="link.label"
          ></button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const props = defineProps({
  user: Object,
  withdrawals: Object,
  stats: Object,
  settings: Object,
});

const getStatusClass = (status) => {
  const classes = {
    'PENDING': 'bg-yellow-100 text-yellow-700 border border-yellow-200',
    'APPROVED': 'bg-blue-100 text-blue-700 border border-blue-200',
    'COMPLETED': 'bg-green-100 text-green-700 border border-green-200',
    'REJECTED': 'bg-red-100 text-red-700 border border-red-200',
    'PROCESSING': 'bg-orange-100 text-orange-700 border border-orange-200',
  };
  return classes[status] || 'bg-gray-100 text-gray-700 border border-gray-200';
};

const formatNumber = (num) => {
  return new Intl.NumberFormat().format(num || 0);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};

const changePage = (url) => {
  if (url) router.visit(url);
};
</script>
