<template>
  <div>
    <!-- Statistik Ringkas Kasir -->
    <div class="mb-6 grid gap-4 sm:grid-cols-3">
      <div class="rounded-[22px] border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-sm font-medium text-slate-500">Status Produk</p>
        <div class="mt-3 flex items-end justify-between">
          <div>
            <p class="text-2xl font-bold text-slate-900">{{ daftarBarang.length }}</p>
            <p class="text-xs text-slate-500">Item terdaftar</p>
          </div>
          <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
        </div>
      </div>
      <div class="rounded-[22px] border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-sm font-medium text-slate-500">Isi Keranjang</p>
        <div class="mt-3 flex items-end justify-between">
          <div>
            <p class="text-2xl font-bold text-slate-900">{{ totalItemQty }}</p>
            <p class="text-xs text-slate-500">{{ keranjang.length }} jenis barang</p>
          </div>
          <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Transaksi Aktif</span>
        </div>
      </div>
      <div class="rounded-[22px] border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-sm font-medium text-slate-500">Total Tagihan Bersih</p>
        <div class="mt-3 flex items-end justify-between">
          <div>
            <p class="text-2xl font-bold text-indigo-600">Rp {{ totalTagihan.toLocaleString() }}</p>
            <p class="text-xs text-slate-500">
              {{ diskonNominal > 0 ? `Hemat Rp ${diskonNominal.toLocaleString()}` : 'Belum ada diskon' }}
            </p>
          </div>
          <span
            :class="statusBayarValid ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'"
            class="rounded-full px-3 py-1 text-xs font-semibold"
          >
            {{ statusBayarValid ? 'Siap Bayar' : 'Menunggu' }}
          </span>
        </div>
      </div>
    </div>

    <!-- Grid Utama: Katalog Produk (Kiri) & Keranjang Kasir (Kanan) -->
    <div class="grid gap-6 xl:grid-cols-[1.45fr_1.05fr]">
      <!-- Katalog Produk -->
      <section class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h2 class="text-xl font-bold text-slate-900">Katalog Produk</h2>
            <p class="text-xs text-slate-500">Scan barcode atau klik produk untuk memasukkan ke keranjang</p>
          </div>
          <div class="rounded-full bg-indigo-50 px-3.5 py-1 text-xs font-semibold text-indigo-700">
            {{ tampilBarang.length }} item tampil
          </div>
        </div>

        <!-- Input Barcode Scanner Cepat & Filter Kategori -->
        <div class="mt-4 grid gap-3 sm:grid-cols-2">
          <div>
            <div class="relative">
              <input
                ref="searchInputRef"
                v-model="searchKeyword"
                type="text"
                placeholder="Scan barcode / cari nama lalu Enter (F2)..."
                @keydown.enter.prevent="handleBarcodeScan"
                class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 pl-3 pr-16 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
              />
              <span class="pointer-events-none absolute right-2.5 top-2.5 rounded bg-slate-200 px-1.5 py-0.5 text-[10px] font-bold text-slate-600">
                ↵ Enter
              </span>
            </div>
            <p class="mt-1 text-[11px] text-slate-400">Scanner barcode otomatis menekan Enter.</p>
          </div>
          <div>
            <select
              v-model="selectedCategory"
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
            >
              <option value="all">Semua Kategori</option>
              <option v-for="c in categories.filter(x => x !== 'all')" :key="c" :value="c">{{ c }}</option>
            </select>
          </div>
        </div>

        <!-- Grid Produk -->
        <div v-if="tampilBarang.length === 0" class="mt-8 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">
          Produk tidak ditemukan untuk pencarian "{{ searchKeyword }}".
        </div>

        <div v-else class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
          <button
            v-for="b in tampilBarang"
            :key="b.id"
            @click="tambahKeKeranjang(b)"
            :disabled="b.stok <= 0"
            class="group rounded-[20px] border border-slate-200 bg-slate-50/70 p-4 text-left transition hover:-translate-y-0.5 hover:border-indigo-400 hover:bg-white hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0"
          >
            <div class="flex items-start justify-between gap-2">
              <div>
                <span v-if="b.kategori" class="inline-block rounded bg-slate-200/80 px-1.5 py-0.5 text-[10px] font-medium text-slate-600 mb-1">
                  {{ b.kategori }}
                </span>
                <div class="font-bold text-slate-900 group-hover:text-indigo-600 transition">{{ b.nama_barang }}</div>
                <div class="text-xs text-slate-500 font-mono">Kode: {{ b.kode_barang }}</div>
              </div>
              <span
                :class="b.stok <= 0 ? 'bg-rose-100 text-rose-700' : b.stok <= 5 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'"
                class="rounded-full px-2 py-0.5 text-[10px] font-bold whitespace-nowrap"
              >
                {{ b.stok <= 0 ? 'Habis' : b.stok + ' stok' }}
              </span>
            </div>

            <div class="mt-4 flex items-end justify-between border-t border-slate-200/60 pt-3">
              <div>
                <div class="text-base font-bold text-slate-900">Rp {{ Number(b.harga).toLocaleString() }}</div>
              </div>
              <span class="rounded-lg bg-indigo-600 px-2.5 py-1 text-xs font-semibold text-white shadow-sm transition group-hover:bg-indigo-700">
                + Tambah
              </span>
            </div>
          </button>
        </div>
      </section>

      <!-- Sidebar Kasir: Keranjang, Pelanggan, & Pembayaran Riil -->
      <aside class="space-y-4">
        <!-- Rincian Keranjang -->
        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
          <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
              <h3 class="text-lg font-bold text-slate-900">Keranjang Belanja</h3>
              <p class="text-xs text-slate-500">Ketik angka kuantitas langsung untuk partai besar</p>
            </div>
            <button
              v-if="keranjang.length > 0"
              @click="kosongkanKeranjang"
              class="text-xs font-semibold text-rose-600 hover:text-rose-700"
            >
              Kosongkan
            </button>
          </div>

          <div v-if="keranjang.length === 0" class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-sm text-slate-500">
            Keranjang masih kosong.<br /><span class="text-xs text-slate-400">Scan barcode atau klik produk di sebelah kiri.</span>
          </div>

          <div v-else class="mt-4 max-h-64 space-y-3 overflow-y-auto pr-1">
            <div
              v-for="item in keranjang"
              :key="item.id"
              class="rounded-2xl border border-slate-100 bg-slate-50 p-3"
            >
              <div class="flex items-start justify-between gap-2">
                <div class="flex-1">
                  <div class="font-bold text-slate-800 text-sm">{{ item.nama_barang }}</div>
                  <div class="text-xs text-slate-500">@ Rp {{ item.harga.toLocaleString() }}</div>
                </div>
                <div class="text-right">
                  <div class="font-bold text-slate-900 text-sm">Rp {{ (item.harga * item.qty).toLocaleString() }}</div>
                </div>
              </div>

              <div class="mt-3 flex items-center justify-between border-t border-slate-200/50 pt-2">
                <button
                  @click="hapusItem(item)"
                  class="text-xs font-semibold text-rose-500 hover:text-rose-600"
                >
                  Hapus
                </button>
                <div class="flex items-center gap-1.5">
                  <button
                    @click="kurangQty(item)"
                    class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-200 font-bold text-slate-700 hover:bg-slate-300 active:scale-95"
                  >
                    −
                  </button>
                  <!-- Input Qty yang bisa diketik langsung -->
                  <input
                    type="number"
                    min="1"
                    :max="item.stok"
                    v-model.number="item.qty"
                    @change="validasiInputQty(item)"
                    class="w-14 rounded-lg border border-slate-300 bg-white py-1 text-center text-xs font-bold text-slate-800 outline-none focus:border-indigo-500"
                  />
                  <button
                    @click="tambahQty(item)"
                    class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-600 font-bold text-white hover:bg-indigo-500 active:scale-95"
                  >
                    +
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Data Pelanggan & Diskon Penjualan -->
        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm space-y-3">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="mb-1 block text-xs font-bold text-slate-700">Nama Pelanggan</label>
              <input
                v-model="namaPelanggan"
                type="text"
                placeholder="Pelanggan Umum"
                class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-800 outline-none focus:border-indigo-500 focus:bg-white"
              />
            </div>
            <div>
              <label class="mb-1 block text-xs font-bold text-slate-700">Potongan Diskon (Rp)</label>
              <input
                v-model.number="diskonNominal"
                type="number"
                min="0"
                placeholder="0"
                class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-800 outline-none focus:border-indigo-500 focus:bg-white"
              />
            </div>
          </div>
        </div>

        <!-- Panel Pembayaran Terstruktur (Anti-Gimmick) -->
        <div class="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
          <div class="border-b border-slate-100 pb-3 space-y-1">
            <div class="flex justify-between text-xs text-slate-500">
              <span>Subtotal Produk</span>
              <span>Rp {{ subtotalBelanja.toLocaleString() }}</span>
            </div>
            <div v-if="diskonNominal > 0" class="flex justify-between text-xs text-rose-600 font-semibold">
              <span>Potongan Diskon</span>
              <span>- Rp {{ Number(diskonNominal).toLocaleString() }}</span>
            </div>
            <div class="flex justify-between items-baseline pt-1">
              <span class="text-sm font-bold text-slate-900">Total Tagihan</span>
              <span class="text-2xl font-extrabold text-indigo-600">Rp {{ totalTagihan.toLocaleString() }}</span>
            </div>
          </div>

          <!-- Pilihan Metode Pembayaran -->
          <div class="mt-4 space-y-4">
            <div>
              <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600">Pilih Metode Pembayaran</label>
              <div class="grid grid-cols-2 gap-2">
                <button
                  type="button"
                  @click="pilihMetode('tunai')"
                  :class="paymentMethod === 'tunai' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 font-bold ring-2 ring-indigo-200' : 'border-slate-200 bg-slate-50 text-slate-700 font-medium'"
                  class="flex items-center justify-center gap-1.5 rounded-xl border p-3 text-xs transition"
                >
                  <span>💵 Tunai (Cash)</span>
                </button>
                <button
                  type="button"
                  @click="pilihMetode('midtrans')"
                  :class="paymentMethod === 'midtrans' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 font-bold ring-2 ring-indigo-200' : 'border-slate-200 bg-slate-50 text-slate-700 font-medium'"
                  class="flex items-center justify-center gap-1.5 rounded-xl border p-3 text-xs transition"
                >
                  <span class="text-amber-500 font-extrabold text-sm">⚡</span>
                  <span>Midtrans Gateway</span>
                </button>
              </div>
            </div>

            <!-- DETAIL METODE: TUNAI -->
            <div v-if="paymentMethod === 'tunai'" class="space-y-3 rounded-2xl bg-slate-50 p-3.5 border border-slate-200">
              <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-700">Uang Tunai Diterima</label>
                <button
                  v-if="totalTagihan > 0"
                  type="button"
                  @click="bayar = totalTagihan"
                  class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700"
                >
                  Uang Pas
                </button>
              </div>
              <input
                v-model.number="bayar"
                type="number"
                placeholder="0"
                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-lg font-bold text-slate-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
              />

              <!-- Tombol Nominal Cepat -->
              <div class="flex flex-wrap gap-1.5">
                <button
                  v-for="nom in [10000, 20000, 50000, 100000, 200000]"
                  :key="nom"
                  type="button"
                  @click="bayar = nom"
                  class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-[11px] font-semibold text-slate-700 hover:bg-slate-100"
                >
                  Rp {{ (nom / 1000).toLocaleString() }}k
                </button>
              </div>

              <!-- Display Kembalian -->
              <div class="rounded-xl bg-white p-2.5 border border-slate-200 flex justify-between items-center text-xs">
                <span class="font-medium text-slate-600">Kembalian:</span>
                <span :class="kembalian < 0 ? 'text-rose-600 font-bold' : 'text-emerald-600 font-bold text-sm'">
                  {{ kembalian < 0 ? 'Kurang Rp ' + Math.abs(kembalian).toLocaleString() : 'Rp ' + kembalian.toLocaleString() }}
                </span>
              </div>
            </div>

            <!-- DETAIL METODE: MIDTRANS SNAP GATEWAY -->
            <div v-else-if="paymentMethod === 'midtrans'" class="space-y-3 rounded-2xl bg-indigo-50/70 p-3.5 border border-indigo-200">
              <div class="flex items-center justify-between border-b border-indigo-100 pb-2 text-xs">
                <div class="flex items-center gap-2">
                  <span class="font-bold text-indigo-950 flex items-center gap-1.5">
                    <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Midtrans Snap Gateway
                  </span>
                  <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-[10px] font-bold text-indigo-700">Sandbox</span>
                </div>
                <span class="text-[11px] font-medium text-emerald-700 font-semibold">Otomatis / Real-Time</span>
              </div>

              <p class="text-xs text-slate-600 leading-relaxed">
                Pelanggan membayar via <strong>QRIS (GoPay, Dana, ShopeePay, OVO)</strong>, <strong>Virtual Account (BCA, Mandiri, BRI, BNI)</strong>, atau Kartu Kredit/Debit melalui popup resmi Midtrans.
              </p>

              <div class="flex flex-wrap gap-1.5 pt-0.5">
                <span class="rounded-md bg-white px-2 py-0.5 text-[10px] font-bold text-slate-700 border border-slate-200 shadow-xs">QRIS Dinamis</span>
                <span class="rounded-md bg-white px-2 py-0.5 text-[10px] font-bold text-slate-700 border border-slate-200 shadow-xs">BCA VA</span>
                <span class="rounded-md bg-white px-2 py-0.5 text-[10px] font-bold text-slate-700 border border-slate-200 shadow-xs">Mandiri VA</span>
                <span class="rounded-md bg-white px-2 py-0.5 text-[10px] font-bold text-slate-700 border border-slate-200 shadow-xs">BRI / BNI VA</span>
                <span class="rounded-md bg-white px-2 py-0.5 text-[10px] font-bold text-slate-700 border border-slate-200 shadow-xs">GoPay / ShopeePay</span>
              </div>

              <div class="rounded-xl bg-white p-2.5 border border-indigo-100 flex justify-between items-center text-xs">
                <span class="text-slate-500">Total Ditagihkan:</span>
                <span class="font-extrabold text-indigo-700 text-sm">Rp {{ totalTagihan.toLocaleString() }} (Pas)</span>
              </div>
            </div>
          </div>

          <!-- Tombol Eksekusi Pembayaran -->
          <button
            @click="bukaKonfirmasi"
            :disabled="isSubmitting || !statusBayarValid || totalTagihan === 0"
            class="mt-4 w-full rounded-xl bg-emerald-600 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-500 active:scale-[0.99] disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-400"
          >
            {{ isSubmitting ? 'Memproses Transaksi...' : (paymentMethod === 'midtrans' ? '⚡ Buka Pembayaran Midtrans Snap' : 'Proses Pembayaran & Cetak Struk') }}
          </button>
        </div>
      </aside>
    </div>

    <!-- Modal Konfirmasi Pembayaran -->
    <div v-if="showKonfirmasi" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" @click.self="showKonfirmasi = false">
      <div class="w-full max-w-lg rounded-[28px] bg-white p-6 shadow-2xl">
        <h3 class="text-xl font-bold text-slate-900">Konfirmasi Transaksi Kasir</h3>
        <p class="mt-1 text-xs text-slate-500">Periksa detail pesanan pelanggan sebelum menyimpan transaksi.</p>

        <div class="mt-4 space-y-1.5 text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-200">
          <div class="flex justify-between">
            <span class="font-semibold">Pelanggan:</span>
            <span class="font-bold text-slate-900">{{ namaPelanggan || 'Pelanggan Umum' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="font-semibold">Metode:</span>
            <span class="font-bold uppercase text-indigo-700">
              {{ paymentMethod === 'midtrans' ? 'Midtrans (QRIS/VA)' : 'Tunai' }}
            </span>
          </div>
        </div>

        <div class="mt-3 max-h-48 space-y-2 overflow-y-auto border-y border-slate-100 py-2.5 text-xs">
          <div v-for="item in keranjang" :key="item.id" class="flex justify-between">
            <span class="text-slate-700">{{ item.nama_barang }} × {{ item.qty }}</span>
            <span class="font-semibold text-slate-900">Rp {{ (item.harga * item.qty).toLocaleString() }}</span>
          </div>
        </div>

        <div class="mt-3 space-y-1.5 text-xs text-slate-700">
          <div class="flex justify-between">
            <span>Subtotal</span>
            <span>Rp {{ subtotalBelanja.toLocaleString() }}</span>
          </div>
          <div v-if="diskonNominal > 0" class="flex justify-between text-rose-600 font-semibold">
            <span>Diskon</span>
            <span>- Rp {{ Number(diskonNominal).toLocaleString() }}</span>
          </div>
          <div class="flex justify-between font-bold text-sm text-slate-900 pt-1 border-t border-slate-100">
            <span>Total Bayar</span>
            <span>Rp {{ totalTagihan.toLocaleString() }}</span>
          </div>
          <div class="flex justify-between">
            <span>Nominal Diterima</span>
            <span class="font-bold text-slate-900">Rp {{ bayar.toLocaleString() }}</span>
          </div>
          <div class="flex justify-between text-emerald-600 font-bold text-sm">
            <span>Kembalian</span>
            <span>Rp {{ kembalian.toLocaleString() }}</span>
          </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
          <button
            @click="showKonfirmasi = false"
            :disabled="isSubmitting"
            class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100"
          >
            Batal (Esc)
          </button>
          <button
            @click="prosesTransaksi"
            :disabled="isSubmitting"
            class="rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white hover:bg-emerald-500"
          >
            {{ isSubmitting ? 'Menyimpan...' : (paymentMethod === 'midtrans' ? 'Buka Midtrans Snap' : 'Selesaikan & Cetak') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Monitoring & Simulasi Pembayaran Midtrans Snap -->
    <div v-if="showSnapModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
      <div class="w-full max-w-md rounded-[28px] bg-white p-6 shadow-2xl border border-indigo-100 animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2.5">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 text-lg font-bold">
              ⚡
            </div>
            <div>
              <h3 class="text-base font-bold text-slate-900">Menunggu Pembayaran</h3>
              <p class="text-[11px] font-mono text-slate-400">{{ activeSnapNoNota }}</p>
            </div>
          </div>
          <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
            <span class="h-2 w-2 rounded-full bg-amber-500 animate-ping"></span>
            Pending
          </span>
        </div>

        <div class="my-5 text-center">
          <p class="text-xs text-slate-500">Total Tagihan Pelanggan:</p>
          <p class="mt-1 text-3xl font-extrabold text-indigo-700">Rp {{ activeSnapTotal.toLocaleString() }}</p>
          <p class="mt-2 text-xs text-slate-500">
            Popup pembayaran Midtrans Snap sedang aktif. Sistem secara otomatis mendeteksi ketika pelanggan selesai membayar.
          </p>
        </div>

        <!-- Indikator Polling Live -->
        <div class="rounded-2xl bg-indigo-50/70 p-3 border border-indigo-100 text-xs text-indigo-900 flex items-center gap-3">
          <div class="h-4 w-4 animate-spin rounded-full border-2 border-indigo-600 border-t-transparent"></div>
          <div class="flex-1">
            <p class="font-semibold text-[11px]">Real-Time Auto Polling</p>
            <p class="text-[10px] text-indigo-600">Mengecek konfirmasi bank/e-wallet setiap 2.5 detik...</p>
          </div>
        </div>

        <!-- Tombol Aksi Kasir -->
        <div class="mt-5 space-y-2">
          <!-- Tombol Simulasi Sukses (Sangat berguna untuk pengujian sandbox / demo sebelum punya key) -->
          <button
            type="button"
            @click="simulasikanBayarSukses"
            :disabled="isSimulating"
            class="w-full flex items-center justify-center gap-2 rounded-xl bg-emerald-600 py-3 text-xs font-bold text-white shadow-sm hover:bg-emerald-500 transition active:scale-[0.99] disabled:opacity-50"
          >
            <span>✓</span>
            <span>{{ isSimulating ? 'Memproses Simulasi...' : 'Simulasi Pembayaran Berhasil (Sandbox Demo)' }}</span>
          </button>

          <div class="grid grid-cols-2 gap-2 pt-1">
            <button
              type="button"
              @click="bukaUlangSnap"
              class="rounded-xl border border-indigo-200 bg-white py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-50 transition"
            >
              Buka Ulang Snap
            </button>
            <button
              type="button"
              @click="batalkanSnapTransaksi"
              :disabled="isCancelling"
              class="rounded-xl border border-rose-200 bg-rose-50 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100 transition disabled:opacity-50"
            >
              {{ isCancelling ? 'Membatalkan...' : 'Batal Transaksi' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Struk Pembayaran -->
    <div v-if="showStruk" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 p-4">
      <div class="print-receipt-shell w-full max-w-sm rounded-[28px] bg-white p-6 shadow-2xl">
        <div class="text-center border-b border-dashed border-slate-300 pb-3">
          <h3 class="text-lg font-bold text-slate-900">TOKO SEJAHTRA</h3>
          <p class="text-xs text-slate-500">Jl. Sejahtera No. 1</p>
          <p class="text-[11px] text-slate-400 mt-1">{{ strukData?.noStruk }} • {{ strukData?.tanggal }}</p>
          <div class="mt-1 flex justify-center gap-3 text-[11px] text-slate-600 font-medium">
            <span>Kasir: {{ strukData?.kasir }}</span>
            <span>Pelanggan: {{ strukData?.nama_pelanggan }}</span>
          </div>
        </div>

        <div class="my-3 space-y-1.5 text-xs text-slate-700">
          <div v-for="item in strukData?.items || []" :key="item.id" class="flex justify-between">
            <span class="flex-1 truncate mr-2">{{ item.nama_barang }} × {{ item.qty }}</span>
            <span class="font-semibold">Rp {{ (item.harga * item.qty).toLocaleString() }}</span>
          </div>
        </div>

        <div class="border-t border-dashed border-slate-300 pt-3 text-xs space-y-1 text-slate-700">
          <div v-if="strukData?.diskon > 0" class="flex justify-between text-slate-500">
            <span>Subtotal:</span>
            <span>Rp {{ ((strukData?.total || 0) + (strukData?.diskon || 0)).toLocaleString() }}</span>
          </div>
          <div v-if="strukData?.diskon > 0" class="flex justify-between text-rose-600 font-semibold">
            <span>Diskon:</span>
            <span>- Rp {{ Number(strukData?.diskon).toLocaleString() }}</span>
          </div>
          <div class="flex justify-between font-bold text-slate-900">
            <span>Total Tagihan:</span>
            <span>Rp {{ strukData?.total?.toLocaleString() }}</span>
          </div>
          <div class="flex justify-between">
            <span>Metode:</span>
            <span class="uppercase font-semibold">
              {{ strukData?.metode }} {{ strukData?.bank ? `(${strukData.bank})` : '' }}
            </span>
          </div>
          <div v-if="strukData?.nomor_referensi" class="flex justify-between font-mono text-[11px]">
            <span>Ref/RRN:</span>
            <span>{{ strukData?.nomor_referensi }}</span>
          </div>
          <div class="flex justify-between">
            <span>Bayar:</span>
            <span>Rp {{ strukData?.bayar?.toLocaleString() }}</span>
          </div>
          <div class="flex justify-between font-bold text-emerald-600">
            <span>Kembali:</span>
            <span>Rp {{ strukData?.kembalian?.toLocaleString() }}</span>
          </div>
        </div>

        <div class="mt-6 flex justify-between gap-2 print:hidden">
          <button
            @click="cetakStrukBrowser"
            class="flex-1 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white hover:bg-indigo-500"
          >
            🖨️ Cetak Ulang Struk
          </button>
          <button
            @click="showStruk = false"
            class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100"
          >
            Tutup (Esc)
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@media print {
  @page {
    size: 80mm auto;
    margin: 0;
  }
  body {
    background: #fff !important;
    color: #000 !important;
  }
  body * {
    visibility: hidden;
  }
  .print-receipt-shell,
  .print-receipt-shell * {
    visibility: visible;
  }
  .print-receipt-shell {
    position: absolute;
    inset: 0;
    width: 100%;
    margin: 0 !important;
    padding: 10px !important;
    border: 0 !important;
    box-shadow: none !important;
  }
}
</style>

<script setup>
import { useToast } from '../composables/useToast'

definePageMeta({ middleware: 'auth' })

const token = useCookie('token')
const userCookie = useCookie('user')
const runtimeConfig = useRuntimeConfig()
const apiBaseUrl = runtimeConfig.public.apiBaseUrl.replace(/\/+$/, '')
const { show: showToast } = useToast()

const searchInputRef = ref(null)
const searchKeyword = ref('')
const selectedCategory = ref('all')

const daftarBarang = ref([])
const keranjang = ref([])

// Pelanggan & Diskon
const namaPelanggan = ref('Pelanggan Umum')
const diskonNominal = ref(0)

// Metode Pembayaran
const paymentMethod = ref('tunai')
const bayar = ref(0)

const showKonfirmasi = ref(false)
const showStruk = ref(false)
const strukData = ref(null)
const isSubmitting = ref(false)

// State Khusus Midtrans Snap
const activeSnapNoNota = ref('')
const activeSnapToken = ref('')
const activeSnapTotal = ref(0)
const activeSnapItems = ref([])
const activeSnapCustomer = ref('')
const showSnapModal = ref(false)
const isSimulating = ref(false)
const isCancelling = ref(false)
let snapPollingTimer = null

const subtotalBelanja = computed(() => {
  return keranjang.value.reduce((total, item) => total + (item.harga * item.qty), 0)
})

const totalTagihan = computed(() => {
  return Math.max(0, subtotalBelanja.value - Math.max(0, Number(diskonNominal.value || 0)))
})

const totalItemQty = computed(() => {
  return keranjang.value.reduce((total, item) => total + item.qty, 0)
})

const kembalian = computed(() => {
  return (bayar.value || 0) - totalTagihan.value
})

// Validasi kesiapan bayar tergantung metode yang dipilih
const statusBayarValid = computed(() => {
  if (totalTagihan.value <= 0 || keranjang.value.length === 0) return false

  if (paymentMethod.value === 'midtrans') {
    return true
  }

  if (paymentMethod.value === 'tunai') {
    return bayar.value >= totalTagihan.value
  }

  return false
})

const categories = computed(() => {
  const set = new Set()
  daftarBarang.value.forEach(b => {
    if (b.kategori && String(b.kategori).trim() !== '') {
      set.add(b.kategori)
    }
  })
  return ['all', ...Array.from(set)]
})

const tampilBarang = computed(() => {
  return daftarBarang.value.filter(b => {
    const matchCategory = !selectedCategory.value || selectedCategory.value === 'all' ||
      String(b.kategori || '').toLowerCase() === String(selectedCategory.value || '').toLowerCase()

    const matchSearch = !searchKeyword.value ||
      String(b.nama_barang || '').toLowerCase().includes(searchKeyword.value.toLowerCase()) ||
      String(b.kode_barang || '').toLowerCase().includes(searchKeyword.value.toLowerCase())

    return matchCategory && matchSearch
  })
})

const pilihMetode = (metode) => {
  paymentMethod.value = metode

  if (metode === 'midtrans') {
    bayar.value = totalTagihan.value
  } else if (metode === 'tunai') {
    bayar.value = 0
  }
}

// Barcode Scanner Handler
const handleBarcodeScan = () => {
  const code = searchKeyword.value.trim().toLowerCase()
  if (!code) return

  const item = daftarBarang.value.find(b =>
    String(b.kode_barang).toLowerCase() === code ||
    String(b.nama_barang).toLowerCase() === code
  )

  if (item) {
    tambahKeKeranjang(item)
    searchKeyword.value = ''
  } else {
    showToast(`Produk dengan kode "${searchKeyword.value}" tidak ditemukan!`, 'warning')
  }
}

const validasiInputQty = (item) => {
  if (!item.qty || item.qty < 1) item.qty = 1
  if (item.qty > item.stok) {
    item.qty = item.stok
    showToast(`Kuantitas disesuaikan ke stok maksimal (${item.stok})`, 'warning')
  }
}

const loadBarang = async () => {
  try {
    daftarBarang.value = await $fetch(`${apiBaseUrl}/barang`, {
      headers: { Authorization: `Bearer ${token.value}` }
    })
  } catch (err) {
    if (err.status === 401) {
      token.value = null
      navigateTo('/')
    } else {
      showToast('Gagal memuat katalog barang', 'error')
    }
  }
}

const tambahKeKeranjang = (barang) => {
  if (barang.stok <= 0) {
    showToast(`Stok ${barang.nama_barang} telah habis`, 'warning')
    return
  }

  const existing = keranjang.value.find(i => i.id === barang.id)
  if (existing) {
    if (existing.qty < barang.stok) {
      existing.qty++
      showToast(`+1 ${barang.nama_barang}`, 'info', 1000)
    } else {
      showToast(`Maksimal stok tersedia hanya ${barang.stok}`, 'warning')
    }
  } else {
    keranjang.value.push({
      id: barang.id,
      kode_barang: barang.kode_barang,
      nama_barang: barang.nama_barang,
      harga: Number(barang.harga),
      stok: barang.stok,
      qty: 1
    })
    showToast(`+ ${barang.nama_barang} masuk keranjang`, 'success', 1000)
  }

  if (paymentMethod.value !== 'tunai') {
    bayar.value = totalTagihan.value
  }
}

const tambahQty = (item) => {
  if (item.qty < item.stok) {
    item.qty++
    if (paymentMethod.value !== 'tunai') bayar.value = totalTagihan.value
  } else {
    showToast(`Stok maksimal barang ini adalah ${item.stok}`, 'warning')
  }
}

const kurangQty = (item) => {
  item.qty--
  if (item.qty <= 0) {
    keranjang.value = keranjang.value.filter(i => i.id !== item.id)
  }
  if (paymentMethod.value !== 'tunai') bayar.value = totalTagihan.value
}

const hapusItem = (item) => {
  keranjang.value = keranjang.value.filter(i => i.id !== item.id)
  if (paymentMethod.value !== 'tunai') bayar.value = totalTagihan.value
}

const kosongkanKeranjang = () => {
  if (confirm('Kosongkan semua barang di keranjang?')) {
    keranjang.value = []
    diskonNominal.value = 0
  }
}

const bukaKonfirmasi = () => {
  if (keranjang.value.length === 0) {
    showToast('Keranjang belanja masih kosong!', 'warning')
    return
  }

  if (!statusBayarValid.value) {
    if (paymentMethod.value === 'tunai') {
      showToast('Uang pembayaran masih kurang!', 'warning')
    }
    return
  }

  showKonfirmasi.value = true
}

const prosesTransaksi = async () => {
  if (isSubmitting.value) return

  // Jika metode adalah Midtrans Snap, gunakan alur Midtrans
  if (paymentMethod.value === 'midtrans') {
    await prosesMidtransPayment()
    return
  }

  isSubmitting.value = true
  showKonfirmasi.value = false

  try {
    const res = await $fetch(`${apiBaseUrl}/transaksi`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` },
      body: {
        nama_pelanggan: namaPelanggan.value,
        diskon: Number(diskonNominal.value || 0),
        items: keranjang.value.map(i => ({ id: i.id, qty: i.qty })),
        bayar: bayar.value,
        metode_pembayaran: 'tunai',
        bank: null,
        nomor_referensi: null
      }
    })

    const cashierName = (() => {
      try {
        const u = typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
        return u?.name || 'Kasir'
      } catch (e) {
        return 'Kasir'
      }
    })()

    strukData.value = {
      noStruk: res.data.no_nota,
      tanggal: new Date().toLocaleString('id-ID'),
      kasir: cashierName,
      nama_pelanggan: res.data.nama_pelanggan,
      items: keranjang.value.map(i => ({ ...i })),
      diskon: res.data.diskon,
      total: res.data.total_harga,
      bayar: res.data.bayar,
      kembalian: res.data.kembali,
      metode: res.data.metode_pembayaran,
      bank: res.data.bank,
      nomor_referensi: res.data.nomor_referensi
    }

    showStruk.value = true
    keranjang.value = []
    bayar.value = 0
    diskonNominal.value = 0
    qrisVerified.value = false
    namaPelanggan.value = 'Pelanggan Umum'
    showToast('Transaksi berhasil disimpan!', 'success')

    await loadBarang()

    // Cetak struk otomatis jika ESC/POS terhubung
    try {
      await $fetch(`${apiBaseUrl}/print`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${token.value}` },
        body: strukData.value
      })
    } catch (e) {}
  } catch (err) {
    console.error('Transaksi error:', err)
    showToast(err.data?.message || 'Transaksi gagal diproses', 'error')
  } finally {
    isSubmitting.value = false
  }
}

// ----------------------------------------------------
// LOGIKA PEMBAYARAN MIDTRANS SNAP
// ----------------------------------------------------

const prosesMidtransPayment = async () => {
  isSubmitting.value = true
  showKonfirmasi.value = false

  try {
    const res = await $fetch(`${apiBaseUrl}/payment/snap`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` },
      body: {
        nama_pelanggan: namaPelanggan.value,
        diskon: Number(diskonNominal.value || 0),
        items: keranjang.value.map(i => ({ id: i.id, qty: i.qty }))
      }
    })

    activeSnapToken.value = res.snap_token
    activeSnapNoNota.value = res.no_nota
    activeSnapTotal.value = res.total_harga
    activeSnapItems.value = keranjang.value.map(i => ({ ...i }))
    activeSnapCustomer.value = namaPelanggan.value || 'Pelanggan Umum'

    showSnapModal.value = true

    // Panggil window.snap.pay jika script Midtrans tersedia
    panggilSnapPopup(res.snap_token, res.no_nota)

    // Mulai auto-polling status transaksi
    mulaiPollingSnap(res.no_nota)

    showToast('Silakan selesaikan pembayaran di popup Midtrans', 'info')
  } catch (err) {
    console.error('Midtrans Snap Error:', err)
    showToast(err.data?.message || 'Gagal memulai transaksi Midtrans', 'error')
  } finally {
    isSubmitting.value = false
  }
}

const panggilSnapPopup = (snapToken, noNota) => {
  if (typeof window !== 'undefined' && window.snap && typeof window.snap.pay === 'function') {
    try {
      window.snap.pay(snapToken, {
        onSuccess: async (result) => {
          console.log('Snap Success:', result)
          try {
            const res = await $fetch(`${apiBaseUrl}/payment/${noNota}/status`, {
              headers: { Authorization: `Bearer ${token.value}` }
            })
            handleSnapSuccess(res.transaksi || res)
          } catch (e) {
            handleSnapSuccess({ no_nota: noNota, total_harga: activeSnapTotal.value })
          }
        },
        onPending: (result) => {
          console.log('Snap Pending:', result)
          showToast('Menunggu pelanggan menyelesaikan pembayaran...', 'info')
        },
        onError: (result) => {
          console.error('Snap Error:', result)
          showToast('Pembayaran Midtrans gagal atau ditolak!', 'error')
        },
        onClose: () => {
          console.log('Snap Closed by user')
        }
      })
    } catch (e) {
      console.warn('Gagal memanggil window.snap.pay, beralih ke simulasi:', e)
    }
  } else {
    console.warn('window.snap belum terpasang atau mode mock, gunakan simulasi kasir')
  }
}

const bukaUlangSnap = () => {
  if (activeSnapToken.value && activeSnapNoNota.value) {
    panggilSnapPopup(activeSnapToken.value, activeSnapNoNota.value)
  }
}

const mulaiPollingSnap = (noNota) => {
  stopPollingSnap()
  snapPollingTimer = setInterval(async () => {
    try {
      const res = await $fetch(`${apiBaseUrl}/payment/${noNota}/status`, {
        headers: { Authorization: `Bearer ${token.value}` }
      })

      if (res.status === 'selesai') {
        stopPollingSnap()
        handleSnapSuccess(res.transaksi || res)
      } else if (res.status === 'dibatalkan' || res.status === 'kadaluwarsa') {
        stopPollingSnap()
        showSnapModal.value = false
        showToast('Pembayaran dibatalkan atau telah kadaluwarsa', 'warning')
        await loadBarang()
      }
    } catch (e) {
      console.warn('Polling error:', e)
    }
  }, 2500)
}

const stopPollingSnap = () => {
  if (snapPollingTimer) {
    clearInterval(snapPollingTimer)
    snapPollingTimer = null
  }
}

const handleSnapSuccess = async (trx) => {
  stopPollingSnap()
  showSnapModal.value = false

  const cashierName = (() => {
    try {
      const u = typeof userCookie.value === 'string' ? JSON.parse(userCookie.value) : userCookie.value
      return u?.name || 'Kasir'
    } catch (e) {
      return 'Kasir'
    }
  })()

  strukData.value = {
    noStruk: trx.no_nota || activeSnapNoNota.value,
    tanggal: new Date().toLocaleString('id-ID'),
    kasir: cashierName,
    nama_pelanggan: trx.nama_pelanggan || activeSnapCustomer.value,
    items: activeSnapItems.value.length ? activeSnapItems.value : (trx.items || []),
    diskon: trx.diskon || 0,
    total: trx.total_harga || activeSnapTotal.value,
    bayar: trx.bayar || activeSnapTotal.value,
    kembalian: 0,
    metode: 'MIDTRANS GATEWAY',
    bank: trx.bank || 'QRIS / VA',
    nomor_referensi: trx.nomor_referensi || trx.no_nota || activeSnapNoNota.value
  }

  showStruk.value = true
  keranjang.value = []
  bayar.value = 0
  diskonNominal.value = 0
  namaPelanggan.value = 'Pelanggan Umum'
  showToast('✓ Pembayaran Midtrans Berhasil Diverifikasi!', 'success')

  await loadBarang()

  // Cetak struk otomatis jika ESC/POS terhubung
  try {
    await $fetch(`${apiBaseUrl}/print`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` },
      body: strukData.value
    })
  } catch (e) {}
}

const simulasikanBayarSukses = async () => {
  if (isSimulating.value || !activeSnapNoNota.value) return
  isSimulating.value = true
  try {
    const res = await $fetch(`${apiBaseUrl}/payment/${activeSnapNoNota.value}/simulasi-sukses`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` }
    })
    await handleSnapSuccess(res.data)
  } catch (err) {
    showToast(err.data?.message || 'Simulasi gagal', 'error')
  } finally {
    isSimulating.value = false
  }
}

const batalkanSnapTransaksi = async () => {
  if (isCancelling.value || !activeSnapNoNota.value) return
  if (!confirm('Apakah Anda yakin ingin membatalkan transaksi ini? Stok produk akan dikembalikan.')) return

  isCancelling.value = true
  try {
    await $fetch(`${apiBaseUrl}/payment/${activeSnapNoNota.value}/batal`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` }
    })
    stopPollingSnap()
    showSnapModal.value = false
    showToast('Transaksi Midtrans telah dibatalkan dan stok dikembalikan.', 'info')
    await loadBarang()
  } catch (err) {
    showToast(err.data?.message || 'Gagal membatalkan transaksi', 'error')
  } finally {
    isCancelling.value = false
  }
}

const cetakStrukBrowser = () => {
  if (typeof window !== 'undefined') {
    window.print()
  }
}

const handleKeydown = (e) => {
  if (e.key === 'F2') {
    e.preventDefault()
    searchInputRef.value?.focus()
  } else if (e.key === 'Escape') {
    if (showKonfirmasi.value) showKonfirmasi.value = false
    if (showStruk.value) showStruk.value = false
  }
}

onMounted(() => {
  if (!token.value) {
    navigateTo('/')
    return
  }
  loadBarang()
  if (typeof window !== 'undefined') {
    window.addEventListener('keydown', handleKeydown)
  }
})

onUnmounted(() => {
  stopPollingSnap()
  if (typeof window !== 'undefined') {
    window.removeEventListener('keydown', handleKeydown)
  }
})
</script>
