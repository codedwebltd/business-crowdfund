<template>
  <UserLayout title="Transaction History">
    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
      <!-- Total Earned -->
      <div class="bg-white/10 backdrop-blur-xl rounded-xl border border-white/20 p-4 hover:shadow-xl hover:shadow-white/10 transition-all duration-300">
        <div class="flex items-start justify-between mb-3">
          <div class="p-2.5 rounded-lg bg-green-500/20">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <div class="text-xs text-gray-400 mb-1">Total Earned</div>
        <div class="text-2xl font-bold text-white">₦{{ formatNumber(stats.total_earned) }}</div>
      </div>

      <!-- Total Withdrawn -->
      <div class="bg-white/10 backdrop-blur-xl rounded-xl border border-white/20 p-4 hover:shadow-xl hover:shadow-white/10 transition-all duration-300">
        <div class="flex items-start justify-between mb-3">
          <div class="p-2.5 rounded-lg bg-orange-500/20">
            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
          </div>
        </div>
        <div class="text-xs text-gray-400 mb-1">Total Withdrawn</div>
        <div class="text-2xl font-bold text-white">₦{{ formatNumber(stats.total_withdrawn) }}</div>
      </div>

      <!-- This Month -->
      <div class="bg-white/10 backdrop-blur-xl rounded-xl border border-white/20 p-4 hover:shadow-xl hover:shadow-white/10 transition-all duration-300">
        <div class="flex items-start justify-between mb-3">
          <div class="p-2.5 rounded-lg bg-blue-500/20">
            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
          </div>
        </div>
        <div class="text-xs text-gray-400 mb-1">This Month</div>
        <div class="text-2xl font-bold text-white">₦{{ formatNumber(stats.this_month_earnings) }}</div>
      </div>

      <!-- Pending -->
      <div class="bg-white/10 backdrop-blur-xl rounded-xl border border-white/20 p-4 hover:shadow-xl hover:shadow-white/10 transition-all duration-300">
        <div class="flex items-start justify-between mb-3">
          <div class="p-2.5 rounded-lg bg-purple-500/20">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <div class="text-xs text-gray-400 mb-1">Pending</div>
        <div class="text-2xl font-bold text-white">{{ stats.pending_transactions }}</div>
      </div>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-5 mb-6">
      <!-- Search Bar -->
      <div class="mb-4">
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
          <input
            v-model="searchQuery"
            @input="applyFilters"
            type="text"
            placeholder="Search by description, ID, or amount..."
            class="w-full bg-white/10 border border-white/20 rounded-lg pl-12 pr-4 py-3 text-white text-sm placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
          >
        </div>
      </div>

      <!-- Filters Row -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2 block">Type</label>
          <select v-model="filters.type" @change="applyFilters" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            <option value="">All Types</option>
            <option v-for="type in transactionTypes" :key="type" :value="type">{{ type }}</option>
          </select>
        </div>

        <div>
          <label class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2 block">Status</label>
          <select v-model="filters.status" @change="applyFilters" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            <option value="">All Status</option>
            <option value="PENDING">Pending</option>
            <option value="COMPLETED">Completed</option>
            <option value="REJECTED">Rejected</option>
            <option value="CANCELLED">Cancelled</option>
          </select>
        </div>

        <div>
          <label class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2 block">From</label>
          <input type="date" v-model="filters.date_from" @change="applyFilters" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
        </div>

        <div>
          <label class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2 block">To</label>
          <input type="date" v-model="filters.date_to" @change="applyFilters" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2.5 text-white text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
        </div>
      </div>

      <button v-if="hasActiveFilters || searchQuery" @click="clearFilters" class="mt-4 text-orange-400 hover:text-orange-300 text-sm font-semibold flex items-center gap-2 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Clear All Filters
      </button>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full table-auto">
          <thead class="bg-white/5">
            <tr class="border-b-2 border-white/10">
              <th class="text-left px-6 py-4 text-gray-300 text-xs font-bold uppercase tracking-wider whitespace-nowrap">Actions</th>
              <th class="text-left px-6 py-4 text-gray-300 text-xs font-bold uppercase tracking-wider whitespace-nowrap">Date</th>
              <th class="text-left px-6 py-4 text-gray-300 text-xs font-bold uppercase tracking-wider whitespace-nowrap">Amount</th>
              <th class="text-left px-6 py-4 text-gray-300 text-xs font-bold uppercase tracking-wider whitespace-nowrap">Type</th>
              <th class="text-left px-6 py-4 text-gray-300 text-xs font-bold uppercase tracking-wider whitespace-nowrap">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/5">
            <tr v-for="transaction in transactions.data" :key="transaction.id" class="hover:bg-white/5 transition-all duration-150">
              <!-- Actions -->
              <td class="px-6 py-5 whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <button
                    @click="viewTransaction(transaction)"
                    class="p-2.5 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg transition-all duration-200 group"
                    title="View Details"
                  >
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                  <button
                    @click="reportTransaction(transaction)"
                    class="p-2.5 bg-orange-500/20 hover:bg-orange-500/30 text-orange-400 rounded-lg transition-all duration-200 group"
                    title="Report Issue"
                  >
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                  </button>
                </div>
              </td>

              <!-- Date -->
              <td class="px-6 py-5 whitespace-nowrap">
                <div class="text-white text-sm font-medium">{{ formatDateOnly(transaction.created_at) }}</div>
                <div class="text-gray-400 text-xs mt-0.5">{{ formatTimeOnly(transaction.created_at) }}</div>
              </td>

              <!-- Amount -->
              <td class="px-6 py-5 whitespace-nowrap">
                <div class="flex items-baseline gap-1">
                  <span class="text-xs font-medium" :class="transaction.is_credit ? 'text-green-400' : 'text-orange-400'">
                    {{ transaction.is_credit ? '+' : '-' }}
                  </span>
                  <span class="text-gray-400 text-xs font-medium">{{ transaction.currency || $page.props.globalSettings?.platform_currency || '₦' }}</span>
                  <span class="text-lg font-bold text-white">
                    {{ formatNumber(transaction.amount) }}
                  </span>
                </div>
              </td>

              <!-- Type -->
              <td class="px-6 py-5">
                <div class="flex items-center gap-2">
                  <div class="w-2 h-2 rounded-full flex-shrink-0" :class="transaction.is_credit ? 'bg-green-400' : 'bg-orange-400'"></div>
                  <span class="text-white text-sm font-medium">{{ formatTransactionType(transaction.transaction_type) }}</span>
                </div>
              </td>

              <!-- Status -->
              <td class="px-6 py-5 whitespace-nowrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold" :class="getStatusColor(transaction.status)">
                  <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotColor(transaction.status)"></span>
                  {{ transaction.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="transactions.data.length > 0" class="p-4 border-t border-white/10 flex items-center justify-between">
        <div class="text-gray-400 text-sm">
          Showing {{ transactions.from }} to {{ transactions.to }} of {{ transactions.total }} transactions
        </div>
        <div class="flex gap-2">
          <Link v-for="link in transactions.links" :key="link.label" :href="link.url" :class="[
            'px-3 py-1 rounded text-sm font-semibold transition-colors',
            link.active ? 'bg-purple-600 text-white' : 'bg-white/10 text-gray-400 hover:bg-white/20 hover:text-white',
            !link.url && 'opacity-50 cursor-not-allowed'
          ]" v-html="link.label"></Link>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="transactions.data.length === 0" class="p-12 text-center">
        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <p class="text-gray-400 text-lg">No transactions found</p>
        <p class="text-gray-500 text-sm mt-2">Your transaction history will appear here</p>
      </div>
    </div>

    <!-- Transaction Detail Modal -->
    <Teleport to="body">
      <div v-if="selectedTransaction" @click="selectedTransaction = null" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div @click.stop class="bg-gradient-to-br from-gray-900 via-gray-900 to-purple-900/20 rounded-2xl border border-purple-500/30 p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl shadow-purple-500/20">
          <!-- Modal Header -->
          <div class="flex items-center justify-between mb-6 pb-4 border-b border-white/10">
            <h3 class="text-2xl font-bold text-white flex items-center gap-3">
              <div class="p-2 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              Transaction Details
            </h3>
            <button @click="selectedTransaction = null" class="text-gray-400 hover:text-white transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Transaction Info Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <!-- Transaction ID -->
            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
              <p class="text-gray-400 text-xs mb-1 uppercase tracking-wider">Transaction ID</p>
              <div class="flex items-center gap-2">
                <p class="text-white text-sm font-mono">{{ selectedTransaction.id.substring(0, 8) }}...</p>
                <button @click="copyToClipboard(selectedTransaction.id)" class="text-purple-400 hover:text-purple-300">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Type -->
            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
              <p class="text-gray-400 text-xs mb-1 uppercase tracking-wider">Type</p>
              <span class="inline-block px-3 py-1 rounded text-sm font-semibold" :class="getTypeColor(selectedTransaction.transaction_type)">
                {{ selectedTransaction.transaction_type }}
              </span>
            </div>

            <!-- Amount -->
            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
              <p class="text-gray-400 text-xs mb-1 uppercase tracking-wider">Amount</p>
              <p class="text-2xl font-bold" :class="selectedTransaction.is_credit ? 'text-green-400' : 'text-orange-400'">
                {{ selectedTransaction.is_credit ? '+' : '-' }}₦{{ formatNumber(selectedTransaction.amount) }}
              </p>
            </div>

            <!-- Status -->
            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
              <p class="text-gray-400 text-xs mb-1 uppercase tracking-wider">Status</p>
              <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold" :class="getStatusColor(selectedTransaction.status)">
                {{ selectedTransaction.status }}
              </span>
            </div>

            <!-- Date -->
            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
              <p class="text-gray-400 text-xs mb-1 uppercase tracking-wider">Date & Time</p>
              <p class="text-white text-sm">{{ formatDateTime(selectedTransaction.created_at) }}</p>
            </div>

            <!-- Balance Type -->
            <div class="bg-white/5 rounded-lg p-4 border border-white/10">
              <p class="text-gray-400 text-xs mb-1 uppercase tracking-wider">Balance Type</p>
              <p class="text-white text-sm">{{ selectedTransaction.balance_type }}</p>
            </div>
          </div>

          <!-- Description -->
          <div class="bg-white/5 rounded-lg p-4 border border-white/10 mb-6">
            <p class="text-gray-400 text-xs mb-2 uppercase tracking-wider">Description</p>
            <p class="text-white text-sm">{{ selectedTransaction.description || 'No description available' }}</p>
          </div>

          <!-- Metadata -->
          <div v-if="selectedTransaction.metadata && Object.keys(selectedTransaction.metadata).length > 0" class="bg-white/5 rounded-lg p-4 border border-white/10">
            <p class="text-gray-400 text-xs mb-3 uppercase tracking-wider">Additional Details</p>
            <div class="grid grid-cols-1 gap-2">
              <div v-for="(value, key) in selectedTransaction.metadata" :key="key" class="flex justify-between text-sm">
                <span class="text-gray-400">{{ formatKey(key) }}:</span>
                <span class="text-white font-semibold">{{ formatValue(value) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </UserLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  transactions: Object,
  stats: Object,
  transactionTypes: Array,
  filters: Object,
});

const selectedTransaction = ref(null);
const searchQuery = ref('');

const filters = ref({
  type: props.filters.type || '',
  status: props.filters.status || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
});

const hasActiveFilters = computed(() => {
  return filters.value.type || filters.value.status || filters.value.date_from || filters.value.date_to;
});

function formatNumber(num) {
  return new Intl.NumberFormat().format(num || 0);
}

function formatDateOnly(date) {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
}

function formatTimeOnly(date) {
  return new Date(date).toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  });
}

