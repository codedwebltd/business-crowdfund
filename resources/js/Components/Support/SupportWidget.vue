<template>
  <!-- Floating Greeting Bubble -->
  <Transition name="greeting-slide">
    <div v-if="showGreeting && !isOpen && !greetingDismissed"
         class="fixed bottom-24 right-4 sm:bottom-28 sm:right-6 z-[9997] max-w-[300px] animate-float">
      <div class="relative bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
        <button @click.stop="dismissGreeting"
                class="absolute top-2 right-2 w-6 h-6 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-all z-10">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
        <div class="bg-gradient-to-r from-orange-500 to-purple-600 px-4 py-3 flex items-center gap-3">
          <div class="relative">
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center ring-2 ring-white/30">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
            </div>
            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 rounded-full border-2 border-white"></span>
          </div>
          <div>
            <p class="text-white font-semibold text-sm">Support Team</p>
            <p class="text-white/70 text-xs flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
              Online now
            </p>
          </div>
        </div>
        <div class="p-4">
          <p class="text-gray-700 text-sm leading-relaxed">{{ greetingMessage }}</p>
          <button @click="toggleWidget"
                  class="mt-3 w-full py-2.5 bg-gradient-to-r from-orange-500 to-purple-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-orange-500/30 hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            Start Chat
          </button>
        </div>
        <div class="absolute -bottom-2 right-8 w-4 h-4 bg-white transform rotate-45 border-r border-b border-gray-100"></div>
      </div>
    </div>
  </Transition>

  <!-- Floating Support Button -->
  <div class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-[9999]">
    <div v-if="(unreadCount > 0 || showGreeting) && !isOpen"
         class="absolute -top-1 -right-1 min-w-[22px] h-[22px] px-1 flex items-center justify-center bg-gradient-to-r from-red-500 to-pink-500 text-white text-[10px] font-bold rounded-full border-2 border-white shadow-lg z-10"
         :class="{ 'animate-bounce': unreadCount > 0 }">
      {{ unreadCount > 0 ? (unreadCount > 9 ? '9+' : unreadCount) : '1' }}
    </div>
    <button
      @click="toggleWidget"
      class="group relative w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gradient-to-r from-orange-500 to-purple-600 text-white shadow-2xl hover:shadow-orange-500/50 hover:scale-110 transition-all duration-300 flex items-center justify-center"
      :class="{ 'animate-pulse-slow': showGreeting && !isOpen && !greetingDismissed }"
    >
      <Transition name="icon-fade" mode="out-in">
        <svg v-if="!isOpen" key="chat" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <svg v-else key="close" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </Transition>
      <span class="absolute inset-0 rounded-full bg-white opacity-0 group-hover:opacity-20 group-active:opacity-30 transition-opacity"></span>
    </button>
  </div>

  <!-- Chat Window -->
  <Transition name="widget-slide">
    <div v-if="isOpen"
         ref="chatWindowRef"
         :class="['chat-window bg-slate-900 shadow-2xl border border-white/10 overflow-hidden z-[9998] flex flex-col', { 'desktop-mode': !isMobileView }]">

      <!-- Professional Chat Header -->
      <div class="bg-gradient-to-r from-orange-500 via-pink-500 to-purple-600 p-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <!-- Support Avatar with Status -->
            <div class="relative">
              <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center ring-2 ring-white/40 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
              </div>
              <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-400 rounded-full border-2 border-white shadow-sm"></span>
            </div>
            <div>
              <h3 class="text-white font-bold text-base">Customer Support</h3>
              <p class="text-white/80 text-xs flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                We typically reply in minutes
              </p>
            </div>
          </div>

          <!-- Header Actions -->
          <div class="flex items-center gap-1">
            <!-- User Info Dropdown (if authenticated) -->
            <div v-if="isAuthenticated" class="relative" ref="userInfoRef">
              <button @click.stop="showUserInfo = !showUserInfo"
                      class="p-2 text-white/80 hover:text-white hover:bg-white/20 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
              </button>
              <Transition name="dropdown">
                <div v-if="showUserInfo"
                     class="absolute right-0 top-full mt-2 w-64 bg-white rounded-2xl shadow-2xl overflow-hidden z-50">
                  <div class="bg-gradient-to-r from-orange-500 to-purple-600 px-4 py-3">
                    <p class="text-white/80 text-xs">Your Contact Info</p>
                  </div>
                  <div class="p-4 space-y-3">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-purple-500 flex items-center justify-center text-white font-bold">
                        {{ userName.charAt(0).toUpperCase() }}
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-gray-900 font-semibold text-sm truncate">{{ userName }}</p>
                        <p class="text-gray-500 text-xs truncate">{{ userEmail }}</p>
                      </div>
                    </div>
                    <div class="pt-2 border-t border-gray-100">
                      <a href="/settings"
                         class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Update Contact Details
                      </a>
                    </div>
                  </div>
                </div>
              </Transition>
            </div>

            <!-- Menu Dropdown -->
            <div class="relative" ref="menuRef">
              <button @click.stop="showMenu = !showMenu"
                      class="p-2 text-white/80 hover:text-white hover:bg-white/20 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                </svg>
              </button>
              <Transition name="dropdown">
                <div v-if="showMenu"
                     class="absolute right-0 top-full mt-2 w-52 bg-white rounded-2xl shadow-2xl overflow-hidden z-50">
                  <a href="/support"
                     class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-all">
                    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                      <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                      </svg>
                    </div>
                    <span>All Tickets</span>
                  </a>
                  <button @click="startNewTicket(); showMenu = false"
                          class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-all">
                    <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                      <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                      </svg>
                    </div>
                    <span>New Conversation</span>
                  </button>
                  <div class="border-t border-gray-100"></div>
                  <button @click="isOpen = false; showMenu = false"
                          class="w-full flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-all">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                      <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                    </div>
                    <span>Close Chat</span>
                  </button>
                </div>
              </Transition>
            </div>

            <!-- Minimize -->
            <button @click="isOpen = false" class="p-2 text-white/80 hover:text-white hover:bg-white/20 rounded-xl transition-all">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Ticket Selection View -->
      <div v-if="view === 'tickets' && tickets.length > 0" class="flex-1 overflow-hidden flex flex-col bg-gradient-to-b from-slate-900 to-slate-800">
        <!-- New Conversation Button -->
        <div class="p-4 border-b border-white/10">
          <button @click="startNewTicket"
                  class="w-full py-3.5 bg-gradient-to-r from-orange-500 to-purple-600 text-white rounded-2xl font-bold hover:shadow-lg hover:shadow-orange-500/30 hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Start New Conversation
          </button>
        </div>

        <!-- Tickets List -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-3">
          <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider px-1 mb-3">Recent Conversations</p>
          <div v-for="ticket in tickets" :key="ticket.id"
               @click="openTicket(ticket)"
               class="group relative p-4 bg-white/5 hover:bg-white/10 rounded-2xl border border-white/10 hover:border-orange-500/30 cursor-pointer transition-all">
            <!-- Unread Indicator -->
            <div v-if="ticket.has_new_admin_reply"
                 class="absolute top-3 right-3 w-3 h-3 bg-orange-500 rounded-full animate-pulse"></div>

            <div class="flex items-start gap-3">
              <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500/20 to-purple-600/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2 mb-1">
                  <p class="text-white font-semibold text-sm truncate group-hover:text-orange-400 transition-colors">{{ ticket.subject }}</p>
                  <span :class="getStatusClass(ticket.status)" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase flex-shrink-0">
                    {{ ticket.status }}
                  </span>
                </div>
                <p class="text-gray-400 text-xs truncate">{{ ticket.last_message }}</p>
                <p class="text-gray-600 text-[10px] mt-2">{{ ticket.last_message_at }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- New Ticket Form View -->
      <div v-else-if="view === 'new'" class="flex-1 overflow-hidden flex flex-col bg-gradient-to-b from-slate-900 to-slate-800">
        <div class="flex-1 overflow-y-auto custom-scrollbar p-5">
          <!-- Welcome Card -->
          <div class="text-center mb-6">
            <div class="w-20 h-20 mx-auto rounded-2xl bg-gradient-to-br from-orange-500/20 to-purple-600/20 flex items-center justify-center mb-4 shadow-lg">
              <svg class="w-10 h-10 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
              </svg>
            </div>
            <h4 class="text-white font-bold text-xl">How can we help?</h4>
            <p class="text-gray-400 text-sm mt-2">Send us a message and we'll respond as soon as possible</p>
          </div>

          <!-- Form Card -->
          <div class="bg-white/5 rounded-2xl border border-white/10 p-5 space-y-4">
            <!-- Guest Fields -->
            <template v-if="!isAuthenticated">
              <div>
                <label class="block text-gray-300 text-xs font-semibold mb-2 uppercase tracking-wider">Your Name</label>
                <div class="relative">
                  <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                  </div>
                  <input v-model="guestName" type="text" placeholder="John Doe"
                         class="w-full pl-12 pr-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 text-sm focus:outline-none focus:border-orange-500/50 focus:ring-2 focus:ring-orange-500/20 transition-all">
                </div>
              </div>
              <div>
                <label class="block text-gray-300 text-xs font-semibold mb-2 uppercase tracking-wider">Email Address</label>
                <div class="relative">
                  <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                  </div>
                  <input v-model="guestEmail" type="email" placeholder="you@example.com"
                         class="w-full pl-12 pr-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 text-sm focus:outline-none focus:border-orange-500/50 focus:ring-2 focus:ring-orange-500/20 transition-all">
                </div>
              </div>
            </template>

            <!-- Category Selection -->
            <div>
              <label class="block text-gray-300 text-xs font-semibold mb-2 uppercase tracking-wider">Topic</label>
              <div class="grid grid-cols-2 gap-2">
                <button v-for="cat in categories" :key="cat.value"
                        @click="category = cat.value"
                        :class="[
                          'p-3 rounded-xl border text-left transition-all',
                          category === cat.value
                            ? 'bg-orange-500/20 border-orange-500/50 text-orange-400'
                            : 'bg-white/5 border-white/10 text-gray-400 hover:border-white/20 hover:text-white'
                        ]">
                  <div class="flex items-center gap-2">
                    <span class="text-lg">{{ cat.icon }}</span>
                    <span class="text-xs font-medium">{{ cat.label }}</span>
                  </div>
                </button>
              </div>
            </div>

            <!-- Message -->
            <div>
              <label class="block text-gray-300 text-xs font-semibold mb-2 uppercase tracking-wider">Message</label>
              <textarea v-model="newMessage" rows="4" placeholder="Tell us how we can help you..."
                        class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 text-sm focus:outline-none focus:border-orange-500/50 focus:ring-2 focus:ring-orange-500/20 transition-all resize-none"></textarea>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="p-4 border-t border-white/10 bg-slate-900/80 backdrop-blur-sm">
          <button @click="submitNewTicket"
                  :disabled="!canSubmit || isSubmitting"
                  class="w-full py-4 bg-gradient-to-r from-orange-500 to-purple-600 text-white rounded-2xl font-bold hover:shadow-lg hover:shadow-orange-500/30 hover:scale-[1.01] transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 flex items-center justify-center gap-2">
            <svg v-if="isSubmitting" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
            {{ isSubmitting ? 'Sending...' : 'Send Message' }}
          </button>
          <button v-if="tickets.length > 0" @click="view = 'tickets'"
                  class="w-full mt-3 py-2 text-gray-400 text-sm hover:text-white transition-colors flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            View Previous Conversations
          </button>
        </div>
      </div>

      <!-- Chat View -->
      <div v-else-if="view === 'chat'" class="flex-1 overflow-hidden flex flex-col bg-gradient-to-b from-slate-900 to-slate-800">
        <!-- Chat Sub-Header -->
        <div class="px-4 py-3 bg-white/5 border-b border-white/10 flex items-center justify-between">
          <button @click="backToTickets" class="flex items-center gap-2 text-gray-400 hover:text-white transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="text-sm font-medium">Back</span>
          </button>
          <div class="flex items-center gap-2">
            <span :class="getStatusClass(currentTicket?.status)" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase">
              {{ currentTicket?.status?.replace(/_/g, ' ') }}
            </span>
            <span class="text-gray-500 text-xs">#{{ currentTicket?.ticket_number }}</span>
          </div>
        </div>

        <!-- Messages Area -->
        <div ref="messagesContainer" class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-4">
          <!-- Date Separator -->
          <div class="flex items-center gap-3 my-4">
            <div class="flex-1 h-px bg-white/10"></div>
            <span class="text-gray-500 text-xs">Today</span>
            <div class="flex-1 h-px bg-white/10"></div>
          </div>

          <!-- Messages -->
          <div v-for="message in messages" :key="message.id">
            <!-- System Message -->
            <div v-if="message.sender_type === 'system'" class="flex justify-center my-3">
              <div class="px-4 py-2 bg-yellow-500/10 border border-yellow-500/20 rounded-full">
                <p class="text-yellow-400 text-xs">{{ message.message }}</p>
              </div>
            </div>

            <!-- Admin Message -->
            <div v-else-if="message.sender_type === 'admin'" class="flex items-end gap-2">
              <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-purple-600 flex items-center justify-center flex-shrink-0 shadow-lg">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
              </div>
              <div class="max-w-[75%]">
                <p class="text-orange-400 text-xs font-medium mb-1 ml-1">{{ message.sender_name || 'Support Agent' }}</p>
                <div class="bg-white/10 rounded-2xl rounded-bl-md px-4 py-3 backdrop-blur-sm">
                  <div v-if="message.file_path" class="mb-2">
                    <img v-if="message.message_type === 'image'" :src="message.file_path"
                         class="max-w-full rounded-lg cursor-pointer hover:opacity-90 transition-opacity" @click="openImage(message.file_path)">
                    <a v-else :href="message.file_path" target="_blank" class="flex items-center gap-2 px-3 py-2 bg-black/20 rounded-lg hover:bg-black/30 transition-colors">
                      <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                      </svg>
                      <span class="text-sm text-white truncate">{{ message.file_name }}</span>
                    </a>
                  </div>
                  <p class="text-white text-sm whitespace-pre-wrap leading-relaxed">{{ message.message }}</p>
                </div>
                <p class="text-gray-500 text-[10px] mt-1 ml-1">{{ formatTime(message.created_at) }}</p>
              </div>
            </div>

            <!-- User Message -->
            <div v-else class="flex items-end justify-end gap-2">
              <div class="max-w-[75%]">
                <div :class="[
                  'rounded-2xl rounded-br-md px-4 py-3 shadow-lg transition-all',
                  message.is_failed
                    ? 'bg-red-500/50 border border-red-500/50'
                    : message.is_sending
                      ? 'bg-gradient-to-r from-orange-500/70 to-purple-600/70'
                      : 'bg-gradient-to-r from-orange-500 to-purple-600'
                ]">
                  <div v-if="message.file_path" class="mb-2">
                    <img v-if="message.message_type === 'image'" :src="message.file_path"
                         class="max-w-full rounded-lg cursor-pointer hover:opacity-90 transition-opacity" @click="openImage(message.file_path)">
                    <a v-else :href="message.file_path" target="_blank" class="flex items-center gap-2 px-3 py-2 bg-black/20 rounded-lg hover:bg-black/30 transition-colors">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                      </svg>
                      <span class="text-sm truncate">{{ message.file_name }}</span>
                    </a>
                  </div>
                  <p class="text-white text-sm whitespace-pre-wrap leading-relaxed">{{ message.message }}</p>
                </div>
                <div class="flex items-center justify-end gap-1.5 mt-1 mr-1">
                  <!-- Failed State -->
                  <button v-if="message.is_failed" @click="retryMessage(message)"
                          class="flex items-center gap-1 text-red-400 text-[10px] hover:text-red-300">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Retry
                  </button>
                  <!-- Sending State -->
                  <svg v-else-if="message.is_sending" class="w-3 h-3 text-gray-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                  </svg>
                  <!-- Normal State -->
                  <template v-else>
                    <p class="text-gray-500 text-[10px]">{{ formatTime(message.created_at) }}</p>
                    <svg v-if="message.is_read" class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </template>
                </div>
              </div>
            </div>
          </div>

          <!-- Typing Indicator -->
          <div v-if="isAdminTyping" class="flex items-end gap-2">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-purple-600 flex items-center justify-center flex-shrink-0">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
            </div>
            <div class="bg-white/10 rounded-2xl rounded-bl-md px-4 py-3">
              <div class="flex items-center gap-1.5">
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Message Input Area -->
        <div class="p-4 border-t border-white/10 bg-slate-900/90 backdrop-blur-sm">
          <!-- File Preview -->
          <div v-if="selectedFile" class="mb-3 p-3 bg-white/10 rounded-xl flex items-center justify-between">
            <div class="flex items-center gap-3 min-w-0">
              <div class="w-10 h-10 rounded-lg bg-orange-500/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
              </div>
              <div class="min-w-0">
                <p class="text-white text-sm font-medium truncate">{{ selectedFile.name }}</p>
                <p class="text-gray-500 text-xs">{{ formatFileSize(selectedFile.size) }}</p>
              </div>
            </div>
            <button @click="selectedFile = null" class="p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition-all">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Input Row -->
          <div class="flex items-end gap-2">
            <input type="file" ref="fileInput" @change="handleFileSelect" class="hidden" accept="image/*,.pdf,.doc,.docx,.txt">
            <button @click="$refs.fileInput.click()"
                    class="flex-shrink-0 w-12 h-12 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/10 rounded-xl transition-all">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
              </svg>
            </button>

            <div class="flex-1 relative">
              <textarea v-model="messageText"
                        @input="handleTyping"
                        @keydown.enter.exact.prevent="sendMessage"
                        rows="1"
                        placeholder="Type your message..."
                        class="w-full px-4 py-3.5 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-gray-500 text-sm focus:outline-none focus:border-orange-500/50 focus:ring-2 focus:ring-orange-500/20 transition-all resize-none max-h-32 pr-12"></textarea>
            </div>

            <button @click="sendMessage"
                    :disabled="(!messageText.trim() && !selectedFile) || isSending"
                    class="flex-shrink-0 w-12 h-12 flex items-center justify-center bg-gradient-to-r from-orange-500 to-purple-600 text-white rounded-xl hover:shadow-lg hover:shadow-orange-500/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
              <svg v-if="isSending" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-else class="flex-1 flex items-center justify-center p-8 bg-gradient-to-b from-slate-900 to-slate-800">
        <div class="text-center">
          <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-orange-500/20 to-purple-600/20 flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-orange-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
          </div>
          <p class="text-gray-400 text-sm">Loading conversations...</p>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();

