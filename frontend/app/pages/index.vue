<template>
  <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(79,70,229,0.18),_transparent_30%),linear-gradient(135deg,_#f8fafc_0%,_#eef2ff_100%)] p-4 sm:p-6 lg:p-10 flex items-center justify-center">
    <div class="w-full max-w-5xl overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-[0_25px_80px_-20px_rgba(15,23,42,0.35)]">
      <div class="grid lg:grid-cols-[1.05fr_0.95fr]">
        <div class="flex flex-col justify-between bg-slate-900 p-6 sm:p-8 lg:p-12 text-white">
          <div>
            <div class="mb-4 sm:mb-6 inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs sm:text-sm text-slate-200">
              <span class="mr-2 text-base sm:text-lg">🛒</span>
              Sistem Kasir Toko Sejahtra
            </div>
            <h1 class="text-xl sm:text-3xl font-bold leading-tight tracking-tight">Kelola transaksi kasir dengan cepat, akurat, dan aman.</h1>
            <p class="mt-2 sm:mt-4 max-w-md text-xs sm:text-sm leading-relaxed text-slate-300">
              Pantau stok barang real-time, proses pembayaran multi-metode (Tunai & Midtrans), dan arsip riwayat transaksi.
            </p>
          </div>

          <div class="hidden sm:block mt-6 sm:mt-8 rounded-2xl border border-white/10 bg-white/10 p-4 sm:p-5 backdrop-blur-sm">
            <p class="text-xs sm:text-sm font-semibold text-slate-100">Fitur Unggulan POS</p>
            <ul class="mt-2 space-y-1.5 text-xs text-slate-300">
              <li class="flex items-center gap-2">
                <span class="text-emerald-400">✓</span> Autentikasi aman dengan Laravel Sanctum
              </li>
              <li class="flex items-center gap-2">
                <span class="text-emerald-400">✓</span> Pengelolaan stok barang & harga modal (HPP)
              </li>
              <li class="flex items-center gap-2">
                <span class="text-emerald-400">✓</span> Integrasi Midtrans Gateway (QRIS, VA, E-Wallet)
              </li>
            </ul>
          </div>
        </div>

        <div class="p-6 sm:p-8 lg:p-12 flex flex-col justify-center">
          <div class="mb-6 sm:mb-8">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Masuk ke Sistem Kasir</h2>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">Gunakan akun terdaftar untuk mulai transaksi.</p>
          </div>

          <form @submit.prevent="handleLogin" class="space-y-5">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Email Kasir / Admin</label>
              <input
                v-model="form.email"
                type="email"
                placeholder="admin@gmail.com"
                class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                required
              />
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
              <input
                v-model="form.password"
                type="password"
                placeholder="••••••••"
                class="w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
                required
              />
            </div>
            <button
              type="submit"
              :disabled="isSubmittingLogin"
              class="w-full rounded-xl bg-indigo-600 px-4 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 active:scale-[0.99] disabled:cursor-not-allowed disabled:bg-indigo-400"
            >
              {{ isSubmittingLogin ? 'Memverifikasi Akun...' : 'Masuk Sekarang' }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useToast } from '../composables/useToast'

definePageMeta({
  layout: false,
  middleware: ['guest']
})

const runtimeConfig = useRuntimeConfig()
const apiBaseUrl = runtimeConfig.public.apiBaseUrl.replace(/\/+$/, '')
const { show: showToast } = useToast()

const form = ref({ email: '', password: '' })
const isSubmittingLogin = ref(false)

const handleLogin = async () => {
  if (isSubmittingLogin.value) return
  isSubmittingLogin.value = true

  try {
    const res = await $fetch(`${apiBaseUrl}/login`, {
      method: 'POST',
      body: form.value
    })

    const tokenCookie = useCookie('token', { maxAge: 60 * 60 * 24 * 7 })
    tokenCookie.value = res.token

    const userCookie = useCookie('user', { maxAge: 60 * 60 * 24 * 7 })
    userCookie.value = JSON.stringify(res.user)

    showToast(`Login berhasil! Selamat datang, ${res.user?.name || 'Kasir'}.`, 'success')
    navigateTo('/kasir')
  } catch (err) {
    console.error('Login error:', err)
    const message = err.data?.message || 'Email atau Password salah!'
    showToast(message, 'error')
  } finally {
    isSubmittingLogin.value = false
  }
}
</script>
