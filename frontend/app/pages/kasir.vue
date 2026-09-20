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
              <div class="grid grid-cols-3 gap-2">
                <button
                  type="button"
                  @click="pilihMetode('tunai')"
                  :class="paymentMethod === 'tunai' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 font-bold ring-2 ring-indigo-200' : 'border-slate-200 bg-slate-50 text-slate-700 font-medium'"
                  class="flex items-center justify-center gap-1.5 rounded-xl border p-2.5 text-xs transition"
                >
                  <span>💵 Tunai</span>
                </button>
                <button
                  type="button"
                  @click="pilihMetode('qris')"
                  :class="paymentMethod === 'qris' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 font-bold ring-2 ring-indigo-200' : 'border-slate-200 bg-slate-50 text-slate-700 font-medium'"
                  class="flex items-center justify-center gap-1.5 rounded-xl border p-2.5 text-xs transition"
                >
                  <span>📱 QRIS</span>
                </button>
                <button
                  type="button"
                  @click="pilihMetode('transfer')"
                  :class="paymentMethod === 'transfer' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 font-bold ring-2 ring-indigo-200' : 'border-slate-200 bg-slate-50 text-slate-700 font-medium'"
                  class="flex items-center justify-center gap-1.5 rounded-xl border p-2.5 text-xs transition"
                >
                  <span>💳 Transfer / Debit</span>
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

            <!-- DETAIL METODE: QRIS DINAMIS -->
            <div v-else-if="paymentMethod === 'qris'" class="space-y-3 rounded-2xl bg-indigo-50/50 p-3.5 border border-indigo-200 text-center">
              <div class="flex items-center justify-between border-b border-indigo-100 pb-2 text-xs">
                <span class="font-bold text-indigo-900">QRIS Standar Nasional</span>
                <span class="rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">
                  {{ qrisVerified ? '✓ Terverifikasi Lunas' : 'Menunggu Scan' }}
                </span>
              </div>

              <!-- Visual QR Code Dinamis -->
              <div class="flex flex-col items-center justify-center p-2 bg-white rounded-xl border border-indigo-100 shadow-inner">
                <div class="relative flex items-center justify-center p-3">
                  <!-- SVG Mock QR Code Generator -->
                  <svg class="h-36 w-36 text-slate-900" viewBox="0 0 100 100" fill="currentColor">
                    <path d="M0,0 h30 v30 h-30 z M5,5 v20 h20 v-20 z M10,10 h10 v10 h-10 z" />
                    <path d="M70,0 h30 v30 h-30 z M75,5 v20 h20 v-20 z M80,10 h10 v10 h-10 z" />
                    <path d="M0,70 h30 v30 h-30 z M5,75 v20 h20 v-20 z M10,80 h10 v10 h-10 z" />
                    <rect x="40" y="10" width="8" height="8" />
                    <rect x="52" y="10" width="6" height="15" />
                    <rect x="40" y="25" width="18" height="6" />
                    <rect x="10" y="40" width="15" height="6" />
                    <rect x="30" y="40" width="8" height="18" />
                    <rect x="45" y="40" width="10" height="10" />
                    <rect x="65" y="40" width="25" height="8" />
                    <rect x="10" y="55" width="12" height="6" />
                    <rect x="60" y="55" width="10" height="18" />
                    <rect x="75" y="55" width="15" height="15" />
                    <rect x="40" y="70" width="8" height="20" />
                    <rect x="55" y="80" width="15" height="10" />
                    <rect x="80" y="80" width="10" height="10" />
                  </svg>
                  <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <span class="rounded bg-white px-1 py-0.5 text-[8px] font-black tracking-tighter text-rose-600 shadow">
                      QRIS
                    </span>
                  </div>
                </div>
                <p class="text-[11px] font-bold text-slate-800">TOKO SEJAHTRA</p>
                <p class="text-[10px] text-slate-500 font-mono">NMID: ID1020268849102</p>
                <p class="mt-1 text-sm font-extrabold text-indigo-700">Rp {{ totalTagihan.toLocaleString() }}</p>
                <p class="text-[10px] font-mono text-slate-400">RRN: {{ qrisRefNumber }}</p>
              </div>

              <button
                type="button"
                @click="verifikasiQris"
                :class="qrisVerified ? 'bg-emerald-600 text-white' : 'bg-indigo-600 text-white hover:bg-indigo-700'"
                class="w-full rounded-xl py-2 text-xs font-bold transition shadow-sm"
              >
                {{ qrisVerified ? '✓ Pembayaran QRIS Sukses Diverifikasi' : 'Simulasi Pembeli Telah Scan & Bayar' }}
              </button>
            </div>

            <!-- DETAIL METODE: TRANSFER / KARTU DEBIT -->
            <div v-else-if="paymentMethod === 'transfer'" class="space-y-3 rounded-2xl bg-slate-50 p-3.5 border border-slate-200">
              <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">Pilih Bank / Mesin EDC</label>
                <div class="grid grid-cols-3 gap-1.5">
                  <button
                    v-for="b in ['BCA', 'Mandiri', 'BRI', 'BNI', 'Seabank', 'Jago']"
                    :key="b"
                    type="button"
                    @click="selectedBank = b"
                    :class="selectedBank === b ? 'border-indigo-600 bg-indigo-50 text-indigo-700 font-bold' : 'border-slate-200 bg-white text-slate-700 font-medium'"
                    class="rounded-lg border p-1.5 text-center text-xs transition"
                  >
                    {{ b }}
                  </button>
                </div>
              </div>

              <div>
                <label class="mb-1 block text-xs font-bold text-slate-700">Nomor Referensi / 4 Digit Kartu *</label>
                <input
                  v-model="transferRefNumber"
                  type="text"
                  placeholder="Misal: REF-884920 atau Kartu ...1234"
                  class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-mono text-slate-800 outline-none focus:border-indigo-500"
                />
              </div>

              <div class="rounded-xl bg-white p-2.5 border border-slate-200 flex justify-between items-center text-xs">
                <span class="text-slate-500">Nominal Transfer:</span>
                <span class="font-bold text-slate-900">Rp {{ totalTagihan.toLocaleString() }} (Pas)</span>
              </div>
            </div>
          </div>

          <!-- Tombol Eksekusi Pembayaran -->
          <button
            @click="bukaKonfirmasi"
            :disabled="isSubmitting || !statusBayarValid || totalTagihan === 0"
            class="mt-4 w-full rounded-xl bg-emerald-600 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-500 active:scale-[0.99] disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-400"
          >
            {{ isSubmitting ? 'Memproses Transaksi...' : 'Proses Pembayaran & Cetak Struk' }}
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
              {{ paymentMethod }} {{ selectedBank ? `(${selectedBank})` : '' }}
            </span>
          </div>
          <div v-if="activeRefNumber" class="flex justify-between font-mono">
            <span>No. Referensi:</span>
            <span class="font-bold text-slate-900">{{ activeRefNumber }}</span>
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
            {{ isSubmitting ? 'Menyimpan...' : 'Selesaikan & Cetak' }}
          </button>
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
const selectedBank = ref('BCA')
const transferRefNumber = ref('')
const qrisRefNumber = ref('')
const qrisVerified = ref(false)

