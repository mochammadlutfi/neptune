<template>
    <div class="content">
        
        <div class="content-header">
            <h2 class="content-title">{{ $t(`${title}`) }}</h2>

            <div class="md:flex hidden items-center gap-2.5 text-sm font-semibold">
                <el-breadcrumb separator="/">
                    <el-breadcrumb-item :to="{ path: '/' }">{{ $t('base.dashboard') }}</el-breadcrumb-item>
                    <el-breadcrumb-item :to="{ path: '/settings' }">{{ $t('base.settings') }}</el-breadcrumb-item>
                    <el-breadcrumb-item>{{ $t('base.system')}}</el-breadcrumb-item>
                </el-breadcrumb>
            </div>
        </div>
        <el-row :gutter="20">
            <el-col :lg="6"> 
                <el-card class="shadow-sm sm:rounded-lg" body-class="p-2">
                    <div class="space-y-2">
                        <router-link to="/profile" exactActiveClass="text-gray-500 bg-slate-100"
                        class="flex items-center py-2 px-4 text-sm rounded hover:text-slate-700 dark:text-gray-400 dark:bg-gray-700 dark:hover:text-gray-300">
                            <icon icon="hugeicons:user-square" class="me-3.5 w-5" width="24" height="24" />
                            <span>Profil</span>
                        </router-link>
                        <router-link to="/profile/password" 
                        class="flex items-center py-2 px-4 text-sm rounded hover:text-slate-700 dark:text-gray-400 dark:bg-gray-700 dark:hover:text-gray-300"
                        exactActiveClass="text-gray-500 bg-slate-100"
                        >
                            <icon icon="hugeicons:lock-password" class="me-3.5 w-5" width="24" height="24" />
                            <span>Password</span>
                        </router-link>
                        <router-link to="/profile/devices" 
                        class="flex items-center py-2 px-4 text-sm rounded hover:text-slate-700 dark:text-gray-400 dark:bg-gray-700 dark:hover:text-gray-300"
                        exactActiveClass="text-gray-500 bg-slate-100"
                        >
                        <icon icon="hugeicons:computer-phone-sync" class="me-3.5 w-5" width="24" height="24" />
                        <span>Devices</span>
                        </router-link>
                    </div>
                </el-card>
            </el-col>
            <el-col :lg="18">
                <router-view @childinit="childInit"></router-view>
            </el-col>
        </el-row>
    </div>
</template>

<script setup>
import { Icon } from '@iconify/vue';
import { ref } from 'vue';

const title = ref('');

const childInit = (val) =>{
  title.value = val
}
</script>

