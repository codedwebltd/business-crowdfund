<template>
  <UserLayout title="Dashboard">
    <!-- Announcement Carousel -->
    <AnnouncementCarousel :announcements="announcements" />

    <!-- Welcome Banner - Mobile App Style -->
    <div class="bg-white/5 backdrop-blur-xl rounded-3xl border border-white/10 shadow-2xl overflow-hidden mb-6">
      <!-- Gradient Header -->
      <div class="relative bg-gradient-to-r from-orange-500 to-purple-600 px-6 py-8 overflow-hidden">
        <!-- Animated Background Patterns -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-black/10 rounded-full blur-2xl"></div>

        <div class="relative z-10">
          <p class="text-white/80 text-sm font-medium mb-1">{{ getGreeting() }}</p>
          <h1 class="text-white text-2xl md:text-3xl font-bold tracking-tight">
            {{ user.full_name }}
          </h1>
        </div>
      </div>

      <!-- Stats Cards Container -->
      <div class="p-6 space-y-4">
        <!-- Account Status Card -->
        <div class="bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-sm rounded-2xl p-5 border border-white/10">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
              <div class="p-2.5 bg-gradient-to-br from-orange-500/20 to-purple-600/20 rounded-xl">
                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
              </div>
              <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Account Status</p>
                <p class="text-sm text-white/90 mt-0.5">{{ getStatusMessage() }}</p>
              </div>
            </div>
            <div :class="[
              'flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold uppercase tracking-wide',
              user.status === 'ACTIVE' ? 'bg-green-500/20 text-green-400 border border-green-500/30' :
              user.status === 'PENDING' ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30' :
              'bg-gray-500/20 text-gray-400 border border-gray-500/30'
            ]">
              <span :class="[
                'w-2 h-2 rounded-full',
                user.status === 'ACTIVE' ? 'bg-green-400 animate-pulse' :
                user.status === 'PENDING' ? 'bg-yellow-400 animate-pulse' :
                'bg-gray-400'
              ]"></span>
              {{ user.status }}
            </div>
          </div>

          <!-- Mini Stats Row -->
          <div class="grid grid-cols-2 gap-3 pt-4 border-t border-white/10">
            <!-- Membership -->
            <div class="flex items-center gap-2.5">
              <div class="p-2 bg-purple-500/20 rounded-lg">
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
              </div>
              <div>
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Plan</p>
                <p class="text-sm font-bold text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-purple-500">
                  {{ user.plan?.display_name || user.plan?.name || 'None' }}
                </p>
              </div>
            </div>

            <!-- Member Since -->
            <div class="flex items-center gap-2.5">
              <div class="p-2 bg-blue-500/20 rounded-lg">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div>
                <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Member</p>
                <p class="text-sm font-bold text-white">{{ getAccountAge() }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Task Ban Warning (if banned) -->
        <div v-if="user.task_ban_until && new Date(user.task_ban_until) > new Date()" class="bg-gradient-to-br from-red-500/10 to-transparent rounded-2xl p-5 border border-red-500/30 mb-4">
          <div class="flex items-center gap-3 mb-3">
            <div class="p-2 bg-red-500/20 rounded-lg">
              <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-sm font-bold text-red-400">🚫 Task Access Suspended</h3>
              <p class="text-xs text-gray-300 mt-1">
                Banned until: {{ new Date(user.task_ban_until).toLocaleString('en-US', {
                  month: 'short',
                  day: 'numeric',
                  year: 'numeric',
                  hour: '2-digit',
                  minute: '2-digit'
                }) }}
              </p>
            </div>
          </div>
          <p class="text-xs text-gray-400">
            Your task access has been temporarily suspended due to suspicious activity. Contact support if you believe this is an error.
          </p>
        </div>

        <!-- Progress Bar (for active users) -->
        <div v-if="user.status === 'ACTIVE'" class="bg-gradient-to-br from-white/5 to-transparent rounded-2xl p-5 border border-white/10">
          <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
              <div class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></div>
              <span class="text-xs font-bold text-white uppercase tracking-wider">Task Progress</span>
            </div>
            <span class="text-lg font-black text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-purple-500">
              {{ getDailyProgress() }}%
            </span>
          </div>
          <div class="w-full bg-gray-800/50 rounded-full h-3 overflow-hidden border border-white/10">
            <div
              class="h-full bg-gradient-to-r from-orange-500 via-orange-400 to-purple-600 rounded-full transition-all duration-700 shadow-lg shadow-orange-500/50 relative overflow-hidden"
              :style="{ width: getDailyProgress() + '%' }"
            >
              <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
            </div>
          </div>
        </div>

        <!-- Token Rate Card (if fluctuation enabled) -->
        <div v-if="tokenFluctuationEnabled" class="bg-gradient-to-br from-white/5 to-transparent rounded-2xl p-5 border border-white/10 mt-4">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
              <div class="p-2 bg-gradient-to-br from-green-500/20 to-emerald-600/20 rounded-lg">
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
              </div>
              <span class="text-xs font-bold text-white uppercase tracking-wider">Token Rate</span>
            </div>
            <div :class="[
              'flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold uppercase',
              tokenRate.trend === 'up' ? 'bg-green-500/20 text-green-400' :
              tokenRate.trend === 'down' ? 'bg-red-500/20 text-red-400' :
              'bg-gray-500/20 text-gray-400'
            ]">
              <span v-if="tokenRate.trend === 'up'">↑</span>
              <span v-else-if="tokenRate.trend === 'down'">↓</span>
              <span v-else>→</span>
              {{ tokenRate.trend_percentage }}
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">CROW Price</p>
              <p class="text-lg font-black text-white">₦{{ tokenRate.token_price }}</p>
            </div>
            <div>
              <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Rate</p>
              <p class="text-lg font-black text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-purple-500">
                {{ (tokenRate.withdrawal_rate * 100).toFixed(0) }}%
              </p>
            </div>
          </div>

          <div v-if="tokenRate.is_good_time" class="mt-3 pt-3 border-t border-white/10 flex items-center gap-2 text-xs text-green-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            <span class="font-semibold">Great time to withdraw!</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Plan Selection (if not activated) -->
    <div v-if="user.status === 'PENDING' || user.status === 'UNVERIFIED'" class="mb-6">
      <div class="bg-gradient-to-r from-orange-500/20 to-purple-600/20 backdrop-blur-xl rounded-2xl border border-orange-500/30 p-6 mb-6">
        <div class="flex items-start gap-4">
          <div class="p-3 bg-orange-500/30 rounded-xl">
            <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <div class="flex-1">
            <h4 class="text-lg font-bold text-white mb-2">Activate Your Account</h4>
            <p class="text-gray-300 text-sm">Choose a plan below to start earning. You need an active plan to access tasks and earn rewards.</p>
          </div>
        </div>
      </div>

      <!-- Plans Grid - Compact & Expandable -->
      <div class="max-w-5xl mx-auto">
        <div class="grid md:grid-cols-3 gap-4">
          <div
            v-for="plan in plans"
            :key="plan.id"
            :class="[
              'group relative bg-white/5 backdrop-blur-xl rounded-xl border transition-all duration-300 hover:bg-white/10',
              plan.is_popular
                ? 'border-orange-500/50 shadow-lg shadow-orange-500/20 md:scale-105 md:z-10'
                : 'border-white/10 hover:border-orange-500/30',
              selectedPlan === plan.id ? 'ring-2 ring-orange-500 scale-[1.02]' : ''
            ]"
            @click="selectPlan(plan)"
          >
            <!-- Popular Badge -->
            <div v-if="plan.is_popular" class="absolute -top-3 left-1/2 transform -translate-x-1/2">
              <span class="bg-gradient-to-r from-orange-500 to-orange-600 text-white text-xs font-bold px-4 py-1 rounded-full shadow-lg">
                ⭐ POPULAR
              </span>
            </div>

            <div class="p-5">
              <!-- Header -->
              <div class="text-center mb-4">
                <h3 class="text-xl font-bold text-white mb-1">{{ plan.display_name }}</h3>
                <p class="text-gray-400 text-xs line-clamp-2">{{ plan.description }}</p>
              </div>

              <!-- Price -->
              <div class="text-center mb-4 py-3 bg-white/5 rounded-lg">
                <div class="text-3xl font-bold text-white">₦{{ (plan.price || 0).toLocaleString() }}</div>
                <div class="text-xs text-gray-400 mt-1">one-time payment</div>
              </div>

              <!-- Key Features -->
              <div class="space-y-2 mb-4">
                <div class="flex items-center justify-between text-sm">
                  <span class="text-gray-300">Daily Tasks</span>
                  <span class="text-white font-semibold">{{ plan.features.max_daily_tasks }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-gray-300">Multiplier</span>
                  <span class="text-orange-400 font-semibold">{{ plan.features.task_reward_multiplier }}x</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-gray-300">Withdrawals</span>
                  <span class="text-white font-semibold">{{ plan.features.withdrawals_per_day }}/day</span>
                </div>
              </div>

              <!-- CTA Button -->
              <button :class="[
                'w-full py-2.5 px-4 rounded-lg font-semibold text-sm transition-all duration-200 transform hover:scale-[1.02]',
                plan.is_popular
                  ? 'bg-gradient-to-r from-orange-500 to-purple-600 text-white shadow-lg shadow-orange-500/30 hover:shadow-xl'
                  : 'bg-white/10 text-white hover:bg-white/20 border border-white/20'
              ]">
                Select {{ plan.name.charAt(0).toUpperCase() + plan.name.slice(1) }}
              </button>

              <!-- Expandable Details -->
              <button
                @click.stop="togglePlanDetails(plan.id)"
                class="w-full mt-2 text-xs text-gray-400 hover:text-white transition-colors flex items-center justify-center gap-1"
              >
                <span>{{ expandedPlan === plan.id ? 'Less' : 'More' }} details</span>
                <svg :class="['w-3 h-3 transition-transform', expandedPlan === plan.id ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>

              <!-- Expanded Content -->
              <Transition name="expand">
                <div v-if="expandedPlan === plan.id" class="mt-3 pt-3 border-t border-white/10 space-y-1.5">
                  <div v-for="(category, index) in plan.features.task_categories?.slice(0, 3)" :key="index"
                       class="flex items-center gap-2 text-xs text-gray-300">
                    <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ category.replace('_', ' ') }}
                  </div>
                  <div v-if="plan.features.priority_support" class="flex items-center gap-2 text-xs text-orange-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Priority Support
                  </div>
                  <div v-if="plan.features.instant_withdrawal" class="flex items-center gap-2 text-xs text-orange-400">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Instant Withdrawal
                  </div>
                </div>
              </Transition>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Grid (for ACTIVE users only) -->
    <div v-else-if="user.status === 'ACTIVE'" class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <StatCard
        title="Total Earnings"
        :value="'₦' + formatNumber(stats?.totalEarnings || 0)"
        icon="currency"
        color="green"
      />
      <StatCard
        title="Available Balance"
        :value="'₦' + formatNumber(stats?.availableBalance || 0)"
        icon="wallet"
        color="blue"
      />
      <StatCard
        title="Tasks Completed"
        :value="formatNumber(stats.tasksCompleted)"
        icon="check"
        color="purple"
      />
      <StatCard
        title="Referrals"
        :value="formatNumber(stats.totalReferrals)"
        icon="users"
        color="orange"
      />
    </div>

    <!-- Tasks Section (for activated users) -->
    <div v-if="user.status === 'ACTIVE' && tasks.length > 0" class="mb-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-white flex items-center gap-2">
          <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
          </svg>
          Today's Tasks
        </h3>
        <span class="text-sm text-gray-400">{{ tasks.filter(t => t.status === 'COMPLETED').length }}/{{ tasks.length }} completed</span>
      </div>

      <div class="grid gap-4">
        <div v-for="task in tasks" :key="task.id"
             class="group relative bg-gradient-to-br from-white/10 via-white/5 to-transparent backdrop-blur-xl rounded-2xl border border-white/20 p-5 hover:border-orange-500/50 hover:shadow-2xl hover:shadow-orange-500/20 transition-all duration-300 overflow-hidden">

          <!-- Animated Background -->
          <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-500/5 to-purple-500/5 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-500"></div>

          <div class="relative z-10">
            <!-- Task Header -->
            <div class="flex items-start justify-between mb-4">
              <div class="flex items-start gap-3 flex-1">
                <!-- Category Icon -->
                <div :class="[
                  'p-3 rounded-xl shrink-0',
                  task.task_template.category === 'VIDEO' ? 'bg-purple-500/20' :
                  task.task_template.category === 'SURVEY' ? 'bg-blue-500/20' :
                  task.task_template.category === 'APP_SYNC' ? 'bg-green-500/20' :
                  'bg-yellow-500/20'
                ]">
                  <svg class="w-6 h-6" :class="[
                    task.task_template.category === 'VIDEO' ? 'text-purple-400' :
                    task.task_template.category === 'SURVEY' ? 'text-blue-400' :
                    task.task_template.category === 'APP_SYNC' ? 'text-green-400' :
                    'text-yellow-400'
                  ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="task.task_template.category === 'VIDEO'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    <path v-if="task.task_template.category === 'VIDEO'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    <path v-if="task.task_template.category === 'SURVEY'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    <path v-if="task.task_template.category === 'APP_SYNC'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    <path v-if="task.task_template.category === 'PRODUCT_REVIEW'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                  </svg>
                </div>

                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 mb-1">
                    <span :class="[
                      'px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider',
                      task.task_template.category === 'VIDEO' ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' :
                      task.task_template.category === 'SURVEY' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' :
                      task.task_template.category === 'APP_SYNC' ? 'bg-green-500/20 text-green-400 border border-green-500/30' :
                      'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30'
                    ]">{{ task.task_template.category.replace('_', ' ') }}</span>
                  </div>
                  <h4 class="text-white font-semibold text-sm mb-1 line-clamp-2">{{ task.task_template.title }}</h4>
                  <p class="text-gray-400 text-xs line-clamp-1">{{ task.task_template.description }}</p>
                </div>
              </div>

              <!-- Status Badge -->
              <div :class="[
                'px-3 py-1.5 rounded-lg text-xs font-bold uppercase shrink-0',
                task.status === 'COMPLETED' ? 'bg-green-500/20 text-green-400 border border-green-500/30' :
                task.status === 'IN_PROGRESS' ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30' :
                task.status === 'EXPIRED' ? 'bg-red-500/20 text-red-400 border border-red-500/30' :
                'bg-gray-500/20 text-gray-400 border border-gray-500/30'
              ]">
                <span v-if="task.status === 'COMPLETED'" class="flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  Done
                </span>
                <span v-else-if="task.status === 'EXPIRED'">Expired</span>
                <span v-else-if="task.status === 'IN_PROGRESS'">Active</span>
                <span v-else>Pending</span>
              </div>
            </div>

            <!-- Task Details -->
            <div class="flex items-center justify-between gap-4 mb-4">
              <!-- Reward -->
              <div class="flex items-center gap-2">
                <div class="p-2 bg-green-500/20 rounded-lg">
                  <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Reward</p>
                  <p class="text-lg font-bold text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-500">₦{{ task.reward_amount }}</p>
                </div>
              </div>

              <!-- Countdown Timer -->
              <div v-if="task.status !== 'COMPLETED' && task.status !== 'EXPIRED'" class="flex items-center gap-2">
                <div :class="[
                  'p-2 rounded-lg',
                  isTaskExpiringSoon(task.expires_at) ? 'bg-red-500/20 animate-pulse' : 'bg-orange-500/20'
                ]">
                  <svg :class="[
                    'w-4 h-4',
                    isTaskExpiringSoon(task.expires_at) ? 'text-red-400' : 'text-orange-400'
                  ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Expires In</p>
                  <p :class="[
                    'text-sm font-bold',
                    isTaskExpiringSoon(task.expires_at) ? 'text-red-400' : 'text-orange-400'
                  ]">{{ getTimeRemaining(task.expires_at) }}</p>
                </div>
              </div>
            </div>

            <!-- Action Button -->
            <button
              v-if="task.status === 'PENDING' || task.status === 'IN_PROGRESS'"
              @click="startTask(task)"
              class="w-full bg-gradient-to-r from-orange-500 to-purple-600 text-white py-3 rounded-xl font-bold hover:shadow-xl hover:shadow-orange-500/50 transition-all flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
              </svg>
              {{ task.status === 'IN_PROGRESS' ? 'Continue Task' : 'Start Task' }}
            </button>

            <div v-else-if="task.status === 'COMPLETED'" class="w-full bg-green-500/20 text-green-400 py-3 rounded-xl font-bold flex items-center justify-center gap-2 border border-green-500/30">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Completed
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions (for activated users) -->
    <div v-if="user.status === 'ACTIVE'" class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 shadow-2xl p-6 mb-6">
      <h3 class="text-lg font-bold text-white mb-4">Quick Actions</h3>
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <Link href="/tasks" class="flex flex-col items-center gap-2 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-all">
          <div class="p-3 bg-purple-500/20 rounded-lg">
            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
          </div>
          <span class="text-sm font-medium text-white">Start Task</span>
        </Link>

        <Link href="/withdrawals" class="flex flex-col items-center gap-2 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-all">
          <div class="p-3 bg-green-500/20 rounded-lg">
            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <span class="text-sm font-medium text-white">Withdraw</span>
        </Link>

        <Link href="/referrals" class="flex flex-col items-center gap-2 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-all">
          <div class="p-3 bg-orange-500/20 rounded-lg">
            <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </div>
          <span class="text-sm font-medium text-white">Invite Friends</span>
        </Link>

        <Link href="/settings" class="flex flex-col items-center gap-2 p-4 bg-white/5 rounded-xl hover:bg-white/10 transition-all">
          <div class="p-3 bg-blue-500/20 rounded-lg">
            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <span class="text-sm font-medium text-white">Settings</span>
        </Link>
      </div>
    </div>

    <!-- Task Viewer Modal -->
    <TaskViewer
      v-if="selectedTask"
      :show="showTaskViewer"
      :task="selectedTask"
      @close="closeTaskViewer"
      @completed="handleTaskCompleted"
    />
  </UserLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import AnnouncementCarousel from '@/Components/AnnouncementCarousel.vue';
