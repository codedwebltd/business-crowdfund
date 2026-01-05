<template>
  <UserLayout>
    <div class="space-y-6">
      <!-- Header Card -->
        <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
          <div class="bg-gradient-to-r from-orange-500 to-purple-600 p-6">
            <div class="flex items-center gap-3">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white">Withdraw Funds</h1>
                <p class="text-white/80 text-sm">Convert your earnings to cash or crypto</p>
              </div>
            </div>
          </div>

          <!-- Balance Display -->
          <div class="p-8 bg-gradient-to-br from-orange-500/10 via-purple-600/10 to-transparent">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div class="space-y-2">
                <p class="text-gray-400 text-sm font-medium uppercase tracking-wide mb-2">Withdrawable Balance</p>
                <p class="text-4xl md:text-5xl font-bold text-white">
                  {{ formatMoney(user.wallet?.withdrawable_balance || 0) }}
                </p>
                <p class="text-sm text-gray-500">Ready for withdrawal</p>
              </div>
              <div class="space-y-2">
                <p class="text-gray-400 text-sm font-medium uppercase tracking-wide mb-2">Pending Balance</p>
                <p class="text-3xl md:text-4xl font-bold text-orange-400">
                  {{ formatMoney(user.wallet?.pending_balance || 0) }}
                </p>
                <p class="text-sm text-gray-500">Matures in {{ settings.pending_balance_maturation_hours }}hrs</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Alerts Section -->
        <div v-if="(testimonialRequired && user.wallet?.withdrawable_balance > 0) || kycRequired || !canWithdrawToday" class="space-y-3">
          <!-- Testimonial Required or Pending -->
          <div v-if="testimonialRequired && user.wallet?.withdrawable_balance > 0" :class="[
            'rounded-lg p-4',
            hasPendingTestimonial
              ? 'bg-purple-500/10 border border-purple-500/30'
              : 'bg-yellow-500/10 border border-yellow-500/30'
          ]">
            <div class="flex items-start gap-3">
              <!-- Animated Spinner for Pending Testimonial -->
              <div v-if="hasPendingTestimonial" class="flex-shrink-0 mt-0.5">
                <div class="relative w-6 h-6">
                  <svg class="w-6 h-6 text-purple-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-2 h-2 bg-purple-400 rounded-full animate-pulse"></div>
                  </div>
                </div>
              </div>
              <!-- Regular icon for not submitted -->
              <svg v-else class="w-6 h-6 text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>

              <div class="flex-1">
                <!-- Pending Testimonial -->
                <div v-if="hasPendingTestimonial">
                  <p class="text-purple-400 font-semibold mb-1 flex items-center gap-2">
                    <span>Reviewing your testimonial</span>
                    <span class="inline-flex gap-0.5">
                      <span class="w-1.5 h-1.5 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                      <span class="w-1.5 h-1.5 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                      <span class="w-1.5 h-1.5 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                    </span>
                  </p>
                  <p class="text-gray-300 text-sm mb-2">
                    Your testimonial is under review. We'll notify you once it's approved!
                  </p>
                  <div class="bg-purple-500/20 rounded-lg p-3 border border-purple-500/30">
                    <p class="text-purple-300 text-xs">
                      ⏱️ <strong>Estimated review time:</strong> 12-24 hours<br/>
                      📧 You'll receive a notification once approved
                    </p>
                  </div>
                </div>

                <!-- Not Submitted Yet -->
                <div v-else>
                  <p class="text-yellow-400 font-semibold mb-1">One-Time Testimonial Required</p>
                  <p class="text-gray-300 text-sm mb-3">We'd love to hear about your experience! Share a quick testimonial to unlock withdrawals. Don't worry—this is just a one-time thing and won't be required for future withdrawals. 😊</p>
                  <button @click="showTestimonialModal = true" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-semibold rounded-lg transition">
                    Submit Testimonial
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- KYC Approved Badge -->
          <div v-if="user.latest_kyc?.status === 'APPROVED'" class="bg-gradient-to-r from-green-500/10 to-emerald-500/10 border border-green-500/30 rounded-lg p-4">
            <div class="flex items-start gap-3">
              <div class="relative flex-shrink-0">
                <!-- Instagram-style verification badge -->
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center shadow-lg shadow-green-500/50">
                  <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <!-- Pulse animation -->
                <div class="absolute inset-0 rounded-full bg-green-400 animate-ping opacity-20"></div>
              </div>
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                  <p class="text-green-400 font-bold text-lg">Verified Account</p>
                  <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <p class="text-gray-300 text-sm mb-2">
                  Your identity has been verified. You can now make withdrawals without restrictions.
                </p>
                <div class="bg-green-500/20 rounded-lg p-2 border border-green-500/30 inline-block">
                  <p class="text-green-300 text-xs">
                    ✓ Verified on {{ new Date(user.kyc_verified_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- KYC Required or Pending -->
          <div v-else-if="kycRequired" :class="[
            'rounded-lg p-4',
            user.latest_kyc?.status === 'PENDING'
              ? 'bg-blue-500/10 border border-blue-500/30'
              : user.latest_kyc?.status === 'REJECTED'
              ? 'bg-orange-500/10 border border-orange-500/30'
              : 'bg-red-500/10 border border-red-500/30'
          ]">
            <div class="flex items-start gap-3">
              <!-- Animated Spinner for Pending KYC -->
              <div v-if="user.latest_kyc?.status === 'PENDING'" class="flex-shrink-0 mt-0.5">
                <div class="relative w-6 h-6">
                  <!-- Spinning outer ring -->
                  <svg class="w-6 h-6 text-blue-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <!-- Pulsing inner circle -->
                  <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                  </div>
                </div>
              </div>
              <!-- Regular icon for not submitted -->
              <svg v-else class="w-6 h-6 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>

              <div class="flex-1">
                <!-- Pending KYC Message -->
                <div v-if="user.latest_kyc?.status === 'PENDING'">
                  <p class="text-blue-400 font-semibold mb-1 flex items-center gap-2">
                    <span>Verifying your KYC</span>
                    <span class="inline-flex gap-0.5">
                      <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                      <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                      <span class="w-1.5 h-1.5 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                    </span>
                  </p>
                  <p class="text-gray-300 text-sm mb-2">
                    Automatic approval is taking some time due to massive approval list surge. We will ensure your request is processed shortly.
                  </p>
                  <div class="bg-blue-500/20 rounded-lg p-3 border border-blue-500/30">
                    <p class="text-blue-300 text-xs">
                      ⏱️ <strong>Estimated processing time:</strong> 24-48 hours<br/>
                      📧 You'll receive an email notification once verified
                    </p>
                  </div>
                </div>

                <!-- Rejected KYC - Resubmit -->
                <div v-else-if="user.latest_kyc?.status === 'REJECTED'">
                  <p class="text-red-400 font-semibold mb-1">KYC Verification Rejected</p>
                  <p class="text-gray-300 text-sm mb-2">
                    {{ user.latest_kyc.rejection_reason || 'Your KYC was rejected. Please resubmit with correct documents.' }}
                  </p>
                  <button @click="showKycModal = true" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Resubmit KYC
                  </button>
                </div>

                <!-- Not Submitted Yet -->
                <div v-else>
                  <p class="text-red-400 font-semibold mb-1">KYC Verification Required</p>
                  <p class="text-gray-300 text-sm mb-3">
                    {{ settings.enable_kyc_on_first_withdrawal ? 'KYC verification is required for your first withdrawal.' : `For withdrawals above ${formatMoney(settings.kyc_withdrawal_threshold)}, you need to complete KYC verification.` }}
                  </p>
                  <button @click="showKycModal = true" class="inline-block px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition">
                    Complete KYC Verification
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Daily Limit Reached -->
          <div v-if="!canWithdrawToday" class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
            <div class="flex items-start gap-3">
              <svg class="w-6 h-6 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="flex-1">
                <p class="text-blue-400 font-semibold mb-1">Daily Limit Reached</p>
                <p class="text-gray-300 text-sm">You've reached your daily withdrawal limit ({{ todayWithdrawals }}/{{ limits.per_day }}). Come back tomorrow!</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Withdrawal Form -->
        <div v-if="!testimonialRequired && !kycRequired && canWithdrawToday" class="space-y-6">

          <!-- Amount Input Card -->
          <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-purple-600 p-4">
              <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Withdrawal Amount
              </h2>
            </div>

            <div class="p-8">
            <!-- Amount Input -->
            <div class="mb-6">
              <label class="block text-sm font-semibold text-gray-300 mb-3">Enter Amount ({{ settings.platform_currency }})</label>
              <input
                v-model.number="withdrawalForm.amount"
                type="number"
                :min="limits.min"
                :max="Math.min(limits.max, user.wallet?.withdrawable_balance || 0)"
                placeholder="0.00"
                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white text-2xl font-bold focus:outline-none focus:ring-2 focus:ring-orange-500"
              />
              <div class="flex justify-between text-xs text-gray-400 mt-2">
                <span>Min: {{ formatMoney(limits.min) }}</span>
                <span>Max: {{ formatMoney(Math.min(limits.max, user.wallet?.withdrawable_balance || 0)) }}</span>
              </div>
            </div>

            <!-- Quick Amount Buttons -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
              <button
                v-for="preset in presetAmounts"
                :key="preset"
                @click="withdrawalForm.amount = preset"
                class="px-4 py-3 bg-white/10 hover:bg-orange-500/20 border border-white/20 hover:border-orange-500/50 rounded-lg text-white font-semibold transition"
              >
                {{ formatMoney(preset) }}
              </button>
            </div>

            <!-- Token Conversion Preview -->
            <div v-if="withdrawalForm.amount > 0" class="bg-gradient-to-r from-purple-500/20 to-orange-500/20 rounded-xl p-6 border border-purple-500/30">
              <div class="flex items-center justify-between mb-3">
                <span class="text-gray-300 text-sm">Token Conversion</span>
                <span class="text-xs bg-purple-500/30 text-purple-300 px-2 py-1 rounded-full">CROW Token</span>
              </div>

              <div class="space-y-2 text-sm">
                <!-- Original Amount -->
                <div class="flex justify-between">
                  <span class="text-gray-400">Requested Amount:</span>
                  <span class="text-white font-semibold">{{ formatMoney(withdrawalForm.amount) }}</span>
                </div>

                <!-- Token Price -->
                <div class="flex justify-between">
                  <span class="text-gray-400">Token Price (1 CROW):</span>
                  <span class="text-white font-semibold">{{ formatMoney(tokenPrice) }}</span>
                </div>

                <!-- Tokens Required -->
                <div class="flex justify-between">
                  <span class="text-gray-400">Tokens Required:</span>
                  <span class="text-orange-400 font-bold">{{ tokensRequired.toFixed(4) }} CROW</span>
                </div>

                <!-- Withdrawal Rate -->
                <div v-if="withdrawalRate !== 1" class="flex justify-between">
                  <span class="text-gray-400">Withdrawal Rate:</span>
                  <span class="text-yellow-400 font-semibold">{{ (withdrawalRate * 100).toFixed(0) }}%</span>
                </div>

                <div class="border-t border-white/10 my-2"></div>

                <!-- Final Amount -->
                <div class="flex justify-between text-base">
                  <span class="text-gray-300 font-semibold">You'll Receive:</span>
                  <span class="text-green-400 font-bold text-lg">{{ formatMoney(finalAmount) }}</span>
                </div>

                <!-- Crypto Value in USDT -->
                <div v-if="cryptoValueUSDT !== null" class="flex justify-between items-center bg-green-500/10 border border-green-500/30 rounded-lg p-3 mt-3">
                  <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-gray-300 font-semibold">Crypto Value (USDT):</span>
                  </div>
                  <span class="text-green-400 font-bold text-lg">{{ cryptoValueUSDT.toFixed(2) }} USDT</span>
                </div>
              </div>
            </div>
          </div>
          </div>

          <!-- Payment Method Selection -->
          <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-purple-600 p-4">
              <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                Select Payment Method
              </h2>
            </div>

            <div class="p-8">
              <div class="space-y-4">
              <!-- Bank Transfer -->
              <label
                :class="[
                  'relative flex items-center gap-4 p-6 rounded-xl border-2 cursor-pointer transition',
                  withdrawalForm.method === 'bank'
                    ? 'border-orange-500 bg-orange-500/10'
                    : 'border-white/20 bg-white/5 hover:border-white/40',
                  !bankEnabled && 'opacity-50 cursor-not-allowed'
                ]"
              >
                <input
                  type="radio"
                  v-model="withdrawalForm.method"
                  value="bank"
                  :disabled="!bankEnabled || !hasBankDetails"
                  class="sr-only"
                />
                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="text-white font-semibold">Bank Transfer</p>
                  <p v-if="hasBankDetails" class="text-sm text-gray-400 mt-1">
                    {{ user.bank_name }} - {{ user.account_number }}
                  </p>
                  <div v-else class="mt-2 space-y-2">
                    <Link href="/settings" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-purple-600 hover:from-orange-600 hover:to-purple-700 text-white text-sm font-semibold rounded-lg transition shadow-md">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                      </svg>
                      Add Bank Details
                    </Link>
                    <div v-if="!bankEnabled" class="flex items-center gap-2 text-red-400 text-xs">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>Under maintenance - temporarily unavailable</span>
                    </div>
                    <div v-else-if="!hasBankDetails" class="flex items-center gap-2 text-yellow-400 text-xs">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>Add your bank details to enable this option</span>
                    </div>
                  </div>
                </div>
              </label>

              <!-- Crypto Transfer -->
              <label
                :class="[
                  'relative flex items-center gap-4 p-6 rounded-xl border-2 cursor-pointer transition',
                  withdrawalForm.method === 'crypto'
                    ? 'border-orange-500 bg-orange-500/10'
                    : 'border-white/20 bg-white/5 hover:border-white/40',
                  !cryptoEnabled && 'opacity-50 cursor-not-allowed'
                ]"
              >
                <input
                  type="radio"
                  v-model="withdrawalForm.method"
                  value="crypto"
                  :disabled="!cryptoEnabled || !hasCryptoDetails"
                  class="sr-only"
                />
                <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                  <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="text-white font-semibold">Crypto (USDT)</p>
                  <p v-if="hasCryptoDetails" class="text-sm text-gray-400 mt-1">
                    {{ user.wallet_details.coin_name }} ({{ user.wallet_details.coin_network }})
                  </p>
                  <div v-else class="mt-2 space-y-2">
                    <Link href="/settings" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-purple-600 hover:from-orange-600 hover:to-purple-700 text-white text-sm font-semibold rounded-lg transition shadow-md">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                      </svg>
                      Add Crypto Wallet
                    </Link>
                    <div v-if="!cryptoEnabled" class="flex items-center gap-2 text-red-400 text-xs">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>Under maintenance - temporarily unavailable</span>
                    </div>
                    <div v-else-if="!hasCryptoDetails" class="flex items-center gap-2 text-yellow-400 text-xs">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span>Add your crypto wallet details to enable this option</span>
                    </div>
                  </div>
                </div>
              </label>
              </div>
            </div>
          </div>

          <!-- 2FA Verification (if enabled) -->
          <div v-if="twoFactorEnabled" class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-purple-600 p-4">
              <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Two-Factor Authentication
              </h3>
            </div>
            <div class="p-6">
              <label class="text-sm font-semibold text-gray-300 mb-2 block">
                Enter 6-digit code from your authenticator app
              </label>
              <input
                v-model="withdrawalForm.two_factor_code"
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="6"
                placeholder="000000"
                class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white text-center text-2xl font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-orange-500"
              />
              <p class="text-xs text-gray-400 mt-2">Open your authenticator app (Google Authenticator, Authy, etc.) to get the code</p>
            </div>
          </div>

          <!-- Submit Button -->
          <button
            @click="submitWithdrawal"
            :disabled="!canSubmit || loading"
            :class="[
              'w-full py-5 rounded-xl font-bold text-xl transition shadow-lg',
              canSubmit && !loading
                ? 'bg-gradient-to-r from-orange-500 to-purple-600 hover:from-orange-600 hover:to-purple-700 text-white'
                : 'bg-gray-600 text-gray-400 cursor-not-allowed'
            ]"
          >
            <span v-if="loading">Processing...</span>
            <span v-else>Withdraw {{ formatMoney(withdrawalForm.amount || 0) }}</span>
          </button>
        </div>

      </div>

    <!-- Testimonial Modal -->
    <div v-if="showTestimonialModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-gray-800 rounded-2xl max-w-lg w-full p-6 border border-gray-700">
          <h3 class="text-2xl font-bold text-white mb-4">Share Your Experience</h3>
          <p class="text-gray-400 text-sm mb-6">Tell us about your experience with {{ settings.app_name }}. Your testimonial will be reviewed before approval.</p>

          <textarea
            v-model="testimonialForm.message"
            rows="6"
            placeholder="Share your thoughts about earning with our platform..."
            class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-orange-500 mb-4"
          ></textarea>

          <div class="flex gap-3">
            <button
              @click="showTestimonialModal = false"
              class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white font-semibold rounded-lg transition"
            >
              Cancel
            </button>
            <button
              @click="submitTestimonial"
              :disabled="!testimonialForm.message || loading"
              class="flex-1 px-4 py-3 bg-gradient-to-r from-orange-500 to-purple-600 hover:from-orange-600 hover:to-purple-700 text-white font-semibold rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Submit
            </button>
          </div>
        </div>
    </div>

    <!-- KYC Upload Modal -->
    <KYCUploadModal
      :show="showKycModal"
      :kyc-requirements="settings.kyc_requirements || {}"
      :user-info="{
        full_name: user.full_name,
        email: user.email,
        date_of_birth: user.date_of_birth,
        nin: user.nin,
        bvn: user.bvn
      }"
      :existing-kyc="user.latest_kyc"
      @close="showKycModal = false"
      @submitted="handleKycSubmitted"
    />
  </UserLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import KYCUploadModal from '@/Components/KYCUploadModal.vue';
