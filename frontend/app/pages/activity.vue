<template>
  <div>
    <!-- Kartu Statistik Ringkasan Aktivitas -->
    <div class="mb-4 sm:mb-6 grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
      <div class="rounded-2xl sm:rounded-[22px] border border-slate-200 bg-white p-3.5 sm:p-5 shadow-sm">
        <p class="text-[10px] sm:text-xs font-medium text-slate-500">Total Log Aktivitas</p>
        <div class="mt-1 sm:mt-2.5 flex items-end justify-between">
          <p class="text-lg sm:text-2xl font-bold text-slate-900">{{ stats.total || 0 }}</p>
          <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-600">Semua</span>
        </div>
      </div>

      <div class="rounded-2xl sm:rounded-[22px] border border-slate-200 bg-white p-3.5 sm:p-5 shadow-sm">
        <p class="text-[10px] sm:text-xs font-medium text-slate-500">Aktivitas Hari Ini</p>
        <div class="mt-1 sm:mt-2.5 flex items-end justify-between">
          <p class="text-lg sm:text-2xl font-bold text-indigo-600">{{ stats.today || 0 }}</p>
          <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-semibold text-indigo-700">Hari ini</span>
        </div>
      </div>

      <div class="rounded-2xl sm:rounded-[22px] border border-slate-200 bg-white p-3.5 sm:p-5 shadow-sm">
        <p class="text-[10px] sm:text-xs font-medium text-slate-500">Aktivitas Staff Kasir</p>
        <div class="mt-1 sm:mt-2.5 flex items-end justify-between">
          <p class="text-lg sm:text-2xl font-bold text-emerald-600">{{ stats.kasir_count || 0 }}</p>
          <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">Kasir</span>
        </div>
      </div>

      <div class="rounded-2xl sm:rounded-[22px] border border-slate-200 bg-white p-3.5 sm:p-5 shadow-sm">
        <p class="text-[10px] sm:text-xs font-medium text-slate-500">Aktivitas Administrator</p>
        <div class="mt-1 sm:mt-2.5 flex items-end justify-between">
          <p class="text-lg sm:text-2xl font-bold text-amber-600">{{ stats.admin_count || 0 }}</p>
          <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-semibold text-amber-700">Admin</span>
        </div>
      </div>
    </div>

    <!-- Toolbar: Search & Filter Aktivitas -->
    <div class="mb-4 sm:mb-6 rounded-2xl sm:rounded-[22px] border border-slate-200 bg-white p-3.5 sm:p-5 shadow-sm">
      <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <!-- Search & Filter Options -->
        <div class="flex flex-1 flex-wrap items-center gap-2.5">
          <!-- Search Input -->
          <div class="relative min-w-[200px] flex-1 max-w-sm">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 text-sm">🔍</span>
            <input
              v-model="searchKeyword"
              @input="handleSearchInput"
              type="text"
              placeholder="Cari aktivitas / nama / deskripsi..."
              class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 pl-9 pr-3 text-xs sm:text-sm text-slate-800 placeholder-slate-400 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
            />
            <button
              v-if="searchKeyword"
              @click="clearSearch"
              class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 text-xs"
            >
              ✕
            </button>
          </div>

          <!-- Filter Role -->
          <select
            v-model="filterRole"
            @change="loadLogs(1)"
            class="rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-xs sm:text-sm text-slate-700 outline-none focus:border-indigo-500 focus:bg-white"
          >
            <option value="all">Semua Role</option>
            <option value="admin">Admin</option>
            <option value="kasir">Staff Kasir</option>
          </select>

          <!-- Filter Kategori Action -->
          <select
            v-model="filterAction"
            @change="loadLogs(1)"
            class="rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-xs sm:text-sm text-slate-700 outline-none focus:border-indigo-500 focus:bg-white"
          >
            <option value="all">Semua Aksi</option>
            <option value="login">🔐 Login</option>
            <option value="logout">🚪 Logout</option>
            <option value="transaksi">🛒 Transaksi POS</option>
            <option value="batal_transaksi">🚫 Batal (Void)</option>
            <option value="barang">📦 Kelola Barang</option>
            <option value="user_management">👥 Kelola User</option>
          </select>

          <!-- Filter Tanggal -->
          <input
            v-model="filterDate"
            @change="loadLogs(1)"
            type="date"
            class="rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs sm:text-sm text-slate-700 outline-none focus:border-indigo-500 focus:bg-white"
          />

          <button
            v-if="filterRole !== 'all' || filterAction !== 'all' || filterDate || searchKeyword"
            @click="resetFilters"
            class="rounded-xl border border-slate-200 bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200 transition"
          >
            Reset Filter
          </button>
        </div>

        <!-- Tombol Aksi Kanan -->
        <div class="flex items-center gap-2 justify-end">
          <button
            @click="loadLogs(currentPage)"
            :disabled="isLoading"
            title="Muat Ulang"
            class="rounded-xl border border-slate-200 bg-slate-50 p-2.5 text-slate-600 hover:bg-slate-100 active:scale-95 transition"
          >
            <span :class="{ 'animate-spin inline-block': isLoading }">🔄</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Daftar Log Aktivitas Pengguna (Timeline Card Style) -->
    <div class="overflow-hidden rounded-2xl sm:rounded-[22px] border border-slate-200 bg-white shadow-sm">
      <div class="border-b border-slate-100 bg-slate-50/70 px-4 sm:px-6 py-3.5 flex items-center justify-between">
        <h3 class="text-xs sm:text-sm font-bold text-slate-800">Linimasa Aktivitas Pengguna</h3>
        <span class="text-xs text-slate-400">Total: {{ totalLogs }} entri</span>
      </div>

      <div v-if="isLoading" class="p-12 text-center text-slate-500">
        <span class="animate-spin inline-block text-2xl mb-2">🔄</span>
        <p class="text-sm font-medium">Memuat log aktivitas...</p>
      </div>

      <div v-else-if="logs.length === 0" class="p-12 text-center text-slate-500">
        <span class="text-4xl mb-2 inline-block">📋</span>
        <p class="text-sm font-semibold text-slate-700">Belum ada aktivitas tercatat</p>
        <p class="text-xs text-slate-400 mt-1">Aktivitas seperti login, transaksi, atau perubahan data akan otomatis tercatat di sini.</p>
      </div>

      <div v-else class="divide-y divide-slate-100">
        <div
          v-for="item in logs"
          :key="item.id"
          class="flex items-start gap-3 sm:gap-4 p-4 sm:p-5 hover:bg-slate-50/80 transition"
        >
          <!-- Icon Action Bulat -->
          <div
            :class="getActionBadgeClass(item.action)"
            class="flex h-10 w-10 sm:h-11 sm:w-11 shrink-0 items-center justify-center rounded-2xl border text-base sm:text-lg shadow-xs"
          >
            {{ getActionIcon(item.action) }}
          </div>

          <!-- Konten Deskripsi & Info Pengguna -->
          <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
              <span class="font-bold text-xs sm:text-sm text-slate-900">{{ item.user_name }}</span>
              <span
                :class="item.user_role === 'admin'
                  ? 'bg-amber-50 text-amber-700 border-amber-300'
                  : 'bg-emerald-50 text-emerald-700 border-emerald-300'"
                class="rounded-full border px-2 py-0.2 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider"
              >
                {{ item.user_role }}
              </span>
              <span class="text-[10px] sm:text-xs text-slate-400">•</span>
              <span class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider">
                {{ formatActionTitle(item.action) }}
              </span>
            </div>

            <!-- Deskripsi Tindakan -->
            <p class="mt-1 text-xs sm:text-sm text-slate-700 leading-relaxed font-normal">
              {{ item.description }}
            </p>

            <!-- Metadata: Waktu & IP -->
            <div class="mt-2 flex flex-wrap items-center gap-3 text-[11px] text-slate-400 font-mono">
              <span class="flex items-center gap-1 font-sans">
                🕒 {{ formatDateTime(item.created_at) }}
              </span>
              <span v-if="item.ip_address" class="flex items-center gap-1">
                🌐 IP: {{ item.ip_address }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination Footer -->
      <div v-if="totalPages > 1" class="border-t border-slate-100 bg-slate-50/50 px-4 sm:px-6 py-3.5 flex items-center justify-between">
        <p class="text-xs text-slate-500">
          Halaman <span class="font-bold text-slate-800">{{ currentPage }}</span> dari <span class="font-bold text-slate-800">{{ totalPages }}</span>
        </p>
        <div class="flex items-center gap-1.5">
          <button
            @click="loadLogs(currentPage - 1)"
            :disabled="currentPage <= 1"
            class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed"
          >
            ← Sebelumnya
          </button>
          <button
            @click="loadLogs(currentPage + 1)"
            :disabled="currentPage >= totalPages"
            class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed"
          >
            Selanjutnya →
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuth } from '../composables/useAuth'
import { useToast } from '../composables/useToast'

