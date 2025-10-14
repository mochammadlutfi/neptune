<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader :title="$t('master.gas_buyer.title')" :primary-action="{
              label: $t('master.gas_buyer.create'),
              icon: 'mingcute:add-line',
              click: onCreate
          }" />

        <el-card body-class="!p-0" class="!rounded-lg !shadow-md">
            <!-- Advanced Filter Section -->
            <TableControls :search="params.q" 
            :search-debounce="1500"
            :search-placeholder="$t('common.actions.search')"
            :loading="isLoading" 
            :selected-rows="selectedRows" 
            @refresh="refetch" 
            @bulk-export="bulkExport"
            @bulk-delete="bulkDelete"
            @search="onSearch"/>

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
                            <el-table-column prop="vessel.name" :label="$t('master.gas_buyer.fields.vessel_id')" sortable>
                                <template #default="scope">
                                    <div class="flex items-center space-x-2">
                                        <el-icon class="text-blue-600">
                                            <Icon icon="fluent:vehicle-ship-24-filled"/>
                                        </el-icon>
                                        <router-link :to="`/master/vessels/${scope.row.vessel.id}`"
                                            class="font-medium text-blue-600 hover:underline">
                                            {{ scope.row.vessel.name }}
                                        </router-link>
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column prop="code" label="Code" width="160" sortable
                                fixed="left">
                                <template #default="scope">
                                    <el-tag type="info" size="small">{{ scope.row.code }}</el-tag>
                                </template>
                            </el-table-column>

                            <el-table-column prop="name" label="Name" sortable>
                                <template #default="scope">
                                    <div class="flex items-center space-x-2">
                                        <el-icon class="text-blue-600">
                                            <Icon icon="mingcute:user"/>
                                        </el-icon>
                                        {{ scope.row.name }}
                                    </div>
                                </template>
                            </el-table-column>

                            <el-table-column prop="is_active" label="Status" width="120">
                                <template #default="scope">
                                    <span>{{ scope.row.is_active ? 'Active' : 'Inactive' }}</span>
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
                                                <el-dropdown-item @click="onEdit(scope.row)"
                                                    v-if="can('update', 'master-gas_buyer')">
                                                    <Icon icon="mingcute:edit-line" class="me-2" />
                                                    {{ $t('common.actions.edit') }}
                                                </el-dropdown-item>
                                                <el-dropdown-item divided class="text-red-600"
                                                    @click="onDelete(scope.row)" v-if="can('delete', 'master-gas_buyer')">
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
            class="!sm:w-full !w-1/3 !rounded-xl p-0"
            header-class="border-b-2 el-dialog__header p-4"
            body-class="p-4"
        >
            <template #header>
                <div class="flex items-center gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ formTitle }}</h3>
                    </div>
                </div>
            </template>
            
            <el-form v-loading="formLoading" :model="form" ref="formRef" :rules="formRules" label-position="top" @submit.prevent="onSubmit" class="space-y-4">
                <div class="grid grid-cols-1 gap-4">
                    <el-form-item :label="$t('master.gas_buyer.fields.vessel_id')" prop="vessel_id" :error="serverErrors.vessel_id">
                        <SelectVessel v-model="form.vessel_id" :placeholder="$t('master.gas_buyer.fields.vessel_id')" />
                    </el-form-item>

                    <el-form-item :label="$t('master.gas_buyer.fields.code')" prop="code" :error="serverErrors.code">
                        <el-input v-model="form.code" :placeholder="$t('master.gas_buyer.fields.code')" />
                    </el-form-item>
                    
                    <el-form-item :label="$t('master.gas_buyer.fields.name')" prop="name" :error="serverErrors.name">
                        <el-input v-model="form.name" :placeholder="$t('master.gas_buyer.fields.name')" />
                    </el-form-item>
                    
                    <el-form-item :label="$t('master.gas_buyer.fields.is_active')" prop="is_active" :error="serverErrors.is_active">
                        <el-switch
                            v-model="form.is_active"
                            :active-text="$t('master.gas_buyer.status.active')"
                            :inactive-text="$t('master.gas_buyer.status.inactive')"
                        />
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
        </el-dialog>
    </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted, computed } from 'vue';
