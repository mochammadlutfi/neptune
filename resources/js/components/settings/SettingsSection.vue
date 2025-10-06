<template>
    <div class="space-y-6">
        <div v-if="title || description" class="pb-4 border-b border-gray-200">
            <h2 v-if="title" class="text-xl font-semibold text-gray-900">{{ title }}</h2>
            <p v-if="description" class="text-gray-600 mt-2">{{ description }}</p>
        </div>
        
        <div class="grid gap-6" :class="gridClass">
            <slot />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: String,
    description: String,
    columns: {
        type: Number,
        default: 1
    }
});

const gridClass = computed(() => {
    const cols = {
        1: 'grid-cols-1',
        2: 'grid-cols-1 lg:grid-cols-2',
        3: 'grid-cols-1 md:grid-cols-2 xl:grid-cols-3',
        4: 'grid-cols-1 md:grid-cols-2 xl:grid-cols-4'
    };
    return cols[props.columns] || 'grid-cols-1';
});
</script>