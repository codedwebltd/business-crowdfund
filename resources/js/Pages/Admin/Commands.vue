<template>
  <AdminLayout title="Command Control Center" :settings="settings">
    <Breadcrumbs :crumbs="[
      { label: 'Home', url: '/admin/dashboard' },
      { label: 'Commands' }
    ]" class="mb-4"/>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Pending Jobs</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ pendingJobs }}</p>
            <p class="text-xs text-blue-600 mt-1">In queue</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Active Batches</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ batches.filter(b => !b.finished_at).length }}</p>
            <p class="text-xs text-purple-600 mt-1">Running now</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-5 hover:shadow-lg transition-all">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-gray-600">Failed Jobs</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ failedJobs.length }}</p>
            <p class="text-xs text-red-600 mt-1">Need attention</p>
          </div>
          <div class="p-3 bg-gradient-to-br from-red-500 to-red-600 rounded-xl">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Command Execution Panel -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 mb-6">
      <div class="p-6 border-b bg-gradient-to-r from-indigo-500 to-purple-600">
        <h2 class="text-lg font-bold text-white">Execute Command</h2>
        <p class="text-purple-100 text-sm mt-1">Run platform maintenance commands</p>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mb-6">
          <button @click="executeCommand('tasks:assign-daily')" :disabled="executing" 
                  class="bg-gradient-to-br from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 disabled:opacity-50 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-sm">
            🎯 Assign Tasks
          </button>
          <button @click="showGenerateTasksModal = true" :disabled="executing"
                  class="bg-gradient-to-br from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 disabled:opacity-50 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-sm">
            🤖 Generate Tasks
          </button>
          <button @click="executeCommand('tasks:mature-earnings')" :disabled="executing"
                  class="bg-gradient-to-br from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700 disabled:opacity-50 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-sm">
            💰 Mature Earnings
          </button>
          <button @click="executeCommand('commissions:disburse')" :disabled="executing"
                  class="bg-gradient-to-br from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 disabled:opacity-50 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-sm">
            💸 Disburse Commissions
          </button>
          <button @click="executeCommand('liquidity:calculate-burn-rate')" :disabled="executing"
                  class="bg-gradient-to-br from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 disabled:opacity-50 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-sm">
            📊 Calculate Burn Rate
          </button>
          <button @click="executeCommand('cache:clear')" :disabled="executing"
                  class="bg-gradient-to-br from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 disabled:opacity-50 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-sm">
            🗑️ Clear Cache
          </button>
          <button @click="executeCommand('queue:restart')" :disabled="executing"
                  class="bg-gradient-to-br from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 disabled:opacity-50 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-sm">
            🔄 Restart Queue
          </button>
          <button @click="clearAllFailedJobs" :disabled="executing || failedJobs.length === 0"
                  class="bg-gradient-to-br from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 disabled:opacity-50 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-sm">
            🧹 Clear Failed Jobs
          </button>
        </div>

        <!-- Command Output -->
        <div v-if="commandOutput" class="bg-gray-900 rounded-xl p-4 mt-4">
          <div class="flex items-center justify-between mb-2">
            <p class="text-green-400 text-xs font-mono font-semibold">$ {{ lastCommand }}</p>
            <button @click="commandOutput = ''" class="text-gray-400 hover:text-white text-xs">✕</button>
          </div>
          <pre class="text-green-300 text-xs font-mono whitespace-pre-wrap">{{ commandOutput }}</pre>
        </div>
      </div>
    </div>

    <!-- Batch Jobs -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 mb-6">
      <div class="p-6 border-b bg-gradient-to-r from-purple-500 to-pink-600 flex justify-between items-center">
        <div>
          <h2 class="text-lg font-bold text-white">Batch Jobs</h2>
          <p class="text-purple-100 text-sm mt-1">Monitor batch job progress</p>
        </div>
        <div class="flex gap-2">
          <button @click="refreshBatches" class="px-4 py-2 bg-white text-purple-600 rounded-lg font-semibold hover:bg-purple-50 transition-all text-sm">
            🔄 Refresh
          </button>
          <button @click="clearAllBatches" :disabled="batches.length === 0" class="px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 disabled:opacity-50 transition-all text-sm">
            🗑️ Clear All
          </button>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Batch ID</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Name</th>
              <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Progress</th>
              <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Jobs</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Created</th>
              <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-if="batches.length === 0">
              <td colspan="6" class="px-4 py-6 text-center text-gray-500 text-sm">No batch jobs</td>
            </tr>
            <tr v-for="batch in batches" :key="batch.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 text-xs font-mono text-gray-600">{{ batch.id.substring(0, 8) }}...</td>
              <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ batch.name || 'Unnamed' }}</td>
              <td class="px-4 py-3">
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div class="bg-gradient-to-r from-purple-500 to-pink-600 h-2 rounded-full transition-all" 
                       :style="{ width: batch.progress + '%' }"></div>
                </div>
                <p class="text-xs text-center text-gray-600 mt-1">{{ batch.progress }}%</p>
              </td>
              <td class="px-4 py-3 text-center">
                <p class="text-sm font-bold text-gray-900">{{ batch.total_jobs }}</p>
                <p class="text-xs text-gray-500">{{ batch.failed_jobs_count }} failed</p>
              </td>
              <td class="px-4 py-3 text-xs text-gray-600">{{ formatDate(batch.created_at) }}</td>
              <td class="px-4 py-3 text-center">
                <span :class="{
                  'bg-green-100 text-green-800': batch.finished_at && batch.failed_jobs_count === 0,
                  'bg-yellow-100 text-yellow-800': !batch.finished_at,
                  'bg-red-100 text-red-800': batch.failed_jobs_count > 0
                }" class="px-2 py-1 rounded text-xs font-bold">
                  {{ batch.finished_at ? (batch.failed_jobs_count > 0 ? 'FAILED' : 'COMPLETED') : 'RUNNING' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Laravel Log -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 mb-6">
      <div class="p-6 border-b bg-gradient-to-r from-gray-700 to-gray-900 flex justify-between items-center">
        <div>
          <h2 class="text-lg font-bold text-white">Laravel Log</h2>
          <p class="text-gray-300 text-sm mt-1">{{ logSize || 'Monitor application logs' }}</p>
        </div>
        <div class="flex gap-2">
          <button @click="refreshLog" :disabled="loadingLog" class="px-4 py-2 bg-white text-gray-700 rounded-lg font-semibold hover:bg-gray-100 disabled:opacity-50 transition-all text-sm">
            <span v-if="!loadingLog">🔄 Refresh</span>
            <span v-else>...</span>
          </button>
          <button @click="clearLog" class="px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-all text-sm">
            🗑️ Clear Log
          </button>
        </div>
      </div>
      <div class="p-4">
        <pre v-if="logContent" class="bg-gray-900 text-green-300 p-4 rounded-xl text-xs overflow-x-auto max-h-96 font-mono">{{ logContent }}</pre>
        <p v-else class="text-center text-gray-500 py-8">Click refresh to load logs</p>
      </div>
    </div>

    <!-- Failed Jobs -->
    <div v-if="failedJobs.length > 0" class="bg-white rounded-2xl shadow-md border border-gray-100">
      <div class="p-6 border-b bg-gradient-to-r from-red-500 to-rose-600 flex justify-between items-center">
        <div>
          <h2 class="text-lg font-bold text-white">Failed Jobs</h2>
          <p class="text-red-100 text-sm mt-1">Jobs that failed to execute</p>
        </div>
        <button @click="router.reload({ only: ['failedJobs'] })" class="px-4 py-2 bg-white text-red-600 rounded-lg font-semibold hover:bg-red-50 transition-all text-sm">
          🔄 Refresh
        </button>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Queue</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Exception</th>
              <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Failed At</th>
              <th class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="job in failedJobs" :key="job.id" class="hover:bg-gray-50">
              <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ job.queue }}</td>
              <td class="px-4 py-3 text-xs text-gray-600">{{ job.exception.substring(0, 100) }}...</td>
              <td class="px-4 py-3 text-xs text-gray-600">{{ formatDate(job.failed_at) }}</td>
              <td class="px-4 py-3 text-center">
                <button @click="retryFailedJob(job.id)" class="text-purple-600 hover:text-purple-800 font-semibold text-xs">Retry</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Generate Tasks Modal -->
    <div v-if="showGenerateTasksModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="showGenerateTasksModal = false">
      <div class="bg-white rounded-2xl max-w-md w-full">
        <div class="p-6 border-b bg-gradient-to-r from-purple-500 to-pink-600">
          <h3 class="text-xl font-bold text-white">Generate Tasks</h3>
          <p class="text-purple-100 text-sm mt-1">Force task generation</p>
        </div>
        <div class="p-6">
          <label class="flex items-center gap-2 mb-4">
            <input type="checkbox" v-model="forceGenerate" class="rounded">
            <span class="text-sm text-gray-700">Force generation (bypass threshold check)</span>
          </label>
          <div class="flex gap-3">
            <button @click="executeGenerateTasks" :disabled="executing"
                    class="flex-1 bg-gradient-to-br from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 disabled:opacity-50 text-white px-4 py-3 rounded-xl font-semibold">
              Generate
            </button>
            <button @click="showGenerateTasksModal = false"
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-3 rounded-xl font-semibold">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  settings: Object,
  batches: Array,
  failedJobs: Array,
  pendingJobs: Number,
});

