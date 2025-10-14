<template>
    <el-select 
        v-model="value" 
        value-key="id" 
        class="w-100"
        filterable 
        clearable 
        remote
        :disabled="disabled"
        :readonly="readonly"
        :placeholder="placeholder"
        @change="selectChange"
        autocomplete="off"
        :loading="isLoading"
    >
        <el-option
            v-for="item in dataList"
            :key="item.id"
            :label="item.name"
            :value="item.id"
        >
            <span style="float: left">{{ item.name }}</span>
            <span style="float: right; color: #8492a6; font-size: 13px">
                {{ item.code }}
            </span>
        </el-option>
    </el-select>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import { readonly } from 'vue';

const emit = defineEmits(['update:modelValue', 'data'])

// Define props
const props = defineProps({
    modelValue: {
        type: [Number, String, Array],
    },
    placeholder: {
        type: String,
        default: 'Select Vessels'
    },
    status: {
        type: String,
        default: null // active, inactive, all
    },
    allowed_ids : {
        type: Array,
        default: null
    },
    disabled: {
        type: Boolean,
        default: false
    },
    readonly : {
        type : Boolean,
        default : false
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
        
        const response = await axios.get('/master/vessels', {
            params: {
                status: props.status,
                per_page: 100,
                allowed_ids: props.allowed_ids
            }
        });
        
        if (response.status === 200) {
            dataList.value = response.data.data || response.data;
        }
        isLoading.value = false;
    } catch (error) {
        console.error('Error fetching vessels:', error);
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