const showKonfirmasi = ref(false)
const showStruk = ref(false)
const strukData = ref(null)
const isSubmitting = ref(false)

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

  if (paymentMethod.value === 'tunai') {
    return bayar.value >= totalTagihan.value
  }

  if (paymentMethod.value === 'qris') {
    return qrisVerified.value === true
  }

  if (paymentMethod.value === 'transfer') {
    return String(transferRefNumber.value || '').trim().length >= 3
  }

  return false
})

const activeRefNumber = computed(() => {
  if (paymentMethod.value === 'qris') return qrisRefNumber.value
  if (paymentMethod.value === 'transfer') return transferRefNumber.value
  return null
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

  if (metode === 'tunai') {
    bayar.value = 0
  } else if (metode === 'qris') {
    bayar.value = totalTagihan.value
    qrisVerified.value = false
    qrisRefNumber.value = 'QRIS-' + new Date().toISOString().slice(0, 10).replace(/-/g, '') + '-' + Math.floor(10000 + Math.random() * 90000)
  } else if (metode === 'transfer') {
    bayar.value = totalTagihan.value
    if (!transferRefNumber.value) {
      transferRefNumber.value = 'TRF-' + Math.floor(100000 + Math.random() * 900000)
    }
  }
}

const verifikasiQris = () => {
  qrisVerified.value = true
  bayar.value = totalTagihan.value
  showToast('Pembayaran QRIS berhasil diverifikasi!', 'success')
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
    } else if (paymentMethod.value === 'qris') {
      showToast('Harap verifikasi pembayaran QRIS terlebih dahulu!', 'warning')
    } else if (paymentMethod.value === 'transfer') {
      showToast('Harap masukkan nomor referensi transfer / 4 digit kartu!', 'warning')
    }
    return
  }

  showKonfirmasi.value = true
}

const prosesTransaksi = async () => {
  if (isSubmitting.value) return
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
        metode_pembayaran: paymentMethod.value,
        bank: paymentMethod.value === 'transfer' ? selectedBank.value : null,
        nomor_referensi: activeRefNumber.value
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
  if (typeof window !== 'undefined') {
    window.removeEventListener('keydown', handleKeydown)
  }
})
</script>
