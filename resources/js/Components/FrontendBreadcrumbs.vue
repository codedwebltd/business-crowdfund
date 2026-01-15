<template>
  <nav class="flex items-center" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-1 overflow-x-auto scrollbar-none">
      <!-- Home Icon -->
      <li class="flex items-center">
        <Link
          href="/"
          class="p-1.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 transition-all duration-200"
          title="Home"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
        </Link>
      </li>

      <!-- Breadcrumb Items -->
      <li v-for="(crumb, index) in crumbs" :key="index" class="flex items-center">
        <!-- Separator -->
        <svg class="w-4 h-4 text-gray-500/50 mx-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>

        <!-- Crumb Link or Text -->
        <Link
          v-if="crumb.url && index !== crumbs.length - 1"
          :href="crumb.url"
          class="flex items-center gap-1.5 px-2.5 py-1 text-sm font-medium text-gray-300 hover:text-white rounded-lg hover:bg-white/10 transition-all duration-200 whitespace-nowrap"
        >
          <component v-if="crumb.icon" :is="crumb.icon" class="w-4 h-4" />
          {{ crumb.label }}
        </Link>

        <!-- Active/Current Crumb -->
        <span
          v-else
          class="flex items-center gap-1.5 px-2.5 py-1 text-sm font-semibold whitespace-nowrap"
          :class="[
            index === crumbs.length - 1
              ? 'text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-purple-400'
              : 'text-gray-400'
          ]"
        >
          <component v-if="crumb.icon" :is="crumb.icon" class="w-4 h-4" />
          {{ crumb.label }}
        </span>
      </li>
    </ol>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  crumbs: {
    type: Array,
    required: true,
    // Example: [
    //   { label: 'Support', url: '/support', icon: null },
    //   { label: 'Ticket #123', url: null, icon: null }
    // ]
  }
});
</script>

<style scoped>
.scrollbar-none::-webkit-scrollbar {
  display: none;
}
.scrollbar-none {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