import Swal from 'sweetalert2';
import axios from 'axios';

const props = defineProps({
  user: Object,
  settings: Object,
  limits: Object,
  hasBankDetails: Boolean,
  hasCryptoDetails: Boolean,
  kycRequired: Boolean,
  testimonialRequired: Boolean,
  canWithdrawToday: Boolean,
  todayWithdrawals: Number,
  tokenPrice: Number,
  withdrawalRate: Number,
  bankEnabled: Boolean,
  cryptoEnabled: Boolean,
  twoFactorEnabled: Boolean,
});

const loading = ref(false);
const showTestimonialModal = ref(false);
const showKycModal = ref(false);
const cryptoValueUSDT = ref(null);
const loadingCryptoRate = ref(false);
const cachedRate = ref(null);
const cacheTimestamp = ref(null);
const fetchTimeout = ref(null);

const withdrawalForm = ref({
  amount: null,
  method: props.cryptoEnabled && props.hasCryptoDetails ? 'crypto' : 'bank',
  two_factor_code: '',
});

const testimonialForm = ref({
  message: '',
});

// Check if user has pending testimonial
const hasPendingTestimonial = computed(() => {
  return props.user.testimonials?.some(t => t.status === 'PENDING') || false;
});

// Preset amounts based on limits
const presetAmounts = computed(() => {
  const max = Math.min(props.limits.max, props.user.wallet?.withdrawable_balance || 0);
  const step = Math.floor(max / 4);
  return [
    Math.max(props.limits.min, step),
    Math.max(props.limits.min, step * 2),
    Math.max(props.limits.min, step * 3),
    Math.max(props.limits.min, max),
  ].filter((v, i, a) => a.indexOf(v) === i); // Remove duplicates
});

