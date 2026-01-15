<template>
  <AdminLayout title="User Details" :settings="settings">
    <!-- Breadcrumbs -->
    <Breadcrumbs :crumbs="[
      { label: 'Dashboard', url: '/admin/dashboard' },
      { label: 'Users', url: '/admin/users' },
      { label: user.full_name }
    ]" />

    <div class="mb-6 mt-4">
      <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
          <!-- Avatar -->
          <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-2xl">
            {{ getUserInitials(user.full_name) }}
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ user.full_name }}</h1>
              <!-- KYC Badge -->
              <svg v-if="user.kyc_verified_at" class="w-6 h-6 text-blue-500" viewBox="0 0 24 24" fill="currentColor" title="KYC Verified">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
            </div>
            <p class="text-gray-600 mt-1">{{ user.email }}</p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <Link :href="`/admin/users`" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition-all">
            ← Back to Users
          </Link>
          <button @click="showEditModal = true" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-all">
            ✏️ Edit User
          </button>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4">
        <p class="text-xs font-medium text-gray-600">Total Earned</p>
        <p class="text-2xl font-bold text-green-600 mt-1">₦{{ formatNumber(stats.total_earned) }}</p>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4">
        <p class="text-xs font-medium text-gray-600">Withdrawable</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">₦{{ formatNumber(stats.withdrawable_balance) }}</p>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4">
        <p class="text-xs font-medium text-gray-600">Pending</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1">₦{{ formatNumber(stats.pending_balance) }}</p>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4">
        <p class="text-xs font-medium text-gray-600">Withdrawn</p>
        <p class="text-2xl font-bold text-purple-600 mt-1">₦{{ formatNumber(stats.total_withdrawn) }}</p>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4">
        <p class="text-xs font-medium text-gray-600">Tasks Done</p>
        <p class="text-2xl font-bold text-indigo-600 mt-1">{{ stats.tasks_completed }}</p>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4">
        <p class="text-xs font-medium text-gray-600">Pending W/D</p>
        <p class="text-2xl font-bold text-orange-600 mt-1">{{ stats.pending_withdrawals }}</p>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column - User Info -->
      <div class="lg:col-span-1 space-y-6">
        <!-- Basic Info Card -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Basic Information</h3>
          <div class="space-y-3 text-sm">
            <div>
              <p class="text-gray-600 text-xs">Status</p>
              <span :class="getStatusClass(user.status)" class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-bold mt-1">
                <span class="w-2 h-2 bg-current rounded-full"></span>
                {{ user.status }}
              </span>
            </div>
            <div>
              <p class="text-gray-600 text-xs">User Type</p>
              <p class="font-semibold text-gray-900">{{ user.user_type }}</p>
            </div>
            <div>
              <p class="text-gray-600 text-xs">Role</p>
              <p class="font-semibold text-gray-900">{{ user.role === 1 ? 'Admin' : 'User' }}</p>
            </div>
            <div>
              <p class="text-gray-600 text-xs">Phone</p>
              <p class="font-semibold text-gray-900">{{ user.phone_number }}</p>
            </div>
            <div>
              <p class="text-gray-600 text-xs">Country</p>
              <p class="font-semibold text-gray-900">{{ user.country }}</p>
            </div>
            <div>
              <p class="text-gray-600 text-xs">Referral Code</p>
              <p class="font-mono text-sm font-bold text-purple-600">{{ user.referral_code }}</p>
            </div>
            <div>
              <p class="text-gray-600 text-xs">Joined</p>
              <p class="font-semibold text-gray-900">{{ formatDate(user.created_at) }}</p>
            </div>
          </div>
        </div>

        <!-- Plan & Rank Card -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Plan & Rank</h3>
          <div class="space-y-3 text-sm">
            <div>
              <p class="text-gray-600 text-xs">Current Plan</p>
              <p class="font-semibold text-gray-900">{{ user.plan?.display_name || 'No Plan' }}</p>
            </div>
            <div>
              <p class="text-gray-600 text-xs">Current Rank</p>
              <p class="font-semibold text-gray-900">{{ user.rank?.name || 'No Rank' }}</p>
            </div>
            <div v-if="user.performance">
              <p class="text-gray-600 text-xs">Performance</p>
              <div class="flex items-center gap-1 mt-1">
                <span v-for="n in 5" :key="n" :class="n <= (user.performance.star_rating || 0) ? 'text-yellow-400' : 'text-gray-300'">⭐</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-6">
          <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
          <div class="space-y-2">
            <Link :href="`/admin/users/${user.id}/referrals`" class="flex items-center gap-2 px-3 py-2 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg text-sm font-medium transition-all">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
              View Referral Tree
            </Link>
            <Link :href="`/admin/users/${user.id}/tasks`" class="flex items-center gap-2 px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-sm font-medium transition-all">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
              View Tasks History
            </Link>
            <Link :href="`/admin/users/${user.id}/withdrawals`" class="flex items-center gap-2 px-3 py-2 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg text-sm font-medium transition-all">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              View Withdrawals
            </Link>
          </div>
        </div>
      </div>

      <!-- Right Column - Activity -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Recent Transactions -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
          <div class="p-4 border-b bg-gradient-to-r from-green-500 to-emerald-600">
            <h3 class="text-lg font-bold text-white">Recent Transactions</h3>
          </div>
          <div class="p-4">
            <div v-if="recentTransactions.length > 0" class="space-y-2">
              <div v-for="txn in recentTransactions" :key="txn.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex-1">
                  <p class="font-semibold text-sm text-gray-900">{{ txn.transaction_type }}</p>
                  <p class="text-xs text-gray-500">{{ txn.description || formatDate(txn.created_at) }}</p>
                </div>
                <div class="text-right">
                  <p :class="txn.is_credit ? 'text-green-600' : 'text-red-600'" class="font-bold">
                    {{ txn.is_credit ? '+' : '-' }}₦{{ formatNumber(txn.amount) }}
                  </p>
                  <span :class="txn.status === 'COMPLETED' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'" class="text-xs px-2 py-0.5 rounded">
                    {{ txn.status }}
                  </span>
                </div>
              </div>
            </div>
            <p v-else class="text-center text-gray-500 py-8">No transactions yet</p>
          </div>
        </div>

        <!-- Recent Tasks -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
          <div class="p-4 border-b bg-gradient-to-r from-blue-500 to-indigo-600">
            <h3 class="text-lg font-bold text-white">Recent Tasks</h3>
          </div>
          <div class="p-4">
            <div v-if="recentTasks.length > 0" class="space-y-2">
              <div v-for="task in recentTasks" :key="task.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex-1">
                  <p class="font-semibold text-sm text-gray-900">{{ task.task_template?.title || 'Task' }}</p>
                  <p class="text-xs text-gray-500">{{ formatDate(task.created_at) }}</p>
                </div>
                <div class="text-right">
                  <p class="font-bold text-green-600">₦{{ formatNumber(task.reward_amount) }}</p>
                  <span :class="task.status === 'COMPLETED' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'" class="text-xs px-2 py-0.5 rounded">
                    {{ task.status }}
                  </span>
                </div>
              </div>
            </div>
            <p v-else class="text-center text-gray-500 py-8">No tasks yet</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="showEditModal = false">
      <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b bg-gradient-to-r from-purple-500 to-indigo-600">
          <div class="flex justify-between items-start">
            <div class="text-white">
              <h3 class="text-xl font-bold">Edit User</h3>
              <p class="text-purple-100 text-sm mt-1">Update user information and permissions</p>
            </div>
            <button @click="showEditModal = false" class="text-white hover:bg-white/20 rounded-lg p-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
            <input v-model="editForm.full_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
            <input v-model="editForm.email" type="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
            <input v-model="editForm.phone_number" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
            <select v-model="editForm.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
              <option value="ACTIVE">Active</option>
              <option value="PENDING">Pending</option>
              <option value="SUSPENDED">Suspended</option>
              <option value="BANNED">Banned</option>
              <option value="UNVERIFIED">Unverified</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">User Type</label>
            <select v-model="editForm.user_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
              <option value="USER">Regular User</option>
              <option value="AGENT">Agent</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
            <select v-model="editForm.role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
              <option :value="0">User</option>
              <option :value="1">Admin</option>
            </select>
          </div>

          <div class="pt-4 border-t flex gap-3">
            <button @click="saveUser" class="flex-1 px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-all">
              Save Changes
            </button>
            <button @click="showEditModal = false" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition-all">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  user: Object,
  stats: Object,
  recentTransactions: Array,
  recentWithdrawals: Array,
  recentTasks: Array,
  settings: Object,
});

const showEditModal = ref(false);
const editForm = reactive({
  full_name: props.user.full_name,
  email: props.user.email,
  phone_number: props.user.phone_number,
  status: props.user.status,
  user_type: props.user.user_type,
  role: props.user.role,
});

const getUserInitials = (name) => {
  return name?.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2) || 'U';
};

const getStatusClass = (status) => {
  const classes = {
    'ACTIVE': 'bg-green-100 text-green-700',
    'PENDING': 'bg-yellow-100 text-yellow-700',
    'SUSPENDED': 'bg-orange-100 text-orange-700',
    'BANNED': 'bg-red-100 text-red-700',
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

const saveUser = () => {
  router.post(`/admin/users/${props.user.id}/update`, editForm, {
    preserveScroll: true,
    onSuccess: () => {
      showEditModal.value = false;
      Swal.fire('Success!', 'User updated successfully', 'success');
    },
  });
};
</script>
