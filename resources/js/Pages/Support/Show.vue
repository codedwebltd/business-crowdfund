<template>
  <component :is="layoutComponent" :title="'Support Ticket'" :breadcrumbs="breadcrumbs">
    <div class="max-w-4xl mx-auto">
      <!-- Professional Ticket Details Card -->
      <div class="bg-gradient-to-r from-orange-500/10 via-purple-500/10 to-pink-500/10 backdrop-blur-xl rounded-2xl sm:rounded-3xl border border-white/20 p-4 sm:p-6 mb-4 sm:mb-6 relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-orange-500/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-purple-500/20 rounded-full blur-3xl"></div>

        <div class="relative z-10">
          <!-- Top Row: Back Button & Actions -->
          <div class="flex items-center justify-between gap-3 mb-4">
            <Link href="/support"
                  class="flex items-center gap-2 px-3 py-2 text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-xl border border-white/10 transition-all text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              <span class="hidden sm:inline">Back</span>
            </Link>

            <!-- Close Ticket Button -->
            <button v-if="ticket.status !== 'closed' && ticket.status !== 'resolved'"
                    @click="showCloseModal = true"
                    class="flex items-center gap-2 px-3 sm:px-4 py-2 text-red-400 hover:text-white bg-red-500/10 hover:bg-red-500/20 border border-red-500/30 hover:border-red-500/50 rounded-xl transition-all text-sm font-medium">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
              <span>Close Ticket</span>
            </button>

            <!-- Resolved Badge (when already closed/resolved) -->
            <div v-else-if="ticket.resolver" class="flex items-center gap-2 px-3 py-2 bg-green-500/10 border border-green-500/30 rounded-xl">
              <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span class="text-green-400 text-xs font-medium hidden sm:inline">Resolved by {{ ticket.resolver.full_name }}</span>
              <span class="text-green-400 text-xs font-medium sm:hidden">Resolved</span>
            </div>
          </div>

          <!-- Ticket Title -->
          <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-white mb-3 line-clamp-2">{{ ticket.subject }}</h1>

          <!-- Scrollable Info Bar -->
          <div class="relative -mx-4 sm:-mx-6 px-4 sm:px-6">
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
              <!-- Ticket Number Badge -->
              <span class="flex-shrink-0 px-3 py-1.5 bg-orange-500/20 text-orange-400 rounded-lg text-xs sm:text-sm font-mono font-semibold border border-orange-500/30">
                #{{ ticket.ticket_number }}
              </span>

              <!-- Status Badge -->
              <span :class="getStatusBadgeClass(ticket.status)" class="flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold uppercase flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotClass(ticket.status)"></span>
                {{ formatStatus(ticket.status) }}
              </span>

              <!-- Category Badge -->
              <span class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 bg-purple-500/20 text-purple-400 border border-purple-500/30 rounded-lg text-xs font-medium">
                <component :is="getCategoryIcon(ticket.category)" class="w-3.5 h-3.5" />
                {{ formatCategory(ticket.category) }}
              </span>

              <!-- Date Badge -->
              <span class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 bg-white/5 text-gray-400 border border-white/10 rounded-lg text-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ formatDate(ticket.created_at) }}
              </span>

              <!-- Messages Count Badge -->
              <span class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 bg-white/5 text-gray-400 border border-white/10 rounded-lg text-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                {{ localMessages.length }} messages
              </span>
            </div>
            <!-- Scroll Fade Indicators -->
            <div class="absolute right-0 top-0 bottom-2 w-8 bg-gradient-to-l from-orange-500/10 to-transparent pointer-events-none sm:hidden"></div>
          </div>
        </div>
      </div>

      <!-- Chat Container -->
      <div class="bg-white/5 backdrop-blur-xl rounded-2xl sm:rounded-3xl border border-white/20 overflow-hidden shadow-xl">
        <!-- Chat Header -->
        <div class="px-4 sm:px-5 py-3 sm:py-4 bg-gradient-to-r from-purple-500/10 to-orange-500/10 border-b border-white/10">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-orange-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-white font-semibold text-sm sm:text-base">Conversation</h3>
                <p class="text-gray-400 text-[10px] sm:text-xs">{{ localMessages.length }} message{{ localMessages.length !== 1 ? 's' : '' }}</p>
              </div>
            </div>
            <div v-if="isAdminTyping" class="flex items-center gap-2 px-2.5 sm:px-3 py-1.5 bg-white/10 rounded-full">
              <span class="text-orange-400 text-[10px] sm:text-xs font-medium">{{ typingAdminName }} typing</span>
              <div class="flex items-center gap-0.5">
                <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-orange-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-orange-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                <div class="w-1 h-1 sm:w-1.5 sm:h-1.5 bg-orange-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Messages -->
        <div ref="messagesContainer" class="h-[400px] sm:h-[500px] overflow-y-auto custom-scrollbar p-3 sm:p-5 space-y-3 sm:space-y-4 bg-gradient-to-b from-transparent to-black/10">
          <div v-for="message in localMessages" :key="message.id"
               :class="['flex', getMessageAlignment(message)]">

            <!-- System Message -->
            <div v-if="message.sender_type === 'system'" class="w-full flex items-center gap-2 sm:gap-3 py-2">
              <div class="h-px flex-1 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
              <span class="text-gray-400 text-[10px] sm:text-xs px-2 sm:px-3 py-1 bg-white/5 rounded-full whitespace-nowrap">{{ message.message }}</span>
              <div class="h-px flex-1 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
            </div>

            <!-- Regular Message -->
            <div v-else class="flex items-end gap-2 max-w-[85%] sm:max-w-[80%]">
              <!-- Avatar for admin messages (left side) -->
              <div v-if="message.sender_type === 'admin'" class="flex-shrink-0">
                <div class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-orange-500 to-purple-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg">
                  <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                  </svg>
                </div>
              </div>

              <!-- Message Bubble -->
              <div :class="getMessageStyle(message)">
                <!-- Admin name -->
                <p v-if="message.sender_type === 'admin'" class="text-orange-400 text-[10px] sm:text-xs font-semibold mb-1 sm:mb-1.5 flex items-center gap-1.5">
                  <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
                  {{ message.sender?.full_name || 'Support Agent' }}
                </p>

                <!-- File Attachment -->
                <div v-if="message.file_path" class="mb-2">
                  <img v-if="message.message_type === 'image'"
                       :src="message.file_path"
                       class="max-w-full rounded-lg cursor-pointer hover:opacity-90 transition-opacity shadow-lg"
                       @click="openImage(message.file_path)">
                  <a v-else :href="message.file_path" target="_blank"
                     class="flex items-center gap-2 px-2.5 sm:px-3 py-1.5 sm:py-2 bg-black/20 rounded-lg hover:bg-black/30 transition-colors group">
                    <div class="w-7 h-7 sm:w-8 sm:h-8 bg-white/10 rounded-lg flex items-center justify-center group-hover:bg-white/20 transition-colors">
                      <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                      </svg>
                    </div>
                    <span class="text-xs sm:text-sm truncate text-white">{{ message.file_name }}</span>
                  </a>
                </div>

                <p class="text-white text-xs sm:text-sm whitespace-pre-wrap leading-relaxed">{{ message.message }}</p>

                <div class="flex items-center justify-end gap-2 mt-1 sm:mt-1.5">
                  <!-- Sending indicator -->
                  <span v-if="message.is_sending" class="text-white/50 text-[9px] sm:text-[10px] flex items-center gap-1">
                    <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Sending...
                  </span>
                  <!-- Failed indicator -->
                  <button v-else-if="message.is_failed"
                          @click="retryMessage(message)"
                          class="text-red-400 text-[9px] sm:text-[10px] flex items-center gap-1 hover:text-red-300 transition-colors">
                    <svg class="w-2.5 h-2.5 sm:w-3 sm:h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Tap to retry
                  </button>
                  <!-- Time and read status -->
                  <template v-else>
                    <p class="text-white/50 text-[9px] sm:text-[10px]">{{ formatTime(message.created_at) }}</p>
                    <svg v-if="message.is_read && message.sender_type !== 'admin'" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </template>
                </div>
              </div>

              <!-- Avatar for user messages (right side) -->
              <div v-if="message.sender_type !== 'admin' && message.sender_type !== 'system'" class="flex-shrink-0">
                <div class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg">
                  <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Typing Indicator -->
          <div v-if="isAdminTyping" class="flex justify-start">
            <div class="flex items-end gap-2">
              <div class="w-7 h-7 sm:w-8 sm:h-8 bg-gradient-to-br from-orange-500 to-purple-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
              </div>
              <div class="bg-white/10 rounded-2xl rounded-bl-sm px-3 sm:px-4 py-2 sm:py-3">
                <p class="text-orange-400 text-[10px] sm:text-xs font-semibold mb-1 sm:mb-1.5">{{ typingAdminName }}</p>
                <div class="flex items-center gap-1 sm:gap-1.5">
                  <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                  <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                  <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Input Area (if ticket not closed) -->
        <div v-if="ticket.status !== 'closed' && ticket.status !== 'resolved'" class="p-3 sm:p-4 border-t border-white/10 bg-gradient-to-r from-purple-500/5 to-orange-500/5">
          <!-- File Preview -->
          <div v-if="selectedFile" class="mb-2 sm:mb-3 p-2 sm:p-3 bg-white/10 rounded-xl flex items-center justify-between border border-white/10">
            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
              <div class="w-8 h-8 sm:w-10 sm:h-10 bg-orange-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
              </div>
              <div class="min-w-0">
                <p class="text-white text-xs sm:text-sm font-medium truncate">{{ selectedFile.name }}</p>
                <p class="text-gray-400 text-[10px] sm:text-xs">{{ formatFileSize(selectedFile.size) }}</p>
              </div>
            </div>
            <button @click="selectedFile = null" class="p-1.5 sm:p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
              <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <div class="flex items-end gap-2 sm:gap-3">
            <!-- File Upload -->
            <input type="file" ref="fileInput" @change="handleFileSelect" class="hidden" accept="image/*,.pdf,.doc,.docx,.txt">
            <button @click="$refs.fileInput.click()"
                    class="flex-shrink-0 p-2.5 sm:p-3 text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-xl border border-white/10 transition-all">
              <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
              </svg>
            </button>

            <!-- Message Input -->
            <div class="flex-1 relative">
              <textarea v-model="messageText"
                        @input="handleTyping"
                        @keydown.enter.exact.prevent="sendMessage"
                        rows="1"
                        placeholder="Type your message..."
                        class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 text-xs sm:text-sm focus:outline-none focus:border-orange-500/50 focus:ring-2 focus:ring-orange-500/20 transition-all resize-none max-h-32"></textarea>
            </div>

            <!-- Send Button -->
            <button @click="sendMessage"
                    :disabled="(!messageText.trim() && !selectedFile) || isSending"
                    class="flex-shrink-0 p-2.5 sm:p-3 bg-gradient-to-r from-orange-500 to-purple-600 text-white rounded-xl hover:shadow-lg hover:shadow-orange-500/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed group">
              <svg v-if="isSending" class="w-4 h-4 sm:w-5 sm:h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              <svg v-else class="w-4 h-4 sm:w-5 sm:h-5 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Closed Ticket Actions -->
        <div v-else class="p-4 sm:p-6 border-t border-white/10 bg-gradient-to-r from-gray-500/5 to-purple-500/5">
          <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 bg-gray-500/20 rounded-2xl mb-3 sm:mb-4">
              <svg class="w-7 h-7 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <p class="text-gray-300 text-sm mb-3 sm:mb-4">This ticket has been closed.</p>

            <!-- Rating Section -->
            <div v-if="!ticket.rating" class="bg-white/5 rounded-xl p-4 sm:p-5 max-w-sm mx-auto">
              <p class="text-white text-sm font-semibold mb-2 sm:mb-3">How was your experience?</p>
              <div class="flex items-center justify-center gap-0.5 sm:gap-1">
                <button v-for="star in 5" :key="star"
                        @click="rateTicket(star)"
                        class="p-0.5 sm:p-1 text-gray-500 hover:text-yellow-400 transition-colors transform hover:scale-110">
                  <svg class="w-8 h-8 sm:w-10 sm:h-10" :class="{ 'text-yellow-400': star <= hoverRating || star <= selectedRating }"
                       @mouseenter="hoverRating = star"
                       @mouseleave="hoverRating = 0"
                       fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                  </svg>
                </button>
              </div>
              <p class="text-gray-400 text-[10px] sm:text-xs mt-2">Click to rate your support experience</p>
            </div>

            <!-- Already Rated -->
            <div v-else class="bg-white/5 rounded-xl p-4 sm:p-5 max-w-sm mx-auto">
              <p class="text-gray-300 text-sm mb-2">You rated this support</p>
              <div class="flex items-center justify-center gap-0.5 sm:gap-1">
                <svg v-for="star in 5" :key="star" class="w-6 h-6 sm:w-8 sm:h-8"
                     :class="star <= ticket.rating ? 'text-yellow-400' : 'text-gray-600'"
                     fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
              </div>
              <p class="text-green-400 text-[10px] sm:text-xs mt-2">Thank you for your feedback!</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Close Ticket Confirmation Modal -->
      <Transition name="modal">
        <div v-if="showCloseModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showCloseModal = false"></div>
          <div class="relative w-full max-w-md bg-slate-900 rounded-2xl sm:rounded-3xl border border-white/10 shadow-2xl overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-red-500/20 to-orange-500/20 px-5 sm:px-6 py-4 sm:py-5 border-b border-white/10">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl sm:rounded-2xl bg-red-500/20 flex items-center justify-center">
                  <svg class="w-5 h-5 sm:w-6 sm:h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-white font-bold text-base sm:text-lg">Close Ticket?</h3>
                  <p class="text-gray-400 text-[10px] sm:text-xs">This action cannot be undone</p>
                </div>
              </div>
            </div>

            <!-- Modal Content -->
            <div class="p-5 sm:p-6">
              <p class="text-gray-300 text-sm mb-4 sm:mb-6">
                Are you sure you want to close ticket <span class="text-orange-400 font-mono font-semibold">#{{ ticket.ticket_number }}</span>?
                You won't be able to send more messages after closing.
              </p>

              <!-- Optional Reason -->
              <div class="mb-4 sm:mb-6">
                <label class="block text-gray-400 text-xs font-semibold mb-2">REASON (OPTIONAL)</label>
                <textarea v-model="closeReason" rows="2" placeholder="Let us know why you're closing this ticket..."
                          class="w-full px-3 sm:px-4 py-2.5 sm:py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 text-xs sm:text-sm focus:outline-none focus:border-orange-500/50 transition-all resize-none"></textarea>
              </div>

              <!-- Action Buttons -->
              <div class="flex items-center gap-3">
                <button @click="showCloseModal = false"
                        class="flex-1 py-2.5 sm:py-3 px-4 bg-white/10 hover:bg-white/20 text-white rounded-xl font-medium transition-all text-sm">
                  Cancel
                </button>
                <button @click="closeTicket"
                        :disabled="isClosing"
                        class="flex-1 py-2.5 sm:py-3 px-4 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 text-sm disabled:opacity-50">
                  <svg v-if="isClosing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                  </svg>
                  <span>{{ isClosing ? 'Closing...' : 'Yes, Close Ticket' }}</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </component>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch, computed, h } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import axios from 'axios';

