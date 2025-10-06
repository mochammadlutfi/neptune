<script setup>
import { computed } from 'vue';

import { useSetting } from "@/composables/common/useSetting";
const appSetting = useSetting();
const app = computed(() => appSetting.app);
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-muted p-6 md:p-10">
    
        <div class="flex w-full max-w-sm flex-col gap-6">
            <a href="/" class="flex items-center gap-2 self-center font-medium">
                <img class="w-full block dark:hidden" src="/images/logo/logo.png" alt="">
                <!-- <img class="w-full hidden dark:block" src="app.logo_dark" alt=""> -->
            </a>
            <slot/>
        </div>
    
    </div>
</template>