import TaskViewer from '@/Components/TaskViewer.vue';
import { useToast } from '@/composables/useToast';
import Swal from 'sweetalert2';

const { info } = useToast();

const props = defineProps({
  user: Object,
  plans: Array,
  tasks: {
    type: Array,
    default: () => []
  },
  announcements: {
    type: Array,
    default: () => []
  },
  tokenFluctuationEnabled: {
    type: Boolean,
    default: false
  },
  tokenRate: {
    type: Object,
    default: () => ({
      token_price: 850,
      withdrawal_rate: 0.68,
      trend: 'stable',
      trend_percentage: '0%',
      is_good_time: false
    })
  },
  stats: {
    type: Object,
    default: () => ({
      totalEarnings: 0,
      availableBalance: 0,
      tasksCompleted: 0,
      totalReferrals: 0
    })
  }
});

const selectedPlan = ref(null);
const expandedPlan = ref(null);
const tokenRate = ref(props.tokenRate);
const currentTime = ref(Date.now());
const showTaskViewer = ref(false);
const selectedTask = ref(null);
let timerInterval = null;

// Countdown timer - updates every second and auto-expires tasks
timerInterval = setInterval(() => {
  currentTime.value = Date.now();

  // Auto-expire tasks
  props.tasks.forEach(task => {
    if ((task.status === 'PENDING' || task.status === 'IN_PROGRESS') &&
        new Date(task.expires_at).getTime() < currentTime.value) {
      task.status = 'EXPIRED';
    }
  });
}, 1000);

