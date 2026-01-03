<template>
  <UserLayout>
    <div class="space-y-6">
      <!-- Header Card -->
      <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-6">
          <div class="flex items-center gap-3 mb-4">
            <svg class="w-8 h-8 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <div>
              <h1 class="text-2xl md:text-3xl font-bold text-white">Notifications</h1>
              <p class="text-white/80 text-sm">Stay updated with your latest activities</p>
            </div>
          </div>

          <div class="flex flex-wrap gap-2">
            <button
              v-if="unreadCount > 0"
              @click="markAllAsRead"
              class="flex-1 sm:flex-none px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-semibold rounded-lg transition-all hover:scale-105"
            >
              Mark all read
            </button>
            <button
              v-if="notifications.length > 0"
              @click="confirmDeleteAll"
              class="flex-1 sm:flex-none px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 text-sm font-semibold rounded-lg transition-all hover:scale-105"
            >
              Delete all
            </button>
          </div>
        </div>

        <!-- Stats -->
        <div class="p-6 bg-gradient-to-br from-purple-500/10 via-pink-600/10 to-transparent">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white/5 rounded-xl p-4 border border-white/10">
              <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Total</p>
              <p class="text-2xl font-bold text-white">{{ notifications.length }}</p>
            </div>
            <div class="bg-white/5 rounded-xl p-4 border border-white/10">
              <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Unread</p>
              <p class="text-2xl font-bold text-pink-400">{{ unreadCount }}</p>
            </div>
            <div class="bg-white/5 rounded-xl p-4 border border-white/10">
              <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">Today</p>
              <p class="text-2xl font-bold text-purple-400">{{ todayCount }}</p>
            </div>
            <div class="bg-white/5 rounded-xl p-4 border border-white/10">
              <p class="text-gray-400 text-xs uppercase tracking-wide mb-1">This Week</p>
              <p class="text-2xl font-bold text-blue-400">{{ weekCount }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="bg-white/5 backdrop-blur-xl rounded-xl border border-white/10 p-2">
        <div class="flex gap-2 overflow-x-auto">
          <button
            v-for="filter in filters"
            :key="filter.value"
            @click="activeFilter = filter.value"
            class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition-all"
            :class="activeFilter === filter.value
              ? 'bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg'
              : 'text-gray-400 hover:text-white hover:bg-white/10'"
          >
            {{ filter.label }}
            <span v-if="filter.count > 0" class="ml-2 px-2 py-0.5 bg-white/20 rounded-full text-xs">
              {{ filter.count }}
            </span>
          </button>
        </div>
      </div>

      <!-- Notifications List -->
      <div class="space-y-3">
        <TransitionGroup name="list">
          <div
            v-for="notification in filteredNotifications"
            :key="notification.id"
            class="bg-white/5 backdrop-blur-xl rounded-xl border transition-all hover:border-purple-500/50 hover:bg-white/10 cursor-pointer overflow-hidden"
            :class="notification.read_at ? 'border-white/10' : 'border-pink-500/30 bg-pink-500/5'"
            @click="openModal(notification)"
          >
            <div class="p-4 md:p-5 flex gap-4">
              <!-- Icon -->
              <div class="flex-shrink-0">
                <div
                  class="w-12 h-12 rounded-xl flex items-center justify-center"
                  :class="getIconBackground(notification.type)"
                >
                  <component :is="getIcon(notification.type)" class="w-6 h-6 text-white" />
                </div>
              </div>

              <!-- Content -->
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3 mb-2">
                  <h3 class="font-semibold text-white text-sm md:text-base">
                    {{ notification.title }}
                  </h3>
                  <span class="text-xs text-gray-500 whitespace-nowrap">
                    {{ formatTime(notification.created_at) }}
                  </span>
                </div>
                <p class="text-gray-400 text-sm mb-3">{{ notification.message }}</p>

                <!-- Action Button -->
                <Link
                  v-if="notification.action_url"
                  :href="notification.action_url"
                  class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white text-sm font-semibold rounded-lg transition-all hover:scale-105"
                >
                  {{ notification.action_label || 'View Details' }}
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                  </svg>
                </Link>
              </div>

              <!-- Unread Indicator -->
              <div v-if="!notification.read_at" class="flex-shrink-0">
                <div class="w-3 h-3 bg-pink-500 rounded-full animate-pulse"></div>
              </div>
            </div>
          </div>
        </TransitionGroup>

        <!-- Empty State -->
        <div v-if="filteredNotifications.length === 0" class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 p-12 text-center">
          <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-purple-500/20 to-pink-500/20 flex items-center justify-center">
            <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
          </div>
          <h3 class="text-xl font-bold text-white mb-2">No Notifications</h3>
          <p class="text-gray-400">You're all caught up! Check back later for updates.</p>
        </div>
      </div>

      <!-- Load More -->
      <div v-if="hasMore" class="text-center">
        <button
          @click="loadMore"
          :disabled="loading"
          class="px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-purple-500/50 text-white font-semibold rounded-xl transition-all disabled:opacity-50"
        >
          <span v-if="!loading">Load More</span>
          <span v-else class="flex items-center gap-2">
            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading...
          </span>
        </button>
      </div>
    </div>

    <!-- Notification Detail Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="selectedNotification" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click="closeModal">
          <!-- Backdrop -->
          <div class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>

          <!-- Modal -->
          <div
            class="relative bg-gradient-to-b from-purple-900/95 to-purple-950/95 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
            @click.stop
          >
            <!-- Header -->
            <div class="sticky top-0 bg-gradient-to-r from-purple-500 to-pink-600 p-6 flex items-start justify-between gap-4 border-b border-white/10">
              <div class="flex items-start gap-4 flex-1 min-w-0">
                <div :class="getIconBackground(selectedNotification.type)" class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0">
                  <component :is="getIcon(selectedNotification.type)" class="w-6 h-6 text-white" />
                </div>
                <div class="flex-1 min-w-0">
                  <h2 class="text-xl md:text-2xl font-bold text-white mb-1">{{ selectedNotification.title }}</h2>
                  <p class="text-white/70 text-sm">{{ formatTime(selectedNotification.created_at) }}</p>
                </div>
              </div>
              <button @click="closeModal" class="flex-shrink-0 p-2 hover:bg-white/10 rounded-lg transition-colors">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!-- Scrollable Content -->
            <div class="overflow-y-auto max-h-[60vh] p-6 space-y-4">
              <div class="prose prose-invert max-w-none">
                <p class="text-gray-300 text-base leading-relaxed whitespace-pre-wrap">{{ selectedNotification.message }}</p>

                <!-- Additional Data -->
                <div v-if="selectedNotification.data" class="mt-6 p-4 bg-white/5 rounded-xl border border-white/10">
                  <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-3">Details</h3>
                  <dl class="space-y-2">
                    <div v-for="(value, key) in selectedNotification.data" :key="key" class="flex justify-between">
                      <dt class="text-gray-400 text-sm capitalize">{{ key.replace(/_/g, ' ') }}:</dt>
                      <dd class="text-white text-sm font-semibold">{{ value }}</dd>
                    </div>
                  </dl>
                </div>
              </div>
            </div>

            <!-- Footer Actions -->
            <div class="sticky bottom-0 bg-purple-900/50 backdrop-blur-xl p-4 border-t border-white/10 flex gap-3">
              <Link
                v-if="selectedNotification.action_url"
                :href="selectedNotification.action_url"
                class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-semibold rounded-xl transition-all hover:scale-105 text-center"
              >
                {{ selectedNotification.action_label || 'View Details' }}
              </Link>
              <button
                @click="deleteNotification(selectedNotification.id)"
                class="px-6 py-3 bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 text-red-300 font-semibold rounded-xl transition-all"
              >
                Delete
              </button>
              <button
                v-if="!selectedNotification.read_at"
                @click="markAsRead(selectedNotification.id)"
                class="px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/10 text-white font-semibold rounded-xl transition-all"
              >
                Mark as Read
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Delete All Confirmation Modal -->
    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showDeleteConfirm" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click="showDeleteConfirm = false">
          <div class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
          <div class="relative bg-gradient-to-b from-red-900/95 to-red-950/95 backdrop-blur-xl rounded-2xl border border-red-500/30 shadow-2xl max-w-md w-full p-6" @click.stop>
            <div class="text-center">
              <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-500/20 flex items-center justify-center">
                <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
              </div>
              <h3 class="text-xl font-bold text-white mb-2">Delete All Notifications?</h3>
              <p class="text-gray-300 mb-6">This action cannot be undone. All {{ notifications.length }} notifications will be permanently deleted.</p>
              <div class="flex gap-3">
                <button @click="showDeleteConfirm = false" class="flex-1 px-4 py-3 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl transition-all">
                  Cancel
                </button>
                <button @click="deleteAll" class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition-all">
                  Delete All
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </UserLayout>
</template>

<script setup>
import { ref, computed, h } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

const props = defineProps({
  notifications: Array,
  hasMore: Boolean,
});

const activeFilter = ref('all');
const loading = ref(false);
const selectedNotification = ref(null);
const showDeleteConfirm = ref(false);

const unreadCount = computed(() =>
  props.notifications.filter(n => !n.read_at).length
);

const todayCount = computed(() => {
  const today = new Date().toDateString();
  return props.notifications.filter(n =>
    new Date(n.created_at).toDateString() === today
  ).length;
});

const weekCount = computed(() => {
  const weekAgo = new Date();
  weekAgo.setDate(weekAgo.getDate() - 7);
  return props.notifications.filter(n =>
    new Date(n.created_at) >= weekAgo
  ).length;
});

const filters = computed(() => [
  { label: 'All', value: 'all', count: props.notifications.length },
  { label: 'Unread', value: 'unread', count: unreadCount.value },
  {
    label: 'Tasks',
    value: 'tasks',
    count: props.notifications.filter(n =>
      n.type === 'task_completed' ||
      n.type === 'task_assigned' ||
      n.type === 'tasks_assigned'
    ).length
  },
  {
    label: 'Earnings',
    value: 'earnings',
    count: props.notifications.filter(n =>
      n.type === 'team_commission' ||
      n.type === 'referral_bonus' ||
      n.type === 'task_completed' ||
      n.type.includes('earning') ||
      n.type.includes('commission') ||
      n.type.includes('bonus')
    ).length
  },
  {
    label: 'System',
    value: 'system',
    count: props.notifications.filter(n =>
      n.type.includes('system') ||
      n.type.includes('security') ||
      n.type.includes('kyc') ||
      n.type.includes('withdrawal') ||
      n.type.includes('account')
    ).length
  },
]);

const filteredNotifications = computed(() => {
  if (activeFilter.value === 'all') return props.notifications;
  if (activeFilter.value === 'unread') return props.notifications.filter(n => !n.read_at);

  if (activeFilter.value === 'tasks') return props.notifications.filter(n =>
    n.type === 'task_completed' ||
    n.type === 'task_assigned' ||
    n.type === 'tasks_assigned'
  );

  if (activeFilter.value === 'earnings') return props.notifications.filter(n =>
    n.type === 'team_commission' ||
    n.type === 'referral_bonus' ||
    n.type === 'task_completed' ||
    n.type.includes('earning') ||
    n.type.includes('commission') ||
    n.type.includes('bonus')
  );

  if (activeFilter.value === 'system') return props.notifications.filter(n =>
    n.type.includes('system') ||
    n.type.includes('security') ||
    n.type.includes('kyc') ||
    n.type.includes('withdrawal') ||
    n.type.includes('account')
  );

  return props.notifications.filter(n => n.type === activeFilter.value);
});

const getIconBackground = (type) => {
  const backgrounds = {
    'tasks_assigned': 'bg-gradient-to-br from-blue-500 to-cyan-500',
    'task_completed': 'bg-gradient-to-br from-green-500 to-emerald-500',
    'earning_matured': 'bg-gradient-to-br from-yellow-500 to-orange-500',
    'commission_earned': 'bg-gradient-to-br from-purple-500 to-pink-500',
    'withdrawal_approved': 'bg-gradient-to-br from-green-500 to-teal-500',
    'withdrawal_rejected': 'bg-gradient-to-br from-red-500 to-rose-500',
    'rank_upgraded': 'bg-gradient-to-br from-amber-500 to-yellow-500',
    'referral_joined': 'bg-gradient-to-br from-indigo-500 to-purple-500',
    'system_announcement': 'bg-gradient-to-br from-gray-500 to-slate-500',
  };
  return backgrounds[type] || 'bg-gradient-to-br from-purple-500 to-pink-500';
};

const getIcon = (type) => {
  const icons = {
    'tasks_assigned': h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' },
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' })
    ),
    'earning_matured': h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' },
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
    ),
    'commission_earned': h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' },
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z' })
    ),
    'rank_upgraded': h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' },
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z' })
    ),
  };
  return icons[type] || h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' },
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9' })
  );
};

