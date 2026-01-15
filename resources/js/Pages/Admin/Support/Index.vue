<template>
  <AdminLayout title="Support Tickets" :settings="settings">
    <div class="mb-6">
      <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Support Center</h1>
      <p class="text-gray-600 mt-1">Manage and respond to customer support tickets</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-3 sm:gap-4 mb-6">
      <div
        v-for="(stat, key) in statCards"
        :key="key"
        @click="filterByStatus(key)"
        :class="[
          'bg-white rounded-2xl shadow-md border border-gray-100 p-4 sm:p-5 hover:shadow-lg transition-all cursor-pointer',
          filters.status === key ? 'ring-2 ring-purple-500 ring-offset-2' : ''
        ]"
      >
        <div class="flex items-center justify-between">
          <div>
            <p class="text-xs sm:text-sm font-medium text-gray-600">{{ stat.label }}</p>
            <p :class="['text-2xl sm:text-3xl font-bold mt-1', stat.textColor]">{{ counts[key] || 0 }}</p>
          </div>
          <div :class="['p-3 rounded-xl', stat.bgColor]">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4 mb-6">
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <select v-model="selectedStatus" @change="applyFilters" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
          <option value="">All Status</option>
          <option value="open">Open</option>
          <option value="in_progress">In Progress</option>
          <option value="awaiting_reply">Awaiting Reply</option>
          <option value="resolved">Resolved</option>
          <option value="closed">Closed</option>
        </select>
        <select v-model="selectedCategory" @change="applyFilters" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
          <option value="">All Categories</option>
          <option value="general">General</option>
          <option value="payment">Payment</option>
          <option value="withdrawal">Withdrawal</option>
          <option value="task">Task Issue</option>
          <option value="account">Account</option>
          <option value="technical">Technical</option>
        </select>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search tickets..."
          class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
          @keyup.enter="applyFilters"
        >
        <button @click="applyFilters" class="px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-lg text-sm font-semibold hover:shadow-lg hover:shadow-purple-500/50 transition-all">
          Apply Filter
        </button>
      </div>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
      <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-purple-500 to-pink-600">
        <h2 class="text-lg font-bold text-white">Support Tickets</h2>
        <p class="text-purple-100 text-sm mt-1">{{ tickets.total || tickets.data?.length || 0 }} tickets found</p>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b-2 border-gray-200">
            <tr>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Ticket</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Customer</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Category</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Last Update</th>
              <th class="px-4 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr
              v-for="ticket in tickets.data"
              :key="ticket.id"
              class="hover:bg-purple-50 transition-colors"
            >
              <td class="px-4 py-4">
                <div class="flex items-start gap-3">
                  <div class="relative">
                    <div :class="['p-2 rounded-lg', ticket.has_new_user_reply ? 'bg-gradient-to-br from-red-500 to-red-600' : 'bg-gradient-to-br from-purple-500 to-purple-600']">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                      </svg>
                    </div>
                    <span v-if="ticket.has_new_user_reply" class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
                  </div>
                  <div>
                    <div class="font-mono text-xs text-purple-600 font-bold">#{{ ticket.ticket_number }}</div>
                    <div class="font-semibold text-gray-900 text-sm mt-0.5 line-clamp-1">{{ ticket.subject || 'No Subject' }}</div>
                    <div class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ ticket.first_message || 'No preview' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <div class="font-semibold text-gray-900 text-sm">{{ ticket.user?.full_name || ticket.guest_name || 'Guest' }}</div>
                <div class="text-xs text-gray-500 mt-0.5">{{ ticket.user?.email || ticket.guest_email || 'No email' }}</div>
              </td>
              <td class="px-4 py-4">
                <span :class="getCategoryBadgeClass(ticket.category)" class="px-2.5 py-1 rounded-lg text-xs font-semibold capitalize">
                  {{ ticket.category }}
                </span>
              </td>
              <td class="px-4 py-4">
                <span :class="getStatusBadgeClass(ticket.status)" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border whitespace-nowrap">
                  <span :class="getStatusDotClass(ticket.status)"></span>
                  {{ formatStatus(ticket.status) }}
                </span>
              </td>
              <td class="px-4 py-4">
                <div class="text-sm text-gray-900 font-medium whitespace-nowrap">{{ formatDate(ticket.last_message_at || ticket.created_at) }}</div>
                <div class="text-xs text-gray-500">{{ formatTime(ticket.last_message_at || ticket.created_at) }}</div>
              </td>
              <td class="px-4 py-4">
                <div class="flex items-center justify-end gap-2">
                  <button @click="viewTicket(ticket.id)" class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="View Conversation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </button>
                  <button
                    v-if="ticket.status !== 'resolved' && ticket.status !== 'closed'"
                    @click="quickResolve(ticket.id)"
                    class="p-2 text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg transition-all shadow-sm"
                    title="Mark Resolved"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!tickets.data || tickets.data.length === 0">
              <td colspan="6" class="px-4 py-12 text-center">
                <div class="flex flex-col items-center">
                  <div class="p-4 bg-gray-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                  </div>
                  <p class="text-gray-900 font-semibold">No tickets found</p>
                  <p class="text-gray-500 text-sm mt-1">There are no support tickets matching your criteria.</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="tickets.last_page > 1" class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="text-sm text-gray-600 text-center sm:text-left">
          Showing {{ tickets.from }} to {{ tickets.to }} of {{ tickets.total }} tickets
        </div>
        <div class="flex gap-1 sm:gap-2 flex-wrap justify-center">
          <button
            v-for="link in tickets.links"
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
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
  tickets: Object,
  counts: Object,
  filters: Object,
  settings: Object,
});