// WebSocket for token rate updates
onMounted(() => {
  if (props.tokenFluctuationEnabled && window.Echo) {
    window.Echo.channel('token-rates')
      .listen('.rate.updated', (event) => {
        // Update token rate data
        tokenRate.value = {
          token_price: event.token_price,
          withdrawal_rate: event.withdrawal_rate,
          trend: event.trend,
          trend_percentage: event.trend_percentage,
          is_good_time: event.is_good_time
        };

        // Show SweetAlert with change details
        const trendIcon = event.trend === 'up' ? 'success' : event.trend === 'down' ? 'warning' : 'info';
        const trendEmoji = event.trend === 'up' ? '📈' : event.trend === 'down' ? '📉' : '➡️';

        Swal.fire({
          icon: trendIcon,
          title: `${trendEmoji} Token Rate Updated!`,
          html: `
            <div class="text-left">
              <p class="mb-2"><strong>New CROW Price:</strong> ₦${event.token_price}</p>
              <p class="mb-2"><strong>Withdrawal Rate:</strong> ${(event.withdrawal_rate * 100).toFixed(0)}%</p>
              <p class="mb-2"><strong>Change:</strong> ${event.trend_percentage}</p>
              ${event.change_reason ? `<p class="mt-3 text-sm text-gray-600">${event.change_reason}</p>` : ''}
            </div>
          `,
          background: '#1f2937',
          color: '#fff',
          confirmButtonColor: '#f97316',
          confirmButtonText: event.is_good_time ? 'Withdraw Now' : 'Got it',
          showCancelButton: event.is_good_time,
          cancelButtonText: 'Later',
        }).then((result) => {
          if (result.isConfirmed && event.is_good_time) {
            window.location.href = '/withdrawals';
          }
        });
      });
  }
});

