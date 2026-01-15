<template>
  <AdminLayout title="User Management" :settings="settings">
    <!-- Breadcrumbs -->
    <Breadcrumbs :crumbs="[
      { label: 'Dashboard', url: '/admin/dashboard' },
      { label: 'Users' }
    ]" />

    <div class="mb-6 mt-4">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">User Management</h1>
      <p class="text-gray-600 mt-1">Manage all platform users with comprehensive controls</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 xl:grid-cols-8 gap-3 sm:gap-4 mb-6">
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-600">Total Users</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total }}</p>
          </div>
          <div class="p-2 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-600">Active</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ stats.active }}</p>
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
            <p class="text-xs font-medium text-gray-600">Suspended</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ stats.suspended }}</p>
          </div>
          <div class="p-2 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-600">Banned</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ stats.banned }}</p>
          </div>
          <div class="p-2 bg-gradient-to-br from-red-500 to-red-600 rounded-xl">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-600">KYC Verified</p>
            <p class="text-2xl font-bold text-indigo-600 mt-1">{{ stats.kyc_verified }}</p>
          </div>
          <div class="p-2 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-600">Task Banned</p>
            <p class="text-2xl font-bold text-pink-600 mt-1">{{ stats.task_banned }}</p>
          </div>
          <div class="p-2 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-600">Agents</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ stats.agents }}</p>
          </div>
          <div class="p-2 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Advanced Filters -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4 sm:p-6 mb-6">
      <h3 class="text-sm font-bold text-gray-900 mb-4">Advanced Filters</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
        <!-- Search -->
        <input
          v-model="filters.search"
          type="text"
          placeholder="Search name, email, phone..."
          class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent"
        >

        <!-- Status Filter -->
        <select v-model="filters.status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
          <option value="">All Status</option>
          <option value="ACTIVE">Active</option>
          <option value="PENDING">Pending</option>
          <option value="SUSPENDED">Suspended</option>
          <option value="BANNED">Banned</option>
          <option value="UNVERIFIED">Unverified</option>
        </select>

        <!-- User Type Filter -->
        <select v-model="filters.user_type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
          <option value="">All Types</option>
          <option value="USER">Regular User</option>
          <option value="AGENT">Agent</option>
        </select>

        <!-- Plan Filter -->
        <select v-model="filters.plan_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
          <option value="">All Plans</option>
          <option v-for="plan in plans" :key="plan.id" :value="plan.id">{{ plan.display_name }}</option>
        </select>

        <!-- Rank Filter -->
        <select v-model="filters.rank_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
          <option value="">All Ranks</option>
          <option v-for="rank in ranks" :key="rank.id" :value="rank.id">{{ rank.name }}</option>
        </select>

        <!-- KYC Status Filter -->
        <select v-model="filters.kyc_status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
          <option value="">All KYC Status</option>
          <option value="verified">KYC Verified</option>
          <option value="not_verified">Not Verified</option>
        </select>

        <!-- Task Ban Filter -->
        <select v-model="filters.task_ban" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500">
          <option value="">All Task Ban Status</option>
          <option value="banned">Task Banned</option>
          <option value="not_banned">Not Banned</option>
        </select>

        <!-- Apply Filters Button -->
        <button
          @click="applyFilters"
          class="px-4 py-2 bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-lg text-sm font-semibold hover:from-purple-600 hover:to-indigo-700 shadow-md transition-all"
        >
          Apply Filters
        </button>

        <!-- Clear Filters Button -->
        <button
          @click="clearFilters"
          class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-all"
        >
          Clear All
        </button>
      </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div v-if="selectedUsers.length > 0" class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 mb-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <span class="text-sm font-semibold text-indigo-900">{{ selectedUsers.length }} user(s) selected</span>
        <button @click="clearSelection" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Clear Selection</button>
      </div>
      <div class="flex items-center gap-2">
        <button @click="bulkAction('activate')" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-all">
          Activate Selected
        </button>
        <button @click="bulkAction('suspend')" class="px-4 py-2 bg-orange-600 text-white text-sm font-semibold rounded-lg hover:bg-orange-700 transition-all">
          Suspend Selected
        </button>
        <button @click="bulkAction('ban')" class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-all">
          Ban Selected
        </button>
        <button @click="bulkAction('delete')" class="px-4 py-2 bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-all">
          Delete Selected
        </button>
      </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-purple-500 to-indigo-600">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-bold text-white">All Users</h2>
            <p class="text-purple-100 text-sm mt-1">{{ users.total }} users found</p>
          </div>
          <button v-if="users.data.length > 0" @click="toggleSelectAll" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-semibold rounded-lg transition-all">
            {{ allSelected ? 'Deselect All' : 'Select All' }}
          </button>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b-2 border-gray-200">
            <tr>
              <th class="px-4 py-4 text-center w-12">
                <input type="checkbox" :checked="allSelected" @change="toggleSelectAll" class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
              </th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User Details</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Plan & Rank</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Wallet Balance</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Team & Activity</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Joined</th>
              <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="user in users.data" :key="user.id" :class="[selectedUsers.includes(user.id) ? 'bg-purple-50' : '', 'hover:bg-purple-50 transition-colors']">
              <!-- Checkbox -->
              <td class="px-4 py-4 text-center">
                <input type="checkbox" :checked="selectedUsers.includes(user.id)" @change="toggleUserSelection(user.id)" class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
              </td>

              <!-- User Details -->
              <td class="px-4 py-4">
                <div class="flex items-start gap-3">
                  <!-- Avatar -->
                  <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg">
                      {{ getUserInitials(user.full_name) }}
                    </div>
                  </div>

                  <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                      <div class="font-semibold text-gray-900 text-sm truncate">{{ user.full_name }}</div>
                      <!-- KYC Verified Badge with Tooltip -->
                      <div v-if="user.kyc_verified_at" class="relative group">
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 cursor-help" viewBox="0 0 24 24" fill="currentColor">
                          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <!-- Tooltip -->
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
                          KYC Verified ✓
                        </div>
                      </div>
                      <!-- Agent Badge -->
                      <span v-if="user.user_type === 'AGENT'" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200 flex-shrink-0">
                        AGENT
                      </span>
                      <!-- Admin Badge -->
                      <span v-if="user.role === 1" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-700 border border-red-200 flex-shrink-0">
                        ADMIN
                      </span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ user.email }}</div>
                    <div class="text-xs text-gray-500">{{ user.phone_number }}</div>
                    <div class="text-xs text-gray-400 mt-1 font-mono">{{ user.referral_code }}</div>
                  </div>
                </div>
              </td>

              <!-- Status -->
              <td class="px-4 py-4">
                <div class="flex flex-col gap-2">
                  <!-- Main Status Badge -->
                  <span :class="getStatusClass(user.status)" class="px-3 py-2 rounded-lg text-xs font-bold inline-flex items-center justify-center gap-2 shadow-sm w-32">
                    <span :class="user.status === 'ACTIVE' ? 'w-2 h-2 bg-white rounded-full animate-pulse' : 'w-2 h-2 bg-current rounded-full'"></span>
                    {{ user.status }}
                  </span>
                  <!-- Task Ban Badge -->
                  <span v-if="user.task_ban_until && new Date(user.task_ban_until) > new Date()" class="px-3 py-2 rounded-lg text-xs font-bold inline-flex items-center justify-center gap-2 bg-red-100 text-red-700 border border-red-200 w-32">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Task Banned
                  </span>
                </div>
              </td>

              <!-- Plan & Rank -->
              <td class="px-4 py-4">
                <div class="text-sm font-semibold text-gray-900">{{ user.plan?.display_name || 'No Plan' }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ user.rank?.name || 'No Rank' }}</div>
                <div v-if="user.performance" class="mt-2 flex items-center gap-1">
                  <span class="text-xs">{{ '⭐'.repeat(user.performance.star_rating || 1) }}</span>
                </div>
              </td>

              <!-- Wallet Balance -->
              <td class="px-4 py-4">
                <div class="space-y-1">
                  <div class="text-xs text-gray-500">Withdrawable:</div>
                  <div class="font-bold text-sm text-green-600">₦{{ formatNumber(user.wallet?.withdrawable_balance || 0) }}</div>
                  <div class="text-xs text-gray-500">Pending:</div>
                  <div class="font-semibold text-xs text-yellow-600">₦{{ formatNumber(user.wallet?.pending_balance || 0) }}</div>
                </div>
              </td>

              <!-- Team & Activity -->
              <td class="px-4 py-4">
                <div class="space-y-1 text-xs">
                  <!-- Referrals - Clickable -->
                  <Link
                    :href="`/admin/users/${user.id}/referrals`"
                    class="flex items-center gap-1 text-purple-600 hover:text-purple-800 hover:underline cursor-pointer transition-colors"
                    title="View referral tree"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="font-medium">{{ user.direct_referrals_count }} referrals</span>
                  </Link>

                  <!-- Tasks - Clickable -->
                  <Link
                    :href="`/admin/users/${user.id}/tasks`"
                    class="flex items-center gap-1 text-blue-600 hover:text-blue-800 hover:underline cursor-pointer transition-colors"
                    title="View user tasks"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="font-medium">{{ user.tasks_count }} tasks</span>
                  </Link>

                  <!-- Withdrawals - Clickable -->
                  <Link
                    :href="`/admin/users/${user.id}/withdrawals`"
                    class="flex items-center gap-1 text-green-600 hover:text-green-800 hover:underline cursor-pointer transition-colors"
                    title="View withdrawals"
                  >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium">{{ user.withdrawals_count }} withdrawals</span>
                  </Link>
                </div>
              </td>

              <!-- Joined Date -->
              <td class="px-4 py-4">
                <div class="text-xs text-gray-600">{{ formatDate(user.created_at) }}</div>
              </td>

              <!-- Actions -->
              <td class="px-4 py-4">
                <div class="flex items-center justify-end gap-1.5">
                  <!-- View Button -->
                  <Link
                    :href="`/admin/users/${user.id}`"
                    class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                    title="View Details"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </Link>

                  <!-- Quick Actions Dropdown -->
                  <div class="relative inline-block">
                    <button
                      @click.stop="toggleActionsMenu(user.id)"
                      class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                      title="More Actions"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                      </svg>
                    </button>

                    <!-- Dropdown Menu with Fixed Positioning -->
                    <div v-if="activeActionsMenu === user.id" @click.stop class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-gray-200 z-[100] overflow-hidden">
                      <div class="py-1">
                        <button @click="changeStatus(user)" class="w-full text-left px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors flex items-center gap-2">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                          </svg>
                          Change Status
                        </button>
                        <button v-if="!user.kyc_verified_at" @click="verifyKyc(user)" class="w-full text-left px-4 py-3 text-sm font-medium text-green-700 hover:bg-green-50 transition-colors flex items-center gap-2">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                          </svg>
                          Verify KYC
                        </button>
                        <button @click="adjustBalance(user)" class="w-full text-left px-4 py-3 text-sm font-medium text-blue-700 hover:bg-blue-50 transition-colors flex items-center gap-2">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>
                          Adjust Balance
                        </button>
                        <div class="border-t border-gray-100 my-1"></div>
                        <button v-if="user.task_ban_until && new Date(user.task_ban_until) > new Date()" @click="clearTaskBan(user)" class="w-full text-left px-4 py-3 text-sm font-medium text-orange-700 hover:bg-orange-50 transition-colors flex items-center gap-2">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                          </svg>
                          Clear Task Ban
                        </button>
                        <button v-else @click="banTask(user)" class="w-full text-left px-4 py-3 text-sm font-medium text-orange-700 hover:bg-orange-50 transition-colors flex items-center gap-2">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                          </svg>
                          Ban Task Access
                        </button>
                        <div class="border-t border-gray-100 my-1"></div>
                        <button @click="deleteUser(user)" class="w-full text-left px-4 py-3 text-sm font-medium text-red-700 hover:bg-red-50 transition-colors flex items-center gap-2">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                          Delete User
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="text-sm text-gray-600 text-center sm:text-left">
          Showing {{ users.from }} to {{ users.to }} of {{ users.total }} users
        </div>
        <div class="flex gap-1 sm:gap-2 flex-wrap justify-center">
          <button
            v-for="link in users.links"
            :key="link.label"
            @click="changePage(link.url)"
            :disabled="!link.url"
            :class="link.active ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
            class="px-2 sm:px-3 py-1 rounded border text-xs sm:text-sm disabled:opacity-50 min-w-[32px]"
            v-html="link.label"
          ></button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  users: Object,
  stats: Object,
  plans: Array,
  ranks: Array,
  filters: Object,
  settings: Object,
});