function formatDateTime(date) {
  return new Date(date).toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  });
}

function formatTransactionType(type) {
  return type.replace(/_/g, ' ').toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
}

function formatKey(key) {
  return key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
}

function formatValue(value) {
  if (typeof value === 'boolean') return value ? 'Yes' : 'No';
  if (typeof value === 'number') return formatNumber(value);
  return value;
}

function getTypeColor(type) {
  const colors = {
    'WITHDRAWAL': 'bg-orange-500/20 text-orange-400',
    'TASK_EARNING': 'bg-green-500/20 text-green-400',
    'REFERRAL_BONUS': 'bg-blue-500/20 text-blue-400',
    'RANK_BONUS': 'bg-purple-500/20 text-purple-400',
    'MATURATION': 'bg-cyan-500/20 text-cyan-400',
    'ADJUSTMENT': 'bg-yellow-500/20 text-yellow-400',
    'PENALTY': 'bg-red-500/20 text-red-400',
  };
  return colors[type] || 'bg-gray-500/20 text-gray-400';
}

function getStatusColor(status) {
  const colors = {
    'PENDING': 'bg-yellow-500/10 text-yellow-300 border border-yellow-500/30',
    'COMPLETED': 'bg-green-500/10 text-green-300 border border-green-500/30',
    'APPROVED': 'bg-blue-500/10 text-blue-300 border border-blue-500/30',
    'REJECTED': 'bg-red-500/10 text-red-300 border border-red-500/30',
    'CANCELLED': 'bg-gray-500/10 text-gray-300 border border-gray-500/30',
  };
  return colors[status] || 'bg-gray-500/10 text-gray-300 border border-gray-500/30';
}

