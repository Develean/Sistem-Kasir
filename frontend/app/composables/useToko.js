export function useToko() {
  const runtimeConfig = useRuntimeConfig()
  const apiBaseUrl = runtimeConfig.public.apiBaseUrl.replace(/\/+$/, '')

  const setting = useState('app-toko-setting', () => ({
    nama_toko: 'TOKO SEJAHTRA',
    alamat: 'Jl. Sejahtera No. 1',
    telepon: '0812-3456-7890',
    logo: null,
    footer_struk: 'Terima kasih atas kunjungan Anda!',
  }))

  const isLoadingSetting = useState('app-toko-loading', () => false)

  const loadSetting = async () => {
    try {
      isLoadingSetting.value = true
      const res = await $fetch(`${apiBaseUrl}/setting`)
      if (res && res.nama_toko) {
        setting.value = {
          nama_toko: res.nama_toko || 'TOKO SEJAHTRA',
          alamat: res.alamat || '',
          telepon: res.telepon || '',
          logo: res.logo || null,
          footer_struk: res.footer_struk || 'Terima kasih atas kunjungan Anda!',
        }
      }
    } catch (err) {
      console.warn('Gagal memuat pengaturan profil toko dari server:', err)
    } finally {
      isLoadingSetting.value = false
    }
  }

  const saveSetting = async (payload, token) => {
    const res = await $fetch(`${apiBaseUrl}/setting`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token}`
      },
      body: payload
    })

    if (res && res.data) {
      setting.value = {
        nama_toko: res.data.nama_toko || 'TOKO SEJAHTRA',
        alamat: res.data.alamat || '',
        telepon: res.data.telepon || '',
        logo: res.data.logo || null,
        footer_struk: res.data.footer_struk || 'Terima kasih atas kunjungan Anda!',
      }
    }
    return res
  }

  const namaToko = computed(() => setting.value?.nama_toko || 'TOKO SEJAHTRA')
  const alamatToko = computed(() => setting.value?.alamat || 'Jl. Sejahtera No. 1')
  const teleponToko = computed(() => setting.value?.telepon || '0812-3456-7890')
  const logoToko = computed(() => setting.value?.logo || null)
  const footerStruk = computed(() => setting.value?.footer_struk || 'Terima kasih atas kunjungan Anda!')

  return {
    setting,
    isLoadingSetting,
    loadSetting,
    saveSetting,
    namaToko,
    alamatToko,
    teleponToko,
    logoToko,
    footerStruk
  }
}