// Token calculations
const tokensRequired = computed(() => {
  if (!withdrawalForm.value.amount || !props.tokenPrice) return 0;
  return withdrawalForm.value.amount / props.tokenPrice;
});

const finalAmount = computed(() => {
  if (!withdrawalForm.value.amount) return 0;
  return withdrawalForm.value.amount * props.withdrawalRate;
});

const canSubmit = computed(() => {
  return (
    withdrawalForm.value.amount >= props.limits.min &&
    withdrawalForm.value.amount <= Math.min(props.limits.max, props.user.wallet?.withdrawable_balance || 0) &&
    withdrawalForm.value.method &&
    ((withdrawalForm.value.method === 'bank' && props.hasBankDetails && props.bankEnabled) ||
     (withdrawalForm.value.method === 'crypto' && props.hasCryptoDetails && props.cryptoEnabled))
  );
});

// Check if cached rate is still valid (60 seconds cache)
const isCacheValid = () => {
  if (!cacheTimestamp.value) return false;
  const now = Date.now();
  const cacheAge = now - cacheTimestamp.value;
  return cacheAge < 60000; // 60 seconds cache
};

// Fetch real-time crypto value when amount changes
const fetchCryptoValue = async (amount) => {
  if (!amount || amount <= 0) {
    cryptoValueUSDT.value = null;
    return;
  }

  // Use cached rate if valid
  if (isCacheValid() && cachedRate.value) {
    cryptoValueUSDT.value = amount / cachedRate.value;
    return;
  }

  loadingCryptoRate.value = true;
  try {
    // Fetch real-time USDT rate from CoinGecko API
    const currencyCode = props.settings.platform_currency || 'NGN';
    const response = await axios.get('https://api.coingecko.com/api/v3/simple/price', {
      params: {
        ids: 'tether',
        vs_currencies: currencyCode.toLowerCase(),
      },
      timeout: 10000,
    });

    if (response.data && response.data.tether) {
      const rate = response.data.tether[currencyCode.toLowerCase()];
      if (rate && rate > 0) {
        // Cache the rate
        cachedRate.value = rate;
        cacheTimestamp.value = Date.now();
        cryptoValueUSDT.value = amount / rate;
      } else {
        cryptoValueUSDT.value = null;
      }
    }
  } catch (error) {
    console.error('Failed to fetch crypto rate:', error);
    // If fetch fails but we have a cached rate, use it
    if (cachedRate.value) {
      cryptoValueUSDT.value = amount / cachedRate.value;
    } else {
      cryptoValueUSDT.value = null;
    }
  } finally {
    loadingCryptoRate.value = false;
  }
};

