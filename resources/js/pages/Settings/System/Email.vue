<template>
    <div class="email-settings">
        <PageHeader 
            :title="$t('settings.system.email.title')"
            :subtitle="$t('settings.system.email.subtitle')"
        />
        
        <el-card class="shadow-sm" v-loading="loading">
            <template #header>
                <div class="card-header">
                    <h3 class="text-lg font-medium text-gray-900">{{ $t('settings.system.email.title') }}</h3>
                    <p class="mt-1 text-sm text-gray-600">{{ $t('settings.system.email.subtitle') }}</p>
                </div>
            </template>

            <el-form 
                :model="form" 
                @submit.prevent="handleSubmit" 
                label-position="top"
                :rules="rules"
                ref="formRef"
            >
                <!-- SMTP Configuration Section -->
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">{{ $t('settings.system.email.smtp_config') }}</h4>
                    <el-row :gutter="20">
                        <el-col :md="12">
                            <el-form-item 
                                :label="$t('settings.system.email.smtp_host')" 
                                prop="mail_host"
                            >
                                <el-input 
                                    v-model="form.mail_host" 
                                    :placeholder="$t('settings.system.email.smtp_host_placeholder')"
                                />
                            </el-form-item>
                        </el-col>
                        <el-col :md="12">
                            <el-form-item 
                                :label="$t('settings.system.email.smtp_port')" 
                                prop="mail_port"
                            >
                                <el-input 
                                    v-model="form.mail_port" 
                                    type="number"
                                    :placeholder="$t('settings.system.email.smtp_port_placeholder')"
                                />
                            </el-form-item>
                        </el-col>
                    </el-row>
                    
                    <el-row :gutter="20">
                        <el-col :md="12">
                            <el-form-item 
                                :label="$t('settings.system.email.smtp_username')" 
                                prop="mail_username"
                            >
                                <el-input 
                                    v-model="form.mail_username" 
                                    :placeholder="$t('settings.system.email.smtp_username_placeholder')"
                                />
                            </el-form-item>
                        </el-col>
                        <el-col :md="12">
                            <el-form-item 
                                :label="$t('settings.system.email.smtp_password')" 
                                prop="mail_password"
                            >
                                <el-input 
                                    v-model="form.mail_password" 
                                    type="password"
                                    show-password
                                    :placeholder="$t('settings.system.email.smtp_password_placeholder')"
                                />
                            </el-form-item>
                        </el-col>
                    </el-row>
                    
                    <el-row :gutter="20">
                        <el-col :md="12">
                            <el-form-item 
                                :label="$t('settings.system.email.smtp_encryption')" 
                                prop="mail_encryption"
                            >
                                <el-select 
                                    v-model="form.mail_encryption" 
                                    :placeholder="$t('settings.system.email.smtp_encryption_placeholder')"
                                    class="w-full"
                                >
                                    <el-option label="SSL" value="ssl" />
                                    <el-option label="TLS" value="tls" />
                                    <el-option label="None" value="none" />
                                </el-select>
                            </el-form-item>
                        </el-col>
                        <el-col :md="12">
                            <el-form-item 
                                :label="$t('settings.system.email.mail_provider')" 
                                prop="mail_provider"
                            >
                                <el-select 
                                    v-model="form.mail_provider" 
                                    :placeholder="$t('settings.system.email.mail_provider_placeholder')"
                                    class="w-full"
                                >
                                    <el-option label="SMTP" value="smtp" />
                                    <el-option label="Sendmail" value="sendmail" />
                                    <el-option label="Mailgun" value="mailgun" />
                                    <el-option label="SES" value="ses" />
                                </el-select>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </div>

                <!-- From Configuration Section -->
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">{{ $t('settings.system.email.from_config') }}</h4>
                    <el-row :gutter="20">
                        <el-col :md="12">
                            <el-form-item 
                                :label="$t('settings.system.email.from_name')" 
                                prop="mail_from_name"
                            >
                                <el-input 
                                    v-model="form.mail_from_name" 
                                    :placeholder="$t('settings.system.email.from_name_placeholder')"
                                />
                            </el-form-item>
                        </el-col>
                        <el-col :md="12">
                            <el-form-item 
                                :label="$t('settings.system.email.from_email')" 
                                prop="mail_from_address"
                            >
                                <el-input 
                                    v-model="form.mail_from_address" 
                                    type="email"
                                    :placeholder="$t('settings.system.email.from_email_placeholder')"
                                />
                            </el-form-item>
                        </el-col>
                    </el-row>
                </div>

                <!-- Test Email Section -->
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">{{ $t('settings.system.email.test_email') }}</h4>
                    <el-row :gutter="20">
                        <el-col :md="12">
                            <el-form-item 
                                :label="$t('settings.system.email.test_email_address')"
                            >
                                <el-input 
                                    v-model="testEmail" 
                                    type="email"
                                    :placeholder="$t('settings.system.email.test_email_placeholder')"
                                />
                            </el-form-item>
                        </el-col>
                        <el-col :md="12" class="flex items-end">
                            <el-button 
                                type="info" 
                                @click="sendTestEmail"
                                :loading="testLoading"
                                :disabled="!testEmail || !form.mail_host"
                            >
                                <Icon icon="mingcute:send-fill" class="mr-2" />
                                {{ $t('settings.system.email.send_test') }}
                            </el-button>
                        </el-col>
                    </el-row>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <el-button @click="resetForm">
                        <Icon icon="mingcute:refresh-1-line" class="mr-2" />
                        {{ $t('common.actions.reset') }}
                    </el-button>
                    <el-button 
                        type="primary" 
                        native-type="submit"
                        :loading="loading"
                    >
                        <Icon icon="mingcute:check-fill" class="mr-2" />
                        {{ loading ? $t('common.messages.saving') : $t('common.actions.save') }}
                    </el-button>
                </div>
            </el-form>
        </el-card>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'
