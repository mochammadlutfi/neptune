<template>
    <el-tag :type="tagType">
        {{ $t(`common.${modelValue}`) }}
    </el-tag>
</template>

<script setup>
import { defineProps, computed } from 'vue';

const props = defineProps({
    modelValue : String,
});

const tagType = computed(() => {
    const typeMapping = {
        draft: 'warning',
        confirmed: 'info',
        unpaid : 'danger',
        paid : 'success',
        partial : 'info',
        partially : 'info',
        cancel : 'danger',
        delivered : 'success',
        sent : 'info',
        pending : 'warning',
        received : 'success',
    };

    // Default type is 'danger' if modelValue is not in the mapping
    return typeMapping[props.modelValue] || 'danger';
});
</script>