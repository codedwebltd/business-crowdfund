<template>
  <UserLayout title="Settings">
    <!-- Security Card -->
    <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 shadow-2xl overflow-hidden mb-6">
      <div class="bg-gradient-to-r from-orange-500 to-purple-600 p-4">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
          Security Settings
        </h2>
      </div>

      <div class="p-6">
        <!-- Google 2FA Section -->
        <div class="mb-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-lg font-semibold text-white">Two-Factor Authentication</h3>
              <p class="text-sm text-gray-400">Add extra security to your account</p>
            </div>
            <button
              @click="toggle2FA"
              :disabled="loading"
              :class="[
                'relative inline-flex h-8 w-14 items-center rounded-full transition-colors disabled:opacity-50',
                twoFactorEnabled ? 'bg-green-500' : 'bg-gray-600'
              ]"
            >
              <svg v-if="loading" class="absolute inset-0 m-auto animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span v-else :class="['inline-block h-6 w-6 transform rounded-full bg-white transition-transform', twoFactorEnabled ? 'translate-x-7' : 'translate-x-1']"></span>
            </button>
          </div>

          <!-- QR Code Modal -->
          <div v-if="showQRCode" class="bg-white/10 rounded-xl p-6 border border-white/20">
            <div class="text-center">
              <h4 class="text-white font-semibold mb-4">Scan QR Code</h4>
              <div class="bg-white p-4 rounded-lg inline-block mb-4" v-html="qrCode"></div>
              <p class="text-sm text-gray-300 mb-2">Or enter this key manually:</p>
              <div class="bg-gray-800 px-4 py-3 rounded-lg mb-3 relative group">
                <div class="font-mono text-sm text-white break-all overflow-x-auto scrollbar-thin scrollbar-thumb-gray-600 scrollbar-track-gray-800">
                  {{ secret }}
                </div>
                <button @click="copySecret" class="absolute top-2 right-2 p-2 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                  </svg>
                </button>
              </div>

              <input
                v-model="verificationCode"
                type="text"
                placeholder="Enter 6-digit code"
                maxlength="6"
                class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 text-center text-lg tracking-widest mb-4 focus:outline-none focus:border-orange-500"
              />

              <div class="flex gap-2">
                <button @click="verify2FA" :disabled="loading" class="flex-1 bg-gradient-to-r from-orange-500 to-purple-600 text-white py-3 rounded-lg font-semibold flex items-center justify-center gap-2 disabled:opacity-50">
                  <svg v-if="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ loading ? 'Verifying...' : 'Verify & Enable' }}
                </button>
                <button @click="cancelSetup" :disabled="loading" class="flex-1 bg-gray-600 text-white py-3 rounded-lg font-semibold disabled:opacity-50">
                  Cancel
                </button>
              </div>
            </div>
          </div>

          <!-- Backup Codes -->
          <div v-if="backupCodes && backupCodes.length" class="bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-4 mt-4">
            <h4 class="text-white font-semibold mb-2 flex items-center gap-2">
              <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
              Save Your Backup Codes
            </h4>
            <p class="text-sm text-gray-300 mb-3">Store these codes safely. You can use them to access your account if you lose your authenticator device.</p>
            <div class="grid grid-cols-2 gap-2 mb-3">
              <div v-for="code in backupCodes" :key="code" class="bg-gray-800 px-3 py-2 rounded font-mono text-sm text-white">
                {{ code }}
              </div>
            </div>
            <button @click="copyBackupCodes" class="w-full bg-orange-500 text-white py-2 rounded-lg font-semibold flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
              </svg>
              Copy All Codes
            </button>
          </div>

          <!-- Disable 2FA -->
          <div v-if="twoFactorEnabled && !showQRCode" class="mt-4">
            <button @click="showDisableModal = true" class="text-red-400 text-sm hover:text-red-300">
              Disable Two-Factor Authentication
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Profile Settings Card -->
    <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 shadow-2xl overflow-hidden mb-6">
      <div class="bg-gradient-to-r from-blue-500 to-cyan-600 p-4">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          Profile Information
        </h2>
      </div>

      <div class="p-6 space-y-4">
        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="text-sm text-gray-400 mb-2 block flex items-center gap-2">
              Full Name
              <Tooltip text="Your legal name (cannot be changed)" />
            </label>
            <input :value="user.full_name" type="text" disabled class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-4 py-3 text-gray-500 cursor-not-allowed" />
          </div>
          <div>
            <label class="text-sm text-gray-400 mb-2 block flex items-center gap-2">
              Email Address
              <Tooltip text="Contact support to change email" />
            </label>
            <input :value="user.email || 'Not set'" type="email" disabled class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-4 py-3 text-gray-500 cursor-not-allowed" />
          </div>
          <div>
            <label class="text-sm text-gray-400 mb-2 block flex items-center gap-2">
              Phone Number
              <Tooltip text="Your registered phone number (cannot be changed)" />
            </label>
            <input :value="user.phone_number" type="text" disabled class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-4 py-3 text-gray-500 cursor-not-allowed" />
          </div>
          <div>
            <label class="text-sm text-gray-400 mb-2 block flex items-center gap-2">
              Referral Code
              <Tooltip text="Share this code to earn referral bonuses" />
            </label>
            <div class="flex gap-2">
              <input :value="user.referral_code" type="text" disabled class="flex-1 bg-gray-800/50 border border-white/10 rounded-lg px-4 py-3 text-white font-mono cursor-not-allowed" />
              <button @click="copyReferralCode" class="bg-blue-500 hover:bg-blue-600 px-4 rounded-lg transition-colors">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Payment Methods Card -->
    <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 shadow-2xl overflow-hidden mb-6">
      <div class="bg-gradient-to-r from-orange-500 to-purple-600 p-4">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
          </svg>
          Payment Methods
        </h2>
      </div>

      <div class="p-6 space-y-6">
        <!-- Bank Account -->
        <div>
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Bank Account</h3>
            <span v-if="!settings?.payment_gateways?.bank_transfer?.enabled" class="text-xs bg-red-500/20 text-red-400 px-3 py-1 rounded-full border border-red-500/30">
              Under Maintenance
            </span>
          </div>

          <div v-if="!settings?.payment_gateways?.bank_transfer?.enabled" class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4 mb-4">
            <div class="flex gap-3">
              <svg class="w-5 h-5 text-yellow-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
              <div class="flex-1">
                <p class="text-yellow-400 font-semibold text-sm mb-1">Bank Withdrawals Under Maintenance</p>
                <p class="text-gray-400 text-xs">Due to increased surge in withdrawal rate, bank method is temporarily risky. Kindly use a stable withdrawal method (crypto). You can still save your details.</p>
              </div>
            </div>
          </div>

          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="text-sm text-gray-400 mb-2 block">Bank Name</label>
              <input v-model="form.bank_name" type="text" placeholder="First Bank" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-orange-500" />
            </div>
            <div>
              <label class="text-sm text-gray-400 mb-2 block">Account Number</label>
              <input v-model="form.account_number" type="text" maxlength="10" placeholder="1234567890" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-orange-500" />
            </div>
            <div class="md:col-span-2">
              <label class="text-sm text-gray-400 mb-2 block">Account Name</label>
              <input v-model="form.account_name" type="text" placeholder="JOHN DOE" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-orange-500" />
            </div>
          </div>
        </div>

        <!-- Crypto Wallet -->
        <div>
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">Crypto Wallet</h3>
            <span v-if="!settings?.payment_gateways?.crypto_transfer?.enabled" class="text-xs bg-red-500/20 text-red-400 px-3 py-1 rounded-full border border-red-500/30">
              Under Maintenance
            </span>
          </div>

          <div v-if="!settings?.payment_gateways?.crypto_transfer?.enabled" class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4 mb-4">
            <div class="flex gap-3">
              <svg class="w-5 h-5 text-yellow-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
              <div class="flex-1">
                <p class="text-yellow-400 font-semibold text-sm mb-1">Crypto Withdrawals Under Maintenance</p>
                <p class="text-gray-400 text-xs">Due to high withdrawal voltage, crypto method is temporarily unavailable. You can still save your details for when service resumes.</p>
              </div>
            </div>
          </div>

          <div class="grid md:grid-cols-2 gap-4">
            <div>
              <label class="text-sm text-gray-400 mb-2 block">Coin Name</label>
              <select v-model="form.coin_name" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-orange-500">
                <option value="USDT">USDT</option>
              </select>
            </div>
            <div>
              <label class="text-sm text-gray-400 mb-2 block">Network</label>
              <select v-model="form.coin_network" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-orange-500">
                <option value="TRC20">TRC20</option>
              </select>
            </div>
            <div class="md:col-span-2">
              <label class="text-sm text-gray-400 mb-2 block">Wallet Address</label>
              <input v-model="form.wallet_address" type="text" placeholder="TXJz3KqF9hDWqYvH8LqK5V2pR7mN4sP8cB" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 font-mono text-sm focus:outline-none focus:border-orange-500" />
              <p class="text-xs text-gray-500 mt-2">⚠️ Double-check your address before saving!</p>
            </div>
          </div>
        </div>

        <!-- Save Button -->
        <button @click="savePaymentMethods" :disabled="loading" class="w-full md:w-auto bg-gradient-to-r from-orange-500 to-purple-600 text-white px-8 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 disabled:opacity-50">
          <svg v-if="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ loading ? 'Saving...' : 'Save Payment Methods' }}
        </button>
      </div>
    </div>

    <!-- Notification Preferences Card -->
    <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 shadow-2xl overflow-hidden mb-6">
      <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
          </svg>
          Notifications
        </h2>
      </div>

      <div class="p-6 space-y-4">
        <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
          <div class="flex-1">
            <h3 class="text-white font-semibold flex items-center gap-2">
              Email Notifications
              <Tooltip text="Receive updates about earnings, withdrawals, and referrals via email" />
            </h3>
            <p class="text-sm text-gray-400 mt-1">Get notified about important account activities</p>
          </div>
          <button
            @click="toggleNotification('email')"
            :class="['relative inline-flex h-8 w-14 items-center rounded-full transition-colors', notificationPrefs.email ? 'bg-green-500' : 'bg-gray-600']"
          >
            <span :class="['inline-block h-6 w-6 transform rounded-full bg-white transition-transform', notificationPrefs.email ? 'translate-x-7' : 'translate-x-1']"></span>
          </button>
        </div>

        <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
          <div class="flex-1">
            <h3 class="text-white font-semibold flex items-center gap-2">
              Task Reminders
              <Tooltip text="Daily reminders to complete available tasks" />
            </h3>
            <p class="text-sm text-gray-400 mt-1">Reminder notifications for pending tasks</p>
          </div>
          <button
            @click="toggleNotification('task_reminders')"
            :class="['relative inline-flex h-8 w-14 items-center rounded-full transition-colors', notificationPrefs.task_reminders ? 'bg-green-500' : 'bg-gray-600']"
          >
            <span :class="['inline-block h-6 w-6 transform rounded-full bg-white transition-transform', notificationPrefs.task_reminders ? 'translate-x-7' : 'translate-x-1']"></span>
          </button>
        </div>

        <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
          <div class="flex-1">
            <h3 class="text-white font-semibold flex items-center gap-2">
              Referral Updates
              <Tooltip text="Get notified when someone joins using your referral code" />
            </h3>
            <p class="text-sm text-gray-400 mt-1">Updates about your referral network</p>
          </div>
          <button
            @click="toggleNotification('referral_updates')"
            :class="['relative inline-flex h-8 w-14 items-center rounded-full transition-colors', notificationPrefs.referral_updates ? 'bg-green-500' : 'bg-gray-600']"
          >
            <span :class="['inline-block h-6 w-6 transform rounded-full bg-white transition-transform', notificationPrefs.referral_updates ? 'translate-x-7' : 'translate-x-1']"></span>
          </button>
        </div>

        <div class="flex items-center justify-between p-4 bg-white/5 rounded-lg border border-white/10">
          <div class="flex-1">
            <h3 class="text-white font-semibold flex items-center gap-2">
              Token Rate Changes
              <Tooltip text="Get alerts when token exchange rates fluctuate" />
            </h3>
            <p class="text-sm text-gray-400 mt-1">Live updates on token price changes</p>
          </div>
          <button
            @click="toggleNotification('token_updates')"
            :class="['relative inline-flex h-8 w-14 items-center rounded-full transition-colors', notificationPrefs.token_updates ? 'bg-green-500' : 'bg-gray-600']"
          >
            <span :class="['inline-block h-6 w-6 transform rounded-full bg-white transition-transform', notificationPrefs.token_updates ? 'translate-x-7' : 'translate-x-1']"></span>
          </button>
        </div>

        <button @click="saveNotifications" :disabled="loading" class="w-full md:w-auto bg-gradient-to-r from-green-500 to-emerald-600 text-white px-8 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 disabled:opacity-50">
          <svg v-if="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ loading ? 'Saving...' : 'Save Preferences' }}
        </button>
      </div>
    </div>

    <!-- Privacy & Account Card -->
    <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 shadow-2xl overflow-hidden mb-6">
      <div class="bg-gradient-to-r from-red-500 to-pink-600 p-4">
        <h2 class="text-xl font-bold text-white flex items-center gap-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
          Privacy & Account
        </h2>
      </div>

      <div class="p-6 space-y-6">
        <!-- Change Password -->
        <div>
          <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            Change Password
            <Tooltip text="Choose a strong password with at least 8 characters" />
          </h3>
          <div class="space-y-4">
            <div>
              <label class="text-sm text-gray-400 mb-2 block">Current Password</label>
              <input v-model="passwordForm.current_password" type="password" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-red-500" />
            </div>
            <div>
              <label class="text-sm text-gray-400 mb-2 block">New Password</label>
              <input v-model="passwordForm.new_password" type="password" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-red-500" />
            </div>
            <div>
              <label class="text-sm text-gray-400 mb-2 block">Confirm New Password</label>
              <input v-model="passwordForm.new_password_confirmation" type="password" class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-red-500" />
            </div>
          </div>
        </div>

        <button @click="changePassword" :disabled="loading" class="w-full md:w-auto bg-gradient-to-r from-red-500 to-pink-600 text-white px-8 py-3 rounded-lg font-semibold flex items-center justify-center gap-2 disabled:opacity-50">
          <svg v-if="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ loading ? 'Updating...' : 'Update Password' }}
        </button>

        <!-- Danger Zone -->
        <div class="pt-6 border-t border-red-500/30">
          <h3 class="text-lg font-semibold text-red-400 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Danger Zone
          </h3>
          <div class="bg-red-500/10 border border-red-500/30 rounded-lg p-4">
            <h4 class="text-white font-semibold mb-2">Delete Account</h4>
            <p class="text-sm text-gray-400 mb-4">Once you delete your account, there is no going back. All your data, earnings, and referrals will be permanently deleted.</p>
            <button @click="confirmDeleteAccount" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors">
              Delete My Account
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Disable Modal -->
    <div v-if="showDisableModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50">
      <div class="bg-gray-900 rounded-2xl border border-white/20 p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-white mb-4">Disable 2FA</h3>
        <p class="text-gray-300 mb-4">Enter your 6-digit code to disable two-factor authentication</p>
        <input
          v-model="disableCode"
          type="text"
          placeholder="000000"
          maxlength="6"
          class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-2 text-white text-center text-lg tracking-widest mb-4"
        />
        <div class="flex gap-2">
          <button @click="disable2FA" :disabled="loading" class="flex-1 bg-red-500 text-white py-2 rounded-lg font-semibold flex items-center justify-center gap-2 disabled:opacity-50">
            <svg v-if="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loading ? 'Disabling...' : 'Disable' }}
          </button>
          <button @click="showDisableModal = false" :disabled="loading" class="flex-1 bg-gray-600 text-white py-2 rounded-lg font-semibold disabled:opacity-50">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import Tooltip from '@/Components/Tooltip.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  user: Object,
  twoFactorEnabled: Boolean,
  qrCode: String,
  secret: String,
  backupCodes: Array,
  settings: Object,
  notificationSettings: {
    type: Object,
    default: () => ({
      email: true,
      task_reminders: true,
      referral_updates: true,
      token_updates: true
    })
  }
});

