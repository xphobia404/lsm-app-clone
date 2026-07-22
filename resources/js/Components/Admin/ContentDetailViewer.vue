<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'

// ── Props ────────────────────────────────────────────────────────────
interface Media {
  id: number
  title: string | null
  media_type: 'image' | 'video' | 'audio' | 'url' | string
  url: string | null
  file_path: string | null
  description: string | null
  media_order: number
  is_active: boolean
}

interface Content {
  id: number
  title: string
  content_type: 'text' | 'video' | 'file' | 'url' | string
  body: string | null
  url: string | null
  content_order: number
  is_active: boolean
  created_at: string
  updated_at: string
  media: Media[]
}

interface Section {
  id: number
  title: string
}

const props = defineProps<{
  content: Content
  section: Section
  editRoute?: string
  backRoute?: string
}>()

// ── Quill read-only init ─────────────────────────────────────────────
const quillContainer = ref<HTMLElement | null>(null)
const quillReady = ref(false)

onMounted(async () => {
  if (!props.content.body) return

  // Dynamically load Quill CSS if not present
  if (!document.querySelector('link[href*="quill"]')) {
    const link = document.createElement('link')
    link.rel = 'stylesheet'
    link.href = 'https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css'
    document.head.appendChild(link)
  }

  // Dynamically import Quill
  const { default: Quill } = await import('quill')

  if (quillContainer.value) {
    const quill = new Quill(quillContainer.value, {
      theme: 'snow',
      readOnly: true,
      modules: { toolbar: false },
    })

    // Set HTML content
    const delta = quill.clipboard.convert({ html: props.content.body })
    quill.setContents(delta, 'silent')
    quillReady.value = true
  }
})

// ── Computed helpers ─────────────────────────────────────────────────
const typeBadge = computed(() => {
  const map: Record<string, { label: string; cls: string }> = {
    video:   { label: 'Video',  cls: 'bg-rose-50 text-rose-600' },
    file:    { label: 'File',   cls: 'bg-amber-50 text-amber-600' },
    url:     { label: 'URL',    cls: 'bg-sky-50 text-sky-600' },
    text:    { label: 'Text',   cls: 'bg-indigo-50 text-indigo-600' },
  }
  return map[props.content.content_type] ?? { label: props.content.content_type, cls: 'bg-slate-100 text-slate-600' }
})

function mediaColors(type: string) {
  const map: Record<string, { bg: string; text: string; badge: string }> = {
    video: { bg: 'bg-rose-50',   text: 'text-rose-600',   badge: 'bg-rose-100 text-rose-600' },
    audio: { bg: 'bg-violet-50', text: 'text-violet-600', badge: 'bg-violet-100 text-violet-600' },
    url:   { bg: 'bg-sky-50',    text: 'text-sky-600',    badge: 'bg-sky-100 text-sky-600' },
  }
  return map[type] ?? { bg: 'bg-indigo-50', text: 'text-indigo-600', badge: 'bg-indigo-100 text-indigo-600' }
}

