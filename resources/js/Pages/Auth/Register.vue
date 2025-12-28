<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 relative overflow-hidden flex flex-col">
        <!-- Animated Background -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-orange-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>
            <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
        </div>

        <Header :settings="settings" />

        <div class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 mt-16">
            <div class="w-full max-w-md relative z-10">
                <div class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 p-8">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-white mb-2">Create Account</h1>
                        <p class="text-gray-300">Join thousands earning daily</p>

                        <!-- Referrer Indicator -->
                        <div v-if="referrer" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500/20 to-purple-600/20 border border-blue-400/30 rounded-full">
                            <span class="text-sm text-gray-300">Referred by</span>
                            <span class="font-bold text-white">{{ referrer.full_name }}</span>
                            <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Registration Disabled Message -->
                    <div v-if="!registrationEnabled" class="text-center py-8">
                        <div class="bg-orange-500/20 border-2 border-orange-500/50 rounded-xl p-6 mb-6">
                            <svg class="w-16 h-16 text-orange-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <h3 class="text-xl font-bold text-white mb-2">Registration Temporarily Closed</h3>
                            <p class="text-gray-300 mb-4">We're currently not accepting new registrations. Please check back later!</p>
                        </div>
                        <Link href="/login" class="inline-block bg-gradient-to-r from-orange-500 to-purple-600 text-white py-3 px-6 rounded-xl font-bold hover:shadow-xl hover:shadow-orange-500/50 transition-all">
                            Back to Login
                        </Link>
                    </div>

                    <Transition v-else name="slide-fade" mode="out-in">
                        <!-- Step 1: Phone/Email Input -->
                        <form v-if="step === 1" key="step1" @submit.prevent="sendOTP" class="space-y-5">
                            <div v-if="smsEnabled">
                                <label class="block text-sm font-semibold text-gray-200 mb-2">Phone Number</label>
                                <vue-tel-input
                                    v-model="form.phone_number"
                                    mode="international"
                                    :inputOptions="{
                                        placeholder: 'Enter your phone number',
                                        styleClasses: 'w-full px-4 py-3.5 bg-white/10 border-2 border-white/20 rounded-xl focus:border-orange-500 focus:bg-white/20 focus:ring-2 focus:ring-orange-500/50 outline-none transition-all text-white placeholder-gray-400'
                                    }"
                                    :dropdownOptions="{
                                        showDialCodeInSelection: true,
                                        showFlags: true,
                                        showSearchBox: true
                                    }"
                                    @validate="onPhoneValidate"
                                ></vue-tel-input>
                            </div>

                            <div v-else>
                                <label class="block text-sm font-semibold text-gray-200 mb-2">Email Address</label>
                                <input v-model="form.email" type="email" required placeholder="you@example.com"
                                    class="w-full px-4 py-3.5 bg-white/10 border-2 border-white/20 rounded-xl focus:border-orange-500 focus:bg-white/20 focus:ring-2 focus:ring-orange-500/50 outline-none transition-all text-white placeholder-gray-400" />
                            </div>

                            <!-- Referrer Card -->
                            <div v-if="form.referral_code && referrer" class="bg-gradient-to-br from-orange-500/20 to-purple-600/20 backdrop-blur-xl border-2 border-orange-500/30 rounded-2xl p-6 shadow-lg">
                                <div class="flex items-center gap-4">
                                    <!-- Avatar SVG -->
                                    <div class="flex-shrink-0">
                                        <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-purple-500 rounded-full flex items-center justify-center shadow-lg">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Referrer Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-white font-bold text-lg truncate">{{ referrer.full_name || 'Referrer' }}</p>
                                            <!-- Verified Badge -->
                                            <svg class="w-5 h-5 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-orange-400 to-purple-500 text-white shadow-md">
                                                {{ referrer.rank || 'Member' }}
                                            </span>
                                            <span class="text-gray-300 text-sm">Invited you</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" :disabled="processing || !isStep1Valid"
                                class="w-full bg-gradient-to-r from-orange-500 to-purple-600 text-white py-3.5 px-4 rounded-xl font-bold hover:shadow-xl hover:shadow-orange-500/50 hover:scale-[1.02] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <span v-if="processing">Sending Code...</span>
                                <span v-else>Continue</span>
                            </button>
                        </form>

                        <!-- Step 2: OTP Verification -->
                        <form v-else-if="step === 2" key="step2" @submit.prevent="verifyOTP" class="space-y-5">
                            <div class="text-center mb-4">
                                <p class="text-gray-300 text-sm">Enter the 6-digit code sent to</p>
                                <p class="text-white font-semibold">{{ smsEnabled ? form.phone_number : form.email }}</p>
                            </div>

                            <div>
                                <input v-model="form.otp_code" type="text" maxlength="6" placeholder="000000" required
                                    class="w-full px-4 py-3.5 bg-white/10 border-2 border-white/20 rounded-xl focus:border-orange-500 focus:bg-white/20 focus:ring-2 focus:ring-orange-500/50 outline-none transition-all text-white text-center text-2xl tracking-widest placeholder-gray-400" />
                            </div>

                            <button type="submit" :disabled="processing || form.otp_code.length !== 6"
                                class="w-full bg-gradient-to-r from-orange-500 to-purple-600 text-white py-3.5 px-4 rounded-xl font-bold hover:shadow-xl hover:shadow-orange-500/50 hover:scale-[1.02] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <span v-if="processing">Verifying...</span>
                                <span v-else>Verify Code</span>
                            </button>

                            <button type="button" @click="resendOTP" :disabled="processing || resendCooldown > 0"
                                class="w-full text-orange-400 hover:text-orange-300 font-semibold transition-colors text-sm py-2">
                                {{ resendCooldown > 0 ? `Resend in ${resendCooldown}s` : 'Resend Code' }}
                            </button>
                        </form>

                        <!-- Step 3: Complete Registration -->
                        <form v-else-if="step === 3" key="step3" @submit.prevent="completeRegistration" class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-200 mb-2">Full Name</label>
                                <input v-model="form.full_name" type="text" required placeholder="John Doe"
                                    class="w-full px-4 py-3.5 bg-white/10 border-2 border-white/20 rounded-xl focus:border-orange-500 focus:bg-white/20 focus:ring-2 focus:ring-orange-500/50 outline-none transition-all text-white placeholder-gray-400" />
                            </div>

                            <div v-if="smsEnabled">
                                <label class="block text-sm font-semibold text-gray-200 mb-2">Email (Optional)</label>
                                <input v-model="form.email" type="email" placeholder="you@example.com"
                                    class="w-full px-4 py-3.5 bg-white/10 border-2 border-white/20 rounded-xl focus:border-orange-500 focus:bg-white/20 focus:ring-2 focus:ring-orange-500/50 outline-none transition-all text-white placeholder-gray-400" />
                            </div>

                            <div v-else>
                                <label class="block text-sm font-semibold text-gray-200 mb-2">Phone Number</label>
                                <vue-tel-input
                                    v-model="form.phone_number"
                                    mode="international"
                                    :inputOptions="{
                                        placeholder: 'Enter your phone number',
                                        styleClasses: 'w-full px-4 py-3.5 bg-white/10 border-2 border-white/20 rounded-xl focus:border-orange-500 focus:bg-white/20 focus:ring-2 focus:ring-orange-500/50 outline-none transition-all text-white placeholder-gray-400'
                                    }"
                                    :dropdownOptions="{
                                        showDialCodeInSelection: true,
                                        showFlags: true
                                    }"
                                ></vue-tel-input>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-200 mb-2">Date of Birth</label>
                                <input v-model="form.date_of_birth" type="date" required
                                    class="w-full px-4 py-3.5 bg-white/10 border-2 border-white/20 rounded-xl focus:border-orange-500 focus:bg-white/20 focus:ring-2 focus:ring-orange-500/50 outline-none transition-all text-white" />
                                <p class="text-gray-400 text-xs mt-1">Must be 18+ years old</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-200 mb-2">Password</label>
                                <input v-model="form.password" type="password" required placeholder="••••••••"
                                    class="w-full px-4 py-3.5 bg-white/10 border-2 border-white/20 rounded-xl focus:border-orange-500 focus:bg-white/20 focus:ring-2 focus:ring-orange-500/50 outline-none transition-all text-white placeholder-gray-400" />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-200 mb-2">Confirm Password</label>
                                <input v-model="form.password_confirmation" type="password" required placeholder="••••••••"
                                    class="w-full px-4 py-3.5 bg-white/10 border-2 border-white/20 rounded-xl focus:border-orange-500 focus:bg-white/20 focus:ring-2 focus:ring-orange-500/50 outline-none transition-all text-white placeholder-gray-400" />
                            </div>

                            <button type="submit" :disabled="processing"
                                class="w-full bg-gradient-to-r from-orange-500 to-purple-600 text-white py-3.5 px-4 rounded-xl font-bold hover:shadow-xl hover:shadow-orange-500/50 hover:scale-[1.02] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <span v-if="processing">Creating Account...</span>
                                <span v-else>Complete Registration</span>
                            </button>
                        </form>
                    </Transition>

                    <div class="mt-6 text-center">
                        <Link href="/login" class="text-gray-300 hover:text-white transition-colors text-sm">
                            Already have an account? <span class="font-semibold text-orange-400">Sign In</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <Footer :settings="settings" />
        <ToastContainer />
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { VueTelInput } from 'vue-tel-input';
import 'vue-tel-input/vue-tel-input.css';
import Header from '@/Components/Header.vue';
import Footer from '@/Components/Footer.vue';
import ToastContainer from '@/Components/ToastContainer.vue';
import { useToast } from '@/composables/useToast';

