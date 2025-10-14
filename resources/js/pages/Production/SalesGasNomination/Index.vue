<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader :title="$t('production.sales_gas_nomination.title')" :primary-action="{
            label: $t('production.sales_gas_nomination.create'),
            icon: 'mingcute:add-line',
            click: onCreate
        }" />

        <el-card body-class="!p-0" class="!rounded-lg !shadow-md">
            <!-- Advanced Filter Section -->
            <TableControls v-model:search="params.q" :search-placeholder="$t('common.search.search_placeholder')"
                :loading="isLoading" :selected-rows="selectedRows" @refresh="refetch" @bulk-export="bulkExport"
                @bulk-delete="bulkDelete">
                <template #filters>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('production.sales_gas_nomination.fields.date') }}</label>
                            <el-select v-model="advancedFilters.shift"
                                :placeholder="$t('common.select.placeholder')" clearable class="w-full">
                                <el-option :label="$t('production.shifts.day')" value="Day" />
                                <el-option :label="$t('production.shifts.night')" value="Night" />
                            </el-select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('production.sales_gas_nomination.fields.data_quality')
                                }}</label>
                            <el-select v-model="advancedFilters.data_quality" :placeholder="$t('common.select.placeholder')"
                                clearable class="w-full">
                                <el-option :label="$t('production.data_quality.good')" value="Good" />
                                <el-option :label="$t('production.data_quality.estimated')" value="Estimated" />
                                <el-option :label="$t('production.data_quality.bad')" value="Bad" />
                            </el-select>
                        </div>
                    </div>
                </template>
            </TableControls>

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

                            <el-table-column prop="date" :label="$t('production.sales_gas_nomination.fields.date')" sortable
                                width="150">
                                <template #default="scope">
                                    <div>
                                        {{ formatDate(scope.row.date) }}
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column prop="vessel.name" :label="$t('production.sales_gas_nomination.fields.vessel_id')" sortable
                                width="200" show-overflow-tooltip>
                                <template #default="scope">
                                    <div class="flex items-center space-x-2">
                                        <el-icon class="text-blue-600">
                                            <Icon icon="fluent:vehicle-ship-24-filled"/>
                                        </el-icon>
                                        <router-link :to="`/master/vessels/${scope.row.vessel_id}`"
                                            class="font-medium text-blue-600 hover:underline">
                                            {{ scope.row.vessel?.name || '-' }}
                                        </router-link>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column prop="name" :label="$t('production.sales_gas_nomination.fields.name')" sortable show-overflow-tooltip>
                                <template #default="scope">
                                    <router-link :to="`/production/sales-gas-nomination/${scope.row.id}`"
                                        class="font-medium text-blue-600 hover:underline">
                                        {{ scope.row?.name || '-' }}
                                    </router-link>
                                </template>
                            </el-table-column>
                            <el-table-column prop="total_nomination" :label="$t('production.sales_gas_nomination.fields.total_nomination')" sortable
                                width="180" align="right">
                                <template #default="scope">
                                    <span class="font-mono">{{ formatNumber(scope.row.total_nomination, 2) }} MMSCF</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="total_confirmed" :label="$t('production.sales_gas_nomination.fields.total_confirmed')" sortable
                                width="180" align="right">
                                <template #default="scope">
                                    <span class="font-mono">{{ formatNumber(scope.row.total_confirmed, 2) }} MMSCF</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="status" :label="$t('production.sales_gas_nomination.fields.status')" sortable
                                width="180" align="right">
                                <template #default="scope">
                                    <!-- <span class="font-mono">{{ scope.row.status }}</span> -->
                                    <el-tag :type="getStatusTagType(scope.row.status)">
                                        {{ $t(`production.sales_gas_nomination.status.${scope.row.status}`) }}
                                    </el-tag>
                                </template>
                            </el-table-column>
                            <el-table-column prop="created_by" :label="$t('common.fields.created_by')" sortable
                                width="180" align="right">
                                <template #default="scope">
                                    {{ scope.row.created_by?.name || '-' }}
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
                                                <el-dropdown-item @click="onEdit(scope.row)">
                                                    <Icon icon="mingcute:edit-line" class="me-2" />
                                                    {{ $t('common.actions.edit') }}
                                                </el-dropdown-item>
                                                <el-dropdown-item divided class="text-red-600"
                                                    @click="onDelete(scope.row)" v-if="scope.row.id != 1">
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
                    <Pagination :current-page="data.meta.current_page" :per-page="data.meta.per_page"
                        :total="data?.meta.total" :last-page="data?.meta.last_page"
                        @page-change="handlePageChange" @per-page-change="handlePerPageChange" />
                </template>
            </el-skeleton>
        </el-card>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'
