<template>
  <div>
    <!-- Statistik Rekap Kasir & Settlement Laci Kas (Anti-Gimmick) -->
    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-[22px] border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Uang Fisik di Laci</p>
          <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700">Tunai</span>
        </div>
        <p class="mt-2 text-2xl font-black text-slate-900">Rp {{ totalTunai.toLocaleString() }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Uang kas kasir yang wajib ada di laci</p>
      </div>

      <div class="rounded-[22px] border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Saldo Masuk QRIS</p>
          <span class="rounded-full bg-indigo-50 px-2.5 py-0.5 text-[10px] font-bold text-indigo-700">QRIS</span>
        </div>
        <p class="mt-2 text-2xl font-black text-indigo-600">Rp {{ totalQris.toLocaleString() }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Masuk ke e-wallet / merchant</p>
      </div>

      <div class="rounded-[22px] border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Saldo Bank / Debit</p>
          <span class="rounded-full bg-blue-50 px-2.5 py-0.5 text-[10px] font-bold text-blue-700">Bank EDC</span>
        </div>
        <p class="mt-2 text-2xl font-black text-blue-600">Rp {{ totalTransfer.toLocaleString() }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Rekening penampung toko</p>
      </div>

      <div class="rounded-[22px] border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Estimasi Laba Kotor</p>
          <span class="rounded-full bg-amber-50 px-2.5 py-0.5 text-[10px] font-bold text-amber-800">Margin</span>
        </div>
        <p class="mt-2 text-2xl font-black text-emerald-600">Rp {{ totalLabaKotor.toLocaleString() }}</p>
        <p class="text-[11px] text-slate-400 mt-0.5">Total Omzet - HPP Modal</p>
      </div>
    </div>

    <!-- Kontainer Riwayat Transaksi -->
    <div class="rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-4">
        <div>
          <h2 class="text-xl font-bold text-slate-900">Arsip Transaksi & Bukti Bayar</h2>
          <p class="text-xs text-slate-500">Pencatatan nota kasir, metode pembayaran, bank, dan nomor referensi</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
          <button
            @click="exportCsv"
            class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 active:scale-95 shadow-sm"
          >
            📥 Export CSV Lengkap
          </button>
          <button
            @click="loadRiwayat"
            class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200 active:scale-95"
          >
            🔄 Refresh Data
          </button>
        </div>
      </div>

      <!-- Filter Pencarian, Tanggal, & Metode Pembayaran -->
      <div class="mt-4 grid gap-3 sm:grid-cols-4">
        <div>
          <label class="mb-1 block text-xs font-bold text-slate-600">Cari Nota / Pelanggan</label>
          <input
            v-model="searchKeyword"
            type="text"
            placeholder="No nota, nama pelanggan, barang..."
            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white"
          />
        </div>
        <div>
          <label class="mb-1 block text-xs font-bold text-slate-600">Filter Tanggal</label>
          <input
            v-model="filterTanggal"
            type="date"
            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white"
          />
        </div>
        <div>
          <label class="mb-1 block text-xs font-bold text-slate-600">Metode Pembayaran</label>
          <select
            v-model="filterMetode"
            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white"
          >
            <option value="all">Semua Metode</option>
            <option value="tunai">💵 Tunai Saja</option>
            <option value="qris">📱 QRIS Saja</option>
            <option value="transfer">💳 Transfer / Debit Saja</option>
          </select>
        </div>
        <div>
          <label class="mb-1 block text-xs font-bold text-slate-600">Status Transaksi</label>
          <select
            v-model="filterStatus"
            class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3 py-2 text-xs text-slate-800 outline-none transition focus:border-indigo-500 focus:bg-white"
          >
            <option value="all">Semua Status</option>
            <option value="selesai">✓ Selesai (Lunas)</option>
            <option value="dibatalkan">❌ Dibatalkan (Void)</option>
          </select>
        </div>
      </div>

      <!-- List Kartu Transaksi -->
      <div v-if="filteredRiwayat.length === 0" class="mt-8 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-8 text-center text-sm text-slate-500">
        Tidak ada transaksi yang cocok dengan kriteria pencarian.
      </div>

      <div v-else class="mt-6 space-y-4">
        <div
          v-for="trx in filteredRiwayat"
          :key="trx.id || trx.noStruk"
          :class="trx.status === 'dibatalkan' ? 'border-rose-200 bg-rose-50/30 opacity-75' : 'border-slate-200 bg-slate-50/70'"
          class="rounded-[24px] border p-4 transition hover:bg-white hover:shadow-sm"
        >
          <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
              <div class="flex flex-wrap items-center gap-2">
                <span class="font-mono text-sm font-bold text-slate-900">{{ trx.noStruk }}</span>
                <span
                  :class="trx.status === 'dibatalkan' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800'"
                  class="rounded-full px-2.5 py-0.5 text-[10px] font-bold"
                >
                  {{ trx.status === 'dibatalkan' ? '❌ Dibatalkan (Void)' : '✓ Lunas' }}
                </span>
                <span
                  :class="trx.metode_pembayaran === 'tunai' ? 'bg-emerald-100 text-emerald-800' : trx.metode_pembayaran === 'qris' ? 'bg-indigo-100 text-indigo-800' : 'bg-blue-100 text-blue-800'"
                  class="rounded bg-slate-200 px-2 py-0.5 text-[10px] font-bold uppercase"
                >
                  {{ trx.metode_pembayaran }} {{ trx.bank ? `(${trx.bank})` : '' }}
                </span>
              </div>

              <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-1 text-xs text-slate-600">
                <div><span class="text-slate-400">Waktu:</span> {{ trx.tanggal }}</div>
                <div><span class="text-slate-400">Kasir:</span> {{ trx.kasir || 'Kasir' }}</div>
                <div><span class="text-slate-400">Pelanggan:</span> <span class="font-semibold text-slate-800">{{ trx.nama_pelanggan }}</span></div>
                <div v-if="trx.nomor_referensi" class="col-span-2 font-mono text-[11px] text-slate-500">
                  <span class="text-slate-400 font-sans">No. Ref:</span> {{ trx.nomor_referensi }}
                </div>
              </div>
            </div>

            <div class="text-left lg:text-right border-t lg:border-t-0 pt-2 lg:pt-0 border-slate-200">
              <div v-if="trx.diskon > 0" class="text-xs text-rose-600 font-semibold">
                Diskon: -Rp {{ Number(trx.diskon).toLocaleString() }}
              </div>
              <div class="text-base font-extrabold text-slate-900">Total: Rp {{ Number(trx.total || 0).toLocaleString() }}</div>
              <div class="text-xs text-emerald-600 font-semibold">Bayar: Rp {{ Number(trx.bayar || 0).toLocaleString() }}</div>
              <div class="text-xs text-indigo-600 font-semibold">Kembalian: Rp {{ Number(trx.kembalian || 0).toLocaleString() }}</div>

              <div class="mt-3 flex flex-wrap items-center justify-end gap-2">
                <button
                  @click="cetakRiwayat(trx)"
                  class="rounded-lg bg-slate-700 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-slate-600 active:scale-95 shadow-sm"
                >
                  🖨️ Cetak Ulang
                </button>
                <button
                  v-if="trx.status !== 'dibatalkan'"
                  @click="batalkanTransaksi(trx)"
                  :disabled="cancellingIds.includes(trx.id)"
                  class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-rose-500 active:scale-95 disabled:cursor-not-allowed disabled:bg-rose-300"
                >
                  {{ cancellingIds.includes(trx.id) ? 'Memproses...' : 'Batalkan (Void & Restock)' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Rincian Item Transaksi -->
          <div class="mt-3 rounded-xl border border-slate-200/70 bg-white p-3 text-xs text-slate-700">
            <p class="mb-1.5 font-bold text-slate-500">Rincian Barang Terjual:</p>
            <div class="grid gap-1 sm:grid-cols-2 lg:grid-cols-3">
              <div
                v-for="(item, idx) in trx.items"
                :key="idx"
                class="flex justify-between border-b border-slate-100 py-1 pr-2"
              >
                <span>{{ item.nama_barang }} × {{ item.qty }}</span>
                <span class="font-semibold text-slate-900">
                  Rp {{ (Number(item.harga || 0) * Number(item.qty || 0)).toLocaleString() }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Cetak Struk Riwayat -->
    <div v-if="showStruk" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 p-4">
      <div class="print-receipt-shell w-full max-w-sm rounded-[28px] bg-white p-6 shadow-2xl">
        <div class="border-b border-dashed border-slate-300 pb-3 text-center">
          <h3 class="text-lg font-bold text-slate-900">TOKO SEJAHTRA</h3>
          <p class="text-xs text-slate-500">Jl. Sejahtera No. 1</p>
          <p class="mt-1 text-[11px] text-slate-400">{{ strukData?.noStruk }} • {{ strukData?.tanggal }}</p>
          <div class="mt-1 flex justify-center gap-3 text-[11px] text-slate-600 font-medium">
            <span>Kasir: {{ strukData?.kasir }}</span>
            <span>Pelanggan: {{ strukData?.nama_pelanggan }}</span>
          </div>
          <span
            v-if="strukData?.status === 'dibatalkan'"
            class="mt-1.5 inline-block rounded bg-rose-100 px-2 py-0.5 text-[10px] font-bold text-rose-700"
          >
            TRANSAKSI TELAH DIBATALKAN (VOID)
          </span>
        </div>

        <div class="my-3 space-y-1.5 text-xs text-slate-700">
          <div v-for="(item, i) in strukData?.items || []" :key="i" class="flex justify-between">
            <span class="mr-2 flex-1 truncate">{{ item.nama_barang }} × {{ item.qty }}</span>
            <span class="font-semibold">Rp {{ (Number(item.harga || 0) * Number(item.qty || 0)).toLocaleString() }}</span>
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
            <span>Rp {{ Number(strukData?.total || 0).toLocaleString() }}</span>
          </div>
          <div class="flex justify-between">
            <span>Metode:</span>
            <span class="font-semibold uppercase">
              {{ strukData?.metode_pembayaran }} {{ strukData?.bank ? `(${strukData.bank})` : '' }}
            </span>
          </div>
          <div v-if="strukData?.nomor_referensi" class="flex justify-between font-mono text-[11px]">
            <span>Ref/RRN:</span>
            <span>{{ strukData?.nomor_referensi }}</span>
          </div>
          <div class="flex justify-between">
            <span>Bayar:</span>
            <span>Rp {{ Number(strukData?.bayar || 0).toLocaleString() }}</span>
          </div>
          <div class="flex justify-between font-bold text-emerald-600">
            <span>Kembali:</span>
            <span>Rp {{ Number(strukData?.kembalian || 0).toLocaleString() }}</span>
          </div>
        </div>

        <div class="mt-6 flex justify-between gap-2 print:hidden">
          <button
            @click="cetakStrukBrowser"
            class="flex-1 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white hover:bg-indigo-500"
          >
            🖨️ Cetak Sekarang
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
const runtimeConfig = useRuntimeConfig()
const apiBaseUrl = runtimeConfig.public.apiBaseUrl.replace(/\/+$/, '')
const { show: showToast } = useToast()

const riwayatTransaksi = ref([])
const searchKeyword = ref('')
const filterTanggal = ref('')
const filterStatus = ref('all')
const filterMetode = ref('all')

const showStruk = ref(false)
const strukData = ref(null)
const cancellingIds = ref([])

const loadRiwayat = async () => {
  try {
    let url = `${apiBaseUrl}/transaksi`
    const params = new URLSearchParams()
    if (filterTanggal.value) params.append('tanggal', filterTanggal.value)
    if (filterStatus.value && filterStatus.value !== 'all') params.append('status', filterStatus.value)
    if (filterMetode.value && filterMetode.value !== 'all') params.append('metode', filterMetode.value)
    const queryString = params.toString()
    if (queryString) url += `?${queryString}`

    riwayatTransaksi.value = await $fetch(url, {
      headers: { Authorization: `Bearer ${token.value}` }
    })
  } catch (err) {
    if (err.status === 401) {
      token.value = null
      navigateTo('/')
    } else {
      showToast('Gagal memuat riwayat transaksi', 'error')
    }
  }
}

const filteredRiwayat = computed(() => {
  const kw = searchKeyword.value.trim().toLowerCase()
  return riwayatTransaksi.value.filter(trx => {
    if (!kw) return true
    const matchNoStruk = String(trx.noStruk || '').toLowerCase().includes(kw)
    const matchPelanggan = String(trx.nama_pelanggan || '').toLowerCase().includes(kw)
    const matchRef = String(trx.nomor_referensi || '').toLowerCase().includes(kw)
    const matchItem = Array.isArray(trx.items) && trx.items.some(i =>
      String(i.nama_barang || '').toLowerCase().includes(kw) ||
      String(i.kode_barang || '').toLowerCase().includes(kw)
    )
    return matchNoStruk || matchPelanggan || matchRef || matchItem
  })
})

// REKAP LACI KASIR / FINANCIAL SETTLEMENT
const validTrx = computed(() => {
  return riwayatTransaksi.value.filter(t => t.status !== 'dibatalkan')
})

const totalTunai = computed(() => {
  return validTrx.value
    .filter(t => t.metode_pembayaran === 'tunai')
    .reduce((tot, t) => tot + (Number(t.total) || 0), 0)
})

const totalQris = computed(() => {
  return validTrx.value
    .filter(t => t.metode_pembayaran === 'qris')
    .reduce((tot, t) => tot + (Number(t.total) || 0), 0)
})

const totalTransfer = computed(() => {
  return validTrx.value
    .filter(t => t.metode_pembayaran === 'transfer')
    .reduce((tot, t) => tot + (Number(t.total) || 0), 0)
})

const totalLabaKotor = computed(() => {
  return validTrx.value.reduce((tot, t) => tot + (Number(t.laba_kotor) || 0), 0)
})

const batalkanTransaksi = async (trx) => {
  if (!confirm(`Batalkan transaksi ${trx.noStruk}? Stok barang akan otomatis dikembalikan ke inventaris.`)) {
    return
  }

  cancellingIds.value.push(trx.id)

  try {
    const res = await $fetch(`${apiBaseUrl}/transaksi/${trx.id}/batal`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` }
    })

    showToast(res.message || 'Transaksi berhasil dibatalkan dan stok dikembalikan!', 'success')
    await loadRiwayat()
  } catch (err) {
    console.error('Batal transaksi error:', err)
    showToast(err.data?.message || 'Gagal membatalkan transaksi', 'error')
  } finally {
    cancellingIds.value = cancellingIds.value.filter(x => x !== trx.id)
  }
}

const cetakRiwayat = (trx) => {
  strukData.value = trx
  showStruk.value = true
}

const cetakStrukBrowser = () => {
  if (typeof window !== 'undefined') {
    window.print()
  }
}

const exportCsv = () => {
  if (riwayatTransaksi.value.length === 0) {
    showToast('Tidak ada data transaksi untuk diexport', 'warning')
    return
  }

  const headers = ['No Struk', 'Tanggal', 'Kasir', 'Pelanggan', 'Metode', 'Bank', 'No Ref', 'Status', 'Subtotal', 'Diskon', 'Total', 'Bayar', 'Kembalian']
  const rows = riwayatTransaksi.value.map(t => [
    `"${t.noStruk}"`,
    `"${t.tanggal}"`,
    `"${t.kasir || 'Kasir'}"`,
    `"${t.nama_pelanggan || 'Pelanggan Umum'}"`,
    `"${t.metode_pembayaran || 'tunai'}"`,
    `"${t.bank || '-'}"`,
    `"${t.nomor_referensi || '-'}"`,
    `"${t.status || 'selesai'}"`,
    t.subtotal || t.total || 0,
    t.diskon || 0,
    t.total || 0,
    t.bayar || 0,
    t.kembalian || 0
  ])

  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(r => r.join(','))].join('\n')
  const encodedUri = encodeURI(csvContent)
  const link = document.createElement('a')
  link.setAttribute('href', encodedUri)
  link.setAttribute('download', `rekap-laci-kasir-${new Date().toISOString().slice(0,10)}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  showToast('Laporan CSV Kasir berhasil didownload!', 'success')
}

watch([filterTanggal, filterStatus, filterMetode], () => {
  loadRiwayat()
})

onMounted(() => {
  if (!token.value) {
    navigateTo('/')
    return
  }
  loadRiwayat()
})
</script>