import { Icon } from '@iconify/vue'
import PageHeader from '@/components/PageHeader.vue'
import axios from 'axios'

const { t } = useI18n()

// Form state menggunakan reactive
const form = reactive({
    mail_provider: 'smtp',
    mail_host: '',
    mail_port: 587,
    mail_username: '',
    mail_password: '',
    mail_encryption: 'tls',
    mail_from_address: '',
    mail_from_name: ''
})

// Refs
const formRef = ref()
const loading = ref(false)
const testLoading = ref(false)
const testEmail = ref('')

// Form validation rules
const rules = {
    mail_host: [
        { required: true, message: t('common.validation.required_field', { field: t('settings.system.email.smtp_host') }), trigger: 'blur' }
    ],
    mail_port: [
        { required: true, message: t('common.validation.required_field', { field: t('settings.system.email.smtp_port') }), trigger: 'blur' },
        { type: 'number', message: t('common.validation.invalid_number'), trigger: 'blur' }
    ],
    mail_username: [
        { required: true, message: t('common.validation.required_field', { field: t('settings.system.email.smtp_username') }), trigger: 'blur' }
    ],
    mail_password: [
        { required: true, message: t('common.validation.required_field', { field: t('settings.system.email.smtp_password') }), trigger: 'blur' }
    ],
    mail_encryption: [
        { required: true, message: t('common.validation.required_field', { field: t('settings.system.email.smtp_encryption') }), trigger: 'change' }
    ],
    mail_from_address: [
        { required: true, message: t('common.validation.required_field', { field: t('settings.system.email.from_email') }), trigger: 'blur' },
        { type: 'email', message: t('common.validation.invalid_email'), trigger: 'blur' }
    ],
    mail_from_name: [
        { required: true, message: t('common.validation.required_field', { field: t('settings.system.email.from_name') }), trigger: 'blur' }
    ]
}

/**
 * Memuat data pengaturan email dari server
 */
const loadEmailSettings = async () => {
    try {
        loading.value = true
        const response = await axios.get('/api/settings/email')
        
        if (response.data) {
            // Update form dengan data dari server
            Object.assign(form, {
                mail_provider: response.data.email_provider || 'smtp',
                mail_host: response.data.mail_host || '',
                mail_port: parseInt(response.data.mail_port) || 587,
                mail_username: response.data.mail_username || '',
                mail_password: response.data.mail_password || '',
                mail_encryption: response.data.mail_encryption || 'tls',
                mail_from_address: response.data.mail_from_address || '',
                mail_from_name: response.data.mail_from_name || ''
            })
        }
    } catch (error) {
        console.error('Error loading email settings:', error)
        ElMessage.error(t('common.errors.operation_failed'))
    } finally {
        loading.value = false
    }
}

/**
 * Menangani submit form untuk menyimpan pengaturan email
 */
const handleSubmit = async () => {
    if (!formRef.value) return
    
    try {
        // Validasi form
        await formRef.value.validate()
        
        loading.value = true
        
        // Kirim data ke server
        const response = await axios.post('/api/settings/email/update', form)
        
        if (response.data.success) {
            ElMessage.success(t('common.success.settings_updated'))
            
            // Reload pengaturan untuk memastikan data terbaru
            await loadEmailSettings()
        } else {
            throw new Error('Update failed')
        }
    } catch (error) {
        console.error('Error saving email settings:', error)
        
        if (error.response?.data?.result) {
            // Handle validation errors dari server
            const errors = error.response.data.result
            Object.keys(errors).forEach(field => {
                ElMessage.error(`${field}: ${errors[field][0]}`)
            })
        } else {
            ElMessage.error(t('common.errors.save_failed'))
        }
    } finally {
        loading.value = false
    }
}

/**
 * Mengirim test email untuk memverifikasi konfigurasi
 */
const sendTestEmail = async () => {
    if (!testEmail.value) {
        ElMessage.warning(t('settings.system.email.test_email_required'))
        return
    }
    
    try {
        testLoading.value = true
        
        const response = await axios.post('/api/settings/email/test', {
            email: testEmail.value,
            ...form
        })
        
        if (response.data.success) {
            ElMessage.success(t('settings.system.email.test_email_sent'))
        } else {
            throw new Error('Test email failed')
        }
    } catch (error) {
        console.error('Error sending test email:', error)
        ElMessage.error(t('settings.system.email.test_email_failed'))
    } finally {
        testLoading.value = false
    }
}

/**
 * Reset form ke nilai default
 */
const resetForm = () => {
    if (formRef.value) {
        formRef.value.resetFields()
    }
    loadEmailSettings()
}

// Lifecycle hooks
onMounted(() => {
    loadEmailSettings()
})
</script>

<style scoped>
.email-settings {
    @apply space-y-6;
}

.card-header {
    @apply border-b border-gray-200 pb-4;
}

.el-form-item {
    @apply mb-4;
}

.el-button {
    @apply transition-all duration-200;
}
</style>