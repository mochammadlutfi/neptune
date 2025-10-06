<template>
    <el-select 
        v-model="value" 
        value-key="id" 
        class="w-100"
        filterable 
        clearable 
        remote
        :placeholder="placeholder"
        @change="selectChange"
        autocomplete="off"
        :loading="isLoading"
    >
        <el-option
            v-for="item in dataList"
            :key="item.id"
            :label="item.contract_name"
            :value="item.id"
        >
            <span style="float: left">{{ item.contract_name }}</span>
            <span style="float: right; color: #8492a6; font-size: 13px">
                {{ item.contract_number }}
            </span>
        </el-option>
    </el-select>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';

const emit = defineEmits(['update:modelValue', 'data'])

// Define props
const props = defineProps({
    modelValue: {
        type: [Number, String],
    },
    placeholder: {
        type: String,
        default: 'Select Contract'
    },
    status: {
        type: String,
        default: null // active, inactive, all
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
        
        const response = await axios.get('/master/contracts', {
            params: {
                status: props.status,
                per_page: 100 // Get more contracts for selection
            }
        });
        
        if (response.status === 200) {
            dataList.value = response.data.data || response.data;
        }
        isLoading.value = false;
    } catch (error) {
        console.error('Error fetching contracts:', error);
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchData();
});

// Emit value change
const selectChange = (newValue) => {
    value.value = newValue;
    emit('update:modelValue', newValue);
    
    // Emit selected contract data
    const selectedContract = dataList.value.find(item => item.id === newValue);
    if (selectedContract) {
        emit('data', selectedContract);
    }
};
</script>