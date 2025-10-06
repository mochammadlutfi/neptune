<template>
  <div class="theme-manager">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
          {{ $t('theme.title') }}
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
          {{ $t('theme.description') }}
        </p>
        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
          💡 {{ $t('theme.tweakcnTip') }}
        </p>
      </div>
      
      <div class="flex gap-2">
        <el-button 
          type="primary" 
          :icon="Upload" 
          @click="handleImportTheme"
          :loading="importing"
        >
          {{ $t('theme.import') }}
        </el-button>
        
        <el-button 
          :icon="Download" 
          @click="handleExportTheme"
          :disabled="!currentThemeData"
        >
          {{ $t('theme.export') }}
        </el-button>
        
        <el-button 
          :icon="RefreshLeft" 
          @click="resetToDefault"
          type="danger"
          plain
        >
          {{ $t('theme.reset') }}
        </el-button>
      </div>
    </div>

    <!-- Theme Status -->
    <el-card class="mb-6" v-if="currentThemeData">
      <template #header>
        <div class="flex items-center justify-between">
          <span class="font-medium">{{ $t('theme.currentTheme') }}</span>
          <div class="flex gap-2">
            <el-tag type="success">{{ $t('theme.jsonMode') }}</el-tag>
            <el-tag v-if="currentThemeData.type" type="info">
              {{ currentThemeData.type }}
            </el-tag>
          </div>
        </div>
      </template>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="space-y-2">
          <span class="text-sm text-gray-600 dark:text-gray-400">
            {{ $t('theme.themeName') }}:
          </span>
          <div class="font-medium">
            {{ currentThemeData?.name || 'Custom Theme' }}
          </div>
        </div>
        
        <div class="space-y-2" v-if="currentThemeData.version">
          <span class="text-sm text-gray-600 dark:text-gray-400">
            {{ $t('theme.version') }}:
          </span>
          <div class="font-medium">
            {{ currentThemeData.version }}
          </div>
        </div>
        
        <div class="space-y-2">
          <span class="text-sm text-gray-600 dark:text-gray-400">
            {{ $t('theme.mode') }}:
          </span>
          <el-tag :type="currentMode === 'dark' ? 'info' : 'primary'">
            {{ $t(`theme.modes.${currentMode}`) }}
          </el-tag>
        </div>
        
        <div class="space-y-2" v-if="primaryColorValue">
          <span class="text-sm text-gray-600 dark:text-gray-400">
            {{ $t('theme.primaryColor') }}:
          </span>
          <div class="flex items-center gap-2">
            <div 
              class="w-4 h-4 rounded border"
              :style="{ backgroundColor: primaryColorValue }"
            ></div>
            <span class="text-sm font-mono">{{ primaryColorValue }}</span>
          </div>
        </div>
        
        <div class="space-y-2" v-if="currentThemeData.cssVars?.theme?.radius">
          <span class="text-sm text-gray-600 dark:text-gray-400">
            {{ $t('theme.borderRadius') }}:
          </span>
          <div class="font-medium">
            {{ currentThemeData.cssVars.theme.radius }}
          </div>
        </div>
        
        <div class="space-y-2" v-if="currentThemeData.cssVars?.theme?.['font-sans']">
          <span class="text-sm text-gray-600 dark:text-gray-400">
            {{ $t('theme.fontFamily') }}:
          </span>
          <div class="font-medium text-sm">
            {{ currentThemeData.cssVars.theme['font-sans'] }}
          </div>
        </div>
      </div>
    </el-card>

    <!-- Theme Controls -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Mode Selection -->
      <el-card>
        <template #header>
          <span class="font-medium">{{ $t('theme.modeSelection') }}</span>
        </template>
        
        <el-radio-group 
          :model-value="theme.mode" 
          @update:model-value="setThemeMode"
          class="w-full"
        >
          <el-radio-button 
            v-for="mode in ['light', 'dark', 'auto']" 
            :key="mode"
            :label="mode"
            class="w-full mb-2"
          >
            <div class="flex items-center gap-2">
              <component :is="getModeIcon(mode)" class="w-4 h-4" />
              {{ $t(`theme.modes.${mode}`) }}
            </div>
          </el-radio-button>
        </el-radio-group>
      </el-card>

      <!-- Color Customization -->
      <el-card>
        <template #header>
          <span class="font-medium">{{ $t('theme.colorCustomization') }}</span>
        </template>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">
              {{ $t('theme.primaryColor') }}
            </label>
            <div class="flex items-center gap-3">
              <el-color-picker 
                :model-value="theme.primaryColor"
                @update:model-value="setPrimaryColor"
                show-alpha
                :predefine="colorPresets"
              />
              <el-input 
                :model-value="theme.primaryColor"
                @update:model-value="setPrimaryColor"
                placeholder="#3b82f6"
                class="flex-1"
              />
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-2">
              {{ $t('theme.borderRadius') }}
            </label>
            <el-select 
              :model-value="theme.radius"
              @update:model-value="setRadius"
              class="w-full"
            >
              <el-option 
                v-for="option in radiusOptions"
                :key="option.value"
                :label="option.label"
                :value="option.value"
              />
            </el-select>
          </div>
        </div>
      </el-card>

      <!-- Typography -->
      <el-card>
        <template #header>
          <span class="font-medium">{{ $t('theme.typography') }}</span>
        </template>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">
              {{ $t('theme.fontFamily') }}
            </label>
            <el-select 
              :model-value="theme.fontFamily"
              @update:model-value="setFontFamily"
              class="w-full"
            >
              <el-option 
                v-for="font in fontFamilies"
                :key="font.value"
                :label="font.label"
                :value="font.value"
              />
            </el-select>
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-2">
              {{ $t('theme.fontSize') }}
            </label>
            <el-input 
              :model-value="theme.fontSize"
              @update:model-value="setFontSize"
              placeholder="14px"
            />
          </div>
        </div>
      </el-card>

      <!-- Advanced Settings -->
      <el-card>
        <template #header>
          <span class="font-medium">{{ $t('theme.advancedSettings') }}</span>
        </template>
        
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-2">
              {{ $t('theme.spacing') }}
            </label>
            <el-select 
              :model-value="theme.spacing"
              @update:model-value="setSpacing"
              class="w-full"
            >
              <el-option 
                v-for="option in spacingOptions"
                :key="option.value"
                :label="option.label"
                :value="option.value"
              />
            </el-select>
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-2">
              {{ $t('theme.shadow') }}
            </label>
            <el-select 
              :model-value="theme.shadow"
              @update:model-value="setShadow"
              class="w-full"
            >
              <el-option 
                v-for="option in shadowOptions"
                :key="option.value"
                :label="option.label"
                :value="option.value"
              />
            </el-select>
          </div>
        </div>
      </el-card>
    </div>

    <!-- Theme Preview -->
    <el-card class="mt-6">
      <template #header>
        <span class="font-medium">{{ $t('theme.preview') }}</span>
      </template>
      
      <div class="space-y-4">
        <!-- Preview Components -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <el-button type="primary">{{ $t('theme.previewButton') }}</el-button>
          <el-button type="success">{{ $t('theme.previewSuccess') }}</el-button>
          <el-button type="warning">{{ $t('theme.previewWarning') }}</el-button>
        </div>
        
        <el-card class="bg-gray-50 dark:bg-gray-800">
          <p class="text-sm">{{ $t('theme.previewText') }}</p>
        </el-card>
      </div>
    </el-card>

    <!-- Actions -->
    <div class="flex justify-between mt-6">
      <el-button @click="resetTheme" type="danger" plain>
        {{ $t('theme.reset') }}
      </el-button>
      
      <div class="flex gap-2">
        <el-button @click="saveTheme" type="success">
          {{ $t('theme.save') }}
        </el-button>
        
        <el-button @click="handleGenerateThemeJson" type="primary">
          {{ $t('theme.generateJson') }}
        </el-button>
      </div>
    </div>

    <!-- Import Dialog -->
    <el-dialog 
      v-model="importDialogVisible"
      :title="$t('theme.importTheme')"
      width="600px"
    >
      <div class="space-y-4">
        <el-upload
          ref="uploadRef"
          :auto-upload="false"
          :show-file-list="false"
          accept=".json"
          @change="handleFileSelect"
        >
          <el-button type="primary" :icon="Upload">
            {{ $t('theme.selectFile') }}
          </el-button>
        </el-upload>
        
        <div v-if="selectedFile">
          <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ $t('theme.selectedFile') }}: {{ selectedFile.name }}
          </p>
        </div>
        
        <el-divider>{{ $t('theme.or') }}</el-divider>
        
        <div>
          <label class="block text-sm font-medium mb-2">
            {{ $t('theme.pasteJson') }}
          </label>
          <el-input
            v-model="jsonInput"
            type="textarea"
            :rows="8"
            :placeholder="$t('theme.jsonPlaceholder')"
          />
        </div>
      </div>
      
      <template #footer>
        <div class="flex justify-end gap-2">
          <el-button @click="importDialogVisible = false">
            {{ $t('common.cancel') }}
          </el-button>
          <el-button 
            type="primary" 
            @click="confirmImport"
            :loading="importing"
          >
            {{ $t('theme.import') }}
          </el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useTheme } from '@/composables/useTheme'
