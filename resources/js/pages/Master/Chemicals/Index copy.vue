<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader :title="$t('master.chemicals.title')" :primary-action="{
              label: $t('master.chemicals.create'),
              icon: 'mingcute:add-line',
              click: onCreate
          }" />

        <el-card body-class="!p-0" class="!rounded-lg !shadow-md">
            <!-- Advanced Filter Section -->
            <TableControls v-model:search="params.q" :search-placeholder="$t('common.actions.search')"
                :loading="isLoading" :selected-rows="selectedRows" @refresh="refetch" @bulk-export="bulkExport"
                @bulk-delete="bulkDelete" />

            <!-- Content Area -->
            <el-skeleton :loading="isLoading" animated>
                <template #template>
                    <SkeletonTable />
                </template>
                <template #default>
                    <div class="overflow-x-auto">
                        <el-table class="base-table" :data="data?.data || []" @sort-change="sortChange"
                            @selection-change="handleSelectionChange" row-key="id" stripe>
                            <el-table-column type="selection" width="50" align="center" />
                            <el-table-column prop="code" :label="$t('master.chemicals.fields.code')" width="120" sortable
                                fixed="left">
                                <template #default="scope">
                                    <el-tag type="info" size="small">{{ scope.row.code }}</el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column prop="name" :label="$t('master.chemicals.fields.name')" min-width="180"
                                sortable show-overflow-tooltip>
                                <template #default="scope">
                                    <div class="flex items-center space-x-2">
                                        <el-icon class="text-blue-600">
                                            <Icon icon="mingcute:flask-line"/>
                                        </el-icon>
                                        <router-link :to="`/master/chemicals/${scope.row.id}`"
                                            class="font-medium text-blue-600 hover:underline">
                                            {{ scope.row.name }}
                                        </router-link>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column prop="trade_name" :label="$t('master.chemicals.fields.trade_name')" min-width="160"
                                sortable show-overflow-tooltip>
                                <template #default="scope">
                                    <span class="text-gray-600">{{ scope.row.trade_name || '-' }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="type" :label="$t('master.chemicals.fields.type')" width="140" sortable>
                                <template #default="{ row }">
                                    <el-tag :type="getChemicalTypeTagType(row.type)" size="small">
                                        {{ row.type }}
                                    </el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column prop="hazard_un" :label="$t('master.chemicals.fields.hazard_un')" width="120"
                                sortable>
                                <template #default="scope">
                                    <el-tag v-if="scope.row.hazard_un" type="warning" size="small">
                                        UN{{ scope.row.hazard_un }}
                                    </el-tag>
                                    <span v-else class="text-gray-400">-</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="unit_package" :label="$t('master.chemicals.fields.unit_package')" width="120"
                                sortable>
                                <template #default="scope">
                                    <span class="text-sm">{{ scope.row.unit_package || '-' }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="primary_supplier" :label="$t('master.chemicals.fields.primary_supplier')"
                                min-width="160" show-overflow-tooltip>
                                <template #default="scope">
                                    <span>{{ scope.row.primary_supplier || '-' }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="stock_status" :label="$t('master.chemicals.fields.stock_status')" width="120"
                                sortable>
                                <template #default="scope">
                                    <el-tag :type="getStockStatusTagType(scope.row.stock_status)" size="small">
                                        {{ scope.row.stock_status }}
                                    </el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column :label="$t('common.actions.action', 2)" align="center" width="120"
                                fixed="right">
                                <template #default="scope">
                                    <el-dropdown popper-class="dropdown-action" placement="bottom-end" trigger="click">
                                        <el-button circle class="!p-0 !m-0">
                                            <Icon icon="mingcute:more-2-fill" />
                                        </el-button>
                                        <template #dropdown>
                                            <el-dropdown-menu>
                                                <el-dropdown-item @click="onView(scope.row)">
                                                    <Icon icon="mingcute:eye-line" class="me-2" />
                                                    {{ $t('common.actions.view') }}
                                                </el-dropdown-item>
                                                <el-dropdown-item @click="onEdit(scope.row)"
                                                    v-if="can('update', 'chemical')">
                                                    <Icon icon="mingcute:edit-line" class="me-2" />
                                                    {{ $t('common.actions.edit') }}
                                                </el-dropdown-item>
                                                <el-dropdown-item divided class="text-red-600"
                                                    @click="onDelete(scope.row)" v-if="can('delete', 'chemical')">
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

                    <!-- Pagination -->
                    <Pagination :current-page="data?.meta?.current_page || 1" :per-page="data?.meta?.per_page || 25"
                        :total="data?.meta?.total || 0" :last-page="data?.meta?.last_page || 1"
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
import { useI18n } from 'vue-i18n';
import { useFormatter } from '@/composables/common/useFormatter';
import { useRouter } from 'vue-router';
import { useAbility } from '@casl/vue';

// Import our reusable components
import PageHeader from '@/components/PageHeader.vue';
import TableControls from '@/components/TableControls.vue';
import Pagination from '@/components/Pagination.vue';
import SkeletonTable from '@/components/SkeletonTable.vue';

const { formatDate, formatNumber } = useFormatter();
const { t } = useI18n();
const { can } = useAbility();
const router = useRouter();

// Page stats
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
  name: null,
  code: null,
  type: null,
  stock_status: null,
  hazard_un: null,
  primary_supplier: null
});

const advancedFilters = ref({
  type: null,
  stock_status: null,
  hazard_un: null,
  primary_supplier: null
});

// Helper methods
const getStatusType = (status) => {
  switch (status) {
      case 'Active': return 'success';
      case 'Standby': return 'warning';
      case 'Maintenance': return 'info';
      case 'Decommissioned': return 'danger';
      default: return '';
  }
};

const fetchData = async ({ queryKey }) => {
  const [_key, queryParams] = queryKey;
  const response = await axios.get("/master/chemicals", {
    params: queryParams,
  });
  return response.data;
};

const { data, isLoading, isError, error, refetch } = useQuery({
  queryKey: ['MasterIndexChemicals', params.value],
  queryFn: fetchData,
  keepPreviousData: true,
});

// Event handlers
const onCreate = () => {
  router.push({ name: 'master.chemicals.create' });
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

const handleSelectionChange = (selection) => {
  selectedRows.value = selection;
};

const getChemicalTypeTagType = (type) => {
  const typeMap = {
    'Drilling Fluid': 'primary',
    'Production Chemical': 'success',
    'Completion Fluid': 'warning',
    'Stimulation Chemical': 'info',
    'Corrosion Inhibitor': 'danger',
    'Scale Inhibitor': '',
    'Biocide': 'primary',
    'Demulsifier': 'success'
  };
  return typeMap[type] || '';
};

const getStockStatusTagType = (status) => {
  const statusMap = {
    'In Stock': 'success',
    'Low Stock': 'warning',
    'Out of Stock': 'danger',
    'On Order': 'info',
    'Discontinued': ''
  };
  return statusMap[status] || '';
};

const bulkExport = (rows) => {
  ElMessage.info(t('common.messages.feature_unavailable'));
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
  router.push({ name: 'master.chemicals.show', params: { id: row.id } });
};

const onEdit = (row) => {
  router.push({ name: 'master.chemicals.edit', params: { id: row.id } });
};

const onDelete = async (row) => {
  try {
      await ElMessageBox.confirm(
          t('common.confirmations.confirm_delete'),
          t('common.confirmations.are_you_sure'),
          {
              confirmButtonText: t('common.actions.delete'),
              cancelButtonText: t('common.actions.cancel'),
              type: 'warning',
          }
      );

      await axios.delete(`/master/chemicals/${row.id}`);
      ElMessage.success(t('common.success.item_deleted'));
      refetch();
  } catch (error) {
      if (error !== 'cancel') {
          ElMessage.error(t('common.errors.delete_failed'));
      }
  }
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