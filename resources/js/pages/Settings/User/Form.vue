<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader
            :title="route.params.id ? $t('settings.user.edit') : $t('settings.user.add')"
        />

        <el-card class="!rounded-lg !shadow-md" v-loading="isLoading">
            <el-form :model="form" :rules="formRules" ref="formRef" @submit.prevent="onSubmit" label-position="top">
                <div class="p-4">
                    <el-row :gutter="20">
                        <el-col :md="12">
                            <el-form-item :label="$t('settings.user.fields.profile_image')">
                                <image-upload v-model="form.image" size="small"/>
                            </el-form-item>
                            <el-form-item :label="$t('settings.user.fields.name')" prop="name">
                                <el-input v-model="form.name" class="w-full" />
                            </el-form-item>
                            <el-form-item :label="$t('settings.user.fields.email')" prop="email">
                                <el-input v-model="form.email" class="w-full" />
                            </el-form-item>
                            <el-form-item :label="$t('settings.user.fields.phone')">
                                <el-input v-model="form.phone" class="w-full" />
                            </el-form-item>
                        </el-col>
                        <el-col :md="12">
                            <el-form-item :label="$t('settings.user.fields.allowed_vessel')" prop="vessel_list">
                                <select-vessel v-model="form.vessel_list" multiple/>
                            </el-form-item>
                            <el-form-item :label="$t('settings.user.fields.vessel_id')" prop="vessel_id">
                                <select-vessel v-model="form.vessel_id"/>
                            </el-form-item>
                            <el-form-item :label="$t('settings.user.fields.role')" prop="role">
                                <select-role v-model="form.role" class="w-full"/>
                            </el-form-item>
                            <el-form-item :label="$t('settings.user.fields.password')" prop="password">
                                <el-input v-model="form.password" type="password" show-password class="w-full"/>
                            </el-form-item>
                            <el-form-item :label="$t('settings.user.fields.confirm_password')" prop="password_confirmation">
                                <el-input v-model="form.password_confirmation" type="password" show-password class="w-full"/>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </div>

                <div class="p-4 border-t border-gray-100 text-end">
                    <el-button @click.prevent="router.back()">
                        {{ $t('common.actions.cancel') }}
                    </el-button>
                    <el-button native-type="submit" type="primary">
                        {{ $t('common.actions.save') }}
                    </el-button>
                </div>
            </el-form>
        </el-card>
    </div>
</template>

<script setup>
import ImageUpload from '@/components/form/ImageUpload.vue'
import SelectRole from '@/components/form/SelectRole.vue'
import PageHeader from '@/components/PageHeader.vue'
import { useRoute, useRouter } from 'vue-router'
import { ref, watch, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import axios from 'axios'
import { ElMessage } from 'element-plus'
import SelectVessel from '@/components/select/SelectVessel.vue'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()

const title = computed(() => {
    return route.params.id ? t('settings.user.edit') : t('settings.user.add');
});

const form = ref({
    id: route.params.id ? route.params.id : null,
    name: '',
    email : '',
    phone: '',
    password : '',
    password_confirmation : '',
    role : null,
    image : null,
    vessel_id : null,
    vessel_list : null,
})

const formRef = ref(null)
const formRules = computed(() => ({
    name: [
        { required: true, message: t('common.validation.required_field', { field: t('settings.user.fields.name') }), trigger: 'blur' },
    ],
    email: [
        { required: true, message: t('common.validation.required_field', { field: t('settings.user.fields.email') }), trigger: 'blur' },
        { type: 'email', message: t('common.validation.invalid_email'), trigger: 'blur' },
    ],
    phone: [
        // Phone is nullable in backend, so not required here unless explicitly set
    ],
    role: [
        { required: true, message: t('common.validation.required_field', { field: t('settings.user.fields.role') }), trigger: 'change' },
    ],
    password: [
        { required: !route.params.id, message: t('common.validation.required_field', { field: t('settings.user.fields.password') }), trigger: 'blur' },
        { min: 6, message: t('common.validation.min_length', { min: 6 }), trigger: 'blur' },
    ],
    password_confirmation: [
        { required: !route.params.id, message: t('common.validation.required_field', { field: t('settings.user.fields.confirm_password') }), trigger: 'blur' },
        { validator: (rule, value, callback) => {
            if (form.value.password && value !== form.value.password) {
                callback(new Error(t('common.validation.password_mismatch')))
            } else {
                callback()
            }
        }, trigger: 'blur' },
    ],
}))
const isLoading = ref(false)

const fetchData = async () => {
    try {
        isLoading.value = true
        const response = await axios.get(`/settings/user/${route.params.id}`)
        if (response.status === 200) {
            const data = response.data.result // Assuming result key
            form.value.id = data.id
            form.value.name = data.name
            form.value.email = data.email
            form.value.username = data.username
            form.value.phone = data.phone
            form.value.role = data.roles?.[0]?.id // Handle roles array
            form.value.image = data.image_url // Assuming image_url is the path
            form.value.vessel_id = data.vessel_id
            form.value.vessel_list = data.vessels?.map(v => v.id)
        }
    } catch (error) {
        console.error(error)
        ElMessage.error(t('common.errors.server_error'))
    } finally {
        isLoading.value = false
    }
}

const onSubmit = async () => {
    if (!formRef.value) return
    formRef.value.validate(async (valid) => {
        if (valid) {
            try {
                isLoading.value = true
                const url = form.value.id ? 
                `/settings/user/${form.value.id}/update` : 
                '/settings/user/store'
                const method = form.value.id ? 'put' : 'post'
                
                const formData = new FormData()
                for (const key in form.value) {
                    if (key === 'image' && form.value[key] instanceof File) {
                        formData.append(key, form.value[key])
                    } else {
                        formData.append(key, form.value[key])
                    }
                }
                if (method === 'put') {
                    formData.append('_method', 'PUT')
                }

                await axios({
                    method: 'post', // Always post for FormData with _method for PUT
                    url,
                    data: formData,
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })

                ElMessage.success(t('common.success.changes_saved'))
                router.replace({ path: '/settings/user' }) // Navigate back to user list
            } catch (error) {
                console.error(error);
                isLoading.value = false;
                ElMessage.error(error.response?.data?.message || t('common.errors.server_error'));
            }
        } else {
            ElMessage.error(t('common.errors.validation_failed'));
        }
    });
};

onMounted(() => {
    if(route.params.id){
        fetchData();
    }
});

// Watch for password changes to re-validate password_confirmation
watch(() => form.value.password, () => {
    if (formRef.value) {
        formRef.value.validateField('password_confirmation');
    }
});
</script>