import { ElMessageBox, ElMessage } from 'element-plus'
import { Icon } from '@iconify/vue'
import _ from 'lodash'
import { useQuery } from '@tanstack/vue-query'
import { useI18n } from 'vue-i18n'
import { useFormatter } from '@/composables/common/useFormatter'
import { useRouter } from 'vue-router'
import { useUser } from '@/composables/auth'
// Import our reusable components
import PageHeader from '@/components/PageHeader.vue'
import TableControls from '@/components/TableControls.vue'
import Pagination from '@/components/Pagination.vue'
import SkeletonTable from '@/components/SkeletonTable.vue'


const { formatDate, formatNumber } = useFormatter();
const { t } = useI18n();
const router = useRouter();

// Page state
const selectedRows = ref([]);
const { userVesselId } = useUser();
// Filter state
const params = ref({
  limit: 25,
  page: 1,
  q: "",
  sort: 'reading_datetime',
  sortDir: 'desc',
  vessel_id: userVesselId,
});

const advancedFilters = ref({
  shift: null,
  data_quality: null,
});

const getStatusTagType = (status) => {
  const statusMap = {
    'draft': 'warning',
    'confirmed': 'success',
    'cancel' : "danger"
  }
  return statusMap[status] || 'info'
}
//


const fetchData = async ({ queryKey }) => {
  const [_key, queryParams] = queryKey;
  const response = await axios.get("/production/sales-gas-nomination", {
      params: queryParams,
  });
  return response.data;
};

const { data, isLoading, refetch } = useQuery({
  queryKey: ['wellProductionReadings', params.value],
  queryFn: fetchData,
  keepPreviousData: true,
});

// Event handlers
const onCreate = () => {
  router.push({ name: 'production.sales_gas_nomination.create' });
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

//

const onView = (row) => {
  router.push({ name: 'production.sales_gas_nomination.show', params: { id: row.id } });
};

const onEdit = (row) => {
  router.push({ name: 'production.sales_gas_nomination.edit', params: { id: row.id } });
};

const onDelete = async (row) => {
  try {
      await ElMessageBox.confirm(
          t('common.confirmations.delete.message'),
          t('common.confirmations.deleete.title'),
          {
              confirmButtonText: t('common.actions.delete'),
              cancelButtonText: t('common.actions.cancel'),
              type: 'warning',
          }
      );

      await axios.delete(`/production/sales-gas-nomination/${row.id}/delete`);
      ElMessage.success(t('common.messages.deleted'));
      refetch();
  } catch (error) {
      if (error !== 'cancel') {
          ElMessage.error(t('common.error.delete_failed'));
      }
  }
};

// Bulk operations
const bulkExport = () => {
  ElMessage.info(t('common.feature_coming_soon'));
};

const bulkDelete = async () => {
  if (selectedRows.value.length === 0) {
      ElMessage.warning(t('common.select_items_first'));
      return;
  }

  try {
      await ElMessageBox.confirm(
          t('common.confirm.bulk_delete_message', { count: selectedRows.value.length }),
          t('common.confirm.delete_title'),
          {
              confirmButtonText: t('common.delete'),
              cancelButtonText: t('common.cancel'),
              type: 'warning',
          }
      );

      const ids = selectedRows.value.map(row => row.id);
      await axios.delete('/production/sales-gas-nomination/bulk', { data: { ids } });
      ElMessage.success(t('common.success.bulk_deleted'));
      selectedRows.value = [];
      refetch();
  } catch (error) {
      if (error !== 'cancel') {
          ElMessage.error(t('common.error.bulk_delete_failed'));
      }
  }
};

// Selection handling
const handleSelectionChange = (selection) => {
  selectedRows.value = selection;
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