<template>
    <div class="content">
        <!-- Page Header -->
        <PageHeader :title="contract?.contract_name || $t('master.contracts.detail')">
            <template #subtitle>
                <span class="text-gray-600">{{ contract?.contract_number }}</span>
            </template>
            <template #actions>
                <div class="flex gap-3">
                    <el-button @click="$router.go(-1)">
                        <Icon icon="mingcute:arrow-left-line" class="mr-2" />
                        {{ $t('common.actions.back') }}
                    </el-button>
                    <el-button type="primary" @click="editContract" v-if="contract">
                        <Icon icon="mingcute:edit-line" class="mr-2" />
                        {{ $t('common.actions.edit') }}
                    </el-button>
                </div>
            </template>
        </PageHeader>

        <div v-if="contract" class="space-y-6">
            <!-- Status and Key Info Card -->
            <el-card class="!rounded-lg !shadow-md">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-primary/10 rounded-lg">
                            <Icon icon="mingcute:file-line" class="text-2xl text-primary" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ contract.contract_name }}</h2>
                            <p class="text-gray-600">{{ contract.contract_number }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <el-tag :type="getContractTypeColor(contract.contract_type)" size="large">
                            {{ contract.contract_type }}
                        </el-tag>
                        <el-tag :type="getStatusColor(contract.contract_status)" size="large">
                            {{ contract.contract_status }}
                        </el-tag>
                    </div>
                </div>
                
                <!-- Key Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ contract.days_to_expiry || 0 }}</div>
                        <div class="text-sm text-gray-600">{{ $t('master.contracts.days_to_expiry') }}</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ contract.working_area_km2 || 0 }} km²</div>
                        <div class="text-sm text-gray-600">{{ $t('master.contracts.fields.working_area_km2') }}</div>
                    </div>
                    <div class="text-center p-4 bg-orange-50 rounded-lg">
                        <div class="text-2xl font-bold text-orange-600">{{ formatCurrency(contract.minimum_expenditure_usd) }}</div>
                        <div class="text-sm text-gray-600">{{ $t('master.contracts.fields.minimum_expenditure_usd') }}</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">{{ contract.local_content_requirement_pct || 0 }}%</div>
                        <div class="text-sm text-gray-600">{{ $t('master.contracts.fields.local_content_requirement_pct') }}</div>
                    </div>
                </div>
            </el-card>

            <!-- Contract Information Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <el-card class="!rounded-lg !shadow-md">
                    <template #header>
                        <div class="flex items-center">
                            <Icon icon="mingcute:file-line" class="mr-2 text-primary" />
                            <span class="font-semibold">{{ $t('master.contracts.basic_information') }}</span>
                        </div>
                    </template>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600 font-medium">{{ $t('master.contracts.fields.contract_number') }}:</span>
                            <span class="font-semibold">{{ contract.contract_number }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600 font-medium">{{ $t('master.contracts.fields.contract_name') }}:</span>
                            <span class="font-semibold">{{ contract.contract_name }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600 font-medium">{{ $t('master.contracts.fields.contract_type') }}:</span>
                            <el-tag :type="getContractTypeColor(contract.contract_type)" size="small">
                                {{ contract.contract_type }}
                            </el-tag>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600 font-medium">{{ $t('master.contracts.fields.operator_name') }}:</span>
                            <span class="font-semibold">{{ contract.operator_name }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600 font-medium">{{ $t('master.contracts.fields.kkks_representative') }}:</span>
                            <span class="font-semibold">{{ contract.kkks_representative || '-' }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600 font-medium">{{ $t('master.contracts.fields.contract_status') }}:</span>
                            <el-tag :type="getStatusColor(contract.contract_status)" size="small">
                                {{ contract.contract_status }}
                            </el-tag>
                        </div>
                    </div>
                </el-card>

                <!-- Contract Dates -->
                <el-card class="!rounded-lg !shadow-md">
                    <template #header>
                        <div class="flex items-center">
                            <Icon icon="mingcute:calendar-line" class="mr-2 text-primary" />
                            <span class="font-semibold">{{ $t('master.contracts.contract_dates') }}</span>
                        </div>
                    </template>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600 font-medium">{{ $t('master.contracts.fields.effective_date') }}:</span>
                            <span class="font-semibold">{{ formatDate(contract.effective_date) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600 font-medium">{{ $t('master.contracts.fields.expiry_date') }}:</span>
                            <div>
                                <span class="font-semibold" :class="getExpiryDateClass(contract.expiry_date, contract.is_expiring_soon)">
                                    {{ formatDate(contract.expiry_date) }}
                                </span>
                                <el-tag v-if="contract.is_expiring_soon" type="warning" size="small" class="ml-2">
                                    {{ $t('master.contracts.expiring_soon') }}
                                </el-tag>
                            </div>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600 font-medium">{{ $t('master.contracts.fields.bond_expiry_date') }}:</span>
                            <span class="font-semibold">{{ formatDate(contract.bond_expiry_date) }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600 font-medium">{{ $t('master.contracts.days_to_expiry') }}:</span>
                            <span :class="contract.days_to_expiry < 180 ? 'text-orange-600 font-bold' : 'font-semibold'">
                                {{ contract.days_to_expiry }} {{ $t('common.time.days') }}
                            </span>
                        </div>
                    </div>
                </el-card>
            </div>

            <!-- Field Information -->
            <el-card class="!rounded-lg !shadow-md">
                <template #header>
                    <div class="flex items-center">
                        <Icon icon="mingcute:location-line" class="mr-2 text-primary" />
                        <span class="font-semibold">{{ $t('master.contracts.field_information') }}</span>
                    </div>
                </template>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="space-y-2">
                        <span class="text-gray-600 font-medium">{{ $t('master.contracts.fields.field_name') }}</span>
                        <div class="font-semibold text-lg">{{ contract.field_name || '-' }}</div>
                    </div>
                    <div class="space-y-2">
                        <span class="text-gray-600 font-medium">{{ $t('master.contracts.fields.block_name') }}</span>
                        <div class="font-semibold text-lg">{{ contract.block_name || '-' }}</div>
                    </div>
                    <div class="space-y-2">
                        <span class="text-gray-600 font-medium">{{ $t('master.contracts.fields.working_area_km2') }}</span>
                        <div class="font-semibold text-lg">{{ contract.working_area_km2 ? contract.working_area_km2 + ' km²' : '-' }}</div>
                    </div>
                    <div class="space-y-2">
                        <span class="text-gray-600 font-medium">{{ $t('master.contracts.fields.partner_companies') }}</span>
                        <div class="space-y-1">
                            <div v-if="contract.partner_companies && contract.partner_companies.length > 0">
                                <div v-for="(partner, index) in contract.partner_companies" :key="index" class="text-sm bg-gray-100 px-2 py-1 rounded">
                                    {{ partner }}
                                </div>
                            </div>
                            <div v-else class="text-gray-500 italic">{{ $t('master.contracts.no_partners') }}</div>
                        </div>
                    </div>
                </div>
            </el-card>

            <!-- Commercial Terms (only for PSC) -->
            <el-card v-if="contract.contract_type === 'PSC'" class="!rounded-lg !shadow-md">
                <template #header>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <Icon icon="mingcute:percentage-line" class="mr-2 text-primary" />
                            <span class="font-semibold">{{ $t('master.contracts.commercial_terms') }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <Icon 
                                :class="isCommercialTermsValid ? 'text-green-500' : 'text-orange-500'"
                                :icon="isCommercialTermsValid ? 'mingcute:check-circle-fill' : 'mingcute:alert-circle-fill'"
                            />
                            <span :class="isCommercialTermsValid ? 'text-green-600' : 'text-orange-600'" class="text-sm font-medium">
                                {{ isCommercialTermsValid ? $t('master.contracts.terms_valid') : $t('master.contracts.terms_invalid') }}
                            </span>
                        </div>
                    </div>
                </template>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- General Terms -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-800">{{ $t('master.contracts.general_terms') }}</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600 text-sm">{{ $t('master.contracts.fields.cost_recovery_limit_pct') }}:</span>
                                <span class="font-medium">{{ contract.cost_recovery_limit_pct || 0 }}%</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600 text-sm">{{ $t('master.contracts.fields.ftp_share_pct') }}:</span>
                                <span class="font-medium">{{ contract.ftp_share_pct || 0 }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Oil Shares -->
                    <div class="bg-blue-50 p-4 rounded-lg space-y-4">
                        <h4 class="font-medium text-blue-800 flex items-center">
                            <Icon icon="mingcute:drop-line" class="mr-2" />
                            {{ $t('master.contracts.oil_shares') }}
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-blue-200">
                                <span class="text-blue-700 text-sm">{{ $t('master.contracts.contractor_share') }}:</span>
                                <span class="font-medium">{{ contract.contractor_share_oil_pct || 0 }}%</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-blue-200">
                                <span class="text-blue-700 text-sm">{{ $t('master.contracts.government_share') }}:</span>
                                <span class="font-medium">{{ contract.government_share_oil_pct || 0 }}%</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-blue-700 text-sm font-medium">{{ $t('master.contracts.total') }}:</span>
                                <el-tag :type="oilSharesValid ? 'success' : 'danger'" size="small">
                                    {{ oilSharesTotal }}%
                                </el-tag>
                            </div>
                        </div>
                    </div>

                    <!-- Gas Shares -->
                    <div class="bg-green-50 p-4 rounded-lg space-y-4">
                        <h4 class="font-medium text-green-800 flex items-center">
                            <Icon icon="mingcute:fire-line" class="mr-2" />
                            {{ $t('master.contracts.gas_shares') }}
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-green-200">
                                <span class="text-green-700 text-sm">{{ $t('master.contracts.contractor_share') }}:</span>
                                <span class="font-medium">{{ contract.contractor_share_gas_pct || 0 }}%</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-green-200">
                                <span class="text-green-700 text-sm">{{ $t('master.contracts.government_share') }}:</span>
                                <span class="font-medium">{{ contract.government_share_gas_pct || 0 }}%</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-green-700 text-sm font-medium">{{ $t('master.contracts.total') }}:</span>
                                <el-tag :type="gasSharesValid ? 'success' : 'danger'" size="small">
                                    {{ gasSharesTotal }}%
                                </el-tag>
                            </div>
                        </div>
                    </div>
                </div>
            </el-card>

            <!-- Commitments & Financial -->
            <el-card class="!rounded-lg !shadow-md">
                <template #header>
                    <div class="flex items-center">
                        <Icon icon="mingcute:currency-dollar-line" class="mr-2 text-primary" />
                        <span class="font-semibold">{{ $t('master.contracts.commitments_financial') }}</span>
                    </div>
                </template>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Work Commitments -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-800">{{ $t('master.contracts.work_commitments') }}</h4>
                        <div class="space-y-4">
                            <div v-if="contract.minimum_work_commitment" class="p-4 bg-gray-50 rounded-lg">
                                <div class="text-sm text-gray-600 mb-1">{{ $t('master.contracts.fields.minimum_work_commitment') }}</div>
                                <div class="text-gray-800">{{ contract.minimum_work_commitment }}</div>
                            </div>
                            <div v-else class="text-gray-500 italic">{{ $t('master.contracts.no_work_commitment') }}</div>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-600 text-sm">{{ $t('master.contracts.fields.minimum_expenditure_usd') }}:</span>
                                    <span class="font-medium">{{ formatCurrency(contract.minimum_expenditure_usd) }}</span>
                                </div>
                                <div class="flex justify-between py-2">
                                    <span class="text-gray-600 text-sm">{{ $t('master.contracts.fields.local_content_requirement_pct') }}:</span>
                                    <span class="font-medium">{{ contract.local_content_requirement_pct || 0 }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Bond -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-800 flex items-center">
                            <Icon icon="mingcute:shield-check-line" class="mr-2 text-primary" />
                            {{ $t('master.contracts.performance_bond') }}
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600 text-sm">{{ $t('master.contracts.bond_amount') }}:</span>
                                <span class="font-medium">{{ formatCurrency(contract.performance_bond_amount_usd) }}</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600 text-sm">{{ $t('master.contracts.fields.bond_expiry_date') }}:</span>
                                <span class="font-medium">{{ formatDate(contract.bond_expiry_date) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </el-card>

            <!-- Extension Options -->
            <el-card v-if="contract.extension_options" class="!rounded-lg !shadow-md">
                <template #header>
                    <div class="flex items-center">
                        <Icon icon="mingcute:time-line" class="mr-2 text-primary" />
                        <span class="font-semibold">{{ $t('master.contracts.fields.extension_options') }}</span>
                    </div>
                </template>
                
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-800 whitespace-pre-wrap">{{ contract.extension_options }}</p>
                </div>
            </el-card>
        </div>

        <!-- Loading State -->
        <div v-else-if="loading" class="text-center p-12">
            <el-icon class="is-loading text-4xl text-primary mb-4">
                <!-- <Loading /> -->
            </el-icon>
            <p class="text-gray-600">{{ $t('common.messages.loading') }}</p>
        </div>

        <!-- Error State -->
        <div v-else class="text-center p-12">
            <Icon icon="mingcute:file-search-line" class="text-6xl text-gray-400 mb-4" />
            <p class="text-gray-600">{{ $t('common.messages.data_not_found') }}</p>
        </div>
    </div>
</template>

<script setup lang="js">
import { Icon } from '@iconify/vue';
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { ElMessage } from 'element-plus';
import PageHeader from '@/components/PageHeader.vue';

const route = useRoute();
const router = useRouter();
const { t } = useI18n();

// Component State
const loading = ref(false);
const contract = ref(null);

// Computed Properties
const isCommercialTermsValid = computed(() => {
    if (!contract.value) return false;
    return contract.value.oil_shares_total === 100 && contract.value.gas_shares_total === 100;
});

const oilSharesTotal = computed(() => {
    if (!contract.value) return 0;
    return (contract.value.contractor_share_oil_pct || 0) + (contract.value.government_share_oil_pct || 0);
});

const gasSharesTotal = computed(() => {
    if (!contract.value) return 0;
    return (contract.value.contractor_share_gas_pct || 0) + (contract.value.government_share_gas_pct || 0);
});

const oilSharesValid = computed(() => Math.abs(oilSharesTotal.value - 100) < 0.01);
const gasSharesValid = computed(() => Math.abs(gasSharesTotal.value - 100) < 0.01);

// Methods
const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatCurrency = (amount) => {
    if (!amount) return '-';
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
};

const getContractTypeColor = (type) => {
    const colors = {
        'PSC': 'primary',
        'Joint Venture': 'success',
        'Service Contract': 'info',
    };
    return colors[type] || 'default';
};

const getStatusColor = (status) => {
    const colors = {
        'Active': 'success',
        'Expired': 'info',
        'Terminated': 'danger',
        'Under Negotiation': 'warning',
    };
    return colors[status] || 'default';
};

const getExpiryDateClass = (date, isExpiring) => {
    if (isExpiring) return 'text-orange-600 font-bold';
    if (new Date(date) < new Date()) return 'text-red-600 font-bold';
    return 'text-gray-900';
};

const loadContract = async () => {
    try {
        loading.value = true;
        const response = await axios.get(`/master/contracts/${route.params.id}`);
        
        if (response.data.success) {
            contract.value = response.data.data;
        } else {
            throw new Error(response.data.message || 'Failed to load contract');
        }
    } catch (error) {
        console.error('Error loading contract:', error);
        ElMessage.error(t('common.errors.server_error'));
        router.push('/master/contracts');
    } finally {
        loading.value = false;
    }
};

const editContract = () => {
    router.push(`/master/contracts/${route.params.id}/edit`);
};

// Lifecycle
onMounted(() => {
    loadContract();
});
</script>

<style scoped>
/* Loading animation */
.is-loading {
    animation: rotating 2s linear infinite;
}

@keyframes rotating {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Card styling */
.el-card {
    border-radius: 12px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

.el-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Background colors for share sections */
.bg-blue-50 {
    background-color: #eff6ff;
}

.bg-green-50 {
    background-color: #f0fdf4;
}

.bg-orange-50 {
    background-color: #fff7ed;
}

.bg-purple-50 {
    background-color: #faf5ff;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .grid {
        grid-template-columns: 1fr;
    }
    
    .space-x-4 > * + * {
        margin-left: 0;
        margin-top: 1rem;
    }
    
    .space-x-3 > * + * {
        margin-left: 0;
        margin-top: 0.75rem;
    }
}
</style>