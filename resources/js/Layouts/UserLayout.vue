<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 relative overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-orange-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
      <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
      <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
    </div>

    <!-- Mobile Overlay -->
    <Transition name="fade">
      <div v-if="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>
    </Transition>

    <!-- Sidebar -->
    <aside :class="[
        'fixed left-0 top-0 h-screen bg-white/10 backdrop-blur-xl border-r border-white/20 shadow-2xl z-50 transition-transform duration-300 ease-in-out',
        'w-64 lg:translate-x-0',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]">
      <!-- Logo Header -->
      <div class="p-6 border-b border-white/20 bg-gradient-to-r from-orange-500/20 to-purple-600/20">
        <div class="flex items-center justify-between">
          <Link href="/" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
            <div v-if="siteLogo" class="h-10">
              <img :src="siteLogo" :alt="appName" class="h-full w-auto object-contain" />
            </div>
            <div v-else class="flex items-end gap-0.5">
              <span class="text-3xl font-bold bg-gradient-to-br from-orange-400 via-purple-500 to-pink-500 bg-clip-text text-transparent leading-none">
                {{ appName.charAt(0) }}
              </span>
              <span class="text-lg font-semibold text-white tracking-wide leading-none pb-0.5">
                {{ appName.slice(1) }}
              </span>
            </div>
          </Link>
          <button @click="sidebarOpen = false" class="lg:hidden text-white hover:text-gray-300 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-176px)] custom-scrollbar">
        <!-- Main Section -->
        <div class="mb-4">
          <p class="px-3 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Main</p>
          <NavLink href="/dashboard" :active="isActive('dashboard', true)">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
          <span>Dashboard</span>
        </NavLink>

          <NavLink href="/tasks" :active="isActive('tasks')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span>My Tasks</span>
          </NavLink>
        </div>

        <!-- Finance Section -->
        <div class="mb-4">
          <p class="px-3 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Finance</p>
          <NavLink href="/wallet" :active="isActive('wallet')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            <span>Wallet</span>
          </NavLink>

          <NavLink href="/transactions" :active="isActive('transactions')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            <span>Transactions</span>
          </NavLink>

          <NavLink href="/withdrawal" :active="isActive('withdrawal')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Withdraw</span>
          </NavLink>
        </div>

        <!-- Team Section -->
        <div class="mb-4">
          <p class="px-3 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Team</p>
          <NavLink href="/referrals" :active="isActive('referrals')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span>Referrals</span>
          </NavLink>
        </div>

        <!-- Account Section -->
        <div>
          <p class="px-3 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Account</p>
          <NavLink href="/settings" :active="isActive('settings')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Settings</span>
          </NavLink>
        </div>
      </nav>

      <!-- Footer -->
      <div class="p-4 border-t border-white/10">
        <div class="text-center space-y-1">
          <p class="text-[10px] text-gray-500 uppercase tracking-wider">Version 1.2.2</p>
          <p class="text-[9px] text-gray-600">Powered by {{ appName }}</p>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:ml-64 min-h-screen flex flex-col relative z-10">
      <!-- Header -->
      <header class="bg-white/10 backdrop-blur-xl border-b border-white/20 sticky top-0 z-30">
        <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
          <div class="flex items-center gap-3">
            <button @click="sidebarOpen = true" class="lg:hidden p-2 hover:bg-white/10 rounded-lg transition-colors">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>
            <h2 class="text-xl sm:text-2xl font-bold text-white">{{ title }}</h2>
          </div>

          <div class="flex items-center gap-3">
            <!-- Notification Icon -->
            <Link href="/notifications" class="relative p-2 hover:bg-white/10 rounded-lg transition-colors">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
              </svg>
              <span v-if="unreadNotifications > 0" class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1.5 flex items-center justify-center bg-gradient-to-r from-pink-500 to-red-500 text-white text-xs font-bold rounded-full border-2 border-slate-900">
                {{ unreadNotifications > 99 ? '99+' : unreadNotifications }}
              </span>
            </Link>

            <!-- User Dropdown -->
            <div class="relative" ref="userMenuRef">
            <button
              @click.stop="userMenuOpen = !userMenuOpen"
              class="flex items-center gap-2 p-2 hover:bg-white/10 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500/50"
            >
              <img
                :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&background=7c3aed&color=fff`"
                class="w-8 h-8 sm:w-10 sm:h-10 rounded-full ring-2 ring-orange-500/50"
                :alt="userName"
              >
              <span class="hidden sm:block text-sm font-medium text-white">{{ userName }}</span>
              <svg
                :class="['w-4 h-4 text-gray-300 transition-transform duration-200', userMenuOpen ? 'rotate-180' : '']"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <Transition name="dropdown">
              <div
                v-if="userMenuOpen"
                @click.stop
                class="absolute right-0 mt-2 w-64 bg-slate-900 backdrop-blur-xl rounded-xl border border-orange-500/30 shadow-2xl overflow-hidden z-50"
              >
                <!-- User Info Header -->
                <div class="px-5 py-4 bg-gradient-to-r from-orange-500/10 to-purple-600/10 border-b border-white/10">
                  <div class="flex items-center gap-3">
                    <img
                      :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(userName)}&background=7c3aed&color=fff`"
                      class="w-12 h-12 rounded-full ring-2 ring-orange-500/50"
                      :alt="userName"
                    >
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-bold text-white truncate">{{ userName }}</p>
                      <p class="text-xs text-gray-300 truncate mt-0.5">{{ userEmail }}</p>
                    </div>
                  </div>
                </div>

                <!-- Menu Items -->
                <div class="py-2">
                  <Link
                    href="/profile"
                    class="flex items-center gap-3 px-5 py-3 text-sm text-gray-200 hover:bg-white/10 hover:text-white transition-all group"
                    @click="userMenuOpen = false"
                  >
                    <div class="p-2 rounded-lg bg-purple-500/10 group-hover:bg-purple-500/20 transition-colors">
                      <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                    </div>
                    <span class="font-medium">My Profile</span>
                  </Link>

                  <Link
                    href="/settings"
                    class="flex items-center gap-3 px-5 py-3 text-sm text-gray-200 hover:bg-white/10 hover:text-white transition-all group"
                    @click="userMenuOpen = false"
                  >
                    <div class="p-2 rounded-lg bg-blue-500/10 group-hover:bg-blue-500/20 transition-colors">
                      <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      </svg>
                    </div>
                    <span class="font-medium">Settings</span>
                  </Link>
                </div>

                <!-- Logout Button -->
                <div class="p-3 border-t border-white/10">
                  <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="flex items-center justify-center gap-2 w-full px-4 py-3 text-sm font-bold text-white bg-gradient-to-r from-orange-500 to-purple-600 rounded-lg shadow-lg hover:shadow-xl hover:shadow-orange-500/50 hover:scale-[1.02] transition-all"
                  >
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
      </header>

      <!-- Page Content -->
      <main class="flex-1 p-4 sm:p-6 lg:p-8">
        <slot />
      </main>

      <!-- Footer -->
      <footer class="bg-white/5 backdrop-blur-xl border-t border-white/20 py-4 px-4 sm:px-6 lg:px-8 mt-auto">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
          <p class="text-sm text-gray-300 text-center sm:text-left">
            &copy; {{ new Date().getFullYear() }} {{ appName }}. All rights reserved.
          </p>
          <p class="text-xs text-gray-400">
            Version 1.0.0
          </p>
        </div>
      </footer>
    </div>

    <ToastContainer />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import ToastContainer from '@/Components/ToastContainer.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  title: {
    type: String,
    default: 'Dashboard'
  }
});

