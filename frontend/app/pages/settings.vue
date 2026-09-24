<template>
  <div class="space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <div class="flex items-center gap-2">
          <span class="text-2xl">⚙️</span>
          <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
            Pengaturan Toko & Struk
          </h1>
          <span class="rounded-full bg-amber-500/10 border border-amber-400/30 px-2.5 py-0.5 text-[10px] font-extrabold text-amber-700 uppercase">
            Admin Only
          </span>
        </div>
        <p class="mt-1 text-xs sm:text-sm text-slate-500">
          Ubah nama toko, alamat, kontak telepon, logo usaha, dan pesan footer pada struk belanja kasir.
        </p>
      </div>

      <!-- Tombol Aksi Header -->
      <div class="flex items-center gap-2">
        <button
          type="button"
          @click="resetForm"
          :disabled="isSaving"
          class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs sm:text-sm font-semibold text-slate-600 hover:bg-slate-50 active:scale-95 transition disabled:opacity-50"
        >
          <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
          <span>Reset</span>
        </button>

        <button
          type="button"
          @click="handleSave"
          :disabled="isSaving"
          class="flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-lg shadow-indigo-600/30 hover:bg-indigo-500 active:scale-95 transition disabled:opacity-50"
        >
          <svg v-if="isSaving" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <span>{{ isSaving ? 'Menyimpan...' : 'Simpan Pengaturan' }}</span>
        </button>
      </div>
    </div>

    <!-- Alert jika bukan Admin -->
    <div
      v-if="!isAdmin"
      class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs sm:text-sm text-rose-800 flex items-center gap-3 shadow-sm"
    >
      <span class="text-xl">⛔</span>
      <div>
        <p class="font-bold">Akses Terbatas</p>
        <p>Halaman ini hanya dapat diubah oleh Administrator sistem kasir.</p>
      </div>
    </div>

    <!-- Konten Utama: 2 Kolom (Form & Live Receipt Preview) -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Kolom Kiri: Form Pengaturan (7 cols) -->
      <div class="lg:col-span-7 space-y-6">
        <!-- Kartu 1: Identitas Toko -->
        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm">
          <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 font-bold text-lg">
              🏪
            </div>
            <div>
              <h2 class="text-base font-bold text-slate-800">Identitas & Kontak Toko</h2>
              <p class="text-xs text-slate-500">Nama toko akan muncul di header aplikasi, struk, dan laporan.</p>
            </div>
          </div>

          <div class="mt-5 space-y-4">
            <!-- Nama Toko -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Nama Toko <span class="text-rose-500">*</span>
              </label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                  🏪
                </span>
                <input
                  v-model="form.nama_toko"
                  type="text"
                  placeholder="Contoh: TOKO SEJAHTRA / TOKO MAJU JAYA"
                  maxlength="100"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 font-semibold"
                  required
                />
              </div>
              <p class="mt-1 text-[11px] text-slate-400">
                Nama ini menggantikan tampilan nama toko di navigasi atas, sidebar, dan header struk.
              </p>
            </div>

            <!-- Nomor Telepon -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Nomor Telepon / WhatsApp
              </label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                  📞
                </span>
                <input
                  v-model="form.telepon"
                  type="text"
                  placeholder="Contoh: 0812-3456-7890 / (021) 555-1234"
                  maxlength="50"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100"
                />
              </div>
            </div>

            <!-- Alamat Toko -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Alamat Toko Lengkap
              </label>
              <div class="relative">
                <textarea
                  v-model="form.alamat"
                  rows="3"
                  placeholder="Contoh: Jl. Sudirman No. 45, RT 02 / RW 05, Jakarta Pusat"
                  maxlength="255"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 placeholder-slate-400 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100 resize-none"
                ></textarea>
              </div>
            </div>

            <!-- Catatan Footer Struk -->
            <div>
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                Pesan Footer Struk Belanja
              </label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                  🧾
                </span>
                <input
                  v-model="form.footer_struk"
                  type="text"
                  placeholder="Contoh: Terima kasih atas kunjungan Anda!"
                  maxlength="255"
                  class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 transition focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-100"
                />
              </div>
              <p class="mt-1 text-[11px] text-slate-400">
                Pesan ini tercetak di bagian paling bawah struk thermal pelanggan.
              </p>
            </div>
          </div>
        </div>

        <!-- Kartu 2: Logo Toko -->
        <div class="rounded-3xl border border-slate-200/80 bg-white p-5 sm:p-6 shadow-sm">
          <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 font-bold text-lg">
              🖼️
            </div>
            <div>
              <h2 class="text-base font-bold text-slate-800">Logo Toko / Usaha</h2>
              <p class="text-xs text-slate-500">Mendukung format PNG, JPG, JPEG, SVG, atau WebP (Disarankan rasio 1:1).</p>
            </div>
          </div>

          <div class="mt-5 space-y-4">
            <div class="flex flex-col sm:flex-row items-center gap-5">
              <!-- Kotak Pratinjau Logo -->
              <div class="relative flex h-28 w-28 shrink-0 items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 p-2 shadow-inner group">
                <img
                  v-if="form.logo"
                  :src="form.logo"
                  alt="Logo Toko"
                  class="h-full w-full object-contain rounded-xl"
                />
                <div v-else class="text-center text-slate-400">
                  <span class="text-3xl block">🛒</span>
                  <span class="text-[10px] font-medium">Belum Ada Logo</span>
                </div>

                <!-- Tombol Hapus Logo jika ada -->
                <button
                  v-if="form.logo"
                  type="button"
                  @click="removeLogo"
                  title="Hapus Logo"
                  class="absolute -top-2 -right-2 h-7 w-7 rounded-full bg-rose-500 text-white flex items-center justify-center shadow-md hover:bg-rose-600 transition"
                >
                  ✕
                </button>
              </div>

              <!-- Input File & Panduan -->
              <div class="flex-1 space-y-2 text-center sm:text-left">
                <div>
                  <input
                    ref="fileInputRef"
                    type="file"
                    accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml"
                    @change="handleFileUpload"
                    class="hidden"
                  />
                  <button
                    type="button"
                    @click="$refs.fileInputRef?.click()"
                    class="rounded-xl border border-indigo-200 bg-indigo-50/70 px-4 py-2.5 text-xs font-bold text-indigo-700 hover:bg-indigo-100 active:scale-95 transition inline-flex items-center gap-2"
                  >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Pilih Berkas Logo</span>
                  </button>
                  <button
                    v-if="form.logo"
                    type="button"
                    @click="removeLogo"
                    class="ml-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 active:scale-95 transition inline-flex items-center gap-1.5"
                  >
                    <span>Hapus Logo</span>
                  </button>
                </div>
                <p class="text-[11px] text-slate-400 leading-normal">
                  Ukuran maksimal yang disarankan 1 MB. Gambar disimpan langsung dan otomatis disinkronkan ke struk kasir & bilah navigasi.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Kolom Kanan: Pratinjau Struk Realtime (5 cols) -->
      <div class="lg:col-span-5">
        <div class="sticky top-6 space-y-4">
          <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
              <span>🧾</span>
              <span>Pratinjau Struk Belanja Kasir</span>
            </h3>
            <span class="rounded bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-600 border border-emerald-200">
              Live Preview
            </span>
          </div>

          <!-- Kertas Struk Simulasi Thermal -->
          <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xl relative overflow-hidden">
            <!-- Gerigi atas struk (estetika kertas kasir) -->
            <div class="absolute top-0 inset-x-0 h-1.5 bg-[radial-gradient(circle,_transparent_3px,_#f1f5f9_3px)] [background-size:10px_10px] opacity-60"></div>

            <div class="space-y-4 font-mono text-xs">
              <!-- Header Struk -->
              <div class="text-center border-b border-dashed border-slate-300 pb-4">
                <!-- Logo di Struk -->
                <div v-if="form.logo" class="flex justify-center mb-2">
                  <img :src="form.logo" alt="Logo Struk" class="h-12 w-12 object-contain" />
                </div>
                <div v-else class="text-xl mb-1">🛒</div>

                <h4 class="text-base font-extrabold text-slate-900 tracking-tight font-sans uppercase">
                  {{ form.nama_toko || 'NAMA TOKO ANDA' }}
                </h4>
                <p class="text-[11px] text-slate-500 font-sans mt-0.5 whitespace-pre-line">
                  {{ form.alamat || 'Alamat toko belum diatur' }}
                </p>
                <p v-if="form.telepon" class="text-[11px] text-slate-500 font-sans">
                  Telp: {{ form.telepon }}
                </p>

                <div class="mt-2.5 pt-2 border-t border-slate-100 flex justify-between text-[10px] text-slate-400 font-sans">
                  <span>STR-20260924-001</span>
                  <span>24/09/2026 20:30</span>
                </div>
                <div class="flex justify-between text-[10px] text-slate-500 font-sans">
                  <span>Kasir: Admin Toko</span>
                  <span>Pelanggan: Umum</span>
                </div>
              </div>

              <!-- Daftar Item Simulasi -->
              <div class="space-y-1.5 text-slate-700 py-1">
                <div class="flex justify-between">
                  <span>Beras Premium 5kg × 1</span>
                  <span>Rp 75.000</span>
                </div>
                <div class="flex justify-between">
                  <span>Minyak Goreng 2L × 2</span>
                  <span>Rp 68.000</span>
                </div>
                <div class="flex justify-between">
                  <span>Gula Pasir 1kg × 1</span>
                  <span>Rp 16.500</span>
                </div>
              </div>

              <!-- Ringkasan Total & Pembayaran -->
              <div class="border-t border-dashed border-slate-300 pt-3 space-y-1 text-slate-700">
                <div class="flex justify-between text-slate-500">
                  <span>Subtotal</span>
                  <span>Rp 159.500</span>
                </div>
                <div class="flex justify-between text-rose-600 font-semibold">
                  <span>Diskon Promo</span>
                  <span>- Rp 9.500</span>
                </div>
                <div class="flex justify-between font-bold text-sm text-slate-900 pt-1 border-t border-slate-100">
                  <span>TOTAL TAGIHAN</span>
                  <span>Rp 150.000</span>
                </div>
                <div class="flex justify-between pt-1">
                  <span>Metode</span>
                  <span class="font-bold uppercase">QRIS / TUNAI</span>
                </div>
                <div class="flex justify-between">
                  <span>Bayar</span>
                  <span>Rp 150.000</span>
                </div>
                <div class="flex justify-between font-bold text-emerald-600">
                  <span>Kembali</span>
                  <span>Rp 0</span>
                </div>
              </div>

              <!-- Footer Struk -->
              <div class="border-t border-dashed border-slate-300 pt-3 text-center text-[10px] text-slate-500 font-sans">
                <p class="font-medium">{{ form.footer_struk || 'Terima kasih atas kunjungan Anda!' }}</p>
                <p class="text-slate-400 mt-1">Barang yang dibeli tidak dapat ditukar</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuth } from '../composables/useAuth'
