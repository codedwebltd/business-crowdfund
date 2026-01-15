<template>
  <component :is="layoutComponent" :title="'Support Center'" :breadcrumbs="breadcrumbs">
    <div class="max-w-5xl mx-auto">
      <!-- Page Header Card - Professional & Responsive -->
      <div class="bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 mb-6 sm:mb-8 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
          <div class="absolute -top-24 -right-24 w-48 sm:w-64 h-48 sm:h-64 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
          <div class="absolute -bottom-24 -left-24 w-48 sm:w-64 h-48 sm:h-64 bg-purple-600/30 rounded-full blur-3xl"></div>
          <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 20px 20px;"></div>
        </div>

        <div class="relative z-10">
          <!-- Mobile: Stacked Layout -->
          <div class="flex flex-col gap-4 sm:gap-6">
            <!-- Header Content -->
            <div class="flex items-start sm:items-center gap-3 sm:gap-4">
              <!-- Icon Container -->
              <div class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 rounded-xl sm:rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center flex-shrink-0 ring-2 ring-white/20">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
              </div>

              <!-- Title & Description -->
              <div class="flex-1 min-w-0">
                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white leading-tight">Support Center</h1>
                <p class="text-white/80 text-xs sm:text-sm mt-0.5 sm:mt-1">We're here to help you 24/7</p>
              </div>

              <!-- Desktop Button (hidden on mobile) -->
              <button @click="showNewTicketModal = true"
                      class="hidden md:inline-flex items-center gap-2 px-5 py-3 bg-white text-gray-900 rounded-xl font-bold hover:bg-gray-50 hover:scale-105 transition-all shadow-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Ticket
              </button>
            </div>

            <!-- Mobile Info Bar -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none sm:hidden">
              <span class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-white text-xs font-medium">
                <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                Online Support
              </span>
              <span class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-white text-xs font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Fast Response
              </span>
              <span class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-white text-xs font-medium">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Secure
              </span>
            </div>

            <!-- Mobile Button (full width) -->
            <button @click="showNewTicketModal = true"
                    class="md:hidden w-full flex items-center justify-center gap-2 px-4 py-3.5 bg-white text-gray-900 rounded-xl font-bold hover:bg-gray-50 active:scale-[0.98] transition-all shadow-xl">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Create New Ticket
            </button>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 mb-8">
        <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-4 sm:p-5">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div>
              <p class="text-xl sm:text-2xl font-bold text-white">{{ stats.total }}</p>
              <p class="text-gray-400 text-[10px] sm:text-xs">Total</p>
            </div>
          </div>
        </div>
        <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-4 sm:p-5">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-yellow-500/20 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div>
              <p class="text-xl sm:text-2xl font-bold text-white">{{ stats.open }}</p>
              <p class="text-gray-400 text-[10px] sm:text-xs">Open</p>
            </div>
          </div>
        </div>
        <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-4 sm:p-5">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-orange-500/20 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
              </svg>
            </div>
            <div>
              <p class="text-xl sm:text-2xl font-bold text-white">{{ stats.unread }}</p>
              <p class="text-gray-400 text-[10px] sm:text-xs">Unread</p>
            </div>
          </div>
        </div>
        <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-4 sm:p-5">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-green-500/20 flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div>
              <p class="text-xl sm:text-2xl font-bold text-white">{{ stats.resolved }}</p>
              <p class="text-gray-400 text-[10px] sm:text-xs">Resolved</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tickets Card -->
      <div class="bg-white/5 backdrop-blur-xl rounded-3xl border border-white/10 overflow-hidden">
        <!-- Card Header -->
        <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-white/10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <h2 class="text-lg font-bold text-white">Your Tickets</h2>
            <p class="text-gray-400 text-sm hidden sm:block">Track all your conversations</p>
          </div>
          <div class="flex items-center gap-2 bg-white/5 rounded-xl p-1">
            <button v-for="tab in filterTabs" :key="tab.value"
                    @click="activeFilter = tab.value"
                    :class="[
                      'px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition-all',
                      activeFilter === tab.value
                        ? 'bg-orange-500 text-white'
                        : 'text-gray-400 hover:text-white hover:bg-white/10'
                    ]">
              {{ tab.label }}
            </button>
          </div>
        </div>

        <!-- Tickets List -->
        <div v-if="filteredTickets.length > 0" class="divide-y divide-white/10">
          <Link v-for="ticket in filteredTickets" :key="ticket.id"
                :href="`/support/${ticket.id}`"
                class="group flex items-start gap-3 sm:gap-4 p-4 sm:p-6 hover:bg-white/5 transition-all">
            <!-- Status Icon -->
            <div :class="['w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl flex items-center justify-center flex-shrink-0', getStatusBg(ticket.status)]">
              <svg class="w-5 h-5 sm:w-6 sm:h-6" :class="getStatusColor(ticket.status)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
              </svg>
            </div>

            <!-- Info -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-2 mb-1">
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 flex-wrap mb-1">
                    <span class="text-orange-400 text-xs font-mono">#{{ ticket.ticket_number }}</span>
                    <span :class="getStatusClass(ticket.status)" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase">
                      {{ formatStatus(ticket.status) }}
                    </span>
                    <span v-if="ticket.has_new_admin_reply"
                          class="flex items-center gap-1 px-2 py-0.5 bg-orange-500/20 text-orange-400 rounded-full text-[10px] font-bold">
                      <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                      New
                    </span>
                  </div>
                  <h3 class="text-white font-semibold group-hover:text-orange-400 transition-colors truncate text-sm sm:text-base">
                    {{ ticket.subject }}
                  </h3>
                </div>
                <p class="text-gray-500 text-[10px] sm:text-xs flex-shrink-0">{{ formatDate(ticket.created_at) }}</p>
              </div>
              <p class="text-gray-400 text-xs sm:text-sm line-clamp-2">{{ ticket.first_message }}</p>
              <div class="flex items-center gap-3 mt-2 text-[10px] sm:text-xs text-gray-500">
                <span class="flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                  </svg>
                  {{ formatCategory(ticket.category) }}
                </span>
                <span v-if="ticket.rating" class="flex items-center gap-1 text-yellow-400">
                  <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                  </svg>
                  {{ ticket.rating }}/5
                </span>
              </div>
            </div>

            <svg class="w-5 h-5 text-gray-500 group-hover:text-orange-400 flex-shrink-0 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </Link>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12 sm:py-16 px-6">
          <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto rounded-3xl bg-gradient-to-br from-orange-500/20 to-purple-600/20 flex items-center justify-center mb-6">
            <svg class="w-10 h-10 sm:w-12 sm:h-12 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
          </div>
          <h3 class="text-white text-lg sm:text-xl font-bold mb-2">No Tickets Found</h3>
          <p class="text-gray-400 mb-6 text-sm">Need help? Create a new support ticket.</p>
          <button @click="showNewTicketModal = true"
                  class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-orange-500 to-purple-600 text-white rounded-xl font-bold hover:shadow-lg hover:shadow-orange-500/30 hover:scale-105 transition-all text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Ticket
          </button>
        </div>
      </div>

      <!-- New Ticket Modal -->
      <Transition name="modal">
        <div v-if="showNewTicketModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showNewTicketModal = false"></div>
          <div class="relative w-full max-w-lg bg-slate-900 rounded-3xl border border-white/10 shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="bg-gradient-to-r from-orange-500 to-purple-600 px-5 sm:px-6 py-4 sm:py-5 flex items-center justify-between sticky top-0">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-white font-bold text-base sm:text-lg">New Ticket</h3>
                  <p class="text-white/70 text-[10px] sm:text-xs">We respond in minutes</p>
                </div>
              </div>
              <button @click="showNewTicketModal = false" class="text-white/70 hover:text-white transition-colors p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitNewTicket" class="p-5 sm:p-6 space-y-4 sm:space-y-5">
              <template v-if="!isAuthenticated">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-gray-300 text-xs font-semibold mb-2">YOUR NAME</label>
                    <input v-model="form.guest_name" type="text" placeholder="John Doe"
                           class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 text-sm focus:outline-none focus:border-orange-500/50 transition-all" required>
                  </div>
                  <div>
                    <label class="block text-gray-300 text-xs font-semibold mb-2">EMAIL</label>
                    <input v-model="form.guest_email" type="email" placeholder="you@email.com"
                           class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 text-sm focus:outline-none focus:border-orange-500/50 transition-all" required>
                  </div>
                </div>
              </template>

              <div>
                <label class="block text-gray-300 text-xs font-semibold mb-2">SUBJECT</label>
                <input v-model="form.subject" type="text" placeholder="Brief description"
                       class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 text-sm focus:outline-none focus:border-orange-500/50 transition-all" required>
              </div>

              <div>
                <label class="block text-gray-300 text-xs font-semibold mb-2">CATEGORY</label>
                <div class="grid grid-cols-3 gap-2">
                  <button v-for="cat in categories" :key="cat.value" type="button"
                          @click="form.category = cat.value"
                          :class="[
                            'p-2.5 sm:p-3 rounded-xl border text-center transition-all',
                            form.category === cat.value
                              ? 'bg-orange-500/20 border-orange-500/50 text-orange-400'
                              : 'bg-white/5 border-white/10 text-gray-400 hover:border-white/20'
                          ]">
                    <span class="text-base sm:text-lg block">{{ cat.icon }}</span>
                    <span class="text-[10px] sm:text-xs font-medium">{{ cat.label }}</span>
                  </button>
                </div>
              </div>

              <div>
                <label class="block text-gray-300 text-xs font-semibold mb-2">MESSAGE</label>
                <textarea v-model="form.message" rows="4" placeholder="Describe your issue..."
                          class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 text-sm focus:outline-none focus:border-orange-500/50 transition-all resize-none" required></textarea>
              </div>

              <button type="submit" :disabled="isSubmitting"
                      class="w-full py-3.5 sm:py-4 bg-gradient-to-r from-orange-500 to-purple-600 text-white rounded-xl font-bold hover:shadow-lg hover:shadow-orange-500/30 transition-all disabled:opacity-50 flex items-center justify-center gap-2">
                <svg v-if="isSubmitting" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                {{ isSubmitting ? 'Sending...' : 'Submit Ticket' }}
              </button>
            </form>
          </div>
        </div>
      </Transition>
    </div>
  </component>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import axios from 'axios';

