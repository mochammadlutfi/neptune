<template>
    <div class="content">
        <!-- Page Header -->
        <PageHeader :title="$t('settings.system.general.title')">
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
                
                <!-- Section 1: Application Settings -->
                <div class="border-b border-gray-100">
                    <div class="p-6 bg-muted">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1 flex items-center">
                            <Icon icon="mingcute:settings-3-line" class="mr-2 text-primary" />
                            {{ $t('settings.system.general.application_settings') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <el-row :gutter="24">
                            <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                                <el-form-item :label="$t('settings.system.general.app_name')" prop="app_name">
                                    <el-input v-model="form.app_name" :placeholder="$t('settings.system.general.app_name')" />
                                </el-form-item>
                            </el-col>
                            <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                                <el-form-item :label="$t('settings.system.general.company_name')" prop="company_name">
                                    <el-input v-model="form.company_name" :placeholder="$t('settings.system.general.company_name')" />
                                </el-form-item>
                            </el-col>
                            <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                                <el-form-item :label="$t('settings.system.general.company_email')" prop="company_email">
                                    <el-input v-model="form.company_email" :placeholder="$t('settings.system.general.company_email')">
                                        <template #prefix>
                                            <Icon icon="mingcute:mail-line" class="text-gray-400" />
                                        </template>
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                                <el-form-item :label="$t('settings.system.general.company_phone')" prop="company_phone">
                                    <el-input v-model="form.company_phone" :placeholder="$t('settings.system.general.company_phone')">
                                        <template #prefix>
                                            <Icon icon="mingcute:phone-line" class="text-gray-400" />
                                        </template>
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                                <el-form-item :label="$t('settings.system.general.company_city')" prop="company_city">
                                    <el-input v-model="form.company_city" :placeholder="$t('settings.system.general.company_city')">
                                        <template #prefix>
                                            <Icon icon="mingcute:map-pin-line" class="text-gray-400" />
                                        </template>
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="8" :sm="12" :xs="24" class="mb-4">
                                <el-form-item :label="$t('settings.system.general.company_country')" prop="company_country">
                                    <el-input v-model="form.company_country" :placeholder="$t('settings.system.general.company_country')">
                                        <template #prefix>
                                            <Icon icon="mingcute:flag-line" class="text-gray-400" />
                                        </template>
                                    </el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="16" class="mb-4">
                                <el-form-item :label="$t('settings.system.general.company_address')" prop="company_address">
                                    <el-input v-model="form.company_address" type="textarea" :rows="3" :placeholder="$t('settings.system.general.company_address')" />
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </div>
                </div>

                <!-- Section 2: Regional Settings -->
                <div class="border-b border-gray-100">
                    <div class="p-6 bg-muted">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1 flex items-center">
                            <Icon icon="mingcute:earth-line" class="mr-2 text-primary" />
                            {{ $t('settings.system.general.regional_settings') }}
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <el-row :gutter="24">
                            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                                <el-form-item :label="$t('settings.system.general.timezone')" prop="timezone">
                                    <select-time-zone v-model="form.timezone" />
                                </el-form-item>
                            </el-col>
                            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                                <el-form-item :label="$t('settings.system.general.date_format')" prop="date_format">
                                    <select-date-format v-model="form.date_format" />
                                </el-form-item>
                            </el-col>
                            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                                <el-form-item :label="$t('settings.system.general.time_format')" prop="time_format">
                                    <select-time-format v-model="form.time_format" />
                                </el-form-item>
                            </el-col>
                            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                                <el-form-item :label="$t('settings.system.general.language')" prop="locale">
                                    <select-locale v-model="form.locale" />
                                </el-form-item>
                            </el-col>
                            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                                <el-form-item :label="$t('settings.system.general.currency')" prop="currency_id">
                                    <select-currency v-model="form.currency_id" />
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </div>
                </div>

                <!-- Section 3: System Branding -->
                <div class="border-b border-gray-100">
                    <div class="p-6 bg-muted">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1 flex items-center">
                            <Icon icon="mingcute:pic-line" class="mr-2 text-primary" />
                            {{ $t('settings.system.general.branding') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <el-row :gutter="24">
                            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                                <el-form-item :label="$t('settings.system.general.logo_light')">
                                    <image-upload v-model="form.logo_light" class="!h-28 !w-28" :cropper="false" />
                                </el-form-item>
                            </el-col>
                            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                                <el-form-item :label="$t('settings.system.general.logo_light_sm')">
                                    <image-upload v-model="form.logo_light_sm" class="!h-28 !w-28" :cropper="false" />
                                </el-form-item>
                            </el-col>
                            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                                 <el-form-item :label="$t('settings.system.general.logo_dark')">
                                     <image-upload v-model="form.logo_dark" class="!h-28 !w-28" :cropper="false" />
                                 </el-form-item>
                             </el-col>
                             <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                                 <el-form-item :label="$t('settings.system.general.logo_dark_sm')">
                                     <image-upload v-model="form.logo_dark_sm" class="!h-28 !w-28" :cropper="false" />
                                 </el-form-item>
                             </el-col>
                            <el-col :md="6" :sm="12" :xs="24" class="mb-4">
                                <el-form-item :label="$t('settings.system.general.favicon')">
                                    <image-upload v-model="form.favicon" class="!h-28 !w-28" :cropper="false" />
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="p-6 bg-gray-50 border-t border-gray-100">
                    <div class="flex justify-end space-x-3">
                        <el-button @click="resetForm">
                            <Icon icon="mingcute:refresh-2-line" class="mr-2" />
                            {{ $t('common.actions.reset') }}
                        </el-button>
                        <el-button native-type="submit" type="primary" :loading="formLoading">
                            <Icon icon="mingcute:check-fill" class="mr-2" />
                            {{ formLoading ? $t('settings.system.general.saving') : $t('settings.system.general.save_settings') }}
                        </el-button>
                    </div>
                </div>
            </el-form>
        </el-card>
    </div>
</template>

<script setup>
import { Icon } from '@iconify/vue';
import { ref, reactive, onMounted, computed } from 'vue';
import SelectDateFormat from '@/components/form/SelectDateFormat.vue';
import SelectTimeFormat from '@/components/form/SelectTimeFormat.vue';
import SelectTimeZone from '@/components/form/SelectTimeZone.vue';
import SelectLocale from '@/components/form/SelectLocale.vue';
import ImageUpload from '@/components/form/ImageUpload.vue';
import { useI18n } from 'vue-i18n';
import PageHeader from '@/components/PageHeader.vue';
import { ElMessage } from 'element-plus';
import { convertToFormData } from '@/utils/utility';
import { useAppBaseStore } from '@/stores/base';

const { t } = useI18n();
const formRef = ref(null);
const formLoading = ref(false);
const appBase = useAppBaseStore();

const app = computed(() => appBase.app);

// Form data dengan nilai default menggunakan reactive
const form = reactive({
    app_name: null,
    logo_light: null,
    logo_dark: null,
    logo_light_sm: null,
    logo_dark_sm: null,
    favicon: null,
    timezone: null,
    date_format: "d-m-Y",
    time_format: "HH:mm",
    locale: null,
    company_name: null,
    company_address: null,
    company_city: null,
    company_state: null,
    company_country: null,
    company_phone: null,
    company_mobile: null,
    company_email: null
});

// Validasi form
const formRules = ref({
    app_name: [{
        required: true,
        message: t('validation.common.required', {
            attribute: t('settings.system.general.company_name')
        }),
        trigger: 'blur'
    }],
    company_email: [{
        type: 'email',
        message: t('validation.common.email', {
            attribute: t('settings.system.general.company_email')
        }),
        trigger: 'blur'
    }],
    timezone: [{
        required: true,
        message: t('validation.common.required', {
            attribute: t('settings.system.general.timezone')
        }),
        trigger: 'change'
    }],
    date_format: [{
        required: true,
        message: t('validation.common.required', {
            attribute: t('settings.system.general.date_format')
        }),
        trigger: 'change'
    }],
    time_format: [{
        required: true,
        message: t('validation.common.required', {
            attribute: t('settings.system.general.time_format')
        }),
        trigger: 'change'
    }],
    locale: [{
        required: true,
        message: t('validation.common.required', {
            attribute: t('settings.system.general.language')
        }),
        trigger: 'change'
    }]
});

const errors = ref({});

// Fungsi untuk menyimpan pengaturan
const onSubmit = async () => {
    if (!formRef.value) return;
    
    formRef.value.validate(async (valid) => {
        if (valid) {
            try {
                formLoading.value = true;
                const url = '/settings/general/update';
                
                const formData = convertToFormData(form);
                const response = await axios.post(url, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    },
                });
                
                if (response.data.success) {
                    await appBase.reinitApp();
                    ElMessage({ 
                        message: t('settings.system.general.settings_updated'), 
                        type: 'success' 
                    });
                }
            } catch (error) {
                console.error('Error saving settings:', error);
                
                // Handle validation errors dari server
                if (error.response && error.response.status === 422) {
                    errors.value = error.response.data.result || {};
                    ElMessage({
                        message: t('settings.system.general.error_occurred'),
                        type: 'error'
                    });
                } else {
                    ElMessage({
                        message: t('common.errors.save_failed'),
                        type: 'error'
                    });
                }
            } finally {
                formLoading.value = false;
            }
        }
    });
};

// Fungsi untuk reset form
const resetForm = () => {
    if (formRef.value) {
        formRef.value.resetFields();
    }
    loadInitialData(); // Reload data dari store
};

// Load data awal dari store
const loadInitialData = () => {
    form.app_name = app.value.app_name;
    form.app_url = app.value.app_url;
    form.logo_light = app.value.logo_light;
    form.logo_light_sm = app.value.logo_light_sm;
    form.logo_dark = app.value.logo_dark;
    form.logo_dark_sm = app.value.logo_dark_sm;
    form.favicon = app.value.favicon;
    form.time_format = app.value.time_format;
    form.date_format = app.value.date_format;
    form.timezone = app.value.timezone;
    form.locale = app.value.locale;
    form.currency_id = app.value.currency?.id;
    form.company_name = app.value.company_name;
    form.company_address = app.value.company_address;
    form.company_city = app.value.company_city;
    form.company_state = app.value.company_state;
    form.company_country = app.value.company_country;
    form.company_phone = app.value.company_phone;
    form.company_mobile = app.value.company_mobile;
    form.company_email = app.value.company_email;
};

// Load data saat komponen dimount
onMounted(() => {
    loadInitialData();
});
</script>

<style scoped>
/* Form styling improvements */
.el-form-item {
    margin-bottom: 1.5rem;
}

.el-input, .el-select {
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

/* Background muted untuk section headers */
.bg-muted {
    background-color: #f8fafc;
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

.text-primary {
    color: #3b82f6;
}

/* Image upload styling */
.image-upload {
    border-radius: 8px;
    overflow: hidden;
}
</style>
