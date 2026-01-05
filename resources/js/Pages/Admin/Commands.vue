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
          <button @click="openGitModal" :disabled="executing"
                  class="bg-gradient-to-br from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 disabled:opacity-50 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all text-sm">
            📤 Git Push
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

    <!-- Git Push Modal -->
    <div v-if="showGitPushModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="closeGitModal">
      <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b bg-gradient-to-r from-emerald-500 to-teal-600 sticky top-0">
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-xl font-bold text-white">📤 Git Push</h3>
              <p class="text-emerald-100 text-sm mt-1">Commit and push changes to repository</p>
            </div>
            <button @click="closeGitModal" class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6">
          <!-- Git Info Tabs - Always Visible -->
          <div class="mb-4">
            <!-- Tab Navigation -->
            <div class="flex gap-2 mb-3 border-b border-gray-200">
              <button
                @click="activeGitTab = 'info'"
                :class="activeGitTab === 'info' ? 'border-b-2 border-emerald-500 text-emerald-600' : 'text-gray-600'"
                class="px-3 py-2 text-sm font-semibold hover:text-emerald-600 transition-colors"
              >
                📋 Info
              </button>
              <button
                @click="loadRecentCommits"
                :class="activeGitTab === 'commits' ? 'border-b-2 border-emerald-500 text-emerald-600' : 'text-gray-600'"
                class="px-3 py-2 text-sm font-semibold hover:text-emerald-600 transition-colors"
              >
                📝 Recent Commits
              </button>
              <button
                @click="loadGitStatus"
                :class="activeGitTab === 'status' ? 'border-b-2 border-emerald-500 text-emerald-600' : 'text-gray-600'"
                class="px-3 py-2 text-sm font-semibold hover:text-emerald-600 transition-colors"
              >
                📊 Status
              </button>
            </div>

            <!-- Repository Info Tab -->
            <div v-if="activeGitTab === 'info'" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
              <div v-if="gitRepoInfo" class="space-y-2 text-xs">
                <div class="flex justify-between">
                  <strong>Branch:</strong>
                  <span class="text-emerald-600 font-mono">{{ gitRepoInfo.branch || 'main' }}</span>
                </div>
                <div class="flex justify-between items-start">
                  <strong>Remote URL:</strong>
                  <span class="text-blue-600 font-mono break-all text-right ml-2">{{ gitRepoInfo.url || 'Not configured' }}</span>
                </div>
                <div v-if="gitRepoInfo.lastCommit" class="flex justify-between">
                  <strong>Last Commit:</strong>
                  <span class="text-gray-600 font-mono">{{ gitRepoInfo.lastCommit }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <strong>Uncommitted Changes:</strong>
                  <button
                    v-if="gitRepoInfo.uncommittedChanges > 0"
                    @click="showUncommittedChanges = !showUncommittedChanges"
                    :class="gitRepoInfo.uncommittedChanges > 0 ? 'text-orange-600 hover:text-orange-700' : 'text-green-600'"
                    class="font-bold underline cursor-pointer flex items-center gap-1"
                  >
                    {{ gitRepoInfo.uncommittedChanges }} {{ gitRepoInfo.uncommittedChanges === 1 ? 'file' : 'files' }}
                    <svg :class="showUncommittedChanges ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                  </button>
                  <span v-else class="text-green-600 font-bold">
                    {{ gitRepoInfo.uncommittedChanges }} files
                  </span>
                </div>
                <!-- Uncommitted Changes List -->
                <div v-if="showUncommittedChanges && gitRepoInfo.uncommittedChanges > 0" class="mt-2 pt-2 border-t border-gray-200">
                  <div v-if="loadingUncommittedFiles" class="text-center py-2 text-gray-500 text-xs">
                    Loading files...
                  </div>
                  <div v-else-if="uncommittedFiles" class="max-h-48 overflow-y-auto bg-white rounded border border-gray-300 p-2">
                    <div v-for="(file, index) in uncommittedFiles" :key="index" class="text-xs font-mono py-1 px-2 hover:bg-gray-50 rounded flex items-start gap-2">
                      <span :class="getFileStatusColor(file.status)" class="font-bold">{{ file.status }}</span>
                      <span class="text-gray-700 break-all flex-1">{{ file.path }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-4 text-gray-500 text-sm">
                Loading repository info...
              </div>
            </div>

            <!-- Recent Commits Tab -->
            <div v-if="activeGitTab === 'commits'" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
              <div v-if="loadingCommits" class="text-center py-4 text-gray-500 text-sm">
                Loading commits...
              </div>
              <div v-else-if="recentCommits" class="max-h-48 overflow-y-auto">
                <pre class="text-xs font-mono text-gray-700 whitespace-pre-wrap">{{ recentCommits }}</pre>
              </div>
              <div v-else class="text-center py-4 text-gray-500 text-sm">
                Click "Recent Commits" tab to load
              </div>
            </div>

            <!-- Git Status Tab -->
            <div v-if="activeGitTab === 'status'" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
              <div v-if="loadingStatus" class="text-center py-4 text-gray-500 text-sm">
                Loading status...
              </div>
              <div v-else-if="gitStatusOutput" class="max-h-48 overflow-y-auto">
                <pre class="text-xs font-mono text-gray-700 whitespace-pre-wrap">{{ gitStatusOutput }}</pre>
              </div>
              <div v-else class="text-center py-4 text-gray-500 text-sm">
                Click "Status" tab to load
              </div>
            </div>
          </div>

          <!-- Commit Message Input -->
          <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
              <label class="block text-sm font-semibold text-gray-700">
                Commit Message <span class="text-red-500">*</span>
              </label>
              <button
                @click="generateCommitMessage"
                :disabled="gitExecuting || generatingCommit"
                class="flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-xs font-medium rounded-lg hover:from-purple-600 hover:to-pink-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg v-if="generatingCommit" class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span>{{ generatingCommit ? 'Generating...' : 'Generate with AI' }}</span>
              </button>
            </div>
            <textarea
              v-model="commitMessage"
              rows="3"
              placeholder="e.g., Add crypto withdrawal tracking feature"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
              :disabled="gitExecuting"
            ></textarea>
          </div>

          <!-- Commit Message Guidelines -->
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <h4 class="text-sm font-bold text-blue-900 mb-2">💡 Good Commit Message Guidelines:</h4>
            <ul class="text-xs text-blue-800 space-y-1">
              <li>✓ <strong>Be specific:</strong> "Add user authentication" not "Update files"</li>
              <li>✓ <strong>Use present tense:</strong> "Add feature" not "Added feature"</li>
              <li>✓ <strong>Keep it concise:</strong> 50-72 characters is ideal</li>
              <li>✓ <strong>Start with a verb:</strong> Add, Fix, Update, Remove, Refactor</li>
            </ul>
            <div class="mt-3 pt-3 border-t border-blue-200">
              <p class="text-xs font-semibold text-blue-900 mb-1">Examples:</p>
              <p class="text-xs text-blue-700">• Add withdrawal crypto value tracking</p>
              <p class="text-xs text-blue-700">• Fix payment gateway timeout issue</p>
              <p class="text-xs text-blue-700">• Update user dashboard UI</p>
            </div>
          </div>

          <!-- Database Backup Option -->
          <label class="flex items-center gap-3 mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
            <input type="checkbox" v-model="backupDatabase" class="rounded w-5 h-5 text-emerald-600" :disabled="gitExecuting">
            <div class="flex-1">
              <span class="text-sm font-semibold text-gray-900">Backup database before push</span>
              <p class="text-xs text-gray-600 mt-0.5">Recommended for safety</p>
            </div>
          </label>

          <!-- Real-time Output -->
          <div v-if="gitOutput" class="mb-4">
            <h4 class="text-sm font-semibold text-gray-700 mb-2">Output:</h4>
            <div class="bg-gray-900 rounded-lg p-4 max-h-64 overflow-y-auto">
              <pre class="text-green-300 text-xs font-mono whitespace-pre-wrap">{{ gitOutput }}</pre>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3">
            <button
              @click="executeGitPush"
              :disabled="gitExecuting || !commitMessage.trim()"
              class="flex-1 bg-gradient-to-br from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all flex items-center justify-center gap-2"
            >
              <svg v-if="gitExecuting" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ gitExecuting ? 'Pushing...' : '📤 Push to Git' }}</span>
            </button>
            <button
              @click="closeGitModal"
              :disabled="gitExecuting"
              class="flex-1 bg-gray-200 hover:bg-gray-300 disabled:opacity-50 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-all"
            >
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
const showGitPushModal = ref(false);
const commitMessage = ref('');
const backupDatabase = ref(true);
const gitOutput = ref('');
const gitExecuting = ref(false);
const gitRepoInfo = ref(null);
const activeGitTab = ref('info');
const recentCommits = ref(null);
const gitStatusOutput = ref(null);
const loadingCommits = ref(false);
const loadingStatus = ref(false);
const showUncommittedChanges = ref(false);
const uncommittedFiles = ref(null);
const loadingUncommittedFiles = ref(false);
const generatingCommit = ref(false);

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

