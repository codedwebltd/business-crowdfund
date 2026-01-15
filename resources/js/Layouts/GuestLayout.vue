<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 relative overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-orange-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
      <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
      <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
    </div>

    <!-- Main Content -->
    <div class="min-h-screen flex flex-col relative z-10">
      <!-- Header -->
      <header class="bg-white/10 backdrop-blur-xl border-b border-white/20 sticky top-0 z-30">
        <div class="px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
          <div class="flex items-center gap-4">
            <!-- Logo -->
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

            <!-- Breadcrumbs -->
            <div v-if="breadcrumbs && breadcrumbs.length > 0" class="hidden sm:block">
              <FrontendBreadcrumbs :crumbs="breadcrumbs" />
            </div>
          </div>

          <div class="flex items-center gap-3">
            <!-- Login Button -->
            <Link
              href="/login"
              class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-orange-500 to-purple-600 rounded-lg shadow-lg hover:shadow-xl hover:shadow-orange-500/30 hover:scale-[1.02] transition-all"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
              </svg>
              <span>Login</span>
            </Link>
          </div>
        </div>

        <!-- Mobile Breadcrumbs -->
        <div v-if="breadcrumbs && breadcrumbs.length > 0" class="sm:hidden px-4 pb-3">
          <FrontendBreadcrumbs :crumbs="breadcrumbs" />
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
          <div class="flex items-center gap-4">
            <Link href="/support" class="text-xs text-gray-400 hover:text-white transition-colors">Support</Link>
            <span class="text-gray-600">|</span>
            <p class="text-xs text-gray-400">Version 1.0.0</p>
          </div>
        </div>
      </footer>
    </div>

    <ToastContainer />
    <SupportWidget />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ToastContainer from '@/Components/ToastContainer.vue';
import SupportWidget from '@/Components/Support/SupportWidget.vue';
import FrontendBreadcrumbs from '@/Components/FrontendBreadcrumbs.vue';

const props = defineProps({
  title: {
    type: String,
    default: 'Support'
  },
  breadcrumbs: {
    type: Array,
    default: () => []
  }
});

const page = usePage();

const appName = computed(() => {
  const settings = page.props.settings;
  if (!settings) return 'CrowdPower';

  if (Array.isArray(settings)) {
    const setting = settings.find(s => s.key === 'app_name');
    return setting?.value || 'CrowdPower';
  }

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
</style>
