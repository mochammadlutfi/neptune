<template>
    <div class="content">


        <el-row :gutter="20">
            <el-col :md="6">
                <el-card class="shadow-sm sm:rounded-lg" body-class="p-2">
                    <div class="space-y-2">
                        <router-link to="/settings/system" exactActiveClass="text-gray-500 bg-slate-100"
                            class="flex items-center py-2 px-4 text-sm rounded hover:text-slate-700 dark:text-gray-400 dark:bg-gray-700 dark:hover:text-gray-300">
                            <icon icon="fluent:document-globe-24-regular" class="me-3.5 w-5" width="24" height="24"/> 
                            <span>{{ $t('settings.system.general.title') }}</span>
                        </router-link>
                        <router-link to="/settings/system/email"
                            class="flex items-center py-2 px-4 text-sm rounded hover:text-slate-700 dark:text-gray-400 dark:bg-gray-700 dark:hover:text-gray-300"
                            exactActiveClass="text-gray-500 bg-slate-100">
                            <icon icon="fluent:mail-alert-24-regular" class="me-3.5 w-5" width="24" height="24" />
                            <span>{{ $t('settings.system.email.title') }}</span>
                        </router-link>
                        <router-link to="/settings/system/languages"
                            class="flex items-center py-2 px-4 text-sm rounded hover:text-slate-700 dark:text-gray-400 dark:bg-gray-700 dark:hover:text-gray-300"
                            exactActiveClass="text-gray-500 bg-slate-100">
                            <icon icon="fluent:local-language-24-filled" class="me-3.5 w-5" width="24" height="24" />
                            <span>{{ $t('settings.system.localization.languages') }}</span>
                        </router-link>
                        <router-link to="/settings/system/currency"
                            class="flex items-center py-2 px-4 text-sm rounded hover:text-slate-700 dark:text-gray-400 dark:bg-gray-700 dark:hover:text-gray-300"
                            exactActiveClass="text-gray-500 bg-slate-100">
                            <icon icon="carbon:currency" class="me-3.5 w-5" width="24" height="24" />
                            <span>{{ $t('settings.system.localization.currencies') }}</span>
                        </router-link>
                        <router-link to="/settings/system/operation"
                            class="flex items-center py-2 px-4 text-sm rounded hover:text-slate-700 dark:text-gray-400 dark:bg-gray-700 dark:hover:text-gray-300"
                            exactActiveClass="text-gray-500 bg-slate-100">
                            <icon icon="fluent:developer-board-24-regular" class="me-3.5 w-5" width="24" height="24" />
                            <span>{{ $t('settings.system.operation.title') }}</span>
                        </router-link>
                        <router-link to="/settings/system/accounting"
                            class="flex items-center py-2 px-4 text-sm rounded hover:text-slate-700 dark:text-gray-400 dark:bg-gray-700 dark:hover:text-gray-300"
                            exactActiveClass="text-gray-500 bg-slate-100">
                            <icon icon="fluent:table-calculator-20-regular" class="me-3.5 w-5" width="24" height="24" />
                            <span>{{ $t('settings.system.accounting.title') }}</span>
                        </router-link>
                        <router-link to="/settings/system/sequences"
                            class="flex items-center py-2 px-4 text-sm rounded hover:text-slate-700 dark:text-gray-400 dark:bg-gray-700 dark:hover:text-gray-300"
                            exactActiveClass="text-gray-500 bg-slate-100">
                            <icon icon="fluent:book-number-24-regular" class="me-3.5 w-5" width="24" height="24" />
                            <span>{{ $t('settings.system.sequences.title') }}</span>
                        </router-link>
                        <router-link to="/settings/system/module"
                            class="flex items-center py-2 px-4 text-sm rounded hover:text-slate-700 dark:text-gray-400 dark:bg-gray-700 dark:hover:text-gray-300"
                            exactActiveClass="text-gray-500 bg-slate-100">
                            <icon icon="mingcute:plugin-line" class="me-3.5 w-5" width="24" height="24" />
                            <span>{{ $t('settings.system.modules.title') }}</span>
                        </router-link>
                        <router-link to="/settings/system/appearance"
                            class="flex items-center py-2 px-4 text-sm rounded hover:text-slate-700 dark:text-gray-400 dark:bg-gray-700 dark:hover:text-gray-300"
                            exactActiveClass="text-gray-500 bg-slate-100">
                            <icon icon="mingcute:palette-line" class="me-3.5 w-5" width="24" height="24" />
                            <span>{{ $t('settings.system.appearance.title') }}</span>
                        </router-link>
                        <router-link to="/settings/system/update"
                            class="flex items-center py-2 px-4 text-sm rounded hover:text-slate-700 dark:text-gray-400 dark:bg-gray-700 dark:hover:text-gray-300"
                            exactActiveClass="text-gray-500 bg-slate-100">
                            <icon icon="mingcute:update-line" class="me-3.5 w-5" width="24" height="24" />
                            <span>{{ $t('settings.system.update.title') }}</span>
                        </router-link>
                    </div>
                </el-card>
            </el-col>
            <el-col :md="18">
                <router-view @childinit="childInit"></router-view>
            </el-col>
        </el-row>
    </div>
</template>

<script setup>
import { Icon } from '@iconify/vue';
import { ref } from 'vue';

const title = ref('');

const childInit = (val) => {
    title.value = val
}
</script>