// Debounced fetch for better performance
const debouncedFetchCryptoValue = (amount) => {
  // Clear existing timeout
  if (fetchTimeout.value) {
    clearTimeout(fetchTimeout.value);
  }

  // If cache is valid, update immediately
  if (isCacheValid() && cachedRate.value && amount > 0) {
    cryptoValueUSDT.value = amount / cachedRate.value;
    return;
  }

  // Otherwise debounce the API call
  fetchTimeout.value = setTimeout(() => {
    fetchCryptoValue(amount);
  }, 300); // 300ms debounce
};

// Watch for amount changes and fetch crypto value
watch(() => finalAmount.value, (newAmount) => {
  debouncedFetchCryptoValue(newAmount);
}, { immediate: true });

const formatMoney = (amount) => {
  return new Intl.NumberFormat('en-NG', {
    style: 'currency',
    currency: 'NGN',
    minimumFractionDigits: 0,
  }).format(amount);
};

const submitWithdrawal = () => {
  loading.value = true;
  router.post('/withdrawal', withdrawalForm.value, {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Withdrawal Requested!',
        text: 'Your withdrawal has been submitted and is being processed.',
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#10b981',
      });
      withdrawalForm.value.amount = null;
      withdrawalForm.value.two_factor_code = '';
    },
    onError: (errors) => {
      const errorMsg = Object.values(errors)[0] || 'Failed to process withdrawal';
      Swal.fire({
        icon: 'error',
        title: 'Withdrawal Failed',
        text: errorMsg,
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#ef4444',
      });
    },
    onFinish: () => loading.value = false,
  });
};

const handleKycSubmitted = () => {
  showKycModal.value = false;
  Swal.fire({
    icon: 'success',
    title: 'KYC Submitted!',
    text: 'Your KYC verification has been submitted for review. Refreshing page...',
    background: '#1f2937',
    color: '#fff',
    confirmButtonColor: '#10b981',
    timer: 2000
  }).then(() => {
    router.reload();
  });
};

const submitTestimonial = () => {
  loading.value = true;
  router.post('/testimonials', testimonialForm.value, {
    onSuccess: () => {
      showTestimonialModal.value = false;
      testimonialForm.value.message = '';
      Swal.fire({
        icon: 'success',
        title: 'Thank You!',
        text: 'Your testimonial has been submitted. Refreshing page...',
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#10b981',
        timer: 2000,
        timerProgressBar: true,
      }).then(() => {
        router.reload();
      });
    },
    onError: (errors) => {
      const errorMsg = Object.values(errors)[0] || 'Failed to submit testimonial';
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: errorMsg,
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#ef4444',
      });
    },
    onFinish: () => loading.value = false,
  });
};
</script>