const props = defineProps({
  tickets: { type: Array, default: () => [] },
  guestSessionId: String
});

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);
const layoutComponent = computed(() => isAuthenticated.value ? UserLayout : GuestLayout);
const breadcrumbs = computed(() => [{ label: 'Support', url: null }]);

const showNewTicketModal = ref(false);
const isSubmitting = ref(false);
const activeFilter = ref('all');

const filterTabs = [
  { label: 'All', value: 'all' },
  { label: 'Open', value: 'open' },
  { label: 'Closed', value: 'closed' },
];

const categories = [
  { value: 'general', label: 'General', icon: '💬' },
  { value: 'payment', label: 'Payment', icon: '💳' },
  { value: 'withdrawal', label: 'Withdraw', icon: '💰' },
  { value: 'task', label: 'Task', icon: '📋' },
  { value: 'account', label: 'Account', icon: '👤' },
  { value: 'technical', label: 'Tech', icon: '🔧' },
];

const form = reactive({
  subject: '',
  category: 'general',
  message: '',
  guest_name: '',
  guest_email: ''
});

const stats = computed(() => {
  const t = props.tickets || [];
  return {
    total: t.length,
    open: t.filter(x => ['open', 'in_progress', 'awaiting_reply'].includes(x.status)).length,
    unread: t.filter(x => x.has_new_admin_reply).length,
    resolved: t.filter(x => ['resolved', 'closed'].includes(x.status)).length,
  };
});