definePageMeta({ middleware: 'auth' })

const { token, isAdmin } = useAuth()
const runtimeConfig = useRuntimeConfig()
const apiBaseUrl = runtimeConfig.public.apiBaseUrl.replace(/\/+$/, '')
const { show: showToast } = useToast()

const logs = ref([])
const isLoading = ref(false)
const searchKeyword = ref('')
const filterRole = ref('all')
const filterAction = ref('all')
const filterDate = ref('')
const currentPage = ref(1)
const totalPages = ref(1)
const totalLogs = ref(0)

const stats = ref({
  total: 0,
  today: 0,
  kasir_count: 0,
  admin_count: 0,
})

let searchDebounce = null
const handleSearchInput = () => {
  if (searchDebounce) clearTimeout(searchDebounce)
  searchDebounce = setTimeout(() => {
    loadLogs(1)
  }, 350)
}

const clearSearch = () => {
  searchKeyword.value = ''
  loadLogs(1)
}

const resetFilters = () => {
  searchKeyword.value = ''
  filterRole.value = 'all'
  filterAction.value = 'all'
  filterDate.value = ''
  loadLogs(1)
}

const getActionIcon = (action) => {
  switch (action) {
    case 'login': return '🔐'
    case 'logout': return '🚪'
    case 'transaksi': return '🛒'
    case 'batal_transaksi': return '🚫'
    case 'barang': return '📦'
    case 'user_management': return '👥'
    default: return '⚡'
  }
}

