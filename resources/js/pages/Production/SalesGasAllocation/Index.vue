<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader :title="$t('production.wells.title')" :primary-action="{
            label: $t('production.wells.create'),
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('production.wells.fields.shift')
                                }}</label>
                            <el-select v-model="advancedFilters.shift"
                                :placeholder="$t('common.select.placeholder')" clearable class="w-full">
                                <el-option :label="$t('production.shifts.day')" value="Day" />
                                <el-option :label="$t('production.shifts.night')" value="Night" />
                            </el-select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('production.wells.fields.data_quality')
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

                            <el-table-column prop="reading_datetime" :label="$t('production.wells.fields.reading_datetime')" sortable
                                width="150" fixed="left">
                                <template #default="scope">
                                    <div>
                                        {{ formatDateTime(scope.row.reading_datetime) }}
                                    </div>
                                    <div>
                                        <el-tag :type="scope.row.shift === 'Day' ? 'warning' : 'info'" size="small">
                                            {{ $t(`production.wells.shifts.${scope.row.shift?.toLowerCase()}`) }}
                                        </el-tag>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column prop="well.name" :label="$t('production.wells.fields.well_id')" sortable
                                min-width="200" show-overflow-tooltip>
                                <template #default="scope">
                                    <div class="flex items-center space-x-2">
                                        <el-icon class="text-blue-600">
                                            <Icon icon="mingcute:oil-line" />
                                        </el-icon>
                                        <router-link :to="`/production/wells/${scope.row.id}`"
                                            class="font-medium text-blue-600 hover:underline">
                                            {{ scope.row.well?.name || '-' }}
                                        </router-link>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column prop="oil_rate_bph" :label="$t('production.wells.fields.oil_rate')" sortable
                                width="120" align="right">
                                <template #default="scope">
                                    <span class="font-mono">{{ formatNumber(scope.row.oil_rate_bph, 2) }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="gas_rate_mscfh" :label="$t('production.wells.fields.gas_rate')" sortable
                                width="130" align="right">
                                <template #default="scope">
                                    <span class="font-mono">{{ formatNumber(scope.row.gas_rate_mscfh, 3) }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="water_rate_bph" :label="$t('production.wells.fields.water_rate')" sortable
                                width="130" align="right">
                                <template #default="scope">
                                    <span class="font-mono">{{ formatNumber(scope.row.water_rate_bph, 2) }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="wellhead_pressure_psi" :label="$t('production.wells.fields.wellhead_pressure')" sortable
                                width="170" align="right">
                                <template #default="scope">
                                    <span class="font-mono">{{ formatNumber(scope.row.wellhead_pressure_psi, 2) }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="wellhead_temperature_f" :label="$t('production.wells.fields.wellhead_temperature')" sortable
                                width="170" align="right">
                                <template #default="scope">
                                    <span class="font-mono">{{ formatNumber(scope.row.wellhead_temperature_f, 2) }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="choke_size_64th" :label="$t('production.wells.fields.choke_size')" sortable
                                width="110" align="right">
                                <template #default="scope">
                                    <span class="font-mono">{{ scope.row.choke_size_64th || '-' }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="flow_hours" :label="$t('production.wells.fields.flow_hours')" sortable
                                width="100" align="right">
                                <template #default="scope">
                                    <span class="font-mono">{{ formatNumber(scope.row.flow_hours, 2) }}</span>
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
import { ref, computed, watch, onMounted } from 'vue'
import axios from 'axios'
import { ElMessageBox, ElMessage } from 'element-plus'
import { Icon } from '@iconify/vue'
import _ from 'lodash'
import { useQuery } from '@tanstack/vue-query'
import { useI18n } from 'vue-i18n'
import { useFormatter } from '@/composables/common/useFormatter'
import { useRouter } from 'vue-router'
// Import our reusable components
import PageHeader from '@/components/PageHeader.vue'
import StatisticCard from '@/components/StatisticCard.vue'
import TableControls from '@/components/TableControls.vue'
import Pagination from '@/components/Pagination.vue'
import SkeletonTable from '@/components/SkeletonTable.vue'


const { formatCurrency } = useFormatter();
const { t } = useI18n();
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
  sort: 'reading_datetime',
  sortDir: 'desc',
});

const advancedFilters = ref({
  shift: null,
  data_quality: null,
  well_id: null,
});

// Computed properties
const activeFilters = computed(() => {
  const filters = [];
  if (advancedFilters.value.shift) {
      filters.push({
          key: 'shift',
          label: t('production.wells.fields.shift'),
          value: advancedFilters.value.shift
      });
  }
  if (advancedFilters.value.data_quality) {
      filters.push({
          key: 'data_quality',
          label: t('production.wells.fields.data_quality'),
          value: advancedFilters.value.data_quality
      });
  }
  return filters;
});

// Helper methods untuk production wells

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
  });
};

const formatDateTime = (datetime) => {
  if (!datetime) return '-';
  return new Date(datetime).toLocaleString('id-ID', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
  });
};

const formatNumber = (value, decimals = 2) => {
  if (value === null || value === undefined) return '-';
  return Number(value).toFixed(decimals);
};

const getDataQualityTagType = (quality) => {
  switch (quality) {
      case 'Good': return 'success';
      case 'Estimated': return 'warning';
      case 'Bad': return 'danger';
      default: return 'info';
  }
};

const fetchData = async ({ queryKey }) => {
  const [_key, queryParams] = queryKey;
  const response = await axios.get("/production/wells", {
      params: queryParams,
  });
  return response.data;
};

const { data, isLoading, isError, error, refetch } = useQuery({
  queryKey: ['wellProductionReadings', params.value],
  queryFn: fetchData,
  keepPreviousData: true,
});

// Event handlers
const onCreate = () => {
  router.push({ name: 'production.wells.create' });
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

const applyAdvancedFilters = () => {
  Object.assign(params.value, {
      shift: advancedFilters.value.shift,
      data_quality: advancedFilters.value.data_quality,
      well_id: advancedFilters.value.well_id,
      page: 1
  });
  refetch();
};

const clearAllFilters = () => {
  advancedFilters.value = {
      shift: null,
      data_quality: null,
      well_id: null,
  };
  params.value = {
      ...params.value,
      shift: null,
      data_quality: null,
      well_id: null,
      page: 1
  };
  refetch();
};

const removeFilter = (filterKey) => {
  if (filterKey === 'shift') {
      advancedFilters.value.shift = null;
  } else if (filterKey === 'data_quality') {
      advancedFilters.value.data_quality = null;
  } else {
      advancedFilters.value[filterKey] = null;
  }
  applyAdvancedFilters();
};

const onView = (row) => {
  router.push({ name: 'production.wells.show', params: { id: row.id } });
};

const onEdit = (row) => {
  router.push({ name: 'production.wells.edit', params: { id: row.id } });
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

      await axios.delete(`/production/wells/${row.id}/delete`);
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
      await axios.delete('/production/wells/bulk', { data: { ids } });
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