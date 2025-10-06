<template>
    <el-date-picker class="!w-full" v-model="inputData" :type="type" @change="onChangeDate"
        :format="dateFormat" value-format="YYYY-MM-DD h:m" />
</template>

<script setup>
import { onMounted, ref, watch, computed } from 'vue';
import { useAppBaseStore } from "@/stores/base";
import dayjs from 'dayjs';

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: {
        type: [Object, String],
        default: null
    },
    placeholder : {
        type : String,
        default : ''
    },
    type : {
        type : String,
        default : "datetime"
    },
    today : {
        type : Boolean,
        default :false
    }
});
const appBase = useAppBaseStore();

const inputData = ref(props.modelValue);

const dateFormat = computed(() => {
    
    const dateFormat = appBase.app.date_format || 'YYYY-MM-DD'; 
    const timeFormat = appBase.app.time_format || 'HH:mm:ss';
        
    const formatString = props.type == 'datetime' ? `${dateFormat} ${timeFormat}` : dateFormat;

    return formatString;
});

watch(() => props.modelValue, (newValue) => {
    inputData.value = new dayjs(newValue).format();
});

const onChangeDate = (value) => {
    emit('update:modelValue', value);
}

onMounted(() => {
    if(props.today){
        inputData.value = new dayjs().format();
        emit('update:modelValue', inputData.value);
    }
    if (typeof props.modelValue === 'string') {
        inputData.value = new dayjs(props.modelValue).format();
    }
});
</script>