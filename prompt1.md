# 🎯 **MASTER PROMPT TEMPLATE: COMPLETE MODULE UI/UX CONSISTENCY**

---

## **📋 TEMPLATE USAGE FORMAT:**

```
Buatkan desain UI/UX yang konsisten untuk modul Product Attribute dengan 3 halaman utama (Index, Form, Show) menggunakan pattern yang sudah ada di @resources\js\Pages\Settings\Outlet sebagai referensi ideal:

REFERENSI PATTERN:
- Index Pattern: @resources\js\Pages\Settings\Outlet\Index.vue  
- Form Pattern: @resources\js\Pages\Settings\Outlet\Form.vue
- Show Pattern: @resources\js\Pages\Settings\Outlet\Show.vue
- Translation Pattern: @resources\js\lang\en\settings.json (bagian outlet)

TARGET FILES:
- @resources\js\Pages\Product\Attribute\Index.vue
- @resources\js\Pages\Product\Attribute\Form.vue  
- @resources\js\Pages\Product\Attribute\Show.vue

BACKEND REFERENCES:
- Model: @app\Models\Product\ProductAttribute.php , @app\Models\Product\ProductAttributeLine.php, @app\Models\Product\ProductAttributeValue.php
- Controller: @app\Http\Controllers\Product\AttributeController.php
- Database: @dev_lovaerp.sql (untuk table structure)
- Translation: @resources\js\lang\en\products.json

REQUIREMENTS: [lihat detail di bawah]
```

---

## **📄 1. INDEX PAGE PATTERN**

### **Template Structure:**
```vue
<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader
            :title="$t('module.entity.title')"
            :description="$t('module.entity.subtitle')"
            :primary-action="{
                label: $t('module.entity.add'),
                icon: 'mingcute:add-line',
                click: () => router.push('/module/entity/create')
            }"
            :dropdown-actions="[
                {
                    key: 'export-all',
                    label: $t('common.actions.export'),
                    icon: 'mingcute:download-line',
                    click: () => ElMessage.info($t('common.messages.feature_unavailable'))
                },
                {
                    key: 'import',
                    label: $t('common.actions.import'),
                    icon: 'mingcute:upload-line',
                    click: () => ElMessage.info($t('common.messages.feature_unavailable'))
                }
            ]"
        />

        <!-- Statistics Cards (Optional) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6" v-if="showStats">
            <StatCard
                v-for="stat in statistics"
                :key="stat.key"
                :title="stat.title"
                :value="stat.value"
                :icon="stat.icon"
                :color="stat.color"
                :loading="statsLoading"
            />
        </div>

        <!-- Main Content Card -->
        <el-card body-class="!p-0" class="!rounded-lg !shadow-md">
            <!-- Advanced Filter Section -->
            <FilterSection
                v-model:search="params.q"
                :search-placeholder="$t('module.entity.search')"
                :loading="isLoading"
                :has-advanced-filters="true"
                :active-filters="activeFilters"
                @apply-filters="applyAdvancedFilters"
                @clear-filters="clearAllFilters"
                @remove-filter="removeFilter"
            >
                <template #advanced-filters>
                    <!-- Advanced filter controls -->
                </template>
            </FilterSection>

            <!-- Data Table -->
            <SkeletonTable v-if="isLoading" />
            <el-table v-else :data="data?.data" class="w-full">
                <!-- Table columns sesuai entity -->
                <el-table-column :label="$t('module.entity.fields.name')" min-width="200">
                    <template #default="scope">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center mr-3">
                                <Icon icon="mingcute:entity-icon" class="text-blue-600" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-gray-900">{{ scope.row.name }}</div>
                                <div class="text-sm text-gray-500 truncate">{{ scope.row.code || '-' }}</div>
                            </div>
                        </div>
                    </template>
                </el-table-column>

                <!-- Status Column -->
                <el-table-column :label="$t('module.entity.fields.status')" width="100" align="center">
                    <template #default="scope">
                        <el-tag :type="scope.row.is_active ? 'success' : 'info'" size="small" effect="light">
                            {{ scope.row.is_active ? $t('common.status.active') : $t('common.status.inactive') }}
                        </el-tag>
                    </template>
                </el-table-column>

                <!-- Action Column -->
                <el-table-column :label="$t('common.actions.action', 2)" align="center" width="120" fixed="right">
                    <template #default="scope">
                        <el-dropdown popper-class="dropdown-action" placement="bottom-end" trigger="click">
                            <el-button circle class="!p-0 !m-0">
                                <Icon icon="mingcute:more-2-fill"/>
                            </el-button>
                            <template #dropdown>
                                <el-dropdown-menu>
                                    <el-dropdown-item @click="onView(scope.row)">
                                        <Icon icon="mingcute:eye-line" class="me-2" />
                                        {{ $t('common.actions.view') }}
                                    </el-dropdown-item>
                                    <el-dropdown-item @click="onEdit(scope.row)">
                                        <Icon icon="mingcute:edit-line" class="me-2" />
                                        {{ $t('common.actions.edit') }}
                                    </el-dropdown-item>
                                    <el-dropdown-item divided class="text-red-600" @click="onDelete(scope.row.id)">
                                        <Icon icon="mingcute:delete-2-line" class="me-2" />
                                        {{ $t('common.actions.delete') }}
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </template>
                        </el-dropdown>
                    </template>
                </el-table-column>
            </el-table>

            <!-- Pagination -->
            <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-t border-gray-100 gap-4">
                <div class="flex">
                    <el-select v-model="params.limit" class="w-20" @change="changePage(1)">
                        <el-option label="25" value="25"/>
                        <el-option label="50" value="50"/>
                        <el-option label="100" value="100"/>
                    </el-select>
                </div>
                <div class="flex align-middle gap-4 items-center">
                    <div class="text-sm text-gray-600 my-auto">
                        {{ $t("common.pagination.showing_results", {
                            from: data?.from || 0,
                            to: data?.to || 0,
                            total: data?.total || 0
                        }) }}
                    </div>
                    <el-pagination
                        v-if="data?.total > 0"
                        background
                        layout="prev, pager, next"
                        :page-size="data.per_page"
                        :total="data.total"
                        :current-page="params.page"
                        @current-change="changePage"
                    />
                </div>
            </div>
        </el-card>
    </div>
</template>
```

