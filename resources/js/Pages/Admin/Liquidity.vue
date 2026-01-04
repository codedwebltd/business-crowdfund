<template>
  <AdminLayout title="Liquidity & Earnings" :settings="settings">
    <!-- Breadcrumbs -->
    <Breadcrumbs :crumbs="[
      { label: 'Home', url: '/admin/dashboard' },
      { label: 'Liquidity' }
    ]" class="mb-4"/>

    <!-- Status Banner Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
      <!-- Liquidity Status -->
      <div class="lg:col-span-2 rounded-2xl shadow-lg overflow-hidden" :class="{
        'bg-gradient-to-br from-green-500 to-emerald-600': stats.liquidity_status === 'healthy',
        'bg-gradient-to-br from-yellow-500 to-orange-500': stats.liquidity_status === 'caution',
        'bg-gradient-to-br from-orange-500 to-red-500': stats.liquidity_status === 'critical',
        'bg-gradient-to-br from-red-600 to-rose-800': stats.liquidity_status === 'collapse_imminent'
      }">
        <div class="p-6 text-white">
          <div class="flex items-start gap-4">
            <div class="text-6xl opacity-90">{{ stats.liquidity_status === 'healthy' ? '✅' : stats.liquidity_status === 'caution' ? '⚠️' : stats.liquidity_status === 'critical' ? '🚨' : '☠️' }}</div>
            <div class="flex-1">
              <p class="text-sm font-semibold opacity-90 mb-1">Platform Status</p>
              <h3 class="text-3xl font-bold mb-2">{{ stats.liquidity_status.toUpperCase().replace('_', ' ') }}</h3>
              <p class="text-sm leading-relaxed opacity-95">{{ getStatusMessage(stats.liquidity_status) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- User Stats Card -->
      <div class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl shadow-lg overflow-hidden">
        <div class="p-6 text-white">
          <div class="flex items-center gap-3 mb-3">
            <div class="bg-white/20 rounded-full p-3 backdrop-blur-sm">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </div>
            <div>
              <p class="text-purple-100 text-sm font-semibold">Total Users</p>
            </div>
          </div>
          <div class="space-y-2">
            <p class="text-5xl font-bold tracking-tight">{{ stats.total_users.toLocaleString() }}</p>
            <div class="flex items-center gap-2 bg-white/10 rounded-lg px-3 py-2 backdrop-blur-sm">
              <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
              <p class="text-sm font-semibold text-green-100">{{ stats.active_users.toLocaleString() }} Active Users</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Revenue Overview Card -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 mb-6">
      <h2 class="text-lg font-bold text-gray-900 mb-4">Revenue Overview</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-5 text-white">
          <p class="text-green-100 text-sm font-semibold mb-2">Total Revenue</p>
          <p class="text-3xl font-bold">{{ formatCurrency(stats.total_income) }}</p>
          <p class="text-green-100 text-xs mt-2">From activations</p>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-5 text-white">
          <p class="text-red-100 text-sm font-semibold mb-2">Total Expenses</p>
          <p class="text-3xl font-bold">{{ formatCurrency(stats.total_expenses) }}</p>
          <p class="text-red-100 text-xs mt-2">All payouts</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-5 text-white">
          <p class="text-purple-100 text-sm font-semibold mb-2">Net Profit</p>
          <p class="text-3xl font-bold">{{ formatCurrency(stats.net_profit) }}</p>
          <p class="text-purple-100 text-xs mt-2">{{ ((stats.net_profit/stats.total_income)*100).toFixed(1) }}% margin</p>
        </div>
      </div>
    </div>

    <!-- Detailed Stats Card -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 mb-6">
      <h2 class="text-lg font-bold text-gray-900 mb-4">Financial Breakdown</h2>
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-4">
          <p class="text-xs font-semibold text-blue-700 uppercase mb-1">Activations</p>
          <p class="text-2xl font-bold text-blue-900">{{ formatCurrency(stats.total_activations) }}</p>
        </div>
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-xl p-4">
          <p class="text-xs font-semibold text-orange-700 uppercase mb-1">Task Earnings</p>
          <p class="text-2xl font-bold text-orange-900">{{ formatCurrency(stats.total_task_earnings) }}</p>
        </div>
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-xl p-4">
          <p class="text-xs font-semibold text-indigo-700 uppercase mb-1">Referral Bonuses</p>
          <p class="text-2xl font-bold text-indigo-900">{{ formatCurrency(stats.total_referral_bonuses) }}</p>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl p-4">
          <p class="text-xs font-semibold text-red-700 uppercase mb-1">Withdrawals</p>
          <p class="text-2xl font-bold text-red-900">{{ formatCurrency(stats.total_withdrawals) }}</p>
        </div>
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-xl p-4">
          <p class="text-xs font-semibold text-yellow-700 uppercase mb-1">Pending W/D</p>
          <p class="text-2xl font-bold text-yellow-900">{{ formatCurrency(stats.pending_withdrawals) }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-4">
          <p class="text-xs font-semibold text-purple-700 uppercase mb-1">Withdrawable</p>
          <p class="text-2xl font-bold text-purple-900">{{ formatCurrency(stats.platform_balance) }}</p>
        </div>
        <div class="bg-gradient-to-br from-pink-50 to-pink-100 border border-pink-200 rounded-xl p-4">
          <p class="text-xs font-semibold text-pink-700 uppercase mb-1">Pending Balance</p>
          <p class="text-2xl font-bold text-pink-900">{{ formatCurrency(stats.total_pending_balance) }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-4">
          <p class="text-xs font-semibold text-green-700 uppercase mb-1">Today Burn Rate</p>
          <p class="text-2xl font-bold" :class="{
            'text-green-900': stats.today_burn_rate < 0.7,
            'text-yellow-900': stats.today_burn_rate >= 0.7 && stats.today_burn_rate < 0.9,
            'text-red-900': stats.today_burn_rate >= 0.9
          }">{{ (stats.today_burn_rate * 100).toFixed(1) }}%</p>
        </div>
      </div>
    </div>

    <!-- Token Rate Periods Card -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden mb-6">
      <div class="p-5 border-b bg-gradient-to-r from-indigo-500 to-purple-600 flex justify-between items-center">
        <div>
          <h2 class="text-lg font-bold text-white">Token Rate Periods</h2>
          <p class="text-purple-100 text-sm mt-1">Profit by token price (for ledger)</p>
        </div>
        <button @click="printLedger" class="px-4 py-2 bg-white text-purple-600 rounded-lg font-semibold hover:shadow-lg transition-all">
          🖨️ Print
        </button>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Period</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Token Price</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">W/D Rate</th>
              <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Revenue</th>
              <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Withdrawals</th>
              <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Expenses</th>
              <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Net Profit</th>
              <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-if="tokenRatePeriods.length === 0">
              <td colspan="8" class="px-4 py-6 text-center text-gray-500 text-sm">No data</td>
            </tr>
            <tr v-for="period in tokenRatePeriods" :key="period.rate_id" class="hover:bg-gray-50">
              <td class="px-4 py-3">
                <div class="text-xs font-semibold text-gray-900 whitespace-nowrap">{{ formatDateShort(period.period_start) }}</div>
                <div class="text-xs text-gray-500">{{ period.duration_days }}d</div>
              </td>
              <td class="px-4 py-3 text-sm font-bold text-gray-900 whitespace-nowrap">{{ formatCurrency(period.token_price) }}</td>
              <td class="px-4 py-3 text-sm font-bold text-blue-600 whitespace-nowrap">{{ (period.withdrawal_rate * 100).toFixed(0) }}%</td>
              <td class="px-4 py-3 text-sm font-bold text-green-600 text-right whitespace-nowrap">{{ formatCurrency(period.activations_ngn) }}</td>
              <td class="px-4 py-3 text-sm font-bold text-orange-600 text-right whitespace-nowrap">{{ formatCurrency(period.withdrawals_ngn) }}</td>
              <td class="px-4 py-3 text-sm font-bold text-red-600 text-right whitespace-nowrap">{{ formatCurrency(period.total_expenses) }}</td>
              <td class="px-4 py-3 text-sm font-bold text-right whitespace-nowrap" :class="period.period_profit >= 0 ? 'text-green-600' : 'text-red-600'">
                {{ formatCurrency(period.period_profit) }}
              </td>
              <td class="px-4 py-3 text-center">
                <button @click="viewPeriodDetails(period)" class="text-purple-600 hover:text-purple-800 font-semibold text-xs">View</button>
              </td>
            </tr>
            <tr v-if="tokenRatePeriods.length > 0" class="bg-purple-50 font-bold border-t-2 border-purple-300">
              <td colspan="3" class="px-4 py-3 text-right text-gray-900 uppercase text-xs">Totals:</td>
              <td class="px-4 py-3 text-sm text-green-700 text-right whitespace-nowrap">{{ formatCurrency(tokenRatePeriods.reduce((s,p)=>s+parseFloat(p.activations_ngn),0)) }}</td>
              <td class="px-4 py-3 text-sm text-orange-700 text-right whitespace-nowrap">{{ formatCurrency(tokenRatePeriods.reduce((s,p)=>s+parseFloat(p.withdrawals_ngn),0)) }}</td>
              <td class="px-4 py-3 text-sm text-red-700 text-right whitespace-nowrap">{{ formatCurrency(tokenRatePeriods.reduce((s,p)=>s+parseFloat(p.total_expenses),0)) }}</td>
              <td class="px-4 py-3 text-sm text-right whitespace-nowrap" :class="tokenRatePeriods.reduce((s,p)=>s+parseFloat(p.period_profit),0)>=0?'text-green-700':'text-red-700'">
                {{ formatCurrency(tokenRatePeriods.reduce((s,p)=>s+parseFloat(p.period_profit),0)) }}
              </td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Burn Rate History Card -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
      <div class="p-5 border-b bg-gradient-to-r from-pink-500 to-red-600">
        <h2 class="text-lg font-bold text-white">Daily Burn Rate</h2>
        <p class="text-pink-100 text-sm mt-1">Last 30 days</p>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Date</th>
              <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Deposits</th>
              <th class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Withdrawals</th>
              <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Burn Rate</th>
              <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Status</th>
              <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-if="burnRateHistory.length === 0">
              <td colspan="6" class="px-4 py-6 text-center text-gray-500 text-sm">No burn rate data</td>
            </tr>
            <tr v-for="record in burnRateHistory" :key="record.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 text-sm font-semibold text-gray-900 whitespace-nowrap">{{ formatDate(record.report_date) }}</td>
              <td class="px-4 py-3 text-sm font-bold text-green-600 text-right whitespace-nowrap">{{ formatCurrency(record.total_deposits) }}</td>
              <td class="px-4 py-3 text-sm font-bold text-red-600 text-right whitespace-nowrap">{{ formatCurrency(record.total_withdrawals) }}</td>
              <td class="px-4 py-3 text-center">
                <span class="text-lg font-bold" :class="{
                  'text-green-600': record.burn_rate < 0.7,
                  'text-yellow-600': record.burn_rate >= 0.7 && record.burn_rate < 0.9,
                  'text-red-600': record.burn_rate >= 0.9
                }">{{ (record.burn_rate * 100).toFixed(1) }}%</span>
              </td>
              <td class="px-4 py-3 text-center">
                <span :class="{
                  'bg-green-100 text-green-800': record.liquidity_status === 'healthy',
                  'bg-yellow-100 text-yellow-800': record.liquidity_status === 'caution',
                  'bg-red-100 text-red-800': record.liquidity_status !== 'healthy' && record.liquidity_status !== 'caution'
                }" class="px-2 py-1 rounded text-xs font-bold whitespace-nowrap">
                  {{ record.liquidity_status.toUpperCase() }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <button @click="deleteRecord(record.id)" class="text-red-600 hover:text-red-800 text-xs font-semibold">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Period Details Modal -->
    <div v-if="viewingPeriod" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="viewingPeriod = null">
      <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b bg-gradient-to-r from-indigo-500 to-purple-600">
          <div class="flex justify-between items-start">
            <div class="text-white">
              <h3 class="text-xl font-bold">Period Details</h3>
              <p class="text-purple-100 text-sm mt-1">{{ viewingPeriod.period_start }} - {{ viewingPeriod.period_end }}</p>
            </div>
            <button @click="viewingPeriod = null" class="text-white hover:bg-white/20 rounded-lg p-2">✕</button>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
              <p class="text-xs font-semibold text-blue-700 uppercase mb-1">Token Price</p>
              <p class="text-2xl font-bold text-blue-900">{{ formatCurrency(viewingPeriod.token_price) }}</p>
            </div>
            <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
              <p class="text-xs font-semibold text-indigo-700 uppercase mb-1">Withdrawal Rate</p>
              <p class="text-2xl font-bold text-indigo-900">{{ (viewingPeriod.withdrawal_rate * 100).toFixed(0) }}%</p>
            </div>
          </div>
          <div class="grid grid-cols-3 gap-4">
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
              <p class="text-xs font-semibold text-green-700 uppercase mb-1">Revenue</p>
              <p class="text-xl font-bold text-green-900">{{ formatCurrency(viewingPeriod.activations_ngn) }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
              <p class="text-xs font-semibold text-red-700 uppercase mb-1">Expenses</p>
              <p class="text-xl font-bold text-red-900">{{ formatCurrency(viewingPeriod.total_expenses) }}</p>
            </div>
            <div :class="viewingPeriod.period_profit >= 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'" class="p-4 rounded-lg border">
              <p class="text-xs font-semibold uppercase mb-1" :class="viewingPeriod.period_profit >= 0 ? 'text-green-700' : 'text-red-700'">Net Profit</p>
              <p class="text-xl font-bold" :class="viewingPeriod.period_profit >= 0 ? 'text-green-900' : 'text-red-900'">{{ formatCurrency(viewingPeriod.period_profit) }}</p>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
              <p class="text-xs font-semibold text-orange-700 uppercase mb-1">Withdrawals</p>
              <p class="text-lg font-bold text-orange-900">{{ formatCurrency(viewingPeriod.withdrawals_ngn) }}</p>
              <p class="text-xs text-orange-600 mt-1">{{ viewingPeriod.withdrawals_count }} requests</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
              <p class="text-xs font-semibold text-purple-700 uppercase mb-1">Tokens Issued</p>
              <p class="text-lg font-bold text-purple-900">{{ viewingPeriod.tokens_to_users.toLocaleString() }}</p>
              <p class="text-xs text-purple-600 mt-1">+{{ viewingPeriod.tokens_retained.toLocaleString() }} retained</p>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
              <p class="text-xs font-semibold text-yellow-700 uppercase mb-1">Task Earnings</p>
              <p class="text-lg font-bold text-yellow-900">{{ formatCurrency(viewingPeriod.task_earnings) }}</p>
            </div>
            <div class="bg-pink-50 p-4 rounded-lg border border-pink-200">
              <p class="text-xs font-semibold text-pink-700 uppercase mb-1">Referral Bonuses</p>
              <p class="text-lg font-bold text-pink-900">{{ formatCurrency(viewingPeriod.referral_bonuses) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  settings: Object,
  burnRateHistory: Array,
  tokenRatePeriods: Array,
  stats: Object,
  currencySymbol: String,
});

const viewingPeriod = ref(null);

const formatCurrency = (amount) => {
  return (props.currencySymbol || '₦') + parseFloat(amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const formatDate = (date) => new Date(date).toLocaleDateString('en-US', {
  month: 'short',
  day: 'numeric',
  year: 'numeric'
});

const formatDateShort = (dateStr) => {
  const d = new Date(dateStr);
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};

const getStatusMessage = (status) => {
  const messages = {
    healthy: 'Platform liquidity is healthy. Revenue exceeds expenses.',
    caution: 'Withdrawals approaching deposit levels. Monitor closely.',
    critical: 'Urgent: Withdrawals exceeding deposits. Add funds immediately.',
    collapse_imminent: 'CRITICAL: Platform cannot sustain withdrawal rate!'
  };
  return messages[status] || 'Status unknown';
};

const viewPeriodDetails = (period) => {
  viewingPeriod.value = period;
};

const deleteRecord = (id) => {
  Swal.fire({
    title: 'Delete Record?',
    text: 'Permanently delete this burn rate record?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Delete',
    confirmButtonColor: '#ef4444'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/admin/liquidity/${id}`, {
        preserveScroll: true,
        onSuccess: () => Swal.fire('Deleted!', '', 'success')
      });
    }
  });
};

const printLedger = () => window.print();
</script>

<style>
@media print {
  body * { visibility: hidden; }
  .print-section, .print-section * { visibility: visible; }
  .print-section { position: absolute; left: 0; top: 0; }
}
</style>