// State
const isOpen = ref(false);
const view = ref('loading');
const tickets = ref([]);
const messages = ref([]);
const currentTicket = ref(null);
const guestSessionId = ref(null);

// Form state
const guestName = ref('');
const guestEmail = ref('');
const category = ref('general');
const newMessage = ref('');
const messageText = ref('');
const selectedFile = ref(null);

// Loading states
const isSubmitting = ref(false);
const isSending = ref(false);
const isAdminTyping = ref(false);

// Greeting state
const showGreeting = ref(false);
const greetingDismissed = ref(false);
const greetingMessages = [
  "Hi there! Need any help? We're online and ready to assist you.",
  "Welcome! Have questions? Our team is here to help you.",
  "Hey! Looking for support? Chat with us now - we respond fast!",
  "Hello! Need assistance? We're just a click away.",
  "Hi! Got questions about our services? Let's chat!"
];
const greetingMessage = ref(greetingMessages[Math.floor(Math.random() * greetingMessages.length)]);

// Categories
const categories = [
  { value: 'general', label: 'General', icon: '💬' },
  { value: 'payment', label: 'Payment', icon: '💳' },
  { value: 'withdrawal', label: 'Withdrawal', icon: '💰' },
  { value: 'task', label: 'Task Issue', icon: '📋' },
  { value: 'account', label: 'Account', icon: '👤' },
  { value: 'technical', label: 'Technical', icon: '🔧' },
];

