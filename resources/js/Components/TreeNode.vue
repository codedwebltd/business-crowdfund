<template>
  <div class="flex items-start gap-3 relative">
    <!-- Connection Line from Parent (horizontal) -->
    <div v-if="!isRoot" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 w-3 h-px bg-orange-500"></div>

    <!-- Node Card -->
    <div
      @click="handleClick"
      @mouseenter="isHovered = true"
      @mouseleave="isHovered = false"
      :class="[
        'relative group cursor-pointer transition-all duration-200 shrink-0',
        isHovered ? 'scale-105 z-10' : 'scale-100'
      ]"
    >
      <div :class="[
        'bg-gradient-to-br rounded-lg p-3 border transition-all duration-200 w-[160px] md:w-[180px]',
        getNodeClass(),
        isHovered ? 'shadow-xl shadow-orange-500/40' : 'shadow-md'
      ]">
        <!-- Avatar & Name -->
        <div class="flex items-center gap-2 mb-2">
          <div class="relative shrink-0">
            <img
              :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(node.name)}&background=7c3aed&color=fff&size=32`"
              class="w-8 h-8 rounded-full ring-1 ring-white/30"
              :alt="node.name"
            />
            <div :class="[
              'absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border border-gray-900',
              node.status === 'ACTIVE' ? 'bg-green-500' : 'bg-yellow-500'
            ]"></div>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-white font-semibold text-xs truncate">{{ node.name }}</p>
            <p class="text-[10px] text-gray-400">{{ node.rank }}</p>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 gap-1.5 text-[10px]">
          <div class="bg-white/10 rounded px-1.5 py-1 text-center">
            <p class="text-gray-400 mb-0.5">Direct</p>
            <p class="text-white font-bold">{{ node.directReferrals }}</p>
          </div>
          <div class="bg-white/10 rounded px-1.5 py-1 text-center">
            <p class="text-gray-400 mb-0.5">Team</p>
            <p class="text-white font-bold">{{ node.totalTeam }}</p>
          </div>
        </div>

        <!-- Expand Indicator -->
        <div v-if="node.children && node.children.length > 0" class="flex justify-end mt-1">
          <svg class="w-3 h-3 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- Children (to the right) -->
    <div v-if="node.children && node.children.length > 0" class="flex flex-col gap-2 md:gap-3">
      <TreeNode
        v-for="child in node.children"
        :key="child.id"
        :node="child"
        :isRoot="false"
        @nodeClick="$emit('nodeClick', $event)"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  node: Object,
  isRoot: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['nodeClick']);

const isHovered = ref(false);

const getNodeClass = () => {
  if (props.isRoot) {
    return 'from-orange-500 to-purple-600 border-white/30';
  }

  switch (props.node.status) {
    case 'ACTIVE':
      return 'from-green-500/20 to-emerald-600/20 border-green-500/30';
    case 'PENDING':
      return 'from-yellow-500/20 to-amber-600/20 border-yellow-500/30';
    default:
      return 'from-gray-500/20 to-slate-600/20 border-gray-500/30';
  }
};

const handleClick = () => {
  emit('nodeClick', props.node);
};
</script>