onUnmounted(() => {
  if (timerInterval) {
    clearInterval(timerInterval);
  }
  if (window.Echo) {
    window.Echo.leaveChannel('token-rates');
  }
});

const formatNumber = (num) => {
  return new Intl.NumberFormat().format(num || 0);
};

const getGreeting = () => {
  const hour = new Date().getHours();
  if (hour < 12) return 'Good morning! Ready to earn today?';
  if (hour < 18) return 'Good afternoon! Keep up the great work!';
  return 'Good evening! Finish strong today!';
};

const togglePlanDetails = (planId) => {
  expandedPlan.value = expandedPlan.value === planId ? null : planId;
};

const selectPlan = (plan) => {
  selectedPlan.value = plan.id;
  info(`Plan selection coming soon! You selected: ${plan.display_name}`);
};

const getStatusMessage = () => {
  if (props.user.status === 'ACTIVE') return 'All systems operational';
  if (props.user.status === 'PENDING') return 'Awaiting plan activation';
  return 'Please verify your account';
};

const getAccountAge = () => {
  if (!props.user.created_at) return '0d';
  const created = new Date(props.user.created_at);
  const now = new Date();
  const diffTime = Math.abs(now - created);
  const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays === 0) return 'Today';
  if (diffDays < 30) return `${diffDays}d`;
  if (diffDays < 365) return `${Math.floor(diffDays / 30)}mo`;
  return `${Math.floor(diffDays / 365)}yr`;
};