// Refs
const messagesContainer = ref(null);
const fileInput = ref(null);
const menuRef = ref(null);
const userInfoRef = ref(null);
const chatWindowRef = ref(null);

// Mobile detection
const isMobileView = ref(typeof window !== 'undefined' ? window.innerWidth < 640 : false);

// Menu states
const showMenu = ref(false);
const showUserInfo = ref(false);

// Typing timeout
let typingTimeout = null;
let pollingInterval = null;

// Pusher channel
let channel = null;

// Computed
const isAuthenticated = computed(() => !!page.props.auth?.user);
const userName = computed(() => page.props.auth?.user?.full_name || 'Guest');
const userEmail = computed(() => page.props.auth?.user?.email || page.props.auth?.user?.phone_number || '');

const canSubmit = computed(() => {
  if (!newMessage.value.trim()) return false;
  if (!isAuthenticated.value) {
    return guestEmail.value.trim() !== '';
  }
  return true;
});

const unreadCount = computed(() => {
  return tickets.value.filter(t => t.has_new_admin_reply).length;
});

// Handle window resize for mobile detection
const handleResize = () => {
  isMobileView.value = window.innerWidth < 640;
};

// Methods
function toggleWidget() {
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    greetingDismissed.value = true;
    loadTickets();
  }
}

