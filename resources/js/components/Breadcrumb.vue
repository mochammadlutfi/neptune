<template>
    <div class="flex items-center gap-2">
        <Link :href="to" class="text-sm font-semibold text-gray-800 dark:text-slate-400" v-if="to">
            <slot></slot>
        </Link>
        <span class="text-sm font-medium text-gray-800 dark:text-slate-400" v-else>
            <slot></slot>
        </span>
        <Icon icon="mingcute:right-line" v-if="separator"
        class="text-lg flex-shrink-0 text-slate-400 rtl:rotate-180"/>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';
import { Icon } from '@iconify/vue';
import { Link } from '@inertiajs/vue3'

defineProps({
    to: {
        type: String,
    },
    separator: {
        type: Boolean,
        default : false
    },
});

</script>