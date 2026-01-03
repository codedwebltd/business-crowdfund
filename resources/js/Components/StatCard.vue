<template>
  <div class="bg-white/10 backdrop-blur-xl rounded-xl border border-white/20 p-4 hover:shadow-xl hover:shadow-white/10 transition-all duration-300">
    <div class="flex items-start justify-between mb-3">
      <div :class="[
        'p-2.5 rounded-lg',
        color === 'green' ? 'bg-green-500/20' :
        color === 'blue' ? 'bg-blue-500/20' :
        color === 'purple' ? 'bg-purple-500/20' :
        color === 'orange' ? 'bg-orange-500/20' :
        color === 'yellow' ? 'bg-yellow-500/20' :
        'bg-gray-500/20'
      ]">
        <!-- Currency Icon -->
        <svg v-if="icon === 'currency'" :class="[
          'w-5 h-5',
          color === 'green' ? 'text-green-400' :
          color === 'blue' ? 'text-blue-400' :
          color === 'purple' ? 'text-purple-400' :
          color === 'orange' ? 'text-orange-400' :
          color === 'yellow' ? 'text-yellow-400' :
          'text-gray-400'
        ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>

        <!-- Wallet Icon -->
        <svg v-else-if="icon === 'wallet'" :class="[
          'w-5 h-5',
          color === 'green' ? 'text-green-400' :
          color === 'blue' ? 'text-blue-400' :
          color === 'purple' ? 'text-purple-400' :
          color === 'orange' ? 'text-orange-400' :
          color === 'yellow' ? 'text-yellow-400' :
          'text-gray-400'
        ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
        </svg>

        <!-- Check Icon -->
        <svg v-else-if="icon === 'check'" :class="[
          'w-5 h-5',
          color === 'green' ? 'text-green-400' :
          color === 'blue' ? 'text-blue-400' :
          color === 'purple' ? 'text-purple-400' :
          color === 'orange' ? 'text-orange-400' :
          color === 'yellow' ? 'text-yellow-400' :
          'text-gray-400'
        ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>

        <!-- Users Icon -->
        <svg v-else-if="icon === 'users'" :class="[
          'w-5 h-5',
          color === 'green' ? 'text-green-400' :
          color === 'blue' ? 'text-blue-400' :
          color === 'purple' ? 'text-purple-400' :
          color === 'orange' ? 'text-orange-400' :
          color === 'yellow' ? 'text-yellow-400' :
          'text-gray-400'
        ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>

        <!-- Clock Icon -->
        <svg v-else-if="icon === 'clock'" :class="[
          'w-5 h-5',
          color === 'green' ? 'text-green-400' :
          color === 'blue' ? 'text-blue-400' :
          color === 'purple' ? 'text-purple-400' :
          color === 'orange' ? 'text-orange-400' :
          color === 'yellow' ? 'text-yellow-400' :
          'text-gray-400'
        ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
    </div>

    <div class="text-xs text-gray-400 mb-1">{{ title }}</div>
    <div class="text-2xl font-bold text-white break-words overflow-hidden" style="word-break: break-word; max-width: 100%;">{{ value }}</div>
    <div v-if="change" :class="[
      'text-xs font-medium mt-2',
      change > 0 ? 'text-green-400' : 'text-red-400'
    ]">
      {{ change > 0 ? '+' : '' }}{{ change }}%
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: String,
  value: [String, Number],
  icon: String,
  color: String,
  change: Number
});
</script>
