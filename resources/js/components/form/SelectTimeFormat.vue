<template>
    <el-select v-model="selected" value-key="format"
    filterable 
    clearable 
    remote
    @change="selectChange"
    autocomplete="off"
    :loading="isLoading"
    :placeholder="placeholder">
        <el-option
            v-for="(v,i) in dataList"
            :key="i"
            :label="$t('common.time.hours_select', {time : v.time, format : v.format})"
            :value="v.format"
        >
        <span class="float-left">{{ $t('common.time.hours_select', {time : v.time, format : v.format}) }}</span>
        <span
            style="
            float: right;
            color: var(--el-text-color-secondary);
            font-size: 13px;
            "
        >
            {{ dateFormat(v.format) }}
        </span>
        </el-option>
    </el-select>
</template>

<script setup>
import { defineProps, ref, defineEmits, watch } from 'vue';
import dayjs from 'dayjs';
 
const props = defineProps({
  modelValue: String,
  placeholder: {
    type: String,
    default: '',
  },
});


const emit = defineEmits(['update:modelValue']);

const dataList = ref([
    {
        time : 12,
        format : 'hh:mm A'
    },
    {
        time : 12,
        format : 'hh:mm a'
    },
    {
        time : 12,
        format : 'hh:mm:ss A'
    },
    {
        time : 12,
        format : 'hh:mm::ss a'
    },
    {
        time : 24,
        format : 'HH:mm'
    },
    {
        time : 24,
        format : 'HH:mm:ss'
    },
]);
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