---

## **📝 2. FORM PAGE PATTERN**

### **Template Structure:**
```vue
<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader
            :title="isEdit ? $t('module.entity.edit') : $t('module.entity.add')"
            :description="isEdit ? $t('module.entity.edit_subtitle') : $t('module.entity.add_subtitle')"
            :back-action="{
                label: $t('common.actions.back'),
                click: goBack
            }"
        />

        <!-- Main Form Card -->
        <el-card body-class="!p-8" class="!rounded-lg !shadow-md">
            <template #header>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                        <Icon icon="mingcute:entity-icon" class="text-2xl text-blue-600" />
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">
                            {{ isEdit ? $t('module.entity.edit_form') : $t('module.entity.create_form') }}
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $t('module.entity.form_description') }}
                        </p>
                    </div>
                </div>
            </template>

            <el-form
                ref="formRef"
                :model="form"
                :rules="formRules"
                label-position="top"
                class="space-y-6"
                @submit.prevent="onSubmit"
            >
                <!-- Sectioned Form Fields -->
                <div class="border-b border-gray-100 pb-6">
                    <h4 class="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                        <Icon icon="mingcute:information-line" class="text-blue-600" />
                        {{ $t('module.entity.sections.basic_info') }}
                    </h4>
                    
                    <el-row :gutter="24">
                        <!-- Form fields sesuai model -->
                    </el-row>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-4 pt-6 border-t border-gray-100">
                    <el-button @click="goBack" :disabled="loading" class="px-8">
                        <Icon icon="mingcute:close-line" class="mr-2" />
                        {{ $t('common.actions.cancel') }}
                    </el-button>
                    <el-button type="primary" native-type="submit" :loading="loading" class="px-8">
                        <Icon icon="mingcute:check-line" class="mr-2" v-if="!loading" />
                        {{ loading ? $t('common.messages.saving') : (isEdit ? $t('common.actions.update') : $t('common.actions.create')) }}
                    </el-button>
                </div>
            </el-form>
        </el-card>
    </div>
</template>
```

---

## **👁️ 3. SHOW PAGE PATTERN**