const openGitModal = async () => {
  showGitPushModal.value = true;

  // Fetch git repository info
  try {
    const response = await fetch('/admin/git/info', {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
    });
    const data = await response.json();
    if (data.success) {
      gitRepoInfo.value = data.info;

      // Auto-load uncommitted files if there are changes
      if (data.info.uncommittedChanges > 0) {
        loadUncommittedFiles();
      }
    }
  } catch (error) {
    console.error('Failed to fetch git info:', error);
  }
};

const loadUncommittedFiles = async () => {
  if (uncommittedFiles.value) return; // Already loaded

  loadingUncommittedFiles.value = true;
  try {
    const response = await fetch('/admin/git/uncommitted', {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
    });
    const data = await response.json();
    if (data.success) {
      uncommittedFiles.value = data.files;
    }
  } catch (error) {
    console.error('Failed to fetch uncommitted files:', error);
    uncommittedFiles.value = [];
  } finally {
    loadingUncommittedFiles.value = false;
  }
};

const getFileStatusColor = (status) => {
  const colors = {
    'M': 'text-blue-600',    // Modified
    'A': 'text-green-600',   // Added
    'D': 'text-red-600',     // Deleted
    'R': 'text-purple-600',  // Renamed
    'C': 'text-cyan-600',    // Copied
    '??': 'text-orange-600', // Untracked
  };
  return colors[status] || 'text-gray-600';
};

