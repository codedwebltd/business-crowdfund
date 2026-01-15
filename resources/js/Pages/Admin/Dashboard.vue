<template>
  <AdminLayout title="Dashboard" :settings="settings">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg overflow-hidden mb-6">
      <div class="p-6 sm:p-8 text-white">
        <div class="flex items-center gap-4">
          <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </div>
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold">Welcome back, Admin</h1>
            <p class="text-blue-100 text-sm sm:text-base">Here's your platform overview</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
      <!-- Total Users -->
      <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
          <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </div>
          <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">Total</span>
        </div>
        <div class="text-3xl font-bold text-gray-900 mb-1">{{ formatNumber(stats.total_users) }}</div>
        <div class="text-sm text-gray-600">Registered Users</div>
      </div>

      <!-- Active Users -->
      <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
          <div class="p-3 bg-gradient-to-br from-green-500 to-green-600 rounded-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <span class="text-xs font-semibold text-green-600 bg-green-50 px-3 py-1 rounded-full">Active</span>
        </div>
        <div class="text-3xl font-bold text-green-600 mb-1">{{ formatNumber(stats.active_users) }}</div>
        <div class="text-sm text-gray-600">Active Users</div>
      </div>

      <!-- Tasks Completed Today -->
      <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
          <div class="p-3 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
          </div>
          <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-3 py-1 rounded-full">Today</span>
        </div>
        <div class="text-3xl font-bold text-purple-600 mb-1">{{ formatNumber(stats.completed_tasks_today) }}</div>
        <div class="text-sm text-gray-600">Tasks Completed</div>
      </div>

      <!-- Total Earnings Paid -->
      <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between mb-4">
          <div class="p-3 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <span class="text-xs font-semibold text-orange-600 bg-orange-50 px-3 py-1 rounded-full">Paid</span>
        </div>
        <div class="text-3xl font-bold text-orange-600 mb-1">₦{{ formatNumber(stats.total_earnings_paid) }}</div>
        <div class="text-sm text-gray-600">Total Withdrawals</div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Daily Registrations Chart -->
      <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-500 rounded-lg">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">User Registrations</h3>
              <p class="text-xs text-gray-600">Last 7 days</p>
            </div>
          </div>
        </div>
        <div class="p-4 sm:p-6">
          <canvas ref="registrationsChart" class="w-full" style="max-height: 300px;"></canvas>
        </div>
      </div>

      <!-- Daily Tasks Chart -->
      <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-purple-50 to-pink-50">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-purple-500 rounded-lg">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Tasks Completed</h3>
              <p class="text-xs text-gray-600">Last 7 days</p>
            </div>
          </div>
        </div>
        <div class="p-4 sm:p-6">
          <canvas ref="tasksChart" class="w-full" style="max-height: 300px;"></canvas>
        </div>
      </div>
    </div>

    <!-- Second Row of Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Daily Withdrawals Chart -->
      <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-orange-50 to-red-50">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-orange-500 rounded-lg">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">Withdrawals</h3>
              <p class="text-xs text-gray-600">Last 7 days (₦)</p>
            </div>
          </div>
        </div>
        <div class="p-4 sm:p-6">
          <canvas ref="withdrawalsChart" class="w-full" style="max-height: 300px;"></canvas>
        </div>
      </div>

      <!-- User Status Distribution -->
      <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-green-50 to-emerald-50">
          <div class="flex items-center gap-3">
            <div class="p-2 bg-green-500 rounded-lg">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-bold text-gray-900">User Distribution</h3>
              <p class="text-xs text-gray-600">By status</p>
            </div>
          </div>
        </div>
        <div class="p-4 sm:p-6">
          <canvas ref="statusChart" class="w-full" style="max-height: 300px;"></canvas>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Chart, registerables } from 'chart.js';

// Register Chart.js components
Chart.register(...registerables);

const props = defineProps({
  settings: Object,
  stats: Object,
  chartData: Object,
  usersByStatus: Object,
});

const registrationsChart = ref(null);
const tasksChart = ref(null);
const withdrawalsChart = ref(null);
const statusChart = ref(null);