### **Template Structure:**
```vue
<template>
    <div class="content">
        <!-- Modern Page Header -->
        <PageHeader
            :title="data?.name || $t('module.entity.detail')"
            :description="$t('module.entity.detail_subtitle')"
            :back-action="{
                label: $t('common.actions.back'),
                click: () => router.push('/module/entity')
            }"
            :primary-action="{
                label: $t('common.actions.edit'),
                icon: 'mingcute:edit-line',
                click: () => router.push(`/module/entity/${route.params.id}/edit`)
            }"
            :dropdown-actions="[
                {
                    key: 'duplicate',
                    label: $t('common.actions.duplicate'),
                    icon: 'mingcute:copy-line',
                    click: onDuplicate
                },
                {
                    key: 'delete',
                    label: $t('common.actions.delete'),
                    icon: 'mingcute:delete-2-line',
                    click: onDelete,
                    class: 'text-red-600'
                }
            ]"
        />

        <!-- Loading State -->
        <el-skeleton v-if="loading" :loading="true" animated>
            <template #template>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <el-skeleton-item variant="rect" style="height: 400px" />
                    </div>
                    <div>
                        <el-skeleton-item variant="rect" style="height: 200px" />
                    </div>
                </div>
            </template>
        </el-skeleton>

        <!-- Main Content -->
        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info Card -->
            <div class="lg:col-span-2">
                <el-card body-class="!p-6" class="!rounded-lg !shadow-md">
                    <template #header>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center">
                                <Icon icon="mingcute:entity-icon" class="text-2xl text-blue-600" />
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">{{ data?.name }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ data?.code || '-' }}</p>
                            </div>
                        </div>
                    </template>

                    <!-- Detail Information -->
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-4">{{ $t('module.entity.sections.basic_info') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <DetailItem :label="$t('module.entity.fields.field1')" :value="data?.field1" />
                                <DetailItem :label="$t('module.entity.fields.field2')" :value="data?.field2" />
                            </div>
                        </div>
                    </div>
                </el-card>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-6">
                <!-- Status Card -->
                <el-card body-class="!p-4" class="!rounded-lg !shadow-md">
                    <template #header>
                        <h4 class="font-medium text-gray-900">{{ $t('module.entity.sections.status') }}</h4>
                    </template>
                    <div class="space-y-3">
                        <DetailItem 
                            :label="$t('module.entity.fields.status')" 
                            :value="data?.is_active ? $t('common.status.active') : $t('common.status.inactive')"
                            :badge="{ type: data?.is_active ? 'success' : 'info' }"
                        />
                        <DetailItem :label="$t('common.fields.created_at')" :value="formatDate(data?.created_at)" />
                        <DetailItem :label="$t('common.fields.updated_at')" :value="formatDate(data?.updated_at)" />
                    </div>
                </el-card>
            </div>
        </div>
    </div>
</template>
```

---

## **🔧 4. SCRIPT SETUP PATTERNS**

### **Index Script:**
```vue
<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useQuery } from '@tanstack/vue-query'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Icon } from '@iconify/vue'
import axios from 'axios'

// Components
import PageHeader from '@/Components/PageHeader.vue'
import FilterSection from '@/Components/FilterSection.vue'
import SkeletonTable from '@/Components/SkeletonTable.vue'
import StatCard from '@/Components/StatCard.vue'

const { t } = useI18n()
const router = useRouter()

// State
const params = ref({
    page: 1,
    limit: 25,
    q: '',
    sort: 'created_at',
    sortDir: 'desc'
})

// Query
const { data, isLoading, error, refetch } = useQuery({
    queryKey: ['entities', params],
    queryFn: () => axios.get('/api/entities', { params: params.value }).then(res => res.data)
})

// Methods
const onView = (item) => router.push(`/module/entity/${item.id}`)
const onEdit = (item) => router.push(`/module/entity/${item.id}/edit`)
const onDelete = async (id) => {
    // Delete implementation
}

// Watchers
watch(() => params.value.q, () => {
    params.value.page = 1
    refetch()
}, { debounce: 300 })
</script>
```

### **Form Script:**
```vue
<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Icon } from '@iconify/vue'
import axios from 'axios'

// Components
import PageHeader from '@/Components/PageHeader.vue'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()

// State
const formRef = ref(null)
const loading = ref(false)
const isEdit = computed(() => !!route.params.id)

const form = ref({
    // fields sesuai model
})

const formRules = ref({
    // validation rules
})

// Methods
const goBack = () => {
    if (hasUnsavedChanges()) {
        ElMessageBox.confirm(/* ... */).then(() => router.push('/module/entity'))
    } else {
        router.push('/module/entity')
    }
}

const onSubmit = async () => {
    // Submit implementation
}

onMounted(() => {
    if (isEdit.value) fetchEntityData()
})
</script>
```

