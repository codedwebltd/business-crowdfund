<template>
  <UserLayout title="Referrals">
    <!-- Combined Referral Card -->
    <div class="group relative bg-gradient-to-br from-orange-500/10 via-purple-600/10 to-transparent backdrop-blur-xl rounded-2xl border border-orange-500/30 p-6 mb-6 hover:border-orange-500/50 hover:shadow-2xl hover:shadow-orange-500/20 transition-all duration-300 overflow-hidden">
      <!-- Animated Background -->
      <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-orange-500/10 to-purple-500/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-500"></div>

      <div class="relative z-10">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
          <div class="p-3 bg-gradient-to-br from-orange-500 to-purple-600 rounded-2xl shadow-lg shadow-orange-500/30">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-white font-bold text-lg">Share & Earn</h3>
            <p class="text-gray-400 text-xs">Invite friends using your link or code</p>
          </div>
        </div>

        <!-- Referral Link Section -->
        <div class="mb-4">
          <label class="text-orange-400 text-xs font-semibold uppercase tracking-wider mb-2 block">Referral Link</label>
          <div class="flex gap-2">
            <div class="flex-1 bg-white/10 border border-white/20 rounded-lg px-4 py-3 overflow-x-auto scrollbar-thin scrollbar-thumb-orange-500/30 scrollbar-track-transparent">
              <p class="text-white text-sm whitespace-nowrap">{{ stats.referralLink }}</p>
            </div>
            <button
              @click="copyLink"
              class="shrink-0 bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-3 rounded-lg font-semibold hover:from-orange-600 hover:to-orange-700 transition-all flex items-center gap-2 shadow-lg shadow-orange-500/30"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
              </svg>
              <span class="hidden sm:inline">Copy</span>
            </button>
          </div>
        </div>

        <!-- Referral Code Section -->
        <div>
          <label class="text-purple-400 text-xs font-semibold uppercase tracking-wider mb-2 block">Referral Code</label>
          <div class="flex gap-2">
            <div class="flex-1 bg-white/10 border border-white/20 rounded-lg px-4 py-3 flex items-center justify-center">
              <p class="text-white text-2xl font-bold font-mono tracking-widest">{{ stats.referralCode }}</p>
            </div>
            <button
              @click="copyCode"
              class="shrink-0 bg-gradient-to-r from-purple-500 to-purple-600 text-white px-4 py-3 rounded-lg font-semibold hover:from-purple-600 hover:to-purple-700 transition-all flex items-center gap-2 shadow-lg shadow-purple-500/30"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
              </svg>
              <span class="hidden sm:inline">Copy</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
      <!-- Direct Referrals -->
      <div class="bg-white/10 backdrop-blur-xl rounded-xl border border-white/20 p-4 hover:shadow-xl hover:shadow-white/10 transition-all duration-300">
        <div class="flex items-start justify-between mb-3">
          <div class="p-2.5 rounded-lg bg-orange-500/20">
            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </div>
        </div>
        <div class="text-xs text-gray-400 mb-1">Direct Referrals</div>
        <div class="text-2xl font-bold text-white">{{ stats.directReferrals }}</div>
      </div>

      <!-- Total Network -->
      <div class="bg-white/10 backdrop-blur-xl rounded-xl border border-white/20 p-4 hover:shadow-xl hover:shadow-white/10 transition-all duration-300">
        <div class="flex items-start justify-between mb-3">
          <div class="p-2.5 rounded-lg bg-purple-500/20">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
        </div>
        <div class="text-xs text-gray-400 mb-1">Total Network</div>
        <div class="text-2xl font-bold text-white">{{ stats.totalTeam }}</div>
      </div>

      <!-- Activation Earnings -->
      <div class="bg-white/10 backdrop-blur-xl rounded-xl border border-white/20 p-4 hover:shadow-xl hover:shadow-white/10 transition-all duration-300">
        <div class="flex items-start justify-between mb-3">
          <div class="p-2.5 rounded-lg bg-blue-500/20">
            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
          </div>
        </div>
        <div class="text-xs text-gray-400 mb-1">Activation Bonus</div>
        <div class="text-2xl font-bold text-white">{{ currencySymbol }}{{ formatNumber(stats.activationEarnings) }}</div>
      </div>

      <!-- Total Earnings -->
      <div class="bg-white/10 backdrop-blur-xl rounded-xl border border-white/20 p-4 hover:shadow-xl hover:shadow-white/10 transition-all duration-300">
        <div class="flex items-start justify-between mb-3">
          <div class="p-2.5 rounded-lg bg-green-500/20">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <div class="text-xs text-gray-400 mb-1">Total Earnings</div>
        <div class="text-2xl font-bold text-white">{{ currencySymbol }}{{ formatNumber(stats.totalEarnings) }}</div>
      </div>
    </div>

    <!-- Team by Level -->
    <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-6 mb-6">
      <h3 class="text-white font-bold text-lg mb-4">Team Distribution by Level</h3>
      <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div v-for="level in 10" :key="level" class="bg-white/5 rounded-lg p-3 border border-white/10">
          <p class="text-gray-400 text-xs mb-1">Level {{ level }}</p>
          <p class="text-white text-xl font-bold">{{ stats.teamByLevel[level] || 0 }}</p>
        </div>
      </div>
    </div>

    <!-- Referral Tree -->
    <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-4 md:p-6 mb-6">
      <h3 class="text-white font-bold text-base md:text-lg mb-2">Referral Tree</h3>
      <ReferralTree
        v-if="treeData"
        :node="treeData"
        :currencySymbol="currencySymbol"
        @nodeClick="showUserModal"
      />
      <p v-else class="text-gray-400 text-center py-8">No referrals yet. Share your link to get started!</p>
    </div>

    <!-- Network Statistics Card (Agents Only) -->
    <div v-if="networkStats" class="group relative bg-gradient-to-br from-green-500/10 via-blue-600/10 to-transparent backdrop-blur-xl rounded-2xl border border-green-500/30 p-6 hover:border-green-500/50 hover:shadow-2xl hover:shadow-green-500/20 transition-all duration-300 overflow-hidden">
      <!-- Animated Background -->
      <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-green-500/10 to-blue-500/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-500"></div>

      <div class="relative z-10">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
          <div class="p-3 bg-gradient-to-br from-green-500 to-blue-600 rounded-2xl shadow-lg shadow-green-500/30">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-white font-bold text-lg">Network Performance</h3>
            <p class="text-gray-400 text-xs">Financial overview of your entire referral network</p>
          </div>
        </div>

        <!-- Explanation Banner -->
        <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-4 mb-6">
          <p class="text-blue-300 text-sm leading-relaxed">
            <span class="font-semibold">How Net Gain Works:</span> This shows the total value your network has contributed to the system (deposits + upgrades) minus what has been paid out (withdrawals). A positive number indicates strong network retention and growth.
          </p>
        </div>

        <!-- Main Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
          <!-- Total Deposits -->
          <div class="bg-gradient-to-br from-green-500/20 to-green-600/10 border border-green-500/30 rounded-xl p-4 hover:shadow-lg hover:shadow-green-500/20 transition-all">
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              <p class="text-green-400 text-xs font-semibold uppercase tracking-wider">Total Deposits</p>
            </div>
            <p class="text-white text-xl sm:text-2xl font-bold break-words">{{ currencySymbol }}{{ formatNumber(networkStats.totalNetworkDeposits) }}</p>
            <p class="text-gray-400 text-[10px] mt-1">Network size: {{ networkStats.networkSize }} members</p>
          </div>

          <!-- Total Withdrawals -->
          <div class="bg-gradient-to-br from-red-500/20 to-red-600/10 border border-red-500/30 rounded-xl p-4 hover:shadow-lg hover:shadow-red-500/20 transition-all">
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
              </svg>
              <p class="text-red-400 text-xs font-semibold uppercase tracking-wider">Total Withdrawals</p>
            </div>
            <p class="text-white text-xl sm:text-2xl font-bold break-words">{{ currencySymbol }}{{ formatNumber(networkStats.totalNetworkWithdrawals) }}</p>
            <p class="text-gray-400 text-[10px] mt-1">Paid out to network</p>
          </div>

          <!-- Net Gain -->
          <div class="sm:col-span-2 lg:col-span-1 bg-gradient-to-br from-purple-500/20 to-purple-600/10 border border-purple-500/30 rounded-xl p-4 hover:shadow-lg hover:shadow-purple-500/20 transition-all">
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
              </svg>
              <p class="text-purple-400 text-xs font-semibold uppercase tracking-wider">Net Gain</p>
            </div>
            <p :class="['text-xl sm:text-2xl font-bold break-words', networkStats.netGain >= 0 ? 'text-green-400' : 'text-red-400']">
              {{ currencySymbol }}{{ formatNumber(networkStats.netGain) }}
            </p>
            <p class="text-gray-400 text-[10px] mt-1">Deposits - Withdrawals</p>
          </div>
        </div>

        <!-- Detailed Breakdown -->
        <div class="bg-white/5 rounded-xl p-4 border border-white/10">
          <h4 class="text-white font-semibold text-sm mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Deposit Breakdown
          </h4>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div class="flex justify-between items-center bg-white/5 rounded-lg px-3 py-2">
              <span class="text-gray-400">Activations:</span>
              <span class="text-white font-semibold">{{ currencySymbol }}{{ formatNumber(networkStats.totalActivations) }}</span>
            </div>
            <div class="flex justify-between items-center bg-white/5 rounded-lg px-3 py-2">
              <span class="text-gray-400">Upgrades:</span>
              <span class="text-white font-semibold">{{ currencySymbol }}{{ formatNumber(networkStats.totalUpgrades) }}</span>
            </div>
          </div>

          <h4 class="text-white font-semibold text-sm mt-6 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Earnings Breakdown
          </h4>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
            <div class="flex justify-between items-center bg-white/5 rounded-lg px-3 py-2">
              <span class="text-gray-400 text-xs">Tasks:</span>
              <span class="text-white font-semibold text-xs">{{ currencySymbol }}{{ formatNumber(networkStats.totalTaskEarnings) }}</span>
            </div>
            <div class="flex justify-between items-center bg-white/5 rounded-lg px-3 py-2">
              <span class="text-gray-400 text-xs">Referrals:</span>
              <span class="text-white font-semibold text-xs">{{ currencySymbol }}{{ formatNumber(networkStats.totalReferralBonuses) }}</span>
            </div>
            <div class="flex justify-between items-center bg-white/5 rounded-lg px-3 py-2">
              <span class="text-gray-400 text-xs">Team:</span>
              <span class="text-white font-semibold text-xs">{{ currencySymbol }}{{ formatNumber(networkStats.totalTeamCommissions) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- User Stats Modal -->
    <Teleport to="body">
      <div v-if="selectedUser" @click="selectedUser = null" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div @click.stop class="bg-gray-900 rounded-2xl border border-white/20 p-6 max-w-md w-full">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-white">{{ selectedUser.name }}</h3>
            <button @click="selectedUser = null" class="text-gray-400 hover:text-white">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-400">Email:</span>
              <span class="text-white">{{ selectedUser.email }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-400">Status:</span>
              <span :class="selectedUser.status === 'ACTIVE' ? 'text-green-400' : 'text-yellow-400'">{{ selectedUser.status }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-400">Rank:</span>
              <span class="text-white">{{ selectedUser.rank }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-400">Direct Referrals:</span>
              <span class="text-white">{{ selectedUser.directReferrals }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-400">Total Team:</span>
              <span class="text-white">{{ selectedUser.totalTeam }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-400">Joined:</span>
              <span class="text-white">{{ selectedUser.activationDate || 'Not activated' }}</span>
            </div>
          </div>
        </div>
      </div>
    </Teleport>
  </UserLayout>
</template>

<script setup>
import { ref } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';
import ReferralTree from '@/Components/ReferralTree.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  user: Object,
  stats: Object,
  treeData: Object,
  commissionRates: Object,
  networkStats: Object,
  currencyCode: {
    type: String,
    default: 'NGN'
  },
  currencySymbol: {
    type: String,
    default: '₦'
  }
});

const selectedUser = ref(null);

const copyLink = () => {
  navigator.clipboard.writeText(props.stats.referralLink);
  Swal.fire({
    icon: 'success',
    title: 'Copied!',
    text: 'Referral link copied to clipboard',
    background: '#1f2937',
    color: '#fff',
    confirmButtonColor: '#f97316',
    timer: 2000,
    showConfirmButton: false,
  });
};

const copyCode = () => {
  navigator.clipboard.writeText(props.stats.referralCode);
  Swal.fire({
    icon: 'success',
    title: 'Copied!',
    text: 'Referral code copied to clipboard',
    background: '#1f2937',
    color: '#fff',
    confirmButtonColor: '#9333ea',
    timer: 2000,
    showConfirmButton: false,
  });
};

const formatNumber = (num) => {
  return new Intl.NumberFormat('en-NG').format(num || 0);
};

const showUserModal = (user) => {
  selectedUser.value = user;
};
</script>
