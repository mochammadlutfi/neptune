<template>
    <el-card class="shadow-sm sm:rounded-lg" v-loading="loading">
        <el-form :model="form" @submit.prevent="onSubmit" label-position="top">
            <el-row :gutter="20">
                <el-col :md="12">
                    <el-form-item :label="$t('old_password')" :error="errors.old_password">
                        <el-input v-model="form.old_password" />
                    </el-form-item>
                    <el-form-item :label="$t('new_password')" :error="errors.new_password">
                        <el-input v-model="form.new_password" />
                    </el-form-item>
                    <el-form-item :label="$t('new_password_confirmation')" :error="errors.password_confirmation">
                        <el-input v-model="form.password_confirmation" />
                    </el-form-item>
                </el-col>
            </el-row>
            <div class="text-right">
                <el-button native-type="submit" type="primary">
                    <i class="mgc_check_fill me-2"></i>
                    {{ $t('save') }}
                </el-button>
            </div>
        </el-form>
    </el-card>
</template>

<script setup>
import { defineProps, onMounted, defineEmits, ref } from 'vue';
import { useI18n } from 'vue-i18n';
const t = useI18n();

const emit = defineEmits(['childinit']);
const props = defineProps({
  title: {
    type: String,
    default: '',
  },
});


const form = ref({
    old_password : null,
    new_password : null,
    password_confirmation : null
});
const errors = ref({});
const loading = ref(false);

const onSubmit = async () => {
    loading.value = true;
    const url = '/profile/password';
    try {
        const response = await axios.post(url, form.value);
        console.log(response.data.result);
        ElMessage({message: t('success_msg'), type: 'success'});
    } catch (error) {
        errors.value = error.validation;
        ElMessage({message: t('error_msg'), type: 'error'});
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
  emit('childinit', 'Edit Password');
});
</script>


