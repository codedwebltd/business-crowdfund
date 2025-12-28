<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 relative overflow-hidden flex flex-col">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-orange-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-pink-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

            <!-- Grid Pattern -->
            <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
        </div>

        <!-- Header -->
        <Header :settings="settings" />

        <div class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8 mt-16">
            <div class="w-full max-w-md relative z-10">

                <!-- Form Card -->
                <div class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 p-8">
                    <!-- Form Header -->
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-white mb-2">
                            {{ mode === 'login' ? 'Welcome Back' : mode === 'forgot' ? 'Reset Password' : mode === 'verify-otp' ? 'Verify Code' : 'New Password' }}
                        </h1>
                        <p class="text-gray-300">
                            {{ mode === 'login' ? 'Sign in to continue your journey' : mode === 'forgot' ? (settings?.sms_notifications_enabled ? 'Enter your phone number to receive reset code' : 'Enter your email to receive reset code') : mode === 'verify-otp' ? 'Enter the verification code' : 'Create your new password' }}
                        </p>
                    </div>

                    <Transition name="slide-fade" mode="out-in" @before-leave="transitioning = true" @after-enter="transitioning = false">
                        <!-- Login Form -->
                        <form v-if="mode === 'login'" key="login" @submit.prevent="handleLogin" class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-200 mb-2">Email or Phone Number</label>
                                <input
                                    v-model="loginForm.login"
                                    type="text"
                                    required
                                    placeholder="Enter your email or phone number"
                                    class="w-full px-4 py-3.5 bg-white/10 border-2 border-white/20 rounded-xl focus:border-orange-500 focus:bg-white/20 focus:ring-2 focus:ring-orange-500/50 outline-none transition-all text-white placeholder-gray-400"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-200 mb-2">Password</label>
                                <div class="relative">
                                    <input
                                        v-model="loginForm.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        required
                                        placeholder="Enter your password"
                                        class="w-full px-4 py-3.5 bg-white/10 border-2 border-white/20 rounded-xl focus:border-orange-500 focus:bg-white/20 focus:ring-2 focus:ring-orange-500/50 outline-none transition-all text-white placeholder-gray-400 pr-12"
                                    />
                                    <button
                                        type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 hover:text-white transition-colors"
                                    >
                                        <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <label class="flex items-center gap-2 text-gray-200 cursor-pointer group">
                                    <input type="checkbox" v-model="loginForm.remember" class="w-4 h-4 text-orange-500 bg-white/10 border-white/30 rounded focus:ring-orange-500 focus:ring-2">
                                    <span class="group-hover:text-white transition-colors">Remember me</span>
                                </label>
                                <button type="button" @click="mode = 'forgot'" class="text-orange-400 hover:text-orange-300 font-semibold transition-colors">
                                    Forgot Password?
                                </button>
                            </div>

                            <button
                                type="submit"
                                :disabled="loggingIn || transitioning"
                                class="w-full bg-gradient-to-r from-orange-500 to-purple-600 text-white py-3.5 px-4 rounded-xl font-bold hover:shadow-xl hover:shadow-orange-500/50 hover:scale-[1.02] transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                            >
                                <span v-if="loggingIn">Signing In...</span>
                                <span v-else>Sign In</span>
                            </button>
                        </form>

                        <!-- Forgot Password - Phone Input -->
                        <form v-else-if="mode === 'forgot'" key="forgot" @submit.prevent="sendResetOTP" class="space-y-5">
                            <div v-if="settings?.sms_notifications_enabled">
                                <label class="block text-sm font-semibold text-gray-200 mb-2">Phone Number</label>
                                <input
                                    v-model="resetForm.phone"
                                    type="tel"
                                    required
                                    placeholder="Enter your phone number"
                                    class="w-full px-4 py-3.5 bg-white/10 border-2 border-white/20 rounded-xl focus:border-orange-500 focus:bg-white/20 focus:ring-2 focus:ring-orange-500/50 outline-none transition-all text-white placeholder-gray-400"
                                />
                            </div>
                            <div v-else>
                                <label class="block text-sm font-semibold text-gray-200 mb-2">Email Address</label>
                                <input
                                    v-model="resetForm.email"
                                    type="email"
                                    required
                                    placeholder="Enter your email address"
                                    class="w-full px-4 py-3.5 bg-white/10 border-2 border-white/20 rounded-xl focus:border-orange-500 focus:bg-white/20 focus:ring-2 focus:ring-orange-500/50 outline-none transition-all text-white placeholder-gray-400"
                                />
                            </div>

                            <button
                                type="submit"
                                :disabled="sendingOTP || transitioning"
                                class="w-full bg-gradient-to-r from-orange-500 to-purple-600 text-white py-3.5 px-4 rounded-xl font-bold hover:shadow-xl hover:shadow-orange-500/50 hover:scale-[1.02] transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                            >
                                <span v-if="sendingOTP">Sending Code...</span>
                                <span v-else>Send Reset Code</span>
                            </button>

                            <button
                                type="button"
                                @click="mode = 'login'"
                                class="w-full flex items-center justify-center gap-2 text-gray-200 hover:text-white py-3 px-4 rounded-xl border-2 border-white/20 hover:border-white/40 hover:bg-white/5 font-semibold transition-all"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Back to Login
                            </button>
                        </form>

                        <!-- Forgot Password - OTP Verification -->
                        <div v-else-if="mode === 'verify-otp'" key="verify-otp" class="space-y-6">
                            <div>
                                <p class="text-sm text-gray-300 mb-6 text-center">Enter the 6-digit code sent to {{ settings?.sms_notifications_enabled ? resetForm.phone : resetForm.email }}</p>

                                <!-- OTP Inputs -->
                                <div class="flex gap-2 justify-center max-w-sm mx-auto mb-6">
                                    <input
                                        v-for="(digit, index) in 6"
                                        :key="index"
                                        :ref="el => otpRefs[index] = el"
                                        v-model="resetOtp[index]"
                                        type="text"
                                        inputmode="numeric"
                                        maxlength="1"
                                        @input="handleOTPInput(index, $event)"
                                        @keydown="handleOTPKeydown(index, $event)"
                                        @paste="handleOTPPaste"
                                        class="w-12 h-14 text-center text-2xl font-bold bg-white/10 border-2 border-white/20 rounded-xl focus:border-orange-500 focus:bg-white/20 focus:ring-2 focus:ring-orange-500/50 outline-none transition-all text-white"
                                        :class="{ 'border-orange-500 bg-orange-500/20': resetOtp[index] }"
                                    />
                                </div>

                                <button
                                    type="button"
                                    @click="verifyResetOTP"
                                    :disabled="resetOtpCode.length !== 6 || verifying || transitioning"
                                    class="w-full bg-gradient-to-r from-orange-500 to-purple-600 text-white py-3.5 px-4 rounded-xl font-bold hover:shadow-xl hover:shadow-orange-500/50 hover:scale-[1.02] transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                                >
                                    <span v-if="verifying">Verifying...</span>
                                    <span v-else>Verify Code</span>
                                </button>

                                <div class="text-center mt-4">
                                    <button type="button" @click="resendResetOTP" class="text-sm text-orange-400 hover:text-orange-300 font-semibold transition-colors">
                                        Didn't receive code? Resend
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- New Password -->
                        <form v-else-if="mode === 'new-password'" key="new-password" @submit.prevent="resetPassword" class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-200 mb-2">New Password</label>
                                <div class="relative">
                                    <input
                                        v-model="resetForm.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        required
                                        minlength="8"
                                        placeholder="Minimum 8 characters"
                                        class="w-full px-4 py-3.5 bg-white/10 border-2 border-white/20 rounded-xl focus:border-orange-500 focus:bg-white/20 focus:ring-2 focus:ring-orange-500/50 outline-none transition-all text-white placeholder-gray-400 pr-12"
                                    />
                                    <button
                                        type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 hover:text-white transition-colors"
                                    >
                                        <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-200 mb-2">Confirm Password</label>
                                <input
                                    v-model="resetForm.password_confirmation"
                                    type="password"
                                    required
                                    minlength="8"
                                    placeholder="Re-enter your password"
                                    class="w-full px-4 py-3.5 bg-white/10 border-2 border-white/20 rounded-xl focus:border-orange-500 focus:bg-white/20 focus:ring-2 focus:ring-orange-500/50 outline-none transition-all text-white placeholder-gray-400"
                                />
                            </div>

                            <button
                                type="submit"
                                :disabled="resetting || transitioning"
                                class="w-full bg-gradient-to-r from-orange-500 to-purple-600 text-white py-3.5 px-4 rounded-xl font-bold hover:shadow-xl hover:shadow-orange-500/50 hover:scale-[1.02] transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                            >
                                <span v-if="resetting">Resetting Password...</span>
                                <span v-else>Reset Password</span>
                            </button>
                        </form>
                    </Transition>
                </div>

                <!-- Register Link -->
                <p class="text-center mt-6 text-sm text-gray-300">
                    Don't have an account?
                    <Link href="/register" class="text-orange-400 hover:text-orange-300 font-bold transition-colors ml-1">Create Account</Link>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <Footer :settings="settings" />

        <!-- Toast Container -->
        <ToastContainer />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';
