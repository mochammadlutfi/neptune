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

        <el-card body-class="!p-0" class="!rounded-lg !shadow-lg !border-0" v-loading="formLoading">
            <el-form :model="form" :rules="formRules" ref="formRef" @submit.prevent="onSubmit" label-position="top">
                
                <!-- Section 1: Basic Information -->
                <div class="border-b border-gray-100">
                    <div class="p-6 bg-muted">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1 flex items-center">
                            <Icon icon="mingcute:file-line" class="mr-2 text-primary" />
                            {{ $t('master.fuel_types.basic_information') }}
                        </h3>
                        <p class="text-sm text-gray-600">{{ $t('master.fuel_types.basic_info_desc') }}</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <el-form-item :label="$t('master.fuel_types.fields.code')" prop="code">
                                <el-input 
                                    v-model="form.code" 
                                    :placeholder="$t('master.fuel_types.fields.code')"
                                    maxlength="20"
                                    show-word-limit
                                />
                            </el-form-item>
                            
                            <el-form-item :label="$t('master.fuel_types.fields.name')" prop="name">
                                <el-input 
                                    v-model="form.name" 
                                    :placeholder="$t('master.fuel_types.fields.name')"
                                    maxlength="100"
                                    show-word-limit
                                />
                            </el-form-item>
                            
                            <el-form-item :label="$t('master.fuel_types.fields.type')" prop="type">
                                <el-select v-model="form.type" :placeholder="$t('master.fuel_types.fields.type')" class="w-full">
                                    <el-option 
                                        v-for="(label, value) in fuelTypes" 
                                        :key="value" 
                                        :value="value" 
                                        :label="label" 
                                    />
                                </el-select>
                            </el-form-item>
                            
                            <el-form-item :label="$t('master.fuel_types.fields.grade')" prop="grade">
                                <el-input 
                                    v-model="form.grade" 
                                    :placeholder="$t('master.fuel_types.fields.grade')"
                                    maxlength="50"
                                />
                            </el-form-item>
                            
                            <el-form-item :label="$t('master.fuel_types.fields.unit')" prop="unit">
                                <el-select v-model="form.unit" :placeholder="$t('master.fuel_types.fields.unit')" class="w-full">
                                    <el-option 
                                        v-for="unit in units" 
                                        :key="unit" 
                                        :value="unit" 
                                        :label="unit" 
                                    />
                                </el-select>
                            </el-form-item>
                            
                            <el-form-item :label="$t('master.fuel_types.fields.is_active')" prop="is_active">
                                <el-switch 
                                    v-model="form.is_active"
                                    :active-text="$t('common.status.active')"
                                    :inactive-text="$t('common.status.inactive')"
                                />
                            </el-form-item>
                        </div>
                    </div>
                </div>
                
                <!-- Section 2: Technical Properties -->
                <div class="border-b border-gray-100">
                    <div class="p-6 bg-muted">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1 flex items-center">
                            <Icon icon="mingcute:settings-line" class="mr-2 text-primary" />
                            {{ $t('master.fuel_types.technical_properties') }}
                        </h3>
                        <p class="text-sm text-gray-600">{{ $t('master.fuel_types.technical_properties_desc') }}</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <el-form-item :label="$t('master.fuel_types.fields.density_kg_l')" prop="density_kg_l">
                                <el-input-number 
                                    v-model="form.density_kg_l" 
                                    :placeholder="$t('master.fuel_types.fields.density_kg_l')"
                                    :precision="4"
                                    :min="0"
                                    :max="99.9999"
                                    class="w-full"
                                />
                            </el-form-item>
                            
                            <el-form-item :label="$t('master.fuel_types.fields.viscosity_cst')" prop="viscosity_cst">
                                <el-input-number 
                                    v-model="form.viscosity_cst" 
                                    :placeholder="$t('master.fuel_types.fields.viscosity_cst')"
                                    :precision="4"
                                    :min="0"
                                    :max="999999.9999"
                                    class="w-full"
                                />
                            </el-form-item>
                            
                            <el-form-item :label="$t('master.fuel_types.fields.flash_point_c')" prop="flash_point_c">
                                <el-input-number 
                                    v-model="form.flash_point_c" 
                                    :placeholder="$t('master.fuel_types.fields.flash_point_c')"
                                    :precision="2"
                                    :min="-99.99"
                                    :max="999.99"
                                    class="w-full"
                                />
                            </el-form-item>
                            
                            <el-form-item :label="$t('master.fuel_types.fields.pour_point_c')" prop="pour_point_c">
                                <el-input-number 
                                    v-model="form.pour_point_c" 
                                    :placeholder="$t('master.fuel_types.fields.pour_point_c')"
                                    :precision="2"
                                    :min="-99.99"
                                    :max="999.99"
                                    class="w-full"
                                />
                            </el-form-item>
                            
                            <el-form-item :label="$t('master.fuel_types.fields.heating_value_mj_kg')" prop="heating_value_mj_kg">
                                <el-input-number 
                                    v-model="form.heating_value_mj_kg" 
                                    :placeholder="$t('master.fuel_types.fields.heating_value_mj_kg')"
                                    :precision="2"
                                    :min="0"
                                    :max="99999999.99"
                                    class="w-full"
                                />
                            </el-form-item>
                            
                            <el-form-item :label="$t('master.fuel_types.fields.sulfur_content_pct')" prop="sulfur_content_pct">
                                <el-input-number 
                                    v-model="form.sulfur_content_pct" 
                                    :placeholder="$t('master.fuel_types.fields.sulfur_content_pct')"
                                    :precision="3"
                                    :min="0"
                                    :max="99.999"
                                    class="w-full"
                                />
                            </el-form-item>
                        </div>
                    </div>
                </div>
                
                <!-- Section 3: Storage Information -->
                <div class="border-b border-gray-100">
                    <div class="p-6 bg-muted">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1 flex items-center">
                            <Icon icon="mingcute:storage-line" class="mr-2 text-primary" />
                            {{ $t('master.fuel_types.storage_information') }}
                        </h3>
                        <p class="text-sm text-gray-600">{{ $t('master.fuel_types.storage_info_desc') }}</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <el-form-item :label="$t('master.fuel_types.fields.storage_tank')" prop="storage_tank">
                                <el-input 
                                    v-model="form.storage_tank" 
                                    :placeholder="$t('master.fuel_types.fields.storage_tank')"
                                    maxlength="50"
                                />
                            </el-form-item>
                            
                            <el-form-item :label="$t('master.fuel_types.fields.tank_capacity')" prop="tank_capacity">
                                <el-input-number 
                                    v-model="form.tank_capacity" 
                                    :placeholder="$t('master.fuel_types.fields.tank_capacity')"
                                    :precision="2"
                                    :min="0"
                                    :max="9999999999.99"
                                    class="w-full"
                                />
                            </el-form-item>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="p-6 bg-gray-50 border-t border-gray-100">
                    <div class="flex justify-end space-x-3">
                        <el-button @click="onCancel">
                            {{ $t('common.actions.cancel') }}
                        </el-button>
                        <el-button type="primary" @click="onSubmit" :loading="formLoading">
                            <Icon icon="mingcute:save-line" class="mr-2" />
                            {{ $t('common.actions.save') }}
                        </el-button>
                    </div>
                </div>
            </el-form>
        </el-card>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ElMessage } from 'element-plus';
