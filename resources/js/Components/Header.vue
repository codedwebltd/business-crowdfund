<template>
    <nav class="fixed top-0 left-0 right-0 z-50 bg-slate-900/80 backdrop-blur-xl border-b border-white/10 shadow-2xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 lg:h-20">
                <!-- Logo -->
                <Link href="/" class="flex items-center hover:opacity-80 transition-opacity">
                    <div v-if="settings?.site_logo" class="h-10 lg:h-12">
                        <img :src="settings.site_logo" :alt="settings?.app_name || 'CrowdPower'" class="h-full w-auto object-contain" />
                    </div>
                    <div v-else class="flex items-end gap-0.5">
                        <span class="text-4xl lg:text-5xl font-bold bg-gradient-to-br from-orange-400 via-purple-500 to-pink-500 bg-clip-text text-transparent leading-none">
                            {{ (settings?.app_name || 'CrowdPower').charAt(0) }}
                        </span>
                        <span class="text-xl lg:text-2xl font-semibold text-white tracking-wide leading-none pb-0.5">
                            {{ (settings?.app_name || 'CrowdPower').slice(1) }}
                        </span>
                        <span class="text-[10px] text-gray-400 font-medium mb-1">™</span>
                    </div>
                </Link>

                <!-- Desktop Nav -->
                <div class="hidden lg:flex items-center gap-8">
                    <a href="#how-it-works" class="text-sm font-semibold text-gray-300 hover:text-orange-400 transition-colors">How It Works</a>
                    <a href="#earnings" class="text-sm font-semibold text-gray-300 hover:text-orange-400 transition-colors">Earnings</a>
                    <a href="#testimonials" class="text-sm font-semibold text-gray-300 hover:text-orange-400 transition-colors">Success Stories</a>
                </div>

                <!-- CTA + Mobile Toggle -->
                <div class="flex items-center gap-3">
                    <Link href="/login" class="hidden sm:inline-flex text-sm font-bold text-gray-300 hover:text-white transition-colors">
                        Sign In
                    </Link>
                    <Link href="/login" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-400 to-purple-500 text-white px-4 lg:px-5 py-2.5 rounded-lg text-sm font-bold shadow-lg hover:shadow-xl hover:shadow-orange-400/50 hover:scale-105 transition-all">
                        Get Started
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </Link>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-gray-300 hover:text-white transition-colors rounded-lg hover:bg-white/10">
                        <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <Transition name="slide-fade">
                <div v-if="mobileMenuOpen" class="lg:hidden pb-6 border-t border-white/10 mt-2">
                    <div class="flex flex-col gap-2 pt-4">
                        <!-- Menu Links -->
                        <a href="#how-it-works" @click="mobileMenuOpen = false"
                           class="group flex items-center justify-between px-4 py-3.5 rounded-xl text-gray-300 hover:text-white bg-white/5 hover:bg-gradient-to-r hover:from-orange-500/20 hover:to-purple-500/20 border border-white/10 hover:border-orange-500/30 transition-all duration-300">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-400/20 to-purple-500/20 flex items-center justify-center border border-orange-500/20 group-hover:border-orange-500/40 group-hover:scale-110 transition-all">
                                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold">How It Works</div>
                                    <div class="text-xs text-gray-400 group-hover:text-gray-300">Quick start guide</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-orange-400 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>

                        <a href="#earnings" @click="mobileMenuOpen = false"
                           class="group flex items-center justify-between px-4 py-3.5 rounded-xl text-gray-300 hover:text-white bg-white/5 hover:bg-gradient-to-r hover:from-orange-500/20 hover:to-purple-500/20 border border-white/10 hover:border-orange-500/30 transition-all duration-300">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-400/20 to-purple-500/20 flex items-center justify-center border border-orange-500/20 group-hover:border-orange-500/40 group-hover:scale-110 transition-all">
                                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold">Earnings</div>
                                    <div class="text-xs text-gray-400 group-hover:text-gray-300">See potential income</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-orange-400 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>

                        <a href="#testimonials" @click="mobileMenuOpen = false"
                           class="group flex items-center justify-between px-4 py-3.5 rounded-xl text-gray-300 hover:text-white bg-white/5 hover:bg-gradient-to-r hover:from-orange-500/20 hover:to-purple-500/20 border border-white/10 hover:border-orange-500/30 transition-all duration-300">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-400/20 to-purple-500/20 flex items-center justify-center border border-orange-500/20 group-hover:border-orange-500/40 group-hover:scale-110 transition-all">
                                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold">Success Stories</div>
                                    <div class="text-xs text-gray-400 group-hover:text-gray-300">User testimonials</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-orange-400 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>

                        <!-- Divider with Text -->
                        <div class="flex items-center gap-3 my-2">
                            <div class="h-px flex-1 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Account</span>
                            <div class="h-px flex-1 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                        </div>

                        <!-- Sign In Button -->
                        <Link href="/login" @click="mobileMenuOpen = false"
                              class="group flex items-center justify-between px-4 py-3.5 rounded-xl text-gray-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 transition-all duration-300 sm:hidden">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center border border-white/20 group-hover:scale-110 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                </div>
                                <div class="text-sm font-bold">Sign In</div>
                            </div>
                            <svg class="w-5 h-5 text-gray-500 group-hover:text-white group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </Link>

                        <!-- Get Started Button -->
                        <Link href="/login" @click="mobileMenuOpen = false"
                              class="group relative overflow-hidden px-4 py-4 rounded-xl bg-gradient-to-r from-orange-400 to-purple-500 hover:from-orange-300 hover:to-purple-400 text-white border-2 border-orange-400/50 hover:border-orange-300 shadow-lg hover:shadow-2xl hover:shadow-orange-500/50 transition-all duration-300">
                            <div class="relative flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center backdrop-blur-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-sm font-bold">Get Started Now</div>
                                        <div class="text-xs opacity-90">Start earning today</div>
                                    </div>
                                </div>
                                <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </div>
                            <!-- Animated Background -->
                            <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                        </Link>
                    </div>
                </div>
            </Transition>
        </div>
    </nav>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    settings: Object
});

const mobileMenuOpen = ref(false);
</script>

<style scoped>
.slide-fade-enter-active, .slide-fade-leave-active {
    transition: all 0.3s ease;
}

.slide-fade-enter-from {
    transform: translateY(-10px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateY(-10px);
    opacity: 0;
}
</style>
