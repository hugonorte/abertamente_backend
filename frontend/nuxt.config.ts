// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  modules: ['@nuxt/eslint', '@nuxt/fonts', '@nuxt/icon'],
    runtimeConfig: {
        public: {
            // Disponibiliza a URL da API para o lado do cliente (navegador)
            apiBaseUrl: process.env.NUXT_PUBLIC_API_BASE_URL || 'http://localhost/api'
        }
    }
})
