<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader :title="$t('master.contracts.title')" :primary-action="{
            label: $t('master.contracts.create'),
            icon: 'mingcute:add-line',
            click: onCreate
        }" />

        <el-card body-class="!p-0" class="!rounded-lg !shadow-md overflow-hidden">
            <!-- Advanced Filter Section -->
            <div class="bg-white border-b border-gray-200">
                <!-- Filter Header -->
                <div class="flex items-center justify-between p-4 bg-gray-50 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <Icon icon="mingcute:filter-line" class="text-gray-600" />
                        <h3 class="font-medium text-gray-900">Filter & Pencarian</h3>
                        <el-badge v-if="activeFilters.length > 0" :value="activeFilters.length" class="ml-2" />
                    </div>
                    <el-button @click="showAdvancedFilters = !showAdvancedFilters" text class="filter-toggle">
                        <Icon :icon="showAdvancedFilters ? 'mingcute:up-line' : 'mingcute:down-line'" class="transition-transform duration-200" />
                    </el-button>
                </div>
                
                <!-- Main Controls -->
                <div class="p-4">
                    <TableControls v-model:search="params.q" :search-placeholder="$t('common.search.search_placeholder')"
                        :loading="isLoading" :selected-rows="selectedRows" @refresh="refetch" @bulk-export="bulkExport"
                        @bulk-delete="bulkDelete">
                        <template #extra-actions>
                            <!-- View Mode Toggle -->
                            <div class="flex items-center space-x-2">
                                <el-button-group>
                                    <el-button 
                                        :type="viewMode === 'table' ? 'primary' : 'default'"
                                        @click="viewMode = 'table'"
                                        size="small"
                                    >
                                        <Icon icon="mingcute:table-line" />
                                    </el-button>
                                    <el-button 
                                        :type="viewMode === 'card' ? 'primary' : 'default'"
                                        @click="viewMode = 'card'"
                                        size="small"
                                        class="hidden sm:inline-flex"
                                    >
                                        <Icon icon="mingcute:grid-line" />
                                    </el-button>
                                </el-button-group>
                            </div>
                        </template>
                    </TableControls>
                </div>
                
                <!-- Advanced Filters -->
                <el-collapse-transition>
                    <div v-show="showAdvancedFilters" class="px-4 pb-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                                <div class="filter-group">
                                    <label class="filter-label">
                                        <Icon icon="mingcute:tag-line" class="mr-1" />
                                        {{ $t('common.fields.type') }}
                                    </label>
                                    <el-select v-model="advancedFilters.contract_type"
                                        :placeholder="$t('common.select.placeholder')" clearable class="w-full filter-select" size="default">
                                        <el-option label="PSC" value="PSC">
                                            <div class="flex items-center">
                                                <Icon icon="mingcute:file-line" class="mr-2 text-blue-500" />
                                                PSC
                                            </div>
                                        </el-option>
                                        <el-option label="TAC" value="TAC">
                                            <div class="flex items-center">
                                                <Icon icon="mingcute:file-line" class="mr-2 text-green-500" />
                                                TAC
                                            </div>
                                        </el-option>
                                        <el-option label="Service Contract" value="Service Contract">
                                            <div class="flex items-center">
                                                <Icon icon="mingcute:tool-line" class="mr-2 text-orange-500" />
                                                Service Contract
                                            </div>
                                        </el-option>
                                        <el-option label="Joint Venture" value="Joint Venture">
                                            <div class="flex items-center">
                                                <Icon icon="mingcute:group-line" class="mr-2 text-purple-500" />
                                                Joint Venture
                                            </div>
                                        </el-option>
                                    </el-select>
                                </div>
                                
                                <div class="filter-group">
                                    <label class="filter-label">
                                        <Icon icon="mingcute:check-circle-line" class="mr-1" />
                                        {{ $t('common.fields.status') }}
                                    </label>
                                    <el-select v-model="advancedFilters.status" :placeholder="$t('common.select.placeholder')"
                                        clearable class="w-full filter-select" size="default">
                                        <el-option label="Active" value="Active">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                                Active
                                            </div>
                                        </el-option>
                                        <el-option label="Suspended" value="Suspended">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                                                Suspended
                                            </div>
                                        </el-option>
                                        <el-option label="Force Majeure" value="Force Majeure">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-orange-500 rounded-full mr-2"></div>
                                                Force Majeure
                                            </div>
                                        </el-option>
                                        <el-option label="Expired" value="Expired">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                                Expired
                                            </div>
                                        </el-option>
                                        <el-option label="Terminated" value="Terminated">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-gray-500 rounded-full mr-2"></div>
                                                Terminated
                                            </div>
                                        </el-option>
                                    </el-select>
                                </div>
                                
                                <div class="flex items-end">
                                    <el-button @click="applyAdvancedFilters" type="primary" class="w-full filter-action-btn">
                                        <Icon icon="mingcute:search-line" class="mr-2" />
                                        {{ $t('common.actions.apply_filters') }}
                                    </el-button>
                                </div>
                            </div>
                            
                            <!-- Filter Actions -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-gray-200">
                                <div class="flex flex-wrap gap-2">
                                    <el-button @click="clearAllFilters" size="default" class="filter-action-btn">
                                        <Icon icon="mingcute:refresh-line" class="mr-2" />
                                        {{ $t('common.actions.clear_all') }}
                                    </el-button>
                                </div>
                                
                                <!-- Quick Filter Stats -->
                                <div v-if="activeFilters.length > 0" class="text-sm text-gray-600">
                                    {{ data?.meta?.total || 0 }} hasil ditemukan
                                </div>
                            </div>
                            
                            <!-- Active Filters Display -->
                            <div v-if="activeFilters.length > 0" class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center mb-2">
                                    <Icon icon="mingcute:filter-2-line" class="mr-2 text-gray-500" />
                                    <span class="text-sm font-medium text-gray-700">{{ $t('common.active_filters') }}:</span>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <el-tag 
                                        v-for="filter in activeFilters" 
                                        :key="filter.key"
                                        closable 
                                        @close="removeFilter(filter.key)"
                                        size="default"
                                        class="filter-tag"
                                    >
                                        <Icon icon="mingcute:tag-line" class="mr-1" />
                                        {{ filter.label }}: {{ filter.value }}
                                    </el-tag>
                                </div>
                            </div>
                        </div>
                    </div>
                </el-collapse-transition>
            </div>

            <!-- Content Area -->
            <el-skeleton :loading="isLoading" animated>
                <template #template>
                    <SkeletonTable />
                </template>
                <template #default>
                    <!-- Empty State -->
                    <div v-if="!isLoading && (!data?.data || data.data.length === 0)" class="text-center py-12">
                        <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <Icon icon="mingcute:file-line" class="text-4xl text-gray-400" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $t('master.contracts.empty.title') }}</h3>
                        <p class="text-gray-500 mb-6">{{ $t('master.contracts.empty.description') }}</p>
                        <el-button type="primary" @click="onCreate">
                            <Icon icon="mingcute:add-line" class="mr-2" />
                            {{ $t('master.contracts.create') }}
                        </el-button>
                    </div>

                    <!-- Card View (Mobile) -->
                    <div v-else-if="viewMode === 'card'" class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="contract in data?.data || []" :key="contract.id" 
                                class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <!-- Card Header -->
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <el-tag type="info" size="small" class="mb-2">{{ contract.contract_number }}</el-tag>
                                        <h3 class="font-medium text-gray-900 text-sm leading-tight">
                                            <router-link :to="`/master/contracts/${contract.id}`" 
                                                class="text-blue-600 hover:underline">
                                                {{ contract.contract_name }}
                                            </router-link>
                                        </h3>
                                    </div>
                                    <el-dropdown popper-class="dropdown-action" placement="bottom-end" trigger="click">
                                        <el-button circle size="small" class="!p-1">
                                            <Icon icon="mingcute:more-2-fill" />
                                        </el-button>
                                        <template #dropdown>
                                            <el-dropdown-menu>
                                                <el-dropdown-item @click="onView(contract)">
                                                    <Icon icon="mingcute:eye-line" class="me-2" />
                                                    {{ $t('common.actions.view') }}
                                                </el-dropdown-item>
                                                <el-dropdown-item @click="onEdit(contract)">
                                                    <Icon icon="mingcute:edit-line" class="me-2" />
                                                    {{ $t('common.actions.edit') }}
                                                </el-dropdown-item>
                                                <el-dropdown-item divided class="text-red-600"
                                                    @click="onDelete(contract)" v-if="contract.id != 1">
                                                    <Icon icon="mingcute:delete-2-line" class="me-2" />
                                                    {{ $t('common.actions.delete') }}
                                                </el-dropdown-item>
                                            </el-dropdown-menu>
                                        </template>
                                    </el-dropdown>
                                </div>

                                <!-- Card Content -->
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">{{ $t('master.contracts.fields.contract_type') }}:</span>
                                        <el-tag :type="getContractTypeTagType(contract.contract_type)" size="small">
                                            {{ contract.contract_type }}
                                        </el-tag>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">{{ $t('master.contracts.fields.contract_status') }}:</span>
                                        <el-tag :type="getStatusType(contract.contract_status)" size="small">
                                            {{ contract.contract_status }}
                                        </el-tag>
                                    </div>
                                    <div v-if="contract.operator_name">
                                        <span class="text-gray-500">{{ $t('master.contracts.fields.operator_name') }}:</span>
                                        <span class="ml-2">{{ contract.operator_name }}</span>
                                    </div>
                                    <div v-if="contract.field_name || contract.block_name">
                                        <span class="text-gray-500">{{ $t('master.contracts.fields.field_name') }}:</span>
                                        <span class="ml-2">
                                            <span v-if="contract.field_name && contract.block_name">
                                                {{ contract.field_name }} - {{ contract.block_name }}
                                            </span>
                                            <span v-else-if="contract.field_name">
                                                {{ contract.field_name }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="pt-2 border-t border-gray-100">
                                        <div class="flex justify-between text-xs text-gray-500">
                                            <span>{{ formatDate(contract.effective_date) }}</span>
                                            <span>{{ formatDate(contract.expiry_date) }}</span>
                                        </div>
                                        <div v-if="contract.days_to_expiry !== null" class="mt-1 text-center">
                                            <el-tag :type="getDaysToExpiryTagType(contract.days_to_expiry)" size="small">
                                                {{ contract.days_to_expiry }} days to expiry
                                            </el-tag>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table View -->
                    <div v-else class="overflow-x-auto">
                        <el-table class="base-table" :data="data?.data || []" @sort-change="sortChange"
                            @selection-change="handleSelectionChange" row-key="id" stripe>
                            <el-table-column type="selection" width="50" align="center" />

                            <el-table-column prop="contract_number" :label="$t('master.contracts.fields.contract_number')" sortable
                                width="140" fixed="left">
                                <template #default="scope">
                                    <el-tag type="info" size="small">{{ scope.row.contract_number }}</el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column prop="contract_name" :label="$t('master.contracts.fields.contract_name')" sortable
                                min-width="200" show-overflow-tooltip>
                                <template #default="scope">
                                    <div class="flex items-center space-x-2">
                                        <el-icon class="text-blue-600">
                                            <Icon icon="mingcute:file-line" />
                                        </el-icon>
                                        <router-link :to="`/master/contracts/${scope.row.id}`"
                                            class="font-medium text-blue-600 hover:underline">
                                            {{ scope.row.contract_name }}
                                        </router-link>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column prop="contract_type" :label="$t('master.contracts.fields.contract_type')" sortable
                                width="120">
                                <template #default="scope">
                                    <el-tag :type="getContractTypeTagType(scope.row.contract_type)" size="small">
                                        {{ scope.row.contract_type }}
                                    </el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column prop="operator_name" :label="$t('master.contracts.fields.operator_name')" 
                                min-width="150" show-overflow-tooltip class-name="hidden-sm">
                                <template #default="scope">
                                    <div class="flex items-center">
                                        <Icon icon="mingcute:building-line" class="text-gray-400 mr-2" />
                                        <span>{{ scope.row.operator_name || '-' }}</span>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column :label="$t('master.contracts.fields.field_name') + ' - ' + $t('master.contracts.fields.block_name')" 
                                min-width="150" show-overflow-tooltip class-name="hidden-sm">
                                <template #default="scope">
                                    <div class="text-sm">
                                        <div class="flex items-center">
                                            <Icon icon="mingcute:location-line" class="text-gray-400 mr-2" />
                                            <span v-if="scope.row.field_name && scope.row.block_name">
                                                {{ scope.row.field_name }} - {{ scope.row.block_name }}
                                            </span>
                                            <span v-else-if="scope.row.field_name">
                                                {{ scope.row.field_name }}
                                            </span>
                                            <span v-else class="text-gray-400">-</span>
                                        </div>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column :label="$t('master.contracts.fields.effective_date') + ' - ' + $t('master.contracts.fields.expiry_date')" 
                                width="160" class-name="hidden-sm">
                                <template #default="scope">
                                    <div class="text-sm space-y-1">
                                        <div class="flex items-center text-green-600">
                                            <Icon icon="mingcute:calendar-line" class="mr-1 text-xs" />
                                            {{ formatDate(scope.row.effective_date) }}
                                        </div>
                                        <div class="flex items-center text-red-600">
                                            <Icon icon="mingcute:calendar-line" class="mr-1 text-xs" />
                                            {{ formatDate(scope.row.expiry_date) }}
                                        </div>
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column :label="$t('master.contracts.days_to_expiry')" sortable
                                width="120">
                                <template #default="scope">
                                    <div v-if="scope.row.days_to_expiry !== null">
                                        <el-tag :type="getDaysToExpiryTagType(scope.row.days_to_expiry)" size="small">
                                            {{ scope.row.days_to_expiry }} days
                                        </el-tag>
                                    </div>
                                    <span v-else class="text-gray-400">-</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="contract_status" :label="$t('master.contracts.fields.contract_status')" sortable
                                width="100">
                                <template #default="scope">
                                    <el-tag :type="getStatusType(scope.row.contract_status)" size="small">
                                        {{ scope.row.contract_status }}
                                    </el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column :label="$t('common.actions.action', 2)" align="center" width="140"
                                fixed="right">
                                <template #default="scope">
                                    <div class="flex items-center justify-center space-x-1">
                                        <!-- Quick Actions (Desktop) -->
                                        <div class="hidden md:flex space-x-1">
                                            <el-tooltip :content="$t('common.actions.view')" placement="top">
                                                <el-button @click="onView(scope.row)" size="small" circle class="action-button">
                                                    <Icon icon="mingcute:eye-line" />
                                                </el-button>
                                            </el-tooltip>
                                            <el-tooltip :content="$t('common.actions.edit')" placement="top">
                                                <el-button @click="onEdit(scope.row)" size="small" circle type="primary" class="action-button">
                                                    <Icon icon="mingcute:edit-line" />
                                                </el-button>
                                            </el-tooltip>
                                            <el-tooltip v-if="scope.row.id != 1" :content="$t('common.actions.delete')" placement="top">
                                                <el-button @click="onDelete(scope.row)" size="small" circle type="danger" class="action-button">
                                                    <Icon icon="mingcute:delete-2-line" />
                                                </el-button>
                                            </el-tooltip>
                                        </div>
                                        
                                        <!-- Dropdown Menu (Mobile) -->
                                        <el-dropdown popper-class="dropdown-action" placement="bottom-end" trigger="click" class="md:hidden">
                                            <el-button circle size="small" class="action-button">
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
                                    </div>
                                </template>
                            </el-table-column>
                        </el-table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="data?.data && data.data.length > 0" class="border-t border-gray-200 bg-white px-4 py-3">
                        <Pagination :current-page="data?.meta.current_page || 1" :per-page="data?.meta.per_page || 25"
                            :total="data?.meta.total || 0" :last-page="data?.meta.last_page || 1"
                            @page-change="handlePageChange" @per-page-change="handlePerPageChange" />
                    </div>
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
const { formatCurrency } = useFormatter();
const { t } = useI18n();
const router = useRouter();

// Page stats
const viewMode = ref('table');
const selectedRows = ref([]);
const isLoadingStats = ref(false);
const stats = ref({
    total: 0,
    active: 0,
    expiring_soon: 0,
    expired: 0
});

// Filter state
const params = ref({
  limit: 25,
  page: 1,
  q: "",
  sort: 'contract_number',
  sortDir: 'asc',
});

const advancedFilters = ref({
  contract_type: null,
  status: null,
});

// Computed properties
const activeFilters = computed(() => {
  const filters = [];
  if (advancedFilters.value.contract_type) {
      filters.push({
          key: 'contract_type',
          label: t('common.fields.type'),
          value: advancedFilters.value.contract_type
      });
  }
  if (advancedFilters.value.status) {
      filters.push({
          key: 'status',
          label: t('common.fields.status'),
          value: advancedFilters.value.status
      });
  }
  return filters;
});

// Helper methods
const getStatusType = (status) => {
  switch (status) {
      case 'Active': return 'success';
      case 'Suspended': return 'warning';
      case 'Force Majeure': return 'warning';
      case 'Expired': return 'danger';
      case 'Terminated': return 'danger';
      default: return 'info';
  }
};

const getContractTypeTagType = (type) => {
    const typeMap = {
        'PSC': 'primary',
        'TAC': 'success',
        'Service Contract': 'warning',
        'Joint Venture': 'info'
    }
    return typeMap[type] || 'info'
}

const getDaysToExpiryTagType = (days) => {
    if (days <= 30) return 'danger'
    if (days <= 90) return 'warning'
    if (days <= 180) return 'info'
    return 'success'
}

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
  });
};