const executing = ref(false);
const commandOutput = ref('');
const lastCommand = ref('');
const showGenerateTasksModal = ref(false);
const forceGenerate = ref(false);
const logContent = ref('');
const logSize = ref('');
const loadingLog = ref(false);

const executeCommand = async (command, args = {}) => {
  executing.value = true;
  lastCommand.value = command;
  commandOutput.value = 'Executing...';

  try {
    const response = await fetch('/admin/commands/execute', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ command, arguments: args }),
    });

    const data = await response.json();

    if (data.success) {
      commandOutput.value = data.output;
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: data.message,
        timer: 2000
      });
      router.reload({ only: ['batches', 'failedJobs', 'pendingJobs'] });
    } else {
      commandOutput.value = data.message;
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: data.message
      });
    }
  } catch (error) {
    commandOutput.value = 'Error: ' + error.message;
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: error.message
    });
  } finally {
    executing.value = false;
  }
};

const executeGenerateTasks = () => {
  showGenerateTasksModal.value = false;
  executeCommand('tasks:generate-templates', forceGenerate.value ? { '--force': true } : {});
};

const retryFailedJob = async (id) => {
  try {
    const response = await fetch(`/admin/commands/retry/${id}`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
    });

    const data = await response.json();

    if (data.success) {
      Swal.fire({ icon: 'success', title: 'Job queued for retry', timer: 2000 });
      router.reload({ only: ['failedJobs', 'pendingJobs'] });
    }
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Error', text: error.message });
  }
};

