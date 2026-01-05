<template>
  <AdminLayout title="Withdrawals" :settings="settings">
    <div class="mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Withdrawals</h1>
      <p class="text-gray-600 mt-1">Review and process withdrawal requests</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Pending</p>
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
            <p class="text-xs sm:text-sm font-medium text-gray-600">Processing</p>
            <p class="text-2xl sm:text-3xl font-bold text-blue-600 mt-1">{{ stats.processing }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Completed</p>
            <p class="text-2xl sm:text-3xl font-bold text-green-600 mt-1">{{ stats.completed }}</p>
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
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4 mb-6">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <select v-model="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
          <option value="PENDING">Pending ({{ stats.pending }})</option>
          <option value="PROCESSING">Processing ({{ stats.processing }})</option>
          <option value="COMPLETED">Completed ({{ stats.completed }})</option>
          <option value="REJECTED">Rejected ({{ stats.rejected }})</option>
        </select>
        <input v-model="searchQuery" type="text" placeholder="Search by user, ID, or account..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
        <button @click="applySearch" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg text-sm font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition-all">
          Apply Filter
        </button>
      </div>
    </div>

    <!-- Withdrawals Table -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-purple-500 to-pink-600">
        <h2 class="text-lg font-bold text-white">All Withdrawals</h2>
        <p class="text-purple-100 text-sm mt-1">{{ filteredWithdrawals.length }} withdrawals found</p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b-2 border-gray-200">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Withdrawal ID</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Amount</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Method</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="withdrawal in filteredWithdrawals" :key="withdrawal.id" class="hover:bg-purple-50 transition-colors">
              <td class="px-6 py-5">
                <div class="font-semibold text-gray-900 text-sm">{{ withdrawal.user?.full_name || 'Unknown' }}</div>
                <div class="text-xs text-gray-500 mt-0.5">{{ withdrawal.user?.email || 'N/A' }}</div>
              </td>
              <td class="px-6 py-5">
                <div class="font-mono text-xs text-gray-900 font-bold max-w-[120px] truncate" :title="withdrawal.id">
                  {{ withdrawal.id.substring(0, 8) }}...
                </div>
              </td>
              <td class="px-6 py-5">
                <div class="text-sm font-bold text-gray-900">{{ formatCurrency(withdrawal.amount_requested) }}</div>
              </td>
              <td class="px-6 py-5">
                <span class="text-xs font-semibold px-2 py-1 rounded bg-gray-100 text-gray-700">{{ withdrawal.payment_method }}</span>
              </td>
              <td class="px-6 py-5">
                <div class="text-sm text-gray-900 font-medium whitespace-nowrap">{{ formatDate(withdrawal.created_at) }}</div>
                <div class="text-xs text-gray-500">{{ formatTime(withdrawal.created_at) }}</div>
              </td>
              <td class="px-6 py-5">
                <span
                  :class="{
                    'bg-yellow-100 text-yellow-800 border-yellow-200': withdrawal.status === 'PENDING',
                    'bg-blue-100 text-blue-800 border-blue-200': withdrawal.status === 'PROCESSING',
                    'bg-green-100 text-green-800 border-green-200': withdrawal.status === 'COMPLETED',
                    'bg-red-100 text-red-800 border-red-200': withdrawal.status === 'REJECTED'
                  }"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border whitespace-nowrap"
                >
                  <span :class="{
                    'w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse': withdrawal.status === 'PENDING',
                    'w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse': withdrawal.status === 'PROCESSING',
                    'w-1.5 h-1.5 bg-green-500 rounded-full': withdrawal.status === 'COMPLETED',
                    'w-1.5 h-1.5 bg-red-500 rounded-full': withdrawal.status === 'REJECTED'
                  }"></span>
                  {{ withdrawal.status }}
                </span>
              </td>
              <td class="px-6 py-5">
                <div class="flex items-center justify-end gap-2">
                  <button @click="viewWithdrawal(withdrawal)" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="View Details">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                  <button
                    v-if="withdrawal.status === 'PENDING'"
                    @click="markProcessing(withdrawal.id)"
                    class="p-2 text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-lg transition-all shadow-sm"
                    title="Mark Processing"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                  </button>
                  <button
                    v-if="['PENDING', 'PROCESSING'].includes(withdrawal.status)"
                    @click="approveWithdrawal(withdrawal.id)"
                    class="p-2 text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg transition-all shadow-sm"
                    title="Approve & Complete"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </button>
                  <button
                    v-if="['PENDING', 'PROCESSING'].includes(withdrawal.status)"
                    @click="rejectWithdrawal(withdrawal.id)"
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
    <div v-if="viewingWithdrawal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-2 sm:p-4 z-50" @click.self="viewingWithdrawal = null">
      <div class="bg-white rounded-xl sm:rounded-2xl max-w-3xl w-full max-h-[95vh] overflow-y-auto">
        <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-purple-500 to-pink-600">
          <div class="flex justify-between items-start">
            <div class="text-white">
              <h3 class="text-lg sm:text-xl font-bold">Withdrawal Details</h3>
              <p class="text-purple-100 text-xs sm:text-sm mt-1 break-all">{{ viewingWithdrawal.transaction_reference || viewingWithdrawal.id }}</p>
            </div>
            <button @click="viewingWithdrawal = null" class="text-white hover:bg-white/20 rounded-lg p-1.5 sm:p-2 transition-colors">
              <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        <div class="p-4 sm:p-6 space-y-3 sm:space-y-4">
          <!-- User Info -->
          <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 uppercase mb-2">User Details</p>
            <p class="text-sm font-bold text-gray-900">{{ viewingWithdrawal.user?.full_name || 'Unknown' }}</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ viewingWithdrawal.user?.email || 'N/A' }}</p>
            <p class="text-xs text-gray-500">{{ viewingWithdrawal.user?.phone_number || 'N/A' }}</p>
          </div>

          <!-- Amount & Payment Info -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-3 sm:p-4 rounded-lg border border-purple-200">
              <p class="text-xs font-semibold text-purple-700 uppercase mb-1">Amount</p>
              <p class="text-xl sm:text-2xl font-bold text-purple-900">{{ formatCurrency(viewingWithdrawal.amount_requested) }}</p>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-3 sm:p-4 rounded-lg border border-blue-200">
              <p class="text-xs font-semibold text-blue-700 uppercase mb-1">Method</p>
              <p class="text-base sm:text-lg font-bold text-blue-900">{{ viewingWithdrawal.payment_method }}</p>
            </div>
          </div>

          <!-- Crypto Value (USDT) -->
          <div v-if="viewingWithdrawal.meta_data?.crypto_value_usdt" class="bg-gradient-to-br from-green-50 to-emerald-100 p-3 sm:p-4 rounded-lg border border-green-200">
            <div class="flex items-center justify-between mb-2">
              <p class="text-xs font-semibold text-green-700 uppercase flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Crypto Value (USDT)
              </p>
              <span class="text-xs bg-green-200 text-green-800 px-2 py-1 rounded-full font-semibold">Real-time Rate</span>
            </div>
            <p class="text-xl sm:text-2xl font-bold text-green-900">
              {{ Number(viewingWithdrawal.meta_data.crypto_value_usdt).toFixed(2) }} USDT
            </p>
            <div class="mt-2 pt-2 border-t border-green-200 space-y-1">
              <p class="text-xs text-green-700">
                <span class="font-semibold">Exchange Rate:</span> 1 USDT = {{ viewingWithdrawal.meta_data.usdt_rate ? Number(viewingWithdrawal.meta_data.usdt_rate).toFixed(2) : 'N/A' }} {{ settings.platform_currency || 'NGN' }}
              </p>
              <p class="text-xs text-green-700">
                <span class="font-semibold">Calculated:</span> {{ viewingWithdrawal.meta_data.crypto_calculated_at || 'N/A' }}
              </p>
            </div>
          </div>

          <!-- Bank/Crypto Details -->
          <div class="bg-gray-50 p-3 sm:p-4 rounded-lg" v-if="viewingWithdrawal.payment_method === 'BANK'">
            <p class="text-xs font-semibold text-gray-600 uppercase mb-2">Bank Details</p>
            <div class="space-y-1">
              <p class="text-xs sm:text-sm"><span class="font-semibold">Bank:</span> {{ viewingWithdrawal.bank_name }}</p>
              <p class="text-xs sm:text-sm"><span class="font-semibold">Account:</span> {{ viewingWithdrawal.account_number }}</p>
              <p class="text-xs sm:text-sm"><span class="font-semibold">Name:</span> {{ viewingWithdrawal.account_name }}</p>
            </div>
          </div>

          <div class="bg-gray-50 p-3 sm:p-4 rounded-lg" v-if="viewingWithdrawal.payment_method === 'CRYPTO'">
            <p class="text-xs font-semibold text-gray-600 uppercase mb-2">Crypto Wallet Details</p>
            <div class="space-y-1">
              <p class="text-xs sm:text-sm"><span class="font-semibold">Coin:</span> {{ viewingWithdrawal.wallet_details?.coin_name }}</p>
              <p class="text-xs sm:text-sm"><span class="font-semibold">Network:</span> {{ viewingWithdrawal.wallet_details?.coin_network }}</p>
              <p class="text-xs sm:text-sm break-all"><span class="font-semibold">Address:</span> {{ viewingWithdrawal.wallet_details?.wallet_address }}</p>
            </div>
          </div>

          <!-- Status Badge -->
          <div class="flex items-center justify-center p-3 sm:p-4 rounded-lg" :class="{
            'bg-yellow-50 border border-yellow-200': viewingWithdrawal.status === 'PENDING',
            'bg-blue-50 border border-blue-200': viewingWithdrawal.status === 'PROCESSING',
            'bg-green-50 border border-green-200': viewingWithdrawal.status === 'COMPLETED',
            'bg-red-50 border border-red-200': viewingWithdrawal.status === 'REJECTED'
          }">
            <span :class="{
              'bg-yellow-100 text-yellow-800 border-yellow-300': viewingWithdrawal.status === 'PENDING',
              'bg-blue-100 text-blue-800 border-blue-300': viewingWithdrawal.status === 'PROCESSING',
              'bg-green-100 text-green-800 border-green-300': viewingWithdrawal.status === 'COMPLETED',
              'bg-red-100 text-red-800 border-red-300': viewingWithdrawal.status === 'REJECTED'
            }" class="inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-bold border">
              <span :class="{
                'w-1.5 h-1.5 sm:w-2 sm:h-2 bg-yellow-500 rounded-full animate-pulse': viewingWithdrawal.status === 'PENDING',
                'w-1.5 h-1.5 sm:w-2 sm:h-2 bg-blue-500 rounded-full animate-pulse': viewingWithdrawal.status === 'PROCESSING',
                'w-1.5 h-1.5 sm:w-2 sm:h-2 bg-green-500 rounded-full': viewingWithdrawal.status === 'COMPLETED',
                'w-1.5 h-1.5 sm:w-2 sm:h-2 bg-red-500 rounded-full': viewingWithdrawal.status === 'REJECTED'
              }"></span>
              {{ viewingWithdrawal.status }}
            </span>
          </div>

          <!-- Rejection Reason -->
          <div v-if="viewingWithdrawal.rejection_reason" class="bg-red-50 border border-red-200 p-3 sm:p-4 rounded-lg">
            <p class="text-xs font-semibold text-red-700 uppercase mb-1">Rejection Reason</p>
            <p class="text-xs sm:text-sm text-red-900">{{ viewingWithdrawal.rejection_reason }}</p>
          </div>

          <!-- Metadata (Collapsible) -->
          <details v-if="viewingWithdrawal.meta_data" class="bg-gray-50 p-3 sm:p-4 rounded-lg border border-gray-200">
            <summary class="text-xs font-semibold text-gray-700 uppercase cursor-pointer hover:text-purple-600">View Metadata (JSON)</summary>
            <pre class="text-xs bg-white p-3 rounded border border-gray-200 overflow-x-auto text-gray-800 font-mono mt-3">{{ JSON.stringify(viewingWithdrawal.meta_data, null, 2) }}</pre>
          </details>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 pt-3 sm:pt-4 pb-1 border-t">
            <button
              v-if="viewingWithdrawal.status === 'PENDING'"
              @click="markProcessing(viewingWithdrawal.id)"
              class="w-full sm:flex-1 py-2.5 px-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg text-xs sm:text-sm font-semibold hover:shadow-lg hover:shadow-blue-500/50 transition-all flex items-center justify-center gap-1.5"
            >
              <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              <span class="hidden sm:inline">Mark Processing</span>
              <span class="sm:hidden">Processing</span>
            </button>
            <button
              v-if="['PENDING', 'PROCESSING'].includes(viewingWithdrawal.status)"
              @click="approveWithdrawal(viewingWithdrawal.id)"
              class="w-full sm:flex-1 py-2.5 px-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg text-xs sm:text-sm font-semibold hover:shadow-lg hover:shadow-green-500/50 transition-all flex items-center justify-center gap-1.5"
            >
              <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span class="hidden sm:inline">Approve & Complete</span>
              <span class="sm:hidden">Approve</span>
            </button>
            <button
              v-if="['PENDING', 'PROCESSING'].includes(viewingWithdrawal.status)"
              @click="rejectWithdrawal(viewingWithdrawal.id)"
              class="w-full sm:flex-1 py-2.5 px-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg text-xs sm:text-sm font-semibold hover:shadow-lg hover:shadow-red-500/50 transition-all flex items-center justify-center gap-1.5"
            >
              <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              Reject
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
  withdrawals: Array,
  stats: Object,
  currencySymbol: String,
  settings: Object,
});

