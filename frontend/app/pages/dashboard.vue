<template>
  <div class="space-y-6">
    <!-- Header Selamat Datang & Action Bar -->
    <div class="rounded-2xl sm:rounded-[26px] border border-slate-200/80 bg-white/90 backdrop-blur-sm p-4 sm:p-6 shadow-sm">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-1.5">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 border border-indigo-100 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
              <span class="h-2 w-2 rounded-full bg-indigo-500 animate-pulse"></span>
              Admin Dashboard
            </span>
            <span class="text-xs text-slate-400 font-medium">{{ currentDateText }}</span>
          </div>
          <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Ringkasan Performa Toko</h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pantau omzet penjualan, volume transaksi, stok kritis, dan produk terlaris secara real-time.</p>
        </div>

        <div class="flex items-center gap-2.5">
          <button
            @click="fetchStats"
            :disabled="isLoading"
            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs sm:text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95 disabled:opacity-50"
          >
            <span :class="isLoading ? 'animate-spin' : ''">🔄</span>
            <span>{{ isLoading ? 'Memperbarui...' : 'Segarkan Data' }}</span>
          </button>

          <NuxtLink
            to="/riwayat"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs sm:text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 active:scale-95"
          >
            <span>Lihat Semua Transaksi</span>
            <span>→</span>
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- 4 Kartu Metrik Utama -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- 1. Omzet Hari Ini -->
      <div class="relative overflow-hidden rounded-2xl sm:rounded-[22px] border border-slate-200/80 bg-white p-4 sm:p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Omzet Hari Ini</span>
          <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 font-bold text-base">
            💰
          </div>
        </div>
        <div class="mt-3">
          <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            {{ formatRupiah(stats.omzet?.hari_ini || 0) }}
          </p>
          <div class="mt-2 flex items-center gap-1.5 text-xs">
            <span
              v-if="omzetHariIniDiff.percent !== 0"
              :class="omzetHariIniDiff.isPositive ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50'"
              class="inline-flex items-center px-1.5 py-0.5 rounded font-bold"
            >
              {{ omzetHariIniDiff.isPositive ? '▲ +' : '▼ -' }}{{ Math.abs(omzetHariIniDiff.percent) }}%
            </span>
            <span class="text-slate-500">vs kemarin ({{ formatRupiah(stats.omzet?.kemarin || 0) }})</span>
          </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-400"></div>
      </div>

      <!-- 2. Omzet Bulan Ini -->
      <div class="relative overflow-hidden rounded-2xl sm:rounded-[22px] border border-slate-200/80 bg-white p-4 sm:p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Omzet Bulan Ini</span>
          <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 font-bold text-base">
            📈
          </div>
        </div>
        <div class="mt-3">
          <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            {{ formatRupiah(stats.omzet?.bulan_ini || 0) }}
          </p>
          <div class="mt-2 flex items-center gap-1.5 text-xs">
            <span
              v-if="omzetBulanIniDiff.percent !== 0"
              :class="omzetBulanIniDiff.isPositive ? 'text-indigo-600 bg-indigo-50' : 'text-slate-600 bg-slate-100'"
              class="inline-flex items-center px-1.5 py-0.5 rounded font-bold"
            >
              {{ omzetBulanIniDiff.isPositive ? '▲ +' : '▼ -' }}{{ Math.abs(omzetBulanIniDiff.percent) }}%
            </span>
            <span class="text-slate-500">vs bln lalu ({{ formatRupiah(stats.omzet?.bulan_lalu || 0) }})</span>
          </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-blue-500"></div>
      </div>

      <!-- 3. Jumlah Transaksi -->
      <div class="relative overflow-hidden rounded-2xl sm:rounded-[22px] border border-slate-200/80 bg-white p-4 sm:p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Jumlah Transaksi</span>
          <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 font-bold text-base">
            🛒
          </div>
        </div>
        <div class="mt-3">
          <div class="flex items-baseline gap-2">
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
              {{ stats.transaksi?.bulan_ini || 0 }}
            </p>
            <span class="text-xs font-semibold text-slate-500">transaksi bln ini</span>
          </div>
          <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
            <span>Hari ini: <strong class="text-slate-800 font-bold">{{ stats.transaksi?.hari_ini || 0 }}</strong></span>
            <span>Total: <strong class="text-slate-800 font-bold">{{ stats.transaksi?.total || 0 }}</strong></span>
          </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 to-orange-400"></div>
      </div>

      <!-- 4. Stok Kritis & Habis -->
      <div class="relative overflow-hidden rounded-2xl sm:rounded-[22px] border border-slate-200/80 bg-white p-4 sm:p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
          <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Stok Kritis (≤ 5)</span>
          <div
            :class="(stats.stok?.total_kritis || 0) > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600'"
            class="flex h-9 w-9 items-center justify-center rounded-xl font-bold text-base"
          >
            {{ (stats.stok?.total_kritis || 0) > 0 ? '⚠️' : '✅' }}
          </div>
        </div>
        <div class="mt-3">
          <div class="flex items-baseline gap-2">
            <p
              :class="(stats.stok?.total_kritis || 0) > 0 ? 'text-rose-600' : 'text-emerald-600'"
              class="text-2xl sm:text-3xl font-extrabold tracking-tight"
            >
              {{ stats.stok?.total_kritis || 0 }}
            </p>
            <span class="text-xs font-semibold text-slate-500">produk menipis</span>
          </div>
          <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
            <span>Total katalog: <strong class="text-slate-800 font-bold">{{ stats.stok?.total_produk || 0 }}</strong> item</span>
            <span
              :class="(stats.stok?.total_kritis || 0) > 0 ? 'text-rose-600 font-semibold' : 'text-emerald-600 font-semibold'"
            >
              {{ (stats.stok?.total_kritis || 0) > 0 ? 'Perlu Restock' : 'Aman' }}
            </span>
          </div>
        </div>
        <div
          :class="(stats.stok?.total_kritis || 0) > 0 ? 'from-rose-500 to-orange-500' : 'from-emerald-500 to-teal-400'"
          class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r"
        ></div>
      </div>
    </div>

    <!-- Bagian Grafik Sederhana Penjualan & Omzet -->
    <div class="rounded-2xl sm:rounded-[26px] border border-slate-200/80 bg-white p-4 sm:p-6 shadow-sm">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
          <div class="flex items-center gap-2">
            <h3 class="text-base sm:text-lg font-bold text-slate-900">Grafik Penjualan & Omzet</h3>
            <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-700 uppercase">
              {{ chartRange === '7d' ? '7 Hari Terakhir' : '30 Hari Terakhir' }}
            </span>
          </div>
          <p class="text-xs text-slate-500 mt-0.5">Visualisasi tren pendapatan harian dan jumlah transaksi kasir.</p>
        </div>

        <!-- Tab Switcher: 7 Hari vs 30 Hari -->
        <div class="inline-flex rounded-xl bg-slate-100 p-1">
          <button
            @click="chartRange = '7d'"
            :class="chartRange === '7d' ? 'bg-white text-indigo-600 font-bold shadow-sm' : 'text-slate-500 hover:text-slate-800 font-medium'"
            class="rounded-lg px-3 py-1.5 text-xs transition"
          >
            7 Hari
          </button>
          <button
            @click="chartRange = '30d'"
            :class="chartRange === '30d' ? 'bg-white text-indigo-600 font-bold shadow-sm' : 'text-slate-500 hover:text-slate-800 font-medium'"
            class="rounded-lg px-3 py-1.5 text-xs transition"
          >
            30 Hari
          </button>
        </div>
      </div>

      <!-- Interactive SVG Bar Chart -->
      <div v-if="activeChartData.length > 0" class="space-y-4">
        <!-- SVG Container -->
        <div class="relative w-full h-64 sm:h-72 select-none">
          <svg class="w-full h-full overflow-visible" :viewBox="`0 0 ${chartSvgWidth} 220`" preserveAspectRatio="none">
            <defs>
              <!-- Gradient Bar Normal -->
              <linearGradient id="barGradient" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#6366f1" stop-opacity="0.95" />
                <stop offset="100%" stop-color="#818cf8" stop-opacity="0.35" />
              </linearGradient>
              <!-- Gradient Bar Active / Hover -->
              <linearGradient id="barActiveGradient" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#4f46e5" stop-opacity="1" />
                <stop offset="100%" stop-color="#6366f1" stop-opacity="0.8" />
              </linearGradient>
            </defs>

            <!-- Horizontal Background Guide Lines -->
            <line x1="0" y1="20" :x2="chartSvgWidth" y2="20" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="4" />
            <line x1="0" y1="75" :x2="chartSvgWidth" y2="75" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="4" />
            <line x1="0" y1="130" :x2="chartSvgWidth" y2="130" stroke="#f1f5f9" stroke-width="1" stroke-dasharray="4" />
            <line x1="0" y1="185" :x2="chartSvgWidth" y2="185" stroke="#e2e8f0" stroke-width="1" />

            <!-- Bars -->
            <g v-for="(item, index) in computedChartBars" :key="item.date">
              <!-- Hit target / hover area -->
              <rect
                :x="item.x"
                y="10"
                :width="item.width"
                height="180"
                fill="transparent"
                class="cursor-pointer"
                @mouseenter="hoveredBar = item"
                @mouseleave="hoveredBar = null"
              />

              <!-- Bar Background Pill (Subtle slot) -->
              <rect
                :x="item.barX"
                y="20"
                :width="item.barWidth"
                height="165"
                rx="6"
                fill="#f8fafc"
              />

              <!-- Actual Value Bar -->
              <rect
                :x="item.barX"
                :y="item.barY"
                :width="item.barWidth"
                :height="item.barHeight"
                rx="6"
                :fill="hoveredBar?.date === item.date ? 'url(#barActiveGradient)' : 'url(#barGradient)'"
                class="transition-all duration-300 cursor-pointer"
                @mouseenter="hoveredBar = item"
                @mouseleave="hoveredBar = null"
              />

              <!-- Transaction indicator dot if trx > 0 -->
              <circle
                v-if="item.transaksi > 0"
                :cx="item.barX + (item.barWidth / 2)"
                :cy="Math.max(25, item.barY - 8)"
                r="3.5"
                fill="#f59e0b"
              />

              <!-- X-Axis Label -->
              <text
                :x="item.barX + (item.barWidth / 2)"
                y="204"
                text-anchor="middle"
                class="text-[10px] font-medium"
                :fill="hoveredBar?.date === item.date ? '#4f46e5' : '#64748b'"
                :font-weight="hoveredBar?.date === item.date ? 'bold' : 'normal'"
              >
                {{ chartRange === '7d' ? (item.hari ? item.hari.slice(0, 3) : item.label) : item.label }}
              </text>
            </g>
          </svg>

          <!-- Hover Tooltip Overlay -->
          <div
            v-if="hoveredBar"
            class="pointer-events-none absolute -top-3 z-30 transform -translate-x-1/2 rounded-xl border border-slate-700 bg-slate-900 px-3 py-2 text-white shadow-xl"
            :style="{ left: `${hoveredBar.tooltipPercent}%` }"
          >
            <p class="text-[11px] font-medium text-slate-300">
              {{ hoveredBar.hari ? `${hoveredBar.hari}, ` : '' }}{{ hoveredBar.label }}
            </p>
            <p class="text-xs font-bold text-emerald-400 mt-0.5">
              {{ formatRupiah(hoveredBar.omzet) }}
            </p>
            <p class="text-[10px] text-amber-300 font-semibold mt-0.5">
              🛒 {{ hoveredBar.transaksi }} Transaksi
            </p>
          </div>
        </div>

        <!-- Legend & Stats Summary -->
        <div class="flex flex-wrap items-center justify-between border-t border-slate-100 pt-3 text-xs text-slate-500 gap-2">
          <div class="flex items-center gap-4">
            <span class="flex items-center gap-1.5">
              <span class="h-3 w-3 rounded bg-indigo-500 inline-block"></span>
              <span>Nominal Omzet</span>
            </span>
            <span class="flex items-center gap-1.5">
              <span class="h-2.5 w-2.5 rounded-full bg-amber-500 inline-block"></span>
              <span>Ada Transaksi POS</span>
            </span>
          </div>

          <div class="flex items-center gap-3">
            <span>Penjualan Tertinggi: <strong class="text-slate-800 font-bold">{{ formatRupiah(peakDay.omzet) }}</strong> ({{ peakDay.label || '-' }})</span>
          </div>
        </div>
      </div>

      <!-- Empty / Loading State for Chart -->
      <div v-else class="py-12 text-center text-slate-400">
        <p class="text-sm">Belum ada riwayat transaksi untuk divisualisasikan.</p>
      </div>
    </div>

    <!-- 2 Kolom: Produk Terlaris & Stok Kritis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Kolom 1: 🏆 Produk Terlaris -->
      <div class="rounded-2xl sm:rounded-[26px] border border-slate-200/80 bg-white p-4 sm:p-6 shadow-sm flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
              <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-50 text-amber-600 font-bold">
                🏆
              </span>
              <div>
                <h3 class="text-base font-bold text-slate-900">5 Produk Terlaris</h3>
                <p class="text-xs text-slate-500">Peringkat produk berdasarkan total unit yang terjual.</p>
              </div>
            </div>
            <span class="text-xs font-semibold text-slate-400">
              Total {{ stats.total_item_terjual || 0 }} unit terjual
            </span>
          </div>

          <!-- List Produk Terlaris -->
          <div v-if="(stats.produk_terlaris || []).length > 0" class="space-y-3.5">
            <div
              v-for="(item, idx) in stats.produk_terlaris"
              :key="item.id || idx"
              class="rounded-xl border border-slate-100 bg-slate-50/70 p-3 transition hover:bg-slate-50"
            >
              <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-3 min-w-0">
                  <!-- Rank Badge -->
                  <div
                    :class="getRankBadgeClass(idx)"
                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-xs font-bold"
                  >
                    #{{ idx + 1 }}
                  </div>
                  <div class="truncate">
                    <p class="text-xs sm:text-sm font-bold text-slate-800 truncate">{{ item.nama_barang }}</p>
                    <p class="text-[11px] text-slate-400">Kode: {{ item.kode_barang || '-' }}</p>
                  </div>
                </div>

                <div class="text-right shrink-0">
                  <p class="text-xs sm:text-sm font-extrabold text-indigo-600">
                    {{ item.total_terjual }} <span class="text-[11px] font-normal text-slate-500">terjual</span>
                  </p>
                  <p class="text-[11px] font-semibold text-slate-500">
                    {{ formatRupiah(item.total_omzet) }}
                  </p>
                </div>
              </div>

              <!-- Progress Bar Porsi Penjualan -->
              <div class="mt-2.5 flex items-center gap-2">
                <div class="h-1.5 flex-1 rounded-full bg-slate-200 overflow-hidden">
                  <div
                    class="h-full rounded-full bg-indigo-600 transition-all duration-500"
                    :style="{ width: `${Math.min(100, Math.max(5, item.persentase || 0))}%` }"
                  ></div>
                </div>
                <span class="text-[10px] font-bold text-slate-500 w-9 text-right">
                  {{ item.persentase || 0 }}%
                </span>
              </div>
            </div>
          </div>

          <!-- Empty State Produk Terlaris -->
          <div v-else class="py-10 text-center text-slate-400">
            <p class="text-3xl mb-1">🛒</p>
            <p class="text-xs font-medium">Belum ada data barang yang terjual dari transaksi kasir.</p>
          </div>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
          <span>Dihitung dari semua transaksi berstatus <strong>selesai</strong></span>
          <NuxtLink to="/riwayat" class="font-bold text-indigo-600 hover:text-indigo-700">Detail &rarr;</NuxtLink>
        </div>
      </div>

      <!-- Kolom 2: ⚠️ Peringatan Stok Kritis -->
      <div class="rounded-2xl sm:rounded-[26px] border border-slate-200/80 bg-white p-4 sm:p-6 shadow-sm flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
              <span
                :class="(stats.stok?.total_kritis || 0) > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600'"
                class="flex h-8 w-8 items-center justify-center rounded-xl font-bold"
              >
                {{ (stats.stok?.total_kritis || 0) > 0 ? '⚠️' : '✅' }}
              </span>
              <div>
                <h3 class="text-base font-bold text-slate-900">Peringatan Stok Kritis</h3>
                <p class="text-xs text-slate-500">Barang dengan sisa stok ≤ 5 unit yang memerlukan restock.</p>
              </div>
            </div>
            <span
              :class="(stats.stok?.total_kritis || 0) > 0 ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200'"
              class="rounded-full border px-2 py-0.5 text-[10px] font-bold"
            >
              {{ stats.stok?.total_kritis || 0 }} Item
            </span>
          </div>

          <!-- List Stok Kritis -->
          <div v-if="(stats.stok?.kritis_list || []).length > 0" class="space-y-2.5">
            <div
              v-for="item in stats.stok.kritis_list"
              :key="item.id"
              class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/70 p-3 transition hover:bg-slate-50"
            >
              <div class="min-w-0 pr-2">
                <div class="flex items-center gap-2">
                  <p class="text-xs sm:text-sm font-bold text-slate-800 truncate">{{ item.nama_barang }}</p>
                  <span
                    :class="item.stok === 0 ? 'bg-rose-500 text-white' : 'bg-amber-100 text-amber-800'"
                    class="rounded px-1.5 py-0.2 text-[9px] font-extrabold uppercase shrink-0"
                  >
                    {{ item.stok === 0 ? 'Habis' : 'Kritis' }}
                  </span>
                </div>
                <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-400">
                  <span>Kode: {{ item.kode_barang || '-' }}</span>
                  <span>•</span>
                  <span>Kategori: {{ item.kategori || 'Umum' }}</span>
                </div>
              </div>

              <div class="text-right shrink-0">
                <p
                  :class="item.stok === 0 ? 'text-rose-600' : 'text-amber-600'"
                  class="text-xs sm:text-sm font-black"
                >
                  Sisa {{ item.stok }}
                </p>
                <p class="text-[11px] text-slate-500 font-medium">
                  {{ formatRupiah(item.harga) }}
                </p>
              </div>
            </div>
          </div>

          <!-- Empty State Stok Kritis (Semua stok cukup) -->
          <div v-else class="py-10 text-center text-slate-400">
            <p class="text-3xl mb-1">🎉</p>
            <p class="text-xs font-semibold text-emerald-600">Semua stok barang dalam kondisi aman!</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Tidak ada barang dengan stok di bawah atau sama dengan 5.</p>
          </div>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
          <span>Stok barang dikelola oleh <strong>Staff Kasir</strong></span>
          <span class="text-slate-400 text-[11px]">Ambang batas: ≤ 5 unit</span>
        </div>
      </div>
    </div>

    <!-- Ringkasan Tambahan: Metode Pembayaran -->
    <div class="rounded-2xl sm:rounded-[26px] border border-slate-200/80 bg-white p-4 sm:p-6 shadow-sm">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="text-base font-bold text-slate-900">Distribusi Metode Pembayaran</h3>
          <p class="text-xs text-slate-500">Pilihan metode bayar pelanggan (Bulan Ini).</p>
        </div>
      </div>

      <div v-if="(stats.metode_pembayaran || []).length > 0" class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
        <div
          v-for="metode in stats.metode_pembayaran"
          :key="metode.metode_pembayaran"
          class="rounded-xl border border-slate-100 bg-slate-50/70 p-3.5 flex items-center justify-between"
        >
          <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 text-lg">
              {{ getMetodeIcon(metode.metode_pembayaran) }}
            </div>
            <div>
              <p class="text-xs font-bold uppercase text-slate-800 tracking-wide">
                {{ formatMetodeLabel(metode.metode_pembayaran) }}
              </p>
              <p class="text-[11px] text-slate-500">{{ metode.count }} kali digunakan</p>
            </div>
          </div>
          <div class="text-right">
            <p class="text-xs sm:text-sm font-bold text-slate-900">{{ formatRupiah(metode.total) }}</p>
          </div>
        </div>
      </div>
      <div v-else class="py-4 text-center text-xs text-slate-400">
        Belum ada transaksi bulan ini untuk metode pembayaran.
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuth } from '../composables/useAuth'
import { useToast } from '../composables/useToast'

