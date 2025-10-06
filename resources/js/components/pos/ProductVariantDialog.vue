<template>
    <el-dialog
        v-model="dialogVisible"
        :title="$t('product.select_variant')"
        :width="500"
        @close="onClose">
        
        <div v-if="product" class="space-y-4">
            <!-- Product Info -->
            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                    <img 
                        v-if="product.image" 
                        :src="product.image" 
                        :alt="product.name"
                        class="w-full h-full object-cover rounded-lg" />
                    <Icon 
                        v-else 
                        icon="mingcute:pic-line" 
                        class="text-2xl text-gray-400" />
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">{{ product.name }}</h3>
                    <p class="text-sm text-gray-500">{{ $t('product.select_variant_to_add') }}</p>
                </div>
            </div>

            <!-- Variants List -->
            <div class="space-y-2 max-h-64 overflow-y-auto">
                <div 
                    v-for="variant in product.variant" 
                    :key="variant.id"
                    class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
                    @click="selectVariant(variant)">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <span class="font-medium text-gray-900">
                                {{ variant.name || $t('product.default_variant') }}
                            </span>
                            <el-tag v-if="variant.sku" size="small" type="info">
                                {{ variant.sku }}
                            </el-tag>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ formatVariantDetails(variant) }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-blue-600">
                            {{ formatCurrency(variant.price) }}
                        </div>
                        <div v-if="getStock(variant.id) !== null" class="text-xs text-gray-500">
                            {{ $t('product.stock') }}: {{ getStock(variant.id) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="product.variants?.length === 0" class="text-center py-8">
                <Icon icon="mingcute:box-3-line" class="text-4xl text-gray-300 mb-2" />
                <p class="text-gray-500">{{ $t('product.no_variants_available') }}</p>
            </div>
        </div>

        <template #footer>
            <div class="text-right">
                <el-button @click="onClose">
                    {{ $t('common.cancel') }}
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { Icon } from '@iconify/vue';
import { useI18n } from 'vue-i18n';
import { useFormatter } from '@/composables/useFormatter';

const { t } = useI18n();
const { formatCurrency } = useFormatter();

// Props & Emits
const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    product: {
        type: Object,
        default: () => null
    }
});

const emit = defineEmits(['update:modelValue', 'variant-selected']);

// State
const dialogVisible = ref(false);

// Methods
const selectVariant = (variant) => {
    // Add product reference to variant for easier access
    const variantWithProduct = {
        ...variant,
        product: props.product
    };
    
    emit('variant-selected', variantWithProduct);
    onClose();
};

const onClose = () => {
    emit('update:modelValue', false);
};

const formatVariantDetails = (variant) => {
    const details = [];
    
    if (variant.var1 && props.product.var1_name) {
        details.push(`${props.product.var1_name}: ${variant.var1}`);
    }
    
    if (variant.var2 && props.product.var2_name) {
        details.push(`${props.product.var2_name}: ${variant.var2}`);
    }
    
    return details.length > 0 ? details.join(', ') : t('product.standard_variant');
};

const getStock = (variantId) => {
    // This would typically fetch from stock data
    // For now, return null to hide stock info
    return null;
};

// Watch for prop changes
watch(() => props.modelValue, (val) => {
    dialogVisible.value = val;
});

watch(dialogVisible, (val) => {
    emit('update:modelValue', val);
});
</script>