const closeGitModal = () => {
  if (!gitExecuting.value) {
    showGitPushModal.value = false;
    commitMessage.value = '';
    gitOutput.value = '';
    gitRepoInfo.value = null;
    activeGitTab.value = 'info';
    recentCommits.value = null;
    gitStatusOutput.value = null;
    showUncommittedChanges.value = false;
    uncommittedFiles.value = null;
  }
};

const loadRecentCommits = async () => {
  activeGitTab.value = 'commits';

  if (recentCommits.value) return; // Already loaded

  loadingCommits.value = true;
  try {
    const response = await fetch('/admin/git/log', {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
    });
    const data = await response.json();
    if (data.success) {
      recentCommits.value = data.output;
    }
  } catch (error) {
    console.error('Failed to fetch commits:', error);
    recentCommits.value = 'Error loading commits';
  } finally {
    loadingCommits.value = false;
  }
};

const loadGitStatus = async () => {
  activeGitTab.value = 'status';

  if (gitStatusOutput.value) return; // Already loaded

  loadingStatus.value = true;
  try {
    const response = await fetch('/admin/git/status', {
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
    });
    const data = await response.json();
    if (data.success) {
      gitStatusOutput.value = data.output;
    }
  } catch (error) {
    console.error('Failed to fetch status:', error);
    gitStatusOutput.value = 'Error loading status';
  } finally {
    loadingStatus.value = false;
  }
};

const generateCommitMessage = async () => {
  generatingCommit.value = true;

  try {
    const response = await fetch('/admin/git/generate-commit', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
    });

    const data = await response.json();

    if (data.success) {
      commitMessage.value = data.message;
      Swal.fire({
        icon: 'success',
        title: 'AI Generated!',
        text: 'You can edit the message before pushing.',
        timer: 2000,
        showConfirmButton: false
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Generation Failed',
        text: data.message || 'Could not generate commit message'
      });
    }
  } catch (error) {
    console.error('Generate commit error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to generate commit message'
    });
  } finally {
    generatingCommit.value = false;
  }
};

const executeGitPush = async () => {
  if (!commitMessage.value.trim()) {
    Swal.fire({ icon: 'warning', title: 'Commit message required', text: 'Please enter a commit message' });
    return;
  }

  gitExecuting.value = true;
  gitOutput.value = '🔄 Starting git push process...\n';

  try {
    const response = await fetch('/admin/git/push', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({
        commit_message: commitMessage.value,
        backup_database: backupDatabase.value,
      }),
    });

    const data = await response.json();

    if (data.success) {
      gitOutput.value = data.output;
      Swal.fire({
        icon: 'success',
        title: 'Git Push Successful!',
        html: `<div class="text-left">
          <p class="mb-2"><strong>Commit:</strong> ${data.commit_message}</p>
          <p class="mb-2"><strong>Files Changed:</strong> ${data.files_changed || 'N/A'}</p>
          ${data.backup_file ? `<p><strong>Backup:</strong> ${data.backup_file}</p>` : ''}
        </div>`,
        confirmButtonColor: '#10b981'
      }).then(() => {
        showGitPushModal.value = false;
        commitMessage.value = '';
        gitOutput.value = '';
      });
    } else {
      gitOutput.value += '\n\n❌ Error: ' + data.message;
      Swal.fire({
        icon: 'error',
        title: 'Git Push Failed',
        text: data.message
      });
    }
  } catch (error) {
    gitOutput.value += '\n\n❌ Error: ' + error.message;
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: error.message
    });
  } finally {
    gitExecuting.value = false;
  }
};
</script>
