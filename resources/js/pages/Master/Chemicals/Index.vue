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

                            <el-table-column prop="type" :label="$t('master.chemicals.fields.type')" width="140" sortable>
                                <template #default="{ row }">
                                    <el-tag :type="getChemicalTypeTagType(row.type)" size="small">
                                        {{ row.type }}
                                    </el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column prop="unit" :label="$t('master.chemicals.fields.unit')" width="120"
                                sortable>
                                <template #default="scope">
                                    <span class="text-sm">{{ scope.row.unit || '-' }}</span>
                                </template>
                            </el-table-column>

                            <el-table-column prop="spesific_gravity" :label="$t('master.chemicals.fields.specific_gravity')"
                                min-width="160" show-overflow-tooltip>
                                <template #default="scope">
                                    <span>{{ scope.row.specific_gravity || '-' }}</span>
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

        <!-- Form Dialog -->
        <el-dialog 
            v-model="formShow" 
            :title="formTitle" 
            :close-on-click-modal="false" 
            :close-on-press-escape="false"
            class="!sm:w-full !w-1/3 !rounded-xl" 
            v-loading="formLoading"
        >
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-50 to-purple-100 flex items-center justify-center">
                        <Icon icon="mingcute:tag-line" class="text-purple-600" />
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ formTitle }}</h3>
                    </div>
                </div>
            </template>
            
            <div class="p-2">
                <el-form :model="form" ref="formRef" :rules="formRules" label-position="top" @submit.prevent="onSubmit" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <el-form-item :label="$t('master.chemicals.fields.code')" prop="code">
                            <el-input v-model="form.code" :placeholder="$t('master.chemicals.fields.code')" />
                        </el-form-item>
                        
                        <el-form-item :label="$t('master.chemicals.fields.name')" prop="name">
                            <el-input v-model="form.name" :placeholder="$t('master.chemicals.fields.name')" />
                        </el-form-item>
                        
                        <el-form-item :label="$t('master.chemicals.fields.type')" prop="type">
                            <el-select v-model="form.type" :placeholder="$t('master.chemicals.fields.type')" class="w-full">
                                <el-option label="Drilling Fluid" value="Drilling Fluid" />
                                <el-option label="Production Chemical" value="Production Chemical" />
                                <el-option label="Completion Fluid" value="Completion Fluid" />
                                <el-option label="Stimulation Chemical" value="Stimulation Chemical" />
                                <el-option label="Corrosion Inhibitor" value="Corrosion Inhibitor" />
                                <el-option label="Scale Inhibitor" value="Scale Inhibitor" />
                                <el-option label="Biocide" value="Biocide" />
                                <el-option label="Demulsifier" value="Demulsifier" />
                            </el-select>
                        </el-form-item>
                        
                        <el-form-item :label="$t('master.chemicals.fields.unit')" prop="unit">
                            <el-select v-model="form.unit" :placeholder="$t('master.chemicals.fields.unit')" class="w-full">
                                <el-option label="Liters" value="Liters" />
                                <el-option label="Gallons" value="Gallons" />
                                <el-option label="Barrels" value="Barrels" />
                                <el-option label="Kilograms" value="Kilograms" />
                                <el-option label="Pounds" value="Pounds" />
                                <el-option label="Tons" value="Tons" />
                            </el-select>
                        </el-form-item>
                        
                        <el-form-item :label="$t('master.chemicals.fields.specific_gravity')" prop="specific_gravity" class="md:col-span-2">
                            <el-input v-model="form.specific_gravity" type="number" step="0.001" :placeholder="$t('master.chemicals.fields.specific_gravity')" />
                        </el-form-item>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <el-button @click.prevent="onCancel" >
                            <Icon icon="mingcute:close-line" class="mr-2" />
                            {{ $t('common.actions.cancel') }}
                        </el-button>
                        <el-button type="primary" native-type="submit"  class="px-8">
                            <Icon icon="mingcute:check-line" class="mr-2" />
                            {{ $t('common.actions.save') }}
                        </el-button>
                    </div>
                </el-form>
            </div>
        </el-dialog>
    </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue';
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


// Form
const formShow = ref(false);
const formTitle = ref('');
const formLoading = ref(false);
const formRef = ref(null);
const form = reactive({
    id : null,
    name: '',
    type: null,
    code : '',
    unit : 'Liters',
    specific_gravity : null,
});

// Form validation rules
const formRules = {
  name: [
    { required: true, message: t('common.validation.required', { attribute: t('master.chemicals.fields.name') }), trigger: 'blur' }
  ],
  code: [
    { required: true, message: t('common.validation.required', { attribute: t('master.chemicals.fields.code') }), trigger: 'blur' }
  ],
  type: [
    { required: true, message: t('common.validation.required', { attribute: t('master.chemicals.fields.type') }), trigger: 'change' }
  ],
  unit: [
    { required: true, message: t('common.validation.required', { attribute: t('master.chemicals.fields.unit') }), trigger: 'blur' }
  ]
};

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
//   router.push({ name: 'master.chemicals.create' });
    formShow.value = true;
    formTitle.value = t('master.chemicals.create');
    form.name = '';
    form.type = null;
    form.code = '';
    form.unit = 'Liters';
    form.specific_gravity = null;
};

// Form handlers
const onSubmit = async () => {
  if (!formRef.value) return;
  
  try {
    await formRef.value.validate();
    formLoading.value = true;
    
    const url = form.id ? `/master/chemicals/${form.id}/update` : '/master/chemicals/store';
    const method = form.id ? 'put' : 'post';
    const response =  await axios({ method, url, data: form });


    if (response.status === 200 || response.status === 201) {
        ElMessage({
            message: t('common.messages.saved'),
            type: 'success',
        })
        formShow.value = false;
        refetch();
    }
  } catch (error) {
    console.log(error.value)
    if (error.status === 422) {
        const errors = error.response.data.errors
        let errorMessage = t('common.errors.validation_failed')
        
        if (errors) {
            const firstError = Object.values(errors)[0]
            if (firstError && firstError[0]) {
                errorMessage = firstError[0]
            }
        }
        
        ElMessage({
            message: errorMessage,
            type: 'error',
        })
    } else {
        ElMessage({
            message: t('common.errors.server_error'),
            type: 'error',
        })
    }
  } finally {
    formLoading.value = false;
  }
};

const onCancel = () => {
  formShow.value = false;
  if (formRef.value) {
    formRef.value.resetFields();
  }
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