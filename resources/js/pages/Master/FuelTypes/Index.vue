<template>
  <div class="content">
    <!-- Modern Page Header -->
    <PageHeader
        :title="$t('master.fuel_types.title')"
        :primary-action="{
            label: $t('master.fuel_types.create'),
            icon: 'mingcute:add-line',
            click: onCreate
        }"
    />

    <el-card body-class="!p-0" class="!rounded-lg !shadow-md">
      <!-- Advanced Filter Section -->
      <TableControls
        v-model:search="params.q"
        :search-placeholder="$t('common.search.search_placeholder')"
        :loading="isLoading"
        :selected-rows="selectedRows"
        @refresh="refetch"
        @bulk-export="bulkExport"
        @bulk-delete="bulkDelete"
      >
        <template #filters>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <el-select
              v-model="params.type"
              :placeholder="$t('master.fuel_types.fields.type')"
              clearable
              class="w-full"
              @change="refetch"
            >
              <el-option
                v-for="(label, value) in fuelTypes"
                :key="value"
                :label="label"
                :value="value"
              />
            </el-select>
            
            <el-input
              v-model="params.grade"
              :placeholder="$t('master.fuel_types.fields.grade')"
              clearable
              class="w-full"
              @input="doSearch"
            />
            
            <el-select
              v-model="params.unit"
              :placeholder="$t('master.fuel_types.fields.unit')"
              clearable
              class="w-full"
              @change="refetch"
            >
              <el-option
                v-for="unit in units"
                :key="unit"
                :label="unit"
                :value="unit"
              />
            </el-select>
            
            <el-select
              v-model="params.is_active"
              :placeholder="$t('common.status.status')"
              clearable
              class="w-full"
              @change="refetch"
            >
              <el-option :label="$t('common.status.active')" :value="1" />
              <el-option :label="$t('common.status.inactive')" :value="0" />
            </el-select>
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
            <el-table 
                class="base-table" 
                :data="data?.data || []" 
                @sort-change="sortChange" 
                @selection-change="handleSelectionChange"
                row-key="id"
                stripe
            >
              <el-table-column type="selection" width="55" />
              
              <el-table-column 
                prop="code" 
                :label="$t('master.fuel_types.fields.code')"
                sortable="custom"
                min-width="120"
              >
                <template #default="{ row }">
                  <span class="font-semibold text-primary">{{ row.code }}</span>
                </template>
              </el-table-column>
              
              <el-table-column 
                prop="name" 
                :label="$t('master.fuel_types.fields.name')"
                sortable="custom"
                min-width="180"
              >
                <template #default="{ row }">
                  <div class="flex items-center space-x-2">
                    <el-icon class="text-orange-600">
                      <Icon icon="mingcute:gas-station-line"/>
                    </el-icon>
                    <router-link :to="`/master/fuel-types/${row.id}`"
                        class="font-medium text-blue-600 hover:underline">
                        {{ row.name }}
                    </router-link>
                  </div>
                </template>
              </el-table-column>
              
              <el-table-column 
                prop="type_grade" 
                :label="$t('master.fuel_types.fields.type_grade')"
                sortable="custom"
                min-width="140"
              >
                <template #default="{ row }">
                  <div>
                    <el-tag :type="getFuelTypeTagType(row.type)" size="small">{{ row.type }}</el-tag>
                    <div class="text-xs text-gray-500 mt-1">{{ row.grade || '-' }}</div>
                  </div>
                </template>
              </el-table-column>
              
              <el-table-column 
                prop="specifications" 
                :label="$t('master.fuel_types.fields.specifications')"
                min-width="160"
                show-overflow-tooltip
              >
                <template #default="{ row }">
                  <div class="text-sm">
                    <div v-if="row.density_kg_l">Density: {{ row.density_kg_l }} kg/L</div>
                    <div v-if="row.flash_point">Flash Point: {{ row.flash_point }}°C</div>
                    <div v-if="row.viscosity">Viscosity: {{ row.viscosity }}</div>
                    <span v-if="!row.density_kg_l && !row.flash_point && !row.viscosity" class="text-gray-400">-</span>
                  </div>
                </template>
              </el-table-column>
              
              <el-table-column 
                prop="unit" 
                :label="$t('master.fuel_types.fields.unit')"
                width="100"
              >
                <template #default="{ row }">
                  <span class="text-sm font-medium">{{ row.unit || 'Litres' }}</span>
                </template>
              </el-table-column>
              
              <el-table-column 
                prop="storage_info" 
                :label="$t('master.fuel_types.fields.storage_info')"
                min-width="140"
              >
                <template #default="{ row }">
                  <div class="text-sm">
                    <div v-if="row.tank_capacity">Capacity: {{ formatNumber(row.tank_capacity) }} {{ row.unit }}</div>
                    <div v-if="row.storage_temperature">Temp: {{ row.storage_temperature }}°C</div>
                    <span v-if="!row.tank_capacity && !row.storage_temperature" class="text-gray-400">-</span>
                  </div>
                </template>
              </el-table-column>
              
              <el-table-column 
                prop="is_active" 
                :label="$t('common.status.status')"
                sortable="custom"
                width="100"
              >
                <template #default="{ row }">
                  <el-tag 
                    :type="row.is_active ? 'success' : 'danger'"
                    size="small"
                  >
                    {{ row.is_active ? $t('common.status.active') : $t('common.status.inactive') }}
                  </el-tag>
                </template>
              </el-table-column>
              
              <el-table-column 
                :label="$t('common.actions.action')"
                width="180"
                fixed="right"
              >
                <template #default="{ row }">
                  <div class="flex gap-2">
                    <el-tooltip :content="$t('common.actions.view')" placement="top">
                      <el-button 
                        size="small" 
                        circle 
                        @click="onView(row)"
                      >
                        <Icon icon="mingcute:eye-line" />
                      </el-button>
                    </el-tooltip>
                    
                    <el-tooltip :content="$t('common.actions.edit')" placement="top">
                      <el-button 
                        type="primary" 
                        size="small" 
                        circle 
                        @click="onEdit(row)"
                      >
                        <Icon icon="mingcute:edit-line" />
                      </el-button>
                    </el-tooltip>
                    
                    <el-tooltip :content="$t('common.actions.delete')" placement="top">
                      <el-button 
                        type="danger" 
                        size="small" 
                        circle 
                        @click="onDelete(row)"
                      >
                        <Icon icon="mingcute:delete-line" />
                      </el-button>
                    </el-tooltip>
                  </div>
                </template>
              </el-table-column>
            </el-table>
          </div>

          <!-- Pagination -->
          <Pagination
              :current-page="data?.meta.current_page || 1"
              :per-page="data?.meta.per_page || 25"
              :total="data?.meta.total || 0"
              :last-page="data?.meta.last_page || 1"
              @page-change="handlePageChange"
              @per-page-change="handlePerPageChange"
          />
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
import { useRouter } from 'vue-router';
import { useFormatter } from '@/composables/common/useFormatter';
import PageHeader from '@/components/PageHeader.vue';
import TableControls from '@/components/TableControls.vue';
import Pagination from '@/components/Pagination.vue';
import SkeletonTable from '@/components/SkeletonTable.vue';