const formatTime = (timestamp) => {
  const date = new Date(timestamp);
  const now = new Date();
  const diff = now - date;
  const minutes = Math.floor(diff / 60000);
  const hours = Math.floor(diff / 3600000);
  const days = Math.floor(diff / 86400000);

  if (minutes < 1) return 'Just now';
  if (minutes < 60) return `${minutes}m ago`;
  if (hours < 24) return `${hours}h ago`;
  if (days < 7) return `${days}d ago`;
  return date.toLocaleDateString();
};

const openModal = (notification) => {
  selectedNotification.value = notification;
  if (!notification.read_at) {
    markAsRead(notification.id);
  }
};

const closeModal = () => {
  selectedNotification.value = null;
};

const markAsRead = (notificationId) => {
  router.post(`/notifications/${notificationId}/read`, {}, {
    preserveScroll: true,
    onSuccess: () => {
      if (selectedNotification.value?.id === notificationId) {
        selectedNotification.value.read_at = new Date().toISOString();
      }
    }
  });
};

const markAllAsRead = () => {
  router.post('/notifications/mark-all-read', {}, {
    preserveScroll: true,
  });
};

const deleteNotification = (notificationId) => {
  router.delete(`/notifications/${notificationId}`, {
    preserveScroll: true,
    onSuccess: () => {
      closeModal();
    }
  });
};

const confirmDeleteAll = () => {
  showDeleteConfirm.value = true;
};

const deleteAll = () => {
  router.post('/notifications/delete-all', {}, {
    preserveScroll: true,
    onSuccess: () => {
      showDeleteConfirm.value = false;
      // Force full page reload
      window.location.reload();
    }
  });
};

const loadMore = () => {
  loading.value = true;
  // Implement pagination logic
};
</script>

<style scoped>
.list-enter-active,
.list-leave-active {
  transition: all 0.3s ease;
}
.list-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}
.list-leave-to {
  opacity: 0;
  transform: translateX(20px);
}

.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.modal-enter-from > div,
.modal-leave-to > div {
  transform: scale(0.9) translateY(20px);
}
</style>
