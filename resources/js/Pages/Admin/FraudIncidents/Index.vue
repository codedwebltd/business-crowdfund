<template>
  <AdminLayout title="Fraud Incidents" :settings="settings">
    <div class="mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Fraud Detection & Security</h1>
      <p class="text-gray-600 mt-1">Monitor and manage security incidents across the platform</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Total Incidents</p>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ stats.total }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-red-500 to-red-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Today</p>
            <p class="text-2xl sm:text-3xl font-bold text-orange-600 mt-1">{{ stats.today }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">Active Bans</p>
            <p class="text-2xl sm:text-3xl font-bold text-red-600 mt-1">{{ stats.active_bans }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-red-500 to-red-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">High Severity</p>
            <p class="text-2xl sm:text-3xl font-bold text-purple-600 mt-1">{{ stats.high_severity }}</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4 mb-6">
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <select v-model="filters.incident_type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
          <option value="">All Incident Types</option>
          <option value="BOT_SPEED">Bot Speed</option>
          <option value="VELOCITY_ABUSE">Velocity Abuse</option>
          <option value="PATTERN_ABUSE">Pattern Abuse</option>
          <option value="DEVICE_SHARING">Device Sharing</option>
        </select>
        <select v-model="filters.severity" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
          <option value="">All Severity Levels</option>
          <option value="LOW">Low</option>
          <option value="MEDIUM">Medium</option>
          <option value="HIGH">High</option>
        </select>
        <input v-model="filters.search" type="text" placeholder="Search by user..." class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
        <button @click="applyFilters" class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg text-sm font-semibold hover:shadow-lg hover:shadow-red-500/50 transition-all">
          Apply Filters
        </button>
      </div>
    </div>

    <!-- Incidents Table -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-red-500 to-orange-600">
        <h2 class="text-lg font-bold text-white">All Fraud Incidents</h2>
        <p class="text-red-100 text-sm mt-1">{{ incidents.total }} incidents recorded</p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b-2 border-gray-200">
            <tr>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">User</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Incident Type</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Severity</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Action Taken</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Ban Status</th>
              <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="incident in incidents.data" :key="incident.id" class="hover:bg-red-50 transition-colors">
              <td class="px-4 py-4">
                <div>
                  <div class="font-semibold text-gray-900 text-sm">{{ incident.user?.name || 'Unknown' }}</div>
                  <div class="text-xs text-gray-500">{{ incident.user?.email || 'N/A' }}</div>
                </div>
              </td>
              <td class="px-4 py-4">
                <span :class="getIncidentTypeBadge(incident.incident_type)" class="px-3 py-1.5 rounded-lg text-xs font-bold inline-flex items-center gap-1.5">
                  <span class="w-2 h-2 rounded-full bg-current"></span>
                  {{ formatIncidentType(incident.incident_type) }}
                </span>
              </td>
              <td class="px-4 py-4">
                <span :class="getSeverityBadge(incident.severity)" class="px-3 py-1.5 rounded-lg text-xs font-bold inline-flex items-center gap-1.5">
                  <span :class="getSeverityIcon(incident.severity)"></span>
                  {{ incident.severity }}
                </span>
              </td>
              <td class="px-4 py-4">
                <div class="text-sm text-gray-900 font-medium">{{ formatDate(incident.created_at) }}</div>
                <div class="text-xs text-gray-500">{{ formatTime(incident.created_at) }}</div>
              </td>
              <td class="px-4 py-4">
                <div class="text-sm font-medium" :class="getActionColor(incident.action_taken)">
                  {{ formatActionTaken(incident.action_taken) }}
                </div>
              </td>
              <td class="px-4 py-4">
                <div v-if="incident.user?.task_ban_until && new Date(incident.user.task_ban_until) > new Date()">
                  <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-red-100 text-red-700 border border-red-200 inline-flex items-center gap-1.5">
                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                    Banned
                  </span>
                  <div class="text-xs text-gray-500 mt-1">Until: {{ formatDate(incident.user.task_ban_until) }}</div>
                </div>
                <span v-else class="px-3 py-1.5 rounded-lg text-xs font-bold bg-green-100 text-green-700 border border-green-200 inline-flex items-center gap-1.5">
                  <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                  Active
                </span>
              </td>
              <td class="px-4 py-4">
                <div class="flex items-center justify-end gap-1.5">
                  <button @click="viewIncident(incident)" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="View Details">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                  <button
                    v-if="incident.user?.task_ban_until && new Date(incident.user.task_ban_until) > new Date()"
                    @click="unsuspendUser(incident.user.id)"
                    class="p-2 text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg transition-all shadow-sm"
                    title="Unsuspend User"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </button>
                  <button
                    v-if="!incident.action_taken.includes('RESOLVED')"
                    @click="resolveIncident(incident.id)"
                    class="p-2 text-purple-600 hover:bg-purple-100 rounded-lg transition-colors"
                    title="Mark as Resolved"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
        <div class="text-sm text-gray-600">
          Showing {{ incidents.from }} to {{ incidents.to }} of {{ incidents.total }} incidents
        </div>
        <div class="flex gap-2">
          <button
            v-for="link in incidents.links"
            :key="link.label"
            @click="changePage(link.url)"
            :disabled="!link.url"
            :class="link.active ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
            class="px-3 py-1 rounded border text-sm disabled:opacity-50 disabled:cursor-not-allowed transition-all"
            v-html="link.label"
          ></button>
        </div>
      </div>
    </div>

    <!-- View Modal -->
    <div v-if="viewingIncident" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="viewingIncident = null">
      <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b bg-gradient-to-r from-red-500 to-orange-600">
          <div class="flex justify-between items-start">
            <div class="text-white">
              <h3 class="text-xl font-bold">Incident Details</h3>
              <p class="text-red-100 text-sm mt-1">{{ formatIncidentType(viewingIncident.incident_type) }} - {{ viewingIncident.severity }}</p>
            </div>
            <button @click="viewingIncident = null" class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-xs font-semibold text-gray-600 uppercase mb-1">User</p>
              <p class="text-sm font-bold text-gray-900">{{ viewingIncident.user?.name || 'Unknown' }}</p>
              <p class="text-xs text-gray-500">{{ viewingIncident.user?.email || 'N/A' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Date & Time</p>
              <p class="text-sm font-bold text-gray-900">{{ formatDate(viewingIncident.created_at) }}</p>
              <p class="text-xs text-gray-500">{{ formatTime(viewingIncident.created_at) }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Incident Type</p>
              <p class="text-sm font-bold text-gray-900">{{ formatIncidentType(viewingIncident.incident_type) }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Severity</p>
              <p class="text-sm font-bold" :class="getSeverityTextColor(viewingIncident.severity)">{{ viewingIncident.severity }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg col-span-2">
              <p class="text-xs font-semibold text-gray-600 uppercase mb-1">Action Taken</p>
              <p class="text-sm font-bold text-gray-900">{{ formatActionTaken(viewingIncident.action_taken) }}</p>
            </div>
          </div>

          <div class="bg-gradient-to-br from-red-50 to-orange-50 p-4 rounded-lg border border-red-200">
            <p class="text-xs font-semibold text-red-700 uppercase mb-2">Incident Data</p>
            <pre class="text-xs bg-white p-3 rounded border border-red-200 overflow-x-auto text-gray-800 font-mono">{{ JSON.stringify(viewingIncident.incident_data, null, 2) }}</pre>
          </div>

          <div v-if="viewingIncident.banned_until" class="bg-red-100 border border-red-300 rounded-lg p-4">
            <p class="text-sm font-bold text-red-800">Ban Information</p>
            <p class="text-xs text-red-700 mt-1">Banned until: {{ formatDate(viewingIncident.banned_until) }} at {{ formatTime(viewingIncident.banned_until) }}</p>
          </div>

          <div class="flex gap-3 pt-4 border-t">
            <button
              v-if="viewingIncident.user?.task_ban_until && new Date(viewingIncident.user.task_ban_until) > new Date()"
              @click="unsuspendUser(viewingIncident.user.id)"
              class="flex-1 py-3 px-4 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg font-semibold hover:shadow-lg hover:shadow-green-500/50 transition-all flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Unsuspend User
            </button>
            <button
              v-if="!viewingIncident.action_taken.includes('RESOLVED')"
              @click="resolveIncident(viewingIncident.id)"
              class="flex-1 py-3 px-4 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition-all flex items-center justify-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              Mark as Resolved
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
import Swal from 'sweetalert2';

const props = defineProps({
  incidents: Object,
  stats: Object,
  settings: Object
});

const filters = reactive({
  incident_type: '',
  severity: '',
  search: ''
});

const viewingIncident = ref(null);

const getIncidentTypeBadge = (type) => ({
  'BOT_SPEED': 'bg-orange-100 text-orange-700 border border-orange-200',
  'VELOCITY_ABUSE': 'bg-red-100 text-red-700 border border-red-200',
  'PATTERN_ABUSE': 'bg-yellow-100 text-yellow-700 border border-yellow-200',
  'DEVICE_SHARING': 'bg-purple-100 text-purple-700 border border-purple-200'
}[type] || 'bg-gray-100 text-gray-700 border border-gray-200');

const getSeverityBadge = (severity) => ({
  'LOW': 'bg-blue-100 text-blue-700 border border-blue-200',
  'MEDIUM': 'bg-yellow-100 text-yellow-700 border border-yellow-200',
  'HIGH': 'bg-red-100 text-red-700 border border-red-200'
}[severity] || 'bg-gray-100 text-gray-700 border border-gray-200');

const getSeverityIcon = (severity) => ({
  'LOW': 'w-2 h-2 bg-blue-500 rounded-full',
  'MEDIUM': 'w-2 h-2 bg-yellow-500 rounded-full',
  'HIGH': 'w-2 h-2 bg-red-500 rounded-full animate-pulse'
}[severity] || 'w-2 h-2 bg-gray-500 rounded-full');

const getSeverityTextColor = (severity) => ({
  'LOW': 'text-blue-600',
  'MEDIUM': 'text-yellow-600',
  'HIGH': 'text-red-600'
}[severity] || 'text-gray-600');

const getActionColor = (action) => {
  if (action.includes('BANNED')) return 'text-red-600';
  if (action.includes('WARNING')) return 'text-yellow-600';
  if (action.includes('RESOLVED')) return 'text-green-600';
  return 'text-gray-600';
};

const formatIncidentType = (type) => {
  return type.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
};

const formatActionTaken = (action) => {
  return action.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
};

const formatDate = (date) => new Date(date).toLocaleDateString('en-US', {
  month: 'short',
  day: 'numeric',
  year: 'numeric'
});

const formatTime = (date) => new Date(date).toLocaleTimeString('en-US', {
  hour: '2-digit',
  minute: '2-digit'
});

const viewIncident = (incident) => viewingIncident.value = incident;

const applyFilters = () => router.get('/admin/fraud-incidents', filters, { preserveState: true });

const changePage = (url) => url && router.visit(url);

const unsuspendUser = (userId) => {
  Swal.fire({
    title: 'Unsuspend User?',
    text: 'This will remove the task ban and allow the user to complete tasks again.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, unsuspend',
    confirmButtonColor: '#10b981',
    cancelButtonColor: '#6b7280'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/fraud-incidents/${userId}/unsuspend`, {}, {
        preserveScroll: true,
        onSuccess: () => {
          viewingIncident.value = null;
          Swal.fire({
            icon: 'success',
            title: 'User Unsuspended',
            text: 'The user can now complete tasks again.',
            confirmButtonColor: '#10b981'
          });
        }
      });
    }
  });
};

const resolveIncident = (incidentId) => {
  Swal.fire({
    title: 'Mark as Resolved?',
    text: 'This will mark the incident as resolved without changing the ban status.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, resolve',
    confirmButtonColor: '#8b5cf6',
    cancelButtonColor: '#6b7280'
  }).then((result) => {
    if (result.isConfirmed) {
      router.post(`/admin/fraud-incidents/${incidentId}/resolve`, {}, {
        preserveScroll: true,
        onSuccess: () => {
          viewingIncident.value = null;
          Swal.fire({
            icon: 'success',
            title: 'Incident Resolved',
            text: 'The incident has been marked as resolved.',
            confirmButtonColor: '#8b5cf6'
          });
        }
      });
    }
  });
};
</script>
