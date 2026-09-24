<template>
  <div>
    <!-- Kartu Ringkasan Inventaris (Responsif Compact di Mobile) -->
    <div class="mb-4 grid grid-cols-3 gap-2 sm:gap-4">
      <div class="rounded-2xl sm:rounded-[22px] border border-slate-200 bg-white p-2.5 sm:p-5 shadow-sm">
        <p class="text-[10px] sm:text-sm font-medium text-slate-500">Jenis Barang</p>
        <div class="mt-1 sm:mt-3 flex items-end justify-between">
          <div>
            <p class="text-base sm:text-2xl font-bold text-slate-900">{{ barangList.length }}</p>
            <p class="hidden sm:block text-xs text-slate-500">Item terdaftar</p>
          </div>
          <span class="rounded-full bg-indigo-50 px-2 py-0.5 sm:px-3 sm:py-1 text-[10px] sm:text-xs font-semibold text-indigo-700">Data</span>
        </div>
      </div>
      <div class="rounded-2xl sm:rounded-[22px] border border-slate-200 bg-white p-2.5 sm:p-5 shadow-sm">
        <p class="text-[10px] sm:text-sm font-medium text-slate-500">Stok Fisik</p>
        <div class="mt-1 sm:mt-3 flex items-end justify-between">
          <div>
            <p class="text-base sm:text-2xl font-bold text-slate-900">{{ totalStokUnit }}</p>
            <p class="hidden sm:block text-xs text-slate-500">Unit siap dijual</p>
          </div>
          <span class="rounded-full bg-emerald-50 px-2 py-0.5 sm:px-3 sm:py-1 text-[10px] sm:text-xs font-semibold text-emerald-700">Ready</span>
        </div>
      </div>
      <div class="rounded-2xl sm:rounded-[22px] border border-slate-200 bg-white p-2.5 sm:p-5 shadow-sm">
        <p class="text-[10px] sm:text-sm font-medium text-slate-500">Stok Kritis</p>
        <div class="mt-1 sm:mt-3 flex items-end justify-between">
          <div>
            <p class="text-base sm:text-2xl font-bold text-rose-600">{{ stokMenipisCount }}</p>
            <p class="hidden sm:block text-xs text-slate-500">Stok ≤ 5 unit</p>
          </div>
          <span :class="stokMenipisCount > 0 ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-600'" class="rounded-full px-2 py-0.5 sm:px-3 sm:py-1 text-[10px] sm:text-xs font-semibold">
            {{ stokMenipisCount > 0 ? 'Kritis' : 'Aman' }}
          </span>
        </div>
      </div>
    </div>

    <!-- Bagian Utama: Form Tambah & Tabel Kelola Barang -->
    <div class="rounded-[28px] border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-4">
        <div>
          <h2 class="text-lg sm:text-xl font-bold text-slate-900">Katalog Barang Toko</h2>
          <p class="text-xs text-slate-500">Tambah barang baru, perbarui stok, dan atur harga jual & harga modal</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
          <button
            type="button"
            @click="bukaBulkModal"
            class="rounded-xl bg-emerald-600 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-emerald-500 active:scale-95 flex items-center gap-1.5"
          >
            <span>📑 Tambah Massal / Import</span>
          </button>
          <button
            type="button"
            @click="showFormTambah = !showFormTambah"
            class="rounded-xl bg-indigo-600 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-500 active:scale-95 flex items-center gap-1.5"
          >
            <span>{{ showFormTambah ? '✕ Tutup Form' : '+ Tambah 1 Barang' }}</span>
          </button>
          <div class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
            {{ filteredBarangList.length }} barang
          </div>
        </div>
      </div>

      <!-- Form Tambah Barang Baru (Toggleable) -->
      <form v-show="showFormTambah" @submit.prevent="simpanBarang" class="mt-4 grid gap-3 rounded-[24px] border border-slate-200 bg-slate-50/80 p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 items-end animate-in fade-in duration-200">
        <div>
          <label class="mb-1.5 block text-xs font-bold text-slate-700">Kode Barang *</label>
          <input
            v-model="form.kode_barang"
            placeholder="BRG001 / Barcode"
            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
            required
          />
        </div>
        <div>
          <label class="mb-1.5 block text-xs font-bold text-slate-700">Nama Barang *</label>
          <input
            v-model="form.nama_barang"
            placeholder="Nama Produk"
            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
            required
          />
        </div>
        <div>
          <label class="mb-1.5 block text-xs font-bold text-slate-700">Kategori</label>
          <input
            v-model="form.kategori"
            placeholder="Makanan / Minuman"
            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
          />
        </div>
        <div>
          <label class="mb-1.5 block text-xs font-bold text-slate-700">Harga Modal (Rp)</label>
          <input
            v-model.number="form.harga_modal"
            type="number"
            min="0"
            placeholder="0"
            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
          />
        </div>
        <div>
          <label class="mb-1.5 block text-xs font-bold text-slate-700">Harga Jual (Rp) *</label>
          <input
            v-model.number="form.harga"
            type="number"
            min="1"
            placeholder="0"
            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
            required
          />
        </div>
        <div>
          <label class="mb-1.5 block text-xs font-bold text-slate-700">Stok Awal *</label>
          <input
            v-model.number="form.stok"
            type="number"
            min="0"
            placeholder="0"
            class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
            required
          />
        </div>
        <div class="sm:col-span-2 lg:col-span-3 xl:col-span-6 flex justify-end mt-1">
          <button
            type="submit"
            :disabled="isSubmittingBarang"
            class="rounded-xl bg-indigo-600 px-6 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-500 active:scale-95 disabled:cursor-not-allowed disabled:bg-indigo-400"
          >
            {{ isSubmittingBarang ? 'Menyimpan...' : '+ Tambah Barang Baru' }}
          </button>
        </div>
      </form>

      <!-- Filter Pencarian Cepat & Multi-Kriteria -->
      <div class="mt-6 space-y-3">
        <!-- Baris 1: Search, Dropdowns (Kategori, Status Stok, Sorting) -->
        <div class="grid gap-2.5 sm:grid-cols-2 lg:grid-cols-4">
          <!-- Cari Nama / Kode -->
          <div class="relative">
            <input
              v-model="searchKeyword"
              type="text"
              placeholder="Cari kode atau nama barang..."
              class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 pl-3 pr-8 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
            />
            <span class="absolute right-2.5 top-2.5 text-slate-400">🔍</span>
          </div>

          <!-- Filter Kategori Dropdown -->
          <div>
            <select
              v-model="filterKategori"
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white"
            >
              <option value="all">📁 Semua Kategori</option>
              <option v-for="kat in kategoriList" :key="kat" :value="kat">{{ kat }}</option>
            </select>
          </div>

          <!-- Filter Status Stok -->
          <div>
            <select
              v-model="filterStok"
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white"
            >
              <option value="all">📦 Semua Stok Fisik</option>
              <option value="safe">✓ Stok Aman (> 5 unit)</option>
              <option value="low">⚠️ Stok Menipis (≤ 5 unit)</option>
              <option value="empty">❌ Stok Habis (0 unit)</option>
            </select>
          </div>

          <!-- Urutkan / Sorting -->
          <div>
            <select
              v-model="sortBy"
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2.5 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white"
            >
              <option value="nama_asc">🔤 Nama (A - Z)</option>
              <option value="nama_desc">🔤 Nama (Z - A)</option>
              <option value="harga_asc">💵 Harga Jual Termurah</option>
              <option value="harga_desc">💰 Harga Jual Termahal</option>
              <option value="modal_desc">🏷️ Modal Terbesar</option>
              <option value="margin_desc">📈 Margin Laba Tertinggi</option>
              <option value="stok_desc">📊 Stok Terbanyak</option>
              <option value="stok_asc">📉 Stok Paling Sedikit</option>
            </select>
          </div>
        </div>

        <!-- Baris 2: Category Chips & Reset Filter -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
          <button
            @click="filterKategori = 'all'"
            :class="filterKategori === 'all' ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-medium'"
            class="rounded-full px-3 py-1.5 whitespace-nowrap transition text-xs shrink-0 active:scale-95"
          >
            ✨ Semua ({{ barangList.length }})
          </button>
          <button
            v-for="kat in kategoriList"
            :key="kat"
            @click="filterKategori = kat"
            :class="filterKategori === kat ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-medium'"
            class="rounded-full px-3 py-1.5 whitespace-nowrap transition text-xs shrink-0 active:scale-95"
          >
            {{ kat }}
          </button>

          <!-- Reset Filter Button jika ada filter aktif -->
          <button
            v-if="searchKeyword || filterKategori !== 'all' || filterStok !== 'all' || sortBy !== 'nama_asc'"
            type="button"
            @click="resetBarangFilters"
            class="rounded-full bg-rose-50 border border-rose-200 text-rose-600 px-3 py-1.5 text-xs font-bold hover:bg-rose-100 whitespace-nowrap transition shrink-0 active:scale-95"
            title="Reset seluruh filter"
          >
            ✕ Reset Filter
          </button>
        </div>
      </div>

      <!-- Tabel Data Barang -->
      <div class="mt-4 overflow-x-auto rounded-[20px] border border-slate-200">
        <table class="min-w-full border-collapse text-left text-xs">
          <thead>
            <tr class="border-b border-slate-200 bg-slate-100/75 text-slate-700">
              <th class="p-3.5 font-bold">Kode</th>
              <th class="p-3.5 font-bold">Nama Barang</th>
              <th class="p-3.5 font-bold">Kategori</th>
              <th class="p-3.5 font-bold text-right">Harga Modal</th>
              <th class="p-3.5 font-bold text-right">Harga Jual</th>
              <th class="p-3.5 font-bold text-right">Margin / Laba</th>
              <th class="p-3.5 font-bold text-center">Stok Fisik</th>
              <th class="p-3.5 font-bold text-center">Aksi Cepat</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredBarangList.length === 0">
              <td colspan="8" class="p-8 text-center text-slate-500 text-sm">
                Tidak ada data barang yang sesuai dengan pencarian.
              </td>
            </tr>

            <tr
              v-for="b in filteredBarangList"
              :key="b.id"
              class="border-b border-slate-100 transition hover:bg-slate-50"
            >
              <td class="p-3.5 font-mono font-bold text-slate-800">{{ b.kode_barang }}</td>
              <td class="p-3.5 font-bold text-slate-900">{{ b.nama_barang }}</td>
              <td class="p-3.5 text-slate-600">
                <span class="rounded bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700">
                  {{ b.kategori || 'Umum' }}
                </span>
              </td>
              <td class="p-3.5 text-right font-medium text-slate-600">
                Rp {{ Number(b.harga_modal || 0).toLocaleString() }}
              </td>
              <td class="p-3.5 text-right font-bold text-slate-900">
                Rp {{ Number(b.harga || 0).toLocaleString() }}
              </td>
              <td class="p-3.5 text-right font-bold text-emerald-600">
                +Rp {{ Math.max(0, Number(b.harga || 0) - Number(b.harga_modal || 0)).toLocaleString() }}
              </td>
              <td class="p-3.5 text-center">
                <span
                  :class="Number(b.stok || 0) <= 0 ? 'bg-rose-100 text-rose-800' : Number(b.stok || 0) <= 5 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'"
                  class="inline-block rounded-full px-2.5 py-0.5 text-[11px] font-bold"
                >
                  {{ Number(b.stok || 0) <= 0 ? 'Habis (0)' : b.stok + ' unit' }}
                </span>
              </td>
              <td class="p-3.5 text-center">
                <div class="flex items-center justify-center gap-1.5">
                  <!-- Tombol Restock Cepat -->
                  <button
                    @click="bukaRestockModal(b)"
                    class="rounded-lg bg-emerald-50 px-2 py-1 text-[11px] font-bold text-emerald-700 hover:bg-emerald-100 active:scale-95"
                    title="Tambah stok masuk barang"
                  >
                    + Restock
                  </button>
                  <button
                    @click="bukaEditForm(b)"
                    class="rounded-lg bg-indigo-50 px-2.5 py-1 text-[11px] font-bold text-indigo-600 hover:bg-indigo-100 active:scale-95"
                  >
                    Edit
                  </button>
                  <button
                    @click="hapusBarang(b.id)"
                    :disabled="deletingBarangIds.includes(b.id)"
                    class="rounded-lg bg-rose-50 px-2.5 py-1 text-[11px] font-bold text-rose-600 hover:bg-rose-100 active:scale-95 disabled:opacity-50"
                  >
                    {{ deletingBarangIds.includes(b.id) ? '...' : 'Hapus' }}
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Restock Cepat -->
    <div
      v-if="showRestockModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
      @click.self="showRestockModal = false"
    >
      <div class="w-full max-w-sm max-h-[90vh] overflow-y-auto rounded-[28px] bg-white p-5 sm:p-6 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900">+ Restock Masuk Barang</h3>
        <p class="mt-1 text-xs text-slate-500">
          Tambah stok fisik untuk <span class="font-bold text-slate-800">{{ selectedRestockBarang?.nama_barang }}</span>.
        </p>

        <div class="mt-4 rounded-xl bg-slate-50 p-3 text-xs border border-slate-200">
          <div class="flex justify-between">
            <span class="text-slate-500">Stok Saat Ini:</span>
            <span class="font-bold text-slate-900">{{ selectedRestockBarang?.stok }} unit</span>
          </div>
          <div class="flex justify-between mt-1">
            <span class="text-slate-500">Stok Setelah Restock:</span>
            <span class="font-bold text-emerald-600">{{ (Number(selectedRestockBarang?.stok || 0) + Number(restockJumlah || 0)) }} unit</span>
          </div>
        </div>

        <form @submit.prevent="submitRestock" class="mt-4 space-y-3">
          <div>
            <label class="mb-1 block text-xs font-bold text-slate-700">Jumlah Unit Masuk *</label>
            <input
              v-model.number="restockJumlah"
              type="number"
              min="1"
              placeholder="Misal: 10 atau 50"
              class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-base font-bold text-slate-900 outline-none focus:border-indigo-500"
              required
              autofocus
            />
          </div>

          <div class="flex gap-2 pt-2">
            <button
              type="button"
              @click="showRestockModal = false"
              class="flex-1 rounded-xl border border-slate-300 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="isSubmittingRestock || restockJumlah <= 0"
              class="flex-1 rounded-xl bg-emerald-600 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-500 active:scale-95 disabled:opacity-50"
            >
              {{ isSubmittingRestock ? 'Menyimpan...' : 'Simpan Restock' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Edit Barang Lengkap -->
    <div
      v-if="showEditForm"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
      @click.self="tutupEditForm"
    >
      <div class="w-full max-w-md max-h-[90vh] overflow-y-auto rounded-[28px] bg-white p-5 sm:p-6 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900">Edit Data Barang</h3>
        <p class="mt-1 text-xs text-slate-500">Perbarui harga, stok, atau kategori produk ini.</p>

        <form @submit.prevent="submitEditBarang" class="mt-4 space-y-3">
          <div>
            <label class="mb-1 block text-xs font-bold text-slate-700">Kode Barang *</label>
            <input
              v-model="editForm.kode_barang"
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white"
              required
            />
          </div>
          <div>
            <label class="mb-1 block text-xs font-bold text-slate-700">Nama Barang *</label>
            <input
              v-model="editForm.nama_barang"
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white"
              required
            />
          </div>
          <div>
            <label class="mb-1 block text-xs font-bold text-slate-700">Kategori</label>
            <input
              v-model="editForm.kategori"
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white"
            />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="mb-1 block text-xs font-bold text-slate-700">Harga Modal (Rp)</label>
              <input
                v-model.number="editForm.harga_modal"
                type="number"
                min="0"
                class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white"
              />
            </div>
            <div>
              <label class="mb-1 block text-xs font-bold text-slate-700">Harga Jual (Rp) *</label>
              <input
                v-model.number="editForm.harga"
                type="number"
                min="1"
                class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white"
                required
              />
            </div>
          </div>
          <div>
            <label class="mb-1 block text-xs font-bold text-slate-700">Stok Barang *</label>
            <input
              v-model.number="editForm.stok"
              type="number"
              min="0"
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white"
              required
            />
          </div>

          <div class="mt-6 flex justify-end gap-2 pt-2">
            <button
              type="button"
              @click="tutupEditForm"
              class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="isSubmittingEdit"
              class="rounded-xl bg-indigo-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-500"
            >
              {{ isSubmittingEdit ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Tambah Banyak Barang Sekaligus (Bulk Add & Import CSV/Excel) -->
    <div
      v-if="showBulkModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-3 sm:p-4 animate-in fade-in duration-200"
      @click.self="tutupBulkModal"
    >
      <div class="w-full max-w-4xl max-h-[92vh] flex flex-col rounded-[28px] bg-white p-4 sm:p-6 shadow-2xl border border-slate-200">
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2.5">
            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 text-lg font-bold">
              📑
            </div>
            <div>
              <h3 class="text-base sm:text-lg font-bold text-slate-900">Tambah Banyak Barang Sekaligus</h3>
              <p class="text-xs text-slate-500">Input beberapa barang dalam tabel dinamis atau impor dari file CSV/Excel</p>
            </div>
          </div>
          <button
            @click="tutupBulkModal"
            class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition"
          >
            ✕
          </button>
        </div>

        <!-- Tab Selector: Multi-Baris vs Import CSV/Excel -->
        <div class="mt-4 flex rounded-2xl bg-slate-100 p-1 font-semibold text-xs text-slate-600">
          <button
            type="button"
            @click="bulkTab = 'tabel'"
            :class="bulkTab === 'tabel' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900'"
            class="flex-1 py-2 rounded-xl transition flex items-center justify-center gap-1.5"
          >
            <span>📝 Formulir Multi-Baris ({{ bulkRows.length }} baris)</span>
          </button>
          <button
            type="button"
            @click="bulkTab = 'csv'"
            :class="bulkTab === 'csv' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900'"
            class="flex-1 py-2 rounded-xl transition flex items-center justify-center gap-1.5"
          >
            <span>📊 Import CSV / Salin Spreadsheet</span>
          </button>
        </div>

        <!-- Body Scrollable -->
        <div class="mt-4 flex-1 overflow-y-auto pr-1">
          <!-- TAB 1: FORMULIR MULTI-BARIS -->
          <div v-show="bulkTab === 'tabel'" class="space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <div class="flex items-center gap-2">
                <button
                  type="button"
                  @click="tambahBarisBulk(1)"
                  class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-indigo-600 hover:bg-indigo-50 shadow-xs active:scale-95 transition"
                >
                  + Tambah 1 Baris
                </button>
                <button
                  type="button"
                  @click="tambahBarisBulk(5)"
                  class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-indigo-600 hover:bg-indigo-50 shadow-xs active:scale-95 transition"
                >
                  + Tambah 5 Baris
                </button>
              </div>
              <button
                type="button"
                @click="resetBarisBulk"
                class="text-xs text-rose-500 font-semibold hover:text-rose-600"
              >
                Reset Baris
              </button>
            </div>

            <!-- Tabel Baris Input -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
              <table class="min-w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                  <tr>
                    <th class="p-2.5 w-10 text-center">No</th>
                    <th class="p-2.5 min-w-[130px]">Kode Barang *</th>
                    <th class="p-2.5 min-w-[180px]">Nama Produk *</th>
                    <th class="p-2.5 min-w-[120px]">Kategori</th>
                    <th class="p-2.5 min-w-[110px]">Harga Modal</th>
                    <th class="p-2.5 min-w-[120px]">Harga Jual *</th>
                    <th class="p-2.5 min-w-[90px]">Stok *</th>
                    <th class="p-2.5 w-10 text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  <tr v-for="(row, idx) in bulkRows" :key="idx" class="hover:bg-slate-50/50">
                    <td class="p-2.5 text-center font-bold text-slate-400">{{ idx + 1 }}</td>
                    <td class="p-2">
                      <input
                        v-model="row.kode_barang"
                        placeholder="BRG001"
                        class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs font-mono outline-none focus:border-indigo-500"
                      />
                    </td>
                    <td class="p-2">
                      <input
                        v-model="row.nama_barang"
                        placeholder="Nama Produk"
                        class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs outline-none focus:border-indigo-500"
                      />
                    </td>
                    <td class="p-2">
                      <input
                        v-model="row.kategori"
                        placeholder="Kategori"
                        class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs outline-none focus:border-indigo-500"
                      />
                    </td>
                    <td class="p-2">
                      <input
                        v-model.number="row.harga_modal"
                        type="number"
                        min="0"
                        placeholder="0"
                        class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs outline-none focus:border-indigo-500"
                      />
                    </td>
                    <td class="p-2">
                      <input
                        v-model.number="row.harga"
                        type="number"
                        min="0"
                        placeholder="0"
                        class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs font-bold outline-none focus:border-indigo-500 text-indigo-700"
                      />
                    </td>
                    <td class="p-2">
                      <input
                        v-model.number="row.stok"
                        type="number"
                        min="0"
                        placeholder="0"
                        class="w-full rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs font-bold text-center outline-none focus:border-indigo-500"
                      />
                    </td>
                    <td class="p-2 text-center">
                      <button
                        type="button"
                        @click="hapusBarisBulk(idx)"
                        :disabled="bulkRows.length <= 1"
                        class="rounded p-1 text-slate-400 hover:text-rose-600 disabled:opacity-30"
                        title="Hapus baris"
                      >
                        🗑️
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- TAB 2: IMPORT CSV & PASTE SPREADSHEET -->
          <div v-show="bulkTab === 'csv'" class="space-y-4">
            <div class="rounded-2xl border border-dashed border-indigo-200 bg-indigo-50/50 p-4">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                  <h4 class="text-xs font-bold text-indigo-950">Format Kolom CSV / Spreadsheet</h4>
                  <p class="text-[11px] text-indigo-700 mt-0.5">
                    Urutan kolom: <code>kode_barang, nama_barang, kategori, harga_modal, harga_jual, stok</code>
                  </p>
                </div>
                <button
                  type="button"
                  @click="downloadTemplateCsv"
                  class="rounded-xl border border-indigo-200 bg-white px-3 py-1.5 text-xs font-bold text-indigo-700 hover:bg-indigo-50 shadow-xs flex items-center gap-1.5 shrink-0"
                >
                  <span>📥 Unduh Template CSV</span>
                </button>
              </div>
            </div>

            <!-- Upload File CSV -->
            <div>
              <label class="mb-1 block text-xs font-bold text-slate-700">Pilih File CSV:</label>
              <input
                type="file"
                accept=".csv,text/csv"
                @change="handleFileUpload"
                class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-3 file:py-1 file:text-xs file:font-bold file:text-white file:hover:bg-indigo-500 cursor-pointer"
              />
            </div>

            <!-- Atau Salin-Tempel dari Excel -->
            <div>
              <label class="mb-1 block text-xs font-bold text-slate-700">Atau Salin-Tempel (Paste) Teks dari Excel / Google Sheets:</label>
              <textarea
                v-model="pastedText"
                @input="parsePastedText"
                rows="4"
                placeholder="Contoh:
BRG001	Kopi Tubruk	Minuman	3000	5000	50
BRG002	Teh Manis	Minuman	2000	4000	40"
                class="w-full rounded-xl border border-slate-300 bg-slate-50 p-3 font-mono text-xs text-slate-800 outline-none focus:border-indigo-500 focus:bg-white"
              ></textarea>
              <p class="mt-1 text-[11px] text-slate-400">Pemisah kolom bisa berupa Tab (dari salinan Excel) atau tanda koma (CSV).</p>
            </div>

            <!-- Preview Data Terbaca -->
            <div v-if="parsedCsvItems.length > 0" class="space-y-2">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-800">Preview Data Terbaca ({{ parsedCsvItems.length }} item):</span>
                <span class="text-[11px] text-emerald-600 font-semibold">Siap diimpor</span>
              </div>
              <div class="max-h-48 overflow-y-auto overflow-x-auto rounded-xl border border-slate-200">
                <table class="min-w-full text-xs text-left">
                  <thead class="bg-slate-100 text-slate-600">
                    <tr>
                      <th class="p-2">Kode</th>
                      <th class="p-2">Nama Barang</th>
                      <th class="p-2">Kategori</th>
                      <th class="p-2 text-right">Modal</th>
                      <th class="p-2 text-right">Jual</th>
                      <th class="p-2 text-center">Stok</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-slate-100">
                    <tr v-for="(it, i) in parsedCsvItems" :key="i">
                      <td class="p-2 font-mono font-bold">{{ it.kode_barang }}</td>
                      <td class="p-2 font-semibold">{{ it.nama_barang }}</td>
                      <td class="p-2 text-slate-500">{{ it.kategori || '-' }}</td>
                      <td class="p-2 text-right">Rp {{ Number(it.harga_modal || 0).toLocaleString() }}</td>
                      <td class="p-2 text-right font-bold text-indigo-600">Rp {{ Number(it.harga || 0).toLocaleString() }}</td>
                      <td class="p-2 text-center font-bold">{{ it.stok }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-slate-100 pt-3">
          <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-700">
            <input
              type="checkbox"
              v-model="bulkUpdateIfExists"
              class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
            />
            <span class="font-medium">Perbarui data & tambahkan stok jika kode barang sudah ada (Opsi A)</span>
          </label>

          <div class="flex items-center gap-2 justify-end">
            <button
              type="button"
              @click="tutupBulkModal"
              :disabled="isSubmittingBulk"
              class="rounded-xl border border-slate-300 px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100"
            >
              Batal
            </button>
            <button
              type="button"
              @click="simpanBulkBarang"
              :disabled="isSubmittingBulk || totalItemsToSubmit === 0"
              class="rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-500 active:scale-95 disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-400 transition flex items-center gap-1.5"
            >
              <span>{{ isSubmittingBulk ? 'Menyimpan...' : `Simpan ${totalItemsToSubmit} Barang` }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuth } from '../composables/useAuth'
import { useToast } from '../composables/useToast'

definePageMeta({ middleware: 'auth' })

const { token, isKasir } = useAuth()
const runtimeConfig = useRuntimeConfig()
const apiBaseUrl = runtimeConfig.public.apiBaseUrl.replace(/\/+$/, '')
const { show: showToast } = useToast()

const barangList = ref([])
const searchKeyword = ref('')
const filterKategori = ref('all')
const filterStok = ref('all')
const sortBy = ref('nama_asc')
const showFormTambah = ref(false)

const resetBarangFilters = () => {
  searchKeyword.value = ''
  filterKategori.value = 'all'
  filterStok.value = 'all'
  sortBy.value = 'nama_asc'
}

// State Tambah Banyak Barang Sekaligus (Bulk Add & Import)
const showBulkModal = ref(false)
const bulkTab = ref('tabel') // 'tabel' | 'csv'
const bulkUpdateIfExists = ref(true) // Opsi A
const isSubmittingBulk = ref(false)
const pastedText = ref('')
const parsedCsvItems = ref([])

const buatBarisKosong = () => ({
  kode_barang: '',
  nama_barang: '',
  kategori: '',
  harga_modal: '',
  harga: '',
  stok: ''
})

const bulkRows = ref([
  buatBarisKosong(),
  buatBarisKosong(),
  buatBarisKosong()
])

const form = ref({
  kode_barang: '',
  nama_barang: '',
  kategori: '',
  harga_modal: 0,
  harga: '',
  stok: ''
})

const showEditForm = ref(false)
const editForm = ref({
  id: null,
  kode_barang: '',
  nama_barang: '',
  kategori: '',
  harga_modal: 0,
  harga: 0,
  stok: 0
})

// Restock Cepat State
const showRestockModal = ref(false)
const selectedRestockBarang = ref(null)
const restockJumlah = ref(10)
const isSubmittingRestock = ref(false)

const isSubmittingBarang = ref(false)
const isSubmittingEdit = ref(false)
const deletingBarangIds = ref([])

const totalStokUnit = computed(() => {
  return barangList.value.reduce((total, b) => total + (Number(b.stok) || 0), 0)
})

const stokMenipisCount = computed(() => {
  return barangList.value.filter(b => Number(b.stok || 0) <= 5).length
})

const kategoriList = computed(() => {
  const set = new Set()
  barangList.value.forEach(b => {
    if (b.kategori && String(b.kategori).trim() !== '') {
      set.add(b.kategori)
    }
  })
  return Array.from(set)
})

const filteredBarangList = computed(() => {
  const kw = searchKeyword.value.trim().toLowerCase()
  const kat = filterKategori.value

  const filtered = barangList.value.filter(item => {
    // 1. Filter Kategori
    const matchCategory = kat === 'all' || String(item.kategori || '').toLowerCase() === kat.toLowerCase()

    // 2. Filter Pencarian Nama / Kode Barang
    const matchSearch = !kw ||
      String(item.nama_barang || '').toLowerCase().includes(kw) ||
      String(item.kode_barang || '').toLowerCase().includes(kw)

    // 3. Filter Status Stok Fisik
    let matchStok = true
    const stok = Number(item.stok) || 0
    if (filterStok.value === 'safe') {
      matchStok = stok > 5
    } else if (filterStok.value === 'low') {
      matchStok = stok > 0 && stok <= 5
    } else if (filterStok.value === 'empty') {
      matchStok = stok <= 0
    }

    return matchCategory && matchSearch && matchStok
  })

  // 4. Urutkan Data (Sorting)
  return filtered.sort((a, b) => {
    if (sortBy.value === 'nama_asc') {
      return String(a.nama_barang || '').localeCompare(String(b.nama_barang || ''))
    }
    if (sortBy.value === 'nama_desc') {
      return String(b.nama_barang || '').localeCompare(String(a.nama_barang || ''))
    }
    if (sortBy.value === 'harga_asc') {
      return (Number(a.harga) || 0) - (Number(b.harga) || 0)
    }
    if (sortBy.value === 'harga_desc') {
      return (Number(b.harga) || 0) - (Number(a.harga) || 0)
    }
    if (sortBy.value === 'modal_desc') {
      return (Number(b.harga_modal) || 0) - (Number(a.harga_modal) || 0)
    }
    if (sortBy.value === 'margin_desc') {
      const marginA = Math.max(0, (Number(a.harga) || 0) - (Number(a.harga_modal) || 0))
      const marginB = Math.max(0, (Number(b.harga) || 0) - (Number(b.harga_modal) || 0))
      return marginB - marginA
    }
    if (sortBy.value === 'stok_desc') {
      return (Number(b.stok) || 0) - (Number(a.stok) || 0)
    }
    if (sortBy.value === 'stok_asc') {
      return (Number(a.stok) || 0) - (Number(b.stok) || 0)
    }
    return 0
  })
})

const loadBarang = async () => {
  try {
    barangList.value = await $fetch(`${apiBaseUrl}/barang`, {
      headers: { Authorization: `Bearer ${token.value}` }
    })
  } catch (err) {
    if (err.status === 401) {
      token.value = null
      navigateTo('/')
    } else {
      showToast('Gagal memuat data barang', 'error')
    }
  }
}

const simpanBarang = async () => {
  if (isSubmittingBarang.value) return

  if (Number(form.value.stok) < 0) {
    showToast('Stok tidak boleh bernilai negatif', 'warning')
    return
  }

  if (Number(form.value.harga) <= 0) {
    showToast('Harga jual harus lebih besar dari 0', 'warning')
    return
  }

  isSubmittingBarang.value = true

  try {
    await $fetch(`${apiBaseUrl}/barang`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` },
      body: form.value
    })

    showToast('Barang berhasil ditambahkan ke inventaris!', 'success')
    form.value = {
      kode_barang: '',
      nama_barang: '',
      kategori: '',
      harga_modal: 0,
      harga: '',
      stok: ''
    }
    await loadBarang()
  } catch (err) {
    console.error('Tambah barang error:', err)
    if (err.status === 422) {
      showToast('Kode barang sudah terdaftar. Gunakan kode lain.', 'error')
    } else {
      showToast(err.data?.message || 'Gagal menambahkan barang', 'error')
    }
  } finally {
    isSubmittingBarang.value = false
  }
}

// Buka Restock Modal
const bukaRestockModal = (barang) => {
  selectedRestockBarang.value = barang
  restockJumlah.value = 10
  showRestockModal.value = true
}

const submitRestock = async () => {
  if (!selectedRestockBarang.value || restockJumlah.value <= 0) return
  isSubmittingRestock.value = true

  try {
    const res = await $fetch(`${apiBaseUrl}/barang/${selectedRestockBarang.value.id}/tambah-stok`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` },
      body: { jumlah: restockJumlah.value }
    })

    showToast(res.message || 'Stok berhasil ditambahkan!', 'success')
    showRestockModal.value = false
    await loadBarang()
  } catch (err) {
    console.error('Restock error:', err)
    showToast(err.data?.message || 'Gagal menambahkan stok', 'error')
  } finally {
    isSubmittingRestock.value = false
  }
}

const bukaEditForm = (barang) => {
  editForm.value = {
    id: barang.id,
    kode_barang: barang.kode_barang,
    nama_barang: barang.nama_barang,
    kategori: barang.kategori || '',
    harga_modal: barang.harga_modal || 0,
    harga: barang.harga,
    stok: barang.stok
  }
  showEditForm.value = true
}

const tutupEditForm = () => {
  showEditForm.value = false
}

const submitEditBarang = async () => {
  if (isSubmittingEdit.value) return
  isSubmittingEdit.value = true

  try {
    await $fetch(`${apiBaseUrl}/barang/${editForm.value.id}`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token.value}` },
      body: editForm.value
    })

    showToast('Data barang berhasil diperbarui!', 'success')
    showEditForm.value = false
    await loadBarang()
  } catch (err) {
    console.error('Edit barang error:', err)
    if (err.status === 422) {
      showToast('Kode barang sudah digunakan oleh barang lain!', 'error')
    } else {
      showToast(err.data?.message || 'Gagal memperbarui barang', 'error')
    }
  } finally {
    isSubmittingEdit.value = false
  }
}

const hapusBarang = async (id) => {
  if (!confirm('Yakin ingin menghapus barang ini dari katalog?')) return

  deletingBarangIds.value.push(id)

  try {
    await $fetch(`${apiBaseUrl}/barang/${id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token.value}` }
    })

    showToast('Barang berhasil dihapus!', 'info')
    await loadBarang()
  } catch (err) {
    console.error('Hapus barang error:', err)
    showToast('Gagal menghapus barang', 'error')
  } finally {
    deletingBarangIds.value = deletingBarangIds.value.filter(x => x !== id)
  }
}

// Operasi Tambah Banyak Barang Sekaligus (Bulk Add & Import)
const bukaBulkModal = () => {
  if (bulkRows.value.length === 0) {
    bulkRows.value = [buatBarisKosong(), buatBarisKosong(), buatBarisKosong()]
  }
  showBulkModal.value = true
}

const tutupBulkModal = () => {
  showBulkModal.value = false
}

const tambahBarisBulk = (count = 1) => {
  for (let i = 0; i < count; i++) {
    bulkRows.value.push(buatBarisKosong())
  }
}

const hapusBarisBulk = (idx) => {
  bulkRows.value.splice(idx, 1)
}

const resetBarisBulk = () => {
  bulkRows.value = [buatBarisKosong(), buatBarisKosong(), buatBarisKosong()]
  pastedText.value = ''
  parsedCsvItems.value = []
}

// Menghitung baris valid dari formulir multi-baris
const validTableRows = computed(() => {
  return bulkRows.value.filter(r => 
    r.kode_barang && String(r.kode_barang).trim() !== '' &&
    r.nama_barang && String(r.nama_barang).trim() !== '' &&
    r.harga !== '' && !isNaN(Number(r.harga))
  )
})

const totalItemsToSubmit = computed(() => {
  if (bulkTab.value === 'tabel') {
    return validTableRows.value.length
  }
  return parsedCsvItems.value.length
})

// Parsing Teks dari Excel / CSV
const parseDelimitedText = (text) => {
  if (!text || typeof text !== 'string') return []
  const lines = text.trim().split(/\r?\n/)
  const result = []

  for (let i = 0; i < lines.length; i++) {
    const rawLine = lines[i].trim()
    if (!rawLine) continue

    let delimiter = '\t'
    if (rawLine.includes('\t')) delimiter = '\t'
    else if (rawLine.includes(';')) delimiter = ';'
    else if (rawLine.includes(',')) delimiter = ','

    const parts = rawLine.split(delimiter).map(p => p.trim().replace(/^["']|["']$/g, ''))
    
    // Abaikan jika baris header
    if (i === 0 && (parts[0].toLowerCase().includes('kode') || (parts[1] && parts[1].toLowerCase().includes('nama')))) {
      continue
    }

    if (parts.length >= 2) {
      const kode = parts[0] || ''
      const nama = parts[1] || ''
      const kategori = parts[2] || ''
      const modal = Number(parts[3]) || 0
      const harga = Number(parts[4]) || 0
      const stok = parts[5] !== undefined ? Number(parts[5]) : 0

      if (kode && nama && !isNaN(harga) && harga >= 0) {
        result.push({
          kode_barang: kode,
          nama_barang: nama,
          kategori: kategori || null,
          harga_modal: modal,
          harga: harga,
          stok: isNaN(stok) ? 0 : stok
        })
      }
    }
  }
  return result
}

const parsePastedText = () => {
  parsedCsvItems.value = parseDelimitedText(pastedText.value)
}

const handleFileUpload = (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  const reader = new FileReader()
  reader.onload = (e) => {
    const content = e.target?.result
    if (typeof content === 'string') {
      pastedText.value = content
      parsedCsvItems.value = parseDelimitedText(content)
      showToast(`${parsedCsvItems.value.length} baris barang terbaca dari file CSV.`, 'info')
    }
  }
  reader.readAsText(file)
}

const downloadTemplateCsv = () => {
  const csvContent = "data:text/csv;charset=utf-8," +
    "kode_barang,nama_barang,kategori,harga_modal,harga_jual,stok\n" +
    "BRG001,Kopi Tubruk 100gr,Minuman,3000,5000,50\n" +
    "BRG002,Teh Melati Celup,Minuman,2500,4000,30\n" +
    "BRG003,Biskuit Cokelat,Makanan,4500,7000,20\n"
  const encodedUri = encodeURI(csvContent)
  const link = document.createElement("a")
  link.setAttribute("href", encodedUri)
  link.setAttribute("download", "template_tambah_barang.csv")
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const simpanBulkBarang = async () => {
  let itemsToSave = []
  if (bulkTab.value === 'tabel') {
    itemsToSave = validTableRows.value.map(r => ({
      kode_barang: String(r.kode_barang).trim(),
      nama_barang: String(r.nama_barang).trim(),
      kategori: r.kategori ? String(r.kategori).trim() : null,
      harga_modal: Number(r.harga_modal) || 0,
      harga: Number(r.harga) || 0,
      stok: Number(r.stok) || 0
    }))
  } else {
    itemsToSave = parsedCsvItems.value
  }

  if (itemsToSave.length === 0) {
    showToast('Tidak ada data barang yang valid untuk disimpan!', 'warning')
    return
  }

  if (isSubmittingBulk.value) return
  isSubmittingBulk.value = true

  try {
    const res = await $fetch(`${apiBaseUrl}/barang/bulk`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` },
      body: {
        items: itemsToSave,
        update_if_exists: bulkUpdateIfExists.value
      }
    })

    const msg = res.message || `Berhasil memproses ${itemsToSave.length} barang!`
    showToast(msg, 'success')
    showBulkModal.value = false
    resetBarisBulk()
    await loadBarang()
  } catch (err) {
    console.error('Bulk store error:', err)
    showToast(err.data?.message || 'Gagal menambahkan banyak barang.', 'error')
  } finally {
    isSubmittingBulk.value = false
  }
}

onMounted(() => {
  if (!token.value) {
    navigateTo('/')
    return
  }
  loadBarang()
})
</script>