const getActionBadgeClass = (action) => {
  switch (action) {
    case 'login': return 'bg-blue-50 border-blue-200 text-blue-600'
    case 'logout': return 'bg-slate-50 border-slate-200 text-slate-500'
    case 'transaksi': return 'bg-emerald-50 border-emerald-200 text-emerald-600'
    case 'batal_transaksi': return 'bg-rose-50 border-rose-200 text-rose-600'
    case 'barang': return 'bg-indigo-50 border-indigo-200 text-indigo-600'
    case 'user_management': return 'bg-purple-50 border-purple-200 text-purple-600'
    default: return 'bg-slate-50 border-slate-200 text-slate-600'
  }
}

const formatActionTitle = (action) => {
  switch (action) {
    case 'login': return 'Login Masuk'
    case 'logout': return 'Logout Keluar'
    case 'transaksi': return 'Transaksi POS'
    case 'batal_transaksi': return 'Pembatalan Void'
    case 'barang': return 'Inventori Barang'
    case 'user_management': return 'Manajemen Akun'
    default: return action
  }
}

const formatDateTime = (dateStr) => {
  if (!dateStr) return '-'
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    })
  } catch {
    return dateStr
  }
}

const loadLogs = async (page = 1) => {
  isLoading.value = true
  currentPage.value = page

  try {
    const params = new URLSearchParams()
    params.append('page', page)
    params.append('per_page', 25)

    if (searchKeyword.value) params.append('search', searchKeyword.value)
    if (filterRole.value !== 'all') params.append('role', filterRole.value)
    if (filterAction.value !== 'all') params.append('action', filterAction.value)
    if (filterDate.value) params.append('date', filterDate.value)

    const res = await $fetch(`${apiBaseUrl}/activity-logs?${params.toString()}`, {
      headers: { Authorization: `Bearer ${token.value}` }
    })

    logs.value = res.data || []
    if (res.stats) stats.value = res.stats
    if (res.meta) {
      totalPages.value = res.meta.last_page || 1
      totalLogs.value = res.meta.total || 0
    }
  } catch (err) {
    console.error('Error load activity logs:', err)
    showToast(err.data?.message || 'Gagal memuat log aktivitas.', 'error')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  if (!token.value) {
    navigateTo('/')
    return
  }
  if (!isAdmin.value) {
    showToast('Akses ditolak: Menu Activity User hanya untuk Administrator.', 'warning')
    navigateTo('/kasir')
    return
  }
  loadLogs(1)
})
</script>