### **Show Script:**
```vue
<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useQuery } from '@tanstack/vue-query'
import { ElMessage } from 'element-plus'
import { Icon } from '@iconify/vue'

// Components
import PageHeader from '@/Components/PageHeader.vue'
import DetailItem from '@/Components/DetailItem.vue'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()

// Query
const { data, isLoading: loading } = useQuery({
    queryKey: ['entity', route.params.id],
    queryFn: () => axios.get(`/api/entities/${route.params.id}`).then(res => res.data)
})

// Methods
const onDuplicate = () => {
    router.push(`/module/entity/create?duplicate=${route.params.id}`)
}

const onDelete = async () => {
    // Delete implementation
}
</script>
```

---

## **🌐 5. TRANSLATION STRUCTURE**

### **[module].json Pattern:**
```json
{
    "entity": {
        "title": "Entity | Entities",
        "subtitle": "Manage entity information",
        "add": "Add Entity",
        "edit": "Edit Entity",
        "detail": "Entity Details",
        "search": "Search entities...",
        
        "edit_subtitle": "Update entity information",
        "add_subtitle": "Create a new entity",
        "detail_subtitle": "View entity information and details",
        "create_form": "Create New Entity",
        "edit_form": "Edit Entity Information", 
        "form_description": "Fill in the required information",
        
        "sections": {
            "basic_info": "Basic Information",
            "contact_info": "Contact Information",
            "status": "Status & Settings"
        },
        
        "fields": {
            "name": "Name",
            "code": "Code",
            "status": "Status"
        },
        
        "placeholders": {
            "name": "Enter entity name...",
            "code": "Enter entity code..."
        },
        
        "status": {
            "active": "Active",
            "inactive": "Inactive"
        }
    }
}
```

---

## **📋 6. COMPREHENSIVE CHECKLIST**

### **✅ Index Page Requirements:**
- [ ] PageHeader dengan primary action dan dropdown actions
- [ ] FilterSection dengan advanced filters
- [ ] Statistics cards (optional)
- [ ] Professional table dengan modern styling
- [ ] Action dropdown di setiap row
- [ ] Professional pagination dengan page size selector
- [ ] Skeleton loading states
- [ ] Proper error handling

### **✅ Form Page Requirements:**
- [ ] PageHeader dengan back action
- [ ] Sectioned form dengan visual hierarchy
- [ ] Card header dengan icon dan description
- [ ] Responsive grid layout (el-row/el-col)
- [ ] Proper validation rules
- [ ] Loading states pada buttons
- [ ] Unsaved changes confirmation
- [ ] Professional action buttons

### **✅ Show Page Requirements:**
- [ ] PageHeader dengan edit action dan dropdown
- [ ] Grid layout dengan main content dan sidebar
- [ ] Skeleton loading states
- [ ] DetailItem component untuk consistent display
- [ ] Status badges dan formatting
- [ ] Related data sections (jika ada)
- [ ] Duplicate dan delete actions

### **✅ Universal Requirements:**
- [ ] Vue 3 Composition API dengan `<script setup>`
- [ ] TanStack Vue Query untuk data fetching
- [ ] Element Plus components consistency
- [ ] Tailwind CSS styling
- [ ] Iconify mingcute icons
- [ ] Responsive design (mobile-first)
- [ ] Proper translation structure
- [ ] Error boundary handling
- [ ] Loading states management

---

## **🎯 EXAMPLE USAGE:**

```
Buatkan desain UI/UX yang konsisten untuk modul Product Category dengan 3 halaman utama menggunakan pattern @resources\js\Pages\Settings\Outlet:

TARGET FILES:
- @resources\js\Pages\Product\Category\Index.vue
- @resources\js\Pages\Product\Category\Form.vue
- @resources\js\Pages\Product\Category\Show.vue

BACKEND REFERENCES:
- Model: @app\Models\Product\ProductCategory.php
- Controller: @app\Http\Controllers\Product\CategoryController.php
- Database: @dev_lovaerp.sql (table: product_categories)
- Translation: @resources\js\lang\en\products.json

SPECIAL REQUIREMENTS:
- Support nested categories dengan parent_id
- Image upload untuk category icon
- SEO fields (slug, meta_description)
- Hierarchical tree view di Index page
- Breadcrumb navigation di Show page
```

Template ini memberikan konsistensi lengkap untuk semua halaman dalam modul! 🚀