const props = defineProps({
  ticket: Object,
  messages: Array
});

const page = usePage();

// Check if user is authenticated
const isAuthenticated = computed(() => !!page.props.auth?.user);

// Conditionally choose layout
const layoutComponent = computed(() => isAuthenticated.value ? UserLayout : GuestLayout);

// Breadcrumbs for GuestLayout
const breadcrumbs = computed(() => [
  { label: 'Support', url: '/support' },
  { label: `Ticket #${props.ticket?.ticket_number || ''}`, url: null }
]);

// State
const localMessages = ref([...props.messages]);
const messageText = ref('');
const selectedFile = ref(null);
const isSending = ref(false);
const isClosing = ref(false);
const isAdminTyping = ref(false);
const typingAdminName = ref('Support Agent');
const hoverRating = ref(0);
const selectedRating = ref(0);
const showCloseModal = ref(false);
const closeReason = ref('');

// Refs
const messagesContainer = ref(null);
const fileInput = ref(null);

// Typing timeout
let typingTimeout = null;

// Pusher channel
let channel = null;

// Category icons
const categoryIcons = {
  general: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'w-3.5 h-3.5' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
  ]),
  technical: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'w-3.5 h-3.5' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' }),
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 12a3 3 0 11-6 0 3 3 0 016 0z' })
  ]),
  billing: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'w-3.5 h-3.5' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' })
  ]),
  payment: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'w-3.5 h-3.5' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' })
  ]),
  account: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'w-3.5 h-3.5' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' })
  ]),
  withdrawal: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'w-3.5 h-3.5' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z' })
  ]),
  task: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'w-3.5 h-3.5' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' })
  ]),
  other: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24', class: 'w-3.5 h-3.5' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z' })
  ])
};

