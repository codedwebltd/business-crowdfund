<template>
    <Head title="Select Your Plan" />

    <UserLayout title="Choose Your Plan">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-500/20 to-pink-500/20 backdrop-blur-xl rounded-2xl border border-purple-500/30 mb-4 shadow-lg shadow-purple-500/20">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Select Your Activation Plan</h1>
            <p class="text-gray-400">Choose a plan to unlock daily earnings and start your journey</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
                <div v-for="plan in plans" :key="plan.id"
                    class="relative group">
                    <!-- Popular Badge -->
                    <div v-if="plan.is_popular" class="absolute -top-4 left-1/2 -translate-x-1/2 z-10">
                        <span class="bg-gradient-to-r from-pink-500 to-purple-500 text-white px-4 py-1 rounded-full text-sm font-semibold shadow-lg">
                            Most Popular
                        </span>
                    </div>

                    <!-- Plan Card -->
                    <div class="relative h-full p-6 rounded-2xl backdrop-blur-xl bg-white/10 border transition-all duration-300 overflow-hidden"
                        :class="plan.is_popular ? 'border-pink-500/50 shadow-2xl shadow-pink-500/20' : 'border-white/20 hover:border-purple-500/50 hover:shadow-xl hover:shadow-white/10'">
                        <div v-if="plan.is_popular" class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-pink-500/10 to-purple-500/10 rounded-full blur-3xl"></div>

                        <div class="relative z-10 text-center mb-6">
                            <h3 class="text-xl font-bold text-white mb-3">{{ plan.display_name }}</h3>
                            <div class="mb-2">
                                <span class="text-3xl md:text-4xl font-bold text-white">{{ formatCurrency(plan.price) }}</span>
                            </div>
                            <span class="inline-block px-3 py-1 bg-purple-500/20 border border-purple-500/30 rounded-full text-xs text-purple-300">One-time activation</span>
                        </div>

                        <!-- Features -->
                        <ul class="relative z-10 space-y-2.5 mb-6">
                            <li v-for="(feature, index) in (plan.features?.feature_list || [])" :key="index" class="flex items-start gap-2.5 text-gray-300">
                                <div class="p-0.5 bg-green-500/20 rounded-full mt-0.5">
                                    <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="text-sm">{{ feature }}</span>
                            </li>
                        </ul>

                        <!-- Select Button -->
                        <button @click="selectPlan(plan)"
                            class="relative z-10 w-full py-3.5 rounded-xl font-semibold transition-all duration-300 shadow-lg"
                            :class="plan.is_popular
                                ? 'bg-gradient-to-r from-pink-500 to-purple-500 text-white hover:from-pink-600 hover:to-purple-600 shadow-pink-500/30'
                                : 'bg-white/10 hover:bg-white/20 border border-white/20 text-white'">
                            Select {{ plan.display_name }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-8 p-4 bg-blue-500/10 border border-blue-500/30 rounded-xl">
                <p class="text-center text-sm text-gray-300">
                    <svg class="w-5 h-5 text-blue-400 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    All plans include daily tasks, referral commissions, and rank progression
                </p>
            </div>
        </UserLayout>

    <!-- Payment Method Modal -->
    <PaymentMethodModal
        :show="showPaymentModal"
        :selectedPlan="selectedPlan"
        :settings="settings"
        :currencySymbol="currencySymbol"
        @close="showPaymentModal = false"
    />
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import PaymentMethodModal from '@/Components/PaymentMethodModal.vue';

const props = defineProps({
    plans: Array,
    settings: Object,
    currencySymbol: String,
});

const showPaymentModal = ref(false);
const selectedPlan = ref(null);

const formatCurrency = (amount) => {
    return (props.currencySymbol || '₦') + parseFloat(amount).toLocaleString();
};

const selectPlan = (plan) => {
    selectedPlan.value = plan;
    showPaymentModal.value = true;
};
</script>