const { token, isAdmin } = useAuth()
const { show: showToast } = useToast()
const runtimeConfig = useRuntimeConfig()
const apiBaseUrl = runtimeConfig.public.apiBaseUrl.replace(/\/+$/, '')

const isLoading = ref(false)
const chartRange = ref('7d')
const hoveredBar = ref(null)

const stats = ref({
  omzet: {
    hari_ini: 0,
    kemarin: 0,
    bulan_ini: 0,
    bulan_lalu: 0,
    total_omzet: 0,
  },
  transaksi: {
    hari_ini: 0,
    bulan_ini: 0,
    total: 0,
  },
  stok: {
    kritis_list: [],
    total_kritis: 0,
    total_produk: 0,
  },
  produk_terlaris: [],
  total_item_terjual: 0,
  grafik: {
    tujuh_hari: [],
    tiga_puluh_hari: [],
  },
  metode_pembayaran: [],
})

const currentDateText = computed(() => {
  return new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
})

const formatRupiah = (val) => {
  const num = Number(val) || 0
  return 'Rp ' + num.toLocaleString('id-ID')
}

// Perhitungan pertumbuhan omzet
const omzetHariIniDiff = computed(() => {
  const hariIni = stats.value.omzet?.hari_ini || 0
  const kemarin = stats.value.omzet?.kemarin || 0
  if (kemarin === 0) return { percent: hariIni > 0 ? 100 : 0, isPositive: true }
  const diff = Math.round(((hariIni - kemarin) / kemarin) * 100)
  return { percent: diff, isPositive: diff >= 0 }
})

