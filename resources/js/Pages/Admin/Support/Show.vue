<template>
  <AdminLayout :title="`Ticket #${ticket.ticket_number}`" :settings="settings">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <div class="flex items-center gap-3 mb-2">
          <Link href="/admin/support" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </Link>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Ticket #{{ ticket.ticket_number }}</h1>
        </div>
        <p class="text-gray-600">{{ ticket.subject || 'Support Conversation' }}</p>
      </div>
      <div class="flex items-center gap-2">
        <select
          v-model="selectedStatus"
          @change="updateStatus"
          class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
        >
          <option value="open">Open</option>
          <option value="in_progress">In Progress</option>
          <option value="awaiting_reply">Awaiting Reply</option>
          <option value="resolved">Resolved</option>
          <option value="closed">Closed</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Chat Section -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden h-[calc(100vh-220px)] flex flex-col">
          <!-- Chat Header -->
          <div class="p-4 sm:p-6 border-b bg-gradient-to-r from-purple-500 to-pink-600">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                  <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                  </svg>
                </div>
                <div class="text-white">
                  <h3 class="font-bold">{{ ticket.user?.full_name || ticket.guest_name || 'Guest' }}</h3>
                  <p class="text-purple-100 text-sm">{{ ticket.user?.email || ticket.guest_email || 'No email' }}</p>
                </div>
              </div>
              <div v-if="isUserTyping" class="flex items-center gap-2 px-3 py-1.5 bg-white/20 rounded-full">
                <span class="text-white text-xs font-medium">Typing</span>
                <div class="flex items-center gap-0.5">
                  <div class="w-1.5 h-1.5 bg-white rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                  <div class="w-1.5 h-1.5 bg-white rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                  <div class="w-1.5 h-1.5 bg-white rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Messages Area -->
          <div ref="messagesContainer" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 bg-gray-50">
            <div v-for="message in localMessages" :key="message.id">
              <!-- System Message -->
              <div v-if="message.sender_type === 'system'" class="flex justify-center my-3">
                <div class="px-4 py-2 bg-yellow-100 border border-yellow-200 rounded-full">
                  <p class="text-yellow-700 text-xs">{{ message.message }}</p>
                </div>
              </div>

              <!-- User/Guest Message -->
              <div v-else-if="message.sender_type === 'user' || message.sender_type === 'guest'" class="flex items-end gap-2 max-w-[80%]">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center flex-shrink-0">
                  <span class="text-white text-xs font-bold">{{ getInitials(message.sender?.full_name || ticket.guest_name) }}</span>
                </div>
                <div>
                  <p class="text-xs text-gray-500 mb-1 ml-1">{{ message.sender?.full_name || ticket.guest_name || 'User' }}</p>
                  <div class="bg-white rounded-2xl rounded-bl-md px-4 py-3 shadow-sm border border-gray-200">
                    <div v-if="message.file_path" class="mb-2">
                      <img v-if="message.message_type === 'image'" :src="message.file_path" class="max-w-full rounded-lg cursor-pointer hover:opacity-90" @click="openImage(message.file_path)">
                      <a v-else :href="message.file_path" target="_blank" class="flex items-center gap-2 px-3 py-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        <span class="text-sm text-gray-700 truncate">{{ message.file_name }}</span>
                      </a>
                    </div>
                    <p class="text-gray-900 text-sm whitespace-pre-wrap">{{ message.message }}</p>
                  </div>
                  <p class="text-gray-400 text-[10px] mt-1 ml-1">{{ formatTime(message.created_at) }}</p>
                </div>
              </div>

              <!-- Admin Message -->
              <div v-else class="flex items-end justify-end gap-2 max-w-[80%] ml-auto">
                <div>
                  <p class="text-xs text-gray-500 mb-1 mr-1 text-right">{{ message.sender?.full_name || 'Admin' }}</p>
                  <div :class="['rounded-2xl rounded-br-md px-4 py-3 shadow-sm', message.is_sending ? 'bg-gradient-to-r from-purple-400 to-pink-500' : 'bg-gradient-to-r from-purple-500 to-pink-600']">
                    <div v-if="message.file_path" class="mb-2">
                      <img v-if="message.message_type === 'image'" :src="message.file_path" class="max-w-full rounded-lg cursor-pointer hover:opacity-90" @click="openImage(message.file_path)">
                      <a v-else :href="message.file_path" target="_blank" class="flex items-center gap-2 px-3 py-2 bg-black/20 rounded-lg hover:bg-black/30 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        <span class="text-sm text-white truncate">{{ message.file_name }}</span>
                      </a>
                    </div>
                    <p class="text-white text-sm whitespace-pre-wrap">{{ message.message }}</p>
                  </div>
                  <p class="text-gray-400 text-[10px] mt-1 mr-1 text-right">{{ formatTime(message.created_at) }}</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center flex-shrink-0">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Typing Indicator -->
            <div v-if="isUserTyping" class="flex items-end gap-2">
              <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center flex-shrink-0">
                <span class="text-white text-xs font-bold">{{ getInitials(ticket.user?.full_name || ticket.guest_name) }}</span>
              </div>
              <div class="bg-white rounded-2xl rounded-bl-md px-4 py-3 shadow-sm border border-gray-200">
                <div class="flex items-center gap-1.5">
                  <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                  <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                  <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Input Area -->
          <div class="p-4 border-t border-gray-200 bg-white">
            <!-- File Preview -->
            <div v-if="selectedFile" class="mb-3 p-3 bg-gray-100 rounded-xl flex items-center justify-between">
              <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                  </svg>
                </div>
                <div class="min-w-0">
                  <p class="text-gray-900 text-sm font-medium truncate">{{ selectedFile.name }}</p>
                  <p class="text-gray-500 text-xs">{{ formatFileSize(selectedFile.size) }}</p>
                </div>
              </div>
              <button @click="selectedFile = null" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>

            <!-- Input Row -->
            <div class="flex items-end gap-2">
              <input type="file" ref="fileInput" @change="handleFileSelect" class="hidden" accept="image/*,.pdf,.doc,.docx,.txt">
              <button @click="$refs.fileInput.click()" class="flex-shrink-0 w-10 h-10 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
              </button>

              <div class="flex-1">
                <textarea
                  v-model="messageText"
                  @input="handleTyping"
                  @keydown.enter.exact.prevent="sendMessage"
                  rows="1"
                  placeholder="Type your reply..."
                  class="w-full px-4 py-3 bg-gray-100 border-0 rounded-xl text-gray-900 placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 resize-none"
                ></textarea>
              </div>

              <button
                @click="sendMessage"
                :disabled="(!messageText.trim() && !selectedFile) || isSending"
                class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-xl hover:shadow-lg hover:shadow-purple-500/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
              >
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
      </div>

      <!-- Sidebar - Ticket Info -->
      <div class="lg:col-span-1 space-y-6">
        <!-- Customer Info Card -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
          <div class="p-4 border-b bg-gradient-to-r from-blue-500 to-blue-600">
            <h3 class="text-white font-bold">Customer Details</h3>
          </div>
          <div class="p-4">
            <div class="flex items-center gap-3 mb-4">
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold">
                {{ getInitials(ticket.user?.full_name || ticket.guest_name) }}
              </div>
              <div>
                <p class="font-semibold text-gray-900">{{ ticket.user?.full_name || ticket.guest_name || 'Guest' }}</p>
                <p class="text-sm text-gray-500">{{ ticket.user?.email || ticket.guest_email || 'No email' }}</p>
              </div>
            </div>
            <div v-if="ticket.user?.phone_number" class="text-sm text-gray-600">
              <span class="font-medium">Phone:</span> {{ ticket.user.phone_number }}
            </div>
          </div>
        </div>

        <!-- Ticket Details Card -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
          <div class="p-4 border-b bg-gradient-to-r from-purple-500 to-pink-600">
            <h3 class="text-white font-bold">Ticket Details</h3>
          </div>
          <div class="p-4 space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-500">Ticket Number</span>
              <span class="text-sm font-mono font-bold text-purple-600">#{{ ticket.ticket_number }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-500">Category</span>
              <span :class="getCategoryBadgeClass(ticket.category)" class="px-2 py-1 rounded-lg text-xs font-semibold capitalize">{{ ticket.category }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-500">Status</span>
              <span :class="getStatusBadgeClass(ticket.status)" class="px-2 py-1 rounded-lg text-xs font-bold uppercase">{{ ticket.status?.replace(/_/g, ' ') }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-500">Created</span>
              <span class="text-sm text-gray-900">{{ formatDate(ticket.created_at) }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-500">Messages</span>
              <span class="text-sm text-gray-900">{{ localMessages.length }}</span>
            </div>
          </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
          <div class="p-4 border-b bg-gradient-to-r from-green-500 to-green-600">
            <h3 class="text-white font-bold">Quick Actions</h3>
          </div>
          <div class="p-4 space-y-2">
            <button
              v-if="ticket.status !== 'resolved'"
              @click="updateStatusTo('resolved')"
              class="w-full px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg hover:shadow-green-500/50 transition-all flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Mark as Resolved
            </button>
            <button
              v-if="ticket.status !== 'closed'"
              @click="updateStatusTo('closed')"
              class="w-full px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition-all flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Close Ticket
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import axios from 'axios';

const props = defineProps({
  ticket: Object,
  messages: Array,
  settings: Object,
});

const localMessages = ref([...props.messages]);
const messageText = ref('');
const selectedFile = ref(null);
const isSending = ref(false);
const isUserTyping = ref(false);
const selectedStatus = ref(props.ticket.status);
const messagesContainer = ref(null);
const fileInput = ref(null);

let typingTimeout = null;
let channel = null;

const sendMessage = async () => {
  if ((!messageText.value.trim() && !selectedFile.value) || isSending.value) return;

  const tempId = 'temp-' + Date.now();
  const content = messageText.value.trim();
  const file = selectedFile.value;

  // Optimistic update
  const optimisticMessage = {
    id: tempId,
    message: content,
    sender_type: 'admin',
    created_at: new Date().toISOString(),
    is_sending: true,
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
    if (content) formData.append('message', content);
    if (file) formData.append('file', file);

    const response = await axios.post(`/admin/support/${props.ticket.id}/message`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    // Replace optimistic message
    const index = localMessages.value.findIndex(m => m.id === tempId);
    if (index !== -1 && response.data.data) {
      localMessages.value[index] = response.data.data;
    }
  } catch (error) {
    console.error('Failed to send message:', error);
    const index = localMessages.value.findIndex(m => m.id === tempId);
    if (index !== -1) {
      localMessages.value.splice(index, 1);
    }
    alert('Failed to send message. Please try again.');
  } finally {
    isSending.value = false;
  }
};

const handleTyping = () => {
  axios.post(`/admin/support/${props.ticket.id}/typing`, { is_typing: true }).catch(() => {});

  if (typingTimeout) clearTimeout(typingTimeout);

  typingTimeout = setTimeout(() => {
    axios.post(`/admin/support/${props.ticket.id}/typing`, { is_typing: false }).catch(() => {});
  }, 2000);
};

const handleFileSelect = (e) => {
  const file = e.target.files[0];
  if (file) {
    if (file.size > 50 * 1024 * 1024) {
      alert('File size must be less than 50MB');
      return;
    }
    selectedFile.value = file;
  }
};

const updateStatus = () => {
  updateStatusTo(selectedStatus.value);
};

const updateStatusTo = async (status) => {
  try {
    await axios.post(`/admin/support/${props.ticket.id}/status`, { status });
    selectedStatus.value = status;
    props.ticket.status = status;
  } catch (error) {
    console.error('Failed to update status:', error);
    alert('Failed to update status');
  }
};

const subscribeToTicket = () => {
  if (!window.Echo) return;

  channel = window.Echo.channel(`support.ticket.${props.ticket.id}`)
    .listen('.new-message', (data) => {
      if (!localMessages.value.find(m => m.id === data.id)) {
        localMessages.value.push(data);
        scrollToBottom();
      }
    })
    .listen('.typing', (data) => {
      if (data.sender_type === 'user' || data.sender_type === 'guest') {
        isUserTyping.value = data.is_typing;
        if (data.is_typing) {
          setTimeout(() => {
            isUserTyping.value = false;
          }, 3000);
        }
      }
    });
};

const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
  });
};

const getInitials = (name) => {
  if (!name) return 'G';
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
};

const formatTime = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const now = new Date();
  const diff = now - date;

  if (diff < 60000) return 'Just now';
  if (diff < 3600000) return `${Math.floor(diff / 60000)}m ago`;
  if (diff < 86400000) return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  return new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
};

