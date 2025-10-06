<template>
  <div class="theme-translation-editor">
    <!-- Header dengan informasi theme -->
    <div class="editor-header">
      <div class="theme-info">
        <h3>{{ $t('themeTranslation.editor.title') }}</h3>
        <p v-if="themeData?.name" class="theme-name">
          {{ $t('themeTranslation.editor.currentTheme') }}: {{ themeData.name }}
        </p>
        <div class="language-info">
          <el-tag type="info" size="small">
            {{ $t('themeTranslation.editor.sourceLanguage') }}: {{ sourceLanguage }}
          </el-tag>
          <el-icon><ArrowRight /></el-icon>
          <el-tag type="success" size="small">
            {{ $t('themeTranslation.editor.targetLanguage') }}: {{ targetLanguage }}
          </el-tag>
        </div>
      </div>
      
      <div class="editor-actions">
        <el-button @click="loadThemeFile" type="primary" :icon="Upload">
          {{ $t('themeTranslation.editor.loadTheme') }}
        </el-button>
        <el-button @click="exportTranslations" type="success" :icon="Download">
          {{ $t('themeTranslation.editor.exportTranslations') }}
        </el-button>
        <el-button @click="importTranslations" :icon="Upload">
          {{ $t('themeTranslation.editor.importTranslations') }}
        </el-button>
      </div>
    </div>

    <!-- Filter dan pencarian -->
    <div class="editor-filters">
      <el-row :gutter="16">
        <el-col :span="8">
          <el-input
            v-model="searchQuery"
            :placeholder="$t('themeTranslation.editor.searchPlaceholder')"
            :prefix-icon="Search"
            clearable
          />
        </el-col>
        <el-col :span="6">
          <el-select
            v-model="filterStatus"
            :placeholder="$t('themeTranslation.editor.filterByStatus')"
            clearable
          >
            <el-option
              :label="$t('themeTranslation.editor.statusAll')"
              value=""
            />
            <el-option
              :label="$t('themeTranslation.editor.statusTranslated')"
              value="translated"
            />
            <el-option
              :label="$t('themeTranslation.editor.statusPending')"
              value="pending"
            />
          </el-select>
        </el-col>
        <el-col :span="6">
          <el-select
            v-model="filterCategory"
            :placeholder="$t('themeTranslation.editor.filterByCategory')"
            clearable
          >
            <el-option
              v-for="category in categories"
              :key="category"
              :label="category"
              :value="category"
            />
          </el-select>
        </el-col>
        <el-col :span="4">
          <el-button @click="autoTranslate" type="warning" :loading="isAutoTranslating">
            {{ $t('themeTranslation.editor.autoTranslate') }}
          </el-button>
        </el-col>
      </el-row>
    </div>

    <!-- Progress bar -->
    <div class="translation-progress">
      <el-progress
        :percentage="translationProgress"
        :status="translationProgress === 100 ? 'success' : 'primary'"
        :stroke-width="8"
      >
        <template #default="{ percentage }">
          <span class="progress-text">
            {{ Math.round(percentage) }}% {{ $t('themeTranslation.editor.completed') }}
            ({{ translatedCount }}/{{ totalCount }})
          </span>
        </template>
      </el-progress>
    </div>

    <!-- Tabel translasi -->
    <div class="translation-table">
      <el-table
        :data="filteredTranslations"
        stripe
        border
        height="500"
        :empty-text="$t('themeTranslation.editor.noData')"
      >
        <el-table-column
          prop="key"
          :label="$t('themeTranslation.editor.columnKey')"
          width="200"
          fixed="left"
        >
          <template #default="{ row }">
            <el-tooltip :content="row.key" placement="top">
              <span class="translation-key">{{ row.key }}</span>
            </el-tooltip>
          </template>
        </el-table-column>
        
        <el-table-column
          prop="category"
          :label="$t('themeTranslation.editor.columnCategory')"
          width="120"
        >
          <template #default="{ row }">
            <el-tag size="small" :type="getCategoryTagType(row.category)">
              {{ row.category }}
            </el-tag>
          </template>
        </el-table-column>
        
        <el-table-column
          prop="sourceText"
          :label="$t('themeTranslation.editor.columnSourceText')"
          width="250"
        >
          <template #default="{ row }">
            <div class="source-text">{{ row.sourceText }}</div>
          </template>
        </el-table-column>
        
        <el-table-column
          prop="translatedText"
          :label="$t('themeTranslation.editor.columnTranslatedText')"
          min-width="250"
        >
          <template #default="{ row }">
            <el-input
              v-model="row.translatedText"
              :placeholder="$t('themeTranslation.editor.translationPlaceholder')"
              @change="updateTranslation(row.key, row.translatedText)"
              clearable
            />
          </template>
        </el-table-column>
        
        <el-table-column
          :label="$t('themeTranslation.editor.columnStatus')"
          width="100"
          align="center"
        >
          <template #default="{ row }">
            <el-tag
              :type="row.translatedText ? 'success' : 'warning'"
              size="small"
            >
              {{ row.translatedText ? $t('themeTranslation.editor.statusTranslated') : $t('themeTranslation.editor.statusPending') }}
            </el-tag>
          </template>
        </el-table-column>
        
        <el-table-column
          :label="$t('themeTranslation.editor.columnActions')"
          width="120"
          fixed="right"
          align="center"
        >
          <template #default="{ row }">
            <el-button
              @click="clearTranslation(row.key)"
              type="danger"
              size="small"
              :icon="Delete"
              circle
              :title="$t('themeTranslation.editor.clearTranslation')"
            />
            <el-button
              @click="copyFromSource(row.key, row.sourceText)"
              type="info"
              size="small"
              :icon="CopyDocument"
              circle
              :title="$t('themeTranslation.editor.copyFromSource')"
            />
          </template>
        </el-table-column>
      </el-table>
    </div>

    <!-- Dialog untuk load theme file -->
    <el-dialog
      v-model="showLoadDialog"
      :title="$t('themeTranslation.editor.loadThemeDialog')"
      width="600px"
    >
      <div class="load-dialog-content">
        <el-tabs v-model="loadMethod">
          <el-tab-pane
            :label="$t('themeTranslation.editor.loadFromFile')"
            name="file"
          >
            <el-upload
              ref="uploadRef"
              :auto-upload="false"
              :show-file-list="false"
              accept=".json"
              @change="handleFileSelect"
            >
              <el-button type="primary" :icon="Upload">
                {{ $t('themeTranslation.editor.selectFile') }}
              </el-button>
            </el-upload>
            <p v-if="selectedFile" class="selected-file">
              {{ $t('themeTranslation.editor.selectedFile') }}: {{ selectedFile.name }}
            </p>
          </el-tab-pane>
          
          <el-tab-pane
            :label="$t('themeTranslation.editor.loadFromText')"
            name="text"
          >
            <el-input
              v-model="themeJsonText"
              type="textarea"
              :rows="10"
              :placeholder="$t('themeTranslation.editor.pasteJsonHere')"
            />
          </el-tab-pane>
        </el-tabs>
      </div>
      
      <template #footer>
        <el-button @click="showLoadDialog = false">
          {{ $t('themeTranslation.editor.cancel') }}
        </el-button>
        <el-button @click="processThemeData" type="primary">
          {{ $t('themeTranslation.editor.load') }}
        </el-button>
      </template>
    </el-dialog>

    <!-- Dialog untuk export -->
    <el-dialog
      v-model="showExportDialog"
      :title="$t('themeTranslation.editor.exportDialog')"
      width="500px"
    >
      <div class="export-options">
        <el-form label-width="150px">
          <el-form-item :label="$t('themeTranslation.editor.exportFormat')">
            <el-select v-model="exportFormat">
              <el-option label="JSON" value="json" />
              <el-option label="CSV" value="csv" />
            </el-select>
          </el-form-item>
          
          <el-form-item :label="$t('themeTranslation.editor.exportType')">
            <el-radio-group v-model="exportType">
              <el-radio label="translations">{{ $t('themeTranslation.editor.exportTranslationsOnly') }}</el-radio>
              <el-radio label="theme">{{ $t('themeTranslation.editor.exportTranslatedTheme') }}</el-radio>
            </el-radio-group>
          </el-form-item>
        </el-form>
      </div>
      
      <template #footer>
        <el-button @click="showExportDialog = false">
          {{ $t('themeTranslation.editor.cancel') }}
        </el-button>
        <el-button @click="performExport" type="primary">
          {{ $t('themeTranslation.editor.export') }}
        </el-button>
      </template>
    </el-dialog>

    <!-- Input tersembunyi untuk import -->
    <input
      ref="importFileInput"
      type="file"
      accept=".json,.csv"
      style="display: none"
      @change="handleImportFile"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { ElMessage, ElMessageBox } from 'element-plus'