import Header from '../../Components/Header.vue';
import Footer from '../../Components/Footer.vue';
import ToastContainer from '@/Components/ToastContainer.vue';

const { success, error } = useToast();

const props = defineProps({
    settings: Object
});

// Mode: 'login', 'forgot', 'verify-otp', 'new-password'
const mode = ref('login');
const showPassword = ref(false);
const transitioning = ref(false);

// Login form
const loginForm = ref({
    login: '',
    password: '',
    remember: false
});

const loggingIn = ref(false);

const handleLogin = () => {
    loggingIn.value = true;

    router.post('/login', {
        login: loginForm.value.login,
        password: loginForm.value.password,
        remember: loginForm.value.remember
    }, {
        preserveScroll: true,
        onSuccess: () => {
            success('Login successful! Welcome back.');
        },
        onError: (errors) => {
            console.error('Login errors:', errors);
            const firstErrorValue = Object.values(errors)[0];
            const firstError = Array.isArray(firstErrorValue) ? firstErrorValue[0] : firstErrorValue;
            error(firstError || 'Login failed. Please check your credentials.');
        },
        onFinish: () => {
            loggingIn.value = false;
        }
    });
};

// Password reset form
const resetForm = ref({
    phone: '',
    password: '',
    password_confirmation: ''
});

