<template>
    <header class="sticky top-0 z-50 backdrop-blur-xl bg-purple-900/30 border-b border-purple-500/20">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <Link href="/dashboard" class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                        <span class="text-white font-bold text-lg">CP</span>
                    </div>
                    <span class="text-white font-bold text-xl hidden sm:block">{{ appName }}</span>
                </Link>

                <!-- Desktop Menu Button -->
                <button @click="toggleSidebar" class="p-2 rounded-lg bg-purple-500/20 hover:bg-purple-500/30 transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <!-- User Profile & Notifications -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="relative p-2 rounded-lg bg-purple-500/20 hover:bg-purple-500/30 transition-colors">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-pink-500 rounded-full"></span>
                    </button>

                    <!-- Avatar -->
                    <div class="flex items-center space-x-2 p-1.5 rounded-lg bg-purple-500/20">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center">
                            <span class="text-white font-semibold text-sm">{{ userInitials }}</span>
                        </div>
                        <span class="text-white text-sm font-medium hidden md:block pr-2">{{ userName }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Overlay -->
        <Transition name="fade">
            <div v-if="sidebarOpen" @click="toggleSidebar" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40"></div>
        </Transition>

        <!-- Sidebar -->
        <Transition name="slide">
            <aside v-if="sidebarOpen" class="fixed top-0 right-0 h-full w-72 bg-gradient-to-b from-purple-900 to-purple-950 border-l border-purple-500/30 z-50 overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-white font-bold text-xl">Menu</h2>
                        <button @click="toggleSidebar" class="p-2 rounded-lg hover:bg-purple-500/20 transition-colors">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <nav class="space-y-2">
                        <Link v-for="item in menuItems" :key="item.name" :href="item.href"
                            class="flex items-center space-x-3 px-4 py-3 rounded-lg text-purple-200 hover:bg-purple-500/20 hover:text-white transition-all"
                            :class="{ 'bg-purple-500/30 text-white': $page.url === item.href }">
                            <span v-html="item.icon" class="w-5 h-5"></span>
                            <span>{{ item.name }}</span>
                        </Link>
                    </nav>

                    <button @click="logout" class="mt-8 w-full flex items-center space-x-3 px-4 py-3 rounded-lg text-red-400 hover:bg-red-500/20 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </div>
            </aside>
        </Transition>
    </header>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

const page = usePage();
const sidebarOpen = ref(false);

const appName = computed(() => page.props.appName || 'CrowdPower');
const userName = computed(() => page.props.auth?.user?.full_name || 'User');
const userInitials = computed(() => {
    const name = userName.value;
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
});

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const menuItems = [
    { name: 'Dashboard', href: '/dashboard', icon: '<svg fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>' },
    { name: 'Tasks', href: '/tasks', icon: '<svg fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>' },
    { name: 'Wallet', href: '/wallet', icon: '<svg fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/></svg>' },
    { name: 'Team', href: '/team', icon: '<svg fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/></svg>' },
    { name: 'Profile', href: '/profile', icon: '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>' },
];

const logout = () => {
    router.post('/logout');
};
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.slide-enter-active, .slide-leave-active { transition: transform 0.3s; }
.slide-enter-from, .slide-leave-to { transform: translateX(100%); }
</style>
