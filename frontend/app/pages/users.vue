<template>
  <div>
    <!-- Kartu Statistik Ringkasan Pengguna -->
    <div class="mb-4 sm:mb-6 grid grid-cols-3 gap-2 sm:gap-4">
      <div class="rounded-2xl sm:rounded-[22px] border border-slate-200 bg-white p-3 sm:p-5 shadow-sm">
        <p class="text-[10px] sm:text-sm font-medium text-slate-500">Total Akun</p>
        <div class="mt-1 sm:mt-3 flex items-end justify-between">
          <div>
            <p class="text-base sm:text-2xl font-bold text-slate-900">{{ users.length }}</p>
            <p class="hidden sm:block text-xs text-slate-500">Pengguna terdaftar</p>
          </div>
          <span class="rounded-full bg-indigo-50 px-2 py-0.5 sm:px-3 sm:py-1 text-[10px] sm:text-xs font-semibold text-indigo-700">Semua</span>
        </div>
      </div>

      <div class="rounded-2xl sm:rounded-[22px] border border-slate-200 bg-white p-3 sm:p-5 shadow-sm">
        <p class="text-[10px] sm:text-sm font-medium text-slate-500">Admin</p>
        <div class="mt-1 sm:mt-3 flex items-end justify-between">
          <div>
            <p class="text-base sm:text-2xl font-bold text-amber-600">{{ adminCount }}</p>
            <p class="hidden sm:block text-xs text-slate-500">Hak akses penuh</p>
          </div>
          <span class="rounded-full bg-amber-50 px-2 py-0.5 sm:px-3 sm:py-1 text-[10px] sm:text-xs font-semibold text-amber-700">Admin</span>
        </div>
      </div>

      <div class="rounded-2xl sm:rounded-[22px] border border-slate-200 bg-white p-3 sm:p-5 shadow-sm">
        <p class="text-[10px] sm:text-sm font-medium text-slate-500">Staff Kasir</p>
        <div class="mt-1 sm:mt-3 flex items-end justify-between">
          <div>
            <p class="text-base sm:text-2xl font-bold text-emerald-600">{{ kasirCount }}</p>
            <p class="hidden sm:block text-xs text-slate-500">Operasional POS</p>
          </div>
          <span class="rounded-full bg-emerald-50 px-2 py-0.5 sm:px-3 sm:py-1 text-[10px] sm:text-xs font-semibold text-emerald-700">Kasir</span>
        </div>
      </div>
    </div>

    <!-- Toolbar: Search, Filter Role, & Tombol Tambah User -->
    <div class="mb-4 sm:mb-6 rounded-2xl sm:rounded-[22px] border border-slate-200 bg-white p-3.5 sm:p-5 shadow-sm">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 flex-col gap-2.5 sm:flex-row sm:items-center">
          <!-- Input Pencarian -->
          <div class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 text-sm">🔍</span>
            <input
              v-model="searchKeyword"
              type="text"
              placeholder="Cari pengguna (nama / email)..."
              class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 pl-9 pr-3 text-xs sm:text-sm text-slate-800 placeholder-slate-400 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
            />
            <button
              v-if="searchKeyword"
              @click="searchKeyword = ''"
              class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 text-xs"
            >
              ✕
            </button>
          </div>

          <!-- Filter Role Chips -->
          <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
            <button
              v-for="f in roleFilters"
              :key="f.key"
              @click="activeRoleFilter = f.key"
              :class="activeRoleFilter === f.key ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
              class="rounded-xl px-3 py-2 text-xs font-semibold whitespace-nowrap transition"
            >
              {{ f.label }}
            </button>
          </div>
        </div>

        <!-- Tombol Tambah Pengguna -->
        <div class="flex items-center gap-2">
          <button
            @click="loadUsers"
            :disabled="isLoading"
            title="Muat ulang data"
            class="rounded-xl border border-slate-200 bg-slate-50 p-2.5 text-slate-600 hover:bg-slate-100 active:scale-95 transition"
          >
            <span :class="{ 'animate-spin inline-block': isLoading }">🔄</span>
          </button>
          <button
            @click="openModalTambah"
            class="flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm transition hover:bg-indigo-500 active:scale-95 whitespace-nowrap"
          >
            <span>➕</span>
            <span>Tambah User</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Tabel Daftar Pengguna -->
    <div class="overflow-hidden rounded-2xl sm:rounded-[22px] border border-slate-200 bg-white shadow-sm">
      <div v-if="isLoading" class="p-12 text-center text-slate-500">
        <span class="animate-spin inline-block text-2xl mb-2">🔄</span>
        <p class="text-sm font-medium">Memuat daftar pengguna...</p>
      </div>

      <div v-else-if="filteredUsers.length === 0" class="p-12 text-center text-slate-500">
        <span class="text-4xl mb-2 inline-block">👤</span>
        <p class="text-sm font-semibold text-slate-700">Tidak ada pengguna yang cocok</p>
        <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter role.</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
              <th class="py-3.5 pl-4 sm:pl-6 pr-3">Pengguna</th>
              <th class="py-3.5 px-3">Email</th>
              <th class="py-3.5 px-3">Role</th>
              <th class="py-3.5 px-3 hidden md:table-cell">Terdaftar Sejak</th>
              <th class="py-3.5 pr-4 sm:pr-6 pl-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs sm:text-sm text-slate-700">
            <tr
              v-for="u in filteredUsers"
              :key="u.id"
              class="hover:bg-slate-50/60 transition"
            >
              <!-- Info Pengguna & Avatar -->
              <td class="py-3.5 pl-4 sm:pl-6 pr-3">
                <div class="flex items-center gap-3">
                  <div
                    :class="u.role === 'admin' ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-emerald-100 text-emerald-700 border-emerald-200'"
                    class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-xl border text-xs sm:text-sm font-bold shrink-0"
                  >
                    {{ getInitials(u.name) }}
                  </div>
                  <div>
                    <div class="flex items-center gap-1.5">
                      <span class="font-bold text-slate-900">{{ u.name }}</span>
                      <span
                        v-if="u.id === currentUser?.id"
                        class="rounded-full bg-indigo-50 px-2 py-0.2 text-[10px] font-bold text-indigo-600 border border-indigo-200"
                      >
                        Anda
                      </span>
                    </div>
                    <span class="text-[11px] text-slate-400">ID: #{{ u.id }}</span>
                  </div>
                </div>
              </td>

              <!-- Email -->
              <td class="py-3.5 px-3">
                <span class="font-mono text-xs text-slate-600">{{ u.email }}</span>
              </td>

              <!-- Role Badge -->
              <td class="py-3.5 px-3">
                <span
                  :class="u.role === 'admin'
                    ? 'bg-amber-50 text-amber-700 border-amber-300'
                    : 'bg-emerald-50 text-emerald-700 border-emerald-300'"
                  class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider"
                >
                  <span
                    :class="u.role === 'admin' ? 'bg-amber-500' : 'bg-emerald-500'"
                    class="h-1.5 w-1.5 rounded-full"
                  ></span>
                  <span>{{ u.role }}</span>
                </span>
              </td>

              <!-- Tanggal Terdaftar -->
              <td class="py-3.5 px-3 hidden md:table-cell text-xs text-slate-500">
                {{ formatDate(u.created_at) }}
              </td>

              <!-- Tombol Aksi -->
              <td class="py-3.5 pr-4 sm:pr-6 pl-3 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <!-- Tombol Edit & Ubah Role -->
                  <button
                    @click="openModalEdit(u)"
                    title="Edit Profil & Ubah Role"
                    class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 active:scale-95 transition"
                  >
                    ✏️ <span class="hidden sm:inline">Edit</span>
                  </button>

                  <!-- Tombol Reset Password -->
                  <button
                    @click="openModalResetPassword(u)"
                    title="Reset Password Akun"
                    class="rounded-lg border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-100 active:scale-95 transition"
                  >
                    🔑 <span class="hidden sm:inline">Reset</span>
                  </button>

                  <!-- Tombol Hapus -->
                  <button
                    @click="hapusUser(u)"
                    :disabled="u.id === currentUser?.id"
                    :title="u.id === currentUser?.id ? 'Tidak dapat menghapus akun Anda sendiri' : 'Hapus pengguna'"
                    class="rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100 active:scale-95 disabled:cursor-not-allowed disabled:opacity-40 transition"
                  >
                    🗑️ <span class="hidden sm:inline">Hapus</span>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Tambah Pengguna Baru -->
    <div v-if="showModalTambah" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
      <div class="w-full max-w-md rounded-2xl sm:rounded-[26px] bg-white p-5 sm:p-7 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2.5">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 text-lg">➕</span>
            <div>
              <h3 class="text-base font-bold text-slate-900">Tambah Pengguna Baru</h3>
              <p class="text-xs text-slate-500">Buat akun admin atau staf kasir baru.</p>
            </div>
          </div>
          <button @click="showModalTambah = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>

        <form @submit.prevent="simpanTambahUser" class="mt-4 space-y-3.5">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
            <input
              v-model="formTambah.name"
              type="text"
              placeholder="Contoh: Budi Santoso"
              required
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
            <input
              v-model="formTambah.email"
              type="email"
              placeholder="contoh: kasir1@toko.com"
              required
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Role Akun</label>
            <select
              v-model="formTambah.role"
              required
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
            >
              <option value="kasir">Staff Kasir (Akses Kasir & Riwayat Transaksi)</option>
              <option value="admin">Administrator (Akses Penuh Termasuk Barang & User)</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Password (Minimal 6 karakter)</label>
            <input
              v-model="formTambah.password"
              type="password"
              placeholder="••••••••"
              minlength="6"
              required
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
            />
          </div>

          <div class="mt-5 flex gap-2.5 pt-2">
            <button
              type="button"
              @click="showModalTambah = false"
              class="flex-1 rounded-xl border border-slate-300 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="flex-1 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-500 active:scale-95 disabled:bg-indigo-300"
            >
              {{ isSubmitting ? 'Menyimpan...' : 'Simpan User' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Edit Pengguna & Ubah Role -->
    <div v-if="showModalEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
      <div class="w-full max-w-md rounded-2xl sm:rounded-[26px] bg-white p-5 sm:p-7 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2.5">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 text-lg">✏️</span>
            <div>
              <h3 class="text-base font-bold text-slate-900">Edit Data & Role</h3>
              <p class="text-xs text-slate-500">Perbarui profil atau ubah role pengguna.</p>
            </div>
          </div>
          <button @click="showModalEdit = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>

        <form @submit.prevent="simpanEditUser" class="mt-4 space-y-3.5">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
            <input
              v-model="formEdit.name"
              type="text"
              required
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
            <input
              v-model="formEdit.email"
              type="email"
              required
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Ubah Role</label>
            <select
              v-model="formEdit.role"
              :disabled="formEdit.id === currentUser?.id"
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100 disabled:bg-slate-100 disabled:text-slate-400"
            >
              <option value="kasir">Staff Kasir (Akses Kasir & Riwayat)</option>
              <option value="admin">Administrator (Akses Penuh POS & User)</option>
            </select>
            <p v-if="formEdit.id === currentUser?.id" class="mt-1 text-[11px] text-amber-600">
              *Anda tidak dapat mengubah role akun Anda sendiri saat sedang aktif.
            </p>
          </div>

          <div class="mt-5 flex gap-2.5 pt-2">
            <button
              type="button"
              @click="showModalEdit = false"
              class="flex-1 rounded-xl border border-slate-300 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="flex-1 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-500 active:scale-95 disabled:bg-indigo-300"
            >
              {{ isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Reset Password -->
    <div v-if="showModalReset" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
      <div class="w-full max-w-md rounded-2xl sm:rounded-[26px] bg-white p-5 sm:p-7 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2.5">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 text-lg">🔑</span>
            <div>
              <h3 class="text-base font-bold text-slate-900">Reset Password User</h3>
              <p class="text-xs text-slate-500">Ganti password akun {{ targetUser?.name }}.</p>
            </div>
          </div>
          <button @click="showModalReset = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
        </div>

        <form @submit.prevent="simpanResetPassword" class="mt-4 space-y-3.5">
          <div class="rounded-xl bg-slate-50 p-3 border border-slate-200 text-xs">
            <p class="font-bold text-slate-800">{{ targetUser?.name }}</p>
            <p class="text-slate-500">{{ targetUser?.email }} • Role: {{ targetUser?.role }}</p>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Password Baru (Minimal 6 karakter)</label>
            <input
              v-model="formReset.password"
              type="password"
              placeholder="••••••••"
              minlength="6"
              required
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Password Baru</label>
            <input
              v-model="formReset.confirmPassword"
              type="password"
              placeholder="••••••••"
              minlength="6"
              required
              class="w-full rounded-xl border border-slate-300 bg-slate-50 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 outline-none focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100"
            />
          </div>

          <div class="mt-5 flex gap-2.5 pt-2">
            <button
              type="button"
              @click="showModalReset = false"
              class="flex-1 rounded-xl border border-slate-300 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="isSubmitting"
              class="flex-1 rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-amber-500 active:scale-95 disabled:bg-amber-300"
            >
              {{ isSubmitting ? 'Mereset...' : 'Reset Password' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuth } from '../composables/useAuth'
import { useToast } from '../composables/useToast'

definePageMeta({ middleware: 'auth' })

const { token, user: currentUser, isAdmin } = useAuth()
const runtimeConfig = useRuntimeConfig()
const apiBaseUrl = runtimeConfig.public.apiBaseUrl.replace(/\/+$/, '')
const { show: showToast } = useToast()

const users = ref([])
const isLoading = ref(false)
const isSubmitting = ref(false)
const searchKeyword = ref('')
const activeRoleFilter = ref('all')

const roleFilters = [
  { key: 'all', label: 'Semua Role' },
  { key: 'admin', label: 'Admin' },
  { key: 'kasir', label: 'Kasir' },
]

// Modal States
const showModalTambah = ref(false)
const showModalEdit = ref(false)
const showModalReset = ref(false)
const targetUser = ref(null)

const formTambah = ref({
  name: '',
  email: '',
  password: '',
  role: 'kasir'
})

const formEdit = ref({
  id: null,
  name: '',
  email: '',
  role: 'kasir'
})

const formReset = ref({
  password: '',
  confirmPassword: ''
})

const adminCount = computed(() => users.value.filter(u => u.role === 'admin').length)
const kasirCount = computed(() => users.value.filter(u => u.role === 'kasir').length)

const filteredUsers = computed(() => {
  return users.value.filter(u => {
    const matchSearch =
      searchKeyword.value === '' ||
      (u.name && u.name.toLowerCase().includes(searchKeyword.value.toLowerCase())) ||
      (u.email && u.email.toLowerCase().includes(searchKeyword.value.toLowerCase()))

    const matchRole =
      activeRoleFilter.value === 'all' || u.role === activeRoleFilter.value

    return matchSearch && matchRole
  })
})

const getInitials = (name) => {
  if (!name) return 'U'
  const parts = name.trim().split(' ')
  if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase()
  return name.slice(0, 2).toUpperCase()
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric'
    })
  } catch {
    return dateStr
  }
}

const loadUsers = async () => {
  isLoading.value = true
  try {
    const res = await $fetch(`${apiBaseUrl}/users`, {
      headers: { Authorization: `Bearer ${token.value}` }
    })
    users.value = res.data || []
  } catch (err) {
    console.error('Error load users:', err)
    showToast(err.data?.message || 'Gagal memuat daftar pengguna.', 'error')
  } finally {
    isLoading.value = false
  }
}

const openModalTambah = () => {
  formTambah.value = {
    name: '',
    email: '',
    password: '',
    role: 'kasir'
  }
  showModalTambah.value = true
}

const simpanTambahUser = async () => {
  if (isSubmitting.value) return
  isSubmitting.value = true

  try {
    const res = await $fetch(`${apiBaseUrl}/users`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` },
      body: formTambah.value
    })

    showToast(res.message || 'Pengguna berhasil ditambahkan!', 'success')
    showModalTambah.value = false
    await loadUsers()
  } catch (err) {
    console.error('Error simpan user:', err)
    showToast(err.data?.message || 'Gagal menambahkan pengguna.', 'error')
  } finally {
    isSubmitting.value = false
  }
}

const openModalEdit = (u) => {
  formEdit.value = {
    id: u.id,
    name: u.name,
    email: u.email,
    role: u.role
  }
  showModalEdit.value = true
}

const simpanEditUser = async () => {
  if (isSubmitting.value) return
  isSubmitting.value = true

  try {
    const res = await $fetch(`${apiBaseUrl}/users/${formEdit.value.id}`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${token.value}` },
      body: {
        name: formEdit.value.name,
        email: formEdit.value.email,
        role: formEdit.value.role
      }
    })

    showToast(res.message || 'Data pengguna berhasil diperbarui!', 'success')
    showModalEdit.value = false
    await loadUsers()
  } catch (err) {
    console.error('Error update user:', err)
    showToast(err.data?.message || 'Gagal memperbarui pengguna.', 'error')
  } finally {
    isSubmitting.value = false
  }
}

