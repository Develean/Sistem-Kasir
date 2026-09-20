<template>
  <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(79,70,229,0.14),_transparent_30%),linear-gradient(135deg,_#f8fafc_0%,_#eef2ff_100%)] p-4 sm:p-6 lg:p-8">
    <!-- Header Navigasi Terpusat -->
    <header class="mb-6 flex flex-col gap-4 rounded-[24px] border border-slate-200 bg-slate-900 p-4 text-white shadow-[0_20px_60px_-20px_rgba(15,23,42,0.45)] md:flex-row md:items-center md:justify-between md:p-6">
      <div class="flex items-center gap-3">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-500/20 text-2xl shadow-inner">
          🛒
        </div>
        <div>
          <h1 class="text-xl font-bold tracking-tight text-white sm:text-2xl">TOKO SEJAHTRA</h1>
          <p class="text-xs text-indigo-300 font-medium">Sistem Kasir & Point of Sales</p>
        </div>
      </div>

      <nav class="flex flex-wrap items-center gap-2 sm:gap-3">
        <NuxtLink
          to="/kasir"
          :class="route.path === '/kasir' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
          class="rounded-full px-4 py-2 text-sm font-semibold transition"
        >
          Halaman Kasir
        </NuxtLink>
        <NuxtLink
          to="/barang"
          :class="route.path === '/barang' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
          class="rounded-full px-4 py-2 text-sm font-semibold transition"
        >
          Kelola Barang
        </NuxtLink>
        <NuxtLink
          to="/riwayat"
          :class="route.path === '/riwayat' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
          class="rounded-full px-4 py-2 text-sm font-semibold transition"
        >
          Riwayat Transaksi
        </NuxtLink>

        <!-- User profile indicator -->
        <div class="hidden items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs text-slate-300 md:flex">
          <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
          <span>{{ userName }}</span>
        </div>

        <button
          @click="handleLogout"
          class="rounded-full bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-500 active:scale-95 shadow-sm"
        >
          Logout
        </button>
      </nav>
    </header>

    <!-- Konten Halaman -->
    <main>
      <slot />
    </main>
  </div>
</template>

<script setup>
import { useToast } from '../composables/useToast'

const route = useRoute()
const token = useCookie('token')
const userCookie = useCookie('user')
const runtimeConfig = useRuntimeConfig()
const apiBaseUrl = runtimeConfig.public.apiBaseUrl.replace(/\/+$/, '')
const { show: showToast } = useToast()

const userName = computed(() => {
  if (!userCookie.value) return 'Kasir'
  try {
    const user = typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
    return user.name || user.email || 'Kasir'
  } catch (e) {
    return 'Kasir'
  }
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
    token.value = null
    userCookie.value = null
    showToast('Anda berhasil keluar.', 'info')
    navigateTo('/')
  }
}
</script>