import { useToast } from '../composables/useToast'
import { useToko } from '../composables/useToko'

const { token, isAdmin } = useAuth()
const { show: showToast } = useToast()
const { setting, loadSetting, saveSetting } = useToko()

const isSaving = ref(false)
const fileInputRef = ref(null)

const form = reactive({
  nama_toko: '',
  alamat: '',
  telepon: '',
  logo: null,
  footer_struk: ''
})

// Isi formulir berdasarkan data tersimpan
const populateForm = () => {
  form.nama_toko = setting.value?.nama_toko || 'TOKO SEJAHTRA'
  form.alamat = setting.value?.alamat || ''
  form.telepon = setting.value?.telepon || ''
  form.logo = setting.value?.logo || null
  form.footer_struk = setting.value?.footer_struk || 'Terima kasih atas kunjungan Anda!'
}

onMounted(async () => {
  await loadSetting()
  populateForm()
})

// Pantau jika data setting berubah di background
watch(setting, () => {
  populateForm()
}, { deep: true })

const resetForm = () => {
  populateForm()
  showToast('Pengaturan telah di-reset ke nilai tersimpan.', 'info')
}

// Handler upload file gambar logo toko ke Base64
const handleFileUpload = (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  // Validasi ukuran berkas (maksimal 2MB)
  if (file.size > 2 * 1024 * 1024) {
    showToast('Ukuran gambar terlalu besar! Maksimal 2 MB.', 'error')
    if (fileInputRef.value) fileInputRef.value.value = ''
    return
  }

  // Validasi tipe berkas
  if (!file.type.startsWith('image/')) {
    showToast('Berkas harus berupa gambar (PNG, JPG, SVG, WebP)!', 'error')
    if (fileInputRef.value) fileInputRef.value.value = ''
    return
  }

  const reader = new FileReader()
  reader.onload = (e) => {
    form.logo = e.target?.result || null
    showToast('Logo berhasil dimuat ke pratinjau. Klik simpan untuk menerapkan.', 'info')
  }
  reader.onerror = () => {
    showToast('Gagal membaca berkas gambar.', 'error')
  }
  reader.readAsDataURL(file)
}

const removeLogo = () => {
  form.logo = null
  if (fileInputRef.value) fileInputRef.value.value = ''
  showToast('Logo dihapus dari pratinjau. Klik simpan untuk menerapkan.', 'info')
}

// Simpan formulir ke backend API
const handleSave = async () => {
  if (!form.nama_toko || !form.nama_toko.trim()) {
    showToast('Nama toko tidak boleh kosong!', 'error')
    return
  }

  try {
    isSaving.value = true
    const payload = {
      nama_toko: form.nama_toko.trim(),
      alamat: form.alamat ? form.alamat.trim() : '',
      telepon: form.telepon ? form.telepon.trim() : '',
      logo: form.logo || null,
      footer_struk: form.footer_struk ? form.footer_struk.trim() : 'Terima kasih atas kunjungan Anda!'
    }

    const res = await saveSetting(payload, token.value)
    showToast(res.message || 'Pengaturan profil toko berhasil disimpan!', 'success')
  } catch (err) {
    console.error('Gagal menyimpan pengaturan toko:', err)
    showToast(err.data?.message || 'Gagal menyimpan pengaturan toko ke server.', 'error')
  } finally {
    isSaving.value = false
  }
}
</script>
