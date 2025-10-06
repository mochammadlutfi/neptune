<template>
    <div class="content">
        <!-- Page Header -->
        <PageHeader
            :title="$t('settings.user.detail')"
            icon="mingcute:user-line"
            :description="user.name"
            :primary-action="{
                label: $t('common.actions.edit'),
                shortLabel: $t('common.actions.edit'),
                icon: 'mingcute:edit-line',
                click: onEdit
            }"
            :dropdown-actions="[
                {
                    key: 'delete',
                    label: $t('common.actions.delete'),
                    icon: 'mingcute:delete-2-line',
                    class: 'text-red-600',
                    click: onDelete
                }
            ]"
        />

        <el-skeleton :loading="isLoading" animated>
            <template #template>
                <el-card class="!rounded-lg !shadow-md mb-6">
                    <el-skeleton-item variant="rect" style="height: 200px;" />
                </el-card>
            </template>
            <template #default>
                <div v-if="user.id">
                    <el-card class="!rounded-lg !shadow-md mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col items-center md:items-start">
                                <el-avatar :size="100" :src="user.image_url || 'https://cube.elemecdn.com/3/7c/3ed68546de90861800426f56bcce0.jpeg'" fit="cover">
                                    <Icon icon="mingcute:user-line" class="text-5xl" />
                                </el-avatar>
                                <h3 class="text-xl font-semibold text-gray-900 mt-4">{{ user.name }}</h3>
                                <p class="text-sm text-gray-600">{{ user.email }}</p>
                                <el-tag v-if="user.roles?.[0]?.name" size="small" class="mt-2">
                                    {{ user.roles[0].name }}
                                </el-tag>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-2">Contact Information</h4>
                                <p class="text-sm text-gray-600 mb-1"><strong>{{ $t('settings.user.fields.phone') }}:</strong> {{ user.phone || '-' }}</p>
                                <p class="text-sm text-gray-600 mb-1"><strong>Status:</strong> 
                                    <el-tag :type="getUserStatusType(user.status)" size="small" effect="light">
                                        {{ $t(`settings.user.status.${user.status}`) }}
                                    </el-tag>
                                </p>
                            </div>
                        </div>
                    </el-card>
                </div>
                <div v-else class="text-center py-12">
                    <Icon icon="mingcute:user-line" class="w-16 h-16 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $t('common.messages.no_data') }}</h3>
                    <p class="text-gray-500">User not found</p>
                </div>
            </template>
        </el-skeleton>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { ElMessage, ElMessageBox } from 'element-plus';
import { Icon } from '@iconify/vue';
import { useTitle } from '@vueuse/core';
import { useI18n } from 'vue-i18n';

// Import reusable components
import PageHeader from '@/components/PageHeader.vue';

const { t } = useI18n();
const route = useRoute();
const router = useRouter();

const user = ref({});
const isLoading = ref(true);

useTitle(t('settings.user.detail'));

const fetchUser = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(`/settings/user/${route.params.id}`);
        user.value = response.data.result; // Assuming result key
    } catch (error) {
        ElMessage.error(t('common.errors.server_error'));
        console.error('Failed to fetch user:', error);
    } finally {
        isLoading.value = false;
    }
};

const getUserStatusType = (status) => {
    switch (status) {
        case 'active': return 'success';
        case 'inactive': return 'info';
        default: return '';
    }
};

const onEdit = () => {
    router.push(`/settings/user/${user.value.id}/edit`);
};

const onDelete = () => {
    ElMessageBox.confirm(t('common.confirmations.confirm_delete'), t('common.confirmations.are_you_sure'), {
        confirmButtonText: t('common.actions.confirm'),
        cancelButtonText: t('common.actions.cancel'),
        type: 'warning',
    }).then(async () => {
        try {
            await axios.delete(`/settings/user/${user.value.id}/delete`);
            ElMessage.success(t('common.success.item_deleted'));
            router.push('/settings/user');
        } catch (error) {
            ElMessage.error(t('common.errors.delete_failed'));
        }
    }).catch(() => {
        ElMessage.info(t('common.messages.operation_cancelled'));
    });
};

onMounted(() => {
    fetchUser();
});
</script>
