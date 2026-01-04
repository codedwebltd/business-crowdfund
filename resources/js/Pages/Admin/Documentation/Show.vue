<template>
  <AdminLayout title="Documentation" :settings="settings">
    <!-- Breadcrumbs -->
    <Breadcrumbs :crumbs="[
      { label: 'Dashboard', url: '/admin' },
      { label: 'Documentation', url: '/admin/documentation' },
      { label: doc.title }
    ]" class="mb-4" />

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
      <!-- Sidebar - Documentation Menu -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 sticky top-4">
          <div class="p-4 border-b border-gray-200">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Documentation</h3>
          </div>

          <nav class="p-2">
            <Link
              v-for="d in allDocs"
              :key="d.slug"
              :href="`/admin/documentation/${d.slug}`"
              :class="[
                'flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all text-sm mb-1',
                d.slug === doc.slug
                  ? 'bg-blue-50 text-blue-700 font-medium'
                  : 'text-gray-700 hover:bg-gray-50'
              ]"
            >
              <span class="text-lg">{{ d.icon }}</span>
              <span class="flex-1 truncate">{{ d.title }}</span>
            </Link>
          </nav>
        </div>
      </div>

      <!-- Main Content -->
      <div class="lg:col-span-4">
        <!-- Header Card -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600 rounded-2xl shadow-xl mb-6">
          <!-- Decorative background elements -->
          <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32 blur-3xl"></div>
          <div class="absolute bottom-0 left-0 w-48 h-48 bg-indigo-400/20 rounded-full -ml-24 -mb-24 blur-2xl"></div>

          <div class="relative p-6 sm:p-10">
            <div class="flex items-start gap-6">
              <!-- Icon with glow effect -->
              <div class="relative flex-shrink-0">
                <div class="absolute inset-0 bg-white/30 rounded-2xl blur-xl"></div>
                <div class="relative w-20 h-20 sm:w-24 sm:h-24 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center text-4xl sm:text-5xl shadow-2xl border border-white/30">
                  {{ doc.icon }}
                </div>
              </div>

              <div class="flex-1 min-w-0">
                <!-- Category badge -->
                <div class="flex items-center gap-2 mb-3">
                  <span class="inline-flex items-center gap-1.5 text-xs font-bold bg-white/25 backdrop-blur-sm text-white px-3 py-1.5 rounded-lg border border-white/30 shadow-lg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    {{ doc.category }}
                  </span>
                </div>

                <!-- Title -->
                <h1 class="text-2xl sm:text-4xl font-bold text-white mb-3 leading-tight drop-shadow-lg">
                  {{ doc.title }}
                </h1>

                <!-- Description -->
                <p class="text-blue-50 text-sm sm:text-base leading-relaxed max-w-3xl">
                  {{ doc.description }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
          <article class="docs-content p-6 sm:p-8 sm:p-10" v-html="renderedContent" ref="contentElement"></article>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { marked } from 'marked';
import hljs from 'highlight.js/lib/common';

const props = defineProps({
  doc: Object,
  allDocs: Array,
  settings: Object
});

const contentElement = ref(null);

// Simple marked configuration without custom renderer
marked.setOptions({
  breaks: true,
  gfm: true,
  highlight: function(code, lang) {
    const language = hljs.getLanguage(lang) ? lang : 'plaintext';
    return hljs.highlight(code, { language }).value;
  }
});

const renderedContent = computed(() => {
  // Safety checks
  if (!props.doc) {
    return '<p class="text-red-600">Documentation not found.</p>';
  }

  if (!props.doc.content) {
    return '<p class="text-red-600">No content available.</p>';
  }

  // Get content and ensure it's a string
  let content = props.doc.content;

  // Handle different content types
  if (typeof content !== 'string') {
    console.warn('Content is not a string, type:', typeof content);
    console.warn('Content value:', content);

    // Try to convert to string
    try {
      content = content.toString();
    } catch (e) {
      return '<p class="text-red-600">Invalid content format.</p>';
    }
  }

  // Validate string
  if (!content || content.trim() === '') {
    return '<p class="text-gray-600">This documentation is empty.</p>';
  }

  // Parse markdown
  try {
    const html = marked.parse(content, { async: false });
    return html;
  } catch (error) {
    console.error('Markdown error:', error);
    // Return the raw content as fallback
    return `<pre class="text-sm bg-gray-100 p-4 rounded">${escapeHtml(content)}</pre>`;
  }
});

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

onMounted(() => {
  nextTick(() => {
    // Add copy buttons to code blocks after render
    if (contentElement.value) {
      const codeBlocks = contentElement.value.querySelectorAll('pre code');
      codeBlocks.forEach((block) => {
        const pre = block.parentElement;
        if (!pre.querySelector('.copy-button')) {
          const button = document.createElement('button');
          button.className = 'copy-button';
          button.innerHTML = `
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <span>Copy</span>
          `;

          button.addEventListener('click', () => {
            navigator.clipboard.writeText(block.textContent).then(() => {
              button.innerHTML = `
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Copied!</span>
              `;
              button.classList.add('copied');

              setTimeout(() => {
                button.innerHTML = `
                  <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                  </svg>
                  <span>Copy</span>
                `;
                button.classList.remove('copied');
              }, 2000);
            });
          });

          const wrapper = document.createElement('div');
          wrapper.className = 'code-wrapper';
          pre.parentNode.insertBefore(wrapper, pre);
          wrapper.appendChild(pre);
          wrapper.insertBefore(button, pre);
        }
      });
    }
  });
});
</script>

<style scoped>
/* GitHub-style Documentation Styling */
.docs-content {
  font-size: 16px;
  line-height: 1.7;
  color: #24292f;
  max-width: 100%;
  overflow-wrap: break-word;
  word-wrap: break-word;
  word-break: break-word;
}

.docs-content :deep(h1),
.docs-content :deep(h2),
.docs-content :deep(h3),
.docs-content :deep(h4) {
  margin-top: 24px;
  margin-bottom: 16px;
  font-weight: 600;
  line-height: 1.25;
  color: #1f2937;
}

.docs-content :deep(h1) {
  font-size: 2em;
  padding-bottom: 0.3em;
  border-bottom: 2px solid #e5e7eb;
}

.docs-content :deep(h2) {
  font-size: 1.5em;
  padding-bottom: 0.3em;
  border-bottom: 1px solid #e5e7eb;
  margin-top: 32px;
}

.docs-content :deep(h3) {
  font-size: 1.25em;
}

.docs-content :deep(p) {
  margin-bottom: 16px;
  color: #374151;
}

.docs-content :deep(ul),
.docs-content :deep(ol) {
  margin-bottom: 16px;
  padding-left: 2em;
  color: #374151;
}

.docs-content :deep(li) {
  margin-bottom: 4px;
}

.docs-content :deep(code) {
  padding: 0.2em 0.4em;
  margin: 0;
  font-size: 85%;
  background-color: rgba(175, 184, 193, 0.2);
  border-radius: 6px;
  font-family: ui-monospace, SFMono-Regular, monospace;
  color: #e11d48;
  word-break: break-all;
  overflow-wrap: break-word;
}

.docs-content :deep(pre) {
  position: relative;
  margin: 16px 0;
  padding: 16px;
  overflow-x: auto;
  overflow-y: hidden;
  font-size: 14px;
  line-height: 1.6;
  background: #1f2937;
  border-radius: 8px;
  font-family: ui-monospace, SFMono-Regular, monospace;
  max-width: 100%;
}

.docs-content :deep(pre code) {
  display: block;
  padding: 0;
  margin: 0;
  background: transparent;
  color: #e5e7eb;
  border-radius: 0;
  word-break: normal;
  overflow-wrap: normal;
  white-space: pre;
}

.docs-content :deep(.code-wrapper) {
  position: relative;
  margin: 16px 0;
}

.docs-content :deep(.code-wrapper:hover .copy-button) {
  opacity: 1;
  visibility: visible;
}

.docs-content :deep(.copy-button) {
  position: absolute;
  top: 12px;
  right: 12px;
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 10px;
  font-size: 12px;
  font-weight: 600;
  color: #d1d5db;
  background: rgba(55, 65, 81, 0.95);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(75, 85, 99, 0.5);
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  z-index: 20;
  opacity: 0;
  visibility: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.docs-content :deep(.copy-button:hover) {
  background: rgba(75, 85, 99, 0.98);
  border-color: #6b7280;
  color: #f9fafb;
  opacity: 1;
  transform: scale(1.05);
}

.docs-content :deep(.copy-button.copied) {
  color: #6ee7b7;
  border-color: #34d399;
  background: rgba(6, 78, 59, 0.95);
  opacity: 1;
}

.docs-content :deep(a) {
  color: #2563eb;
  text-decoration: none;
  font-weight: 500;
  word-break: break-all;
  overflow-wrap: break-word;
}

.docs-content :deep(a:hover) {
  text-decoration: underline;
}

.docs-content :deep(blockquote) {
  margin: 0 0 16px 0;
  padding: 0 1em;
  color: #6b7280;
  border-left: 4px solid #e5e7eb;
}

.docs-content :deep(strong) {
  font-weight: 600;
  color: #1f2937;
}

@media (max-width: 640px) {
  .docs-content {
    font-size: 15px;
  }

  .docs-content :deep(pre) {
    padding: 12px;
    font-size: 13px;
  }
}
</style>
