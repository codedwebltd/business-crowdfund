<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-orange-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

            <!-- Grid Pattern -->
            <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
        </div>

        <!-- Navigation -->
        <Header :settings="settings" />

        <!-- Hero Slider Section -->
        <section class="relative pt-24 lg:pt-32 pb-16 lg:pb-24 overflow-hidden">

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Slider Container -->
                <div class="relative overflow-hidden">
                    <!-- Slide Content -->
                    <Transition name="slide-horizontal">
                        <div :key="currentSlide" class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center min-h-[600px]">
                            <!-- Left Content -->
                            <div class="space-y-6 lg:space-y-8 animate-slide-in-left">
                                <!-- Headline -->
                                <div class="space-y-4">
                                    <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-extrabold leading-[1.1] tracking-tight">
                                        <span class="text-white">{{ slides[currentSlide].title }}</span>
                                        <br />
                                        <span class="bg-gradient-to-r from-orange-400 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                                            {{ slides[currentSlide].highlight }}
                                        </span>
                                    </h1>
                                    <p class="text-lg lg:text-xl text-gray-300 leading-relaxed max-w-xl">
                                        {{ slides[currentSlide].description }}
                                    </p>
                                </div>

                                <!-- Stats -->
                                <div class="grid grid-cols-3 gap-4 pt-2">
                                    <div class="space-y-1 transform hover:scale-105 transition-transform bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                        <div class="text-3xl lg:text-4xl font-bold text-white">₦{{ settings?.daily_earning_average || 850 }}</div>
                                        <div class="text-xs lg:text-sm text-gray-300">Daily Avg.</div>
                                    </div>
                                    <div class="space-y-1 transform hover:scale-105 transition-transform bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                        <div class="text-3xl lg:text-4xl font-bold text-white">{{ settings?.time_required || '5min' }}</div>
                                        <div class="text-xs lg:text-sm text-gray-300">Daily Time</div>
                                    </div>
                                    <div class="space-y-1 transform hover:scale-105 transition-transform bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                                        <div class="text-3xl lg:text-4xl font-bold text-white">{{ settings?.anonymity_level || '100%' }}</div>
                                        <div class="text-xs lg:text-sm text-gray-300">Anonymous</div>
                                    </div>
                                </div>

                                <!-- CTA -->
                                <div class="flex flex-col sm:flex-row gap-4 pt-2">
                                    <Link :href="auth?.user ? '/dashboard' : '/login'" class="group inline-flex items-center justify-center gap-2 bg-gradient-to-r from-orange-400 to-purple-500 text-white px-8 py-4 rounded-xl text-base font-bold shadow-lg shadow-orange-500/50 hover:shadow-2xl hover:shadow-orange-400/60 hover:scale-105 transition-all">
                                        {{ auth?.user ? 'Go to Dashboard' : 'Start Earning Now' }}
                                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                        </svg>
                                    </Link>
                                    <a href="#how-it-works" class="inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur-sm text-white px-8 py-4 rounded-xl text-base font-bold border-2 border-white/20 hover:border-orange-400 hover:bg-white/20 transition-all">
                                        Learn More
                                    </a>
                                </div>

                                <!-- Trust Badges -->
                                <div class="flex items-center gap-2 sm:gap-3 lg:gap-4 pt-2 text-xs sm:text-sm text-gray-300 overflow-x-auto scrollbar-hide">
                                    <div class="flex items-center gap-1.5 sm:gap-2 animate-slide-in-left bg-white/5 backdrop-blur-sm px-3 sm:px-4 py-2 rounded-full border border-white/10 whitespace-nowrap flex-shrink-0">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        <span class="font-semibold text-white">Secure</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 sm:gap-2 animate-slide-in-left animation-delay-100 bg-white/5 backdrop-blur-sm px-3 sm:px-4 py-2 rounded-full border border-white/10 whitespace-nowrap flex-shrink-0">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <span class="font-semibold text-white">Instant Payout</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 sm:gap-2 animate-slide-in-left animation-delay-200 bg-white/5 backdrop-blur-sm px-3 sm:px-4 py-2 rounded-full border border-white/10 whitespace-nowrap flex-shrink-0">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <span class="font-semibold text-white">{{ settings?.total_users || '10,000+' }} Users</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Visual -->
                            <div class="relative lg:block hidden">
                                <div class="relative animate-slide-in-right">
                                    <!-- Globe Animation -->
                                    <div v-if="currentSlide === 0" class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64">
                                        <div class="w-full h-full rounded-full bg-gradient-to-br from-orange-100 to-orange-200 animate-spin-slow opacity-20"></div>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <svg class="w-40 h-40 text-orange-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Dashboard Preview -->
                                    <div class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 overflow-hidden relative z-10">
                                        <div class="bg-gradient-to-br from-orange-500 to-purple-600 px-6 py-8 text-white">
                                            <div class="flex items-center justify-between mb-6">
                                                <div>
                                                    <div class="text-sm opacity-90 mb-1">{{ slides[currentSlide].cardTitle }}</div>
                                                    <div class="text-4xl font-bold">{{ slides[currentSlide].cardValue }}</div>
                                                </div>
                                                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm shadow-lg">
                                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 text-sm">
                                                <div class="flex items-center gap-1 bg-white/30 rounded-full px-3 py-1 backdrop-blur-sm">
                                                    <span>{{ slides[currentSlide].cardBadge }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tasks List -->
                                        <div class="p-6 space-y-4">
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="font-semibold text-white">Today's Tasks</span>
                                                <span class="text-orange-400 font-semibold">3/8 Completed</span>
                                            </div>

                                            <div class="space-y-3">
                                                <!-- Task 1 -->
                                                <div class="flex items-center gap-3 p-3 bg-green-500/20 border border-green-500/30 rounded-lg backdrop-blur-sm">
                                                    <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-sm font-semibold text-white">Morning Survey</div>
                                                        <div class="text-xs text-gray-300">Completed 2 hours ago</div>
                                                    </div>
                                                    <div class="text-sm font-bold text-green-400">₦200</div>
                                                </div>

                                                <!-- Task 2 -->
                                                <div class="flex items-center gap-3 p-3 bg-green-500/20 border border-green-500/30 rounded-lg backdrop-blur-sm">
                                                    <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-sm font-semibold text-white">Video Review</div>
                                                        <div class="text-xs text-gray-300">Completed 1 hour ago</div>
                                                    </div>
                                                    <div class="text-sm font-bold text-green-400">₦400</div>
                                                </div>

                                                <!-- Task 3 -->
                                                <div class="flex items-center gap-3 p-3 bg-white/10 border border-white/20 rounded-lg backdrop-blur-sm">
                                                    <div class="w-5 h-5 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-sm font-semibold text-white">Quick Survey</div>
                                                        <div class="text-xs text-gray-300">Available now</div>
                                                    </div>
                                                    <div class="text-sm font-bold text-orange-400">₦150</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Transition>

                    <!-- Slider Dots -->
                    <div class="flex justify-center gap-2 mt-8 lg:mt-12">
                        <button
                            v-for="(_, index) in slides"
                            :key="index"
                            @click="currentSlide = index"
                            :class="[
                                'h-2 rounded-full transition-all duration-300',
                                currentSlide === index ? 'w-8 bg-gradient-to-r from-orange-400 to-purple-500 shadow-lg' : 'w-2 bg-white/30 hover:bg-white/50'
                            ]"
                        ></button>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section id="how-it-works" class="py-16 lg:py-24 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-16 animate-fade-in">
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-4">
                        How It <span class="bg-gradient-to-r from-orange-400 to-purple-500 bg-clip-text text-transparent">Works</span>
                    </h2>
                    <p class="text-lg text-gray-300 max-w-2xl mx-auto">
                        Start earning in 4 simple steps. No technical skills required.
                    </p>
                </div>

                <!-- Steps Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 relative">
                    <!-- Connecting Lines (Desktop) -->
                    <div class="hidden lg:block absolute top-24 left-0 right-0 h-0.5 bg-gradient-to-r from-orange-500/20 via-purple-500/40 to-orange-500/20 -z-10"></div>

                    <!-- Step 1 -->
                    <div
                        @click="toggleStep(0)"
                        class="group bg-white/10 backdrop-blur-xl rounded-2xl p-6 lg:p-8 border-2 border-white/20 hover:border-orange-400 transition-all duration-300 cursor-pointer hover:shadow-2xl hover:shadow-orange-500/20 hover:-translate-y-2 animate-slide-up"
                        :class="{ 'ring-2 ring-orange-500 border-orange-500': expandedStep === 0 }"
                    >
                        <!-- Step Number -->
                        <div class="w-12 h-12 lg:w-16 lg:h-16 mx-auto mb-6 bg-gradient-to-br from-orange-400 to-purple-500 rounded-full flex items-center justify-center text-white text-xl lg:text-2xl font-bold shadow-lg group-hover:scale-110 transition-transform">
                            01
                        </div>

                        <!-- Icon -->
                        <div class="w-16 h-16 mx-auto mb-4 bg-orange-500/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:bg-orange-500/30 transition-colors border border-orange-500/30">
                            <svg class="w-8 h-8 text-orange-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>

                        <!-- Content -->
                        <h3 class="text-xl font-bold text-white mb-3 text-center">Register & Activate</h3>
                        <p class="text-gray-300 text-sm text-center mb-4">
                            Sign up with your phone number and bank details. Choose your plan.
                        </p>

                        <!-- Expandable Details -->
                        <Transition name="expand">
                            <div v-if="expandedStep === 0" class="mt-4 pt-4 border-t border-white/20 space-y-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-5 h-5 bg-green-500/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-500/50">
                                        <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-300">Phone verification via SMS</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="w-5 h-5 bg-green-500/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-500/50">
                                        <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-300">Choose Basic, Premium or VIP plan</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="w-5 h-5 bg-green-500/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-500/50">
                                        <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-300">Instant activation after payment</p>
                                </div>
                            </div>
                        </Transition>

                        <!-- Expand Indicator -->
                        <div class="flex justify-center mt-4">
                            <svg
                                class="w-5 h-5 text-orange-400 transition-transform duration-300"
                                :class="{ 'rotate-180': expandedStep === 0 }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div
                        @click="toggleStep(1)"
                        class="group bg-white/10 backdrop-blur-xl rounded-2xl p-6 lg:p-8 border-2 border-white/20 hover:border-orange-400 transition-all duration-300 cursor-pointer hover:shadow-2xl hover:shadow-orange-500/20 hover:-translate-y-2 animate-slide-up animation-delay-100"
                        :class="{ 'ring-2 ring-orange-500 border-orange-500': expandedStep === 1 }"
                    >
                        <div class="w-12 h-12 lg:w-16 lg:h-16 mx-auto mb-6 bg-gradient-to-br from-orange-400 to-purple-500 rounded-full flex items-center justify-center text-white text-xl lg:text-2xl font-bold shadow-lg group-hover:scale-110 transition-transform">
                            02
                        </div>

                        <div class="w-16 h-16 mx-auto mb-4 bg-orange-500/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:bg-orange-500/30 transition-colors border border-orange-500/30">
                            <svg class="w-8 h-8 text-orange-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>

                        <h3 class="text-xl font-bold text-white mb-3 text-center">Complete Tasks</h3>
                        <p class="text-gray-300 text-sm text-center mb-4">
                            Access daily tasks. Complete surveys, watch videos, sync data.
                        </p>

                        <Transition name="expand">
                            <div v-if="expandedStep === 1" class="mt-4 pt-4 border-t border-white/20 space-y-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-5 h-5 bg-green-500/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-500/50">
                                        <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-300">8-25 tasks daily based on plan</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="w-5 h-5 bg-green-500/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-500/50">
                                        <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-300">2-5 minutes per task</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="w-5 h-5 bg-green-500/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-500/50">
                                        <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-300">Earn ₦50-500 per task</p>
                                </div>
                            </div>
                        </Transition>

                        <div class="flex justify-center mt-4">
                            <svg
                                class="w-5 h-5 text-orange-400 transition-transform duration-300"
                                :class="{ 'rotate-180': expandedStep === 1 }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div
                        @click="toggleStep(2)"
                        class="group bg-white/10 backdrop-blur-xl rounded-2xl p-6 lg:p-8 border-2 border-white/20 hover:border-orange-400 transition-all duration-300 cursor-pointer hover:shadow-2xl hover:shadow-orange-500/20 hover:-translate-y-2 animate-slide-up animation-delay-200"
                        :class="{ 'ring-2 ring-orange-500 border-orange-500': expandedStep === 2 }"
                    >
                        <div class="w-12 h-12 lg:w-16 lg:h-16 mx-auto mb-6 bg-gradient-to-br from-orange-400 to-purple-500 rounded-full flex items-center justify-center text-white text-xl lg:text-2xl font-bold shadow-lg group-hover:scale-110 transition-transform">
                            03
                        </div>

                        <div class="w-16 h-16 mx-auto mb-4 bg-orange-500/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:bg-orange-500/30 transition-colors border border-orange-500/30">
                            <svg class="w-8 h-8 text-orange-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>

                        <h3 class="text-xl font-bold text-white mb-3 text-center">Refer & Multiply</h3>
                        <p class="text-gray-300 text-sm text-center mb-4">
                            Share your link. Earn 20% activation + 10% of their task earnings.
                        </p>

                        <Transition name="expand">
                            <div v-if="expandedStep === 2" class="mt-4 pt-4 border-t border-white/20 space-y-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-5 h-5 bg-green-500/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-500/50">
                                        <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-300">Earn across 40 levels deep</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="w-5 h-5 bg-green-500/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-500/50">
                                        <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-300">Build passive income streams</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="w-5 h-5 bg-green-500/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-500/50">
                                        <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-300">Unlock higher ranks & bonuses</p>
                                </div>
                            </div>
                        </Transition>

                        <div class="flex justify-center mt-4">
                            <svg
                                class="w-5 h-5 text-orange-400 transition-transform duration-300"
                                :class="{ 'rotate-180': expandedStep === 2 }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div
                        @click="toggleStep(3)"
                        class="group bg-white/10 backdrop-blur-xl rounded-2xl p-6 lg:p-8 border-2 border-white/20 hover:border-orange-400 transition-all duration-300 cursor-pointer hover:shadow-2xl hover:shadow-orange-500/20 hover:-translate-y-2 animate-slide-up animation-delay-300"
                        :class="{ 'ring-2 ring-orange-500 border-orange-500': expandedStep === 3 }"
                    >
                        <div class="w-12 h-12 lg:w-16 lg:h-16 mx-auto mb-6 bg-gradient-to-br from-orange-400 to-purple-500 rounded-full flex items-center justify-center text-white text-xl lg:text-2xl font-bold shadow-lg group-hover:scale-110 transition-transform">
                            04
                        </div>

                        <div class="w-16 h-16 mx-auto mb-4 bg-orange-500/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:bg-orange-500/30 transition-colors border border-orange-500/30">
                            <svg class="w-8 h-8 text-orange-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>

                        <h3 class="text-xl font-bold text-white mb-3 text-center">Withdraw Earnings</h3>
                        <p class="text-gray-300 text-sm text-center mb-4">
                            Funds mature in 72 hours. Withdraw to bank, OPay or crypto.
                        </p>

                        <Transition name="expand">
                            <div v-if="expandedStep === 3" class="mt-4 pt-4 border-t border-white/20 space-y-3">
                                <div class="flex items-start gap-3">
                                    <div class="w-5 h-5 bg-green-500/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-500/50">
                                        <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-300">Multiple withdrawal methods</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="w-5 h-5 bg-green-500/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-500/50">
                                        <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-300">Processing time based on rank</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="w-5 h-5 bg-green-500/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-500/50">
                                        <svg class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-300">No hidden fees or charges</p>
                                </div>
                            </div>
                        </Transition>

                        <div class="flex justify-center mt-4">
                            <svg
                                class="w-5 h-5 text-orange-400 transition-transform duration-300"
                                :class="{ 'rotate-180': expandedStep === 3 }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="text-center mt-16">
                    <Link :href="auth?.user ? '/dashboard' : '/login'" class="inline-flex items-center gap-2 bg-gradient-to-r from-orange-400 to-purple-500 text-white px-10 py-4 rounded-xl text-lg font-bold shadow-xl shadow-orange-500/50 hover:shadow-2xl hover:shadow-orange-400/60 hover:scale-105 transition-all">
                        {{ auth?.user ? 'Go to Dashboard' : 'Get Started Now' }}
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </Link>
                    <p class="text-sm text-gray-300 mt-4">Join {{ settings?.total_users || '10,000+' }} users already earning daily</p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <Footer :settings="settings" />
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import Header from '../Components/Header.vue';
import Footer from '../Components/Footer.vue';

