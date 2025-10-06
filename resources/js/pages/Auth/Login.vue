<template>
    <div class="flex flex-col gap-6">
        <Card>
        <CardHeader class="text-center">
            <CardTitle class="text-xl fw-bold">
                Welcome back
            </CardTitle>
        </CardHeader>
        <CardContent>
            <el-form ref="formRef" :model="form" :rules="formRules" label-position="top" @submit.prevent="submit">
                <el-form-item :label="$t('auth.login.email')" prop="email" :error="emailError">
                    <el-input v-model="form.email" type="text" @input="clearEmailError" />
                </el-form-item>
                <el-form-item :label="$t('auth.login.password')" prop="password">
                    <el-input v-model="form.password" type="password" show-password />
                </el-form-item>
                <el-button native-type="submit" type="primary" class="w-full" :loading="isLoading">
                    {{ $t('auth.login.button') }}
                </el-button>
            </el-form>
        </CardContent>
        </Card>
    </div>
</template>
<script setup>

import { ref } from 'vue';
import { useAuth } from '@/composables/auth'
import { useI18n } from 'vue-i18n';
import { useRouter } from "vue-router";
import { Card, CardHeader, CardContent, CardTitle } from '@/components/ui/card'

const { t } = useI18n();
const router = useRouter();

// Auth composable dengan loading state terintegrasi
const { login, isLoading, error } = useAuth();

const formRef = ref(null);
const form = ref({
  email: "",
  password: "",
});

const formRules = ref({
    email: [
        { required: true, message: t('validation.common.required', { attribute: t('auth.login.email') }), trigger: 'blur' },
    ],
    password: [
        { required: true, message: t('validation.common.required', { attribute: t('auth.login.password') }), trigger: 'blur' },
    ],
});

const emailError = ref('');

const clearEmailError = () => {
    emailError.value = '';
};

/**
 * Submit form login dengan validasi dan error handling
 */
const submit = async () => {
    if (!formRef.value) return;
    
    formRef.value.validate(async (valid) => {
        if (valid) {
            try {
                // Login menggunakan composable auth
                await login(form.value);
                
                // Tampilkan pesan sukses
                ElMessage({
                    message: t('auth.messages.login_success'),
                    type: 'success',
                });
                
                // Redirect ke dashboard
                router.replace('/dashboard');
            } catch (loginError) {
                // Handle validation errors khusus untuk email
                if (loginError.validation?.email) {
                    emailError.value = Array.isArray(loginError.validation.email) 
                        ? loginError.validation.email[0] 
                        : loginError.validation.email;
                }
                console.log(loginError)
                // Tampilkan pesan error
                ElMessage({
                    message: loginError.message || t('common.errors.authentication_failed'),
                    type: 'error',
                });
            }
        } else {
            ElMessage({
                message: t('common.form.please_correct_errors'),
                type: 'error',
            });
        }
    });
};

</script>