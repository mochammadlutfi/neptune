<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader :title="$t('master.equipment.title')" :primary-action="{
              label: $t('master.equipment.create'),
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
                            <el-table-column prop="code" :label="$t('master.equipment.fields.code')" min-width="120" sortable="custom">
                              <template #default="{ row }">
                                <span class="font-medium text-gray-900">{{ row.code }}</span>
                              </template>
                            </el-table-column>

                            <el-table-column prop="tag" :label="$t('master.equipment.fields.tag')" min-width="120" sortable="custom">
                              <template #default="{ row }">
                                <span class="font-mono text-sm text-blue-600">{{ row.tag }}</span>
                              </template>
                            </el-table-column>

                            <el-table-column prop="name" :label="$t('master.equipment.fields.name')" min-width="200" sortable="custom">
                              <template #default="{ row }">
                                <div class="flex items-center space-x-2">
                                  <Icon icon="mingcute:tool-line" class="w-4 h-4 text-orange-500" />
                                  <span class="font-medium text-gray-900">{{ row.name }}</span>
                                </div>
                              </template>
                            </el-table-column>

                            <el-table-column prop="vessel_name" :label="$t('master.equipment.fields.vessel_id')" min-width="150" sortable="custom">
                              <template #default="{ row }">
                                  <div class="flex items-center space-x-2" v-if="row.vessel">
                                      <el-icon class="text-blue-600">
                                          <Icon icon="fluent:vehicle-ship-24-filled"/>
                                      </el-icon>
                                      <router-link :to="`/master/vessels/${row.id}`"
                                          class="font-medium text-blue-600 hover:underline">
                                          {{ row.name }}
                                      </router-link>
                                  </div>
                                  <span v-else class="text-gray-400">-</span>
                              </template>
                            </el-table-column>

                            <el-table-column prop="type_category" :label="$t('master.equipment.fields.type_category')" min-width="160">
                              <template #default="{ row }">
                                <div class="space-y-1">
                                  <el-tag :type="getEquipmentTypeTagType(row.type)" size="small">
                                    {{ row.type }}
                                  </el-tag>
                                  <div class="text-xs text-gray-500">{{ row.category }}</div>
                                </div>
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
                                                    v-if="can('update', 'equipment')">
                                                    <Icon icon="mingcute:edit-line" class="me-2" />
                                                    {{ $t('common.actions.edit') }}
                                                </el-dropdown-item>
                                                <el-dropdown-item divided class="text-red-600"
                                                    @click="onDelete(scope.row)" v-if="can('delete', 'equipment')">
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
  tag: null,
  type: null,
  critical_status: null,
  vessel_id: null,
  category: null
});

const advancedFilters = ref({
  type: null,
  critical_status: null,
  vessel_id: null,
  category: null
});

// Helper methods
const getEquipmentTypeTagType = (type) => {
  const typeMap = {
    'Rotating': 'primary',
    'Static': 'success',
    'Electrical': 'warning',
    'Instrumentation': 'info',
    'Safety': 'danger'
  };
  return typeMap[type] || '';
};

const getCriticalStatusTagType = (status) => {
  const statusMap = {
    'Critical': 'danger',
    'Important': 'warning',
    'Normal': 'success',
    'Spare': 'info'
  };
  return statusMap[status] || '';
};

const fetchData = async ({ queryKey }) => {
  const [_key, queryParams] = queryKey;
  const response = await axios.get("/master/equipment", {
    params: queryParams,
  });
  return response.data;
};

const { data, isLoading, isError, error, refetch } = useQuery({
  queryKey: ['MasterIndexEquipment', params.value],
  queryFn: fetchData,
  keepPreviousData: true,
});

// Event handlers
const onCreate = () => {
  router.push({ name: 'master.equipment.create' });
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
  router.push({ name: 'master.equipment.show', params: { id: row.id } });
};

const onEdit = (row) => {
  router.push({ name: 'master.equipment.edit', params: { id: row.id } });
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

      await axios.delete(`/master/equipment/${row.id}`);
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