const fetchData = async ({ queryKey }) => {
  const [_key, queryParams] = queryKey;
  const response = await axios.get("/master/contracts", {
      params: queryParams,
  });
  return response.data;
};

const { data, isLoading, isError, error, refetch } = useQuery({
  queryKey: ['contractList', params.value],
  queryFn: fetchData,
  keepPreviousData: true,
});

// Fungsi untuk menghitung statistik dari data yang ada
const calculateStats = () => {
  if (!data.value?.data) {
    stats.value = {
      total: 0,
      active: 0,
      expiring_soon: 0,
      expired: 0
    };
    return;
  }
  
  const contracts = data.value.data;
  const now = new Date();
  const thirtyDaysFromNow = new Date(now.getTime() + (30 * 24 * 60 * 60 * 1000));

  const activeContracts = contracts.filter(contract => {
    const expiryDate = new Date(contract.expiry_date);
    return contract.contract_status === 'Active' && expiryDate > now;
  });

  const expiringSoonContracts = contracts.filter(contract => {
    const expiryDate = new Date(contract.expiry_date);
    return contract.contract_status === 'Active' && expiryDate > now && expiryDate <= thirtyDaysFromNow;
  });

  const expiredContracts = contracts.filter(contract => {
    const expiryDate = new Date(contract.expiry_date);
    return expiryDate <= now || contract.contract_status === 'Expired';
  });

  stats.value = {
    total: contracts.length,
    active: activeContracts.length,
    expiring_soon: expiringSoonContracts.length,
    expired: expiredContracts.length
  };
};

