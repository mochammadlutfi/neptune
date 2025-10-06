<template>
    <ol class="stepbar">
      <li class="step" v-for="(d, i) in data" :key="i" :class="{'is-current' : d.value == modelValue}">
        {{ d.label }}
      </li>
    </ol>
</template>
  
  
<script setup>
    import { ref, defineProps } from "vue";

    const props = defineProps({
        data : Array,
        modelValue : String,
    });
  
  // Daftar langkah
  const steps = [
    { label: "RFQ" },
    { label: "RFQ Sent" },
    { label: "Purchase Order" },
  ];
  
  // Langkah aktif saat ini (index)
  const currentStep = ref(2); // Contoh: Langkah ketiga
  </script>
  