function getCategoryIcon(category) {
  return categoryIcons[category] || categoryIcons.general;
}

// Methods
async function sendMessage() {
  if ((!messageText.value.trim() && !selectedFile.value) || isSending.value) return;

  const tempId = 'temp-' + Date.now();
  const messageContent = messageText.value.trim();
  const file = selectedFile.value;

  // Optimistic message
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

  localMessages.value.push(optimisticMessage);
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

    const response = await axios.post(`/api/support/ticket/${props.ticket.id}/message`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    // Replace optimistic message with real one
    const index = localMessages.value.findIndex(m => m.id === tempId);
    if (index !== -1) {
      localMessages.value.splice(index, 1, response.data.data);
    }
  } catch (error) {
    console.error('Failed to send message:', error);
    // Mark message as failed
    const index = localMessages.value.findIndex(m => m.id === tempId);
    if (index !== -1) {
      localMessages.value[index].is_sending = false;
      localMessages.value[index].is_failed = true;
      localMessages.value[index]._originalContent = messageContent;
      localMessages.value[index]._originalFile = file;
    }
  } finally {
    isSending.value = false;
  }
}

async function retryMessage(failedMessage) {
  if (!failedMessage._originalContent && !failedMessage._originalFile) return;

  failedMessage.is_sending = true;
  failedMessage.is_failed = false;

  try {
    const formData = new FormData();
    if (failedMessage._originalContent) {
      formData.append('message', failedMessage._originalContent);
    }
    if (failedMessage._originalFile) {
      formData.append('file', failedMessage._originalFile);
    }

    const response = await axios.post(`/api/support/ticket/${props.ticket.id}/message`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    // Replace failed message with real one
    const index = localMessages.value.findIndex(m => m.id === failedMessage.id);
    if (index !== -1) {
      localMessages.value.splice(index, 1, response.data.data);
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
    if (file.size > 50 * 1024 * 1024) { // 50MB
      alert('File size must be less than 50MB');
      return;
    }
    selectedFile.value = file;
  }
}

function formatFileSize(bytes) {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function handleTyping() {
  // Send typing indicator
  axios.post(`/api/support/ticket/${props.ticket.id}/typing`, {
    is_typing: true
  }).catch(() => {});

  // Clear previous timeout
  if (typingTimeout) clearTimeout(typingTimeout);

  // Stop typing after 2 seconds of inactivity
  typingTimeout = setTimeout(() => {
    axios.post(`/api/support/ticket/${props.ticket.id}/typing`, {
      is_typing: false
    }).catch(() => {});
  }, 2000);
}

async function closeTicket() {
  isClosing.value = true;
  try {
    await axios.post(`/api/support/ticket/${props.ticket.id}/close`, {
      reason: closeReason.value || undefined
    });
    showCloseModal.value = false;
    router.reload();
  } catch (error) {
    console.error('Failed to close ticket:', error);
    alert(error.response?.data?.message || 'Failed to close ticket. Please try again.');
  } finally {
    isClosing.value = false;
  }
}

async function rateTicket(rating) {
  selectedRating.value = rating;
  try {
    await axios.post(`/api/support/ticket/${props.ticket.id}/rate`, { rating });
    router.reload();
  } catch (error) {
    console.error('Failed to rate ticket:', error);
    selectedRating.value = 0;
  }
}

function scrollToBottom() {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
  });
}

function subscribeToTicket() {
  if (!window.Echo) return;

  channel = window.Echo.channel(`support.ticket.${props.ticket.id}`);

  channel.listen('.new-message', (data) => {
    // Only add if not already in messages
    if (!localMessages.value.find(m => m.id === data.id)) {
      localMessages.value.push(data);
      scrollToBottom();
    }
  });

  channel.listen('.typing', (data) => {
    if (data.sender_type === 'admin') {
      isAdminTyping.value = data.is_typing;
      typingAdminName.value = data.sender_name || 'Support Agent';
      if (data.is_typing) {
        // Auto-hide after 3 seconds
        setTimeout(() => {
          isAdminTyping.value = false;
        }, 3000);
      }
    }
  });
}

function getMessageAlignment(message) {
  if (message.sender_type === 'system') return 'justify-center';
  if (message.sender_type === 'admin') return 'justify-start';
  return 'justify-end';
}

function getMessageStyle(message) {
  if (message.sender_type === 'system') {
    return 'bg-transparent w-full max-w-full';
  }
  if (message.sender_type === 'admin') {
    return 'bg-white/10 backdrop-blur-sm rounded-2xl rounded-bl-sm px-3 sm:px-4 py-2.5 sm:py-3 border border-white/5';
  }
  return 'bg-gradient-to-r from-orange-500 to-purple-600 rounded-2xl rounded-br-sm px-3 sm:px-4 py-2.5 sm:py-3 shadow-lg';
}

function formatTime(dateString) {
  const date = new Date(dateString);
  const now = new Date();
  const diff = now - date;

  if (diff < 60000) return 'Just now';
  if (diff < 3600000) return `${Math.floor(diff / 60000)}m ago`;
  if (diff < 86400000) return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  return date.toLocaleDateString([], { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString([], {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

function getStatusBadgeClass(status) {
  const classes = {
    'open': 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
    'in_progress': 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30',
    'awaiting_reply': 'bg-purple-500/20 text-purple-400 border border-purple-500/30',
    'resolved': 'bg-green-500/20 text-green-400 border border-green-500/30',
    'closed': 'bg-gray-500/20 text-gray-400 border border-gray-500/30'
  };
  return classes[status] || 'bg-gray-500/20 text-gray-400 border border-gray-500/30';
}

function getStatusDotClass(status) {
  const classes = {
    'open': 'bg-blue-400',
    'in_progress': 'bg-yellow-400',
    'awaiting_reply': 'bg-purple-400',
    'resolved': 'bg-green-400',
    'closed': 'bg-gray-400'
  };
  return classes[status] || 'bg-gray-400';
}

function formatStatus(status) {
  return status.replace(/_/g, ' ');
}

function formatCategory(category) {
  return category.charAt(0).toUpperCase() + category.slice(1).replace(/_/g, ' ');
}

function openImage(url) {
  window.open(url, '_blank');
}

// Lifecycle
onMounted(() => {
  scrollToBottom();
  subscribeToTicket();
});

onUnmounted(() => {
  if (channel) {
    window.Echo.leave(`support.ticket.${props.ticket.id}`);
  }
  if (typingTimeout) clearTimeout(typingTimeout);
});

// Watch for new messages
watch(() => localMessages.value.length, () => {
  scrollToBottom();
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
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

.scrollbar-none::-webkit-scrollbar {
  display: none;
}

.scrollbar-none {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.modal-enter-active, .modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

.modal-enter-from > div:last-child, .modal-leave-to > div:last-child {
  transform: scale(0.95) translateY(20px);
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