const formatNumber = (num) => {
  return new Intl.NumberFormat('en-US').format(num || 0);
};

const getLast7Days = () => {
  const days = [];
  for (let i = 6; i >= 0; i--) {
    const date = new Date();
    date.setDate(date.getDate() - i);
    days.push(date.toISOString().split('T')[0]);
  }
  return days;
};

const prepareChartData = (dataObject, labels) => {
  return labels.map(label => dataObject[label] || 0);
};

onMounted(() => {
  const last7Days = getLast7Days();
  const dayLabels = last7Days.map(date => {
    const d = new Date(date);
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
  });

  // Registrations Chart
  if (registrationsChart.value) {
    new Chart(registrationsChart.value, {
      type: 'line',
      data: {
        labels: dayLabels,
        datasets: [{
          label: 'New Users',
          data: prepareChartData(props.chartData.daily_registrations, last7Days),
          borderColor: 'rgb(59, 130, 246)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          tension: 0.4,
          fill: true,
          borderWidth: 3,
          pointRadius: 4,
          pointHoverRadius: 6,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 13 },
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { precision: 0 },
            grid: { color: 'rgba(0, 0, 0, 0.05)' }
          },
          x: {
            grid: { display: false }
          }
        }
      }
    });
  }

  // Tasks Chart
  if (tasksChart.value) {
    new Chart(tasksChart.value, {
      type: 'bar',
      data: {
        labels: dayLabels,
        datasets: [{
          label: 'Tasks Completed',
          data: prepareChartData(props.chartData.daily_tasks, last7Days),
          backgroundColor: 'rgba(168, 85, 247, 0.8)',
          borderColor: 'rgb(168, 85, 247)',
          borderWidth: 2,
          borderRadius: 6,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 13 },
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { precision: 0 },
            grid: { color: 'rgba(0, 0, 0, 0.05)' }
          },
          x: {
            grid: { display: false }
          }
        }
      }
    });
  }

  // Withdrawals Chart
  if (withdrawalsChart.value) {
    new Chart(withdrawalsChart.value, {
      type: 'line',
      data: {
        labels: dayLabels,
        datasets: [{
          label: 'Withdrawals (₦)',
          data: prepareChartData(props.chartData.daily_withdrawals, last7Days),
          borderColor: 'rgb(249, 115, 22)',
          backgroundColor: 'rgba(249, 115, 22, 0.1)',
          tension: 0.4,
          fill: true,
          borderWidth: 3,
          pointRadius: 4,
          pointHoverRadius: 6,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 13 },
            callbacks: {
              label: (context) => `₦${formatNumber(context.parsed.y)}`
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: (value) => `₦${formatNumber(value)}`
            },
            grid: { color: 'rgba(0, 0, 0, 0.05)' }
          },
          x: {
            grid: { display: false }
          }
        }
      }
    });
  }

  // Status Distribution Chart
  if (statusChart.value) {
    const statusColors = {
      'ACTIVE': 'rgb(34, 197, 94)',
      'PENDING': 'rgb(251, 191, 36)',
      'UNVERIFIED': 'rgb(239, 68, 68)',
    };

    const statuses = Object.keys(props.usersByStatus);
    const statusData = Object.values(props.usersByStatus);

    new Chart(statusChart.value, {
      type: 'doughnut',
      data: {
        labels: statuses.map(s => s.charAt(0) + s.slice(1).toLowerCase()),
        datasets: [{
          data: statusData,
          backgroundColor: statuses.map(s => statusColors[s] || 'rgb(156, 163, 175)'),
          borderWidth: 3,
          borderColor: '#fff',
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 15,
              font: { size: 12, weight: '600' },
              usePointStyle: true,
              pointStyle: 'circle',
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: { size: 14, weight: 'bold' },
            bodyFont: { size: 13 },
            callbacks: {
              label: (context) => {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((context.parsed / total) * 100).toFixed(1);
                return `${context.label}: ${context.parsed} (${percentage}%)`;
              }
            }
          }
        },
        cutout: '65%',
      }
    });
  }
});
</script>
