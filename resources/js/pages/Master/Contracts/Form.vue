<template>
    <div class="content">
        <!-- Page Header -->
        <PageHeader :title="$t(title)">
            <template #actions>
                <el-tooltip :content="$t('common.form.form_help')" placement="bottom">
                    <el-button circle>
                        <Icon icon="mingcute:question-line" />
                    </el-button>
                </el-tooltip>
            </template>
        </PageHeader>

        <el-card body-class="!p-0" class="!rounded-lg !shadow-lg" v-loading="formLoading">
            <el-form :model="form" :rules="formRules" ref="formRef" @submit.prevent="onSubmit" label-position="top">
                
                <!-- Section 1: Basic Contract Information -->
                <div class="p-6">
                    <el-row :gutter="24">
                        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('master.contracts.fields.contract_number')" prop="contract_number">
                                <el-input v-model="form.contract_number" :placeholder="$t('master.contracts.fields.contract_number')" />
                            </el-form-item>
                        </el-col>
                        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('master.contracts.fields.contract_name')" prop="contract_name">
                                <el-input v-model="form.contract_name" :placeholder="$t('master.contracts.fields.contract_name')" />
                            </el-form-item>
                        </el-col>
                        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('master.contracts.fields.contract_type')" prop="contract_type">
                                <el-select v-model="form.contract_type" :placeholder="$t('common.form.select')" class="w-full">
                                    <el-option label="PSC" value="PSC" />
                                    <el-option label="Joint Venture" value="Joint Venture" />
                                    <el-option label="Service Contract" value="Service Contract" />
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('master.contracts.fields.contract_status')" prop="contract_status">
                                <el-select v-model="form.contract_status" :placeholder="$t('common.form.select')" class="w-full">
                                    <el-option label="Active" value="Active" />
                                    <el-option label="Expired" value="Expired" />
                                    <el-option label="Terminated" value="Terminated" />
                                    <el-option label="Under Negotiation" value="Under Negotiation" />
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </div>

                <!-- Form Actions -->
                <div class="p-6 border-t">
                    <div class="flex justify-end space-x-3">
                        <el-button @click="$router.go(-1)">
                            <Icon icon="mingcute:arrow-left-line" class="mr-2" />
                            {{ $t('common.actions.cancel') }}
                        </el-button>
                        <el-button native-type="submit" type="primary" :loading="formLoading">
                            <Icon icon="mingcute:check-fill" class="mr-2" />
                            {{ $t('common.actions.save') }}
                        </el-button>
                    </div>
                </div>
            </el-form>
        </el-card>
    </div>
</template>

<script setup lang="js">
import { Icon } from '@iconify/vue';
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import PageHeader from '@/components/PageHeader.vue';

const route = useRoute();
const router = useRouter();
const { t } = useI18n();
const title = ref(route.meta.title ?? 'master.contracts.create');
const formRef = ref(null);

const form = ref({
    contract_number: null,
    contract_name: null,
    contract_type: null,
    operator_name: null,
    kkks_representative: null,
    partner_companies_text: null,
    effective_date: null,
    expiry_date: null,
    extension_options: null,
    field_name: null,
    block_name: null,
    working_area_km2: null,
    cost_recovery_limit_pct: null,
    ftp_share_pct: null,
    contractor_share_oil_pct: null,
    contractor_share_gas_pct: null,
    government_share_oil_pct: null,
    government_share_gas_pct: null,
    minimum_work_commitment: null,
    minimum_expenditure_usd: null,
    local_content_requirement_pct: null,
    performance_bond_amount_usd: null,
    bond_expiry_date: null,
    contract_status: 'Active'
});

const formRules = ref({
    contract_number: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.contracts.fields.contract_number') }),
        trigger: 'blur'
    }],
    
    contract_name: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.contracts.fields.contract_name') }),
        trigger: 'blur'
    }, {
        min: 3,
        max: 255,
        message: t('common.validation.between', { attribute: t('master.contracts.fields.contract_name'), min: 3, max: 255 }),
        trigger: 'blur'
    }],
    
    contract_type: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.contracts.fields.contract_type') }),
        trigger: 'change'
    }],
    
    operator_name: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.contracts.fields.operator_name') }),
        trigger: 'blur'
    }],
    
    effective_date: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.contracts.fields.effective_date') }),
        trigger: 'change'
    }],
    
    expiry_date: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.contracts.fields.expiry_date') }),
        trigger: 'change'
    }],
    
    field_name: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.contracts.fields.field_name') }),
        trigger: 'blur'
    }],
    
    cost_recovery_limit_pct: [{
        validator: (_, value, callback) => {
            if (value && (isNaN(value) || parseFloat(value) < 0 || parseFloat(value) > 100)) {
                callback(new Error(t('common.validation.between_values', { 
                    attribute: t('master.contracts.fields.cost_recovery_limit_pct'),
                    min: 0, 
                    max: 100 
                })));
            } else {
                callback();
            }
        },
        trigger: 'blur'
    }],
    
    contractor_share_oil_pct: [{
        validator: (_, value, callback) => {
            if (value && (isNaN(value) || parseFloat(value) < 0 || parseFloat(value) > 100)) {
                callback(new Error(t('common.validation.between_values', { 
                    attribute: t('master.contracts.fields.contractor_share_oil_pct'),
                    min: 0, 
                    max: 100 
                })));
            } else {
                callback();
            }
        },
        trigger: 'blur'
    }],
    
    government_share_oil_pct: [{
        validator: (_, value, callback) => {
            if (value && (isNaN(value) || parseFloat(value) < 0 || parseFloat(value) > 100)) {
                callback(new Error(t('common.validation.between_values', { 
                    attribute: t('master.contracts.fields.government_share_oil_pct'),
                    min: 0, 
                    max: 100 
                })));
            } else {
                callback();
            }
        },
        trigger: 'blur'
    }],
    
    contractor_share_gas_pct: [{
        validator: (_, value, callback) => {
            if (value && (isNaN(value) || parseFloat(value) < 0 || parseFloat(value) > 100)) {
                callback(new Error(t('common.validation.between_values', { 
                    attribute: t('master.contracts.fields.contractor_share_gas_pct'),
                    min: 0, 
                    max: 100 
                })));
            } else {
                callback();
            }
        },
        trigger: 'blur'
    }],
    
    government_share_gas_pct: [{
        validator: (_, value, callback) => {
            if (value && (isNaN(value) || parseFloat(value) < 0 || parseFloat(value) > 100)) {
                callback(new Error(t('common.validation.between_values', { 
                    attribute: t('master.contracts.fields.government_share_gas_pct'),
                    min: 0, 
                    max: 100 
                })));
            } else {
                callback();
            }
        },
        trigger: 'blur'
    }],
    
    minimum_expenditure_usd: [{
        validator: (_, value, callback) => {
            if (value && (isNaN(value) || parseFloat(value) < 0)) {
                callback(new Error(t('common.validation.positive_number', { 
                    attribute: t('master.contracts.fields.minimum_expenditure_usd')
                })));
            } else {
                callback();
            }
        },
        trigger: 'blur'
    }],
    
    performance_bond_amount_usd: [{
        validator: (_, value, callback) => {
            if (value && (isNaN(value) || parseFloat(value) < 0)) {
                callback(new Error(t('common.validation.positive_number', { 
                    attribute: t('master.contracts.fields.performance_bond_amount_usd')
                })));
            } else {
                callback();
            }
        },
        trigger: 'blur'
    }]
});