const openModalResetPassword = (u) => {
  targetUser.value = u
  formReset.value = {
    password: '',
    confirmPassword: ''
  }
  showModalReset.value = true
}

const simpanResetPassword = async () => {
  if (formReset.value.password !== formReset.value.confirmPassword) {
    showToast('Konfirmasi password tidak cocok!', 'warning')
    return
  }

  if (isSubmitting.value) return
  isSubmitting.value = true

  try {
    const res = await $fetch(`${apiBaseUrl}/users/${targetUser.value.id}/reset-password`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${token.value}` },
      body: {
        password: formReset.value.password
      }
    })

    showToast(res.message || 'Password berhasil direset!', 'success')
    showModalReset.value = false
  } catch (err) {
    console.error('Error reset password:', err)
    showToast(err.data?.message || 'Gagal mereset password.', 'error')
  } finally {
    isSubmitting.value = false
  }
}

const hapusUser = async (u) => {
  if (u.id === currentUser.value?.id) {
    showToast('Anda tidak dapat menghapus akun Anda sendiri!', 'warning')
    return
  }

  if (!confirm(`Yakin ingin menghapus pengguna "${u.name}" (${u.email})? Tindakan ini tidak dapat dibatalkan.`)) {
    return
  }

  try {
    const res = await $fetch(`${apiBaseUrl}/users/${u.id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${token.value}` }
    })

    showToast(res.message || 'Pengguna berhasil dihapus!', 'success')
    await loadUsers()
  } catch (err) {
    console.error('Error hapus user:', err)
    showToast(err.data?.message || 'Gagal menghapus pengguna.', 'error')
  }
}

onMounted(() => {
  if (!token.value) {
    navigateTo('/')
    return
  }
  if (!isAdmin.value) {
    showToast('Akses ditolak: Halaman Kelola Pengguna hanya untuk Admin.', 'warning')
    navigateTo('/kasir')
    return
  }
  loadUsers()
})
</script>
