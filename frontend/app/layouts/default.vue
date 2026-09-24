<template>
  <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(79,70,229,0.14),_transparent_30%),linear-gradient(135deg,_#f8fafc_0%,_#eef2ff_100%)] p-3 sm:p-6 lg:p-8 pb-28 md:pb-8">
    <!-- Header Navigasi Desktop & Tablet -->
    <header class="mb-4 sm:mb-6 rounded-[22px] sm:rounded-[26px] border border-slate-800 bg-slate-900 p-3.5 sm:p-5 text-white shadow-[0_20px_50px_-20px_rgba(15,23,42,0.45)]">
      <div class="flex items-center justify-between">
        <!-- Logo & Identitas Toko -->
        <div class="flex items-center gap-2.5 sm:gap-3.5">
          <div class="flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-xl sm:rounded-2xl bg-indigo-500/20 text-xl sm:text-2xl shadow-inner">
            🛒
          </div>
          <div>
            <h1 class="text-base sm:text-xl font-bold tracking-tight text-white leading-tight">TOKO SEJAHTRA</h1>
            <p class="text-[10px] sm:text-xs text-indigo-300 font-medium">Sistem Kasir & Point of Sales</p>
          </div>
        </div>

        <!-- Menu Navigasi Desktop (hidden di mobile) -->
        <nav class="hidden md:flex items-center gap-2 lg:gap-3">
          <!-- Menu Halaman Kasir: Hanya terlihat untuk Staff Kasir -->
          <NuxtLink
            v-if="isKasir"
            to="/kasir"
            :class="route.path === '/kasir' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
            class="rounded-full px-4 py-2 text-sm font-semibold transition"
          >
            Halaman Kasir
          </NuxtLink>

          <!-- Menu Activity User: Hanya terlihat untuk Admin -->
          <NuxtLink
            v-if="isAdmin"
            to="/activity"
            :class="route.path === '/activity' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
            class="rounded-full px-4 py-2 text-sm font-semibold transition flex items-center gap-1.5"
          >
            <span>Activity User</span>
            <span class="rounded-full bg-indigo-400/20 px-1.5 py-0.2 text-[9px] font-bold text-indigo-300 uppercase">Admin</span>
          </NuxtLink>

          <!-- Menu Kelola Barang: Hanya terlihat untuk Staff Kasir -->
          <NuxtLink
            v-if="isKasir"
            to="/barang"
            :class="route.path === '/barang' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
            class="rounded-full px-4 py-2 text-sm font-semibold transition flex items-center gap-1.5"
          >
            <span>Kelola Barang</span>
          </NuxtLink>

          <NuxtLink
            to="/riwayat"
            :class="route.path === '/riwayat' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
            class="rounded-full px-4 py-2 text-sm font-semibold transition"
          >
            Riwayat Transaksi
          </NuxtLink>

          <!-- Menu Kelola User: Hanya terlihat untuk Admin -->
          <NuxtLink
            v-if="isAdmin"
            to="/users"
            :class="route.path === '/users' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
            class="rounded-full px-4 py-2 text-sm font-semibold transition flex items-center gap-1.5"
          >
            <span>Kelola User</span>
            <span class="rounded-full bg-indigo-400/20 px-1.5 py-0.2 text-[9px] font-bold text-indigo-300 uppercase">Admin</span>
          </NuxtLink>

          <!-- User Profile & Role Badge -->
          <div class="flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs text-slate-300">
            <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="font-medium">{{ userName }}</span>
            <span
              :class="isAdmin ? 'bg-amber-500/20 text-amber-300 border-amber-400/30' : 'bg-emerald-500/20 text-emerald-300 border-emerald-400/30'"
              class="rounded-full border px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
            >
              {{ role }}
            </span>
          </div>

          <button
            @click="handleLogout"
            class="rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500 active:scale-95 shadow-sm"
          >
            Logout
          </button>
        </nav>

        <!-- Right Action Mobile: User info & Logout -->
        <div class="flex md:hidden items-center gap-2">
          <span class="rounded-full bg-white/10 px-2.5 py-1 text-[11px] font-medium text-slate-300 flex items-center gap-1.5">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
            <span class="max-w-[70px] truncate">{{ userName }}</span>
            <span
              :class="isAdmin ? 'bg-amber-500/30 text-amber-300' : 'bg-emerald-500/30 text-emerald-300'"
              class="rounded px-1 text-[9px] font-bold uppercase"
            >
              {{ role }}
            </span>
          </span>
          <button
            @click="handleLogout"
            title="Keluar Akun"
            class="rounded-xl bg-rose-600/90 p-2 text-xs font-semibold text-white transition hover:bg-rose-500 active:scale-95"
          >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </button>
        </div>
      </div>
    </header>

    <!-- Konten Halaman -->
    <main>
      <slot />
    </main>

    <!-- Bottom Navigation Bar Khusus Mobile (< md) -->
    <nav class="fixed bottom-0 inset-x-0 z-40 border-t border-slate-800 bg-slate-900/95 backdrop-blur-md px-3 py-2 flex items-center justify-around shadow-2xl md:hidden">
      <!-- Menu Kasir Mobile: Hanya untuk Staff Kasir -->
      <NuxtLink
        v-if="isKasir"
        to="/kasir"
        :class="route.path === '/kasir' ? 'text-indigo-400 font-bold' : 'text-slate-400 font-medium hover:text-slate-200'"
        class="flex flex-col items-center gap-1 px-3 py-1 text-[11px] transition relative"
      >
        <span class="text-xl">🛒</span>
        <span>Kasir</span>
        <span v-if="route.path === '/kasir'" class="absolute -bottom-1 h-1 w-6 rounded-full bg-indigo-500"></span>
      </NuxtLink>

      <!-- Menu Activity User Mobile: Hanya untuk Admin -->
      <NuxtLink
        v-if="isAdmin"
        to="/activity"
        :class="route.path === '/activity' ? 'text-indigo-400 font-bold' : 'text-slate-400 font-medium hover:text-slate-200'"
        class="flex flex-col items-center gap-1 px-3 py-1 text-[11px] transition relative"
      >
        <span class="text-xl">📊</span>
        <span>Activity</span>
        <span v-if="route.path === '/activity'" class="absolute -bottom-1 h-1 w-6 rounded-full bg-indigo-500"></span>
      </NuxtLink>

      <!-- Menu Kelola Barang Mobile: Hanya terlihat untuk Admin -->
      <NuxtLink
        v-if="isKasir"
        to="/barang"
        :class="route.path === '/barang' ? 'text-indigo-400 font-bold' : 'text-slate-400 font-medium hover:text-slate-200'"
        class="flex flex-col items-center gap-1 px-3 py-1 text-[11px] transition relative"
      >
        <span class="text-xl">📦</span>
        <span>Barang</span>
        <span v-if="route.path === '/barang'" class="absolute -bottom-1 h-1 w-6 rounded-full bg-indigo-500"></span>
      </NuxtLink>

      <NuxtLink
        to="/riwayat"
        :class="route.path === '/riwayat' ? 'text-indigo-400 font-bold' : 'text-slate-400 font-medium hover:text-slate-200'"
        class="flex flex-col items-center gap-1 px-3 py-1 text-[11px] transition relative"
      >
        <span class="text-xl">📜</span>
        <span>Riwayat</span>
        <span v-if="route.path === '/riwayat'" class="absolute -bottom-1 h-1 w-6 rounded-full bg-indigo-500"></span>
      </NuxtLink>

      <!-- Menu Kelola User Mobile: Hanya terlihat untuk Admin -->
      <NuxtLink
        v-if="isAdmin"
        to="/users"
        :class="route.path === '/users' ? 'text-indigo-400 font-bold' : 'text-slate-400 font-medium hover:text-slate-200'"
        class="flex flex-col items-center gap-1 px-3 py-1 text-[11px] transition relative"
      >
        <span class="text-xl">👥</span>
        <span>Users</span>
        <span v-if="route.path === '/users'" class="absolute -bottom-1 h-1 w-6 rounded-full bg-indigo-500"></span>
      </NuxtLink>
    </nav>
  </div>
</template>

<script setup>
import { useAuth } from '../composables/useAuth'
import { useToast } from '../composables/useToast'

const route = useRoute()
const runtimeConfig = useRuntimeConfig()
const apiBaseUrl = runtimeConfig.public.apiBaseUrl.replace(/\/+$/, '')
const { show: showToast } = useToast()
const { user, token, role, isAdmin, isKasir, clearAuth } = useAuth()

const userName = computed(() => {
  return user.value?.name || user.value?.email || 'Kasir'
})

const handleLogout = async () => {
  if (!confirm('Yakin ingin keluar dari aplikasi?')) return

  try {
    if (token.value) {
      await $fetch(`${apiBaseUrl}/logout`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${token.value}` }
      })
    }
  } catch (err) {
    console.error('Logout error:', err)
  } finally {
    clearAuth()
    showToast('Anda berhasil keluar.', 'info')
    navigateTo('/')
  }
}
</script>