function getStatusDotColor(status) {
  const colors = {
    'PENDING': 'bg-yellow-400 animate-pulse',
    'COMPLETED': 'bg-green-400',
    'APPROVED': 'bg-blue-400',
    'REJECTED': 'bg-red-400',
    'CANCELLED': 'bg-gray-400',
  };
  return colors[status] || 'bg-gray-400';
}

function viewTransaction(transaction) {
  selectedTransaction.value = transaction;
}

async function reportTransaction(transaction) {
  const result = await Swal.fire({
    title: 'Report Transaction Issue',
    html: `
      <div class="text-left">
        <p class="text-gray-300 mb-4">Are you sure you want to report an issue with this transaction?</p>
        <div class="bg-white/5 rounded-lg p-3 mb-4 border border-white/10">
          <p class="text-sm text-gray-400 mb-1">Transaction ID</p>
          <p class="text-white font-mono text-sm">${transaction.id.substring(0, 16)}...</p>
        </div>
        <div class="bg-white/5 rounded-lg p-3 border border-white/10">
          <p class="text-sm text-gray-400 mb-1">Amount</p>
          <p class="text-white font-semibold">${transaction.is_credit ? '+' : '-'}₦${formatNumber(transaction.amount)}</p>
        </div>
      </div>
    `,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, Report',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#f97316',
    cancelButtonColor: '#6b7280',
    background: '#1e293b',
    color: '#fff',
    customClass: {
      popup: 'rounded-2xl border border-orange-500/30',
      title: 'text-xl font-bold',
      confirmButton: 'px-6 py-2.5 rounded-lg font-semibold',
      cancelButton: 'px-6 py-2.5 rounded-lg font-semibold'
    }
  });

  if (result.isConfirmed) {
    await Swal.fire({
      title: 'Report Submitted!',
      html: `
        <div class="text-center">
          <p class="text-gray-300 mb-3">Your report has been submitted successfully.</p>
          <p class="text-sm text-gray-400">Our team will review this transaction and get back to you within 24-48 hours.</p>
        </div>
      `,
      icon: 'success',
      confirmButtonText: 'OK',
      confirmButtonColor: '#10b981',
      background: '#1e293b',
      color: '#fff',
      customClass: {
        popup: 'rounded-2xl border border-green-500/30',
        confirmButton: 'px-6 py-2.5 rounded-lg font-semibold'
      }
    });
  }
}

function copyToClipboard(text) {
  navigator.clipboard.writeText(text);
  Swal.fire({
    title: 'Copied!',
    text: 'Transaction ID copied to clipboard',
    icon: 'success',
    timer: 1500,
    showConfirmButton: false,
    background: '#1e293b',
    color: '#fff',
    customClass: {
      popup: 'rounded-2xl border border-purple-500/30'
    }
  });
}

function applyFilters() {
  router.get('/transactions', { ...filters.value, search: searchQuery.value }, { preserveState: true });
}

function clearFilters() {
  filters.value = {
    type: '',
    status: '',
    date_from: '',
    date_to: '',
  };
  searchQuery.value = '';
  router.get('/transactions');
}
</script>
