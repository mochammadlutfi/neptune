<template>
    <el-select v-model="value"
    value-key="code" 
    class="w-100"
    filterable 
    clearable 
    remote
    @change="selectChange"
    autocomplete="off"
    :loading="isLoading">
        <el-option
            v-for="item in dataList"
            :key="item.code"
            :label="item.name"
            :value="item.code"
        />
    </el-select>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';

const emit = defineEmits(['update:modelValue'])
// Define props
const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    }
});

// Define data
const dataList = ref([]);
const value = ref(props.modelValue);
const isLoading = ref(false);

// Watch for changes to modelValue
watch(() => props.modelValue, (newValue) => {
    value.value = newValue;
});

// Fetch data on mounted
const fetchData = async () => {
    try {
        isLoading.value = true;
        const response = await axios.get("/settings/language");
        if (response.status === 200) {
            dataList.value = response.data;
        }
        isLoading.value = false;
    } catch (error) {
    }
};

onMounted(() => {
    fetchData();
});

// Emit value change
const selectChange = (newValue) => {
    value.value = newValue;
    emit('update:modelValue', newValue);
};
</script>