function dismissGreeting() {
  greetingDismissed.value = true;
  sessionStorage.setItem('support_greeting_dismissed', 'true');
}

async function loadTickets() {
  view.value = 'loading';
  try {
    const response = await axios.get('/api/support/tickets', {
      params: { guest_session_id: guestSessionId.value }
    });
    tickets.value = response.data.tickets || [];

    const openTicket = tickets.value.find(t => ['open', 'in_progress', 'awaiting_reply'].includes(t.status));
    if (openTicket) {
      await openTicketView(openTicket);
    } else if (tickets.value.length > 0) {
      view.value = 'tickets';
    } else {
      view.value = 'new';
    }
  } catch (error) {
    console.error('Failed to load tickets:', error);
    view.value = 'new';
  }
}

function startNewTicket() {
  view.value = 'new';
  newMessage.value = '';
  category.value = 'general';
}

async function submitNewTicket() {
  if (!canSubmit.value || isSubmitting.value) return;

  isSubmitting.value = true;
  try {
    const response = await axios.post('/api/support/ticket', {
      message: newMessage.value,
      subject: `${category.value.charAt(0).toUpperCase() + category.value.slice(1)} Support Request`,
      category: category.value,
      guest_name: guestName.value || undefined,
      guest_email: guestEmail.value || undefined,
    });

    if (response.data.guest_session_id) {
      guestSessionId.value = response.data.guest_session_id;
      localStorage.setItem('support_guest_id', response.data.guest_session_id);
    }

    currentTicket.value = {
      id: response.data.ticket_id,
      ticket_number: response.data.ticket_number,
      status: 'open'
    };

    await loadMessages();
    subscribeToTicket();
    view.value = 'chat';
    newMessage.value = '';
  } catch (error) {
    console.error('Failed to create ticket:', error);
    alert(error.response?.data?.message || 'Failed to send message. Please try again.');
  } finally {
    isSubmitting.value = false;
  }
}

