<template>
  <AdminLayout title="Documentation" :settings="settings">
    <!-- Breadcrumbs -->
    <Breadcrumbs :crumbs="[
      { label: 'Dashboard', url: '/admin' },
      { label: 'Documentation' }
    ]" class="mb-4" />

    <!-- Header -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 sm:p-8 mb-6 text-white">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-4xl">
            📚
          </div>
          <div>
            <h1 class="text-3xl sm:text-4xl font-bold">Documentation</h1>
            <p class="text-blue-100 text-sm sm:text-base mt-1">Technical guides and integration documentation</p>
          </div>
        </div>

        <!-- View Toggle -->
        <div class="hidden sm:flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg p-1">
          <button
            @click="viewMode = 'list'"
            :class="[
              'flex items-center gap-2 px-3 py-2 rounded-md transition-all text-sm font-medium',
              viewMode === 'list'
                ? 'bg-white text-blue-600 shadow-md'
                : 'text-white/80 hover:text-white'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <span>List</span>
          </button>
          <button
            @click="viewMode = 'grid'"
            :class="[
              'flex items-center gap-2 px-3 py-2 rounded-md transition-all text-sm font-medium',
              viewMode === 'grid'
                ? 'bg-white text-blue-600 shadow-md'
                : 'text-white/80 hover:text-white'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span>Grid</span>
          </button>
        </div>
      </div>

      <!-- Mobile View Toggle -->
      <div class="flex sm:hidden items-center gap-2 bg-white/10 backdrop-blur-sm rounded-lg p-1 mt-4">
        <button
          @click="viewMode = 'list'"
          :class="[
            'flex items-center justify-center gap-2 flex-1 px-3 py-2 rounded-md transition-all text-sm font-medium',
            viewMode === 'list'
              ? 'bg-white text-blue-600 shadow-md'
              : 'text-white/80'
          ]"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
          <span>List</span>
        </button>
        <button
          @click="viewMode = 'grid'"
          :class="[
            'flex items-center justify-center gap-2 flex-1 px-3 py-2 rounded-md transition-all text-sm font-medium',
            viewMode === 'grid'
              ? 'bg-white text-blue-600 shadow-md'
              : 'text-white/80'
          ]"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
          </svg>
          <span>Grid</span>
        </button>
      </div>
    </div>

    <!-- List View -->
    <div v-if="viewMode === 'list'" class="space-y-3">
      <Link
        v-for="doc in docs"
        :key="doc.slug"
        :href="`/admin/documentation/${doc.slug}`"
        class="group block bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md hover:border-blue-200 transition-all duration-200"
      >
        <div class="flex items-center gap-4 p-5">
          <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-3xl shadow-md flex-shrink-0 group-hover:shadow-lg transition-shadow">
            {{ doc.icon }}
          </div>

          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
              <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors truncate">
                {{ doc.title }}
              </h3>
              <span class="text-xs font-semibold bg-blue-100 text-blue-700 px-2 py-0.5 rounded flex-shrink-0">
                {{ doc.category }}
              </span>
            </div>
            <p class="text-sm text-gray-600 line-clamp-2">
              {{ doc.description }}
            </p>
          </div>

          <div class="hidden sm:flex items-center gap-2 text-blue-600 flex-shrink-0">
            <span class="text-sm font-medium">Read docs</span>
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </div>
        </div>
      </Link>
    </div>

    <!-- Grid View -->
    <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <Link
        v-for="doc in docs"
        :key="doc.slug"
        :href="`/admin/documentation/${doc.slug}`"
        class="group bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg hover:border-blue-200 transition-all duration-200 transform hover:-translate-y-1"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-3xl shadow-lg group-hover:shadow-xl transition-shadow">
            {{ doc.icon }}
          </div>
          <span class="text-xs font-semibold bg-blue-100 text-blue-700 px-3 py-1 rounded-full">
            {{ doc.category }}
          </span>
        </div>

        <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
          {{ doc.title }}
        </h3>

        <p class="text-sm text-gray-600 line-clamp-3 mb-4">
          {{ doc.description }}
        </p>

        <div class="flex items-center text-blue-600 text-sm font-medium">
          <span>Read documentation</span>
          <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
      </Link>
    </div>

    <!-- Empty State -->
    <div v-if="!docs || docs.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
      <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
      </div>
      <h3 class="text-xl font-bold text-gray-900 mb-2">No Documentation Available</h3>
      <p class="text-gray-600">Documentation will appear here as it becomes available.</p>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const props = defineProps({
  docs: Array,
  settings: Object
});

// Default to list view
const viewMode = ref('list');
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
