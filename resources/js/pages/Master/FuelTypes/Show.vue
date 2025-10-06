<template>
    <div class="content">
        <!-- Page Header -->
        <PageHeader :title="data?.name || $t('master.fuel_types.detail')">
            <template #subtitle>
                <span class="text-gray-600">{{ data?.code }}</span>
            </template>
            <template #actions>
                <div class="flex gap-3">
                    <el-button @click="$router.go(-1)">
                        <Icon icon="mingcute:arrow-left-line" class="mr-2" />
                        {{ $t('common.actions.back') }}
                    </el-button>
                    <el-button type="primary" @click="editRecord" v-if="data">
                        <Icon icon="mingcute:edit-line" class="mr-2" />
                        {{ $t('common.actions.edit') }}
                    </el-button>
                </div>
            </template>
        </PageHeader>

        <div v-if="data" class="space-y-6">
            <!-- Status and Key Info Card -->
            <el-card class="!rounded-lg !shadow-md">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary mb-1">{{ data.code }}</div>
                        <div class="text-sm text-gray-600">{{ $t('master.fuel_types.fields.code') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 mb-1">{{ data.name }}</div>
                        <div class="text-sm text-gray-600">{{ $t('master.fuel_types.fields.name') }}</div>
                    </div>
                    <div class="text-center">
                        <el-tag :type="data.type === 'Diesel' ? 'primary' : 'info'" size="large" class="text-lg">
                            {{ data.type }}
                        </el-tag>
                        <div class="text-sm text-gray-600 mt-2">{{ $t('master.fuel_types.fields.type') }}</div>
                    </div>
                    <div class="text-center">
                        <el-tag 
                            :type="data.is_active ? 'success' : 'danger'" 
                            size="large"
                            class="text-lg"
                        >
                            {{ data.is_active ? $t('common.status.active') : $t('common.status.inactive') }}
                        </el-tag>
                        <div class="text-sm text-gray-600 mt-2">{{ $t('common.status.status') }}</div>
                    </div>
                </div>
            </el-card>

            <!-- Detail Information Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <el-card class="!rounded-lg !shadow-md">
                    <template #header>
                        <div class="flex items-center">
                            <Icon icon="mingcute:file-line" class="mr-2 text-primary" />
                            <span class="font-semibold">{{ $t('master.fuel_types.basic_information') }}</span>
                        </div>
                    </template>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">{{ $t('master.fuel_types.fields.code') }}</span>
                            <span class="text-gray-900">{{ data.code }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">{{ $t('master.fuel_types.fields.name') }}</span>
                            <span class="text-gray-900">{{ data.name }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">{{ $t('master.fuel_types.fields.type') }}</span>
                            <el-tag type="info" size="small">{{ data.type }}</el-tag>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">{{ $t('master.fuel_types.fields.grade') }}</span>
                            <span class="text-gray-900">{{ data.grade || '-' }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">{{ $t('master.fuel_types.fields.unit') }}</span>
                            <span class="text-gray-900">{{ data.unit || 'Litres' }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2">
                            <span class="font-medium text-gray-700">{{ $t('master.fuel_types.fields.is_active') }}</span>
                            <el-tag :type="data.is_active ? 'success' : 'danger'" size="small">
                                {{ data.is_active ? $t('common.status.active') : $t('common.status.inactive') }}
                            </el-tag>
                        </div>
                    </div>
                </el-card>

                <!-- Technical Properties -->
                <el-card class="!rounded-lg !shadow-md">
                    <template #header>
                        <div class="flex items-center">
                            <Icon icon="mingcute:settings-line" class="mr-2 text-primary" />
                            <span class="font-semibold">{{ $t('master.fuel_types.technical_properties') }}</span>
                        </div>
                    </template>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">{{ $t('master.fuel_types.fields.density_kg_l') }}</span>
                            <span class="text-gray-900">{{ data.density_kg_l || '-' }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">{{ $t('master.fuel_types.fields.viscosity_cst') }}</span>
                            <span class="text-gray-900">{{ data.viscosity_cst || '-' }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">{{ $t('master.fuel_types.fields.flash_point_c') }}</span>
                            <span class="text-gray-900">{{ data.flash_point_c || '-' }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">{{ $t('master.fuel_types.fields.pour_point_c') }}</span>
                            <span class="text-gray-900">{{ data.pour_point_c || '-' }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">{{ $t('master.fuel_types.fields.heating_value_mj_kg') }}</span>
                            <span class="text-gray-900">{{ data.heating_value_mj_kg || '-' }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2">
                            <span class="font-medium text-gray-700">{{ $t('master.fuel_types.fields.sulfur_content_pct') }}</span>
                            <span class="text-gray-900">{{ data.sulfur_content_pct || '-' }}</span>
                        </div>
                    </div>
                </el-card>

                <!-- Storage Information -->
                <el-card class="!rounded-lg !shadow-md">
                    <template #header>
                        <div class="flex items-center">
                            <Icon icon="mingcute:storage-line" class="mr-2 text-primary" />
                            <span class="font-semibold">{{ $t('master.fuel_types.storage_information') }}</span>
                        </div>
                    </template>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">{{ $t('master.fuel_types.fields.storage_tank') }}</span>
                            <span class="text-gray-900">{{ data.storage_tank || '-' }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2">
                            <span class="font-medium text-gray-700">{{ $t('master.fuel_types.fields.tank_capacity') }}</span>
                            <span class="text-gray-900">{{ data.tank_capacity || '-' }}</span>
                        </div>
                    </div>
                </el-card>

                <!-- Timestamps -->
                <el-card class="!rounded-lg !shadow-md">
                    <template #header>
                        <div class="flex items-center">
                            <Icon icon="mingcute:time-line" class="mr-2 text-primary" />
                            <span class="font-semibold">{{ $t('common.labels.information') }}</span>
                        </div>
                    </template>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="font-medium text-gray-700">{{ $t('common.fields.created_at') }}</span>
                            <span class="text-gray-900">{{ formatDate(data.created_at) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2">
                            <span class="font-medium text-gray-700">{{ $t('common.fields.updated_at') }}</span>
                            <span class="text-gray-900">{{ formatDate(data.updated_at) }}</span>
                        </div>
                    </div>
                </el-card>
            </div>
        </div>

        <!-- Loading State -->
        <div v-else-if="loading" class="flex justify-center items-center h-64">
            <el-icon class="is-loading text-2xl">
                <Icon icon="mingcute:loading-line" />
            </el-icon>
        </div>

        <!-- Error State -->
        <div v-else class="text-center text-gray-500 py-12">
            <Icon icon="mingcute:close-circle-line" class="text-4xl mb-4" />
            <p>{{ $t('common.messages.data_not_found') }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import { Icon } from '@iconify/vue';
import axios from 'axios';
import { useI18n } from 'vue-i18n';
import { useFormatter } from '@/composables/useFormatter';
import PageHeader from '@/components/PageHeader.vue';

const { t } = useI18n();
const { formatDate } = useFormatter();
const route = useRoute();
const router = useRouter();
const data = ref(null);
const loading = ref(false);

const fetchData = async () => {
    loading.value = true;
    try {
        const response = await axios.get(`/master/fuel-types/${route.params.id}`);
        data.value = response.data.data;
    } catch (error) {
        ElMessage.error(t('common.errors.operation_failed'));
        router.push({ name: 'master.fuel_types.index' });
    } finally {
        loading.value = false;
    }
};

const editRecord = () => {
    router.push({ name: 'master.fuel_types.edit', params: { id: route.params.id } });
};

onMounted(() => {
    fetchData();
});
</script>