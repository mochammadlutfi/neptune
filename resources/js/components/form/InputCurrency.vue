<template>
    <el-input 
    ref="inputRef"
    v-model="formattedValue"
    @input="onInput" class="w-full"/>
</template>

<script setup>
import { onMounted, computed , ref, watch } from 'vue';
import { useCurrencyInput } from 'vue-currency-input';
import { useAppBaseStore } from '@/stores/base';


const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    placeholder : {
        type : String,
        default : ''
    },
    max : {
        type : Number,
    }
});

const appBase = computed(() => useAppBaseStore());

const input = ref('');

// watch(
//   () => app,
//   (newCurrency, oldCurrency) => {
//     if(newCurrency){
//         options.value.currency = newCurrency.code;
//         setOptions(options.value);
//     }
//   }
// );
watch(
  () => props.modelValue,
  (value) => {
    setValue(value);
  }
);

const { inputRef, formattedValue, setValue } = useCurrencyInput({
  currency: appBase.value.app.currency.code,
  locale : appBase.value.locale,
  currencyDisplay: "symbol",
  hideCurrencySymbolOnFocus: false,
  hideGroupingSeparatorOnFocus: false,
  precision: 0,
  useGrouping : true,
  valueRange: { min: 0, max: props.max },
});

watch(() => props.modelValue, (newValue) => {
    input.value = newValue;
});

const onInput = () => {
    emit('update:modelValue', input.value);
}

onMounted(() => {
    // if(app.value){
    //     options.value.currency = app.value.currency.code;
    //     setOptions(options.value);
    // }
});
</script>