const page = usePage();
const sidebarOpen = ref(false);
const userMenuOpen = ref(false);
const userMenuRef = ref(null);

// Subscribe to Pusher events
let pusherChannel = null;

onMounted(() => {
  // Close dropdown when clicking outside
  document.addEventListener('click', (e) => {
    if (userMenuRef.value && !userMenuRef.value.contains(e.target)) {
      userMenuOpen.value = false;
    }
  });

  // Subscribe to private user channel
  const userId = page.props.auth?.user?.id;
  if (userId && window.Echo) {
    pusherChannel = window.Echo.private(`user.${userId}`);

    console.log('🔔 Pusher channel subscribed:', `user.${userId}`);

    // Listen for notification broadcasts (same pattern as fraud-logout)
    pusherChannel.listen('.notification', (data) => {
      console.log('📬 Notification received:', data);

      // Update notification counter
      page.props.unreadNotifications = (page.props.unreadNotifications || 0) + 1;

      // Show SweetAlert toast
      Swal.fire({
        title: data.title || 'New Notification',
        html: `<p style="color: #e5e7eb;">${data.message || ''}</p>`,
        icon: 'info',
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        color: '#fff',
        confirmButtonColor: '#8b5cf6',
        confirmButtonText: data.action_url ? 'View Details' : 'OK',
        showCancelButton: !!data.action_url,
        cancelButtonText: 'Dismiss',
        cancelButtonColor: '#6b7280',
        timer: 8000,
        timerProgressBar: true,
        toast: true,
        position: 'top-end',
        showConfirmButton: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer);
          toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
      }).then((result) => {
        if (result.isConfirmed && data.action_url) {
          router.visit(data.action_url);
        }
      });
    });

    // Listen for fraud-logout alerts
    pusherChannel.listen('.fraud-logout', (data) => {
      // Show SweetAlert with fraud details
      Swal.fire({
        title: data.title || 'Security Alert',
        html: `
          <div class="text-left">
            <p class="text-gray-700 mb-4">${data.message || 'Security violation detected.'}</p>
            ${data.banned_until ? `<p class="text-sm text-red-600 font-semibold">Banned until: ${data.banned_until ? new Date(data.banned_until).toLocaleString() : 'N/A'}</p>` : ''}
          </div>
        `,
        icon: 'warning',
        iconColor: '#f97316',
        confirmButtonText: 'OK',
        confirmButtonColor: '#ef4444',
        allowOutsideClick: false,
        allowEscapeKey: false,
        customClass: {
          popup: 'rounded-2xl shadow-2xl',
          title: 'text-2xl font-bold text-gray-800',
          htmlContainer: 'text-base',
          confirmButton: 'px-8 py-3 rounded-lg font-bold shadow-lg hover:shadow-xl transition-all'
        }
      }).then(() => {
        // Logout user after they click OK
        router.post('/logout', {}, {
          onFinish: () => {
            window.location.href = '/login';
          }
        });
      });

      // Auto-logout after 10 seconds if user doesn't click
      setTimeout(() => {
        router.post('/logout', {}, {
          onFinish: () => {
            window.location.href = '/login';
          }
        });
      }, 10000);
    });
  }
});

