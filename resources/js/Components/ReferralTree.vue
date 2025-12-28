<template>
  <div class="overflow-x-auto pb-2">
    <div class="inline-flex flex-col items-center min-w-full pt-2">
      <TreeNode
        :node="node"
        :isRoot="true"
        @nodeClick="$emit('nodeClick', $event)"
      />
    </div>
  </div>
</template>

<script setup>
import TreeNode from '@/Components/TreeNode.vue';

defineProps({
  node: Object
});

defineEmits(['nodeClick']);
</script>
