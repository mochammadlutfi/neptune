<template>
    <el-dialog 
        v-model="dialogVisible" 
        id="modalForm"
        :title="dialogTitle"
        :close-on-click-modal="false" 
        :close-on-press-escape="false"
        class="md:w-[650px]">
        <el-form
            ref="formRef"
            :model="form"
            :rules="formRules"
            label-position="top"
            @submit.prevent="onSubmit"
            v-loading="isLoading">
            <el-row :gutter="16">
                <el-col :md="12">
                    <el-form-item :label="$t('common.date')" prop="date">
                        <input-date v-model="form.date" today/>
                    </el-form-item>
                    <el-form-item :label="$t('transaction.amount')" prop="amount">
                        <input-currency v-model="form.amount"/>
                    </el-form-item>
                </el-col>
                <el-col :md="12">
                    <el-form-item :label="$t('base.payment_method')" prop="payment_method_id">
                        <select-payment-method v-model="form.payment_method_id"/>
                    </el-form-item>
                    <el-form-item :label="$t('transaction.payment_ref')">
                        <el-input type="text" v-model="form.ref"/>
                    </el-form-item>
                </el-col>
            </el-row>
            <div class="text-end">
                <el-button @click.prevent="onCancel">
                    {{ $t('common.cancel') }}
                </el-button>
                <el-button type="primary" native-type="submit">
                    {{ $t('common.save') }}
                </el-button>
            </div>
        </el-form>
    </el-dialog>
</template>

<script setup="setup">
    import { ref } from 'vue';
    import InputDate from '@/components/Form/InputDate.vue';
    import InputCurrency from '@/components/Form/InputCurrency.vue';
    import { ElMessageBox, ElMessage, ElLoading } from 'element-plus';
    import SelectPaymentMethod from '@/components/Form/SelectPaymentMethod.vue';
    import { useI18n } from 'vue-i18n';

    const { t } = useI18n();

    const emit = defineEmits(['success']);
    
    const props = defineProps({
        data : {
            type : Object,
            default : null
        },
        amount : {
            type : Number,
            default : 0
        }
    });

    const dialogVisible = ref(false);
    const isLoading = ref(false);

    const formRef = ref(null);
    const form = ref({
        type : '',
        payable_id : props.data.id,
        date: null,
        payment_method_id : null,
        order_id : null,
        amount : props.data.amount_due,
        ref : null
    });
    
    const formRules = ref({
        date: [
            { required: true, message: t('validation.required', { attribute: t('common.date') }), trigger: 'blur' },
        ],
        payment_method_id: [
            { required: true, message: t('validation.required', { attribute: t('base.payment_method') }), trigger: 'blur' },
        ],
        amount: [
            { required: true, message: t('validation.required', { attribute: t('transaction.amount') }), trigger: 'change' },
        ],
    });

    // State untuk judul dialog
    const dialogTitle = ref('Form Pembayaran');

    // Method untuk membuka dialog
    const open = (type) => {
        form.value.type = type;
        dialogTitle.value = type === 'invoice'
            ? 'Pembayaran Invoice'
            : 'Pembayaran Vendor Bill';
        dialogVisible.value = true;
    };

    // Method untuk menutup dialog dan mereset form
    const onCancel = () => {
        dialogVisible.value = false;
    };

    // Method untuk handle submit form
    const onSubmit = async () => {
        if (!formRef.value) return;
        formRef.value.validate(async (valid) => {
            if (valid) {
                const loading = ElLoading.service({
                    customClass: 'rounded-md',
                    target: document.querySelector('#modalForm')
                });
                try {
                    const response = await axios.post('/accounting/payment/store', form.value);
                    emit('success');
                    ElMessage({
                        message: t('message.success_save'),
                        type: 'success',
                    });
                } catch (error) {
                    ElMessage({
                        message: t('message.error_server'),
                        type: 'error',
                    });
                }
                loading.close();
                dialogVisible.value = false;
            } else {
                ElMessage({
                    message: t('message.error_input'),
                    type: 'error',
                });
            }
        });
    };

    defineExpose({
        open,
    });
</script>