const filteredTickets = computed(() => {
  if (activeFilter.value === 'all') return props.tickets;
  if (activeFilter.value === 'open') {
    return props.tickets.filter(t => ['open', 'in_progress', 'awaiting_reply'].includes(t.status));
  }
  return props.tickets.filter(t => ['closed', 'resolved'].includes(t.status));
});

async function submitNewTicket() {
  if (isSubmitting.value) return;
  isSubmitting.value = true;
  try {
    const payload = { subject: form.subject, category: form.category, message: form.message };
    if (!isAuthenticated.value) {
      payload.guest_name = form.guest_name;
      payload.guest_email = form.guest_email;
    }
    const response = await axios.post('/api/support/ticket', payload);
    showNewTicketModal.value = false;
    router.visit(`/support/${response.data.ticket_id}`);
  } catch (error) {
    alert(error.response?.data?.message || 'Failed to create ticket.');
  } finally {
    isSubmitting.value = false;
  }
}

function getStatusClass(status) {
  return {
    'open': 'bg-blue-500/20 text-blue-400',
    'in_progress': 'bg-yellow-500/20 text-yellow-400',
    'awaiting_reply': 'bg-purple-500/20 text-purple-400',
    'resolved': 'bg-green-500/20 text-green-400',
    'closed': 'bg-gray-500/20 text-gray-400'
  }[status] || 'bg-gray-500/20 text-gray-400';
}

function getStatusBg(status) {
  return {
    'open': 'bg-blue-500/20',
    'in_progress': 'bg-yellow-500/20',
    'awaiting_reply': 'bg-purple-500/20',
    'resolved': 'bg-green-500/20',
    'closed': 'bg-gray-500/20'
  }[status] || 'bg-gray-500/20';
}

function getStatusColor(status) {
  return {
    'open': 'text-blue-400',
    'in_progress': 'text-yellow-400',
    'awaiting_reply': 'text-purple-400',
    'resolved': 'text-green-400',
    'closed': 'text-gray-400'
  }[status] || 'text-gray-400';
}

function formatStatus(status) { return status.replace(/_/g, ' '); }
function formatCategory(cat) { return cat.charAt(0).toUpperCase() + cat.slice(1).replace(/_/g, ' '); }
function formatDate(d) {
  if (!d) return '';
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: all 0.3s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-enter-from > div:last-child, .modal-leave-to > div:last-child { transform: scale(0.95) translateY(20px); }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.scrollbar-none::-webkit-scrollbar { display: none; }
.scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
</style>
