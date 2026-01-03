<template>
  <nav class="flex items-center space-x-1 overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 pb-2">
    <Link
      v-for="(crumb, index) in crumbs"
      :key="index"
      :href="crumb.url || '#'"
      class="flex items-center group whitespace-nowrap"
    >
      <span
        :class="[
          'text-sm font-medium transition-colors',
          index === crumbs.length - 1
            ? 'text-purple-600'
            : 'text-gray-500 hover:text-gray-700'
        ]"
      >
        {{ crumb.label }}
      </span>
      <svg
        v-if="index < crumbs.length - 1"
        class="w-4 h-4 mx-2 text-gray-400"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
    </Link>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  crumbs: {
    type: Array,
    required: true,
    // Example: [{ label: 'Home', url: '/admin/dashboard' }, { label: 'Liquidity' }]
  }
});
</script>
