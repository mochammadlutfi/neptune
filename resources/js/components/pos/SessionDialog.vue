<template>
    <el-dialog
        v-model="dialogVisible"
        :title="$t('base.create_pos_session')"
        :width="500"
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        @close="onClose">
        
        <el-form 
            ref="formRef" 
            :model="form" 
            :rules="rules" 
            label-position="top"
            @submit.prevent="onSubmit">
            
            <el-form-item :label="$t('base.POS_profile')" prop="profile_id">
                <el-select 
                    v-model="form.profile_id" 
                    :placeholder="$t('base.select_profile')"
                    class="w-full"
                    :loading="profilesLoading">
                    <el-option
                        v-for="profile in profiles"
                        :key="profile.id"
                        :label="profile.name"
                        :value="profile.id">
                        <div class="flex justify-between">
                            <span>{{ profile.name }}</span>
                            <span class="text-gray-400 text-sm">{{ profile.warehouse?.name }}</span>
                        </div>
                    </el-option>
                </el-select>
            </el-form-item>

            <el-form-item :label="$t('common.date')" prop="date">
                <el-date-picker
                    v-model="form.date"
                    type="date"
                    :placeholder="$t('common.select_date')"
                    class="w-full"
                    format="DD/MM/YYYY"
                    value-format="YYYY-MM-DD" />
            </el-form-item>

            <el-form-item :label="$t('base.opening_cash')" prop="opening_cash">
                <el-input-number
                    v-model="form.opening_cash"
                    :min="0"
                    :precision="2"
                    class="w-full"
                    :placeholder="$t('base.enter_opening_cash')" />
            </el-form-item>

            <el-form-item :label="$t('common.note')" prop="note">
                <el-input
                    v-model="form.note"
                    type="textarea"
                    :rows="3"
                    :placeholder="$t('common.enter_note')" />
            </el-form-item>

            <div class="flex justify-end space-x-3">
                <el-button @click="onClose">
                    {{ $t('common.cancel') }}
                </el-button>
                <el-button 
                    type="primary" 
                    native-type="submit"
                    :loading="loading">
                    {{ $t('base.start_session') }}
                </el-button>
            </div>
        </el-form>
    </el-dialog>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import { ElMessage } from 'element-plus';
import { useI18n } from 'vue-i18n';
import dayjs from 'dayjs';

const { t } = useI18n();

// Props & Emits
const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue', 'session-created']);

// State
const dialogVisible = ref(false);
const formRef = ref();
const loading = ref(false);
const profilesLoading = ref(false);
const profiles = ref([]);

const form = ref({
    profile_id: null,
    date: dayjs().format('YYYY-MM-DD'),
    opening_cash: 0,
    note: ''
});

const rules = ref({
    profile_id: [
        { required: true, message: t('validation.required', { attribute: t('base.POS_profile') }), trigger: 'change' }
    ],
    date: [
        { required: true, message: t('validation.required', { attribute: t('common.date') }), trigger: 'change' }
    ],
    opening_cash: [
        { required: true, message: t('validation.required', { attribute: t('base.opening_cash') }), trigger: 'blur' }
    ]
});

// Methods
const loadProfiles = async () => {
    profilesLoading.value = true;
    try {
        const response = await axios.get('/pos/profile', {
            params: { limit: 100 }
        });
        profiles.value = response.data.data;
    } catch (error) {
        ElMessage.error(t('message.error_loading_profiles'));
    } finally {
        profilesLoading.value = false;
    }
};

const onSubmit = async () => {
    if (!formRef.value) return;
    
    formRef.value.validate(async (valid) => {
        if (valid) {
            loading.value = true;
            try {
                // Emit session data to parent (POSLayout) to handle with Pinia
                emit('session-created', form.value);
                onClose();
            } catch (error) {
                ElMessage.error(error.response?.data?.message || t('message.error_server'));
            } finally {
                loading.value = false;
            }
        } else {
            ElMessage.error(t('message.error_input'));
        }
    });
};

const onClose = () => {
    emit('update:modelValue', false);
    resetForm();
};

const resetForm = () => {
    form.value = {
        profile_id: null,
        date: dayjs().format('YYYY-MM-DD'),
        opening_cash: 0,
        note: ''
    };
    if (formRef.value) {
        formRef.value.resetFields();
    }
};

// Watch for prop changes
watch(() => props.modelValue, (val) => {
    dialogVisible.value = val;
    if (val) {
        loadProfiles();
    }
});

watch(dialogVisible, (val) => {
    emit('update:modelValue', val);
});

// Lifecycle
onMounted(() => {
    dialogVisible.value = props.modelValue;
});
</script>