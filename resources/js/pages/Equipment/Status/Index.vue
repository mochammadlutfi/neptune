<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader :title="$t('equipment.status.title')" :primary-action="{
            label: $t('equipment.status.create'),
            icon: 'mingcute:add-line',
            click: onCreate
        }" />

        <el-card body-class="!p-0" class="!rounded-lg !shadow-md">
            <!-- Advanced Filter Section -->
            <TableControls v-model:search="params.q" :search-placeholder="$t('common.search.search_placeholder')"
                :loading="isLoading" :selected-rows="selectedRows" @refresh="refetch" @bulk-export="bulkExport"
                @bulk-delete="bulkDelete">
                <template #filters>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.status.fields.vessel_id') }}</label>
                            <el-select v-model="advancedFilters.vessel_id" :placeholder="$t('equipment.status.placeholder.vessel_id')"
                                clearable filterable class="w-full">
                                <el-option v-for="vessel in vessels" :key="vessel.id" :label="vessel.name" :value="vessel.id" />
                            </el-select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.status.fields.equipment_id') }}</label>
                            <el-select v-model="advancedFilters.equipment_id" :placeholder="$t('equipment.status.placeholder.equipment_id')"
                                clearable filterable class="w-full">
                                <el-option v-for="equipment in equipments" :key="equipment.id" :label="equipment.name" :value="equipment.id" />
                            </el-select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.status.fields.operational_status') }}</label>
                            <el-select v-model="advancedFilters.operational_status" :placeholder="$t('equipment.status.placeholder.operational_status')"
                                clearable class="w-full">
                                <el-option v-for="(label, value) in $t('equipment.status.operational_status')" :key="value" :label="label" :value="value" />
                            </el-select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('equipment.status.fields.shift') }}</label>
                            <el-select v-model="advancedFilters.shift" :placeholder="$t('equipment.status.placeholder.shift')"
                                clearable class="w-full">
                                <el-option v-for="(label, value) in $t('equipment.status.shifts')" :key="value" :label="label" :value="value" />
                            </el-select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('common.date_range') }}</label>
                            <el-date-picker v-model="advancedFilters.date_range" type="daterange"
                                :range-separator="$t('common.to')" :start-placeholder="$t('common.start_date')"
                                :end-placeholder="$t('common.end_date')" format="YYYY-MM-DD" value-format="YYYY-MM-DD"
                                class="w-full" />
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

                            <el-table-column prop="reading_time" :label="$t('equipment.status.fields.reading_time')" sortable
                                width="180" fixed="left">
                                <template #default="scope">
                                    {{ formatDateTime(scope.row.reading_time) }}
                                </template>
                            </el-table-column>

                            <el-table-column prop="vessel_name" :label="$t('equipment.status.fields.vessel_name')" sortable
                                min-width="150" show-overflow-tooltip>
                                <template #default="scope">
                                    <span class="font-medium">{{ scope.row.vessel_name || '-' }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="equipment_name" :label="$t('equipment.status.fields.equipment_name')" sortable
                                min-width="200" show-overflow-tooltip>
                                <template #default="scope">
                                    <span class="font-medium">{{ scope.row.equipment_name || '-' }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="equipment_code" :label="$t('equipment.status.fields.equipment_code')" sortable
                                width="120" show-overflow-tooltip>
                                <template #default="scope">
                                    <span class="font-mono">{{ scope.row.equipment_code || '-' }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="operational_status" :label="$t('equipment.status.fields.operational_status')" sortable
                                width="130" align="center">
                                <template #default="scope">
                                    <el-tag :type="getOperationalStatusTagType(scope.row.operational_status)">
                                        {{ $t(`equipment.status.operational_status.${scope.row.operational_status}`) }}
                                    </el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column prop="shift" :label="$t('equipment.status.fields.shift')" sortable
                                width="100" align="center">
                                <template #default="scope">
                                    <span>{{ $t(`equipment.status.shifts.${scope.row.shift}`) }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="recorded_by" :label="$t('equipment.status.fields.recorded_by')" sortable
                                width="150" show-overflow-tooltip>
                                <template #default="scope">
                                    <span>{{ scope.row.recorded_by || '-' }}</span>
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
  sort: 'reading_time',
  sortDir: 'desc',
});

const advancedFilters = ref({
  vessel_id: null,
  equipment_id: null,
  operational_status: null,
  shift: null,
  date_from: null,
  date_to: null,
});

// Computed properties
const activeFilters = computed(() => {
  const filters = [];
  if (advancedFilters.value.vessel_id) {
      filters.push({
          key: 'vessel_id',
          label: t('equipment.status.fields.vessel_id'),
          value: advancedFilters.value.vessel_id
      });
  }
  if (advancedFilters.value.equipment_id) {
      filters.push({
          key: 'equipment_id',
          label: t('equipment.status.fields.equipment_id'),
          value: advancedFilters.value.equipment_id
      });
  }
  if (advancedFilters.value.operational_status) {
      filters.push({
          key: 'operational_status',
          label: t('equipment.status.fields.operational_status'),
          value: t(`equipment.status.operational_status.${advancedFilters.value.operational_status}`)
      });
  }
  if (advancedFilters.value.shift) {
      filters.push({
          key: 'shift',
          label: t('equipment.status.fields.shift'),
          value: t(`equipment.status.shift.${advancedFilters.value.shift}`)
      });
  }
  if (advancedFilters.value.date_from) {
      filters.push({
          key: 'date_from',
          label: t('equipment.status.fields.date_from'),
          value: formatDate(advancedFilters.value.date_from)
      });
  }
  if (advancedFilters.value.date_to) {
      filters.push({
          key: 'date_to',
          label: t('equipment.status.fields.date_to'),
          value: formatDate(advancedFilters.value.date_to)
      });
  }
  return filters;
});

// Helper methods for equipment status
const formatDateTime = (dateTime) => {
  if (!dateTime) return '-';
  return new Date(dateTime).toLocaleString();
};

const getOperationalStatusTagType = (status) => {
  const statusMap = {
    'operational': 'success',
    'maintenance': 'warning',
    'breakdown': 'danger',
    'standby': 'info'
  };
  return statusMap[status] || 'info';
};

const fetchData = async ({ queryKey }) => {
  const [_key, queryParams] = queryKey;
  const response = await axios.get("/equipment/status", {
      params: queryParams,
  });
  return response.data;
};

const { data, isLoading, isError, error, refetch } = useQuery({
  queryKey: ['equipmentStatus', params.value],
  queryFn: fetchData,
  keepPreviousData: true,
});

// Event handlers
const onCreate = () => {
  router.push({ name: 'equipment.status.create' });
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
      vessel_id: advancedFilters.value.vessel_id,
      equipment_id: advancedFilters.value.equipment_id,
      operational_status: advancedFilters.value.operational_status,
      shift: advancedFilters.value.shift,
      date_from: advancedFilters.value.date_from,
      date_to: advancedFilters.value.date_to,
      page: 1
  });
  refetch();
};