const { success, error, warning } = useToast();
const page = usePage();

const props = defineProps({
    referralCode: String,
    smsEnabled: Boolean,
    appName: String,
    settings: Object,
    registrationEnabled: {
        type: Boolean,
        default: true
    },
});

const step = ref(1);
const processing = ref(false);
const referrer = ref(null);
const userId = ref(null);
const resendCooldown = ref(0);
const phoneValid = ref(false);

const form = reactive({
    phone_number: '',
    email: '',
    otp_code: '',
    referral_code: props.referralCode || '',
    full_name: '',
    date_of_birth: '',
    password: '',
    password_confirmation: '',
});

const maxDate = computed(() => {
    const date = new Date();
    date.setFullYear(date.getFullYear() - 18);
    return date.toISOString().split('T')[0];
});

const isStep1Valid = computed(() => {
    if (props.smsEnabled) {
        return phoneValid.value && form.phone_number && form.phone_number.length >= 10;
    }
    // Email validation - must be valid format
    if (!form.email || !form.email.includes('@')) return false;

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(form.email)) return false;

    // Check for common typos in popular domains
    const email = form.email.toLowerCase();
    const commonTypos = {
        'gmial': 'gmail',
        'gmai': 'gmail',
        'gmali': 'gmail',
        'yahooo': 'yahoo',
        'yaho': 'yahoo',
        'hotmial': 'hotmail',
        'outlok': 'outlook'
    };

    const domain = email.split('@')[1];
    if (domain) {
        const domainName = domain.split('.')[0];
        if (commonTypos[domainName]) {
            error(`Did you mean ${form.email.replace(domainName, commonTypos[domainName])}?`);
            return false;
        }
    }

    return true;
});

