<template>
  <AdminLayout title="Dashboard" :settings="settings">
    <!-- Welcome Banner -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300 mb-6">
      <div class="p-4 sm:p-6 border-b border-gray-100">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-gradient-to-br from-gray-700 to-gray-800 rounded-xl shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900">Welcome back, Admin</h3>
            <p class="text-sm text-gray-600">Here's what's happening today</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Platform Overview -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300 mb-6">
      <div class="p-4 sm:p-6 border-b border-gray-100">
        <div class="flex items-center gap-3">
          <div class="p-2.5 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900">Platform Overview</h3>
            <p class="text-sm text-gray-600">User statistics</p>
          </div>
        </div>
      </div>

      <div class="p-4 sm:p-6 bg-gray-50">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="text-xs text-gray-600 mb-1">Total Users</div>
            <div class="text-2xl font-bold text-gray-900">0</div>
          </div>
          <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="text-xs text-gray-600 mb-1">Active Users</div>
            <div class="text-2xl font-bold text-green-600">0</div>
          </div>
          <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="text-xs text-gray-600 mb-1">Tasks Completed</div>
            <div class="text-2xl font-bold text-purple-600">0</div>
          </div>
          <div class="bg-white rounded-lg p-4 border border-gray-200">
            <div class="text-xs text-gray-600 mb-1">Total Earnings</div>
            <div class="text-2xl font-bold text-orange-600">₦0</div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
  settings: {
    type: Object,
    default: () => ({ app_name: 'CrowdPower' })
  }
});
</script>