const filters = reactive({
  search: props.filters.search || '',
  status: props.filters.status || '',
  user_type: props.filters.user_type || '',
  plan_id: props.filters.plan_id || '',
  rank_id: props.filters.rank_id || '',
  kyc_status: props.filters.kyc_status || '',
  task_ban: props.filters.task_ban || '',
});

const activeActionsMenu = ref(null);
const selectedUsers = ref([]);
const allSelected = ref(false);

const toggleActionsMenu = (userId) => {
  activeActionsMenu.value = activeActionsMenu.value === userId ? null : userId;
};

const toggleUserSelection = (userId) => {
  const index = selectedUsers.value.indexOf(userId);
  if (index > -1) {
    selectedUsers.value.splice(index, 1);
  } else {
    selectedUsers.value.push(userId);
  }
  updateAllSelectedState();
};

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedUsers.value = [];
    allSelected.value = false;
  } else {
    selectedUsers.value = props.users.data.map(user => user.id);
    allSelected.value = true;
  }
};

const updateAllSelectedState = () => {
  allSelected.value = selectedUsers.value.length === props.users.data.length && props.users.data.length > 0;
};

const clearSelection = () => {
  selectedUsers.value = [];
  allSelected.value = false;
};

const bulkAction = (action) => {
  if (selectedUsers.value.length === 0) {
    Swal.fire('No Users Selected', 'Please select at least one user', 'warning');
    return;
  }

  const actions = {
    activate: {
      title: 'Activate Users?',
      text: `Activate ${selectedUsers.value.length} selected user(s)?`,
      status: 'ACTIVE',
      confirmText: 'Yes, activate',
      icon: 'question'
    },
    suspend: {
      title: 'Suspend Users?',
      text: `Suspend ${selectedUsers.value.length} selected user(s)?`,
      status: 'SUSPENDED',
      confirmText: 'Yes, suspend',
      icon: 'warning'
    },
    ban: {
      title: 'Ban Users?',
      text: `Permanently ban ${selectedUsers.value.length} selected user(s)?`,
      status: 'BANNED',
      confirmText: 'Yes, ban',
      icon: 'error'
    },
    delete: {
      title: 'Delete Users?',
      text: `Permanently delete ${selectedUsers.value.length} selected user(s)? This action cannot be undone!`,
      confirmText: 'Yes, delete',
      icon: 'error'
    }
  };

  const actionConfig = actions[action];

  Swal.fire({
    title: actionConfig.title,
    text: actionConfig.text,
    icon: actionConfig.icon,
    showCancelButton: true,
    confirmButtonText: actionConfig.confirmText,
    confirmButtonColor: action === 'delete' || action === 'ban' ? '#ef4444' : '#3b82f6'
  }).then((result) => {
    if (result.isConfirmed) {
      if (action === 'delete') {
        // For delete, we need a different route
        router.post('/admin/users/bulk-delete', {
          user_ids: selectedUsers.value
        }, {
          preserveScroll: true,
          onSuccess: () => {
            clearSelection();
            Swal.fire('Deleted!', 'Selected users have been deleted', 'success');
          }
        });
      } else {
        // For status changes
        router.post('/admin/users/bulk-status', {
          user_ids: selectedUsers.value,
          status: actionConfig.status
        }, {
          preserveScroll: true,
          onSuccess: () => {
            clearSelection();
            Swal.fire('Updated!', 'Selected users have been updated', 'success');
          }
        });
      }
    }
  });
};