const onPhoneValidate = (validation) => {
    phoneValid.value = validation.valid;
};

onMounted(async () => {
    if (props.referralCode) {
        try {
            const res = await fetch(`/api/referrer/${props.referralCode}`);
            if (res.ok) referrer.value = await res.json();
        } catch (e) {}
    }
});

const sendOTP = () => {
    processing.value = true;
    router.post('/register', {
        phone_number: props.smsEnabled ? form.phone_number : null,
        email: !props.smsEnabled ? form.email : null,
        referral_code: form.referral_code || null,
    }, {
        onSuccess: (page) => {
            if (page.props.user_id) {
                userId.value = page.props.user_id;
                step.value = 2;
                startResendCooldown();
                success('Verification code sent successfully!');
            }
        },
        onError: (errors) => {
            const firstErrorValue = Object.values(errors)[0];
            const firstError = Array.isArray(firstErrorValue) ? firstErrorValue[0] : firstErrorValue;
            error(firstError || 'Failed to send verification code');
        },
        onFinish: () => processing.value = false,
    });
};

const verifyOTP = () => {
    processing.value = true;
    router.post('/register/verify-otp', {
        user_id: userId.value,
        otp_code: form.otp_code,
    }, {
        onSuccess: () => {
            step.value = 3;
            success('Phone verified! Complete your registration');
        },
        onError: (errors) => {
            const firstErrorValue = Object.values(errors)[0];
            const firstError = Array.isArray(firstErrorValue) ? firstErrorValue[0] : firstErrorValue;
            error(firstError || 'Invalid verification code');
        },
        onFinish: () => processing.value = false,
    });
};

const resendOTP = () => {
    processing.value = true;
    router.post('/register/resend-otp', {
        user_id: userId.value,
    }, {
        preserveUrl: true,
        preserveState: true,
        onSuccess: () => {
            startResendCooldown();
            success('New verification code sent!');
        },
        onError: (errors) => {
            const firstErrorValue = Object.values(errors)[0];
            const firstError = Array.isArray(firstErrorValue) ? firstErrorValue[0] : firstErrorValue;
            error(firstError || 'Failed to resend code');
        },
        onFinish: () => processing.value = false,
    });
};

const completeRegistration = () => {
    processing.value = true;
    router.post('/register', {
        user_id: userId.value,
        phone_number: form.phone_number,
        full_name: form.full_name,
        email: form.email || null,
        date_of_birth: form.date_of_birth,
        password: form.password,
        password_confirmation: form.password_confirmation,
        referral_code: form.referral_code || null,
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            success('Account created successfully! Redirecting...');
        },
        onError: (errors) => {
            console.error('Registration errors:', errors);
            const firstErrorValue = Object.values(errors)[0];
            const firstError = Array.isArray(firstErrorValue) ? firstErrorValue[0] : firstErrorValue;
            error(firstError || 'Registration failed. Please try again');
        },
        onFinish: () => processing.value = false,
    });
};

const startResendCooldown = () => {
    resendCooldown.value = 60;
    const interval = setInterval(() => {
        resendCooldown.value--;
        if (resendCooldown.value <= 0) clearInterval(interval);
    }, 1000);
};
</script>

<style scoped>
@keyframes blob {
    0%, 100% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
}

.animate-blob { animation: blob 7s infinite; }
.animation-delay-2000 { animation-delay: 2s; }
.animation-delay-4000 { animation-delay: 4s; }

.bg-grid-pattern {
    background-image:
        linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
    background-size: 50px 50px;
}

.slide-fade-enter-active, .slide-fade-leave-active {
    transition: all 0.3s ease;
}

.slide-fade-enter-from {
    transform: translateX(20px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateX(-20px);
    opacity: 0;
}
</style>
