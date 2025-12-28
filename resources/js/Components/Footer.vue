<template>
    <footer class="bg-gray-900 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm text-gray-400">
                © {{ new Date().getFullYear() }} {{ settings?.app_name || 'CrowdPower' }}. All rights reserved.
            </p>
        </div>
    </footer>
</template>

<script setup>
const props = defineProps({
    settings: Object
});
</script>