const sendingOTP = ref(false);
const verifying = ref(false);
const resetting = ref(false);

const sendResetOTP = () => {
    sendingOTP.value = true;
    setTimeout(() => {
        sendingOTP.value = false;
        mode.value = 'verify-otp';
    }, 1000);
};

// OTP handling
const resetOtp = ref(['', '', '', '', '', '']);
const otpRefs = ref([]);

const resetOtpCode = computed(() => resetOtp.value.join(''));

const handleOTPInput = (index, event) => {
    const value = event.target.value;
    if (!/^\d*$/.test(value)) {
        resetOtp.value[index] = '';
        return;
    }

    resetOtp.value[index] = value;

    if (value && index < 5) {
        otpRefs.value[index + 1]?.focus();
    }
};

const handleOTPKeydown = (index, event) => {
    if (event.key === 'Backspace' && !resetOtp.value[index] && index > 0) {
        otpRefs.value[index - 1]?.focus();
    }
};

const handleOTPPaste = (event) => {
    event.preventDefault();
    const pastedData = event.clipboardData.getData('text').slice(0, 6);
    if (!/^\d+$/.test(pastedData)) return;

    pastedData.split('').forEach((char, index) => {
        if (index < 6) {
            resetOtp.value[index] = char;
        }
    });

    const lastIndex = Math.min(pastedData.length, 5);
    otpRefs.value[lastIndex]?.focus();
};

const verifyResetOTP = () => {
    verifying.value = true;
    setTimeout(() => {
        verifying.value = false;
        mode.value = 'new-password';
    }, 1000);
};

const resendResetOTP = () => {
    resetOtp.value = ['', '', '', '', '', ''];
    otpRefs.value[0]?.focus();
    sendResetOTP();
};

const resetPassword = () => {
    if (resetForm.value.password !== resetForm.value.password_confirmation) {
        alert('Passwords do not match!');
        return;
    }

    resetting.value = true;
    setTimeout(() => {
        resetting.value = false;
        alert('Password reset successful! Please login.');
        mode.value = 'login';
    }, 1500);
};
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
