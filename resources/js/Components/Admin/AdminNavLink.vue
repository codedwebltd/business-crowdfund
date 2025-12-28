<template>
  <Link
    :href="href"
    :class="[
      'flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200',
      active
        ? 'bg-gray-900 text-white shadow-lg'
        : 'text-gray-700 hover:bg-gray-100'
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