import {
  Upload,
  Download,
  Search,
  Delete,
  CopyDocument,
  ArrowRight
} from '@element-plus/icons-vue'
import ThemeTranslationUtils from '@/utils/ThemeTranslationUtils'

// Props
const props = defineProps({
  initialTheme: {
    type: Object,
    default: null
  },
  sourceLanguage: {
    type: String,
    default: 'en'
  },
  targetLanguage: {
    type: String,
    default: 'id'
  }
})

// Emits
const emit = defineEmits(['theme-loaded', 'translations-updated'])

// Composables
const { t } = useI18n()

// Reactive data
const themeData = ref(props.initialTheme)
const translations = ref({})
const searchQuery = ref('')
const filterStatus = ref('')
const filterCategory = ref('')
const isAutoTranslating = ref(false)

// Dialog states
const showLoadDialog = ref(false)
const showExportDialog = ref(false)
const loadMethod = ref('file')
const selectedFile = ref(null)
const themeJsonText = ref('')

// Export options
const exportFormat = ref('json')
const exportType = ref('translations')

// Refs
const uploadRef = ref()
const importFileInput = ref()

// Computed properties
const translatableTexts = computed(() => {
  if (!themeData.value) return {}
  return ThemeTranslationUtils.extractTranslatableTexts(themeData.value)
})