const omzetBulanIniDiff = computed(() => {
  const blnIni = stats.value.omzet?.bulan_ini || 0
  const blnLalu = stats.value.omzet?.bulan_lalu || 0
  if (blnLalu === 0) return { percent: blnIni > 0 ? 100 : 0, isPositive: true }
  const diff = Math.round(((blnIni - blnLalu) / blnLalu) * 100)
  return { percent: diff, isPositive: diff >= 0 }
})

// Data grafik aktif berdasarkan tab
const activeChartData = computed(() => {
  if (chartRange.value === '7d') {
    return stats.value.grafik?.tujuh_hari || []
  }
  return stats.value.grafik?.tiga_puluh_hari || []
})

const chartSvgWidth = computed(() => {
  return chartRange.value === '7d' ? 700 : 900
})

// Perhitungan koordinat Bar SVG
const computedChartBars = computed(() => {
  const data = activeChartData.value
  if (!data || data.length === 0) return []

  const maxVal = Math.max(...data.map(d => d.omzet || 0), 1)
  const totalItems = data.length
  const totalSvgWidth = chartSvgWidth.value
  const slotWidth = totalSvgWidth / totalItems
  const barWidth = chartRange.value === '7d' ? Math.min(46, slotWidth * 0.55) : Math.min(18, slotWidth * 0.65)
  const maxHeight = 160 // px

  return data.map((item, i) => {
    const ratio = (item.omzet || 0) / maxVal
    const barHeight = Math.max(item.omzet > 0 ? 8 : 4, ratio * maxHeight)
    const slotX = i * slotWidth
    const barX = slotX + (slotWidth - barWidth) / 2
    const barY = 185 - barHeight
    const tooltipPercent = ((slotX + slotWidth / 2) / totalSvgWidth) * 100

    return {
      ...item,
      x: slotX,
      width: slotWidth,
      barX,
      barY,
      barWidth,
      barHeight,
      tooltipPercent
    }
  })
})