const formLoading = ref(false);

const onSubmit = async () => {
    if (!formRef.value) return;
    
    formRef.value.validate(async (valid) => {
        if (valid) {
            try {
                formLoading.value = true;
                
                // Prepare form data
                const formData = { ...form.value };

                const url = route.params.id ? `/master/contracts/${route.params.id}/update` : '/master/contracts/store';
                const method = route.params.id ? 'put' : 'post';

                const response = await axios({
                    method,
                    url,
                    data: formData,
                });
                
                if (response.status === 200 || response.status === 201) {
                    ElMessage({
                        message: t('common.messages.saved'),
                        type: 'success',
                    });
                    router.replace({ path: `/master/contracts/${response.data.id || response.data.data?.id}` });
                }
            } catch (error) {
                console.log(error);
                formLoading.value = false;
                
                // Handle validation errors from server
                if (error.response && error.response.status === 422) {
                    const errors = error.response.data.errors;
                    let errorMessage = t('common.errors.validation_failed');
                    
                    if (errors) {
                        const firstError = Object.values(errors)[0];
                        if (firstError && firstError[0]) {
                            errorMessage = firstError[0];
                        }
                    }
                    
                    ElMessage({
                        message: errorMessage,
                        type: 'error',
                    });
                } else {
                    ElMessage({
                        message: t('common.errors.server_error'),
                        type: 'error',
                    });
                }
            }
        } else {
            ElMessage({
                message: t('common.errors.validation_failed'),
                type: 'error',
            });
        }
    });
};

const fetchData = async () => {
    formLoading.value = true;
    await axios.get(`/master/contracts/${route.params.id}`)
        .then(response => {
            const data = response.data.data || response.data;

            // Map response data to form
            Object.keys(form.value).forEach(key => {
                if (data.hasOwnProperty(key)) {
                    form.value[key] = data[key];
                }
            });
            
            // Handle partner companies JSON to text
            if (data.partner_companies && Array.isArray(data.partner_companies)) {
                form.value.partner_companies_text = data.partner_companies.join('\n');
            }
        })
        .catch(error => {
            console.error('Error fetching contract data:', error);
            ElMessage({
                message: t('common.errors.server_error'),
                type: 'error',
            });
        })
        .finally(() => {
            formLoading.value = false;
        });
};

onMounted(() => {
    if (route.params.id) {
        fetchData();
    }
});
</script>

<style scoped>
/* Section styling untuk visual hierarchy yang lebih baik */
.section-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-bottom: 1px solid #e2e8f0;
}

/* Form spacing dan layout improvements */
.el-form-item {
    margin-bottom: 24px;
}

.el-form-item:last-child {
    margin-bottom: 0;
}

/* Input styling enhancements */
.el-input :deep(.el-input__wrapper) {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.el-input :deep(.el-input__wrapper:hover) {
    box-shadow: 0 0 0 1px #3b82f6;
}

.el-input :deep(.el-input__wrapper.is-focus) {
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

/* Select styling */
.el-select :deep(.el-input__wrapper) {
    border-radius: 8px;
}

/* Date picker styling */
.el-date-editor :deep(.el-input__wrapper) {
    border-radius: 8px;
}

/* Card styling improvements */
.el-card {
    border-radius: 12px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

/* Button styling */
.el-button {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.el-button--primary {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border: none;
}

.el-button--primary:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

/* Section divider styling */
.border-b {
    border-color: #e5e7eb;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .p-6 {
        padding: 1rem;
    }
    
    .el-col {
        margin-bottom: 1rem;
    }
}

/* Icon styling dalam form */
.text-gray-400 {
    color: #9ca3af;
}

/* Share section styling */
.bg-blue-50 {
    background-color: #eff6ff;
}

.bg-green-50 {
    background-color: #f0fdf4;
}
</style>