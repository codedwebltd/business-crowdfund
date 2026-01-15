<template>
  <AdminLayout title="Plan Management" :settings="settings">
    <!-- Breadcrumbs -->
    <Breadcrumbs :crumbs="[
      { label: 'Dashboard', url: '/admin/dashboard' },
      { label: 'Plans' }
    ]" />

    <div class="mb-6 mt-4">
      <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Plan Management</h1>
          <p class="text-gray-600 mt-1">Manage subscription plans and pricing</p>
        </div>
        <button @click="showCreateModal = true" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-all flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Create Plan
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs font-medium text-gray-600">Total Plans</p>
          <div class="p-2 bg-purple-100 rounded-lg">
            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
          </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ stats.total_plans }}</p>
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
        <p class="text-2xl font-bold text-green-600">{{ stats.active_plans }}</p>
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
        <p class="text-2xl font-bold text-gray-600">{{ stats.inactive_plans }}</p>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs font-medium text-gray-600">Subscribers</p>
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </div>
        </div>
        <p class="text-2xl font-bold text-blue-600">{{ stats.total_subscribed_users }}</p>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs font-medium text-gray-600">Total Revenue</p>
          <div class="p-2 bg-emerald-100 rounded-lg">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <p class="text-2xl font-bold text-emerald-600">₦{{ formatNumber(stats.total_revenue) }}</p>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs font-medium text-gray-600">Popular Plan</p>
          <div class="p-2 bg-yellow-100 rounded-lg">
            <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2L15 8.5L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L9 8.5L12 2Z"/>
            </svg>
          </div>
        </div>
        <p class="text-lg font-bold text-yellow-600 truncate">{{ stats.popular_plan }}</p>
      </div>
    </div>

    <!-- Plans Table -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold uppercase">Order</th>
              <th class="px-6 py-4 text-left text-xs font-bold uppercase">Plan</th>
              <th class="px-6 py-4 text-left text-xs font-bold uppercase">Price</th>
              <th class="px-6 py-4 text-left text-xs font-bold uppercase">Features</th>
              <th class="px-6 py-4 text-left text-xs font-bold uppercase">Rank</th>
              <th class="px-6 py-4 text-left text-xs font-bold uppercase">Users</th>
              <th class="px-6 py-4 text-left text-xs font-bold uppercase">Status</th>
              <th class="px-6 py-4 text-left text-xs font-bold uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="plan in plans" :key="plan.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-6 py-4">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-700 font-bold text-sm">
                  {{ plan.order }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full flex items-center justify-center" :style="{ backgroundColor: plan.badge_color || '#7c3aed' }">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                  </div>
                  <div>
                    <div class="flex items-center gap-2">
                      <p class="font-bold text-gray-900">{{ plan.display_name }}</p>
                      <span v-if="plan.is_popular" class="inline-flex items-center gap-1 px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                          <path d="M12 2L15 8.5L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L9 8.5L12 2Z"/>
                        </svg>
                        Popular
                      </span>
                    </div>
                    <p class="text-xs text-gray-500">{{ plan.description }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <p class="text-lg font-bold text-purple-600">₦{{ formatNumber(plan.price) }}</p>
                <p class="text-xs text-gray-500">{{ plan.currency }}</p>
              </td>
              <td class="px-6 py-4">
                <div class="space-y-1 text-xs">
                  <p class="text-gray-600"><span class="font-semibold">Daily Tasks:</span> {{ plan.features.max_daily_tasks }}</p>
                  <p class="text-gray-600"><span class="font-semibold">Earning Potential:</span> ₦{{ formatNumber(plan.features.daily_earning_potential) }}</p>
                  <p class="text-gray-600"><span class="font-semibold">Referral Bonus:</span> {{ plan.features.referral_bonus_percentage }}%</p>
                  <p class="text-gray-600"><span class="font-semibold">Multiplier:</span> {{ plan.features.task_reward_multiplier }}x</p>
                  <p class="text-gray-600">
                    <span class="font-semibold">Support:</span>
                    <span :class="plan.features.priority_support ? 'text-green-600' : 'text-gray-500'">
                      {{ plan.features.priority_support ? 'Priority' : 'Standard' }}
                    </span>
                  </p>
                </div>
              </td>
              <td class="px-6 py-4">
                <span v-if="plan.rank" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold" :style="{ backgroundColor: plan.rank.badge_color + '20', color: plan.rank.badge_color }">
                  {{ plan.rank.display_name }}
                </span>
                <span v-else class="text-gray-400 text-xs">No Rank</span>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-bold">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                  </svg>
                  {{ plan.users_count }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-col gap-2">
                  <button
                    @click="toggleStatus(plan)"
                    :class="plan.is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold transition-all"
                  >
                    <span class="w-2 h-2 rounded-full" :class="plan.is_active ? 'bg-green-500' : 'bg-gray-500'"></span>
                    {{ plan.is_active ? 'Active' : 'Inactive' }}
                  </button>
                  <button
                    @click="togglePopular(plan)"
                    :class="plan.is_popular ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold transition-all"
                  >
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 2L15 8.5L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L9 8.5L12 2Z"/>
                    </svg>
                    {{ plan.is_popular ? 'Popular' : 'Normal' }}
                  </button>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <button @click="editPlan(plan)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </button>
                  <button @click="deletePlan(plan)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
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
    <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto" @click.self="closeModal">
      <div class="bg-white rounded-2xl max-w-4xl w-full my-8">
        <div class="p-6 border-b bg-gradient-to-r from-purple-500 to-indigo-600">
          <div class="flex justify-between items-start">
            <div class="text-white">
              <h3 class="text-xl font-bold">{{ showEditModal ? 'Edit Plan' : 'Create New Plan' }}</h3>
              <p class="text-purple-100 text-sm mt-1">{{ showEditModal ? 'Update plan details and features' : 'Add a new subscription plan' }}</p>
            </div>
            <button @click="closeModal" class="text-white hover:bg-white/20 rounded-lg p-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
          <!-- Basic Info -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Name (lowercase)</label>
              <input v-model="form.name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="basic">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Display Name</label>
              <input v-model="form.display_name" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Basic">
            </div>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
            <textarea v-model="form.description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Plan description..."></textarea>
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

          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Price (₦)</label>
              <input v-model="form.price" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Currency</label>
              <input v-model="form.currency" type="text" maxlength="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Assign Rank</label>
              <select v-model="form.rank_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                <option :value="null">No Rank</option>
                <option v-for="rank in ranks" :key="rank.id" :value="rank.id">{{ rank.display_name }}</option>
              </select>
            </div>
          </div>

          <!-- Features Section -->
          <div class="border-t pt-4">
            <h4 class="text-lg font-bold text-gray-900 mb-4">Plan Features</h4>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Max Daily Tasks</label>
                <input v-model="form.features.max_daily_tasks" type="number" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Daily Earning Potential (₦)</label>
                <input v-model="form.features.daily_earning_potential" type="number" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Referral Bonus (%)</label>
                <input v-model="form.features.referral_bonus_percentage" type="number" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Task Reward Multiplier</label>
                <input v-model="form.features.task_reward_multiplier" type="number" min="1" max="3" step="0.1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
              </div>
              <div class="col-span-2">
                <label class="flex items-center gap-2">
                  <input v-model="form.features.priority_support" type="checkbox" class="w-4 h-4 text-purple-600 rounded focus:ring-2 focus:ring-purple-500">
                  <span class="text-sm font-semibold text-gray-700">Priority Support</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Feature List -->
          <div class="border-t pt-4">
            <div class="flex items-center justify-between mb-3">
              <h4 class="text-lg font-bold text-gray-900">Feature List</h4>
              <button @click="addFeature" class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-semibold transition-all">
                + Add Feature
              </button>
            </div>
            <div class="space-y-2">
              <div v-for="(feature, index) in form.features.feature_list" :key="index" class="flex items-center gap-2">
                <input v-model="form.features.feature_list[index]" type="text" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Feature description">
                <button @click="removeFeature(index)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="p-6 border-t flex gap-3">
          <button @click="savePlan" class="flex-1 px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-all">
            {{ showEditModal ? 'Update Plan' : 'Create Plan' }}
          </button>
          <button @click="closeModal" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition-all">
            Cancel
          </button>
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
  plans: Array,
  stats: Object,
  ranks: Array,
  settings: Object,
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingPlan = ref(null);

const form = reactive({
  name: '',
  display_name: '',
  description: '',
  order: 1,
  price: 5000,
  currency: 'NGN',
  badge_color: '#7c3aed',
  icon: '',
  is_popular: false,
  rank_id: null,
  features: {
    max_daily_tasks: 8,
    daily_earning_potential: 800,
    referral_bonus_percentage: 10,
    task_reward_multiplier: 1.0,
    priority_support: false,
    feature_list: ['8 tasks per day', '₦800 daily earning potential', '10% referral bonus'],
  },
});

const formatNumber = (num) => {
  return new Intl.NumberFormat().format(num || 0);
};

const editPlan = (plan) => {
  editingPlan.value = plan;
  form.name = plan.name;
  form.display_name = plan.display_name;
  form.description = plan.description;
  form.order = plan.order;
  form.price = plan.price;
  form.currency = plan.currency;
  form.badge_color = plan.badge_color || '#7c3aed';
  form.icon = plan.icon || '';
  form.is_popular = plan.is_popular;
  form.rank_id = plan.rank_id;
  form.features = { ...plan.features };
  showEditModal.value = true;
};

const closeModal = () => {
  showCreateModal.value = false;
  showEditModal.value = false;
  editingPlan.value = null;
  resetForm();
};

const resetForm = () => {
  form.name = '';
  form.display_name = '';
  form.description = '';
  form.order = 1;
  form.price = 5000;
  form.currency = 'NGN';
  form.badge_color = '#7c3aed';
  form.icon = '';
  form.is_popular = false;
  form.rank_id = null;
  form.features = {
    max_daily_tasks: 8,
    daily_earning_potential: 800,
    referral_bonus_percentage: 10,
    task_reward_multiplier: 1.0,
    priority_support: false,
    feature_list: ['8 tasks per day', '₦800 daily earning potential', '10% referral bonus'],
  };
};

const addFeature = () => {
  form.features.feature_list.push('');
};

const removeFeature = (index) => {
  form.features.feature_list.splice(index, 1);
};

const savePlan = () => {
  if (showEditModal.value) {
    router.post(`/admin/subscriptions/${editingPlan.value.id}/update`, form, {
      preserveScroll: true,
      onSuccess: () => {
        closeModal();
        Swal.fire('Success!', 'Plan updated successfully', 'success');
      },
    });
  } else {
    router.post('/admin/subscriptions', form, {
      preserveScroll: true,
      onSuccess: () => {
        closeModal();
        Swal.fire('Success!', 'Plan created successfully', 'success');
      },
    });
  }
};

const toggleStatus = (plan) => {
  router.post(`/admin/subscriptions/${plan.id}/toggle`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire('Success!', 'Plan status updated', 'success');
    },
  });
};

const togglePopular = (plan) => {
  router.post(`/admin/subscriptions/${plan.id}/toggle-popular`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      Swal.fire('Success!', 'Popular plan updated', 'success');
    },
  });
};

const deletePlan = (plan) => {
  Swal.fire({
    title: 'Delete Plan?',
    text: `Are you sure you want to delete ${plan.display_name}? This cannot be undone.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, delete it',
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/admin/subscriptions/${plan.id}`, {
        preserveScroll: true,
        onSuccess: () => {
          Swal.fire('Deleted!', 'Plan has been deleted', 'success');
        },
        onError: () => {
          Swal.fire('Error!', 'Cannot delete plan with active users', 'error');
        },
      });
    }
  });
};
</script>