const props = defineProps({
    settings: Object,
    auth: Object
});

const currentSlide = ref(0);
const expandedStep = ref(null);

const toggleStep = (step) => {
    expandedStep.value = expandedStep.value === step ? null : step;
};

const slides = [
    {
        title: "Turn Your Data",
        highlight: "Into Cash",
        description: "Companies make billions from your browsing data. Now YOU get paid for it. Join thousands earning daily.",
        cardTitle: "Today's Earnings",
        cardValue: "₦850.00",
        cardBadge: "+42% vs yesterday",
        cardDescription: "Complete simple surveys, watch videos, and share anonymous browsing patterns to earn."
    },
    {
        title: "Build Passive",
        highlight: "Income Streams",
        description: "Refer friends and earn from their activities. Build a network across 40 levels and watch your income grow.",
        cardTitle: "Referral Earnings",
        cardValue: "₦2,450",
        cardBadge: "From 12 referrals",
        cardDescription: "Earn up to 20% commission on referral activations and continuous earnings from your network."
    },
    {
        title: "Withdraw",
        highlight: "Instantly",
        description: "Your earnings mature in 72 hours. Withdraw to your bank account, OPay, or crypto wallet anytime.",
        cardTitle: "Available Balance",
        cardValue: "₦15,300",
        cardBadge: "Ready to withdraw",
        cardDescription: "Fast, secure withdrawals with multiple payment options. No hidden fees or minimum thresholds."
    }
];