onUnmounted(() => {
  // Clean up Pusher subscriptions
  if (pusherChannel) {
    pusherChannel.stopListening('.notification');
    pusherChannel.stopListening('.fraud-logout');
    window.Echo.leave(`user.${page.props.auth?.user?.id}`);
  }
});

const appName = computed(() => {
  const settings = page.props.settings;
  if (!settings) return 'CrowdPower';

  // Handle if settings is an array
  if (Array.isArray(settings)) {
    const setting = settings.find(s => s.key === 'app_name');
    return setting?.value || 'CrowdPower';
  }

  // Handle if settings is an object
  return settings.app_name || 'CrowdPower';
});

const siteLogo = computed(() => {
  const settings = page.props.settings;
  if (!settings) return null;

  if (Array.isArray(settings)) {
    const setting = settings.find(s => s.key === 'site_logo');
    return setting?.value || null;
  }

  return settings.site_logo || null;
});

const appAbbreviation = computed(() => {
  const name = appName.value;
  return name.split(' ').map(word => word[0]).join('').substring(0, 2).toUpperCase();
});

const userName = computed(() => page.props.auth?.user?.full_name || page.props.user?.full_name || 'User');
const userEmail = computed(() => page.props.auth?.user?.email || page.props.user?.email || page.props.auth?.user?.phone_number || page.props.user?.phone_number || 'user@example.com');
const unreadNotifications = computed(() => page.props.unreadNotifications || 0);

const isActive = (path, exact = false) => {
  const currentPath = page.url;
  if (exact) {
    return currentPath === `/${path}` || currentPath === path;
  }
  return currentPath.startsWith(`/${path}`) || currentPath.startsWith(path);
};
</script>

<style scoped>
@keyframes blob {
  0%, 100% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

.bg-grid-pattern {
  background-image:
    linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
  background-size: 50px 50px;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.3);
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.dropdown-enter-active, .dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
