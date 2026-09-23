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
        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="showFormTambah = !showFormTambah"
            class="rounded-xl bg-indigo-600 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-indigo-500 active:scale-95 flex items-center gap-1.5"
          >
            <span>{{ showFormTambah ? '✕ Tutup Form' : '+ Tambah Barang' }}</span>
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

      <!-- Filter Pencarian Cepat -->
      <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="relative w-full max-w-sm">
          <input
            v-model="searchKeyword"
            type="text"
            placeholder="Cari kode atau nama barang..."
            class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2 pl-3 pr-8 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
          />
          <span class="absolute right-2.5 top-2 text-slate-400">🔍</span>
        </div>
        <div class="flex items-center gap-2">
          <label class="text-xs text-slate-500 font-medium">Filter Kategori:</label>
          <select
            v-model="filterKategori"
            class="rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-800 outline-none"
          >
            <option value="all">Semua Kategori</option>
            <option v-for="kat in kategoriList" :key="kat" :value="kat">{{ kat }}</option>
          </select>
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
  </div>
</template>

<script setup>
import { useToast } from '../composables/useToast'

definePageMeta({ middleware: 'auth' })

const token = useCookie('token')
const runtimeConfig = useRuntimeConfig()
const apiBaseUrl = runtimeConfig.public.apiBaseUrl.replace(/\/+$/, '')
const { show: showToast } = useToast()

const barangList = ref([])
const searchKeyword = ref('')
const filterKategori = ref('all')
const showFormTambah = ref(false)

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

  return barangList.value.filter(item => {
    const matchCategory = kat === 'all' || String(item.kategori || '').toLowerCase() === kat.toLowerCase()
    const matchSearch = !kw ||
      String(item.nama_barang || '').toLowerCase().includes(kw) ||
      String(item.kode_barang || '').toLowerCase().includes(kw)

    return matchCategory && matchSearch
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

onMounted(() => {
  if (!token.value) {
    navigateTo('/')
    return
  }
  loadBarang()
})
</script>