async function openTicketView(ticket) {
  currentTicket.value = ticket;
  view.value = 'loading';
  await loadMessages();
  subscribeToTicket();
  view.value = 'chat';
}

function openTicket(ticket) {
  openTicketView(ticket);
}

async function loadMessages() {
  if (!currentTicket.value) return;

  try {
    const response = await axios.get(`/api/support/ticket/${currentTicket.value.id}/messages`, {
      params: { guest_session_id: guestSessionId.value }
    });
    messages.value = response.data.messages || [];
    currentTicket.value.status = response.data.ticket_status;
    scrollToBottom();
  } catch (error) {
    console.error('Failed to load messages:', error);
  }
}

async function sendMessage() {
  if ((!messageText.value.trim() && !selectedFile.value) || isSending.value) return;
  if (!currentTicket.value) return;

  const tempId = 'temp-' + Date.now();
  const messageContent = messageText.value.trim();
  const file = selectedFile.value;

  // Optimistic update - add message immediately
  const optimisticMessage = {
    id: tempId,
    message: messageContent,
    sender_type: 'user',
    created_at: new Date().toISOString(),
    is_sending: true,
    is_failed: false,
    file_path: file ? URL.createObjectURL(file) : null,
    file_name: file?.name,
    message_type: file && file.type.startsWith('image/') ? 'image' : 'text'
  };

  messages.value.push(optimisticMessage);
  messageText.value = '';
  selectedFile.value = null;
  scrollToBottom();

  isSending.value = true;

  try {
    const formData = new FormData();
    if (messageContent) {
      formData.append('message', messageContent);
    }
    if (file) {
      formData.append('file', file);
    }
    formData.append('guest_session_id', guestSessionId.value || '');

    const response = await axios.post(`/api/support/ticket/${currentTicket.value.id}/message`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    // Replace optimistic message with server response
    const index = messages.value.findIndex(m => m.id === tempId);
    if (index !== -1) {
      messages.value[index] = response.data.data;
    }
  } catch (error) {
    console.error('Failed to send message:', error);
    // Mark message as failed
    const index = messages.value.findIndex(m => m.id === tempId);
    if (index !== -1) {
      messages.value[index].is_sending = false;
      messages.value[index].is_failed = true;
    }
  } finally {
    isSending.value = false;
  }
}

async function retryMessage(failedMessage) {
  if (!currentTicket.value) return;

  // Mark as sending again
  failedMessage.is_sending = true;
  failedMessage.is_failed = false;

  try {
    const formData = new FormData();
    if (failedMessage.message) {
      formData.append('message', failedMessage.message);
    }
    formData.append('guest_session_id', guestSessionId.value || '');

    const response = await axios.post(`/api/support/ticket/${currentTicket.value.id}/message`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    // Replace failed message with server response
    const index = messages.value.findIndex(m => m.id === failedMessage.id);
    if (index !== -1) {
      messages.value[index] = response.data.data;
    }
  } catch (error) {
    console.error('Failed to retry message:', error);
    failedMessage.is_sending = false;
    failedMessage.is_failed = true;
  }
}

function handleFileSelect(e) {
  const file = e.target.files[0];
  if (file) {
    if (file.size > 50 * 1024 * 1024) {
      alert('File size must be less than 50MB');
      return;
    }
    selectedFile.value = file;
  }
}

function handleTyping() {
  if (!currentTicket.value) return;

  axios.post(`/api/support/ticket/${currentTicket.value.id}/typing`, {
    is_typing: true,
    guest_session_id: guestSessionId.value
  }).catch(() => {});

  if (typingTimeout) clearTimeout(typingTimeout);

  typingTimeout = setTimeout(() => {
    axios.post(`/api/support/ticket/${currentTicket.value.id}/typing`, {
      is_typing: false,
      guest_session_id: guestSessionId.value
    }).catch(() => {});
  }, 2000);
}

function subscribeToTicket() {
  if (!currentTicket.value || !window.Echo) return;

  if (channel) {
    window.Echo.leave(`support.ticket.${channel}`);
  }

  channel = currentTicket.value.id;

  window.Echo.channel(`support.ticket.${currentTicket.value.id}`)
    .listen('.new-message', (data) => {
      if (!messages.value.find(m => m.id === data.id)) {
        messages.value.push(data);
        scrollToBottom();
      }
    })
    .listen('.typing', (data) => {
      if (data.sender_type === 'admin') {
        isAdminTyping.value = data.is_typing;
        if (data.is_typing) {
          setTimeout(() => {
            isAdminTyping.value = false;
          }, 3000);
        }
      }
    });
}

function backToTickets() {
  if (channel) {
    window.Echo.leave(`support.ticket.${channel}`);
    channel = null;
  }
  currentTicket.value = null;
  messages.value = [];
  loadTickets();
}

function scrollToBottom() {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
  });
}