let slideInterval = null;

onMounted(() => {
    slideInterval = setInterval(() => {
        currentSlide.value = (currentSlide.value + 1) % slides.length;
    }, 5000);
});

onUnmounted(() => {
    if (slideInterval) {
        clearInterval(slideInterval);
    }
});
</script>

<style scoped>
@keyframes blob {
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    25% {
        transform: translate(20px, -50px) scale(1.1);
    }
    50% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    75% {
        transform: translate(50px, 50px) scale(1.05);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.bg-grid-pattern {
    background-image:
        linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
    background-size: 50px 50px;
}

@keyframes heartbeat {
    0%, 100% { transform: scale(1); }
    14% { transform: scale(1.05); }
    28% { transform: scale(1); }
    42% { transform: scale(1.05); }
    56% { transform: scale(1); }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

@keyframes float-delayed {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-30px); }
}

@keyframes slide-in-left {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slide-in-right {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes spin-slow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-heartbeat {
    animation: heartbeat 2s ease-in-out infinite;
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 8s ease-in-out infinite;
}

.animate-slide-in-left {
    animation: slide-in-left 0.6s ease-out;
}

.animate-slide-in-right {
    animation: slide-in-right 0.6s ease-out;
}

.animate-spin-slow {
    animation: spin-slow 20s linear infinite;
}

.animate-pulse-subtle {
    animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animation-delay-100 {
    animation-delay: 0.1s;
}

.animation-delay-200 {
    animation-delay: 0.2s;
}

.animation-delay-300 {
    animation-delay: 0.3s;
}

@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-up {
    animation: slide-up 0.6s ease-out;
}

@keyframes fade-in {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out;
}

.slide-fade-enter-active, .slide-fade-leave-active {
    transition: all 0.3s ease;
}

.slide-fade-enter-from {
    transform: translateY(-10px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateY(10px);
    opacity: 0;
}

.slide-horizontal-enter-active,
.slide-horizontal-leave-active {
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-horizontal-leave-active {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
}

.slide-horizontal-enter-from {
    transform: translateX(100%);
}

.slide-horizontal-leave-to {
    transform: translateX(-100%);
}

.expand-enter-active,
.expand-leave-active {
    transition: all 0.3s ease-out;
    max-height: 500px;
    overflow: hidden;
}

.expand-enter-from,
.expand-leave-to {
    max-height: 0;
    opacity: 0;
}
</style>