const searchQuery = ref(props.filters?.search || '');
const selectedStatus = ref(props.filters?.status || '');
const selectedCategory = ref(props.filters?.category || '');

const statCards = {
  open: { label: 'Open', textColor: 'text-blue-600', bgColor: 'bg-gradient-to-br from-blue-500 to-blue-600', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
  in_progress: { label: 'In Progress', textColor: 'text-yellow-600', bgColor: 'bg-gradient-to-br from-yellow-500 to-yellow-600', icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15' },
  awaiting_reply: { label: 'Awaiting', textColor: 'text-purple-600', bgColor: 'bg-gradient-to-br from-purple-500 to-purple-600', icon: 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
  resolved: { label: 'Resolved', textColor: 'text-green-600', bgColor: 'bg-gradient-to-br from-green-500 to-green-600', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
  closed: { label: 'Closed', textColor: 'text-gray-600', bgColor: 'bg-gradient-to-br from-gray-500 to-gray-600', icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' },
  unread: { label: 'Unread', textColor: 'text-red-600', bgColor: 'bg-gradient-to-br from-red-500 to-red-600', icon: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9' },
};

const applyFilters = () => {
  router.get('/admin/support', {
    status: selectedStatus.value,
    search: searchQuery.value,
    category: selectedCategory.value,
  }, { preserveState: true });
};

const filterByStatus = (status) => {
  selectedStatus.value = status === 'all' ? '' : status;
  applyFilters();
};

const changePage = (url) => {
  if (url) router.visit(url);
};

const viewTicket = (ticketId) => {
  router.visit(`/admin/support/${ticketId}`);
};

const quickResolve = async (ticketId) => {
  if (confirm('Mark this ticket as resolved?')) {
    router.post(`/admin/support/${ticketId}/status`, { status: 'resolved' }, {
      preserveState: true,
      onSuccess: () => router.reload()
    });
  }
};

const formatStatus = (status) => {
  return status?.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) || 'Unknown';
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const formatTime = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
};

const getStatusBadgeClass = (status) => {
  const classes = {
    'open': 'bg-blue-100 text-blue-800 border-blue-200',
    'in_progress': 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'awaiting_reply': 'bg-purple-100 text-purple-800 border-purple-200',
    'resolved': 'bg-green-100 text-green-800 border-green-200',
    'closed': 'bg-gray-100 text-gray-800 border-gray-200',
  };
  return classes[status] || 'bg-gray-100 text-gray-800 border-gray-200';
};

const getStatusDotClass = (status) => {
  const classes = {
    'open': 'w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse',
    'in_progress': 'w-1.5 h-1.5 bg-yellow-500 rounded-full animate-pulse',
    'awaiting_reply': 'w-1.5 h-1.5 bg-purple-500 rounded-full animate-pulse',
    'resolved': 'w-1.5 h-1.5 bg-green-500 rounded-full',
    'closed': 'w-1.5 h-1.5 bg-gray-500 rounded-full',
  };
  return classes[status] || 'w-1.5 h-1.5 bg-gray-500 rounded-full';
};

const getCategoryBadgeClass = (category) => {
  const classes = {
    'general': 'bg-gray-100 text-gray-700',
    'payment': 'bg-emerald-100 text-emerald-700',
    'withdrawal': 'bg-amber-100 text-amber-700',
    'task': 'bg-indigo-100 text-indigo-700',
    'account': 'bg-cyan-100 text-cyan-700',
    'technical': 'bg-rose-100 text-rose-700',
  };
  return classes[category] || 'bg-gray-100 text-gray-700';
};
</script>