function formatDate(iso: string): string {
  return new Date(iso).toLocaleString('id-ID', {
    day: '2-digit', month: 'long', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}

function storageUrl(path: string): string {
  return `/storage/${path}`
}

function basename(path: string): string {
  return path.split('/').pop() ?? path
}
</script>

<template>
  <div class="px-4 pt-5 pb-10 space-y-5">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 text-xs text-slate-400">
      <a v-if="backRoute" :href="`/admin/sections`" class="hover:text-indigo-600 transition">Sections</a>
      <span v-else>Sections</span>

      <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
      </svg>

      <a v-if="backRoute" :href="backRoute" class="hover:text-indigo-600 transition truncate">
        {{ section.title }}
      </a>
      <span v-else class="truncate">{{ section.title }}</span>

      <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
      </svg>

      <span class="text-slate-800 font-semibold truncate">{{ content.title }}</span>
    </nav>

    <!-- Header -->
    <div class="flex items-start justify-between gap-3">
      <div>
        <h2 class="text-base font-bold text-slate-800">{{ content.title }}</h2>
        <div class="mt-1 flex flex-wrap items-center gap-1.5">
          <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold" :class="typeBadge.cls">
            {{ typeBadge.label }}
          </span>
          <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold"
            :class="content.is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'">
            {{ content.is_active ? 'Aktif' : 'Non-aktif' }}
          </span>
          <span class="text-[10px] text-slate-400">Urutan: {{ content.content_order }}</span>
        </div>
      </div>

      <a v-if="editRoute" :href="editRoute"
         class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-600 hover:bg-amber-100 transition shrink-0">
        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Edit
      </a>
    </div>

    <!-- Body: Quill Read-Only Viewer -->
    <div v-if="content.body" class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5">
      <h3 class="text-xs font-semibold text-slate-500 mb-3 uppercase tracking-wide">Isi Konten</h3>

      <!-- Quill container — toolbar disabled, read-only -->
      <div
        ref="quillContainer"
        class="quill-readonly-viewer"
      />

      <!-- Fallback skeleton while Quill loads -->
      <div v-if="!quillReady" class="space-y-2 animate-pulse">
        <div class="h-4 bg-slate-100 rounded w-full" />
        <div class="h-4 bg-slate-100 rounded w-5/6" />
        <div class="h-4 bg-slate-100 rounded w-4/6" />
      </div>
    </div>

    <!-- URL -->
    <div v-if="content.url" class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5">
      <h3 class="text-xs font-semibold text-slate-500 mb-2 uppercase tracking-wide">URL</h3>
      <a :href="content.url" target="_blank" rel="noopener noreferrer"
         class="text-sm text-indigo-600 break-all hover:underline">
        {{ content.url }}
      </a>
    </div>

    <!-- Media Lampiran -->
    <div class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5">
      <h3 class="text-xs font-semibold text-slate-500 mb-3 uppercase tracking-wide">
        Media Lampiran
        <span class="ml-1 rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-600">
          {{ content.media.length }}
        </span>
      </h3>

      <p v-if="content.media.length === 0" class="text-xs text-slate-400 italic">
        Belum ada media untuk konten ini.
      </p>

      <div v-else class="space-y-3">
        <div
          v-for="m in content.media"
          :key="m.id"
          class="flex items-start gap-3 rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3"
        >
          <!-- Icon -->
          <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl" :class="mediaColors(m.media_type).bg">
            <svg v-if="m.media_type === 'image'" class="h-4 w-4" :class="mediaColors(m.media_type).text" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>
            </svg>
            <svg v-else-if="m.media_type === 'video'" class="h-4 w-4" :class="mediaColors(m.media_type).text" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.26a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
            </svg>
            <svg v-else-if="m.media_type === 'audio'" class="h-4 w-4" :class="mediaColors(m.media_type).text" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
            </svg>
            <svg v-else class="h-4 w-4" :class="mediaColors(m.media_type).text" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
          </div>

          <!-- Info -->
          <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-1.5 mb-0.5">
              <p class="text-xs font-semibold text-slate-700 truncate">
                {{ m.title || `(${m.media_type.toUpperCase()})` }}
              </p>
              <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold" :class="mediaColors(m.media_type).badge">
                {{ m.media_type.toUpperCase() }}
              </span>
              <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold"
                :class="m.is_active ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-slate-400'">
                {{ m.is_active ? 'Aktif' : 'Non-aktif' }}
              </span>
              <span class="text-[10px] text-slate-400">#{{ m.media_order }}</span>
            </div>
            <p v-if="m.description" class="text-[11px] text-slate-400 line-clamp-1 mb-1">{{ m.description }}</p>
            <a v-if="m.url" :href="m.url" target="_blank" rel="noopener noreferrer"
               class="text-[11px] text-indigo-500 break-all hover:underline">{{ m.url }}</a>
            <div v-if="m.file_path" class="flex items-center gap-1.5 mt-1">
              <svg class="h-3 w-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
              </svg>
              <a :href="storageUrl(m.file_path)" target="_blank"
                 class="text-[11px] text-indigo-500 hover:underline truncate">{{ basename(m.file_path) }}</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Meta -->
    <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3 text-xs text-slate-400 space-y-1">
      <p>Dibuat: {{ formatDate(content.created_at) }}</p>
      <p>Diperbarui: {{ formatDate(content.updated_at) }}</p>
    </div>

  </div>
</template>

<style scoped>
/*
  Quill read-only: hide toolbar border, reset padding.
  Class .quill-readonly-viewer wraps the ql-container.
*/
.quill-readonly-viewer :deep(.ql-container.ql-snow) {
  border: none !important;
}
.quill-readonly-viewer :deep(.ql-editor) {
  padding: 0 !important;
  font-size: 0.875rem;
  color: #334155;
  cursor: default;
}
/* Remove Quill focus outline since it's read-only */
.quill-readonly-viewer :deep(.ql-editor:focus) {
  outline: none;
}
</style>
