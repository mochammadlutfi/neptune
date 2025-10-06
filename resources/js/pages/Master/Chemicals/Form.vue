<template>
    <div class="content">
        <div class="content-header">
            <div class="mt-auto mb-0">
                <div class="text-lg font-semibold">{{ $t(title, 2) }}</div>
            </div>
            <div class="mt-auto mb-0">

            </div>
        </div>
    </div>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'


const route = useRoute()

const title = route.meta.title || '';

</script>