function formatTime(dateString) {
  const date = new Date(dateString);
  const now = new Date();
  const diff = now - date;

  if (diff < 60000) return 'Just now';
  if (diff < 3600000) return `${Math.floor(diff / 60000)}m ago`;
  if (diff < 86400000) return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
}

function formatFileSize(bytes) {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function getStatusClass(status) {
  const classes = {
    'open': 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
    'in_progress': 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30',
    'awaiting_reply': 'bg-purple-500/20 text-purple-400 border border-purple-500/30',
    'resolved': 'bg-green-500/20 text-green-400 border border-green-500/30',
    'closed': 'bg-gray-500/20 text-gray-400 border border-gray-500/30'
  };
  return classes[status] || 'bg-gray-500/20 text-gray-400 border border-gray-500/30';
}

function openImage(url) {
  window.open(url, '_blank');
}

// Click outside handlers
function handleClickOutside(e) {
  if (menuRef.value && !menuRef.value.contains(e.target)) {
    showMenu.value = false;
  }
  if (userInfoRef.value && !userInfoRef.value.contains(e.target)) {
    showUserInfo.value = false;
  }
}

let greetingTimeout = null;

onMounted(() => {
  guestSessionId.value = localStorage.getItem('support_guest_id') || null;

  const wasDismissed = sessionStorage.getItem('support_greeting_dismissed');
  if (!wasDismissed) {
    greetingTimeout = setTimeout(() => {
      showGreeting.value = true;
    }, 3000);
  } else {
    greetingDismissed.value = true;
  }

  document.addEventListener('click', handleClickOutside);
  window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
  if (channel) {
    window.Echo.leave(`support.ticket.${channel}`);
  }
  if (typingTimeout) clearTimeout(typingTimeout);
  if (pollingInterval) clearInterval(pollingInterval);
  if (greetingTimeout) clearTimeout(greetingTimeout);
  document.removeEventListener('click', handleClickOutside);
  window.removeEventListener('resize', handleResize);
});

watch(messages, () => {
  scrollToBottom();
});
</script>

<style scoped>
/* Chat Window Positioning - Mobile first (full screen) */
.chat-window {
  position: fixed;
  top: 8px;
  bottom: 80px;
  left: 8px;
  right: 8px;
  width: auto;
  height: auto;
  max-height: none;
  border-radius: 24px;
}

/* Desktop positioning */
.chat-window.desktop-mode {
  top: auto;
  left: auto;
  bottom: 100px !important;
  right: 24px !important;
  width: 400px;
  height: 600px;
  max-height: calc(100vh - 130px);
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.3);
}

.widget-slide-enter-active, .widget-slide-leave-active {
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.widget-slide-enter-from {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}

.widget-slide-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.95);
}

.icon-fade-enter-active, .icon-fade-leave-active {
  transition: all 0.2s ease;
}

.icon-fade-enter-from, .icon-fade-leave-to {
  opacity: 0;
  transform: scale(0.8);
}

.dropdown-enter-active, .dropdown-leave-active {
  transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.dropdown-enter-from {
  opacity: 0;
  transform: translateY(-8px) scale(0.95);
}

.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px) scale(0.95);
}

.greeting-slide-enter-active {
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.greeting-slide-leave-active {
  transition: all 0.3s ease;
}

.greeting-slide-enter-from {
  opacity: 0;
  transform: translateX(30px) scale(0.9);
}

.greeting-slide-leave-to {
  opacity: 0;
  transform: translateX(30px) scale(0.9);
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-6px); }
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

@keyframes pulse-slow {
  0%, 100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.4); }
  50% { box-shadow: 0 0 0 12px rgba(249, 115, 22, 0); }
}

.animate-pulse-slow {
  animation: pulse-slow 2s ease-in-out infinite;
}
</style>
