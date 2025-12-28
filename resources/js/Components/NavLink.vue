<template>
  <Link
    :href="href"
    :class="[
      'flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200',
      active
        ? 'bg-gradient-to-r from-orange-500 to-purple-600 text-white shadow-lg shadow-orange-500/50'
        : 'text-white/80 hover:bg-white/10 hover:text-white'
    ]"
  >
    <slot />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  href: String,
  active: Boolean
});
</script>
