<template>
    <div class="fixed top-4 right-4 z-50 space-y-3">
        <TransitionGroup name="toast-list">
            <Toast
                v-for="toast in toasts"
                :key="toast.id"
                :message="toast.message"
                :type="toast.type"
                :duration="0"
                :show="toast.visible"
                @close="remove(toast.id)"
            />
        </TransitionGroup>
    </div>
</template>

<script setup>
import { useToast } from '@/composables/useToast';
import Toast from './Toast.vue';

const { toasts, remove } = useToast();
</script>

<style scoped>
.toast-list-move,
.toast-list-enter-active,
.toast-list-leave-active {
    transition: all 0.3s ease;
}

.toast-list-enter-from {
    transform: translateX(100%);
    opacity: 0;
}

.toast-list-leave-to {
    transform: translateY(-20px);
    opacity: 0;
}

.toast-list-leave-active {
    position: absolute;
    right: 1rem;
}
</style>
