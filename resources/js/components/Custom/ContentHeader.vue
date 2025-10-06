<template>
    
    <div class="content-header border-bottom mb-2">
      <div class="mt-auto mb-0">
        <div class="text-lg font-semibold">{{ $t(title,2) }}</div>
      </div>
      <div class="mt-auto mb-0 flex">
        <slot></slot>
      </div>
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
const route = useRoute()

const title = route.meta.title || '';
</script>