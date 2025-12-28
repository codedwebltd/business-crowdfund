<template>
    <footer class="fixed bottom-0 left-0 right-0 backdrop-blur-xl bg-purple-900/30 border-t border-purple-500/20 z-30">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center justify-between text-sm">
                <p class="text-purple-200">
                    &copy; {{ currentYear }} {{ appName }}. All rights reserved.
                </p>
                <p class="text-purple-300 hidden sm:block">
                    Empowering <span class="text-pink-400 font-semibold">{{ totalMembers }}+</span> earners worldwide
                </p>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const currentYear = new Date().getFullYear();
const appName = computed(() => page.props.appName || 'CrowdPower');
const totalMembers = computed(() => page.props.settings?.total_members || '10,000');
</script>