const peakDay = computed(() => {
  const data = activeChartData.value
  if (!data || data.length === 0) return { omzet: 0, label: '-' }
  return [...data].sort((a, b) => (b.omzet || 0) - (a.omzet || 0))[0] || { omzet: 0, label: '-' }
})

const getRankBadgeClass = (index) => {
  switch (index) {
    case 0: return 'bg-amber-100 text-amber-700 border border-amber-200 shadow-sm'
    case 1: return 'bg-slate-200 text-slate-700 border border-slate-300'
    case 2: return 'bg-amber-600/10 text-amber-800 border border-amber-600/20'
    default: return 'bg-slate-100 text-slate-500'
  }
}

const getMetodeIcon = (metode) => {
  switch (String(metode).toLowerCase()) {
    case 'tunai': return '💵'
    case 'qris': return '📱'
    case 'midtrans': return '💳'
    case 'transfer': return '🏦'
    default: return '💳'
  }
}

const formatMetodeLabel = (metode) => {
  switch (String(metode).toLowerCase()) {
    case 'tunai': return 'Tunai (Cash)'
    case 'qris': return 'QRIS Digital'
    case 'midtrans': return 'Midtrans Gateway'
    case 'transfer': return 'Transfer Bank'
    default: return metode || 'Lainnya'
  }
}

const fetchStats = async () => {
  isLoading.value = true
  try {
    const res = await $fetch(`${apiBaseUrl}/dashboard/stats`, {
      headers: { Authorization: `Bearer ${token.value}` }
    })
    stats.value = res
  } catch (err) {
    console.error('Error fetching dashboard stats:', err)
    showToast(err.data?.message || 'Gagal mengambil data statistik dashboard.', 'error')
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
    showToast('Akses ditolak: Menu Dashboard hanya dapat diakses oleh Administrator.', 'warning')
    navigateTo('/kasir')
    return
  }
  fetchStats()
})
</script>