const getUserInitials = (name) => {
  return name?.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) || 'U';
};

const getStatusClass = (status) => {
  const classes = {
    'ACTIVE': 'bg-gradient-to-r from-green-500 to-green-600 text-white',
    'PENDING': 'bg-gradient-to-r from-yellow-500 to-yellow-600 text-white',
    'SUSPENDED': 'bg-gradient-to-r from-orange-500 to-orange-600 text-white',
    'BANNED': 'bg-gradient-to-r from-red-500 to-red-600 text-white',
    'UNVERIFIED': 'bg-gray-100 text-gray-700',
  };
  return classes[status] || 'bg-gray-100 text-gray-700';
};

const formatNumber = (num) => {
  return new Intl.NumberFormat().format(num || 0);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const applyFilters = () => {
  router.get('/admin/users', filters, { preserveState: true });
};

const clearFilters = () => {
  Object.keys(filters).forEach(key => filters[key] = '');
  applyFilters();
};

const changePage = (url) => {
  if (url) router.visit(url);
};

const changeStatus = (user) => {
  activeActionsMenu.value = null;

  Swal.fire({
    title: 'Change User Status',
    html: `
      <select id="status" class="swal2-input">
        <option value="ACTIVE" ${user.status === 'ACTIVE' ? 'selected' : ''}>Active</option>
        <option value="PENDING" ${user.status === 'PENDING' ? 'selected' : ''}>Pending</option>
        <option value="SUSPENDED" ${user.status === 'SUSPENDED' ? 'selected' : ''}>Suspended</option>
        <option value="BANNED" ${user.status === 'BANNED' ? 'selected' : ''}>Banned</option>
        <option value="UNVERIFIED" ${user.status === 'UNVERIFIED' ? 'selected' : ''}>Unverified</option>
      </select>
      <textarea id="reason" class="swal2-textarea" placeholder="Reason (optional)"></textarea>
    `,
    showCancelButton: true,
    confirmButtonText: 'Update Status',
    preConfirm: () => {
      return {
        status: document.getElementById('status').value,
        reason: document.getElementById('reason').value
      };
    }
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/users/${user.id}/status`, result.value, { preserveScroll: true });
    }
  });
};

const verifyKyc = (user) => {
  activeActionsMenu.value = null;

  Swal.fire({
    title: 'Verify KYC?',
    text: `Manually verify KYC for ${user.full_name}?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, verify'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/users/${user.id}/verify-kyc`, {}, { preserveScroll: true });
    }
  });
};