const translationItems = computed(() => {
  const items = []
  const texts = translatableTexts.value
  
  for (const [key, sourceText] of Object.entries(texts)) {
    const category = key.split('.')[0]
    items.push({
      key,
      category,
      sourceText,
      translatedText: translations.value[key] || ''
    })
  }
  
  return items
})

const filteredTranslations = computed(() => {
  let filtered = translationItems.value
  
  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(item =>
      item.key.toLowerCase().includes(query) ||
      item.sourceText.toLowerCase().includes(query) ||
      item.translatedText.toLowerCase().includes(query)
    )
  }
  
  // Filter by status
  if (filterStatus.value) {
    filtered = filtered.filter(item => {
      if (filterStatus.value === 'translated') {
        return item.translatedText.trim() !== ''
      } else if (filterStatus.value === 'pending') {
        return item.translatedText.trim() === ''
      }
      return true
    })
  }
  
  // Filter by category
  if (filterCategory.value) {
    filtered = filtered.filter(item => item.category === filterCategory.value)
  }
  
  return filtered
})

const categories = computed(() => {
  const cats = new Set()
  translationItems.value.forEach(item => cats.add(item.category))
  return Array.from(cats).sort()
})

const totalCount = computed(() => translationItems.value.length)

const translatedCount = computed(() => {
  return translationItems.value.filter(item => item.translatedText.trim() !== '').length
})

const translationProgress = computed(() => {
  if (totalCount.value === 0) return 0
  return (translatedCount.value / totalCount.value) * 100
})