const showQRCode = ref(!!props.qrCode);

watch(() => props.qrCode, (newVal) => {
  if (newVal) {
    showQRCode.value = true;
  }
});

// Keep QR visible if there's a validation error
watch(() => props.secret, (newVal) => {
  if (newVal) {
    showQRCode.value = true;
  }
});

const verificationCode = ref('');
const showDisableModal = ref(false);
const disableCode = ref('');
const loading = ref(false);

// Profile Form
const profileForm = ref({
  full_name: props.user.full_name || '',
  email: props.user.email || '',
});

// Payment Methods Form
const form = ref({
  bank_name: props.user.bank_name || '',
  account_number: props.user.account_number || '',
  account_name: props.user.account_name || '',
  wallet_address: props.user.wallet_details?.wallet_address || '',
  coin_name: props.user.wallet_details?.coin_name || 'USDT',
  coin_network: props.user.wallet_details?.coin_network || 'TRC20',
});

// Notification Preferences
const notificationPrefs = ref({ ...props.notificationSettings });

// Password Form
const passwordForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
});

const toggle2FA = () => {
  if (props.twoFactorEnabled) {
    showDisableModal.value = true;
  } else {
    loading.value = true;
    router.post('/settings/2fa/enable', {}, {
      preserveScroll: true,
      preserveState: false,
      onFinish: () => loading.value = false,
    });
  }
};

