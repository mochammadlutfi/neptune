<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader
            :title="$t('settings.user.title')"
            :description="$t('settings.user.subtitle')"
            :primary-action="{
                label: $t('settings.user.add'),
                icon: 'mingcute:add-line',
                click: createUser
            }"
            :dropdown-actions="[
                {
                    key: 'export-all',
                    label: $t('common.actions.export'),
                    icon: 'mingcute:download-line',
                    click: () => ElMessage.info($t('common.messages.feature_unavailable'))
                },
                {
                    key: 'import',
                    label: $t('common.actions.import'),
                    icon: 'mingcute:upload-line',
                    click: () => ElMessage.info($t('common.messages.feature_unavailable'))
                }
            ]"
        />

        <el-card body-class="!p-0" class="!rounded-lg !shadow-md">
            <!-- Advanced Filter Section -->
            <TableControls
                v-model:search="params.q"
                :search-placeholder="$t('settings.user.search_users')"
                :loading="isLoading"
                :has-advanced-filters="true"
                :active-filters="activeFilters"
                @apply-filters="applyAdvancedFilters"
                @clear-filters="clearAllFilters"
                @remove-filter="removeFilter"
            
                v-model:page-size="params.limit"
                :total-results="data?.total"
                :selected-rows="selectedRows"
                @refresh="refetch"
                @bulk-export="bulkExport"
                @bulk-delete="bulkDelete">
                <template #advanced-filters>
                    <select-role v-model="advancedFilters.role_id" :placeholder="$t('settings.user.fields.role')" />
                    <el-select v-model="advancedFilters.status" :placeholder="$t('settings.user.fields.status')" clearable>
                        <el-option :label="$t('settings.user.status.active')" value="active" />
                        <el-option :label="$t('settings.user.status.inactive')" value="inactive" />
                    </el-select>
                </template>
            </TableControls>

            <!-- Content Area -->
            <el-skeleton :loading="isLoading" animated>
                <template #template>
                    <SkeletonTable />
                </template>
                <template #default>
                    <div v-if="viewMode === 'table'" class="overflow-x-auto">
                        <el-table 
                            class="base-table" 
                            :data="data?.data || []" 
                            @sort-change="sortChange" 
                            @selection-change="onSelectionChange"
                            row-key="id"
                            stripe
                        >
                            <el-table-column type="selection" width="50" align="center" />
                            
                            <el-table-column prop="name" :label="$t('settings.user.fields.name')" sortable min-width="180">
                                <template #default="scope">
                                    <router-link :to="`/settings/user/${scope.row.id}`" class="font-medium text-blue-600 hover:text-blue-800">
                                        {{ scope.row.name }}
                                    </router-link>
                                </template>
                            </el-table-column>
                            
                            <el-table-column prop="email" :label="$t('settings.user.fields.email')" sortable min-width="200" />

                            <el-table-column prop="vessel_id" :label="$t('settings.user.fields.vessel_id')" min-width="150">
                                <template #default="scope">
                                    {{ scope.row.vessel?.name || '-' }}
                                </template>
                            </el-table-column>

                            <el-table-column prop="phone" :label="$t('settings.user.fields.phone')" min-width="150">
                                <template #default="scope">
                                    {{ scope.row.phone || '-' }}
                                </template>
                            </el-table-column>

                            <el-table-column :label="$t('settings.user.fields.role')" min-width="150">
                                <template #default="scope">
                                    {{ scope.row.roles?.[0]?.name || '-' }}
                                </template>
                            </el-table-column>

                            <el-table-column prop="status" :label="$t('settings.user.fields.status')" sortable width="120">
                                <template #default="scope">
                                    <el-tag :type="getUserStatusType(scope.row.status)" size="small" effect="light">
                                        {{ $t(`settings.user.status.${scope.row.status}`) }}
                                    </el-tag>
                                </template>
                            </el-table-column>
                            
                            <el-table-column :label="$t('common.actions.action', 2)" align="center" width="120" fixed="right">
                                <template #default="scope">
                                    <el-dropdown popper-class="dropdown-action" placement="bottom-end" trigger="click">
                                        <el-button circle class="!p-0 !m-0">
                                            <Icon icon="mingcute:more-2-fill"/>
                                        </el-button>
                                        <template #dropdown>
                                            <el-dropdown-menu>
                                                <el-dropdown-item @click="onView(scope.row)">
                                                    <Icon icon="mingcute:eye-line" class="me-2" />
                                                    {{ $t('common.actions.view') }}
                                                </el-dropdown-item>
                                                <el-dropdown-item @click="onEdit(scope.row)">
                                                    <Icon icon="mingcute:edit-line" class="me-2" />
                                                    {{ $t('common.actions.edit') }}
                                                </el-dropdown-item>
                                                <el-dropdown-item divided class="text-red-600" @click="onDelete(scope.row.id)" v-if="scope.row.id != 1">
                                                    <Icon icon="mingcute:delete-2-line" class="me-2" />
                                                    {{ $t('common.actions.delete') }}
                                                </el-dropdown-item>
                                            </el-dropdown-menu>
                                        </template>
                                    </el-dropdown>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>

                    <!-- Card View -->
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 p-4">
                        <div
                            v-for="user in data?.data || []"
                            :key="user.id"
                            class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-gray-900 truncate">{{ user.name }}</h3>
                                <el-tag :type="getUserStatusType(user.status)" size="small" effect="light">
                                    {{ $t(`settings.user.status.${user.status}`) }}
                                </el-tag>
                            </div>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <Icon icon="mingcute:mail-line" class="mr-2 text-gray-400" />
                                    <span>{{ user.email }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <Icon icon="mingcute:phone-line" class="mr-2 text-gray-400" />
                                    <span>{{ user.phone || '-' }}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <Icon icon="mingcute:user-star-line" class="mr-2 text-gray-400" />
                                    <span class="truncate">{{ user.roles?.[0]?.name || '-' }}</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                <el-button size="small" @click="onView(user)">
                                    <Icon icon="mingcute:eye-line" />
                                </el-button>
                                <el-button size="small" @click="onEdit(user)">
                                    <Icon icon="mingcute:edit-line" />
                                </el-button>
                                <el-button size="small" type="danger" @click="onDelete(user.id)" v-if="user.id != 1">
                                    <Icon icon="mingcute:delete-2-line" />
                                </el-button>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <Pagination :current-page="data?.current_page || 1" :per-page="data?.per_page || 25"
                        :total="data?.total || 0" :last-page="data?.last_page || 1" :from="data?.from || 0"
                        :to="data?.to || 0"
                        @page-change="handlePageChange" @per-page-change="handlePerPageChange" />
                </template>
            </el-skeleton>
        </el-card>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';
import { ElMessageBox, ElMessage } from 'element-plus';
import { Icon } from '@iconify/vue';
import _ from 'lodash';
import { useQuery } from '@tanstack/vue-query';
import { useTitle } from '@vueuse/core';
import { useI18n } from 'vue-i18n';
import { useFormatter } from '@/composables/common/useFormatter';

// Import our reusable components
import PageHeader from '@/components/PageHeader.vue';
import TableControls from '@/components/TableControls.vue'
import Pagination from '@/components/Pagination.vue';
import SkeletonTable from '@/components/SkeletonTable.vue';
import SelectRole from '@/components/form/SelectRole.vue';

const { formatCurrency, formatDate } = useFormatter();
const { t } = useI18n();

// Page state
useTitle(t('settings.user.title'));
const viewMode = ref('table');
const selectedRows = ref([]);
const isLoadingStats = ref(false);
const stats = ref({});

// Filter state
const params = ref({
    limit: 25,
    page: 1,
    q: "",
    sort: 'name',
    sortDir: 'asc',
});

const advancedFilters = ref({
    role_id: null,
    status: null,
});

// Computed properties
const activeFilters = computed(() => {
    const filters = [];
    if (advancedFilters.value.role_id) {
        filters.push({
            key: 'role_id',
            label: t('settings.user.fields.role'),
            value: advancedFilters.value.role_id // This should ideally be the role name
        });
    }
    if (advancedFilters.value.status) {
        filters.push({
            key: 'status',
            label: t('settings.user.fields.status'),
            value: t(`settings.user.status.${advancedFilters.value.status}`)
        });
    }
    return filters;
});

// Helper methods
const getUserStatusType = (status) => {
    switch (status) {
        case 'active': return 'success';
        case 'inactive': return 'info';
        default: return '';
    }
};

const bulkExport = async () => {
    try {
        const response = await axios.post('/settings/user/bulk-export', params.value);
        if (response.data.success) {
            ElMessage.success(t('common.success.bulk_export_success'));
            window.location.href = response.data.url;
        } else {
            ElMessage.error(t('common.errors.bulk_export_failed'));
        }
    } catch (error) {
        ElMessage.error(t('common.errors.server_error'));
    }
}

const fetchData = async ({ queryKey }) => {
    const [_key, queryParams] = queryKey;
    const response = await axios.get("/settings/user", {
        params: queryParams,
    });
    return response.data;
};

const { data, isLoading, isError, error, refetch } = useQuery({
    queryKey: ['userList', params.value],
    queryFn: fetchData,
    keepPreviousData: true,
});

// Event handlers
const createUser = () => {
    window.location.href = '/settings/user/create';
};

const doSearch = _.debounce(() => {
    params.value.page = 1;
    refetch();
}, 1000);

const sortChange = (sortObj) => {
    params.value.sort = sortObj.prop;
    params.value.sortDir = sortObj.order === 'ascending' ? 'asc' : 'desc';
    refetch();
};

const handlePageChange = (page) => {
  params.value.page = page;
  refetch();
};

const handlePerPageChange = (perPage) => {
  params.value.limit = perPage;
  params.value.page = 1;
  refetch();
};

const onSelectionChange = (selection) => {
    selectedRows.value = selection;
};

const applyAdvancedFilters = () => {
    Object.assign(params.value, {
        role_id: advancedFilters.value.role_id,
        status: advancedFilters.value.status,
        page: 1
    });
    refetch();
};

const clearAllFilters = () => {
    advancedFilters.value = {
        role_id: null,
        status: null,
    };
    params.value = {
        ...params.value,
        role_id: null,
        status: null,
        page: 1
    };
    refetch();
};

const removeFilter = (filterKey) => {
    if (filterKey === 'role_id') {
        advancedFilters.value.role_id = null;
    } else {
        advancedFilters.value[filterKey] = null;
    }
    applyAdvancedFilters();
};

const bulkDelete = (rows) => {
    ElMessageBox.confirm(
        t('common.confirmations.confirm_delete_multiple', { count: rows.length }),
        t('common.confirmations.are_you_sure'),
        {
            confirmButtonText: t("common.actions.confirm"),
            cancelButtonText: t("common.actions.cancel"),
            type: 'warning',
        }
    ).then(() => {
        ElMessage.info(t('common.messages.feature_unavailable'));
    });
};

const onView = (row) => {
    window.location.href = `/settings/user/${row.id}`;
};

const onEdit = (row) => {
    window.location.href = `/settings/user/${row.id}/edit`;
};

const onDelete = (id) => {
    ElMessageBox.confirm(t("common.confirmations.confirm_delete"), t('common.confirmations.are_you_sure'), {
        confirmButtonText: t("common.actions.confirm"),
        cancelButtonText: t("common.actions.cancel"),
        type: 'warning',
    }).then(async () => {
        try {
            await axios.delete(`/settings/user/${id}/delete`);
            ElMessage.success(t('common.success.item_deleted'));
            refetch();
        } catch (error) {
            ElMessage.error(t('common.errors.delete_failed'));
        }
    }).catch(() => {
        ElMessage.info(t('common.messages.operation_cancelled'));
    });
};

// Watchers
watch(() => params.value.q, () => {
    doSearch();
});

// Lifecycle
onMounted(() => {
    refetch();
});
</script>

<style scoped>
/* Add any specific styles for this page here */
</style>