import { useI18n } from 'vue-i18n'
import { ElMessage, ElMessageBox } from 'element-plus'
import { 
  Upload, 
  Download, 
  Sunny, 
  Moon, 
  Monitor 
} from '@element-plus/icons-vue'

const { t } = useI18n()

// Theme composable
const {
  theme,
  isThemeJsonLoaded,
  currentThemeJson,
  fontFamilies,
  radiusOptions,
  spacingOptions,
  shadowOptions,
  setThemeMode,
  setPrimaryColor,
  setRadius,
  setFontFamily,
  setFontSize,
  setSpacing,
  setShadow,
  resetTheme,
  saveTheme,
  exportThemeJson,
  importThemeJson
} = useTheme()

// Color presets
const colorPresets = [
  '#3b82f6', // blue
  '#10b981', // emerald
  '#8b5cf6', // violet
  '#f59e0b', // amber
  '#ef4444', // red
  '#06b6d4', // cyan
  '#84cc16', // lime
  '#f97316'  // orange
]

// Import/Export state
const importing = ref(false)
const importDialogVisible = ref(false)
const selectedFile = ref(null)
const jsonInput = ref('')
const uploadRef = ref()

// Get mode icon
const getModeIcon = (mode) => {
  switch (mode) {
    case 'light': return Sunny
    case 'dark': return Moon
    case 'auto': return Monitor
    default: return Monitor
  }
}