const adjustBalance = (user) => {
  activeActionsMenu.value = null;

  Swal.fire({
    title: 'Adjust Balance',
    html: `
      <select id="type" class="swal2-input">
        <option value="withdrawable_balance">Withdrawable Balance</option>
        <option value="pending_balance">Pending Balance</option>
      </select>
      <select id="action" class="swal2-input">
        <option value="add">Add</option>
        <option value="subtract">Subtract</option>
      </select>
      <input id="amount" type="number" step="0.01" class="swal2-input" placeholder="Amount">
      <textarea id="reason" class="swal2-textarea" placeholder="Reason (required)"></textarea>
    `,
    showCancelButton: true,
    confirmButtonText: 'Adjust Balance',
    preConfirm: () => {
      const amount = parseFloat(document.getElementById('amount').value);
      const reason = document.getElementById('reason').value;

      if (!amount || amount <= 0) {
        Swal.showValidationMessage('Please enter a valid amount');
        return false;
      }
      if (!reason) {
        Swal.showValidationMessage('Please provide a reason');
        return false;
      }

      return {
        type: document.getElementById('type').value,
        action: document.getElementById('action').value,
        amount: amount,
        reason: reason
      };
    }
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/users/${user.id}/adjust-balance`, result.value, { preserveScroll: true });
    }
  });
};

const banTask = (user) => {
  activeActionsMenu.value = null;

  Swal.fire({
    title: 'Ban Task Access',
    html: `
      <input id="ban_until" type="datetime-local" class="swal2-input" min="${new Date().toISOString().slice(0, 16)}">
      <textarea id="reason" class="swal2-textarea" placeholder="Reason (required)"></textarea>
    `,
    showCancelButton: true,
    confirmButtonText: 'Ban User',
    preConfirm: () => {
      const banUntil = document.getElementById('ban_until').value;
      const reason = document.getElementById('reason').value;

      if (!banUntil) {
        Swal.showValidationMessage('Please select ban end date');
        return false;
      }
      if (!reason) {
        Swal.showValidationMessage('Please provide a reason');
        return false;
      }

      return {
        ban_until: banUntil,
        reason: reason
      };
    }
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/users/${user.id}/ban-task`, result.value, { preserveScroll: true });
    }
  });
};

const clearTaskBan = (user) => {
  activeActionsMenu.value = null;

  Swal.fire({
    title: 'Clear Task Ban?',
    text: `Remove task ban for ${user.full_name}?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, clear ban'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/users/${user.id}/clear-task-ban`, {}, { preserveScroll: true });
    }
  });
};

const deleteUser = (user) => {
  activeActionsMenu.value = null;

  Swal.fire({
    title: 'Delete User?',
    text: `Permanently delete ${user.full_name}? This action cannot be undone!`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, delete',
    confirmButtonColor: '#ef4444'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/admin/users/${user.id}`, { preserveScroll: true });
    }
  });
};

// Close dropdown when clicking outside
if (typeof window !== 'undefined') {
  window.addEventListener('click', (e) => {
    if (!e.target.closest('.relative.inline-block')) {
      activeActionsMenu.value = null;
    }
  });
}
</script>
