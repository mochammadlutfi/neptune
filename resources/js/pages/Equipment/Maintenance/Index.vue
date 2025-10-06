<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader :title="$t('equipment.maintenance.title')" :primary-action="{
            label: $t('equipment.maintenance.create'),
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.maintenance.fields.buyer_name')
                                }}</label>
                            <el-input v-model="advancedFilters.buyer_name"
                                :placeholder="$t('common.search.search_placeholder')" clearable class="w-full" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.maintenance.fields.buyer_confirmed')
                                }}</label>
                            <el-select v-model="advancedFilters.buyer_confirmed" :placeholder="$t('common.select.placeholder')"
                                clearable class="w-full">
                                <el-option :label="$t('common.yes')" :value="true" />
                                <el-option :label="$t('common.no')" :value="false" />
                            </el-select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.maintenance.fields.data_validated')
                                }}</label>
                            <el-select v-model="advancedFilters.data_validated" :placeholder="$t('common.select.placeholder')"
                                clearable class="w-full">
                                <el-option :label="$t('common.yes')" :value="true" />
                                <el-option :label="$t('common.no')" :value="false" />
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

                            <el-table-column prop="reading_time" :label="$t('equipment.maintenance.fields.reading_time')" sortable
                                width="140" fixed="left">
                                <template #default="scope">
                                    {{ formatDate(scope.row.reading_time) }}
                                </template>
                            </el-table-column>


                            <el-table-column prop="equipment" :label="$t('equipment.maintenance.fields.equipment_id')" sortable
                                min-width="120" show-overflow-tooltip>
                                <template #default="scope">
                                    <span class="font-medium">{{ scope.row.equipment.name || '-' }}</span>
                                </template>
                            </el-table-column>
<!-- 
                            <el-table-column prop="flowrate_mmscfd" :label="$t('equipment.maintenance.fields.flowrate')" sortable
                                width="140" align="right">
                                <template #default="scope">
                                    <span class="font-mono" v-if="scope.row.flowrate_mmscfd">{{ formatNumber(scope.row.flowrate_mmscfd, 3) }} MMSCFD</span>
                                    <span v-else>-</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="actual_delivery_mmscf" :label="$t('equipment.maintenance.fields.actual')" sortable
                                width="140" align="right">
                                <template #default="scope">
                                    <span class="font-mono" v-if="scope.row.actual_delivery_mmscf">{{ formatNumber(scope.row.flowrate_mmscfd, 3) }} MMSCF</span>
                                    <span v-else>-</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="nomination_mmscf" :label="$t('equipment.maintenance.fields.nomination_mmscf')" sortable
                                width="130" align="right">
                                <template #default="scope">
                                    <span class="font-mono">{{ formatNumber(scope.row.nomination_mmscf, 3) }}</span>
                                </template>
                            </el-table-column>
                            
                            -->

                            <el-table-column prop="operational_status" :label="$t('equipment.maintenance.fields.operational_status')" sortable
                                width="140" align="right">
                                <template #default="scope">
                                    <span class="font-mono" :class="getVarianceClass(scope.row.operational_status)">
                                        {{ scope.row.operational_status }}
                                    </span>
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
                    <Pagination :current-page="data?.meta.current_page || 1" :per-page="data?.meta.per_page || 25"
                        :total="data?.meta.total || 0" :last-page="data?.meta.last_page || 1"
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
// Import our reusable components
import PageHeader from '@/components/PageHeader.vue';
import StatisticCard from '@/components/StatisticCard.vue';
import TableControls from '@/components/TableControls.vue';
import Pagination from '@/components/Pagination.vue';
import SkeletonTable from '@/components/SkeletonTable.vue';
const { formatDate } = useFormatter();
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
  sort: 'reading_timestamp',
  sortDir: 'desc',
});

const advancedFilters = ref({
  buyer_name: null,
  buyer_confirmed: null,
  data_validated: null,
});

// Computed properties
const activeFilters = computed(() => {
  const filters = [];
  if (advancedFilters.value.buyer_name) {
      filters.push({
          key: 'buyer_name',
          label: t('equipment.maintenance.fields.buyer_name'),
          value: advancedFilters.value.buyer_name
      });
  }
  if (advancedFilters.value.buyer_confirmed !== null) {
      filters.push({
          key: 'buyer_confirmed',
          label: t('equipment.maintenance.fields.buyer_confirmed'),
          value: advancedFilters.value.buyer_confirmed ? t('common.yes') : t('common.no')
      });
  }
  if (advancedFilters.value.data_validated !== null) {
      filters.push({
          key: 'data_validated',
          label: t('equipment.maintenance.fields.data_validated'),
          value: advancedFilters.value.data_validated ? t('common.yes') : t('common.no')
      });
  }
  return filters;
});

// Helper methods untuk production wells

const formatNumber = (value, decimals = 2) => {
  if (value === null || value === undefined) return '-';
  return Number(value).toFixed(decimals);
};

const getVarianceClass = (variance) => {
  if (variance === null || variance === undefined) return 'text-gray-400';
  const absVariance = Math.abs(variance);
  if (absVariance <= 2) return 'text-green-600';
  if (absVariance <= 5) return 'text-yellow-600';
  return 'text-red-600';
};

const fetchData = async ({ queryKey }) => {
  const [_key, queryParams] = queryKey;
  const response = await axios.get("/equipment/status", {
      params: queryParams,
  });
  return response.data;
};

const { data, isLoading, isError, error, refetch } = useQuery({
  queryKey: ['gasSalesMetering', params.value],
  queryFn: fetchData,
  keepPreviousData: true,
});

// Event handlers
const onCreate = () => {
  router.push({ name: 'equipment.maintenance.create' });
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
      buyer_name: advancedFilters.value.buyer_name,
      buyer_confirmed: advancedFilters.value.buyer_confirmed,
      data_validated: advancedFilters.value.data_validated,
      page: 1
  });
  refetch();
};

const clearAllFilters = () => {
  advancedFilters.value = {
      buyer_name: null,
      buyer_confirmed: null,
      data_validated: null,
  };
  params.value = {
      ...params.value,
      buyer_name: null,
      buyer_confirmed: null,
      data_validated: null,
      page: 1
  };
  refetch();
};

const removeFilter = (filterKey) => {
  if (filterKey === 'buyer_name') {
      advancedFilters.value.buyer_name = null;
  } else if (filterKey === 'buyer_confirmed') {
      advancedFilters.value.buyer_confirmed = null;
  } else if (filterKey === 'data_validated') {
      advancedFilters.value.data_validated = null;
  } else {
      advancedFilters.value[filterKey] = null;
  }
  applyAdvancedFilters();
};

const onView = (row) => {
  router.push({ name: 'equipment.maintenance.show', params: { id: row.id } });
};

const onEdit = (row) => {
  router.push({ name: 'equipment.maintenance.edit', params: { id: row.id } });
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

      await axios.delete(`/equipment/status/${row.id}/delete`);
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
      await axios.delete('/equipment/status/bulk', { data: { ids } });
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