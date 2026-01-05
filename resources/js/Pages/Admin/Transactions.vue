<template>
  <AdminLayout title="Transaction Management" :settings="settings">
    <div class="mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Transaction Management</h1>
      <p class="text-gray-600 mt-1">Review and approve activation payments</p>
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
            <p class="text-xs sm:text-sm font-medium text-gray-600">Total</p>
            <p class="text-2xl sm:text-3xl font-bold text-purple-600 mt-1">{{ stats.pending + stats.approved + stats.rejected }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4 mb-6">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <select v-model="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
          <option value="AWAITING_APPROVAL">Pending ({{ stats.pending }})</option>
          <option value="APPROVED">Approved ({{ stats.approved }})</option>
          <option value="REJECTED">Rejected ({{ stats.rejected }})</option>
        </select>
        <input v-model="searchQuery" type="text" placeholder="Search by user or ID..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
        <button @click="applySearch" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg text-sm font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition-all">
          Apply Filter
        </button>
      </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-purple-500 to-pink-600">
        <h2 class="text-lg font-bold text-white">All Transactions</h2>
        <p class="text-purple-100 text-sm mt-1">{{ filteredTransactions.length }} transactions found</p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b-2 border-gray-200">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Transaction ID</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Hash</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="txn in filteredTransactions" :key="txn.id" class="hover:bg-purple-50 transition-colors">
              <td class="px-6 py-5">
                <div class="font-semibold text-gray-900 text-sm">{{ txn.user?.full_name || 'Unknown' }}</div>
              </td>
              <td class="px-6 py-5">
                <div class="font-mono text-xs text-gray-900 font-bold max-w-[120px] truncate" :title="txn.id">
                  {{ txn.id.substring(0, 8) }}...
                </div>
              </td>
              <td class="px-6 py-5">
                <span class="font-mono text-xs text-gray-900 font-bold max-w-[100px] truncate inline-block" :title="txn.transaction_hash">{{ txn.transaction_hash.substring(0, 8) }}...</span>
              </td>
              <td class="px-6 py-5">
                <div class="text-sm font-bold text-gray-900">{{ formatCurrency(txn.amount) }}</div>
                <div class="text-xs text-gray-500 mt-0.5">{{ txn.metadata?.plan_name || 'N/A' }}</div>
              </td>
              <td class="px-6 py-5">
                <div class="text-sm text-gray-900 font-medium whitespace-nowrap">{{ formatDate(txn.created_at) }}, {{ formatTime(txn.created_at) }}</div>
              </td>
              <td class="px-6 py-5">
                <span
                  :class="{
                    'bg-yellow-100 text-yellow-800 border-yellow-200': txn.status === 'AWAITING_APPROVAL',
                    'bg-green-100 text-green-800 border-green-200': txn.status === 'APPROVED',
                    'bg-red-100 text-red-800 border-red-200': txn.status === 'REJECTED'
                  }"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border whitespace-nowrap"
                >
                  <span :class="{
                    'w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse': txn.status === 'AWAITING_APPROVAL',
                    'w-1.5 h-1.5 bg-green-500 rounded-full': txn.status === 'APPROVED',
                    'w-1.5 h-1.5 bg-red-500 rounded-full': txn.status === 'REJECTED'
                  }"></span>
                  {{ txn.status.replace('_', ' ') }}
                </span>
              </td>
              <td class="px-6 py-5">
                <div class="flex items-center justify-end gap-2">
                  <button @click="viewTransaction(txn)" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="View Details">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                  <button
                    v-if="txn.status === 'AWAITING_APPROVAL'"
                    @click="approveTxn(txn.id)"
                    class="p-2 text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg transition-all shadow-sm"
                    title="Approve"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </button>
                  <button
                    v-if="txn.status === 'AWAITING_APPROVAL'"
                    @click="rejectTxn(txn.id)"
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
    <div v-if="viewingTransaction" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="viewingTransaction = null">
      <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b bg-gradient-to-r from-purple-500 to-pink-600">
          <div class="flex justify-between items-start">
            <div class="text-white">
              <h3 class="text-xl font-bold">Transaction Details</h3>
              <p class="text-purple-100 text-sm mt-1">{{ viewingTransaction.transaction_hash }}</p>
            </div>
            <button @click="viewingTransaction = null" class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <!-- User & Basic Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-xs font-semibold text-gray-600 uppercase mb-1">User Details</p>
              <p class="text-sm font-bold text-gray-900">{{ viewingTransaction.user?.full_name || 'Unknown' }}</p>
              <p class="text-xs text-gray-500 mt-0.5">{{ viewingTransaction.user?.email || 'N/A' }}</p>
              <p class="text-xs text-gray-500">{{ viewingTransaction.user?.phone_number || 'N/A' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Transaction ID</p>
              <p class="text-sm font-bold text-gray-900 font-mono">{{ viewingTransaction.transaction_hash }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ formatDate(viewingTransaction.created_at) }} at {{ formatTime(viewingTransaction.created_at) }}</p>
            </div>
          </div>

          <!-- Payment Details -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
              <p class="text-xs font-semibold text-purple-700 uppercase mb-1">Plan</p>
              <p class="text-lg font-bold text-purple-900">{{ viewingTransaction.metadata?.plan_name || 'N/A' }}</p>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
              <p class="text-xs font-semibold text-green-700 uppercase mb-1">Amount (Local)</p>
              <p class="text-lg font-bold text-green-900">{{ formatCurrency(viewingTransaction.amount) }}</p>
              <p class="text-xs text-green-700">{{ viewingTransaction.metadata?.local_currency || viewingTransaction.currency }}</p>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
              <p class="text-xs font-semibold text-blue-700 uppercase mb-1">Payment Method</p>
              <p class="text-sm font-bold text-blue-900 capitalize">{{ viewingTransaction.metadata?.payment_method?.replace('_', ' ') || 'N/A' }}</p>
            </div>
          </div>

          <!-- Crypto Conversion (if available) -->
          <div v-if="viewingTransaction.metadata?.crypto_amount" class="bg-gradient-to-br from-orange-50 to-yellow-50 p-5 rounded-xl border-2 border-orange-300">
            <div class="flex items-center gap-2 mb-3">
              <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <p class="text-sm font-bold text-orange-800 uppercase">Cryptocurrency Payment Details</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-white p-4 rounded-lg">
              <div>
                <p class="text-xs text-gray-600 font-semibold mb-1">Crypto Amount</p>
                <p class="text-2xl font-bold text-orange-600 font-mono">{{ viewingTransaction.metadata.crypto_amount.toFixed(6) }}</p>
                <p class="text-xs text-gray-500">{{ viewingTransaction.metadata.crypto_currency }}</p>
              </div>
              <div>
                <p class="text-xs text-gray-600 font-semibold mb-1">Conversion Rate</p>
                <p class="text-lg font-bold text-gray-900">1 {{ viewingTransaction.metadata.crypto_currency }} = {{ viewingTransaction.metadata.conversion_rate?.toLocaleString() }} {{ viewingTransaction.metadata.local_currency }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ viewingTransaction.metadata.conversion_display }}</p>
              </div>
            </div>
          </div>

          <!-- Status Badge -->
          <div class="flex items-center justify-center p-4 rounded-lg" :class="{
            'bg-yellow-50 border border-yellow-200': viewingTransaction.status === 'AWAITING_APPROVAL',
            'bg-green-50 border border-green-200': viewingTransaction.status === 'APPROVED',
            'bg-red-50 border border-red-200': viewingTransaction.status === 'REJECTED'
          }">
            <span :class="{
              'bg-yellow-100 text-yellow-800 border-yellow-300': viewingTransaction.status === 'AWAITING_APPROVAL',
              'bg-green-100 text-green-800 border-green-300': viewingTransaction.status === 'APPROVED',
              'bg-red-100 text-red-800 border-red-300': viewingTransaction.status === 'REJECTED'
            }" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold border">
              <span :class="{
                'w-2 h-2 bg-yellow-500 rounded-full animate-pulse': viewingTransaction.status === 'AWAITING_APPROVAL',
                'w-2 h-2 bg-green-500 rounded-full': viewingTransaction.status === 'APPROVED',
                'w-2 h-2 bg-red-500 rounded-full': viewingTransaction.status === 'REJECTED'
              }"></span>
              {{ viewingTransaction.status.replace('_', ' ') }}
            </span>
          </div>

          <!-- Full Metadata (Collapsible) -->
          <details class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <summary class="text-xs font-semibold text-gray-700 uppercase cursor-pointer hover:text-purple-600">View Full Metadata (JSON)</summary>
            <pre class="text-xs bg-white p-3 rounded border border-gray-200 overflow-x-auto text-gray-800 font-mono mt-3">{{ JSON.stringify(viewingTransaction.metadata, null, 2) }}</pre>
          </details>

          <div class="flex gap-3 pt-4 border-t">
            <button
              v-if="viewingTransaction.status === 'AWAITING_APPROVAL'"
              @click="approveTxn(viewingTransaction.id)"
              class="flex-1 py-3 px-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg font-semibold hover:shadow-lg hover:shadow-green-500/50 transition-all flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Approve Transaction
            </button>
            <button
              v-if="viewingTransaction.status === 'AWAITING_APPROVAL'"
              @click="rejectTxn(viewingTransaction.id)"
              class="flex-1 py-3 px-4 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg font-semibold hover:shadow-lg hover:shadow-red-500/50 transition-all flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              Reject Transaction
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  transactions: Array,
  stats: Object,
  currencySymbol: String,
  settings: Object,
});

