<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Mobile Overlay -->
    <Transition name="fade">
      <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>
    </Transition>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed left-0 top-0 h-screen bg-white shadow-2xl z-50 transition-transform duration-300 ease-in-out',
        'w-64 lg:translate-x-0',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-gray-900 to-gray-800">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
              <span class="text-lg font-bold text-white tracking-tight">{{ appAbbreviation }}</span>
            </div>
            <h1 class="text-xl font-bold text-white">{{ settings.app_name }}</h1>
          </div>
          <button @click="sidebarOpen = false" class="lg:hidden text-white hover:text-gray-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <nav class="p-4 space-y-2 overflow-y-auto h-[calc(100vh-88px)] overscroll-contain" style="scrollbar-width: thin; scrollbar-color: #9CA3AF #F3F4F6; -webkit-overflow-scrolling: touch;">
        <!-- Dashboard -->
        <AdminNavLink href="/admin" :active="isActive('/admin', true)">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
          <span>Dashboard</span>
        </AdminNavLink>

        <!-- User Management -->
        <div class="pt-4">
          <button
            @click="toggleSection('users')"
            class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors"
          >
            <span>Users</span>
            <svg :class="['w-4 h-4 transition-transform', openSections.users ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div v-show="openSections.users" class="space-y-1 mt-1">
            <AdminNavLink href="/admin/users" :active="isActive('users')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
              <span>All Users</span>
            </AdminNavLink>
            <AdminNavLink href="/admin/ranks" :active="isActive('ranks')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
              </svg>
              <span>Ranks</span>
            </AdminNavLink>
          </div>
        </div>

        <!-- Tasks -->
        <div class="pt-2">
          <button @click="toggleSection('tasks')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors">
            <span>Tasks</span>
            <svg :class="['w-4 h-4 transition-transform', openSections.tasks ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div v-show="openSections.tasks" class="space-y-1 mt-1">
            <AdminNavLink href="/admin/tasks" :active="isActive('tasks')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
              <span>Task Templates</span>
            </AdminNavLink>
          </div>
        </div>

        <!-- Financial -->
        <div class="pt-2">
          <button @click="toggleSection('financial')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors">
            <span>Financial</span>
            <svg :class="['w-4 h-4 transition-transform', openSections.financial ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div v-show="openSections.financial" class="space-y-1 mt-1">
            <AdminNavLink href="/admin/transactions" :active="isActive('transactions')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Transactions</span>
            </AdminNavLink>
            <AdminNavLink href="/admin/withdrawals" :active="isActive('withdrawals')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              <span>Withdrawals</span>
            </AdminNavLink>
            <AdminNavLink href="/admin/subscriptions" :active="isActive('subscriptions')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
              </svg>
              <span>Plans</span>
            </AdminNavLink>
            <AdminNavLink href="/admin/testimonials" :active="isActive('testimonials')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
              </svg>
              <span>Testimonials</span>
            </AdminNavLink>
            <AdminNavLink href="/admin/liquidity" :active="isActive('liquidity')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
              <span>Liquidity & Earnings</span>
            </AdminNavLink>
          </div>
        </div>

        <!-- Security -->
        <div class="pt-2">
          <button @click="toggleSection('security')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors">
            <span>Security</span>
            <svg :class="['w-4 h-4 transition-transform', openSections.security ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div v-show="openSections.security" class="space-y-1 mt-1">
            <AdminNavLink href="/admin/kyc" :active="isActive('kyc')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
              <span>KYC Verifications</span>
            </AdminNavLink>
            <AdminNavLink href="/admin/fraud-incidents" :active="isActive('fraud-incidents')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
              <span>Fraud Incidents</span>
            </AdminNavLink>
          </div>
        </div>

        <!-- System -->
        <div class="pt-2">
          <button @click="toggleSection('system')" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors">
            <span>System</span>
            <svg :class="['w-4 h-4 transition-transform', openSections.system ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div v-show="openSections.system" class="space-y-1 mt-1">
            <AdminNavLink href="/admin/settings" :active="isActive('settings')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              <span>Global Settings</span>
            </AdminNavLink>
            <AdminNavLink href="/admin/announcements" :active="isActive('announcements')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
              </svg>
              <span>Announcements</span>
            </AdminNavLink>
            <AdminNavLink href="/admin/documentation" :active="isActive('documentation')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
              <span>📚 Documentation</span>
            </AdminNavLink>
            <AdminNavLink href="/admin/commands" :active="isActive('commands')">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <span>⚡ Command Center</span>
            </AdminNavLink>
          </div>
        </div>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="lg:ml-64 min-h-screen flex flex-col">
      <!-- Header -->
      <header class="bg-white shadow-sm sticky top-0 z-30 border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8 py-4">
          <div class="flex justify-between items-center mb-3">
            <div class="flex items-center gap-3">
              <button @click="sidebarOpen = true" class="lg:hidden p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
              </button>
              <h2 class="text-xl sm:text-2xl font-bold text-gray-900">{{ title }}</h2>
            </div>
            <div class="flex items-center gap-2 sm:gap-4">
              <Link href="/dashboard" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="View User Site">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </Link>

              <!-- User Dropdown -->
              <div class="relative">
              <button
                @click="userMenuOpen = !userMenuOpen"
                class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300"
              >
                <img
                  :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&background=1F2937&color=fff`"
                  class="w-8 h-8 sm:w-10 sm:h-10 rounded-full ring-2 ring-gray-200"
                  :alt="userName"
                >
                <span class="hidden sm:block text-sm font-medium text-gray-700">{{ userName }}</span>
                <svg :class="['w-4 h-4 text-gray-600 transition-transform duration-200', userMenuOpen ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>

              <!-- Dropdown Menu -->
              <Transition name="dropdown">
                <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50">
                  <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-900">{{ userName }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ userEmail }}</p>
                  </div>
                  <div class="border-t border-gray-100 py-1">
                    <Link href="/logout" method="post" as="button" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors text-left">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                      </svg>
                      Logout
                    </Link>
                  </div>
                </div>
              </Transition>
              </div>
            </div>
          </div>

          <!-- Breadcrumbs -->
          <Breadcrumbs v-if="breadcrumbs && breadcrumbs.length" :crumbs="breadcrumbs" class="mt-3" />
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <slot />
      </main>

      <!-- Footer -->
      <footer class="bg-white border-t border-gray-200 py-4 px-4 sm:px-6 lg:px-8 mt-auto">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
          <p class="text-sm text-gray-600 text-center sm:text-left">
            &copy; {{ new Date().getFullYear() }} {{ settings.app_name }}. All rights reserved.
          </p>
        </div>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AdminNavLink from '@/Components/Admin/AdminNavLink.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const props = defineProps({
  title: String,
  settings: {
    type: Object,
    default: () => ({ app_name: 'CrowdPower' })
  },
  breadcrumbs: {
    type: Array,
    default: () => []
  }
});

const page = usePage();
const sidebarOpen = ref(false);
const userMenuOpen = ref(false);

const user = computed(() => page.props.auth?.user || page.props.user || {});
const userName = computed(() => user.value.full_name || 'Admin');
const userEmail = computed(() => user.value.email || 'admin@example.com');
const appAbbreviation = computed(() => {
  const name = props.settings.app_name || 'CrowdPower';
  return name.substring(0, 2).toUpperCase();
});

const openSections = reactive({
  users: true,
  security: false,
  tasks: true,
  financial: true,
  system: true,
});

const toggleSection = (section) => {
  openSections[section] = !openSections[section];
};

const isActive = (route, exact = false) => {
  if (exact) {
    return window.location.pathname === route;
  }
  return window.location.pathname.includes(route);
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.dropdown-enter-active {
  transition: all 0.2s ease-out;
}
.dropdown-leave-active {
  transition: all 0.15s ease-in;
}
.dropdown-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}

/* Scrollbar styling */
nav::-webkit-scrollbar {
  width: 8px;
}
nav::-webkit-scrollbar-track {
  background: #F3F4F6;
}
nav::-webkit-scrollbar-thumb {
  background: #9CA3AF;
  border-radius: 4px;
}
nav::-webkit-scrollbar-thumb:hover {
  background: #6B7280;
}
</style>
