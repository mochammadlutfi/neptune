<template>
    <div>
        <div class="text-sm font-normal text-zinc-400">
            {{ label }}
        </div>
        <div class="text-sm font-semibold text-gray-900">
            <slot></slot>
        </div>
    </div>    
</template>

<script setup>
import { defineProps } from 'vue';

defineProps({
    label : String
});

</script>