const verify2FA = () => {
  if (verificationCode.value.length !== 6) {
    Swal.fire({
      icon: 'error',
      title: 'Invalid Code',
      text: 'Please enter a 6-digit code',
      background: '#1f2937',
      color: '#fff',
      confirmButtonColor: '#f97316',
    });
    return;
  }

  loading.value = true;
  router.post('/settings/2fa/verify', {
    code: verificationCode.value,
    secret: props.secret,
  }, {
    onSuccess: () => {
      showQRCode.value = false;
      verificationCode.value = '';
      Swal.fire({
        icon: 'success',
        title: '2FA Enabled',
        text: 'Two-factor authentication enabled successfully. Check backup codes below.',
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#10b981',
      });
    },
    onError: (errors) => {
      const errorMsg = errors.code || errors.message || 'Invalid verification code. Please try again.';
      Swal.fire({
        icon: 'error',
        title: 'Verification Failed',
        text: errorMsg,
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#ef4444',
      });
    },
    onFinish: () => loading.value = false,
  });
};

const disable2FA = () => {
  if (disableCode.value.length !== 6) {
    Swal.fire({
      icon: 'error',
      title: 'Invalid Code',
      text: 'Please enter a 6-digit code',
      background: '#1f2937',
      color: '#fff',
      confirmButtonColor: '#f97316',
    });
    return;
  }

  loading.value = true;
  router.post('/settings/2fa/disable', {
    code: disableCode.value,
  }, {
    onSuccess: () => {
      showDisableModal.value = false;
      disableCode.value = '';
      Swal.fire({
        icon: 'success',
        title: '2FA Disabled',
        text: 'Two-factor authentication has been disabled',
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#10b981',
      });
    },
    onError: (errors) => {
      const errorMsg = errors.code || 'Invalid code. Please try again.';
      Swal.fire({
        icon: 'error',
        title: 'Failed',
        text: errorMsg,
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#ef4444',
      });
    },
    onFinish: () => loading.value = false,
  });
};

