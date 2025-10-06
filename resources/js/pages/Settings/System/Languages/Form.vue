<template>
    <el-card class="shadow-sm sm:rounded-lg">
        <form>
            <h1>Email</h1>
        </form>
    </el-card>
</template>

<script setup>
import { defineProps, onMounted, defineEmits } from 'vue';
import { useI18n } from 'vue-i18n';
const t = useI18n();

const emit = defineEmits(['childinit']);

const props = defineProps({
  title: {
    type: String,
    default: '',
  },
});

onMounted(() => {
  emit('childinit', 'email_setting');
});
</script>