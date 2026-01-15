<template>
  <AdminLayout title="Rank Management" :settings="settings">
    <!-- Breadcrumbs -->
    <Breadcrumbs :crumbs="[
      { label: 'Dashboard', url: '/admin/dashboard' },
      { label: 'Ranks' }
    ]" />

    <div class="mb-6 mt-4">
      <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Rank Management</h1>
          <p class="text-gray-600 mt-1">Manage user rank tiers and benefits</p>
        </div>
        <button @click="showCreateModal = true" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-all flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Create Rank
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs font-medium text-gray-600">Total Ranks</p>
          <div class="p-2 bg-purple-100 rounded-lg">
            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
            </svg>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ stats.total_ranks }}</p>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs font-medium text-gray-600">Active</p>
          <div class="p-2 bg-green-100 rounded-lg">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <p class="text-2xl font-bold text-green-600">{{ stats.active_ranks }}</p>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs font-medium text-gray-600">Inactive</p>
          <div class="p-2 bg-gray-100 rounded-lg">
            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-600">{{ stats.inactive_ranks }}</p>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs font-medium text-gray-600">Ranked Users</p>
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </div>
        </div>
        <p class="text-2xl font-bold text-blue-600">{{ stats.total_ranked_users }}</p>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs font-medium text-gray-600">Unranked</p>
          <div class="p-2 bg-orange-100 rounded-lg">
            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </div>
        </div>
        <p class="text-2xl font-bold text-orange-600">{{ stats.unranked_users }}</p>
      </div>
    </div>

    <!-- Ranks Table -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold uppercase">Order</th>
              <th class="px-6 py-4 text-left text-xs font-bold uppercase">Rank</th>
              <th class="px-6 py-4 text-left text-xs font-bold uppercase">Criteria</th>
              <th class="px-6 py-4 text-left text-xs font-bold uppercase">Benefits</th>
              <th class="px-6 py-4 text-left text-xs font-bold uppercase">Users</th>
              <th class="px-6 py-4 text-left text-xs font-bold uppercase">Status</th>
              <th class="px-6 py-4 text-left text-xs font-bold uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="rank in ranks" :key="rank.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-700 font-bold text-sm">
                  {{ rank.order }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full flex items-center justify-center" :style="{ backgroundColor: rank.badge_color }">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="font-bold text-gray-900">{{ rank.display_name }}</p>
                    <p class="text-xs text-gray-500">{{ rank.description }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="space-y-1 text-xs">
                  <p class="text-gray-600"><span class="font-semibold">Direct:</span> {{ rank.criteria.min_direct_referrals }}+</p>
                  <p class="text-gray-600"><span class="font-semibold">Team:</span> {{ rank.criteria.min_team_size }}+</p>
                  <p class="text-gray-600"><span class="font-semibold">Volume:</span> ₦{{ formatNumber(rank.criteria.min_monthly_volume) }}</p>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="space-y-1 text-xs">
                  <p class="text-gray-600"><span class="font-semibold">W/D Range:</span> ₦{{ formatNumber(rank.benefits.withdrawal_min) }} - ₦{{ formatNumber(rank.benefits.withdrawal_max) }}</p>
                  <p class="text-gray-600"><span class="font-semibold">Daily W/D:</span> {{ rank.benefits.withdrawals_per_day }}x</p>
                  <p class="text-gray-600"><span class="font-semibold">Multiplier:</span> {{ rank.benefits.commission_multiplier }}x</p>
                  <p class="text-gray-600"><span class="font-semibold">Processing:</span> {{ rank.benefits.processing_hours }}</p>
                </div>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-bold">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                  </svg>
                  {{ rank.users_count }}
                </span>
              </td>
              <td class="px-6 py-4">
                <button
                  @click="toggleStatus(rank)"
                  :class="rank.is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                  class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold transition-all"
                >
                  <span class="w-2 h-2 rounded-full" :class="rank.is_active ? 'bg-green-500' : 'bg-gray-500'"></span>
                  {{ rank.is_active ? 'Active' : 'Inactive' }}
                </button>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <button @click="editRank(rank)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </button>
                  <button @click="deleteRank(rank)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="closeModal">
      <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b bg-gradient-to-r from-purple-500 to-indigo-600">
          <div class="flex justify-between items-start">
            <div class="text-white">
              <h3 class="text-xl font-bold">{{ showEditModal ? 'Edit Rank' : 'Create New Rank' }}</h3>
              <p class="text-purple-100 text-sm mt-1">{{ showEditModal ? 'Update rank details and benefits' : 'Add a new rank tier to the system' }}</p>
            </div>
            <button @click="closeModal" class="text-white hover:bg-white/20 rounded-lg p-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-6">
          <!-- Basic Info -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Name (lowercase)</label>
              <input v-model="form.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="bronze">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Display Name</label>
              <input v-model="form.display_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Bronze">
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Order</label>
              <input v-model="form.order" type="number" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Badge Color</label>
              <input v-model="form.badge_color" type="color" class="w-full h-10 px-2 py-1 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
            <textarea v-model="form.description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Rank description..."></textarea>
          </div>

          <!-- Criteria Section -->
          <div class="border-t pt-4">
            <h4 class="text-lg font-bold text-gray-900 mb-4">Rank Criteria</h4>
            <div class="grid grid-cols-3 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Min Direct Referrals</label>
                <input v-model="form.criteria.min_direct_referrals" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Min Team Size</label>
                <input v-model="form.criteria.min_team_size" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Min Monthly Volume (₦)</label>
                <input v-model="form.criteria.min_monthly_volume" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
              </div>
            </div>
          </div>

          <!-- Benefits Section -->
          <div class="border-t pt-4">
            <h4 class="text-lg font-bold text-gray-900 mb-4">Rank Benefits</h4>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Withdrawal Min (₦)</label>
                <input v-model="form.benefits.withdrawal_min" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Withdrawal Max (₦)</label>
                <input v-model="form.benefits.withdrawal_max" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Withdrawals Per Day</label>
                <input v-model="form.benefits.withdrawals_per_day" type="number" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Commission Multiplier</label>
                <input v-model="form.benefits.commission_multiplier" type="number" min="1" max="2" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
              </div>
              <div class="col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Processing Hours</label>
                <input v-model="form.benefits.processing_hours" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="24-48 or instant">
              </div>
            </div>
          </div>

          <div class="pt-4 border-t flex gap-3">
            <button @click="saveRank" class="flex-1 px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-all">
              {{ showEditModal ? 'Update Rank' : 'Create Rank' }}
            </button>
            <button @click="closeModal" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition-all">
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
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  ranks: Array,
  stats: Object,
  settings: Object,
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingRank = ref(null);

const form = reactive({
  name: '',
  display_name: '',
  description: '',
  order: 1,
  badge_color: '#CD7F32',
  icon: '',
  criteria: {
    min_direct_referrals: 0,
    min_team_size: 0,
    min_monthly_volume: 0,
  },
  benefits: {
    withdrawal_min: 5000,
    withdrawal_max: 50000,
    withdrawals_per_day: 1,
    commission_multiplier: 1.0,
    processing_hours: '48-72',
  },
});

const formatNumber = (num) => {
  return new Intl.NumberFormat().format(num || 0);
};

const editRank = (rank) => {
  editingRank.value = rank;
  form.name = rank.name;
  form.display_name = rank.display_name;
  form.description = rank.description;
  form.order = rank.order;
  form.badge_color = rank.badge_color;
  form.icon = rank.icon || '';
  form.criteria = { ...rank.criteria };
  form.benefits = { ...rank.benefits };
  showEditModal.value = true;
};

const closeModal = () => {
  showCreateModal.value = false;
  showEditModal.value = false;
  editingRank.value = null;
  resetForm();
};

const resetForm = () => {
  form.name = '';
  form.display_name = '';
  form.description = '';
  form.order = 1;
  form.badge_color = '#CD7F32';
  form.icon = '';
  form.criteria = {
    min_direct_referrals: 0,
    min_team_size: 0,
    min_monthly_volume: 0,
  };
  form.benefits = {
    withdrawal_min: 5000,
    withdrawal_max: 50000,
    withdrawals_per_day: 1,
    commission_multiplier: 1.0,
    processing_hours: '48-72',
  };
};

const saveRank = () => {
  if (showEditModal.value) {
    router.post(`/admin/ranks/${editingRank.value.id}/update`, form, {
      preserveScroll: true,
      onSuccess: () => {
        closeModal();
        Swal.fire('Success!', 'Rank updated successfully', 'success');
      },
    });
  } else {
    router.post('/admin/ranks', form, {
      preserveScroll: true,
      onSuccess: () => {
        closeModal();
        Swal.fire('Success!', 'Rank created successfully', 'success');
      },
    });
  }
};

const toggleStatus = (rank) => {
  router.post(`/admin/ranks/${rank.id}/toggle`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire('Success!', 'Rank status updated', 'success');
    },
  });
};

const deleteRank = (rank) => {
  Swal.fire({
    title: 'Delete Rank?',
    text: `Are you sure you want to delete ${rank.display_name}? This cannot be undone.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, delete it',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/admin/ranks/${rank.id}`, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire('Deleted!', 'Rank has been deleted', 'success');
        },
        onError: () => {
          Swal.fire('Error!', 'Cannot delete rank with active users', 'error');
        },
      });
    }
  });
};
</script>