const cancelSetup = () => {
  loading.value = true;
  showQRCode.value = false;
  verificationCode.value = '';

  // Clear session data
  router.post('/settings/2fa/cancel', {}, {
    onFinish: () => {
      loading.value = false;
      router.get('/settings');
    }
  });
};

const copySecret = () => {
  navigator.clipboard.writeText(props.secret);
  Swal.fire({
    icon: 'success',
    title: 'Copied!',
    text: 'Secret key copied to clipboard',
    background: '#1f2937',
    color: '#fff',
    confirmButtonColor: '#f97316',
    timer: 2000,
    showConfirmButton: false,
  });
};

const copyBackupCodes = () => {
  const text = props.backupCodes.join('\n');
  navigator.clipboard.writeText(text);
  Swal.fire({
    icon: 'success',
    title: 'Copied!',
    text: 'Backup codes copied to clipboard',
    background: '#1f2937',
    color: '#fff',
    confirmButtonColor: '#f97316',
    timer: 2000,
    showConfirmButton: false,
  });
};

const savePaymentMethods = () => {
  // Check 2FA if enabled
  if (props.twoFactorEnabled) {
    Swal.fire({
      title: '<span class="text-white font-bold">2FA Verification Required</span>',
      html: `
        <div class="text-center">
          <p class="text-gray-300 mb-4">Enter your authenticator code to save payment methods</p>
          <input
            id="swal-2fa-code"
            type="text"
            inputmode="numeric"
            class="w-full bg-gray-700 border-2 border-gray-600 focus:border-orange-500 rounded-lg px-4 py-3 text-white text-center text-2xl font-mono tracking-widest outline-none transition"
            placeholder="000000"
            maxlength="6"
            autocomplete="off"
          >
        </div>
      `,
      confirmButtonText: 'Verify & Save',
      showCancelButton: true,
      cancelButtonText: 'Cancel',
      customClass: {
        popup: 'rounded-2xl border-2 border-gray-700',
        confirmButton: 'bg-gradient-to-r from-orange-500 to-purple-600 hover:from-orange-600 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-all',
        cancelButton: 'bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition-all mr-2',
      },
      background: 'linear-gradient(135deg, #1f2937 0%, #111827 100%)',
      backdrop: 'rgba(0, 0, 0, 0.8)',
      showClass: {
        popup: 'animate__animated animate__fadeInDown animate__faster'
      },
      hideClass: {
        popup: 'animate__animated animate__fadeOutUp animate__faster'
      },
      didOpen: () => {
        const input = document.getElementById('swal-2fa-code');
        if (input) {
          input.focus();
          input.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
          });
        }
      },
      preConfirm: async () => {
        const code = document.getElementById('swal-2fa-code').value;
        if (!code || code.length !== 6) {
          Swal.showValidationMessage('<span class="text-red-400">Please enter a 6-digit code</span>');
          return false;
        }

        // Show loading state
        Swal.showLoading();

        // Submit and handle response
        return new Promise((resolve) => {
          router.post('/settings/payment-methods', {
            bank_name: form.value.bank_name,
            account_number: form.value.account_number,
            account_name: form.value.account_name,
            wallet_address: form.value.wallet_address,
            coin_name: form.value.coin_name,
            coin_network: form.value.coin_network,
            two_factor_code: code,
          }, {
            onSuccess: () => {
              resolve({ success: true });
            },
            onError: (errors) => {
              Swal.hideLoading();
              const errorMsg = errors.two_factor_code || errors.code || Object.values(errors)[0] || 'Invalid verification code';
              Swal.showValidationMessage(`<span class="text-red-400">${errorMsg}</span>`);
              resolve(false);
            },
          });
        });
      },
      allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
      if (result.isConfirmed && result.value?.success) {
        Swal.fire({
          icon: 'success',
          title: '<span class="text-white font-bold">Saved!</span>',
          text: 'Payment methods updated successfully',
          background: 'linear-gradient(135deg, #1f2937 0%, #111827 100%)',
          color: '#fff',
          confirmButtonColor: '#10b981',
          customClass: {
            popup: 'rounded-2xl border-2 border-gray-700',
          },
          timer: 3000,
          showConfirmButton: false,
        });
      }
    });
    return;
  }

  // No 2FA, submit directly
  submitPaymentMethods();
};