const clearAllFailedJobs = async () => {
  const result = await Swal.fire({
    title: 'Clear all failed jobs?',
    text: 'This action cannot be undone',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Clear All',
    confirmButtonColor: '#ef4444'
  });

  if (!result.isConfirmed) return;

  try {
    const response = await fetch('/admin/commands/clear-failed', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
    });

    const data = await response.json();

    if (data.success) {
      Swal.fire({ icon: 'success', title: 'All failed jobs cleared', timer: 2000 });
      router.reload({ only: ['failedJobs'] });
    }
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Error', text: error.message });
  }
};

const formatDate = (date) => new Date(date * 1000).toLocaleString();

const refreshLog = async () => {
  loadingLog.value = true;
  try {
    const response = await fetch('/admin/commands/laravel-log', {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
    });
    const data = await response.json();
    if (data.success) {
      logContent.value = data.content;
      logSize.value = data.size_readable;
    }
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Error loading log', text: error.message });
  } finally {
    loadingLog.value = false;
  }
};

const clearLog = async () => {
  const result = await Swal.fire({
    title: 'Clear Laravel log?',
    text: 'This will delete all log entries',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Clear',
    confirmButtonColor: '#ef4444'
  });

  if (!result.isConfirmed) return;

  try {
    const response = await fetch('/admin/commands/clear-log', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
    });
    const data = await response.json();
    if (data.success) {
      logContent.value = '';
      logSize.value = '';
      Swal.fire({ icon: 'success', title: 'Log cleared', timer: 2000 });
    }
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Error', text: error.message });
  }
};

const refreshBatches = () => {
  router.reload({ only: ['batches', 'pendingJobs', 'failedJobs'] });
};

const clearAllBatches = async () => {
  const result = await Swal.fire({
    title: 'Clear all batch jobs?',
    text: 'This will delete all batch job records from the database',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Clear All',
    confirmButtonColor: '#ef4444'
  });

  if (!result.isConfirmed) return;

  try {
    const response = await fetch('/admin/commands/clear-batches', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
    });

    const data = await response.json();

    if (data.success) {
      Swal.fire({ icon: 'success', title: 'All batch jobs cleared', timer: 2000 });
      router.reload({ only: ['batches', 'pendingJobs'] });
    }
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Error', text: error.message });
  }
};
</script>
