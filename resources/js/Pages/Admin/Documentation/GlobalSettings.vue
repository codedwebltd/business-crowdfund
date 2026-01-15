<template>
  <AdminLayout title="Global Settings Documentation" :settings="settings">
    <!-- Breadcrumbs -->
    <Breadcrumbs :crumbs="[
      { label: 'Dashboard', url: '/admin' },
      { label: 'Documentation', url: '/admin/documentation' },
      { label: 'Global Settings' }
    ]" class="mb-4" />

    <!-- Header -->
    <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl shadow-lg p-6 sm:p-8 mb-6 text-white">
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-4xl">
          ⚙️
        </div>
        <div>
          <h1 class="text-3xl sm:text-4xl font-bold">Global Settings Documentation</h1>
          <p class="text-purple-100 text-sm sm:text-base mt-1">Complete guide to platform configuration and settings management</p>
        </div>
      </div>
    </div>

    <!-- Table of Contents -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 mb-6 sticky top-4 z-10">
      <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
          <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
        </svg>
        Table of Contents
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 text-sm">
        <a v-for="section in sections" :key="section.id" :href="`#${section.id}`" class="text-purple-600 hover:text-purple-800 hover:underline flex items-center gap-2 p-2 rounded hover:bg-purple-50 transition-colors">
          <span class="text-lg">{{ section.icon }}</span>
          <span>{{ section.title }}</span>
        </a>
      </div>
    </div>

    <!-- Documentation Content -->
    <div class="space-y-6">
      <!-- Overview -->
      <div id="overview" class="bg-white rounded-xl shadow-md border border-gray-200 p-6 scroll-mt-24">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-2xl">
            📋
          </div>
          <div>
            <h2 class="text-2xl font-bold text-gray-900">Overview</h2>
            <p class="text-gray-600 text-sm">Understanding the Global Settings system</p>
          </div>
        </div>

        <div class="prose max-w-none space-y-4">
          <p class="text-gray-700">
            The Global Settings system is the central configuration hub for your crowdfunding platform. All settings are stored in the
            <code class="bg-gray-100 px-2 py-1 rounded text-sm">global_settings</code> database table and can be managed through the
            admin panel at <code class="bg-gray-100 px-2 py-1 rounded text-sm">/admin/settings</code>.
          </p>

          <div class="bg-blue-50 border-l-4 border-blue-500 p-4 my-4">
            <div class="flex items-start gap-3">
              <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
              </svg>
              <div class="text-sm text-blue-900">
                <p class="font-semibold mb-1">Important Notes</p>
                <ul class="list-disc list-inside space-y-1">
                  <li>Settings are organized into 15 tabs for easy management</li>
                  <li>Most settings take effect immediately after saving</li>
                  <li>JSON fields are automatically parsed and validated</li>
                  <li>Always test changes in a staging environment first</li>
                </ul>
              </div>
            </div>
          </div>

          <h3 class="text-lg font-bold text-gray-900 mt-6 mb-3">Database Structure</h3>
          <p class="text-gray-700">The global_settings table contains 77 columns covering all platform configurations:</p>
          <ul class="list-disc list-inside space-y-1 text-gray-700 ml-4">
            <li>Scalar values (strings, integers, decimals, booleans)</li>
            <li>JSON objects for complex configurations</li>
            <li>Timestamps for tracking changes</li>
          </ul>
        </div>
      </div>

      <!-- App Config -->
      <div id="app-config" class="bg-white rounded-xl shadow-md border border-gray-200 p-6 scroll-mt-24">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-2xl">
            📱
          </div>
          <div>
            <h2 class="text-2xl font-bold text-gray-900">App Configuration</h2>
            <p class="text-gray-600 text-sm">Basic application identity and branding</p>
          </div>
        </div>

        <div class="prose max-w-none">
          <p class="text-gray-700 mb-4">Controls the fundamental identity of your platform including name, URL, country, currency, and marketing information.</p>

          <div class="bg-gray-50 rounded-lg p-4 my-4">
            <h4 class="font-bold text-gray-900 mb-3">Settings Fields</h4>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
              <thead>
                <tr class="bg-gray-100">
                  <th class="px-4 py-2 text-left font-semibold text-gray-900">Field</th>
                  <th class="px-4 py-2 text-left font-semibold text-gray-900">Type</th>
                  <th class="px-4 py-2 text-left font-semibold text-gray-900">Description</th>
                  <th class="px-4 py-2 text-left font-semibold text-gray-900">Example</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr>
                  <td class="px-4 py-2 font-mono text-xs">app_name</td>
                  <td class="px-4 py-2">string</td>
                  <td class="px-4 py-2">Official platform name</td>
                  <td class="px-4 py-2 font-mono text-xs">CROWDFUNDS</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 font-mono text-xs">app_url</td>
                  <td class="px-4 py-2">string</td>
                  <td class="px-4 py-2">Full platform URL</td>
                  <td class="px-4 py-2 font-mono text-xs">https://business.qiviotalk.online</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 font-mono text-xs">country_of_operation</td>
                  <td class="px-4 py-2">string</td>
                  <td class="px-4 py-2">Country code (ISO 3166)</td>
                  <td class="px-4 py-2 font-mono text-xs">NGA</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 font-mono text-xs">platform_currency</td>
                  <td class="px-4 py-2">string</td>
                  <td class="px-4 py-2">Currency symbol</td>
                  <td class="px-4 py-2 font-mono text-xs">NGN</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 font-mono text-xs">app_description</td>
                  <td class="px-4 py-2">text</td>
                  <td class="px-4 py-2">Short marketing description</td>
                  <td class="px-4 py-2">Earn money daily by completing simple tasks</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 font-mono text-xs">daily_earning_average</td>
                  <td class="px-4 py-2">integer</td>
                  <td class="px-4 py-2">Avg. daily earnings claim</td>
                  <td class="px-4 py-2 font-mono text-xs">950</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 font-mono text-xs">time_required</td>
                  <td class="px-4 py-2">string</td>
                  <td class="px-4 py-2">Time needed per day</td>
                  <td class="px-4 py-2 font-mono text-xs">30min</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 font-mono text-xs">anonymity_level</td>
                  <td class="px-4 py-2">string</td>
                  <td class="px-4 py-2">Privacy level claim</td>
                  <td class="px-4 py-2 font-mono text-xs">100%</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 font-mono text-xs">total_users</td>
                  <td class="px-4 py-2">string</td>
                  <td class="px-4 py-2">Display user count (social proof)</td>
                  <td class="px-4 py-2 font-mono text-xs">200</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 font-mono text-xs">site_logo</td>
                  <td class="px-4 py-2">string</td>
                  <td class="px-4 py-2">Logo URL (uploaded to Backblaze)</td>
                  <td class="px-4 py-2 font-mono text-xs">https://...</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 my-4">
            <p class="text-sm text-yellow-800">
              <strong>Note:</strong> Logo uploads are handled via AJAX with progress tracking and stored on Backblaze B2.
            </p>
          </div>
        </div>
      </div>

      <!-- AI Task Generation -->
      <div id="ai-generation" class="bg-white rounded-xl shadow-md border border-gray-200 p-6 scroll-mt-24">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center text-2xl">
            🤖
          </div>
          <div>
            <h2 class="text-2xl font-bold text-gray-900">AI Task Generation</h2>
            <p class="text-gray-600 text-sm">Automated task creation using Groq API</p>
          </div>
        </div>

        <div class="prose max-w-none">
          <p class="text-gray-700 mb-4">
            Configures automatic task generation using Groq's Llama models. When task inventory falls below threshold,
            the system automatically generates surveys, video tasks, and product reviews.
          </p>

          <div class="bg-gray-50 rounded-lg p-4 my-4">
            <h4 class="font-bold text-gray-900 mb-3">Primary Fields</h4>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
              <thead>
                <tr class="bg-gray-100">
                  <th class="px-4 py-2 text-left font-semibold text-gray-900">Field</th>
                  <th class="px-4 py-2 text-left font-semibold text-gray-900">Type</th>
                  <th class="px-4 py-2 text-left font-semibold text-gray-900">Description</th>
                  <th class="px-4 py-2 text-left font-semibold text-gray-900">Default</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr>
                  <td class="px-4 py-2 font-mono text-xs">ai_task_generation_enabled</td>
                  <td class="px-4 py-2">boolean</td>
                  <td class="px-4 py-2">Enable/disable AI generation</td>
                  <td class="px-4 py-2">true</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 font-mono text-xs">ai_generation_frequency_hours</td>
                  <td class="px-4 py-2">integer</td>
                  <td class="px-4 py-2">How often to check (in hours)</td>
                  <td class="px-4 py-2">168 (weekly)</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 font-mono text-xs">min_task_templates_threshold</td>
                  <td class="px-4 py-2">integer</td>
                  <td class="px-4 py-2">Generate when total active < this</td>
                  <td class="px-4 py-2">50</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 font-mono text-xs">task_deactivation_days</td>
                  <td class="px-4 py-2">integer</td>
                  <td class="px-4 py-2">Days before tasks deactivate</td>
                  <td class="px-4 py-2">7</td>
                </tr>
                <tr>
                  <td class="px-4 py-2 font-mono text-xs">task_deletion_days</td>
                  <td class="px-4 py-2">integer</td>
                  <td class="px-4 py-2">Days before permanent deletion</td>
                  <td class="px-4 py-2">30</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="bg-gray-50 rounded-lg p-4 my-4">
            <h4 class="font-bold text-gray-900 mb-3">AI Configuration JSON</h4>
            <p class="text-sm text-gray-700 mb-2">The <code class="bg-gray-200 px-1 rounded">ai_configuration</code> field stores a JSON object:</p>
            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-xs"><code>{
  "groq_api_key": "gsk_...",
  "groq_model": "llama-3.1-8b-instant",
  "groq_endpoint": "https://api.groq.com/openai/v1/chat/completions",
  "temperature": 0.7,
  "youtube_api_key": "AIzaSyA...",
  "max_tokens": {
    "survey": 3500,
    "video_questions": 2000,
    "product_names": 1000
  },
  "tasks_to_generate": {
    "surveys": 10,
    "videos": 30,
    "syncs": 20,
    "reviews": 30
  },
  "reward_ranges": {
    "survey": { "min": 350, "max": 750 },
    "video": { "min": 400, "max": 900 },
    "sync": { "fixed": 150 },
    "review": { "min": 220, "max": 550 }
  }
}</code></pre>
          </div>

          <div class="bg-blue-50 border-l-4 border-blue-500 p-4 my-4">
            <p class="text-sm text-blue-900">
              <strong>How It Works:</strong> Cron job runs every N hours → Checks if task_count < threshold → Generates tasks using Groq AI →
              Creates country-specific content → Admin reviews before activation
            </p>
          </div>
        </div>
      </div>

      <!-- Continue with remaining sections... -->
      <!-- Due to length, I'll create a comprehensive structure for all sections -->

      <!-- Financial Settings -->
      <div id="financial" class="bg-white rounded-xl shadow-md border border-gray-200 p-6 scroll-mt-24">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-lg flex items-center justify-center text-2xl">
            💰
          </div>
          <div>
            <h2 class="text-2xl font-bold text-gray-900">Financial Settings</h2>
            <p class="text-gray-600 text-sm">Withdrawal limits, payment gateways, and banking info</p>
          </div>
        </div>

        <div class="prose max-w-none">
          <p class="text-gray-700 mb-4">
            Controls withdrawal limits per rank, payment gateway configuration, bank accounts, and crypto wallets.
          </p>

          <div class="bg-gray-50 rounded-lg p-4 my-4">
            <h4 class="font-bold text-gray-900 mb-3">Key Settings</h4>
            <ul class="list-disc list-inside space-y-2 text-sm text-gray-700">
              <li><code class="bg-gray-200 px-1 rounded">minimum_withdrawal</code> - Global minimum (e.g., 2000)</li>
              <li><code class="bg-gray-200 px-1 rounded">maximum_withdrawal</code> - Global maximum (e.g., 20000)</li>
              <li><code class="bg-gray-200 px-1 rounded">withdrawals_per_day</code> - Daily limit per user (e.g., 2)</li>
              <li><code class="bg-gray-200 px-1 rounded">referral_threshold</code> - Min active referrals to withdraw (e.g., 3)</li>
              <li><code class="bg-gray-200 px-1 rounded">withdrawal_rate</code> - Multiplier (0.77 = user gets 77%)</li>
            </ul>
          </div>

          <div class="bg-gray-50 rounded-lg p-4 my-4">
            <h4 class="font-bold text-gray-900 mb-3">Withdrawal Limits by Rank (JSON)</h4>
            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-xs"><code>{
  "basic": { "min": 3500, "max": 20000, "per_day": 2 },
  "bronze": { "min": 3000, "max": 30000, "per_day": 2 },
  "silver": { "min": 2000, "max": 200000, "per_day": 2 },
  "gold": { "min": 1000, "max": 500000 },
  "platinum": { "min": 500, "max": 1000000, "per_day": 1 },
  "diamond": { "min": 100, "max": 5000000, "per_day": 1 }
}</code></pre>
          </div>

          <div class="bg-gray-50 rounded-lg p-4 my-4">
            <h4 class="font-bold text-gray-900 mb-3">Payment Gateways (JSON)</h4>
            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-xs"><code>{
  "bank_transfer": { "enabled": false },
  "crypto_transfer": { "enabled": true }
}</code></pre>
          </div>

          <div class="bg-gray-50 rounded-lg p-4 my-4">
            <h4 class="font-bold text-gray-900 mb-3">Bank Accounts Array (JSON)</h4>
            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-xs"><code>[
  {
    "bank_name": "GTBank",
    "account_number": "0123456789",
    "account_name": "QivioTalk Limited",
    "special_note": "Use your phone number as reference"
  }
]</code></pre>
          </div>

          <div class="bg-gray-50 rounded-lg p-4 my-4">
            <h4 class="font-bold text-gray-900 mb-3">Crypto Wallets Array (JSON)</h4>
            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-xs"><code>[
  {
    "coin_name": "USDT",
    "network": "TRC20",
    "address": "TYourWalletAddressHere",
    "special_note": null
  }
]</code></pre>
          </div>
        </div>
      </div>

      <!-- Add more sections following the same pattern -->
      <!-- I'll create a condensed version for the remaining sections to fit within reasonable length -->

      <!-- Quick Reference for Remaining Sections -->
      <div id="quick-reference" class="bg-white rounded-xl shadow-md border border-gray-200 p-6 scroll-mt-24">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center text-2xl">
            ⚡
          </div>
          <div>
            <h2 class="text-2xl font-bold text-gray-900">Quick Reference Guide</h2>
            <p class="text-gray-600 text-sm">Summary of all remaining settings tabs</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Each remaining section as a card -->
          <div class="bg-gradient-to-br from-red-50 to-pink-50 border border-red-200 rounded-lg p-4">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
              <span class="text-xl">🔒</span> Fraud Detection
            </h3>
            <p class="text-sm text-gray-700 mb-3">Protects platform from abuse with progressive penalties.</p>
            <div class="text-xs text-gray-600 space-y-1">
              <div><code class="bg-red-100 px-1 rounded">max_tasks_per_hour</code> - Velocity limit (5)</div>
              <div><code class="bg-red-100 px-1 rounded">min_task_completion_time</code> - Bot detection (8s)</div>
              <div><code class="bg-red-100 px-1 rounded">device_fingerprinting_enabled</code> - Track devices</div>
              <div><code class="bg-red-100 px-1 rounded">ip_monitoring_enabled</code> - Multi-account detection</div>
            </div>
          </div>

          <div class="bg-gradient-to-br from-teal-50 to-cyan-50 border border-teal-200 rounded-lg p-4">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
              <span class="text-xl">🛡️</span> KYC Settings
            </h3>
            <p class="text-sm text-gray-700 mb-3">Know Your Customer verification requirements.</p>
            <div class="text-xs text-gray-600 space-y-1">
              <div><code class="bg-teal-100 px-1 rounded">kyc_withdrawal_threshold</code> - Amount requiring KYC (7000)</div>
              <div><code class="bg-teal-100 px-1 rounded">enable_kyc_on_first_withdrawal</code> - Force on 1st (true)</div>
              <div><code class="bg-teal-100 px-1 rounded">kyc_requirements</code> - JSON: nin, bvn, utility_bill, selfie</div>
              <div><code class="bg-teal-100 px-1 rounded">require_testimonial_first_withdrawal</code> - Social proof</div>
            </div>
          </div>

          <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
              <span class="text-xl">🔔</span> Notifications
            </h3>
            <p class="text-sm text-gray-700 mb-3">Multi-channel notification delivery config.</p>
            <div class="text-xs text-gray-600 space-y-1">
              <div><code class="bg-blue-100 px-1 rounded">email_notifications_enabled</code> - Email (true)</div>
              <div><code class="bg-blue-100 px-1 rounded">sms_notifications_enabled</code> - SMS (false)</div>
              <div><code class="bg-blue-100 px-1 rounded">notification_channels</code> - JSON: firebase, whatsapp, telegram</div>
            </div>
          </div>

          <div class="bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-4">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
              <span class="text-xl">📈</span> Plan Upgrades
            </h3>
            <p class="text-sm text-gray-700 mb-3">Discount system for star-based plan upgrades.</p>
            <div class="text-xs text-gray-600 space-y-1">
              <div><code class="bg-purple-100 px-1 rounded">plan_upgrade_discount_percentage</code> - Discount % (20.00)</div>
              <div>Maps star ratings to qualified plans</div>
              <div>5⭐ → Platinum, 4⭐ → Gold, 3⭐ → Silver, 2⭐ → Bronze</div>
            </div>
          </div>

          <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg p-4">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
              <span class="text-xl">🏆</span> Rank System
            </h3>
            <p class="text-sm text-gray-700 mb-3">Rank criteria and commission multipliers.</p>
            <div class="text-xs text-gray-600 space-y-1">
              <div><code class="bg-yellow-100 px-1 rounded">rank_criteria</code> - JSON per rank: direct_referrals, team_size, monthly_volume</div>
              <div><code class="bg-yellow-100 px-1 rounded">rank_commission_multipliers</code> - Bonus multipliers (1.05 = +5%)</div>
              <div><code class="bg-yellow-100 px-1 rounded">diamond_leadership_bonus</code> - Monthly bonus (5000)</div>
            </div>
          </div>

          <div class="bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-4">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
              <span class="text-xl">👥</span> Referral System
            </h3>
            <p class="text-sm text-gray-700 mb-3">Multi-level commission structure.</p>
            <div class="text-xs text-gray-600 space-y-1">
              <div><code class="bg-purple-100 px-1 rounded">referral_levels_depth</code> - How many levels (5)</div>
              <div><code class="bg-purple-100 px-1 rounded">commission_rates</code> - JSON: activation %, task_earnings %</div>
              <div>L1: 10% activation, 5% task | L2: 9.5%, 3% | L3: 8.5%, 2%...</div>
            </div>
          </div>

          <div class="bg-gradient-to-br from-blue-50 to-cyan-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
              <span class="text-xl">🔐</span> Security (reCAPTCHA)
            </h3>
            <p class="text-sm text-gray-700 mb-3">Google reCAPTCHA v2 bot protection.</p>
            <div class="text-xs text-gray-600 space-y-1">
              <div><code class="bg-blue-100 px-1 rounded">recaptcha_site_key</code> - Public key</div>
              <div><code class="bg-blue-100 px-1 rounded">recaptcha_secret_key</code> - Private key</div>
              <div><code class="bg-blue-100 px-1 rounded">recaptcha_enabled</code> - Enable/disable (true)</div>
              <div><code class="bg-blue-100 px-1 rounded">recaptcha_trigger_on_fraud</code> - Auto-trigger (true)</div>
            </div>
          </div>

          <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg p-4">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
              <span class="text-xl">⭐</span> Star Ratings
            </h3>
            <p class="text-sm text-gray-700 mb-3">Performance-based user rating system.</p>
            <div class="text-xs text-gray-600 space-y-1">
              <div><code class="bg-yellow-100 px-1 rounded">star_requirements</code> - JSON per star level</div>
              <div>5⭐: 8 tasks/week + 9 refs/week (General 👑)</div>
              <div>4⭐: 6 tasks + 5 refs | 3⭐: 4 tasks + 3 refs</div>
              <div><code class="bg-yellow-100 px-1 rounded">decay_days</code> - Inactivity decay period (4)</div>
            </div>
          </div>

          <div class="bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-300 rounded-lg p-4">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
              <span class="text-xl">⚙️</span> System Controls
            </h3>
            <p class="text-sm text-gray-700 mb-3">Platform-wide operational toggles.</p>
            <div class="text-xs text-gray-600 space-y-1">
              <div><code class="bg-gray-200 px-1 rounded">maintenance_mode</code> - Site down (false)</div>
              <div><code class="bg-gray-200 px-1 rounded">new_registrations_enabled</code> - Allow signups (true)</div>
              <div><code class="bg-gray-200 px-1 rounded">withdrawals_enabled</code> - Allow withdrawals (true)</div>
              <div><code class="bg-gray-200 px-1 rounded">admin_ip_whitelist</code> - IP restrictions ([])</div>
            </div>
          </div>

          <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
              <span class="text-xl">✅</span> Task Configuration
            </h3>
            <p class="text-sm text-gray-700 mb-3">Task types, limits, and validation rules.</p>
            <div class="text-xs text-gray-600 space-y-1">
              <div><code class="bg-green-100 px-1 rounded">task_distribution_percentages</code> - VIDEO:40%, SURVEY:30%...</div>
              <div><code class="bg-green-100 px-1 rounded">daily_task_limits</code> - Per plan: basic:8, bronze:10...</div>
              <div><code class="bg-green-100 px-1 rounded">task_validation_rules</code> - Min times, watch %, char limits</div>
            </div>
          </div>

          <div class="bg-gradient-to-br from-orange-50 to-red-50 border border-orange-200 rounded-lg p-4">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
              <span class="text-xl">🪙</span> Token & Liquidity
            </h3>
            <p class="text-sm text-gray-700 mb-3">Token pricing and platform health monitoring.</p>
            <div class="text-xs text-gray-600 space-y-1">
              <div><code class="bg-orange-100 px-1 rounded">token_settings.token_price</code> - Price per CROW (850)</div>
              <div><code class="bg-orange-100 px-1 rounded">withdrawal_rate</code> - User payout % (0.77)</div>
              <div><code class="bg-orange-100 px-1 rounded">liquidity_settings</code> - Burn rate thresholds</div>
              <div><code class="bg-orange-100 px-1 rounded">pending_balance_maturation_hours</code> - Delay (1)</div>
            </div>
          </div>

          <div class="bg-gradient-to-br from-cyan-50 to-blue-50 border border-cyan-200 rounded-lg p-4">
            <h3 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
              <span class="text-xl">📧</span> Email & Integrations
            </h3>
            <p class="text-sm text-gray-700 mb-3">SMTP, Pusher, and support contact config.</p>
            <div class="text-xs text-gray-600 space-y-1">
              <div><code class="bg-cyan-100 px-1 rounded">mail_*</code> - SMTP: host, port, username, password, encryption</div>
              <div><code class="bg-cyan-100 px-1 rounded">pusher_*</code> - WebSocket: app_id, key, secret, cluster</div>
              <div><code class="bg-cyan-100 px-1 rounded">support_*</code> - Contact: email, phone, whatsapp</div>
            </div>
          </div>
        </div>
      </div>

      <!-- API Routes -->
      <div id="api-routes" class="bg-white rounded-xl shadow-md border border-gray-200 p-6 scroll-mt-24">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-2xl">
            🔌
          </div>
          <div>
            <h2 class="text-2xl font-bold text-gray-900">API Routes</h2>
            <p class="text-gray-600 text-sm">Settings update endpoints</p>
          </div>
        </div>

        <div class="prose max-w-none">
          <p class="text-gray-700 mb-4">Each settings tab posts to its own dedicated route:</p>

          <div class="bg-gray-50 rounded-lg p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
              <thead>
                <tr class="bg-gray-100">
                  <th class="px-4 py-2 text-left font-semibold">Route</th>
                  <th class="px-4 py-2 text-left font-semibold">Settings Tab</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 font-mono text-xs">
                <tr><td class="px-4 py-2">/admin/settings/app-config</td><td class="px-4 py-2">App Configuration</td></tr>
                <tr><td class="px-4 py-2">/admin/settings/ai</td><td class="px-4 py-2">AI Task Generation</td></tr>
                <tr><td class="px-4 py-2">/admin/settings/financial</td><td class="px-4 py-2">Financial Settings</td></tr>
                <tr><td class="px-4 py-2">/admin/settings/fraud</td><td class="px-4 py-2">Fraud Detection</td></tr>
                <tr><td class="px-4 py-2">/admin/settings/kyc</td><td class="px-4 py-2">KYC Settings</td></tr>
                <tr><td class="px-4 py-2">/admin/settings/notifications</td><td class="px-4 py-2">Notifications</td></tr>
                <tr><td class="px-4 py-2">/admin/settings/plan-upgrades</td><td class="px-4 py-2">Plan Upgrades</td></tr>
                <tr><td class="px-4 py-2">/admin/settings/ranks</td><td class="px-4 py-2">Rank System</td></tr>
                <tr><td class="px-4 py-2">/admin/settings/referral</td><td class="px-4 py-2">Referral System</td></tr>
                <tr><td class="px-4 py-2">/admin/settings/security</td><td class="px-4 py-2">Security (reCAPTCHA)</td></tr>
                <tr><td class="px-4 py-2">/admin/settings/star-ratings</td><td class="px-4 py-2">Star Ratings</td></tr>
                <tr><td class="px-4 py-2">/admin/settings/system</td><td class="px-4 py-2">System Controls</td></tr>
                <tr><td class="px-4 py-2">/admin/settings/tasks</td><td class="px-4 py-2">Task Configuration</td></tr>
                <tr><td class="px-4 py-2">/admin/settings/token</td><td class="px-4 py-2">Token & Liquidity</td></tr>
                <tr><td class="px-4 py-2">/admin/settings/integrations</td><td class="px-4 py-2">Email & Integrations</td></tr>
              </tbody>
            </table>
          </div>

          <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-4">
            <p class="text-sm text-blue-900">
              <strong>Note:</strong> All routes use POST method and return Inertia responses with flash messages on success/error.
            </p>
          </div>
        </div>
      </div>

      <!-- Best Practices -->
      <div id="best-practices" class="bg-white rounded-xl shadow-md border border-gray-200 p-6 scroll-mt-24">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center text-2xl">
            ✨
          </div>
          <div>
            <h2 class="text-2xl font-bold text-gray-900">Best Practices</h2>
            <p class="text-gray-600 text-sm">Recommendations for safe configuration</p>
          </div>
        </div>

        <div class="prose max-w-none">
          <div class="space-y-4">
            <div class="bg-green-50 border-l-4 border-green-500 p-4">
              <h4 class="font-bold text-green-900 mb-2">✓ DO</h4>
              <ul class="list-disc list-inside space-y-1 text-sm text-green-800">
                <li>Test changes in staging environment first</li>
                <li>Document why you changed specific values</li>
                <li>Monitor platform metrics after changes</li>
                <li>Keep API keys secure and rotate regularly</li>
                <li>Set appropriate fraud detection thresholds</li>
                <li>Configure withdrawal limits per rank carefully</li>
                <li>Use version control for settings backups</li>
              </ul>
            </div>

            <div class="bg-red-50 border-l-4 border-red-500 p-4">
              <h4 class="font-bold text-red-900 mb-2">✗ DON'T</h4>
              <ul class="list-disc list-inside space-y-1 text-sm text-red-800">
                <li>Never disable fraud detection in production</li>
                <li>Don't set withdrawal_rate too high (causes losses)</li>
                <li>Avoid changing rank criteria without user notice</li>
                <li>Don't disable maintenance mode during updates</li>
                <li>Never commit API keys to git repositories</li>
                <li>Don't reduce star requirements without analysis</li>
                <li>Avoid frequent token price changes</li>
              </ul>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
              <h4 class="font-bold text-yellow-900 mb-2">⚠️ CAUTION</h4>
              <ul class="list-disc list-inside space-y-1 text-sm text-yellow-800">
                <li>Changing commission rates affects existing users immediately</li>
                <li>Withdrawal rate changes impact platform profitability</li>
                <li>Star rating decay affects user withdrawal priority</li>
                <li>Task distribution changes may confuse users</li>
                <li>Liquidity thresholds trigger automatic alerts</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Troubleshooting -->
      <div id="troubleshooting" class="bg-white rounded-xl shadow-md border border-gray-200 p-6 scroll-mt-24">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-orange-600 rounded-lg flex items-center justify-center text-2xl">
            🔧
          </div>
          <div>
            <h2 class="text-2xl font-bold text-gray-900">Troubleshooting</h2>
            <p class="text-gray-600 text-sm">Common issues and solutions</p>
          </div>
        </div>

        <div class="prose max-w-none">
          <div class="space-y-4">
            <div class="border-l-4 border-blue-500 bg-blue-50 p-4">
              <h4 class="font-bold text-blue-900 mb-2">Settings not saving</h4>
              <p class="text-sm text-blue-800 mb-2">Check:</p>
              <ul class="list-disc list-inside space-y-1 text-sm text-blue-800 ml-4">
                <li>Browser console for JavaScript errors</li>
                <li>Laravel logs in <code class="bg-blue-100 px-1 rounded">storage/logs</code></li>
                <li>Database connection is active</li>
                <li>CSRF token is valid</li>
              </ul>
            </div>

            <div class="border-l-4 border-purple-500 bg-purple-50 p-4">
              <h4 class="font-bold text-purple-900 mb-2">AI task generation not working</h4>
              <p class="text-sm text-purple-800 mb-2">Verify:</p>
              <ul class="list-disc list-inside space-y-1 text-sm text-purple-800 ml-4">
                <li>Groq API key is valid and has credits</li>
                <li>Cron job is running (check <code class="bg-purple-100 px-1 rounded">php artisan schedule:run</code>)</li>
                <li>Task count is below threshold</li>
                <li><code class="bg-purple-100 px-1 rounded">ai_task_generation_enabled</code> is true</li>
              </ul>
            </div>

            <div class="border-l-4 border-orange-500 bg-orange-50 p-4">
              <h4 class="font-bold text-orange-900 mb-2">Email notifications failing</h4>
              <p class="text-sm text-orange-800 mb-2">Check:</p>
              <ul class="list-disc list-inside space-y-1 text-sm text-orange-800 ml-4">
                <li>SMTP credentials are correct</li>
                <li>Port and encryption settings match provider</li>
                <li>Test with <code class="bg-orange-100 px-1 rounded">php artisan tinker</code> → <code class="bg-orange-100 px-1 rounded">Mail::raw('test', ...)</code></li>
                <li>Check mail logs for errors</li>
              </ul>
            </div>

            <div class="border-l-4 border-green-500 bg-green-50 p-4">
              <h4 class="font-bold text-green-900 mb-2">JSON fields corrupted</h4>
              <p class="text-sm text-green-800 mb-2">Solution:</p>
              <ul class="list-disc list-inside space-y-1 text-sm text-green-800 ml-4">
                <li>Access database directly via phpMyAdmin or CLI</li>
                <li>Restore from backup if available</li>
                <li>Re-enter values manually through admin panel</li>
                <li>Validate JSON using online tools before saving</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Back to Top -->
    <div class="flex justify-center mt-8 pb-8">
      <a href="#" class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl transition-colors shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
        Back to Top
      </a>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

defineProps({
  settings: Object
});

const sections = ref([
  { id: 'overview', title: 'Overview', icon: '📋' },
  { id: 'app-config', title: 'App Configuration', icon: '📱' },
  { id: 'ai-generation', title: 'AI Task Generation', icon: '🤖' },
  { id: 'financial', title: 'Financial Settings', icon: '💰' },
  { id: 'quick-reference', title: 'Quick Reference', icon: '⚡' },
  { id: 'api-routes', title: 'API Routes', icon: '🔌' },
  { id: 'best-practices', title: 'Best Practices', icon: '✨' },
  { id: 'troubleshooting', title: 'Troubleshooting', icon: '🔧' },
]);
</script>

<style scoped>
.scroll-mt-24 {
  scroll-margin-top: 6rem;
}

code {
  font-family: 'Courier New', Courier, monospace;
}
</style>