const formatFileSize = (bytes) => {
  if (bytes < 1024) return bytes + ' B';
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
  return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
};

const getStatusBadgeClass = (status) => {
  const classes = {
    'open': 'bg-blue-100 text-blue-700',
    'in_progress': 'bg-yellow-100 text-yellow-700',
    'awaiting_reply': 'bg-purple-100 text-purple-700',
    'resolved': 'bg-green-100 text-green-700',
    'closed': 'bg-gray-100 text-gray-700',
  };
  return classes[status] || 'bg-gray-100 text-gray-700';
};

const getCategoryBadgeClass = (category) => {
  const classes = {
    'general': 'bg-gray-100 text-gray-700',
    'payment': 'bg-emerald-100 text-emerald-700',
    'withdrawal': 'bg-amber-100 text-amber-700',
    'task': 'bg-indigo-100 text-indigo-700',
    'account': 'bg-cyan-100 text-cyan-700',
    'technical': 'bg-rose-100 text-rose-700',
  };
  return classes[category] || 'bg-gray-100 text-gray-700';
};

const openImage = (url) => {
  window.open(url, '_blank');
};

onMounted(() => {
  subscribeToTicket();
  scrollToBottom();
});

onUnmounted(() => {
  if (channel) {
    window.Echo.leave(`support.ticket.${props.ticket.id}`);
  }
  if (typingTimeout) clearTimeout(typingTimeout);
});
</script>
