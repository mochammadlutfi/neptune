<template>
    <el-card class="shadow-sm sm:rounded-lg" v-loading="loading">
        <el-form :model="form" @submit.prevent="onSubmit" label-position="top">
            <el-row :gutter="20">
                <el-col :md="16">
                    <el-form-item :label="$t('settings.user.fields.name')" prop="name">
                        <el-input v-model="form.name" />
                    </el-form-item>
                    <el-form-item :label="$t('settings.user.fields.email')" prop="email">
                        <el-input v-model="form.email" />
                    </el-form-item>
                    <el-form-item :label="$t('settings.user.fields.phone')">
                        <el-input v-model="form.phone" />
                    </el-form-item>
                </el-col>
                <el-col :md="8">
                    <el-form-item :label="$t('settings.user.fields.profile_image')">
                        <image-upload v-model="form.image" size="small"/>
                    </el-form-item>
                </el-col>
            </el-row>
            <div class="text-left">
                <el-button native-type="submit" type="primary">
                    {{ $t('common.actions.save') }}
                </el-button>
            </div>
        </el-form>
    </el-card>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useUser } from '@/composables/auth'
const t = useI18n();
const { user } = useUser();


const emit = defineEmits(['childinit']);
const props = defineProps({
  title: {
    type: String,
    default: '',
  },
});
// user sudah tersedia dari useUser composable

const form = ref({
    name : null,
    phone : null,
    email : null,
    image : null,
});

const errors = ref({});
const loading = ref(false);

const onSubmit = async () => {
    loading.value = true;
    const url = '/profile/update';
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
    emit('childinit', 'Edit Profile');
    form.value.name = user.name;
    form.value.phone = user.phone;
    form.value.email = user.email;
    form.value.image = user.image;
});

</script>


