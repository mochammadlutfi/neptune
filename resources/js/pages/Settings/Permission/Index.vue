<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader
            :title="$t('settings.role_permission.title')"
            :description="$t('settings.role_permission.subtitle')"
            :primary-action="{
                label: $t('settings.role_permission.create'),
                icon: 'mingcute:add-line',
                click: createRole
            }"
        />

        <el-card body-class="!p-0" class="!rounded-lg !shadow-md">
            <!-- Advanced Filter Section -->
            <TableControls
                v-model:search="params.q"
                :search-placeholder="$t('settings.role_permission.search_roles')"
                :loading="loading"
                v-model:page-size="params.limit"
                :total-results="data?.total"
                :selected-rows="selectedRows"
                @refresh="refetch"/>

            <!-- Content Area -->
            <el-skeleton :loading="loading" animated>
                <template #template>
                    <SkeletonTable />
                </template>
                <template #default>
                    <div class="overflow-x-auto">
                        <el-table 
                            class="base-table" 
                            :data="data?.data || []" 
                            @sort-change="sortChange" 
                            @selection-change="onSelectionChange"
                            row-key="id"
                            stripe
                        >
                            <el-table-column type="selection" width="50" align="center" />
                            
                            <el-table-column prop="name" :label="$t('settings.role_permission.fields.role_name')" sortable min-width="180">
                                <template #default="scope">
                                    <span :to="`/settings/permission/${scope.row.id}`" class="font-medium text-blue-600 hover:text-blue-800">
                                        {{ scope.row.name }}
                                    </span>
                                </template>
                            </el-table-column>
                            
                            <el-table-column prop="users_count" :label="$t('settings.role_permission.fields.users_count')" sortable width="140" align="center">
                                <template #default="scope">
                                    <span class="text-gray-600">{{ scope.row.users_count || 0 }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="permissions_count" :label="$t('settings.role_permission.fields.permissions_count')" sortable width="200" align="center">
                                <template #default="scope">
                                    <span class="text-gray-600">{{ scope.row.permissions_count || 0 }}</span>
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
                </template>
            </el-skeleton>

            <!-- Pagination -->
            <Pagination
                :current-page="params.page"
                :page-size="params.limit"
                :total-results="data?.total || 0"
                :from="data?.from || 0"
                :to="data?.to || 0"
                @update:page-size="(size) => { params.limit = size; changePage(1) }"
                @page-change="changePage"
            />
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

// Import our reusable components
import PageHeader from '@/components/PageHeader.vue';
import TableControls from '@/components/TableControls.vue'
import Pagination from '@/components/Pagination.vue';
import SkeletonTable from '@/components/SkeletonTable.vue';

const { t } = useI18n();

// Page state
useTitle(t('settings.role_permission.title'));
const selectedRows = ref([]);

// Filter state
const params = ref({
    limit: 25,
    page: 1,
    q: "",
    sort: 'name',
    sortDir: 'asc',
});

const advancedFilters = ref({
    guard_name: null,
    users_count: null,
});

// Computed properties
const activeFilters = computed(() => {
    const filters = [];
    if (advancedFilters.value.guard_name) {
        filters.push({
            key: 'guard_name',
            label: t('settings.role_permission.fields.guard_name'),
            value: advancedFilters.value.guard_name
        });
    }
    if (advancedFilters.value.users_count) {
        filters.push({
            key: 'users_count',
            label: t('settings.role_permission.fields.users_count'),
            value: advancedFilters.value.users_count === '0' ? 'No Users' : 'Has Users'
        });
    }
    return filters;
});

// Data fetching
const fetchData = async ({ queryKey }) => {
    const [_key, queryParams] = queryKey;
    const response = await axios.get("/settings/permissions", {
        params: queryParams,
    });
    return response.data;
};

const { data, isLoading: loading, isError, error, refetch } = useQuery({
    queryKey: ['rolePermissionList', params.value],
    queryFn: fetchData,
    keepPreviousData: true,
});

// Convert query data to match existing structure
const paginationParams = computed(() => ({
    from: data.value?.from || 0,
    to: data.value?.to || 0,
    total: data.value?.total || 0,
    pageSize: data.value?.per_page || 25,
    page: data.value?.current_page || 1,
}));

// Update params for pagination compatibility
watch(paginationParams, (newParams) => {
    Object.assign(params.value, newParams);
}, { deep: true });

// Event handlers
const createRole = () => {
    window.location.href = '/settings/permission/create';
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

const changePage = (newPage) => {
    params.value.page = newPage;
    refetch();
};

const onSelectionChange = (selection) => {
    selectedRows.value = selection;
};

const applyAdvancedFilters = () => {
    Object.assign(params.value, {
        guard_name: advancedFilters.value.guard_name,
        users_count: advancedFilters.value.users_count,
        page: 1
    });
    refetch();
};

const clearAllFilters = () => {
    advancedFilters.value = {
        guard_name: null,
        users_count: null,
    };
    params.value = {
        ...params.value,
        guard_name: null,
        users_count: null,
        page: 1
    };
    refetch();
};

const removeFilter = (filterKey) => {
    if (filterKey === 'guard_name') {
        advancedFilters.value.guard_name = null;
    } else {
        advancedFilters.value[filterKey] = null;
    }
    applyAdvancedFilters();
};

const onView = (row) => {
    window.location.href = `/settings/permission/${row.id}`;
};

const onEdit = (row) => {
    window.location.href = `/settings/permission/${row.id}/edit`;
};

const onDelete = (id) => {
    ElMessageBox.confirm(t("common.confirmations.confirm_delete"), t('common.confirmations.are_you_sure'), {
        confirmButtonText: t("common.actions.confirm"),
        cancelButtonText: t("common.actions.cancel"),
        type: 'warning',
    }).then(async () => {
        try {
            await axios.delete(`/settings/permissions/${id}/delete`);
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
