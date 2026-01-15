<template>
  <div class="flex items-start gap-3 relative">
    <!-- Connection Line from Parent (horizontal) -->
    <div v-if="!isRoot" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-3 w-3 h-px bg-gray-300"></div>

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
        'bg-white rounded-xl p-3 border-2 transition-all duration-200 w-[160px] md:w-[180px]',
        getNodeClass(),
        isHovered ? 'shadow-xl' : 'shadow-md'
      ]">
        <!-- Avatar & Name -->
        <div class="flex items-center gap-2 mb-2">
          <div class="relative shrink-0">
            <img
              :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(node.name)}&background=${getAvatarColor()}&color=fff&size=32`"
              class="w-8 h-8 rounded-full ring-2 ring-gray-200"
              :alt="node.name"
            />
            <div :class="[
              'absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white',
              node.status === 'ACTIVE' ? 'bg-green-500' : 'bg-yellow-500'
            ]"></div>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-gray-900 font-bold text-xs truncate">{{ node.name }}</p>
            <p class="text-[10px] text-gray-500">{{ node.rank }}</p>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 gap-1.5 text-[10px] mb-1.5">
          <div class="bg-blue-50 border border-blue-200 rounded px-1.5 py-1 text-center">
            <p class="text-blue-600 mb-0.5 font-semibold">Direct</p>
            <p class="text-gray-900 font-bold">{{ node.directReferrals }}</p>
          </div>
          <div class="bg-purple-50 border border-purple-200 rounded px-1.5 py-1 text-center">
            <p class="text-purple-600 mb-0.5 font-semibold">Team</p>
            <p class="text-gray-900 font-bold">{{ node.totalTeam }}</p>
          </div>
        </div>

        <!-- Financial Stats (Total Earned & Total Deposited) -->
        <div class="grid grid-cols-2 gap-1.5 text-[10px]">
          <div class="bg-green-50 border border-green-300 rounded px-1.5 py-1 text-center overflow-hidden">
            <p class="text-green-600 mb-0.5 text-[9px] font-semibold">Earned</p>
            <p class="text-green-700 font-bold text-[10px] break-all leading-tight">{{ currencySymbol }}{{ formatCurrency(node.totalEarned) }}</p>
          </div>
          <div class="bg-indigo-50 border border-indigo-300 rounded px-1.5 py-1 text-center overflow-hidden">
            <p class="text-indigo-600 mb-0.5 text-[9px] font-semibold">Deposited</p>
            <p class="text-indigo-700 font-bold text-[10px] break-all leading-tight">{{ currencySymbol }}{{ formatCurrency(node.totalDeposited) }}</p>
          </div>
        </div>

        <!-- Expand Indicator -->
        <div v-if="node.children && node.children.length > 0" class="flex justify-end mt-1">
          <svg class="w-3 h-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        :currencySymbol="currencySymbol"
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
  },
  currencySymbol: {
    type: String,
    default: '₦'
  }
});

const emit = defineEmits(['nodeClick']);

const isHovered = ref(false);

const getNodeClass = () => {
  if (props.isRoot) {
    return 'border-purple-400 bg-purple-50';
  }

  switch (props.node.status) {
    case 'ACTIVE':
      return 'border-green-400 hover:border-green-500';
    case 'PENDING':
      return 'border-yellow-400 hover:border-yellow-500';
    case 'SUSPENDED':
      return 'border-orange-400 hover:border-orange-500';
    case 'BANNED':
      return 'border-red-400 hover:border-red-500';
    default:
      return 'border-gray-300 hover:border-gray-400';
  }
};

const getAvatarColor = () => {
  if (props.isRoot) {
    return '7c3aed'; // Purple for root
  }

  switch (props.node.status) {
    case 'ACTIVE':
      return '10b981'; // Green
    case 'PENDING':
      return 'f59e0b'; // Yellow
    case 'SUSPENDED':
      return 'f97316'; // Orange
    case 'BANNED':
      return 'ef4444'; // Red
    default:
      return '6b7280'; // Gray
  }
};

const formatCurrency = (amount) => {
  if (!amount && amount !== 0) return '0';
  const num = parseFloat(amount);
  // Format with comma separators, no decimals
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(num);
};

const handleClick = () => {
  emit('nodeClick', props.node);
};
</script>
