<template>
  <AdminLayout title="Global Settings" :settings="settings">
    <!-- Header -->
    <div class="mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Global Settings</h1>
      <p class="text-gray-600 mt-1 text-sm sm:text-base">Manage your platform's configuration</p>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 mb-6 overflow-x-auto">
      <div class="flex border-b border-gray-200">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'px-4 sm:px-6 py-3 sm:py-4 text-sm font-semibold whitespace-nowrap transition-all',
            activeTab === tab.id
              ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50'
              : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
          ]"
        >
          {{ tab.name }}
        </button>
      </div>
    </div>

    <!-- Tab Content -->
    <component :is="currentTabComponent" :settings="settings" :countries="countries" :plans="plans" :ranks="ranks" @saved="handleSaved" />
  </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppConfig from './Tabs/AppConfig.vue';
import Referral from './Tabs/Referral.vue';
import Tasks from './Tabs/Tasks.vue';
import Ranks from './Tabs/Ranks.vue';
import Financial from './Tabs/Financial.vue';
import Fraud from './Tabs/Fraud.vue';
import Security from './Tabs/Security.vue';
import System from './Tabs/System.vue';
import Email from './Tabs/Email.vue';
import Token from './Tabs/Token.vue';
import KYC from './Tabs/KYC.vue';
import Notifications from './Tabs/Notifications.vue';
import AI from './Tabs/AI.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  settings: Object,
  countries: Array,
  plans: Array,
  ranks: Array
});

const tabs = [
  { id: 'app', name: 'App Config', component: AppConfig },
  { id: 'referral', name: 'Referral & Commission', component: Referral },
  { id: 'tasks', name: 'Tasks', component: Tasks },
  { id: 'ai', name: 'AI Task Generation', component: AI },
  { id: 'ranks', name: 'Ranks', component: Ranks },
  { id: 'financial', name: 'Financial', component: Financial },
  { id: 'token', name: 'Token & Liquidity', component: Token },
  { id: 'kyc', name: 'KYC & Testimonials', component: KYC },
  { id: 'notifications', name: 'Notifications', component: Notifications },
  { id: 'email', name: 'Email & Integrations', component: Email },
  { id: 'fraud', name: 'Fraud Detection', component: Fraud },
  { id: 'security', name: 'reCAPTCHA', component: Security },
  { id: 'system', name: 'System Controls', component: System },
];

const activeTab = ref('app');

const currentTabComponent = computed(() => {
  const tab = tabs.find(t => t.id === activeTab.value);
  return tab?.component || 'div';
});

const handleSaved = () => {
  // Reload settings data only (keeps tab active) - preserveState keeps current tab
  router.reload({
    only: ['settings'],
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      // Show success notification
      Swal.fire({
        icon: 'success',
        title: 'Settings Saved!',
        text: 'Your changes have been saved successfully.',
        timer: 2000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
      });
    }
  });
};
</script>
