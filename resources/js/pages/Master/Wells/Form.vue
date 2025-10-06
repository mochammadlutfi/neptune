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

        <!-- Form Content -->
        <el-card body-class="!p-0" class="!rounded-lg !shadow-lg" v-loading="loading">
            <el-form :model="form" :rules="formRules" ref="formRef" @submit.prevent="onSubmit" label-position="top">

                <!-- Section 1: Basic Contract Information -->
                <div class="p-6">
                    <el-row :gutter="24">
                        <el-col :md="12" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('master.wells.fields.vessel_id')" prop="vessel_id">
                                <select-vessel v-model="form.vessel_id"
                                    :placeholder="$t('master.wells.placeholders.vessel_id')" />
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('master.wells.fields.code')" prop="code">
                                <el-input v-model="form.code" :placeholder="$t('master.wells.placeholders.code')"
                                    maxlength="20" show-word-limit />
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('master.wells.fields.name')" prop="name">
                                <el-input v-model="form.name" :placeholder="$t('master.wells.placeholders.name')"
                                    maxlength="100" show-word-limit />
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('master.wells.fields.type')" prop="type">
                                <el-select v-model="form.type" :placeholder="$t('master.wells.placeholders.type')"
                                    class="w-full">
                                    <el-option v-for="(label, value) in wellTypes" :key="value" :label="label"
                                        :value="value" />
                                </el-select>
                            </el-form-item>
                        </el-col>

                        <!-- Oil rate -->
                        <el-col :md="12" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('master.wells.fields.max_oil_rate')" prop="max_oil_rate">
                                <el-input v-model.number="form.max_oil_rate" type="number"
                                    :placeholder="$t('master.wells.placeholders.max_oil_rate')" :min="0" :step="0.1"
                                    @change="() => formRef?.validateField('max_oil_rate')">
                                    <template #prefix>
                                        <Icon icon="heroicons:beaker" class="text-green-600" :width="16" :height="16" />
                                    </template>
                                    <template #suffix>
                                        <span class="text-gray-500 text-sm">BPH</span>
                                    </template>
                                </el-input>
                            </el-form-item>
                        </el-col>

                        <!-- Gas rate -->
                        <el-col :md="12" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('master.wells.fields.max_gas_rate')" prop="max_gas_rate">
                                <el-input v-model.number="form.max_gas_rate" type="number"
                                    :placeholder="$t('master.wells.placeholders.max_gas_rate')" :min="0" :step="0.1"
                                    @change="() => formRef?.validateField('max_gas_rate')">
                                    <template #prefix>
                                        <Icon icon="heroicons:cloud" class="text-blue-600" :width="16" :height="16" />
                                    </template>
                                    <template #suffix>
                                        <span class="text-gray-500 text-sm">MSCFH</span>
                                    </template>
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('master.wells.fields.max_water_rate')" prop="max_water_rate">
                                <el-input v-model.number="form.max_water_rate" type="number"
                                    :placeholder="$t('master.wells.placeholders.max_water_rate')" :min="0" :step="0.1"
                                    @change="() => formRef?.validateField('max_water_rate')">
                                    <template #prefix>
                                        <Icon icon="heroicons:beaker" class="text-cyan-600" :width="16" :height="16" />
                                    </template>
                                    <template #suffix>
                                        <span class="text-gray-500 text-sm">BPH</span>
                                    </template>
                                </el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" :sm="12" :xs="24" class="mb-4">
                            <el-form-item :label="$t('master.wells.fields.status')" prop="status">
                                <el-select v-model="form.status" :placeholder="$t('master.wells.placeholders.status')"
                                    class="w-full">
                                    <el-option v-for="(label, value) in statuses" :key="value" :label="label"
                                        :value="value" />
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
                        <el-button native-type="submit" type="primary" :loading="loading">
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
import { ref, computed, onMounted, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'

import PageHeader from '@/components/PageHeader.vue'
import SelectVessel from '@/components/select/SelectVessel.vue'
import { wellsApi } from '@/api/master/wells'
const router = useRouter()
const route = useRoute()
const { t } = useI18n()

const title = ref(route.meta.title ?? 'master.wells.create')
const formRef = ref(null)

// State
const loading = ref(false)
const vessels = ref([])

// Check if editing
const isEdit = computed(() => !!route.params.id)

// Form data
const form = reactive({
    vessel_id: null,
    code: null,
    name: null,
    type: null,
    max_oil_rate: null,
    max_gas_rate: null,
    max_water_rate :null,
    status: 'Active',
})

// Options - Use translations
const wellTypes = computed(() => ({
    'Oil': 'Oil',
    'Gas': 'Gas',
    'Water Injection': 'Water Injection',
    'Gas Injection' : 'Gas Injection'
}))

const statuses = computed(() => ({
    'active': t('master.wells.status.active'),
    'shut_in': t('master.wells.status.shut_in'),
    'testing': t('master.wells.status.testing'),
    'abandoned': t('master.wells.status.abandoned')
}))

// Validation rules
const formRules = ref({
    vessel_id: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.wells.fields.vessel_id') }),
        trigger: 'change'
    }],
    
    code: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.wells.fields.code') }),
        trigger: 'blur'
    }, {
        min: 2,
        max: 20,
        message: t('common.validation.between', { attribute: t('master.wells.fields.code'), min: 2, max: 20 }),
        trigger: 'blur'
    }],
    
    name: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.wells.fields.name') }),
        trigger: 'blur'
    }, {
        min: 2,
        max: 100,
        message: t('common.validation.between', { attribute: t('master.wells.fields.name'), min: 2, max: 100 }),
        trigger: 'blur'
    }],
    
    type: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.wells.fields.type') }),
        trigger: 'change'
    }],
    
    status: [{
        required: true,
        message: t('common.validation.required', { attribute: t('master.wells.fields.status') }),
        trigger: 'change'
    }]
})

// Load well data for editing
const loadWell = async () => {
    if (!isEdit.value) return
    
    try {
        loading.value = true
        const response = await wellsApi.getById(route.params.id)
        const wellData = response.data.data
        
        // Map the data to form
        Object.keys(form).forEach(key => {
            if (wellData[key] !== undefined) {
                form[key] = wellData[key]
            }
        })
    } catch (error) {
        console.error('Failed to load well:', error)
        ElMessage.error(t('common.errors.server_error'))
        onBack()
    } finally {
        loading.value = false
    }
}

// Submit form
const onSubmit = async () => {
    if (!formRef.value) return
    
    formRef.value.validate(async (valid) => {
        if (valid) {
            try {
                loading.value = true
                
                // Prepare form data

                const url = route.params.id ? `/master/wells/${route.params.id}/update` : '/master/wells/store'
                const method = route.params.id ? 'put' : 'post'

                const response = await axios({
                    method,
                    url,
                    data: form,
                })
                
                if (response.status === 200 || response.status === 201) {
                    ElMessage({
                        message: t('common.messages.saved'),
                        type: 'success',
                    })
                    router.replace({ path: `/master/wells/${response.data.id || response.data.data?.id}` })
                }
            } catch (error) {
                console.log(error)
                loading.value = false
                
                // Handle validation errors from server
                if (error.response && error.response.status === 422) {
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
            }
        } else {
            ElMessage({
                message: t('common.errors.validation_failed'),
                type: 'error',
            })
        }
    })
}

// Go back
const onBack = () => {
    router.push({ name: 'master.wells.index' })
}

// Initialize
onMounted(() => {
    loadWell()
})
</script>
