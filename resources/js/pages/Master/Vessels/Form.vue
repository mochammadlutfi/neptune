<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader
            :title="isEdit ? $t('master.vessels.edit') : $t('master.vessels.create')"
        />

        <!-- Form Content -->
        <el-card body-class="!p-0" class="!rounded-lg !shadow-lg" v-loading="loading">
            <el-form :model="form" :rules="formRules" ref="formRef" @submit.prevent="onSubmit" label-position="top">
                
                <!-- Section 1: Basic Contract Information -->
                <div class="p-6">
                    <el-row :gutter="24">
                        <el-col :md="12" :sm="24">
                            <el-form-item :label="$t('master.vessels.fields.code')" prop="code">
                                <el-input 
                                    v-model="form.code" 
                                    :placeholder="$t('master.vessels.placeholders.code')"
                                    maxlength="50"
                                    show-word-limit
                                />
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" :sm="24">
                            <el-form-item :label="$t('master.vessels.fields.name')" prop="name">
                                <el-input 
                                    v-model="form.name" 
                                    :placeholder="$t('master.vessels.placeholders.name')"
                                    maxlength="191"
                                    show-word-limit
                                />
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" :sm="24">
                            <el-form-item :label="$t('master.vessels.fields.type')" prop="type">
                                <el-select 
                                    v-model="form.type" 
                                    :placeholder="$t('master.vessels.placeholders.type')"
                                    class="w-full"
                                    clearable
                                >
                                    <el-option 
                                        v-for="(label, value) in vesselTypes" 
                                        :key="value" 
                                        :label="label" 
                                        :value="value" 
                                    />
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" :sm="24">
                            <el-form-item :label="$t('master.vessels.fields.oim_id')" prop="oim_id">
                                <select-user
                                    v-model="form.oim_id"
                                    :placeholder="$t('master.vessels.placeholders.oim_id')"
                                    class="w-full"
                                />
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" :sm="24">
                            <el-form-item :label="$t('master.vessels.fields.client_name')" prop="client_name">
                                <el-input 
                                    v-model="form.client_name" 
                                    :placeholder="$t('master.vessels.placeholders.client_name')"
                                    maxlength="191"
                                />
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" :sm="24">
                            <el-form-item :label="$t('master.vessels.fields.client_oim')" prop="client_oim">
                                <el-input 
                                    v-model="form.client_oim"
                                    :placeholder="$t('master.vessels.placeholders.client_oim')"
                                    maxlength="191"
                                />
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" :sm="24">
                            <el-form-item :label="$t('master.vessels.fields.status')" prop="status">
                                <el-switch
                                    v-model="form.is_active"
                                    :active-text="$t('master.vessels.status.active')"
                                    :inactive-text="$t('master.vessels.status.inactive')"
                                />
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

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Icon } from '@iconify/vue'
import axios from 'axios'

// Import components
import PageHeader from '@/components/PageHeader.vue'
import SelectUser from '@/components/form/SelectUser.vue'


const { t } = useI18n()
const route = useRoute()
const router = useRouter()

// Form state
const formRef = ref(null)
const loading = ref(false)
const isEdit = computed(() => !!route.params.id)

// Vessel types options
const vesselTypes = {
    'FPU' : 'FPU',
    'MOPU' : 'MOPU'
}

// Form data
const form = reactive({
    code: '',
    name: '',
    type: '',
    oim_id: null,
    client_name: '',
    client_oim: '',
    is_active: true,
})

// Validation rules
const formRules = ref({
    // Basic Information
    code: [
        { required: true, message: t('common.validation.required', { attribute : t('master.vessels.fields.code')}), trigger: 'blur' },
        { min: 2, max: 50, message: t('common.validation.min_length', { min: 2 }), trigger: 'blur' }
    ],
    name: [
        { required: true, message: t('common.validation.required', { attribute : t('master.vessels.fields.name')}), trigger: 'blur' },
        { min: 2, max: 191, message: t('common.validation.min_length', { min: 2 }), trigger: 'blur' }
    ],
    type: [
        { required: true, message: t('common.validation.required', { attribute : t('master.vessels.fields.type')}), trigger: 'change' }
    ],
    oim_id: [
        { required: true, message: t('common.validation.required', { attribute : t('master.vessels.fields.oim_id')}), trigger: 'change' }
    ],
    client_name: [
        { required: true, message: t('common.validation.required', { attribute : t('master.vessels.fields.client_name')}), trigger: 'blur' },
        { min: 2, max: 191, message: t('common.validation.min_length', { min: 2 }), trigger: 'blur' }
    ],
    client_oim: [
        { required: true, message: t('common.validation.required', { attribute : t('master.vessels.fields.client_oim')}), trigger: 'blur' },
        { min: 2, max: 191, message: t('common.validation.min_length', { min: 2 }), trigger: 'blur' }
    ],
})

// Methods
const goBack = () => {
    if (hasUnsavedChanges()) {
        ElMessageBox.confirm(
            t('common.confirmations.confirm_leave'),
            t('common.confirmations.are_you_sure'),
            {
                confirmButtonText: t('common.actions.confirm'),
                cancelButtonText: t('common.actions.cancel'),
                type: 'warning'
            }
        ).then(() => {
            router.push({ name: 'master.vessels.index' })
        })
    } else {
        router.push({ name: 'master.vessels.index' })
    }
}

const hasUnsavedChanges = () => {
    // Simple check - in production you might want more sophisticated dirty checking
    return Object.values(form).some(value => value !== '' && value !== null && value !== false)
}

const fetchVesselData = async () => {
    if (!isEdit.value) return
    
    try {
        loading.value = true
        const response = await axios.get(`/master/vessels/${route.params.id}`)
        const vesselData = response.data.data
        
        if (response.data) {
            Object.keys(form).forEach(key => {
                if (vesselData[key] !== undefined) {
                    form[key] = vesselData[key]
                }
            })
        }
    } catch (error) {
        console.error('Error fetching vessel data:', error)
        ElMessage.error(t('common.errors.operation_failed'))
        goBack()
    } finally {
        loading.value = false
    }
}

const saveForm = async () => {
    if (!formRef.value) return
    
    try {
        const valid = await formRef.value.validate()
        if (!valid) return
        
        loading.value = true
        
        const url = isEdit.value 
            ? `/master/vessels/${route.params.id}/update` 
            : '/master/vessels/store'
        const method = isEdit.value ? 'put' : 'post'
        
        const response = await axios({
            method,
            url,
            data: form
        })
        
        if (response.data.success) {
            ElMessage.success(
                isEdit.value 
                    ? t('common.success.item_updated') 
                    : t('common.success.item_created')
            )
            router.push({ name: 'master.vessels.index' })
        } else {
            throw new Error(response.data.result || 'Operation failed')
        }
    } catch (error) {
        console.error('Error saving vessel:', error)
        
        if (error.response?.status === 422 && error.response?.data?.result) {
            // Handle validation errors
            const errors = error.response.data.result
            if (typeof errors === 'object') {
                Object.keys(errors).forEach(field => {
                    ElMessage.error(`${field}: ${errors[field][0]}`)
                })
            } else {
                ElMessage.error(errors)
            }
        } else {
            ElMessage.error(t('common.errors.operation_failed'))
        }
    } finally {
        loading.value = false
    }
}

const onSubmit = saveForm

// Lifecycle
onMounted(() => {
    fetchVesselData()
})
</script>

<style scoped>
</style>