const formatJoinDate = () => {
  if (!props.user.created_at) return 'Recently';
  const date = new Date(props.user.created_at);
  return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
};

const getTimeRemaining = (expiresAt) => {
  const now = currentTime.value;
  const expiry = new Date(expiresAt).getTime();
  const diff = expiry - now;

  if (diff <= 0) return 'Expired';

  const hours = Math.floor(diff / (1000 * 60 * 60));
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((diff % (1000 * 60)) / 1000);

  // Red warning if < 5 minutes
  if (diff < 5 * 60 * 1000) {
    return `⚠️ ${minutes}m ${seconds}s`;
  }

  if (hours > 0) return `${hours}h ${minutes}m`;
  if (minutes > 0) return `${minutes}m ${seconds}s`;
  return `${seconds}s`;
};

const isTaskExpiringSoon = (expiresAt) => {
  const now = currentTime.value;
  const expiry = new Date(expiresAt).getTime();
  const diff = expiry - now;
  return diff > 0 && diff < 5 * 60 * 1000; // Less than 5 minutes
};

const startTask = (task) => {
  // Check if user is banned - show warning but allow them to proceed
  if (props.user.task_ban_until && new Date(props.user.task_ban_until) > new Date()) {
    const banEnd = new Date(props.user.task_ban_until).toLocaleString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });

    Swal.fire({
      icon: 'warning',
      title: '⚠️ Account Suspended',
      html: `
        <div class="text-left">
          <p class="mb-3 text-gray-300">Your task access is suspended until <strong class="text-orange-400">${banEnd}</strong></p>
          <p class="mb-3 text-yellow-400">⚠️ You can view this task, but:</p>
          <ul class="list-disc list-inside text-gray-300 mb-3 space-y-1">
            <li>Task completion will be REJECTED</li>
            <li>No earnings will be credited</li>
            <li>Your submission won't count</li>
          </ul>
          <p class="text-sm text-gray-400">Contact support if you believe this is an error.</p>
        </div>
      `,
      background: '#1f2937',
      color: '#fff',
      showCancelButton: true,
      confirmButtonText: 'View Task Anyway',
      cancelButtonText: 'Go Back',
      confirmButtonColor: '#f97316',
      cancelButtonColor: '#6b7280',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        // User chose to view task anyway - proceed
        proceedWithTask(task);
      }
    });
    return;
  }

  // Check if expired
  if (new Date(task.expires_at).getTime() < Date.now()) {
    Swal.fire({
      icon: 'error',
      title: 'Task Expired',
      text: 'This task has expired and can no longer be completed.',
      background: '#1f2937',
      color: '#fff',
      confirmButtonColor: '#ef4444'
    });
    return;
  }

  // Check if user has reached daily task limit
  const completedToday = props.stats.tasksCompletedToday || 0;
  const maxDailyTasks = props.tasks?.length || 8; // Use assigned tasks or fallback to 8

  if (completedToday >= maxDailyTasks) {
    Swal.fire({
      icon: 'warning',
      title: '🎯 Daily Task Limit Reached!',
      html: `
        <div class="text-left">
          <p class="mb-3">You've completed <strong class="text-orange-400">${completedToday}/${maxDailyTasks}</strong> tasks today. Great work! 🎉</p>
          <p class="mb-4 text-gray-300">Want to earn even more?</p>
          <div class="space-y-3">
            <div class="flex items-start gap-2 bg-orange-500/10 p-3 rounded-lg border border-orange-500/30">
              <span class="text-xl">✨</span>
              <div>
                <p class="font-bold text-orange-400">Upgrade Your Plan</p>
                <p class="text-sm text-gray-300">Unlock more daily tasks and higher earnings</p>
              </div>
            </div>
            <div class="flex items-start gap-2 bg-purple-500/10 p-3 rounded-lg border border-purple-500/30">
              <span class="text-xl">👥</span>
              <div>
                <p class="font-bold text-purple-400">Refer Friends</p>
                <p class="text-sm text-gray-300">Earn unlimited commissions from your team's tasks</p>
              </div>
            </div>
          </div>
        </div>
      `,
      background: '#1f2937',
      color: '#fff',
      showCancelButton: true,
      confirmButtonText: '✨ Upgrade Plan',
      cancelButtonText: '👥 Refer & Earn',
      confirmButtonColor: '#f97316',
      cancelButtonColor: '#8b5cf6',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        // Redirect to upgrade plan page
        window.location.href = '/upgrade-plan';
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        // Redirect to referral page
        window.location.href = '/referrals';
      }
    });
    return;
  }

  proceedWithTask(task);
};

// Helper function to actually open the task
const proceedWithTask = (task) => {
  selectedTask.value = task;
  showTaskViewer.value = true;
};

const closeTaskViewer = () => {
  showTaskViewer.value = false;
  selectedTask.value = null;
};

const handleTaskCompleted = () => {
  closeTaskViewer();
  // Refresh page to update task list
  window.location.reload();
};

const getDailyProgress = () => {
  if (!props.stats || !props.user.plan) return 0;

  // Use TODAY's completed tasks only (resets daily)
  const completedToday = props.stats.tasksCompletedToday || 0;

  // Use today's total assigned tasks as the denominator (no hardcoded values)
  const todayTasks = props.tasks?.length || 1; // Avoid division by zero

  return Math.round(Math.min((completedToday / todayTasks) * 100, 100));
};
</script>

<style scoped>
.expand-enter-active, .expand-leave-active {
  transition: all 0.3s ease;
  max-height: 200px;
  overflow: hidden;
}

.expand-enter-from, .expand-leave-to {
  max-height: 0;
  opacity: 0;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>