import { Icon } from '@iconify/vue';
import axios from 'axios';
import { useI18n } from 'vue-i18n';
import PageHeader from '@/components/PageHeader.vue';

const { t } = useI18n();
const route = useRoute();
const router = useRouter();
const formRef = ref();
const formLoading = ref(false);
const isEdit = computed(() => !!route.params.id);
const title = computed(() => isEdit.value ? 'master.fuel_types.edit' : 'master.fuel_types.create');

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

// Form data
const form = ref({
    code: '',
    name: '',
    type: '',
    grade: '',
    density_kg_l: null,
    viscosity_cst: null,
    flash_point_c: null,
    pour_point_c: null,
    heating_value_mj_kg: null,
    sulfur_content_pct: null,
    unit: 'Litres',
    storage_tank: '',
    tank_capacity: null,
    is_active: true,
});

// Form validation rules
const formRules = ref({
    code: [
        { required: true, message: t('common.validation.required_field', { field: t('master.fuel_types.fields.code') }), trigger: 'blur' },
        { max: 20, message: t('common.validation.max_length', { max: 20 }), trigger: 'blur' }
    ],
    name: [
        { required: true, message: t('common.validation.required_field', { field: t('master.fuel_types.fields.name') }), trigger: 'blur' },
        { max: 100, message: t('common.validation.max_length', { max: 100 }), trigger: 'blur' }
    ],
    type: [
        { required: true, message: t('common.validation.required_field', { field: t('master.fuel_types.fields.type') }), trigger: 'change' }
    ]
});

const fetchData = async () => {
    if (!isEdit.value) return;
    
    formLoading.value = true;
    try {
        const response = await axios.get(`/master/fuel-types/${route.params.id}`);
        Object.assign(form.value, response.data.data);
    } catch (error) {
        ElMessage.error(t('common.errors.operation_failed'));
        router.push({ name: 'master.fuel_types.index' });
    } finally {
        formLoading.value = false;
    }
};

const onSubmit = async () => {
    if (!formRef.value) return;
    
    try {
        await formRef.value.validate();
        formLoading.value = true;
        
        const url = isEdit.value ? `/master/fuel-types/${route.params.id}/update` : '/master/fuel-types/store';
        const method = isEdit.value ? 'put' : 'post';
        
        await axios[method](url, form.value);
        
        ElMessage.success(t(isEdit.value ? 'common.messages.updated' : 'common.messages.created'));
        router.push({ name: 'master.fuel_types.index' });
    } catch (error) {
        if (error.response?.data?.errors) {
            // Handle validation errors from server
            const errors = error.response.data.errors;
            for (const field in errors) {
                formRef.value.setFields({
                    [field]: {
                        message: errors[field][0],
                        field: field
                    }
                });
            }
        } else {
            ElMessage.error(t('common.errors.save_failed'));
        }
    } finally {
        formLoading.value = false;
    }
};

const onCancel = () => {
    router.push({ name: 'master.fuel_types.index' });
};

onMounted(() => {
    fetchData();
});
</script>