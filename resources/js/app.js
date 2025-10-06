import './bootstrap';

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import App from './App.vue'
import router from './router'
import '@/utils/axios';
import setupI18n from '@/utils/i18n';
import { VueQueryPlugin, QueryClient } from '@tanstack/vue-query';
import { createHead } from '@unhead/vue'
import { abilitiesPlugin } from '@casl/vue';
import { ability } from './utils/ability';
import { Icon } from "@iconify/vue";
import { Vue3Mq } from "vue3-mq";
import Cookies from 'js-cookie';

import AutoBreadcrumb from './components/AutoBreadcrumb.vue';

(async () => {
    const i18n = await setupI18n();
    const pinia = createPinia()
    pinia.use(piniaPluginPersistedstate)
    
    const app = createApp(App)
    app.use(pinia)
    
    // Initialize authentication dan user profile menggunakan utility
    const { initializeApp } = await import('./utils/app-init.js')
    const initResult = await initializeApp()
    
    if (!initResult.success) {
        console.log(`App initialization completed with status: ${initResult.reason}`)
        // Untuk session_expired, axios interceptor sudah menangani redirect
        // Untuk error lain, aplikasi tetap bisa berjalan dalam mode terbatas
    } else {
        console.log('App initialization successful, user authenticated')
    }
    
    app.use(router)
    app.use(createHead)
    app.use(i18n)
    app.use(Vue3Mq)
    app.use(VueQueryPlugin);
    app.use(abilitiesPlugin, ability);
    app.component('Icon', Icon);
    app.component('AutoBreadcrumb', AutoBreadcrumb);
    app.mount('#app');
})();