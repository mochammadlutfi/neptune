<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader :title="$t('master.personnel_positions.title')" :primary-action="{
              label: $t('master.personnel_positions.create'),
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
                            <el-table-column prop="code" :label="$t('master.personnel_positions.fields.code')" min-width="120" sortable="custom">
                                <template #default="{ row }">
                                    <span class="font-medium text-gray-900">{{ row.code }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="name" :label="$t('master.personnel_positions.fields.name')" min-width="200" sortable="custom">
                                <template #default="{ row }">
                                    <div class="flex items-center space-x-2">
                                        <Icon icon="mingcute:user-4-line" class="w-4 h-4 text-purple-500" />
                                        <span class="font-medium text-gray-900">{{ row.name }}</span>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column prop="department_level" :label="$t('master.personnel_positions.fields.department_level')" min-width="160">
                                <template #default="{ row }">
                                    <div class="space-y-1">
                                        <div class="font-medium text-gray-700">{{ row.department }}</div>
                                        <el-tag :type="getLevelTagType(row.level)" size="small">
                                            {{ row.level }}
                                        </el-tag>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column prop="flags" :label="$t('master.personnel_positions.fields.flags')" min-width="140">
                                <template #default="{ row }">
                                    <div class="flex flex-wrap gap-1">
                                        <el-tag v-if="row.is_safety_critical" type="danger" size="small">
                                            Safety Critical
                                        </el-tag>
                                        <el-tag v-if="row.is_offshore_required" type="warning" size="small">
                                            Offshore
                                        </el-tag>
                                        <el-tag v-if="row.is_leadership" type="primary" size="small">
                                            Leadership
                                        </el-tag>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column prop="min_experience_years" :label="$t('master.personnel_positions.fields.min_experience_years')" min-width="140" sortable="custom">
                                <template #default="{ row }">
                                    <div class="text-center">
                                        <span class="font-medium text-gray-700">{{ row.min_experience_years }}</span>
                                        <span class="text-sm text-gray-500 ml-1">years</span>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column prop="certificates_count" :label="$t('master.personnel_positions.fields.certificates_count')" min-width="140" sortable="custom">
                                <template #default="{ row }">
                                    <div class="text-center">
                                        <el-tag :type="getCertificatesCountTagType(row.certificates_count)" size="small">
                                            {{ row.certificates_count }} certs
                                        </el-tag>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column prop="is_active" :label="$t('master.personnel_positions.fields.is_active')" min-width="120" sortable="custom">
                                <template #default="{ row }">
                                    <el-tag :type="row.is_active ? 'success' : 'danger'" size="small">
                                        {{ row.is_active ? 'Active' : 'Inactive' }}
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
                                                    v-if="can('update', 'personnel_position')">
                                                    <Icon icon="mingcute:edit-line" class="me-2" />
                                                    {{ $t('common.actions.edit') }}
                                                </el-dropdown-item>
                                                <el-dropdown-item divided class="text-red-600"
                                                    @click="onDelete(scope.row)" v-if="can('delete', 'personnel_position')">
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
  department: null,
  level: null,
  is_active: null,
  is_safety_critical: null
});

const advancedFilters = ref({
  department: null,
  level: null,
  is_active: null,
  is_safety_critical: null
});

// Helper methods
const getLevelTagType = (level) => {
  const levelMap = {
    'Executive': 'danger',
    'Manager': 'warning',
    'Supervisor': 'primary',
    'Senior': 'success',
    'Junior': 'info'
  };
  return levelMap[level] || '';
};

const getCertificatesCountTagType = (count) => {
  if (count >= 5) return 'success';
  if (count >= 3) return 'warning';
  if (count >= 1) return 'info';
  return 'danger';
};

const fetchData = async ({ queryKey }) => {
  const [_key, queryParams] = queryKey;
  const response = await axios.get("/master/personnel-positions", {
    params: queryParams,
  });
  return response.data;
};

const { data, isLoading, isError, error, refetch } = useQuery({
  queryKey: ['MasterIndexPersonnelPositions', params.value],
  queryFn: fetchData,
  keepPreviousData: true,
});

// Event handlers
const onCreate = () => {
  router.push({ name: 'master.personnel-positions.create' });
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
  router.push({ name: 'master.personnel-positions.show', params: { id: row.id } });
};

const onEdit = (row) => {
  router.push({ name: 'master.personnel-positions.edit', params: { id: row.id } });
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

      await axios.delete(`/master/personnel-positions/${row.id}`);
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