// Methods
const loadThemeFile = () => {
  showLoadDialog.value = true
}

const handleFileSelect = (file) => {
  selectedFile.value = file.raw
}

const processThemeData = async () => {
  try {
    let themeJson = null
    
    if (loadMethod.value === 'file' && selectedFile.value) {
      const text = await readFileAsText(selectedFile.value)
      themeJson = JSON.parse(text)
    } else if (loadMethod.value === 'text' && themeJsonText.value) {
      themeJson = JSON.parse(themeJsonText.value)
    } else {
      ElMessage.warning(t('themeTranslation.editor.pleaseSelectOrPasteTheme'))
      return
    }
    
    // Validasi struktur theme
    const validation = ThemeTranslationUtils.validateThemeStructure(themeJson)
    if (!validation.isValid) {
      ElMessage.error(t('themeTranslation.editor.invalidThemeStructure') + ': ' + validation.errors.join(', '))
      return
    }
    
    // Load theme data
    themeData.value = themeJson
    
    // Reset translations
    translations.value = {}
    
    // Show warnings if any
    if (validation.warnings.length > 0) {
      ElMessage.warning(validation.warnings.join(', '))
    }
    
    showLoadDialog.value = false
    selectedFile.value = null
    themeJsonText.value = ''
    
    emit('theme-loaded', themeJson)
    ElMessage.success(t('themeTranslation.editor.themeLoadedSuccessfully'))
    
  } catch (error) {
    ElMessage.error(t('themeTranslation.editor.errorLoadingTheme') + ': ' + error.message)
  }
}

const readFileAsText = (file) => {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.onload = (e) => resolve(e.target.result)
    reader.onerror = reject
    reader.readAsText(file)
  })
}

const updateTranslation = (key, value) => {
  translations.value[key] = value
  emit('translations-updated', { ...translations.value })
}

const clearTranslation = (key) => {
  translations.value[key] = ''
  emit('translations-updated', { ...translations.value })
}

const copyFromSource = (key, sourceText) => {
  translations.value[key] = sourceText
  emit('translations-updated', { ...translations.value })
}

const autoTranslate = async () => {
  isAutoTranslating.value = true
  
  try {
    // Simulasi auto translate (dalam implementasi nyata, gunakan API translate)
    const pendingItems = translationItems.value.filter(item => !item.translatedText.trim())
    
    for (const item of pendingItems) {
      // Simulasi delay
      await new Promise(resolve => setTimeout(resolve, 100))
      
      // Simulasi translasi sederhana (ganti dengan API translate yang sesungguhnya)
      let translated = item.sourceText
      
      // Contoh translasi sederhana untuk beberapa kata umum
      const simpleTranslations = {
        'Professional Blue': 'Biru Profesional',
        'Light Mode': 'Mode Terang',
        'Dark Mode': 'Mode Gelap',
        'Auto Mode': 'Mode Otomatis',
        'Primary Colors': 'Warna Utama',
        'Secondary Colors': 'Warna Sekunder',
        'Button Styles': 'Gaya Tombol',
        'professional': 'profesional',
        'blue': 'biru',
        'business': 'bisnis',
        'modern': 'modern'
      }
      
      if (simpleTranslations[translated]) {
        translated = simpleTranslations[translated]
      }
      
      translations.value[item.key] = translated
    }
    
    emit('translations-updated', { ...translations.value })
    ElMessage.success(t('themeTranslation.editor.autoTranslateCompleted'))
    
  } catch (error) {
    ElMessage.error(t('themeTranslation.editor.autoTranslateError') + ': ' + error.message)
  } finally {
    isAutoTranslating.value = false
  }
}

const exportTranslations = () => {
  if (!themeData.value) {
    ElMessage.warning(t('themeTranslation.editor.pleaseLoadThemeFirst'))
    return
  }
  
  showExportDialog.value = true
}

