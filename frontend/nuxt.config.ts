// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2026-07-24',
  devtools: { enabled: true },
  modules: ['@nuxtjs/tailwindcss'],
  app: {
    head: {
      title: 'Sistem Kasir Modern POS',
      script: [
        {
          src: 'https://app.sandbox.midtrans.com/snap/snap.js',
          'data-client-key': 'SB-Mid-client-YOUR_CLIENT_KEY',
          id: 'midtrans-snap-script'
        }
      ]
    }
  },
  runtimeConfig: {
    public: {
      apiBaseUrl:
        import.meta.env.NUXT_PUBLIC_API_BASE_URL ||
        (import.meta.env.PROD
          ? 'https://localhost/crud-laravel/public/api'
          : 'http://localhost/crud-laravel/public/api')
    }
  }
})