// Profile Functions
const saveProfile = () => {
  loading.value = true;
  router.post('/settings/profile', profileForm.value, {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Profile updated successfully',
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#3b82f6',
      });
    },
    onError: (errors) => {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: Object.values(errors)[0] || 'Failed to update profile',
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#ef4444',
      });
    },
    onFinish: () => loading.value = false,
  });
};

const copyReferralCode = () => {
  navigator.clipboard.writeText(props.user.referral_code);
  Swal.fire({
    icon: 'success',
    title: 'Copied!',
    text: 'Referral code copied to clipboard',
    background: '#1f2937',
    color: '#fff',
    confirmButtonColor: '#3b82f6',
    timer: 2000,
    showConfirmButton: false,
  });
};

// Notification Functions
const toggleNotification = (type) => {
  notificationPrefs.value[type] = !notificationPrefs.value[type];
};

const saveNotifications = () => {
  loading.value = true;
  router.post('/settings/notifications', notificationPrefs.value, {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Notification preferences saved',
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#10b981',
      });
    },
    onError: (errors) => {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: Object.values(errors)[0] || 'Failed to save preferences',
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#ef4444',
      });
    },
    onFinish: () => loading.value = false,
  });
};

// Password Functions
const changePassword = () => {
  if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Passwords do not match',
      background: '#1f2937',
      color: '#fff',
      confirmButtonColor: '#ef4444',
    });
    return;
  }

  // Check 2FA if enabled
  if (props.twoFactorEnabled) {
    Swal.fire({
      title: '<span class="text-white font-bold">2FA Verification Required</span>',
      html: `
        <div class="text-center">
          <p class="text-gray-300 mb-4">Enter your authenticator code to change password</p>
          <input
            id="swal-2fa-password"
            type="text"
            inputmode="numeric"
            class="w-full bg-gray-700 border-2 border-gray-600 focus:border-red-500 rounded-lg px-4 py-3 text-white text-center text-2xl font-mono tracking-widest outline-none transition"
            placeholder="000000"
            maxlength="6"
            autocomplete="off"
          >
        </div>
      `,
      confirmButtonText: 'Verify & Update',
      showCancelButton: true,
      cancelButtonText: 'Cancel',
      customClass: {
        popup: 'rounded-2xl border-2 border-gray-700',
        confirmButton: 'bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-bold py-3 px-6 rounded-lg transition-all',
        cancelButton: 'bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition-all mr-2',
      },
      background: 'linear-gradient(135deg, #1f2937 0%, #111827 100%)',
      didOpen: () => {
        const input = document.getElementById('swal-2fa-password');
        if (input) {
          input.focus();
          input.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
          });
        }
      },
      preConfirm: async () => {
        const code = document.getElementById('swal-2fa-password').value;
        if (!code || code.length !== 6) {
          Swal.showValidationMessage('<span class="text-red-400">Please enter a 6-digit code</span>');
          return false;
        }
        Swal.showLoading();
        return new Promise((resolve) => {
          router.post('/settings/password', {
            ...passwordForm.value,
            two_factor_code: code,
          }, {
            onSuccess: () => {
              resolve({ success: true });
            },
            onError: (errors) => {
              Swal.hideLoading();
              const errorMsg = errors.two_factor_code || errors.code || Object.values(errors)[0] || 'Invalid verification code';
              Swal.showValidationMessage(`<span class="text-red-400">${errorMsg}</span>`);
              resolve(false);
            },
          });
        });
      },
    }).then((result) => {
      if (result.isConfirmed && result.value?.success) {
        passwordForm.value = {
          current_password: '',
          new_password: '',
          new_password_confirmation: '',
        };
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: 'Password changed successfully',
          background: '#1f2937',
          color: '#fff',
          confirmButtonColor: '#10b981',
        });
      }
    });
    return;
  }

  // No 2FA, submit directly
  loading.value = true;
  router.post('/settings/password', passwordForm.value, {
    onSuccess: () => {
      passwordForm.value = {
        current_password: '',
        new_password: '',
        new_password_confirmation: '',
      };
      Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Password changed successfully',
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#10b981',
      });
    },
    onError: (errors) => {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: Object.values(errors)[0] || 'Failed to change password',
        background: '#1f2937',
        color: '#fff',
        confirmButtonColor: '#ef4444',
      });
    },
    onFinish: () => loading.value = false,
  });
};

