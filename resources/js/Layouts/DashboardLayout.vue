<template>
    <div class="min-h-screen bg-gradient-to-br from-[#1a0033] via-[#2d1b4e] to-[#1a0033]">
        <!-- Header -->
        <DashboardHeader />

        <!-- Main Content -->
        <main class="pb-20">
            <slot />
        </main>

        <!-- Footer -->
        <DashboardFooter />
    </div>
</template>

<script setup>
import DashboardHeader from '@/Components/Dashboard/DashboardHeader.vue';
import DashboardFooter from '@/Components/Dashboard/DashboardFooter.vue';
</script>