import axios from 'axios';
import { ElMessageBox, ElMessage } from 'element-plus';
import { Icon } from '@iconify/vue';
import _ from 'lodash';
import { useQuery } from '@tanstack/vue-query';
import { useI18n } from 'vue-i18n';
import { useFormatter } from '@/composables/common/useFormatter';
import { useRouter } from 'vue-router';
import { useAbility } from '@casl/vue';
import { useUser } from '@/composables/auth';

// Import our reusable components
import PageHeader from '@/components/PageHeader.vue';
import TableControls from '@/components/TableControls.vue';
import SelectVessel from '@/components/select/SelectVessel.vue';
import Pagination from '@/components/Pagination.vue';
import SkeletonTable from '@/components/SkeletonTable.vue';

const { formatDate, formatNumber } = useFormatter();
const { user } = useUser();
const { t } = useI18n();
const { can } = useAbility();
const router = useRouter();

// Page stats
const viewMode = ref('table');
const selectedRows = ref([]);
const isLoadingStats = ref(false);
const stats = ref({});
const { userVesselId } = useUser();


// Form
const formShow = ref(false);
const formTitle = ref('');
const formLoading = ref(false);
const formRef = ref(null);
const form = reactive({
    id : null,
    vessel_id : userVesselId,
    name: '',
    code : '',
    is_active : true,
});
// Server-side validation errors holder
const serverErrors = reactive({});
const clearServerErrors = () => {
  Object.keys(serverErrors).forEach(key => {
    delete serverErrors[key];
  });
};

// Form validation rules
const formRules = {
  name: [
    { required: true, message: t('common.validation.required', { attribute: t('master.gas_buyer.fields.name') }), trigger: 'blur' }
  ],
  code: [
    { required: true, message: t('common.validation.required', { attribute: t('master.gas_buyer.fields.code') }), trigger: 'blur' }
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
  vessel_id: userVesselId,
});
const fetchData = async ({ queryKey }) => {
  const [_key, queryParams] = queryKey;
  const response = await axios.get("/master/gas-buyer", {
    params: queryParams,
  });
  return response.data;
};

const { data, isLoading, isError, error, refetch } = useQuery({
  queryKey: computed(() => ['MasterIndexGasBuyer', params.value]),
  queryFn: fetchData,
  keepPreviousData: true,
});

// Event handlers
const onCreate = () => {
//   router.push({ name: 'master.gas_buyer.create' });
    formShow.value = true;
    formTitle.value = t('master.gas_buyer.create');
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
    
    const url = form.id ? `/master/gas-buyer/${form.id}/update` : '/master/gas-buyer/store';
    const method = form.id ? 'put' : 'post';
    const response =  await axios({ method, url, data: form });


    if (response.status === 200 || response.status === 201) {
        ElMessage({
            message: t('common.messages.saved'),
            type: 'success',
        })
        clearServerErrors();
        formRef.value.clearValidate();
        formShow.value = false;
        refetch();
    }
  } catch (error) {
    // console.log('ini error',error.validation)
    const status = error?.status || error?.response?.status;
    const validation = error?.validation || error?.response?.data?.errors;
    if (status === 422) {
        const errors = validation
        
        if (errors && typeof errors === 'object') {
          Object.keys(errors).forEach(key => {
            const msg = Array.isArray(errors[key]) ? errors[key][0] : errors[key];
            serverErrors[key] = msg;
            if (formRef.value?.scrollToField) {
              formRef.value.scrollToField(key);
            }
          });
        }
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
    clearServerErrors();
    formRef.value.clearValidate();
  }
};

const onSearch = (val) => {
  params.value.q = val;
  params.value.page = 1;
  refetch();
};
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
  router.push({ name: 'master.gas_buyer.show', params: { id: row.id } });
};

const onEdit = (row) => {
    formShow.value = true;
    formTitle.value = t('master.gas_buyer.edit');
    form.id = row.id;
    form.vessel_id = row.vessel_id;
    form.name = row.name;
    form.code = row.code;
    form.is_active = row.is_active;
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

      await axios.delete(`/master/gas-buyer/${row.id}/delete`);
      ElMessage.success(t('common.success.item_deleted'));
      refetch();
  } catch (error) {
      if (error !== 'cancel') {
          ElMessage.error(t('common.errors.delete_failed'));
      }
  }
};

// Lifecycle
onMounted(() => {
  // console.log(user.value)
  // if(user.value.vessel_id) {
  //   form.vessel_id = user.value.vessel_id;
  // }
  refetch();
});
</script>

<style scoped>
/* Add any specific styles for this page here */
</style>