const confirmDeleteAccount = () => {
  Swal.fire({
    title: 'Delete Account?',
    html: '<p class="text-gray-300 mb-4">This action cannot be undone. All your data will be permanently deleted.</p><input id="swal-delete-confirm" type="text" placeholder="Type DELETE to confirm" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white" />',
    icon: 'warning',
    background: '#1f2937',
    color: '#fff',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Delete Account',
    preConfirm: () => {
      const input = document.getElementById('swal-delete-confirm').value;
      if (input !== 'DELETE') {
        Swal.showValidationMessage('Please type DELETE to confirm');
        return false;
      }
      return true;
    }
  }).then((result) => {
    if (result.isConfirmed) {
      router.post('/settings/delete-account', {}, {
        onSuccess: () => {
          window.location.href = '/';
        }
      });
    }
  });
};

const submitPaymentMethods = (twoFactorCode = null) => {
  loading.value = true;
  router.post('/settings/payment-methods', {
    bank_name: form.value.bank_name,
    account_number: form.value.account_number,
    account_name: form.value.account_name,
    wallet_address: form.value.wallet_address,
    coin_name: form.value.coin_name,
    coin_network: form.value.coin_network,
    two_factor_code: twoFactorCode,
  }, {
    onSuccess: () => {
      Swal.fire({
        icon: 'success',
        title: '<span class="text-white font-bold">Saved!</span>',
        text: 'Payment methods updated successfully',
        background: 'linear-gradient(135deg, #1f2937 0%, #111827 100%)',
        color: '#fff',
        confirmButtonColor: '#10b981',
        customClass: {
          popup: 'rounded-2xl border-2 border-gray-700',
        },
      });
    },
    onError: (errors) => {
      const errorMsg = Object.values(errors)[0] || 'Failed to save payment methods';
      Swal.fire({
        icon: 'error',
        title: '<span class="text-white font-bold">Error</span>',
        text: errorMsg,
        background: 'linear-gradient(135deg, #1f2937 0%, #111827 100%)',
        color: '#fff',
        confirmButtonColor: '#ef4444',
        customClass: {
          popup: 'rounded-2xl border-2 border-gray-700',
        },
      });
    },
    onFinish: () => loading.value = false,
  });
};
</script>