const { t } = useI18n();
const router = useRouter();
const { formatNumber } = useFormatter();
const selectedRows = ref([]);

// Filter state
const params = ref({
  limit: 25,
  page: 1,
  q: "",
  sort: 'created_at',
  sortDir: 'desc',
  type: '',
  grade: '',
  unit: '',
  is_active: '',
});

// Fuel Types options
const fuelTypes = {
  'Diesel': 'Diesel',
  'Gas': 'Gas',
  'Bunker Fuel': 'Bunker Fuel',
  'Aviation Gas': 'Aviation Gas',
  'Lube Oil': 'Lube Oil',
  'Hydraulic Oil': 'Hydraulic Oil'
};

// Units options
const units = ['Litres', 'Gallons', 'Barrels', 'Cubic Meters', 'Kg', 'Tons'];

// Helper methods
const getFuelTypeTagType = (type) => {
  const typeMap = {
    'Diesel': 'primary',
    'Gas': 'success',
    'Bunker Fuel': 'warning',
    'Aviation Gas': 'info',
    'Lube Oil': 'danger',
    'Hydraulic Oil': ''
  };
  return typeMap[type] || '';
};

// Data fetching
const fetchData = async ({ queryKey }) => {
  const [_key, queryParams] = queryKey;
  const response = await axios.get("/master/fuel-types", {
      params: queryParams,
  });
  return response.data;
};

const { data, isLoading, isError, error, refetch } = useQuery({
  queryKey: ['MasterIndexFuelTypes', params.value],
  queryFn: fetchData,
  keepPreviousData: true,
});

// Event handlers
const onCreate = () => {
  router.push({ name: 'master.fuel_types.create' });
};

const doSearch = _.debounce(() => {
  params.value.page = 1;
  refetch();
}, 1000);

watch(() => params.value.q, doSearch);

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

const onView = (row) => {
  router.push({ name: 'master.fuel_types.show', params: { id: row.id } });
};

const onEdit = (row) => {
  router.push({ name: 'master.fuel_types.edit', params: { id: row.id } });
};

const onDelete = async (row) => {
  try {
      await ElMessageBox.confirm(
          t('common.confirmations.delete.message'),
          t('common.confirmations.delete.title'),
          {
              confirmButtonText: t('common.actions.delete'),
              cancelButtonText: t('common.actions.cancel'),
              type: 'warning',
          }
      );

      await axios.delete(`/api/master/fuel-types/${row.id}`);
      ElMessage.success(t('common.messages.deleted'));
      refetch();
  } catch (error) {
      if (error !== 'cancel') {
          ElMessage.error(t('common.errors.delete_failed'));
      }
  }
};

const handleSelectionChange = (selection) => {
  selectedRows.value = selection;
};

const bulkExport = () => {
  // Implement bulk export logic
  console.log('Bulk export:', selectedRows.value);
};

const bulkDelete = async () => {
  if (selectedRows.value.length === 0) return;
  
  try {
    await ElMessageBox.confirm(
      t('common.confirmations.bulk_delete.message', { count: selectedRows.value.length }),
      t('common.confirmations.bulk_delete.title'),
      {
        confirmButtonText: t('common.actions.delete'),
        cancelButtonText: t('common.actions.cancel'),
        type: 'warning',
      }
    );
    
    // Implement bulk delete logic
    const deletePromises = selectedRows.value.map(row => 
      axios.delete(`/api/master/fuel-types/${row.id}`)
    );
    
    await Promise.all(deletePromises);
    ElMessage.success(t('common.messages.deleted'));
    selectedRows.value = [];
    refetch();
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error(t('common.errors.delete_failed'));
    }
  }
};
</script>