const statusFilter = ref('AWAITING_APPROVAL');
const searchQuery = ref('');
const viewingTransaction = ref(null);

const filteredTransactions = computed(() => {
  let filtered = props.transactions.filter(t => t.status === statusFilter.value);

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(t =>
      t.user?.full_name?.toLowerCase().includes(query) ||
      t.user?.phone_number?.toLowerCase().includes(query) ||
      t.transaction_hash?.toLowerCase().includes(query)
    );
  }

  return filtered;
});

const formatCurrency = (amount) => {
  return (props.currencySymbol || '₦') + parseFloat(amount).toLocaleString();
};

const formatDate = (date) => new Date(date).toLocaleDateString('en-US', {
  month: 'short',
  day: 'numeric',
  year: 'numeric'
});

const formatTime = (date) => new Date(date).toLocaleTimeString('en-US', {
  hour: '2-digit',
  minute: '2-digit'
});

const viewTransaction = (txn) => viewingTransaction.value = txn;

const applySearch = () => {
  // Filter is reactive, just for UI feedback
};

const approveTxn = (id) => {
  Swal.fire({
    title: 'Approve Payment?',
    text: 'User account will be activated immediately.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, approve',
    confirmButtonColor: '#10b981',
    cancelButtonColor: '#6b7280'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/transactions/${id}/approve`, {}, {
        preserveScroll: true,
        onSuccess: () => {
          viewingTransaction.value = null;
          Swal.fire({
            icon: 'success',
            title: 'Payment Approved',
            text: 'User has been activated successfully.',
            confirmButtonColor: '#10b981'
          });
        }
      });
    }
  });
};

const rejectTxn = (id) => {
  Swal.fire({
    title: 'Reject Payment?',
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
      router.post(`/admin/transactions/${id}/reject`, { reason: result.value }, {
        preserveScroll: true,
        onSuccess: () => {
          viewingTransaction.value = null;
          Swal.fire({
            icon: 'success',
            title: 'Payment Rejected',
            text: 'Transaction has been marked as rejected.',
            confirmButtonColor: '#ef4444'
          });
        }
      });
    }
  });
};
</script>
