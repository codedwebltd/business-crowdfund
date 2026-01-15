<template>
  <AdminLayout title="User Referral Tree" :settings="settings">
    <!-- Breadcrumbs -->
    <Breadcrumbs :crumbs="[
      { label: 'Dashboard', url: '/admin/dashboard' },
      { label: 'Users', url: '/admin/users' },
      { label: user.full_name, url: `/admin/users/${user.id}` },
      { label: 'Referral Tree' }
    ]" />

    <div class="mb-6 mt-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Referral Tree</h1>
          <p class="text-gray-600 mt-1">{{ user.full_name }}'s referral network</p>
        </div>
        <Link :href="`/admin/users/${user.id}`" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition-all">
          ← Back to User
        </Link>
      </div>
    </div>

    <!-- Agent Earnings Card (Only for Agents) -->
    <div v-if="agentEarnings" class="bg-white rounded-2xl shadow-md border border-gray-100 p-6 mb-6">
      <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
        <div class="p-3 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
          </svg>
        </div>
        <div>
          <h2 class="text-xl font-bold text-gray-900">Agent Earnings Calculator</h2>
          <p class="text-gray-600 text-sm">Total deposits minus total withdrawals from downline (excluding agent)</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 hover:shadow-md transition-all">
          <div class="flex items-center justify-between mb-2">
            <p class="text-gray-600 text-sm font-medium">Downline Size</p>
            <div class="p-2 bg-blue-100 rounded-lg">
              <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
            </div>
          </div>
          <p class="text-3xl font-bold text-gray-900">{{ agentEarnings.tree_size }}</p>
          <p class="text-gray-500 text-xs mt-1">Referrals only</p>
        </div>

        <div class="bg-white rounded-xl border border-green-200 shadow-sm p-4 hover:shadow-md transition-all">
          <div class="flex items-center justify-between mb-2">
            <p class="text-gray-600 text-sm font-medium">Total Deposits</p>
            <div class="p-2 bg-green-100 rounded-lg">
              <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          <p class="text-3xl font-bold text-green-600">₦{{ formatNumber(agentEarnings.total_deposits) }}</p>
          <p class="text-gray-500 text-xs mt-1">Activation + Upgrades</p>
        </div>

        <div class="bg-white rounded-xl border border-red-200 shadow-sm p-4 hover:shadow-md transition-all">
          <div class="flex items-center justify-between mb-2">
            <p class="text-gray-600 text-sm font-medium">Total Withdrawals</p>
            <div class="p-2 bg-red-100 rounded-lg">
              <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
          </div>
          <p class="text-3xl font-bold text-red-600">₦{{ formatNumber(agentEarnings.total_withdrawals) }}</p>
          <p class="text-gray-500 text-xs mt-1">Completed only</p>
        </div>

        <div class="bg-white rounded-xl border-2 border-purple-300 shadow-md p-4 hover:shadow-lg transition-all">
          <div class="flex items-center justify-between mb-2">
            <p class="text-gray-600 text-sm font-medium">Net Earnings</p>
            <div class="p-2 bg-purple-100 rounded-lg">
              <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
              </svg>
            </div>
          </div>
          <p class="text-3xl font-bold text-purple-600">₦{{ formatNumber(agentEarnings.net_earnings) }}</p>
          <p class="text-gray-500 text-xs mt-1">Deposits - Withdrawals</p>
        </div>
      </div>
    </div>

    <!-- Tree Visualization -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
      <div class="mb-4">
        <h3 class="text-lg font-bold text-gray-900">Referral Network Tree</h3>
        <p class="text-sm text-gray-600">Visual representation of the referral hierarchy</p>
      </div>

      <ReferralTree :node="tree" @nodeClick="showNodeDetails" />
    </div>

    <!-- Node Details Modal -->
    <div v-if="selectedNode" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="selectedNode = null">
      <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b bg-gradient-to-r from-purple-500 to-indigo-600">
          <div class="flex justify-between items-start">
            <div class="text-white">
              <h3 class="text-xl font-bold">{{ selectedNode.name }}</h3>
              <p class="text-purple-100 text-sm mt-1">{{ selectedNode.plan || 'No Plan' }}</p>
            </div>
            <button @click="selectedNode = null" class="text-white hover:bg-white/20 rounded-lg p-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
              <p class="text-xs text-green-600 font-semibold">Total Deposited</p>
              <p class="text-2xl font-bold text-green-700 mt-1">₦{{ formatNumber(selectedNode.total_deposited) }}</p>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
              <p class="text-xs text-red-600 font-semibold">Total Withdrawn</p>
              <p class="text-2xl font-bold text-red-700 mt-1">₦{{ formatNumber(selectedNode.total_withdrawn) }}</p>
            </div>
          </div>

          <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
            <p class="text-xs text-gray-600 font-semibold">Direct Referrals</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ selectedNode.children?.length || 0 }}</p>
          </div>

          <Link :href="`/admin/users/${selectedNode.id}`" class="block w-full px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold text-center transition-all">
            View Full Profile
          </Link>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import ReferralTree from '@/Components/ReferralTree.vue';

const props = defineProps({
  user: Object,
  tree: Object,
  agentEarnings: Object,
  settings: Object,
});

const selectedNode = ref(null);

const showNodeDetails = (node) => {
  selectedNode.value = node;
};

const formatNumber = (num) => {
  return new Intl.NumberFormat().format(num || 0);
};
</script>
