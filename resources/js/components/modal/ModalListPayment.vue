<template>
    <el-dialog 
        v-model="dialogVisible" 
        id="modalForm"
        :title="dialogTitle"
        :close-on-click-modal="false" 
        :close-on-press-escape="false"
        class="md:w-[650px]">
        
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
    
    const props = defineProps({
        data : {
            type : Object,
            default : null
        }
    });

    const dialogVisible = ref(false);
    const isLoading = ref(false);
    const type = ref('');
    const formRef = ref(null);
    const form = ref({
        type : '',
        payable_id : props.data.id,
        date: null,
        payment_method_id : null,
        order_id : null,
        amount : props.data.amount_unpaid,
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
        type.value = type;
        dialogVisible.value = true;
    };

    // Method untuk menutup dialog dan mereset form
    const onCancel = () => {
        dialogVisible.value = false;
    };

    defineExpose({
        open,
    });
</script>