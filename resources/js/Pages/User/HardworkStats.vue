<template>
  <UserLayout title="Hardwork Stats">
    <!-- Page Header Banner -->
    <div class="group relative bg-gradient-to-br from-purple-500/10 via-pink-600/10 to-transparent backdrop-blur-xl rounded-2xl border border-purple-500/30 p-6 mb-6 hover:border-purple-500/50 hover:shadow-2xl hover:shadow-purple-500/20 transition-all duration-300 overflow-hidden">
      <!-- Animated Background -->
      <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-purple-500/10 to-pink-500/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-500"></div>

      <div class="relative z-10">
        <div class="flex items-center gap-3 mb-3">
          <div class="p-3 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-lg shadow-purple-500/30">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </div>
          <div>
            <h2 class="text-white font-bold text-xl">Agent Hardwork Statistics</h2>
            <p class="text-gray-400 text-sm">Comprehensive analytics of your network performance</p>
          </div>
        </div>
        <div class="bg-purple-500/10 border border-purple-500/30 rounded-xl p-4 mt-4">
          <p class="text-purple-200 text-sm leading-relaxed">
            <span class="font-semibold">Agent Dashboard:</span> This exclusive dashboard provides deep insights into your referral network's financial performance, team activity, and overall growth metrics. Use these analytics to identify opportunities and optimize your network strategy.
          </p>
        </div>
      </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <!-- Network Size -->
      <div class="bg-gradient-to-br from-blue-500/20 to-blue-600/10 backdrop-blur-xl rounded-xl border border-blue-500/30 p-4 hover:shadow-xl hover:shadow-blue-500/20 transition-all duration-300">
        <div class="flex items-center justify-between mb-3">
          <div class="p-2.5 rounded-lg bg-blue-500/20">
            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
        </div>
        <div class="text-xs text-gray-400 mb-1">Network Size</div>
        <div class="text-2xl font-bold text-white">{{ stats.networkSize }}</div>
        <div class="text-[10px] text-blue-400 mt-1">Total team members</div>
      </div>

      <!-- Total Value Contributed -->
      <div class="bg-gradient-to-br from-green-500/20 to-green-600/10 backdrop-blur-xl rounded-xl border border-green-500/30 p-4 hover:shadow-xl hover:shadow-green-500/20 transition-all duration-300">
        <div class="flex items-center justify-between mb-3">
          <div class="p-2.5 rounded-lg bg-green-500/20">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <div class="text-xs text-gray-400 mb-1">Total Contributed</div>
        <div class="text-xl lg:text-2xl font-bold text-white break-words">{{ currencySymbol }}{{ formatNumber(stats.totalNetworkDeposits) }}</div>
        <div class="text-[10px] text-green-400 mt-1">Deposits + Upgrades</div>
      </div>

      <!-- Total Paid Out -->
      <div class="bg-gradient-to-br from-orange-500/20 to-orange-600/10 backdrop-blur-xl rounded-xl border border-orange-500/30 p-4 hover:shadow-xl hover:shadow-orange-500/20 transition-all duration-300">
        <div class="flex items-center justify-between mb-3">
          <div class="p-2.5 rounded-lg bg-orange-500/20">
            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
          </div>
        </div>
        <div class="text-xs text-gray-400 mb-1">Total Paid Out</div>
        <div class="text-xl lg:text-2xl font-bold text-white break-words">{{ currencySymbol }}{{ formatNumber(stats.totalNetworkWithdrawals) }}</div>
        <div class="text-[10px] text-orange-400 mt-1">Network withdrawals</div>
      </div>

      <!-- Net Retention -->
      <div class="bg-gradient-to-br from-purple-500/20 to-purple-600/10 backdrop-blur-xl rounded-xl border border-purple-500/30 p-4 hover:shadow-xl hover:shadow-purple-500/20 transition-all duration-300">
        <div class="flex items-center justify-between mb-3">
          <div class="p-2.5 rounded-lg bg-purple-500/20">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
          </div>
        </div>
        <div class="text-xs text-gray-400 mb-1">Net Retention</div>
        <div :class="['text-xl lg:text-2xl font-bold break-words', stats.netGain >= 0 ? 'text-green-400' : 'text-red-400']">
          {{ currencySymbol }}{{ formatNumber(stats.netGain) }}
        </div>
        <div class="text-[10px] text-purple-400 mt-1">{{ stats.retentionRate }}% retention</div>
      </div>
    </div>

    <!-- Financial Overview Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Deposit Analysis -->
      <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-6">
        <div class="flex items-center gap-2 mb-4">
          <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <h3 class="text-white font-bold text-lg">Deposit Analysis</h3>
        </div>
        <p class="text-gray-400 text-xs mb-4">Total value deposited into the system by your network</p>

        <div class="space-y-3">
          <div class="bg-white/5 rounded-lg p-4 border border-green-500/20">
            <div class="flex justify-between items-center mb-2">
              <span class="text-gray-400 text-sm">Activation Payments</span>
              <span class="text-white font-semibold">{{ currencySymbol }}{{ formatNumber(stats.totalActivations) }}</span>
            </div>
            <div class="w-full bg-white/10 rounded-full h-2">
              <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full" :style="{ width: getPercentage(stats.totalActivations, stats.totalNetworkDeposits) + '%' }"></div>
            </div>
            <p class="text-[10px] text-gray-500 mt-1">{{ getPercentage(stats.totalActivations, stats.totalNetworkDeposits) }}% of total deposits</p>
          </div>

          <div class="bg-white/5 rounded-lg p-4 border border-blue-500/20">
            <div class="flex justify-between items-center mb-2">
              <span class="text-gray-400 text-sm">Plan Upgrades</span>
              <span class="text-white font-semibold">{{ currencySymbol }}{{ formatNumber(stats.totalUpgrades) }}</span>
            </div>
            <div class="w-full bg-white/10 rounded-full h-2">
              <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full" :style="{ width: getPercentage(stats.totalUpgrades, stats.totalNetworkDeposits) + '%' }"></div>
            </div>
            <p class="text-[10px] text-gray-500 mt-1">{{ getPercentage(stats.totalUpgrades, stats.totalNetworkDeposits) }}% of total deposits</p>
          </div>

          <div class="bg-gradient-to-r from-green-500/10 to-blue-500/10 rounded-lg p-4 border border-green-500/30">
            <div class="flex justify-between items-center">
              <span class="text-green-400 font-semibold">Total Deposits</span>
              <span class="text-white text-lg font-bold">{{ currencySymbol }}{{ formatNumber(stats.totalNetworkDeposits) }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Earnings Breakdown -->
      <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-6">
        <div class="flex items-center gap-2 mb-4">
          <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <h3 class="text-white font-bold text-lg">Network Earnings</h3>
        </div>
        <p class="text-gray-400 text-xs mb-4">How your network has earned across different categories</p>

        <div class="space-y-3">
          <div class="bg-white/5 rounded-lg p-4 border border-yellow-500/20">
            <div class="flex justify-between items-center mb-2">
              <span class="text-gray-400 text-sm">Task Earnings</span>
              <span class="text-white font-semibold">{{ currencySymbol }}{{ formatNumber(stats.totalTaskEarnings) }}</span>
            </div>
            <div class="w-full bg-white/10 rounded-full h-2">
              <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 h-2 rounded-full" :style="{ width: getPercentage(stats.totalTaskEarnings, stats.totalEarnings) + '%' }"></div>
            </div>
            <p class="text-[10px] text-gray-500 mt-1">{{ getPercentage(stats.totalTaskEarnings, stats.totalEarnings) }}% of earnings</p>
          </div>

          <div class="bg-white/5 rounded-lg p-4 border border-pink-500/20">
            <div class="flex justify-between items-center mb-2">
              <span class="text-gray-400 text-sm">Referral Bonuses</span>
              <span class="text-white font-semibold">{{ currencySymbol }}{{ formatNumber(stats.totalReferralBonuses) }}</span>
            </div>
            <div class="w-full bg-white/10 rounded-full h-2">
              <div class="bg-gradient-to-r from-pink-500 to-pink-600 h-2 rounded-full" :style="{ width: getPercentage(stats.totalReferralBonuses, stats.totalEarnings) + '%' }"></div>
            </div>
            <p class="text-[10px] text-gray-500 mt-1">{{ getPercentage(stats.totalReferralBonuses, stats.totalEarnings) }}% of earnings</p>
          </div>

          <div class="bg-white/5 rounded-lg p-4 border border-purple-500/20">
            <div class="flex justify-between items-center mb-2">
              <span class="text-gray-400 text-sm">Team Commissions</span>
              <span class="text-white font-semibold">{{ currencySymbol }}{{ formatNumber(stats.totalTeamCommissions) }}</span>
            </div>
            <div class="w-full bg-white/10 rounded-full h-2">
              <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full" :style="{ width: getPercentage(stats.totalTeamCommissions, stats.totalEarnings) + '%' }"></div>
            </div>
            <p class="text-[10px] text-gray-500 mt-1">{{ getPercentage(stats.totalTeamCommissions, stats.totalEarnings) }}% of earnings</p>
          </div>

          <div class="bg-gradient-to-r from-yellow-500/10 to-purple-500/10 rounded-lg p-4 border border-yellow-500/30">
            <div class="flex justify-between items-center">
              <span class="text-yellow-400 font-semibold">Total Earnings</span>
              <span class="text-white text-lg font-bold">{{ currencySymbol }}{{ formatNumber(stats.totalEarnings) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Network Performance Insights -->
    <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-6">
      <div class="flex items-center gap-2 mb-6">
        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <h3 class="text-white font-bold text-lg">Performance Insights</h3>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-blue-500/10 to-blue-600/5 rounded-xl p-4 border border-blue-500/20">
          <div class="text-gray-400 text-xs mb-1">Average Deposit per Member</div>
          <div class="text-white text-xl font-bold">{{ currencySymbol }}{{ formatNumber(stats.avgDepositPerMember) }}</div>
          <p class="text-blue-400 text-[10px] mt-2">Total deposits ÷ network size</p>
        </div>

        <div class="bg-gradient-to-br from-green-500/10 to-green-600/5 rounded-xl p-4 border border-green-500/20">
          <div class="text-gray-400 text-xs mb-1">Average Earnings per Member</div>
          <div class="text-white text-xl font-bold">{{ currencySymbol }}{{ formatNumber(stats.avgEarningsPerMember) }}</div>
          <p class="text-green-400 text-[10px] mt-2">Total earnings ÷ network size</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500/10 to-purple-600/5 rounded-xl p-4 border border-purple-500/20">
          <div class="text-gray-400 text-xs mb-1">Value Retention Rate</div>
          <div :class="['text-xl font-bold', stats.retentionRate >= 50 ? 'text-green-400' : 'text-orange-400']">
            {{ stats.retentionRate }}%
          </div>
          <p class="text-purple-400 text-[10px] mt-2">(Deposits - Withdrawals) ÷ Deposits</p>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { computed } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
  stats: Object,
  currencySymbol: {
    type: String,
    default: '₦'
  }
});

const formatNumber = (num) => {
  return new Intl.NumberFormat('en-NG').format(num || 0);
};

const getPercentage = (part, total) => {
  if (!total || total === 0) return 0;
  return Math.round((part / total) * 100);
};
</script>
