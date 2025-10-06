<template>
    <div>
        <el-card class="shadow-sm sm:rounded-lg mb-6">
            <template #header>
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">System Information</h3>
                    <el-button @click="checkForUpdates" :loading="checking" type="primary">
                        <Icon icon="mingcute:refresh-2-line" class="me-2" />
                        {{ $t('settings.system.update.check_update') }}
                    </el-button>
                </div>
            </template>
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-800">{{ $t('settings.system.update.current_version') }}</h4>
                        <p class="text-2xl font-bold text-blue-600">{{ systemInfo.version }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-800">Last Update</h4>
                        <p class="text-gray-600">{{ systemInfo.lastUpdate }}</p>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-800 mb-2">Environment</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                        <div><strong>PHP:</strong> {{ systemInfo.php }}</div>
                        <div><strong>Laravel:</strong> {{ systemInfo.laravel }}</div>
                        <div><strong>Database:</strong> {{ systemInfo.database }}</div>
                        <div><strong>Cache:</strong> {{ systemInfo.cache }}</div>
                    </div>
                </div>
            </div>
        </el-card>

        <el-card class="shadow-sm sm:rounded-lg" v-if="updateAvailable">
            <template #header>
                <h3 class="text-lg font-semibold text-green-600">{{ $t('settings.system.update.update_available') }}</h3>
            </template>
            
            <div class="space-y-4">
                <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                    <h4 class="font-medium text-green-800">{{ $t('settings.system.update.latest_version') }}: {{ updateInfo.version }}</h4>
                    <p class="text-green-700 mt-2">{{ updateInfo.description }}</p>
                </div>
                
                <div class="flex gap-2">
                    <el-button type="success" @click="performUpdate" :loading="updating">
                        <Icon icon="mingcute:download-line" class="me-2" />
                        {{ $t('base.update_now') }}
                    </el-button>
                    <el-button @click="viewChangelog">
                        <Icon icon="mingcute:file-text-line" class="me-2" />
                        {{ $t('base.view_changelog') }}
                    </el-button>
                </div>
            </div>
        </el-card>

        <el-card class="shadow-sm sm:rounded-lg" v-else-if="!checking">
            <div class="text-center py-8">
                <Icon icon="mingcute:check-circle-fill" class="text-6xl text-green-500 mb-4" />
                <h3 class="text-xl font-semibold text-gray-800">{{ $t('base.system_up_to_date') }}</h3>
                <p class="text-gray-600 mt-2">{{ $t('base.no_updates_available') }}</p>
            </div>
        </el-card>
    </div>
</template>

<script setup>
import { defineProps, onMounted, defineEmits, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Icon } from '@iconify/vue';
import { ElMessage, ElMessageBox } from 'element-plus';

const { t } = useI18n();
const emit = defineEmits(['childinit']);

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
});

const checking = ref(false);
const updating = ref(false);
const updateAvailable = ref(false);

const systemInfo = ref({
    version: '1.0.0',
    lastUpdate: '2025-01-01',
    php: '8.3.16',
    laravel: '11.x',
    database: 'MySQL 8.0',
    cache: 'Redis'
});

const updateInfo = ref({
    version: '',
    description: '',
    changelog: ''
});

const checkForUpdates = async () => {
    try {
        checking.value = true;
        const response = await axios.get('/settings/system/check-updates');
        
        if (response.data.available) {
            updateAvailable.value = true;
            updateInfo.value = response.data.update;
            ElMessage({
                message: t('base.update_found'),
                type: 'success'
            });
        } else {
            updateAvailable.value = false;
            ElMessage({
                message: t('base.no_updates'),
                type: 'info'
            });
        }
    } catch (error) {
        ElMessage({
            message: t('message.error_server'),
            type: 'error'
        });
    } finally {
        checking.value = false;
    }
};

const performUpdate = async () => {
    try {
        await ElMessageBox.confirm(
            t('base.update_confirm'),
            t('base.update_confirm_title'),
            {
                confirmButtonText: t('common.ok'),
                cancelButtonText: t('common.cancel'),
                type: 'warning',
            }
        );
        
        updating.value = true;
        const response = await axios.post('/settings/system/update');
        
        ElMessage({
            message: t('base.update_success'),
            type: 'success'
        });
        
        // Refresh system info
        await fetchSystemInfo();
        updateAvailable.value = false;
        
    } catch (error) {
        if (error !== 'cancel') {
            ElMessage({
                message: t('message.error_server'),
                type: 'error'
            });
        }
    } finally {
        updating.value = false;
    }
};

const viewChangelog = () => {
    ElMessageBox.alert(
        updateInfo.value.changelog || t('base.no_changelog'),
        t('base.changelog'),
        {
            confirmButtonText: t('common.ok')
        }
    );
};

const fetchSystemInfo = async () => {
    try {
        const response = await axios.get('/settings/system/info');
        systemInfo.value = response.data;
    } catch (error) {
        console.error('Failed to fetch system info:', error);
    }
};

onMounted(() => {
    emit('childinit', t('settings.system.update.title'));
    fetchSystemInfo();
});
</script>