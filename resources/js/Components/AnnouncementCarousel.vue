<template>
  <div v-if="visibleAnnouncements.length > 0" class="mb-6">
    <div class="relative group bg-gradient-to-br from-orange-500/10 via-purple-600/10 to-transparent backdrop-blur-xl rounded-2xl border border-orange-500/30 p-5 overflow-hidden hover:border-orange-500/50 hover:shadow-2xl hover:shadow-orange-500/20 transition-all duration-300">
      <!-- Animated Background Blob -->
      <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-orange-500/20 to-purple-500/20 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>

      <div class="relative z-10">
        <!-- Current Announcement -->
        <div :key="currentAnnouncement.id" class="announcement-slide">
          <div class="flex items-start gap-4 mb-4">
            <!-- Icon based on type -->
            <div :class="[
              'p-3 rounded-xl shrink-0',
              currentAnnouncement.type === 'success' ? 'bg-green-500/20' :
              currentAnnouncement.type === 'warning' ? 'bg-yellow-500/20' :
              currentAnnouncement.type === 'danger' ? 'bg-red-500/20' :
              'bg-blue-500/20'
            ]">
              <svg v-if="currentAnnouncement.type === 'success'" class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <svg v-else-if="currentAnnouncement.type === 'warning'" class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
              <svg v-else-if="currentAnnouncement.type === 'danger'" class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <svg v-else class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>

            <div class="flex-1 min-w-0">
              <h3 class="text-white font-bold text-base md:text-lg mb-1">{{ currentAnnouncement.title }}</h3>
              <p class="text-gray-300 text-sm">{{ currentAnnouncement.message }}</p>
            </div>

            <!-- Close button (if dismissable) -->
            <button
              v-if="currentAnnouncement.is_dismissable"
              @click="dismissAnnouncement(currentAnnouncement)"
              class="shrink-0 p-2 hover:bg-white/10 rounded-lg transition-colors"
            >
              <svg class="w-5 h-5 text-gray-400 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>

            <!-- Non-dismissable badge -->
            <div v-else class="shrink-0 px-3 py-1 bg-orange-500/20 rounded-lg border border-orange-500/30">
              <span class="text-[10px] font-bold text-orange-400 uppercase tracking-wider">System</span>
            </div>
          </div>

          <!-- CTA Button -->
          <div v-if="currentAnnouncement.link_url" class="flex items-center justify-between mt-4 pt-4 border-t border-white/10">
            <Link
              :href="currentAnnouncement.link_url"
              class="px-4 py-2 bg-gradient-to-r from-orange-500 to-purple-600 hover:from-orange-600 hover:to-purple-700 text-white text-sm font-semibold rounded-lg transition-all shadow-lg shadow-orange-500/30"
            >
              {{ currentAnnouncement.link_text || 'Learn More' }}
            </Link>
          </div>
        </div>

        <!-- Pagination Dots & Controls -->
        <div v-if="visibleAnnouncements.length > 1" class="flex items-center justify-between mt-4 pt-4 border-t border-white/10">
          <div class="flex gap-1.5">
            <button
              v-for="(announcement, index) in visibleAnnouncements"
              :key="announcement.id"
              @click="goToSlide(index)"
              :class="[
                'h-1.5 rounded-full transition-all duration-300',
                currentIndex === index ? 'w-8 bg-orange-500' : 'w-1.5 bg-white/30 hover:bg-white/50'
              ]"
            ></button>
          </div>

          <div class="flex gap-2">
            <button @click="previousSlide" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
              <svg class="w-4 h-4 text-gray-400 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </button>
            <button @click="nextSlide" class="p-2 hover:bg-white/10 rounded-lg transition-colors">
              <svg class="w-4 h-4 text-gray-400 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal for full details -->
    <Teleport to="body">
      <div v-if="showModal" @click="showModal = false" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div @click.stop class="bg-gray-900 rounded-2xl max-w-lg w-full p-6 border border-white/20">
          <div class="flex items-start justify-between mb-4">
            <h3 class="text-2xl font-bold text-white">{{ currentAnnouncement.title }}</h3>
            <button @click="showModal = false" class="text-gray-400 hover:text-white">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
          <p class="text-gray-300 mb-6">{{ currentAnnouncement.message }}</p>
          <div v-if="currentAnnouncement.link_url" class="flex gap-3">
            <Link
              :href="currentAnnouncement.link_url"
              class="flex-1 px-4 py-3 bg-gradient-to-r from-orange-500 to-purple-600 hover:from-orange-600 hover:to-purple-700 text-white font-semibold rounded-lg transition-all text-center"
            >
              {{ currentAnnouncement.link_text || 'Learn More' }}
            </Link>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
  announcements: {
    type: Array,
    default: () => []
  }
});

const currentIndex = ref(0);
const showModal = ref(false);
const visibleAnnouncements = ref([...props.announcements]);
let interval = null;

const currentAnnouncement = computed(() => {
  return visibleAnnouncements.value[currentIndex.value] || {};
});

const nextSlide = () => {
  if (visibleAnnouncements.value.length > 1) {
    currentIndex.value = (currentIndex.value + 1) % visibleAnnouncements.value.length;
  }
};

const previousSlide = () => {
  if (visibleAnnouncements.value.length > 1) {
    currentIndex.value = currentIndex.value === 0 ? visibleAnnouncements.value.length - 1 : currentIndex.value - 1;
  }
};

const goToSlide = (index) => {
  currentIndex.value = index;
};

const dismissAnnouncement = (announcement) => {
  router.post('/announcements/dismiss', { announcement_id: announcement.id }, {
    preserveScroll: true,
    onSuccess: () => {
      // Remove from visible list
      visibleAnnouncements.value = visibleAnnouncements.value.filter(a => a.id !== announcement.id);
      
      // Adjust current index if needed
      if (currentIndex.value >= visibleAnnouncements.value.length) {
        currentIndex.value = Math.max(0, visibleAnnouncements.value.length - 1);
      }
    }
  });
};

// Auto-rotate every 7 seconds
onMounted(() => {
  if (visibleAnnouncements.value.length > 1) {
    interval = setInterval(nextSlide, 7000);
  }
});

onUnmounted(() => {
  if (interval) clearInterval(interval);
});
</script>

<style scoped>
.announcement-slide {
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
