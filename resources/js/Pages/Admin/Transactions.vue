<template>
  <AdminLayout title="Transaction Management" :settings="settings">
    <div class="mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Transaction Management</h1>
      <p class="text-gray-600 mt-1">View all transactions including activation payments, task earnings, referral bonuses, and more</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4 mb-6">
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all cursor-pointer" @click="typeFilter = 'ACTIVATION_PAYMENT'">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Activation</p>
            <p class="text-2xl sm:text-3xl font-bold text-purple-600 mt-1">{{ typeStats.ACTIVATION_PAYMENT }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all cursor-pointer" @click="typeFilter = 'TASK_EARNING'">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Task Earnings</p>
            <p class="text-2xl sm:text-3xl font-bold text-green-600 mt-1">{{ typeStats.TASK_EARNING }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all cursor-pointer" @click="typeFilter = 'REFERRAL_BONUS'">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Referral Bonus</p>
            <p class="text-2xl sm:text-3xl font-bold text-blue-600 mt-1">{{ typeStats.REFERRAL_BONUS }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all cursor-pointer" @click="typeFilter = 'TEAM_COMMISSION'">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Team Commission</p>
            <p class="text-2xl sm:text-3xl font-bold text-orange-600 mt-1">{{ typeStats.TEAM_COMMISSION }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all cursor-pointer" @click="typeFilter = 'WITHDRAWAL'">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Withdrawals</p>
            <p class="text-2xl sm:text-3xl font-bold text-red-600 mt-1">{{ typeStats.WITHDRAWAL }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-red-500 to-red-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4 mb-6">
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <select v-model="typeFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
          <option value="ALL">All Types ({{ transactions.length }})</option>
          <option value="ACTIVATION_PAYMENT">Activation ({{ typeStats.ACTIVATION_PAYMENT }})</option>
          <option value="TASK_EARNING">Task Earnings ({{ typeStats.TASK_EARNING }})</option>
          <option value="REFERRAL_BONUS">Referral Bonus ({{ typeStats.REFERRAL_BONUS }})</option>
          <option value="TEAM_COMMISSION">Team Commission ({{ typeStats.TEAM_COMMISSION }})</option>
          <option value="WITHDRAWAL">Withdrawals ({{ typeStats.WITHDRAWAL }})</option>
        </select>
        <select v-if="typeFilter === 'ACTIVATION_PAYMENT'" v-model="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
          <option value="AWAITING_APPROVAL">Pending ({{ stats.pending }})</option>
          <option value="APPROVED">Approved ({{ stats.approved }})</option>
          <option value="REJECTED">Rejected ({{ stats.rejected }})</option>
        </select>
        <input v-model="searchQuery" type="text" placeholder="Search by user, ID, or description..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
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
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Priority/Stars</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Type</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Description</th>
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
                <div class="text-xs text-gray-500 mt-0.5">{{ txn.user?.email || '' }}</div>
              </td>
              <td class="px-6 py-5">
                <div class="flex flex-col gap-1.5">
                  <span :class="getPriorityBadgeClass(txn.priority)"
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold w-fit">
                    {{ getPriorityIcon(txn.priority) }} {{ getPriorityText(txn.priority) }}
                  </span>
                  <div v-if="txn.user?.performance" class="flex items-center gap-1">
                    <span class="text-sm">{{ getStarDisplay(txn.user.performance.star_rating) }}</span>
                    <span v-if="txn.user.performance.star_rating === 5" class="text-sm">👑</span>
                  </div>
                </div>
              </td>
              <td class="px-6 py-5">
                <span
                  :class="{
                    'bg-purple-100 text-purple-800 border-purple-200': txn.transaction_type === 'ACTIVATION_PAYMENT',
                    'bg-green-100 text-green-800 border-green-200': txn.transaction_type === 'TASK_EARNING',
                    'bg-blue-100 text-blue-800 border-blue-200': txn.transaction_type === 'REFERRAL_BONUS',
                    'bg-orange-100 text-orange-800 border-orange-200': txn.transaction_type === 'TEAM_COMMISSION',
                    'bg-red-100 text-red-800 border-red-200': txn.transaction_type === 'WITHDRAWAL'
                  }"
                  class="inline-flex px-2 py-1 rounded-lg text-xs font-bold border whitespace-nowrap"
                >
                  {{ txn.transaction_type.replace('_', ' ') }}
                </span>
              </td>
              <td class="px-6 py-5">
                <div class="text-sm text-gray-900 max-w-[200px]">{{ txn.description || txn.metadata?.plan_name || 'N/A' }}</div>
              </td>
              <td class="px-6 py-5">
                <div class="text-sm font-bold" :class="txn.is_credit ? 'text-green-600' : 'text-red-600'">
                  {{ txn.is_credit ? '+' : '-' }}{{ formatCurrency(txn.amount) }}
                </div>
              </td>
              <td class="px-6 py-5">
                <div class="text-sm text-gray-900 font-medium whitespace-nowrap">{{ formatDate(txn.created_at) }}</div>
                <div class="text-xs text-gray-500">{{ formatTime(txn.created_at) }}</div>
              </td>
              <td class="px-6 py-5">
                <span
                  :class="{
                    'bg-yellow-100 text-yellow-800 border-yellow-200': txn.status === 'AWAITING_APPROVAL',
                    'bg-green-100 text-green-800 border-green-200': txn.status === 'APPROVED' || txn.status === 'COMPLETED',
                    'bg-red-100 text-red-800 border-red-200': txn.status === 'REJECTED' || txn.status === 'FAILED',
                    'bg-blue-100 text-blue-800 border-blue-200': txn.status === 'PENDING'
                  }"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border whitespace-nowrap"
                >
                  <span :class="{
                    'w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse': txn.status === 'AWAITING_APPROVAL',
                    'w-1.5 h-1.5 bg-green-500 rounded-full': txn.status === 'APPROVED' || txn.status === 'COMPLETED',
                    'w-1.5 h-1.5 bg-red-500 rounded-full': txn.status === 'REJECTED' || txn.status === 'FAILED',
                    'w-1.5 h-1.5 bg-blue-500 rounded-full': txn.status === 'PENDING'
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
                    v-if="txn.transaction_type === 'ACTIVATION_PAYMENT' && txn.status === 'AWAITING_APPROVAL'"
                    @click="approveTxn(txn.id)"
                    class="p-2 text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg transition-all shadow-sm"
                    title="Approve"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </button>
                  <button
                    v-if="txn.transaction_type === 'ACTIVATION_PAYMENT' && txn.status === 'AWAITING_APPROVAL'"
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
  typeStats: Object,
  currencySymbol: String,
  settings: Object,
});

const statusFilter = ref('AWAITING_APPROVAL');
const typeFilter = ref('ACTIVATION_PAYMENT');
const searchQuery = ref('');
const viewingTransaction = ref(null);

const filteredTransactions = computed(() => {
  let filtered = props.transactions;

  // Filter by transaction type
  if (typeFilter.value !== 'ALL') {
    filtered = filtered.filter(t => t.transaction_type === typeFilter.value);
  }

  // Filter by status (only for activation payments)
  if (typeFilter.value === 'ACTIVATION_PAYMENT') {
    filtered = filtered.filter(t => t.status === statusFilter.value);
  }

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(t =>
      t.user?.full_name?.toLowerCase().includes(query) ||
      t.user?.phone_number?.toLowerCase().includes(query) ||
      t.transaction_hash?.toLowerCase().includes(query) ||
      t.description?.toLowerCase().includes(query)
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

const getStarDisplay = (stars) => {
  if (!stars) return '⭐';
  return '⭐'.repeat(stars);
};

const getPriorityIcon = (priority) => {
  if (!priority) return '⚪';
  const icons = { 5: '🔴', 4: '🟠', 3: '🟡', 2: '🔵', 1: '⚪' };
  return icons[priority] || '⚪';
};

const getPriorityText = (priority) => {
  if (!priority) return 'Very Low';
  const texts = { 5: 'Urgent', 4: 'High', 3: 'Medium', 2: 'Low', 1: 'Very Low' };
  return texts[priority] || 'Very Low';
};

const getPriorityBadgeClass = (priority) => {
  if (!priority) return 'bg-gray-100 text-gray-700';
  const classes = {
    5: 'bg-red-100 text-red-700 border border-red-200',
    4: 'bg-orange-100 text-orange-700 border border-orange-200',
    3: 'bg-yellow-100 text-yellow-700 border border-yellow-200',
    2: 'bg-blue-100 text-blue-700 border border-blue-200',
    1: 'bg-gray-100 text-gray-600 border border-gray-200'
  };
  return classes[priority] || 'bg-gray-100 text-gray-700';
};

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