const statusFilter = ref('PENDING');
const searchQuery = ref('');
const viewingWithdrawal = ref(null);

const filteredWithdrawals = computed(() => {
  let filtered = props.withdrawals.filter(w => w.status === statusFilter.value);

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(w =>
      w.user?.full_name?.toLowerCase().includes(query) ||
      w.user?.phone_number?.toLowerCase().includes(query) ||
      w.account_number?.toLowerCase().includes(query) ||
      w.id?.toLowerCase().includes(query)
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

const viewWithdrawal = (withdrawal) => viewingWithdrawal.value = withdrawal;

const applySearch = () => {
  // Filter is reactive, just for UI feedback
};

const markProcessing = (id) => {
  Swal.fire({
    title: 'Mark as Processing?',
    text: 'This indicates you are working on this withdrawal.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, mark processing',
    confirmButtonColor: '#3b82f6',
    cancelButtonColor: '#6b7280'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/withdrawals/${id}/processing`, {}, {
        preserveScroll: true,
        onSuccess: () => {
          viewingWithdrawal.value = null;
          Swal.fire({
            icon: 'success',
            title: 'Status Updated',
            text: 'Withdrawal marked as processing.',
            confirmButtonColor: '#3b82f6'
          });
        }
      });
    }
  });
};

const approveWithdrawal = (id) => {
  Swal.fire({
    title: 'Approve Withdrawal?',
    text: 'User will receive payment confirmation with receipt.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, approve',
    confirmButtonColor: '#10b981',
    cancelButtonColor: '#6b7280'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/withdrawals/${id}/approve`, {}, {
        preserveScroll: true,
        onSuccess: () => {
          viewingWithdrawal.value = null;
          Swal.fire({
            icon: 'success',
            title: 'Withdrawal Approved',
            text: 'User notified with receipt.',
            confirmButtonColor: '#10b981'
          });
        }
      });
    }
  });
};

const rejectWithdrawal = (id) => {
  Swal.fire({
    title: 'Reject Withdrawal?',
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
      router.post(`/admin/withdrawals/${id}/reject`, { reason: result.value }, {
        preserveScroll: true,
        onSuccess: () => {
          viewingWithdrawal.value = null;
          Swal.fire({
            icon: 'success',
            title: 'Withdrawal Rejected',
            text: 'Balance restored to user.',
            confirmButtonColor: '#ef4444'
          });
        }
      });
    }
  });
};
</script>
