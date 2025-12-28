<template>
  <div class="relative inline-block" ref="tooltipContainer">
    <button
      type="button"
      @mouseenter="show = true"
      @mouseleave="show = false"
      @click.stop="show = !show"
      class="inline-flex items-center justify-center w-4 h-4 text-gray-400 hover:text-gray-600 focus:outline-none"
    >
      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
      </svg>
    </button>

    <Transition name="tooltip">
      <div
        v-if="show"
        class="fixed z-[9999] w-72 px-4 py-3 text-xs leading-relaxed text-white bg-gray-900 rounded-lg shadow-2xl"
        :style="tooltipStyle"
        @click.stop
      >
        {{ text }}
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

defineProps({
  text: String
});

const show = ref(false);
const tooltipContainer = ref(null);

// Calculate tooltip position to avoid overflow
const tooltipStyle = computed(() => {
  if (!tooltipContainer.value) return {};

  const rect = tooltipContainer.value.getBoundingClientRect();
  const top = rect.top - 10; // Position above icon with gap
  const left = rect.left + rect.width / 2; // Center horizontally

  return {
    top: `${top}px`,
    left: `${left}px`,
    transform: 'translate(-50%, -100%)'
  };
});

// Close tooltip on outside click
const handleClickOutside = (e) => {
  if (tooltipContainer.value && !tooltipContainer.value.contains(e.target)) {
    show.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
.tooltip-enter-active, .tooltip-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.tooltip-enter-from, .tooltip-leave-to {
  opacity: 0;
  transform: translate(-50%, -100%) scale(0.95);
}
</style>