// Event handlers
const onCreate = () => {
  router.push({ name: 'master.contracts.create' });
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
      contract_type: advancedFilters.value.contract_type,
      status: advancedFilters.value.status,
      page: 1
  });
  refetch();
};

const clearAllFilters = () => {
  advancedFilters.value = {
      contract_type: null,
      status: null,
  };
  params.value = {
      ...params.value,
      contract_type: null,
      status: null,
      page: 1
  };
  refetch();
};

const removeFilter = (filterKey) => {
  if (filterKey === 'contract_type') {
      advancedFilters.value.contract_type = null;
  } else {
      advancedFilters.value[filterKey] = null;
  }
  applyAdvancedFilters();
};

const onView = (row) => {
  router.push({ name: 'master.contracts.show', params: { id: row.id } });
};

const onEdit = (row) => {
  router.push({ name: 'master.contracts.edit', params: { id: row.id } });
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

      await axios.delete(`/master/contract/${row.id}/delete`);
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
      await axios.delete('/master/contract/bulk', { data: { ids } });
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

watch(() => advancedFilters.value, () => {
  applyAdvancedFilters();
}, { deep: true });

watch(() => data.value, () => {
  calculateStats();
}, { immediate: true });

// Lifecycle
onMounted(() => {
  refetch();
});
</script>

<style scoped>
/* Custom styles untuk halaman Master Contracts */
.content {
  @apply space-y-6;
}

/* Responsive table improvements */
.base-table {
  @apply w-full;
}

.base-table :deep(.el-table__header-wrapper) {
  @apply bg-gray-50;
}

.base-table :deep(.el-table__header th) {
  @apply bg-gray-50 text-gray-700 font-semibold text-sm;
  border-bottom: 2px solid #e5e7eb;
}

.base-table :deep(.el-table__row) {
  @apply hover:bg-gray-50 transition-colors;
}

.base-table :deep(.el-table__row td) {
  @apply py-3 px-4 border-b border-gray-100;
}

/* Card hover effects */
.contract-card {
  @apply transition-all duration-200 hover:shadow-lg hover:border-blue-200;
}

/* Mobile optimizations */
@media (max-width: 768px) {
  .content {
    @apply space-y-4;
  }
  
  .base-table :deep(.el-table__header th),
  .base-table :deep(.el-table__row td) {
    @apply px-2 py-2 text-xs;
  }
  
  /* Hide less important columns on mobile */
  .base-table :deep(.el-table__column--hidden-sm) {
    @apply hidden;
  }
}

/* Filter section styling */
.filter-section {
  @apply bg-gradient-to-r from-gray-50 to-white;
}

/* Statistics cards responsive grid */
.stats-grid {
  @apply grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4;
}

/* Action buttons styling */
.action-button {
  @apply transition-all duration-200 hover:scale-105 hover:shadow-md;
  min-width: 32px;
  height: 32px;
}

.action-button:hover {
  @apply transform scale-105;
}

/* Desktop Quick Actions */
@media (min-width: 768px) {
  .action-button {
    @apply opacity-80 hover:opacity-100;
  }
}

/* Empty state styling */
.empty-state {
  @apply text-center py-16 px-4;
}

.empty-state-icon {
  @apply mx-auto w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6;
}

/* Loading skeleton improvements */
:deep(.el-skeleton) {
  @apply animate-pulse;
}

/* Dropdown menu styling */
:deep(.dropdown-action) {
  @apply shadow-lg border border-gray-200 rounded-lg;
}

:deep(.dropdown-action .el-dropdown-menu__item) {
  @apply px-4 py-2 text-sm transition-colors;
}

:deep(.dropdown-action .el-dropdown-menu__item:hover) {
  @apply bg-gray-50;
}

/* Tag styling improvements */
:deep(.el-tag) {
  @apply font-medium;
}

/* Button group styling */
:deep(.el-button-group) {
  @apply shadow-sm border border-gray-200 rounded-lg overflow-hidden;
}

:deep(.el-button-group .el-button) {
  @apply border-0;
}
</style>