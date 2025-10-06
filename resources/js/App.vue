<template>
  <div v-if="loading" class="loading-screen">
    <div class="spinner"></div>
    <p>Loading, please wait...</p>
  </div>
  <ConfigProvider v-else>
    <div vaul-drawer-wrapper class="relative">
      <el-config-provider>
        <Component :is="currentLayout" v-if="route">
          <RouterView />
        </Component>
      </el-config-provider>
    </div>
  </ConfigProvider>
</template>
  
  <script setup>
  import { useRoute, RouterView } from 'vue-router';
  import BaseLayout from '@/layouts/BaseLayout.vue';
  import GuestLayout from '@/layouts/GuestLayout.vue';
  import { onMounted, computed } from 'vue';
  import { useSetting } from "@/composables/common/useSetting";
  import { ConfigProvider } from 'radix-vue';
  
  // Store untuk inisialisasi aplikasi
  const appSetting = useSetting();
  
  
  // Data route dan layout
  const route = useRoute();
  const currentLayout = computed(() => {
    const layouts = new Map([
      ['BaseLayout', BaseLayout],
      ['GuestLayout', GuestLayout]
    ]);
    return layouts.get(`${route.meta.layout || 'Base'}Layout`) || BaseLayout;
  });

  onMounted(() => {
    if (!appSetting.isInitialized) {
        appSetting.initApp();
    }
  });

    const loading = computed(() => appSetting.loading);
  
</script>

<style scoped>
.loading-screen {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background-color: rgba(255, 255, 255, 0.9);
  z-index: 9999;
}
.spinner {
  width: 50px;
  height: 50px;
  border: 5px solid #ccc;
  border-top-color: #007bff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>