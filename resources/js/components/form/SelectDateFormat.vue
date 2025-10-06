<template>
    <el-select v-model="selected" 
    value-key="id"
    filterable 
    clearable 
    remote
    @change="selectChange"
    autocomplete="off"
    :loading="isLoading"
    :placeholder="placeholder">
        <el-option
            v-for="(format, key) in dataList"
            :key="key"
            :label="format"
            :value="key"
        >
        <span class="float-left">{{ format }}</span>
        <span
            style="
            float: right;
            color: var(--el-text-color-secondary);
            font-size: 13px;
            "
        >
            {{ dateFormat(format) }}
        </span>
        </el-option>
    </el-select>
</template>

<script setup>
import { defineProps, ref, defineEmits, watch } from 'vue';
import dayjs from 'dayjs';

const props = defineProps({
  modelValue: [String, Number],
  placeholder: {
    type: String,
    default: '',
  },
});


const emit = defineEmits(['update:modelValue']);

const dataList = ref({
    'd-m-Y' : 'DD-MM-YYYY',
    'm-d-Y' : 'MM-DD-YYYY',
    'Y-m-d' : 'YYYY-MM-DD',
    'd.m.Y' : 'DD.MM.YYYY',
    'm.d.Y' : 'MM.DD.YYYY',
    'Y.m.d' : 'YYYY.MM.DD',
    'd/m/Y' : 'DD/MM/YYYY',
    'm/d/Y' : 'MM/DD/YYYY',
    'Y/m/d' : 'YYYY/MM/DD',
    'd/M/Y' : 'DD/MMM/YYYY',
    'd.M.Y' : 'DD.MMM.YYYY',
    'd-M-Y' : 'DD-MMM-YYYY',
    'd M Y' : 'DD MMM YYYY',
    'd F, Y' : 'DD MMMM, YYYY',
    'D/M/Y' : 'ddd/MMM/YYYY',
    'D.M.Y' : 'ddd.MMM.YYYY',
    'D-M-Y' : 'ddd-MMM-YYYY',
    'D M Y' : 'ddd MMM YYYY',
    'd D M Y' : 'DD ddd MMM YYYY',
    'D d M Y' : 'ddd DD MMM YYYY',
    'dS M Y' : 'Do MMM YYYY',
});
const selected = ref(props.modelValue);
const isLoading = ref(false);

const dateFormat = (format) =>{
    return dayjs().format(format);
}

watch(() => props.modelValue, (newValue) => {
    selected.value = newValue;
});

const selectChange = (v) => {
  emit('update:modelValue', v);
};
</script>