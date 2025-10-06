<template>
    <el-select v-model="selected" value-key="id"
    filterable 
    clearable 
    remote
    @change="selectChange"
    autocomplete="off"
    :loading="isLoading"
    :placeholder="placeholder">
        <el-option
            v-for="item in dataList"
            :key="item.id"
            :label="item.name"
            :value="item.id"
        />
    </el-select>
</template>

<script setup>
import { defineProps, ref, onMounted, defineEmits, watch } from 'vue';

const props = defineProps({
  modelValue: [String, Number],
  placeholder: {
    type: String,
    default: '',
  },
});


const emit = defineEmits(['update:modelValue']);

const dataList = ref([]);
const selected = ref(props.modelValue);
const isLoading = ref(false);

watch(() => props.modelValue, (newValue) => {
    selected.value = newValue;
});


const fetchData = async () => {
  try {
    isLoading.value = true;
    const response = await axios.get('/master/vessels');
    if (response.status === 200) {
      dataList.value = response.data;
    }
    isLoading.value = false;
  } catch (error) {
    console.error(error);
  }
};

const selectChange = (v) => {
  emit('update:modelValue', v);
};

onMounted(fetchData);
</script>