const clearAllFilters = () => {
  advancedFilters.value = {
      vessel_id: null,
      equipment_id: null,
      operational_status: null,
      shift: null,
      date_from: null,
      date_to: null,
  };
  params.value = {
      ...params.value,
      vessel_id: null,
      equipment_id: null,
      operational_status: null,
      shift: null,
      date_from: null,
      date_to: null,
      page: 1
  };
  refetch();
};

const removeFilter = (filterKey) => {
  if (filterKey === 'vessel_id') {
      advancedFilters.value.vessel_id = null;
  } else if (filterKey === 'equipment_id') {
      advancedFilters.value.equipment_id = null;
  } else if (filterKey === 'operational_status') {
      advancedFilters.value.operational_status = null;
  } else if (filterKey === 'shift') {
      advancedFilters.value.shift = null;
  } else if (filterKey === 'date_from') {
      advancedFilters.value.date_from = null;
  } else if (filterKey === 'date_to') {
      advancedFilters.value.date_to = null;
  } else {
      advancedFilters.value[filterKey] = null;
  }
  applyAdvancedFilters();
};

const onView = (row) => {
  router.push({ name: 'equipment.status.show', params: { id: row.id } });
};

const onEdit = (row) => {
  router.push({ name: 'equipment.status.edit', params: { id: row.id } });
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