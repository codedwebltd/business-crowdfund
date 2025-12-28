<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 p-8">
                <h1 class="text-3xl font-bold text-white mb-4">Welcome to Dashboard</h1>

                <div class="bg-white/5 rounded-xl p-6 mb-6">
                    <h2 class="text-xl font-semibold text-white mb-4">Account Information</h2>
                    <div class="space-y-2 text-gray-300">
                        <p><strong class="text-white">Name:</strong> {{ user.full_name || 'Not set' }}</p>
                        <p><strong class="text-white">Email:</strong> {{ user.email || 'Not set' }}</p>
                        <p><strong class="text-white">Phone:</strong> {{ user.phone_number || 'Not set' }}</p>
                        <p><strong class="text-white">Referral Code:</strong> {{ user.referral_code || 'Not set' }}</p>
                        <p><strong class="text-white">Status:</strong>
                            <span :class="{
                                'text-yellow-400': user.status === 'PENDING' || user.status === 'UNVERIFIED',
                                'text-green-400': user.status === 'ACTIVE',
                                'text-red-400': user.status === 'SUSPENDED' || user.status === 'BANNED'
                            }">{{ user.status }}</span>
                        </p>
                    </div>
                </div>

                <div v-if="user.status === 'PENDING' || user.status === 'UNVERIFIED'" class="bg-orange-500/10 border border-orange-500/20 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-orange-400 mb-2">Account Activation Required</h3>
                    <p class="text-gray-300 mb-4">Your account needs to be activated before you can start earning. Please select a plan to continue.</p>
                    <Link href="/plans" class="inline-block bg-gradient-to-r from-orange-500 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:shadow-xl hover:shadow-orange-500/50 transition-all">
                        Choose Plan
                    </Link>
                </div>

                <div v-else class="bg-green-500/10 border border-green-500/20 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-green-400 mb-2">Account Active</h3>
                    <p class="text-gray-300">Your account is active and ready to earn!</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    settings: Object,
    user: Object
});
</script>
