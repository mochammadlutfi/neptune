<template>
    <el-dialog
        v-model="dialogVisible"
        :title="$t('base.close_pos_session')"
        :width="600"
        :close-on-click-modal="false"
        @close="onClose">
        
        <div v-if="session" class="space-y-6">
            <!-- Session Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $t('base.session_information') }}</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">{{ $t('base.session_name') }}:</span>
                        <span class="ml-2 font-medium">{{ session.name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">{{ $t('common.date') }}:</span>
                        <span class="ml-2 font-medium">{{ formatDate(session.date) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">{{ $t('base.opening_time') }}:</span>
                        <span class="ml-2 font-medium">{{ session.opening_time }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">{{ $t('base.opening_cash') }}:</span>
                        <span class="ml-2 font-medium">{{ formatCurrency(session.opening_cash) }}</span>
                    </div>
                </div>
            </div>

            <!-- Session Statistics -->
            <div v-if="statistics" class="bg-blue-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">{{ $t('base.session_statistics') }}</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">{{ $t('base.total_sales') }}:</span>
                        <span class="ml-2 font-medium text-green-600">{{ formatCurrency(statistics.total_sales) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">{{ $t('base.total_orders') }}:</span>
                        <span class="ml-2 font-medium">{{ statistics.total_orders }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">{{ $t('base.total_refunds') }}:</span>
                        <span class="ml-2 font-medium text-red-600">{{ formatCurrency(statistics.total_refund) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">{{ $t('base.total_transactions') }}:</span>
                        <span class="ml-2 font-medium">{{ statistics.total_transactions }}</span>
                    </div>
                </div>
            </div>

            <!-- Closing Form -->
            <el-form 
                ref="formRef" 
                :model="form" 
                :rules="rules" 
                label-position="top"
                @submit.prevent="onSubmit">
                
                <el-form-item :label="$t('base.closing_cash')" prop="closing_cash">
                    <el-input-number
                        v-model="form.closing_cash"
                        :min="0"
                        :precision="2"
                        class="w-full"
                        :placeholder="$t('base.enter_closing_cash')" />
                    <div class="text-xs text-gray-500 mt-1">
                        {{ $t('base.count_physical_cash_drawer') }}
                    </div>
                </el-form-item>

                <!-- Cash Difference -->
                <div v-if="form.closing_cash !== null" class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">{{ $t('base.cash_difference') }}:</span>
                        <span 
                            :class="[
                                'text-sm font-bold',
                                cashDifference > 0 ? 'text-green-600' : cashDifference < 0 ? 'text-red-600' : 'text-gray-600'
                            ]">
                            {{ formatCurrency(cashDifference) }}
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        {{ $t('base.expected_cash') }}: {{ formatCurrency(expectedCash) }}
                    </div>
                </div>

                <el-form-item :label="$t('common.note')" prop="note">
                    <el-input
                        v-model="form.note"
                        type="textarea"
                        :rows="3"
                        :placeholder="$t('base.closing_note_placeholder')" />
                </el-form-item>

                <div class="flex justify-end space-x-3">
                    <el-button @click="onClose">
                        {{ $t('common.cancel') }}
                    </el-button>
                    <el-button 
                        type="primary" 
                        native-type="submit"
                        :loading="loading">
                        {{ $t('base.close_session') }}
                    </el-button>
                </div>
            </el-form>
        </div>
    </el-dialog>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import axios from 'axios';
import { ElMessage, ElMessageBox } from 'element-plus';
import { useI18n } from 'vue-i18n';
import { useFormatter } from '@/composables/useFormatter';

const { t } = useI18n();
const { formatCurrency, formatDate } = useFormatter();

// Props & Emits
const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    session: {
        type: Object,
        default: () => null
    }
});

const emit = defineEmits(['update:modelValue', 'session-closed']);

// State
const dialogVisible = ref(false);
const formRef = ref();
const loading = ref(false);
const statistics = ref(null);

const form = ref({
    closing_cash: null,
    note: ''
});

const rules = ref({
    closing_cash: [
        { required: true, message: t('validation.required', { attribute: t('base.closing_cash') }), trigger: 'blur' }
    ]
});

// Computed
const expectedCash = computed(() => {
    if (!props.session || !statistics.value) return 0;
    return props.session.opening_cash + statistics.value.total_sales - statistics.value.total_refund;
});

const cashDifference = computed(() => {
    if (form.value.closing_cash === null) return 0;
    return form.value.closing_cash - expectedCash.value;
});

// Methods
const loadSessionStatistics = async () => {
    if (!props.session) return;
    
    try {
        const response = await axios.get(`/pos/session/${props.session.id}`);
        statistics.value = response.data.result.statistics;
    } catch (error) {
        ElMessage.error(t('message.error_loading_statistics'));
    }
};

const onSubmit = async () => {
    if (!formRef.value) return;
    
    // Confirm if there's a significant cash difference
    if (Math.abs(cashDifference.value) > 10) {
        try {
            await ElMessageBox.confirm(
                t('base.cash_difference_warning', { amount: formatCurrency(Math.abs(cashDifference.value)) }),
                t('base.confirm_close_session'),
                {
                    confirmButtonText: t('common.yes'),
                    cancelButtonText: t('common.no'),
                    type: 'warning'
                }
            );
        } catch {
            return; // User cancelled
        }
    }
    
    formRef.value.validate(async (valid) => {
        if (valid) {
            loading.value = true;
            try {
                await axios.post(`/pos/session/${props.session.id}/close`, form.value);
                
                ElMessage.success(t('message.session_closed_successfully'));
                emit('session-closed');
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
        closing_cash: null,
        note: ''
    };
    statistics.value = null;
    if (formRef.value) {
        formRef.value.resetFields();
    }
};

// Watch for prop changes
watch(() => props.modelValue, (val) => {
    dialogVisible.value = val;
    if (val && props.session) {
        loadSessionStatistics();
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