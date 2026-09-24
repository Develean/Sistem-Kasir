<template>
  <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(79,70,229,0.14),_transparent_30%),linear-gradient(135deg,_#f8fafc_0%,_#eef2ff_100%)] flex flex-col lg:flex-row">
    <!-- ========================================== -->
    <!-- DESKTOP SIDEBAR (Tampil di Layar lg ke Atas) -->
    <!-- ========================================== -->
    <aside
      :class="isDesktopSidebarOpen ? 'w-64 xl:w-72 p-4 xl:p-5 opacity-100' : 'w-0 p-0 border-r-0 opacity-0 overflow-hidden pointer-events-none'"
      class="hidden lg:flex shrink-0 bg-slate-900 border-r border-slate-800 text-white h-screen sticky top-0 flex-col justify-between shadow-2xl z-30 overflow-y-auto transition-all duration-300 ease-in-out"
    >
      <div class="space-y-6 w-full">
        <!-- Logo & Branding Toko + Tombol Tutup Sidebar -->
        <div class="flex items-center justify-between gap-2 px-1 py-1">
          <div class="flex items-center gap-3 min-w-0">
            <img
              v-if="logoToko"
              :src="logoToko"
              alt="Logo Toko"
              class="h-10 w-10 shrink-0 rounded-2xl object-cover shadow-inner border border-indigo-500/30 bg-white"
            />
            <div
              v-else
              class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-indigo-500/20 text-xl shadow-inner border border-indigo-500/30"
            >
              🛒
            </div>
            <div class="min-w-0">
              <h1 class="text-sm xl:text-base font-extrabold tracking-tight text-white leading-tight truncate">
                {{ namaToko }}
              </h1>
              <p class="text-[10px] text-indigo-300 font-medium">Sistem Kasir & POS</p>
            </div>
          </div>

          <!-- Tombol Tutup Sidebar di Desktop -->
          <button
            type="button"
            @click="toggleDesktopSidebar"
            title="Tutup Sidebar (Ctrl+B)"
            class="hidden lg:flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/10 text-slate-300 hover:bg-white/20 hover:text-white transition active:scale-95"
          >
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
          </button>
        </div>

        <!-- Kartu Profil Pengguna & Role Badge -->
        <div class="rounded-2xl border border-white/10 bg-white/5 p-3 xl:p-3.5 backdrop-blur-sm">
          <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-800 border border-white/10 text-sm font-bold text-white uppercase">
              {{ userName.slice(0, 2) }}
            </div>
            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-1.5">
                <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                <p class="text-xs font-bold text-slate-100 truncate">{{ userName }}</p>
              </div>
              <div class="mt-1">
                <span
                  :class="isAdmin ? 'bg-amber-500/20 text-amber-300 border-amber-400/30' : 'bg-emerald-500/20 text-emerald-300 border-emerald-400/30'"
                  class="inline-block rounded-full border px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wider"
                >
                  {{ role }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Daftar Navigasi Utama -->
        <div class="space-y-5">
          <!-- KELOMPOK 1: MENU UTAMA -->
          <div>
            <p class="px-3 text-[10px] font-extrabold tracking-wider text-slate-400 uppercase mb-2">
              Menu Utama
            </p>
            <nav class="space-y-1">
              <!-- Menu Dashboard: Khusus Admin -->
              <NuxtLink
                v-if="isAdmin"
                to="/dashboard"
                :class="route.path === '/dashboard' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
                class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs xl:text-sm font-medium transition group"
              >
                <span class="text-lg">📈</span>
                <span class="flex-1">Dashboard</span>
                <span v-if="route.path === '/dashboard'" class="h-1.5 w-1.5 rounded-full bg-white"></span>
              </NuxtLink>

              <!-- Menu Halaman Kasir: Khusus Staff Kasir -->
              <NuxtLink
                v-if="isKasir"
                to="/kasir"
                :class="route.path === '/kasir' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
                class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs xl:text-sm font-medium transition group"
              >
                <span class="text-lg">🛒</span>
                <span class="flex-1">Halaman Kasir</span>
                <span v-if="route.path === '/kasir'" class="h-1.5 w-1.5 rounded-full bg-white"></span>
              </NuxtLink>

              <!-- Menu Kelola Barang: Dapat diakses oleh Admin & Kasir -->
              <NuxtLink
                to="/barang"
                :class="route.path === '/barang' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
                class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs xl:text-sm font-medium transition group"
              >
                <span class="text-lg">📦</span>
                <span class="flex-1">Kelola Barang</span>
                <span v-if="route.path === '/barang'" class="h-1.5 w-1.5 rounded-full bg-white"></span>
              </NuxtLink>

              <!-- Menu Riwayat Transaksi: Semua Role -->
              <NuxtLink
                to="/riwayat"
                :class="route.path === '/riwayat' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
                class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs xl:text-sm font-medium transition group"
              >
                <span class="text-lg">📜</span>
                <span class="flex-1">Riwayat Transaksi</span>
                <span v-if="route.path === '/riwayat'" class="h-1.5 w-1.5 rounded-full bg-white"></span>
              </NuxtLink>
            </nav>
          </div>

          <!-- KELOMPOK 2: ADMINISTRASI & SISTEM (Khusus Admin) -->
          <div v-if="isAdmin">
            <p class="px-3 text-[10px] font-extrabold tracking-wider text-slate-400 uppercase mb-2">
              Sistem & Kelola
            </p>
            <nav class="space-y-1">
              <!-- Menu Activity User: Khusus Admin -->
              <NuxtLink
                to="/activity"
                :class="route.path === '/activity' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
                class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs xl:text-sm font-medium transition group"
              >
                <span class="text-lg">📊</span>
                <span class="flex-1">Activity User</span>
                <span class="rounded bg-indigo-400/20 px-1.5 py-0.2 text-[9px] font-bold text-indigo-300 uppercase">Admin</span>
              </NuxtLink>

              <!-- Menu Kelola User: Khusus Admin -->
              <NuxtLink
                to="/users"
                :class="route.path === '/users' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
                class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs xl:text-sm font-medium transition group"
              >
                <span class="text-lg">👥</span>
                <span class="flex-1">Kelola Pengguna</span>
                <span class="rounded bg-indigo-400/20 px-1.5 py-0.2 text-[9px] font-bold text-indigo-300 uppercase">Admin</span>
              </NuxtLink>

              <!-- Menu Pengaturan Toko: Khusus Admin -->
              <NuxtLink
                to="/settings"
                :class="route.path === '/settings' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white'"
                class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs xl:text-sm font-medium transition group"
              >
                <span class="text-lg">⚙️</span>
                <span class="flex-1">Pengaturan Toko</span>
                <span class="rounded bg-indigo-400/20 px-1.5 py-0.2 text-[9px] font-bold text-indigo-300 uppercase">Admin</span>
              </NuxtLink>
            </nav>
          </div>
        </div>
      </div>

      <!-- Footer Sidebar Desktop: Tombol Keluar Akun -->
      <div class="pt-4 border-t border-slate-800">
        <button
          @click="handleLogout"
          class="w-full flex items-center justify-center gap-2 rounded-xl bg-rose-600/90 hover:bg-rose-500 px-4 py-2.5 text-xs xl:text-sm font-bold text-white transition active:scale-[0.98] shadow-sm"
        >
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span>Keluar Akun</span>
        </button>
      </div>
    </aside>

    <!-- ========================================== -->
    <!-- HEADER NAVIGASI KHUSUS MOBILE (< lg)       -->
    <!-- ========================================== -->
    <div class="lg:hidden sticky top-0 z-40 bg-slate-900/95 backdrop-blur-md border-b border-slate-800 px-4 py-3 flex items-center justify-between text-white shadow-lg">
      <div class="flex items-center gap-3">
        <!-- Tombol Buka Drawer Mobile -->
        <button
          @click="isMobileSidebarOpen = true"
          aria-label="Buka Navigasi"
          class="rounded-xl bg-white/10 p-2 text-white hover:bg-white/15 active:scale-95 transition"
        >
          <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <div class="flex items-center gap-2.5">
          <img
            v-if="logoToko"
            :src="logoToko"
            alt="Logo Toko"
            class="h-7 w-7 rounded-lg object-cover bg-white"
          />
          <span v-else class="text-xl">🛒</span>
          <div>
            <h1 class="text-sm font-bold leading-tight truncate max-w-[140px] sm:max-w-xs">{{ namaToko }}</h1>
            <p class="text-[9px] text-indigo-300">Point of Sales</p>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <span
          :class="isAdmin ? 'bg-amber-500/20 text-amber-300 border-amber-400/30' : 'bg-emerald-500/20 text-emerald-300 border-emerald-400/30'"
          class="rounded-full border px-2 py-0.5 text-[9px] font-bold uppercase"
        >
          {{ role }}
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

    <!-- ========================================== -->
    <!-- SLIDE-OVER DRAWER MOBILE (< lg)            -->
    <!-- ========================================== -->
    <div
      v-if="isMobileSidebarOpen"
      class="fixed inset-0 z-50 lg:hidden flex"
    >
      <!-- Backdrop Overlay -->
      <div
        @click="isMobileSidebarOpen = false"
        class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"
      ></div>

      <!-- Drawer Content -->
      <div class="relative w-72 max-w-[85vw] bg-slate-900 text-white h-full flex flex-col justify-between p-5 z-10 shadow-2xl border-r border-slate-800">
        <div>
          <!-- Header Drawer -->
          <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div class="flex items-center gap-2.5">
              <img
                v-if="logoToko"
                :src="logoToko"
                alt="Logo Toko"
                class="h-8 w-8 rounded-xl object-cover bg-white"
              />
              <span v-else class="text-2xl">🛒</span>
              <div>
                <h2 class="text-sm font-bold text-white truncate max-w-[150px]">{{ namaToko }}</h2>
                <p class="text-[10px] text-indigo-300">Menu Navigasi</p>
              </div>
            </div>
            <button
              @click="isMobileSidebarOpen = false"
              class="rounded-lg p-1.5 text-slate-400 hover:text-white hover:bg-white/10"
            >
              ✕
            </button>
          </div>

          <!-- User Info di Drawer -->
          <div class="my-4 rounded-xl border border-white/10 bg-white/5 p-3">
            <p class="text-[11px] text-slate-400">Masuk sebagai:</p>
            <p class="text-xs font-bold text-white truncate">{{ userName }}</p>
            <span
              :class="isAdmin ? 'bg-amber-500/20 text-amber-300 border-amber-400/30' : 'bg-emerald-500/20 text-emerald-300 border-emerald-400/30'"
              class="inline-block mt-1 rounded border px-1.5 py-0.2 text-[9px] font-extrabold uppercase"
            >
              {{ role }}
            </span>
          </div>

          <!-- Navigasi Mobile Drawer -->
          <nav class="space-y-1 text-sm">
            <NuxtLink
              v-if="isAdmin"
              to="/dashboard"
              @click="isMobileSidebarOpen = false"
              :class="route.path === '/dashboard' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-white/10'"
              class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition"
            >
              <span class="text-lg">📈</span>
              <span>Dashboard</span>
            </NuxtLink>

            <NuxtLink
              v-if="isKasir"
              to="/kasir"
              @click="isMobileSidebarOpen = false"
              :class="route.path === '/kasir' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-white/10'"
              class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition"
            >
              <span class="text-lg">🛒</span>
              <span>Halaman Kasir</span>
            </NuxtLink>

            <NuxtLink
              to="/barang"
              @click="isMobileSidebarOpen = false"
              :class="route.path === '/barang' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-white/10'"
              class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition"
            >
              <span class="text-lg">📦</span>
              <span>Kelola Barang</span>
            </NuxtLink>

            <NuxtLink
              to="/riwayat"
              @click="isMobileSidebarOpen = false"
              :class="route.path === '/riwayat' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-white/10'"
              class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition"
            >
              <span class="text-lg">📜</span>
              <span>Riwayat Transaksi</span>
            </NuxtLink>

            <div v-if="isAdmin" class="pt-2 border-t border-slate-800 my-2">
              <p class="px-3 text-[10px] font-bold text-slate-400 uppercase mb-1">Admin</p>
              <NuxtLink
                to="/activity"
                @click="isMobileSidebarOpen = false"
                :class="route.path === '/activity' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-white/10'"
                class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition"
              >
                <span class="text-lg">📊</span>
                <span>Activity User</span>
              </NuxtLink>

              <NuxtLink
                to="/users"
                @click="isMobileSidebarOpen = false"
                :class="route.path === '/users' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-white/10'"
                class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition"
              >
                <span class="text-lg">👥</span>
                <span>Kelola Pengguna</span>
              </NuxtLink>

              <NuxtLink
                to="/settings"
                @click="isMobileSidebarOpen = false"
                :class="route.path === '/settings' ? 'bg-indigo-600 text-white font-bold' : 'text-slate-300 hover:bg-white/10'"
                class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition"
              >
                <span class="text-lg">⚙️</span>
                <span>Pengaturan Toko</span>
              </NuxtLink>
            </div>
          </nav>
        </div>

        <!-- Tombol Logout Mobile Drawer -->
        <button
          @click="handleLogout"
          class="w-full flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-xs font-bold text-white transition active:scale-[0.98]"
        >
          <span>Keluar Akun</span>
        </button>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- AREA KONTEN UTAMA (Main Content Area)      -->
    <!-- ========================================== -->
    <div class="flex-1 min-w-0 flex flex-col min-h-screen">
      <!-- Desktop Topbar untuk Buka / Tutup Sidebar -->
      <div class="hidden lg:flex items-center justify-between border-b border-slate-200/80 bg-white/80 backdrop-blur-md px-6 py-2.5 sticky top-0 z-20 shadow-xs">
        <div class="flex items-center gap-3">
          <!-- Tombol Buka / Tutup Sidebar Desktop -->
          <button
            type="button"
            @click="toggleDesktopSidebar"
            class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-50 hover:text-indigo-600 hover:border-indigo-300 active:scale-95 transition shadow-xs group"
            :title="isDesktopSidebarOpen ? 'Tutup Sidebar (Ctrl+B)' : 'Buka Sidebar (Ctrl+B)'"
          >
            <svg
              class="h-4 w-4 text-slate-500 group-hover:text-indigo-600 transition-transform duration-200"
              :class="{ 'rotate-180': !isDesktopSidebarOpen }"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
            <span>{{ isDesktopSidebarOpen ? 'Tutup Sidebar' : 'Buka Sidebar' }}</span>
            <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[9px] font-mono text-slate-400">Ctrl+B</span>
          </button>

          <!-- Indikator Branding jika Sidebar Tertutup -->
          <div v-if="!isDesktopSidebarOpen" class="flex items-center gap-2 pl-3 border-l border-slate-200">
            <img
              v-if="logoToko"
              :src="logoToko"
              alt="Logo"
              class="h-6 w-6 rounded-lg object-cover bg-white border border-slate-200"
            />
            <span v-else class="text-base">🛒</span>
            <span class="text-xs font-extrabold text-slate-800 tracking-tight">{{ namaToko }}</span>
          </div>
        </div>

        <!-- Info User & Status di Topbar Kanan Desktop -->
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-xs font-semibold text-slate-600">{{ userName }}</span>
            <span
              :class="isAdmin ? 'bg-amber-50 text-amber-700 border-amber-300' : 'bg-emerald-50 text-emerald-700 border-emerald-300'"
              class="rounded-full border px-2 py-0.5 text-[9px] font-extrabold uppercase"
            >
              {{ role }}
            </span>
          </div>
        </div>
      </div>

      <!-- Main Slot Content -->
      <main class="flex-1 min-w-0 p-3 sm:p-6 lg:p-8 pb-24 lg:pb-8 overflow-y-auto">
        <div class="max-w-7xl mx-auto w-full">
          <slot />
        </div>
      </main>
    </div>

    <!-- ========================================== -->
    <!-- BOTTOM NAV MOBILE (< md)                   -->
    <!-- ========================================== -->
    <nav class="fixed bottom-0 inset-x-0 z-40 border-t border-slate-800 bg-slate-900/95 backdrop-blur-md px-2 py-1.5 flex items-center justify-around shadow-2xl lg:hidden">
      <!-- Shortcut 1: Kasir (jika Kasir) / Dashboard (jika Admin) -->
      <NuxtLink
        v-if="isKasir"
        to="/kasir"
        :class="route.path === '/kasir' ? 'text-indigo-400 font-bold' : 'text-slate-400 font-medium hover:text-slate-200'"
        class="flex flex-col items-center gap-0.5 px-2 py-1 text-[10px] transition relative"
      >
        <span class="text-lg">🛒</span>
        <span>Kasir</span>
        <span v-if="route.path === '/kasir'" class="absolute -bottom-1 h-1 w-5 rounded-full bg-indigo-500"></span>
      </NuxtLink>

      <NuxtLink
        v-if="isAdmin"
        to="/dashboard"
        :class="route.path === '/dashboard' ? 'text-indigo-400 font-bold' : 'text-slate-400 font-medium hover:text-slate-200'"
        class="flex flex-col items-center gap-0.5 px-2 py-1 text-[10px] transition relative"
      >
        <span class="text-lg">📈</span>
        <span>Dashboard</span>
        <span v-if="route.path === '/dashboard'" class="absolute -bottom-1 h-1 w-5 rounded-full bg-indigo-500"></span>
      </NuxtLink>

      <!-- Shortcut 2: Kelola Barang (Admin & Kasir) -->
      <NuxtLink
        to="/barang"
        :class="route.path === '/barang' ? 'text-indigo-400 font-bold' : 'text-slate-400 font-medium hover:text-slate-200'"
        class="flex flex-col items-center gap-0.5 px-2 py-1 text-[10px] transition relative"
      >
        <span class="text-lg">📦</span>
        <span>Barang</span>
        <span v-if="route.path === '/barang'" class="absolute -bottom-1 h-1 w-5 rounded-full bg-indigo-500"></span>
      </NuxtLink>

      <!-- Shortcut 3: Riwayat Transaksi -->
      <NuxtLink
        to="/riwayat"
        :class="route.path === '/riwayat' ? 'text-indigo-400 font-bold' : 'text-slate-400 font-medium hover:text-slate-200'"
        class="flex flex-col items-center gap-0.5 px-2 py-1 text-[10px] transition relative"
      >
        <span class="text-lg">📜</span>
        <span>Riwayat</span>
        <span v-if="route.path === '/riwayat'" class="absolute -bottom-1 h-1 w-5 rounded-full bg-indigo-500"></span>
      </NuxtLink>

      <!-- Shortcut 4: Activity (Admin) -->
      <NuxtLink
        v-if="isAdmin"
        to="/activity"
        :class="route.path === '/activity' ? 'text-indigo-400 font-bold' : 'text-slate-400 font-medium hover:text-slate-200'"
        class="flex flex-col items-center gap-0.5 px-2 py-1 text-[10px] transition relative"
      >
        <span class="text-lg">📊</span>
        <span>Activity</span>
        <span v-if="route.path === '/activity'" class="absolute -bottom-1 h-1 w-5 rounded-full bg-indigo-500"></span>
      </NuxtLink>

      <!-- Shortcut 5: Kelola Users (Admin) -->
      <NuxtLink
        v-if="isAdmin"
        to="/users"
        :class="route.path === '/users' ? 'text-indigo-400 font-bold' : 'text-slate-400 font-medium hover:text-slate-200'"
        class="flex flex-col items-center gap-0.5 px-2 py-1 text-[10px] transition relative"
      >
        <span class="text-lg">👥</span>
        <span>Users</span>
        <span v-if="route.path === '/users'" class="absolute -bottom-1 h-1 w-5 rounded-full bg-indigo-500"></span>
      </NuxtLink>
    </nav>
  </div>
</template>

<script setup>
import { useAuth } from '../composables/useAuth'
import { useToast } from '../composables/useToast'
import { useToko } from '../composables/useToko'

const route = useRoute()
const runtimeConfig = useRuntimeConfig()
const apiBaseUrl = runtimeConfig.public.apiBaseUrl.replace(/\/+$/, '')
const { show: showToast } = useToast()
const { user, token, role, isAdmin, isKasir, clearAuth } = useAuth()
const { namaToko, logoToko, loadSetting } = useToko()

const isMobileSidebarOpen = ref(false)
const isDesktopSidebarOpen = ref(true)

const toggleDesktopSidebar = () => {
  isDesktopSidebarOpen.value = !isDesktopSidebarOpen.value
  if (typeof window !== 'undefined') {
    localStorage.setItem('pos_desktop_sidebar_open', String(isDesktopSidebarOpen.value))
  }
}

const handleGlobalKeydown = (e) => {
  if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
    e.preventDefault()
    toggleDesktopSidebar()
  }
}

onMounted(() => {
  loadSetting()
  if (typeof window !== 'undefined') {
    const saved = localStorage.getItem('pos_desktop_sidebar_open')
    if (saved !== null) {
      isDesktopSidebarOpen.value = saved === 'true'
    }
    window.addEventListener('keydown', handleGlobalKeydown)
  }
})

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('keydown', handleGlobalKeydown)
  }
})

const userName = computed(() => {
  return user.value?.name || user.value?.email || 'Pengguna'
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