// Handle import theme
const handleImportTheme = () => {
  importDialogVisible.value = true
  selectedFile.value = null
  jsonInput.value = ''
}

// Handle export theme
const handleExportTheme = () => {
  try {
    const themeJson = exportThemeJson()
    const blob = new Blob([JSON.stringify(themeJson, null, 2)], {
      type: 'application/json'
    })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = 'theme.json'
    a.click()
    URL.revokeObjectURL(url)
    
    ElMessage.success(t('theme.exportSuccess'))
  } catch (error) {
    ElMessage.error(t('theme.exportError'))
  }
}

// Handle file select
const handleFileSelect = (file) => {
  selectedFile.value = file
  jsonInput.value = ''
}

// Confirm import
const confirmImport = async () => {
  importing.value = true
  
  try {
    let themeData = null
    
    if (selectedFile.value) {
      // Import from file
      const text = await selectedFile.value.raw.text()
      themeData = JSON.parse(text)
    } else if (jsonInput.value.trim()) {
      // Import from JSON input
      themeData = JSON.parse(jsonInput.value.trim())
    } else {
      ElMessage.warning(t('theme.selectFileOrPaste'))
      return
    }
    
    const success = await importThemeJson(themeData)
    
    if (success) {
      ElMessage.success(t('theme.importSuccess'))
      importDialogVisible.value = false
    } else {
      ElMessage.error(t('theme.importError'))
    }
  } catch (error) {
    ElMessage.error(t('theme.invalidJson'))
  } finally {
    importing.value = false
  }
}

// Generate theme JSON
const handleGenerateThemeJson = async () => {
  try {
    const result = await ElMessageBox.confirm(
      t('theme.generateJsonConfirm'),
      t('theme.generateJson'),
      {
        confirmButtonText: t('common.confirm'),
        cancelButtonText: t('common.cancel'),
        type: 'info'
      }
    )
    
    if (result === 'confirm') {
      handleExportTheme()
    }
  } catch {
    // User cancelled
  }
}
</script>

<style scoped>
.theme-manager {
  @apply p-6 max-w-6xl mx-auto;
}

.el-radio-button {
  @apply w-full;
}

.el-radio-button__inner {
  @apply w-full text-left;
}
</style>