const performExport = () => {
  try {
    let dataToExport = null
    let filename = ''
    
    if (exportType.value === 'translations') {
      // Export hanya translasi
      dataToExport = {
        sourceLanguage: props.sourceLanguage,
        targetLanguage: props.targetLanguage,
        translations: translations.value
      }
      filename = `theme-translations-${props.targetLanguage}.${exportFormat.value}`
    } else {
      // Export theme yang sudah diterjemahkan
      dataToExport = ThemeTranslationUtils.applyTranslations(themeData.value, translations.value)
      dataToExport.language = props.targetLanguage
      filename = `theme-translated-${props.targetLanguage}.${exportFormat.value}`
    }
    
    const exportedData = ThemeTranslationUtils.exportTranslations(dataToExport, exportFormat.value)
    downloadFile(exportedData, filename, exportFormat.value === 'json' ? 'application/json' : 'text/csv')
    
    showExportDialog.value = false
    ElMessage.success(t('themeTranslation.editor.exportSuccessful'))
    
  } catch (error) {
    ElMessage.error(t('themeTranslation.editor.exportError') + ': ' + error.message)
  }
}

const importTranslations = () => {
  importFileInput.value.click()
}

const handleImportFile = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  try {
    const text = await readFileAsText(file)
    let importedData = null
    
    if (file.name.endsWith('.json')) {
      importedData = JSON.parse(text)
    } else if (file.name.endsWith('.csv')) {
      importedData = ThemeTranslationUtils.importFromCsv(text)
    } else {
      ElMessage.error(t('themeTranslation.editor.unsupportedFileFormat'))
      return
    }
    
    if (importedData.translations) {
      Object.assign(translations.value, importedData.translations)
      emit('translations-updated', { ...translations.value })
      ElMessage.success(t('themeTranslation.editor.importSuccessful'))
    } else {
      ElMessage.error(t('themeTranslation.editor.invalidTranslationFile'))
    }
    
  } catch (error) {
    ElMessage.error(t('themeTranslation.editor.importError') + ': ' + error.message)
  }
  
  // Reset input
  event.target.value = ''
}

const downloadFile = (content, filename, mimeType) => {
  const blob = new Blob([content], { type: mimeType })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = filename
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
}

const getCategoryTagType = (category) => {
  const types = {
    'theme': 'primary',
    'labels': 'success',
    'metadata': 'info'
  }
  return types[category] || 'default'
}

// Watch for prop changes
watch(() => props.initialTheme, (newTheme) => {
  if (newTheme) {
    themeData.value = newTheme
  }
}, { immediate: true })

// Lifecycle
onMounted(() => {
  if (props.initialTheme) {
    themeData.value = props.initialTheme
  }
})
</script>

<style scoped>
.theme-translation-editor {
  padding: 20px;
}

.editor-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 20px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--el-border-color);
}

.theme-info h3 {
  margin: 0 0 8px 0;
  color: var(--el-text-color-primary);
}

.theme-name {
  margin: 0 0 12px 0;
  color: var(--el-text-color-regular);
  font-size: 14px;
}

.language-info {
  display: flex;
  align-items: center;
  gap: 8px;
}

.editor-actions {
  display: flex;
  gap: 12px;
}

.editor-filters {
  margin-bottom: 20px;
}

.translation-progress {
  margin-bottom: 20px;
}

.progress-text {
  font-size: 12px;
  color: var(--el-text-color-regular);
}

.translation-table {
  border-radius: 6px;
  overflow: hidden;
}

.translation-key {
  font-family: var(--el-font-family-mono);
  font-size: 12px;
  color: var(--el-color-primary);
  display: block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.source-text {
  color: var(--el-text-color-regular);
  line-height: 1.4;
}

.load-dialog-content {
  padding: 20px 0;
}

.selected-file {
  margin-top: 12px;
  color: var(--el-color-success);
  font-size: 14px;
}

.export-options {
  padding: 20px 0;
}
</style>