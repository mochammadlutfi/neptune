<template>
  <div class="theme-translation-manager">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <h1>{{ $t('themeTranslation.title') }}</h1>
        <p class="header-description">{{ $t('themeTranslation.description') }}</p>
      </div>
      
      <div class="header-actions">
        <el-button @click="loadDefaultTheme" type="primary" :icon="Folder">
          {{ $t('themeTranslation.loadDefaultTheme') }}
        </el-button>
        <el-button @click="showQuickStart" :icon="QuestionFilled">
          {{ $t('themeTranslation.quickStart') }}
        </el-button>
      </div>
    </div>

    <!-- Quick Start Guide -->
    <el-alert
      v-if="showQuickStartGuide"
      :title="$t('themeTranslation.quickStartTitle')"
      type="info"
      :closable="true"
      @close="showQuickStartGuide = false"
      class="quick-start-alert"
    >
      <template #default>
        <ol class="quick-start-steps">
          <li>{{ $t('themeTranslation.step1') }}</li>
          <li>{{ $t('themeTranslation.step2') }}</li>
          <li>{{ $t('themeTranslation.step3') }}</li>
          <li>{{ $t('themeTranslation.step4') }}</li>
        </ol>
      </template>
    </el-alert>

    <!-- Language Selection -->
    <el-card class="language-selection-card" shadow="never">
      <template #header>
        <span>{{ $t('themeTranslation.languageSettings') }}</span>
      </template>
      
      <el-row :gutter="24">
        <el-col :span="12">
          <el-form-item :label="$t('themeTranslation.sourceLanguage')">
            <el-select v-model="sourceLanguage" style="width: 100%" @change="onLanguageChange">
              <el-option
                v-for="lang in availableLanguages"
                :key="lang.code"
                :label="lang.name"
                :value="lang.code"
              />
            </el-select>
          </el-form-item>
        </el-col>
        <el-col :span="12">
          <el-form-item :label="$t('themeTranslation.targetLanguage')">
            <el-select v-model="targetLanguage" style="width: 100%" @change="onLanguageChange">
              <el-option
                v-for="lang in availableLanguages"
                :key="lang.code"
                :label="lang.name"
                :value="lang.code"
              />
            </el-select>
          </el-form-item>
        </el-col>
      </el-row>
    </el-card>

    <!-- Translation Editor -->
    <el-card v-if="currentTheme" class="translation-editor-card" shadow="never">
      <template #header>
        <div class="card-header">
          <span>{{ $t('themeTranslation.translationEditor') }}</span>
          <div class="editor-actions">
            <el-button @click="resetTranslations" type="warning" :icon="RefreshLeft">
              {{ $t('themeTranslation.resetTranslations') }}
            </el-button>
            <el-button @click="saveTranslations" type="success" :icon="Check" :loading="isSaving">
              {{ $t('themeTranslation.saveTranslations') }}
            </el-button>
          </div>
        </div>
      </template>
      
      <ThemeTranslationEditor
        :initial-theme="currentTheme"
        :source-language="sourceLanguage"
        :target-language="targetLanguage"
        @theme-loaded="onThemeLoaded"
        @translations-updated="onTranslationsUpdated"
      />
    </el-card>

    <!-- Empty State -->
    <el-empty
      v-else
      :description="$t('themeTranslation.noThemeLoaded')"
      class="empty-state"
    >
      <el-button @click="loadDefaultTheme" type="primary">
        {{ $t('themeTranslation.loadTheme') }}
      </el-button>
    </el-empty>

    <!-- Statistics Card -->
    <el-card v-if="currentTheme && translationStats" class="stats-card" shadow="never">
      <template #header>
        <span>{{ $t('themeTranslation.translationStatistics') }}</span>
      </template>
      
      <el-row :gutter="24">
        <el-col :span="6">
          <el-statistic
            :title="$t('themeTranslation.totalItems')"
            :value="translationStats.total"
            suffix="items"
          />
        </el-col>
        <el-col :span="6">
          <el-statistic
            :title="$t('themeTranslation.translated')"
            :value="translationStats.translated"
            :value-style="{ color: '#67c23a' }"
          />
        </el-col>
        <el-col :span="6">
          <el-statistic
            :title="$t('themeTranslation.pending')"
            :value="translationStats.pending"
            :value-style="{ color: '#e6a23c' }"
          />
        </el-col>
        <el-col :span="6">
          <el-statistic
            :title="$t('themeTranslation.progress')"
            :value="translationStats.progress"
            suffix="%"
            :precision="1"
            :value-style="{ color: '#409eff' }"
          />
        </el-col>
      </el-row>
    </el-card>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { ElMessage, ElMessageBox } from 'element-plus'
import { 
  Folder,
  QuestionFilled,
  RefreshLeft,
  Check
} from '@element-plus/icons-vue'
import ThemeTranslationEditor from '@/components/Theme/ThemeTranslationEditor.vue'

const { t } = useI18n()

// State
const currentTheme = ref(null)
const sourceLanguage = ref('en')
const targetLanguage = ref('id')
const showQuickStartGuide = ref(false)
const isSaving = ref(false)
const translationStats = ref(null)

// Available languages
const availableLanguages = [
  { code: 'en', name: 'English' },
  { code: 'id', name: 'Bahasa Indonesia' },
  { code: 'es', name: 'Español' },
  { code: 'fr', name: 'Français' },
  { code: 'de', name: 'Deutsch' },
  { code: 'zh', name: '中文' },
  { code: 'ja', name: '日本語' },
  { code: 'ko', name: '한국어' }
]

// Methods
const loadDefaultTheme = async () => {
  try {
    // Load default theme from the uploaded theme file
    const response = await fetch('/uploads/theme/theme.json')
    if (response.ok) {
      const defaultTheme = await response.json()
      currentTheme.value = defaultTheme
      ElMessage.success(t('themeTranslation.themeLoaded'))
    } else {
      // Fallback to sample theme
      const fallbackTheme = {
        "$schema": "https://ui.shadcn.com/schema/registry-item.json",
        "name": "Professional Blue Theme",
        "description": "A professional blue theme for business applications",
        "version": "1.0.0",
        "author": "LovAERP Team",
        "language": "en",
        "type": "registry:style",
        "labels": {
          "themeName": "Professional Blue",
          "themeDescription": "A clean and professional theme with blue accents",
          "colorScheme": "Blue Professional",
          "fontScheme": "Modern Sans",
          "spacingScheme": "Comfortable",
          "shadowScheme": "Subtle Depth",
          "categories": {
            "primary": "Primary Colors",
            "secondary": "Secondary Colors",
            "accent": "Accent Colors",
            "neutral": "Neutral Colors",
            "semantic": "Semantic Colors"
          },
          "components": {
            "button": "Button Styles",
            "card": "Card Components",
            "form": "Form Elements",
            "navigation": "Navigation",
            "sidebar": "Sidebar",
            "header": "Header",
            "footer": "Footer"
          },
          "modes": {
            "light": "Light Mode",
            "dark": "Dark Mode",
            "auto": "Auto Mode"
          }
        },
        "cssVars": {
          "theme": {
            "font-sans": "Inter, system-ui, sans-serif",
            "font-mono": "\"JetBrains Mono\", \"Fira Code\", monospace",
            "font-serif": "\"Merriweather\", Georgia, serif",
            "radius": "0.5rem"
          },
          "light": {
            "background": "0 0% 100%",
            "foreground": "222.2 84% 4.9%",
            "primary": "221.2 83.2% 53.3%",
            "primary-foreground": "210 40% 98%"
          },
          "dark": {
            "background": "222.2 84% 4.9%",
            "foreground": "210 40% 98%",
            "primary": "217.2 91.2% 59.8%",
            "primary-foreground": "222.2 84% 4.9%"
          }
        },
        "metadata": {
          "createdAt": "2024-01-15T10:00:00Z",
          "updatedAt": "2024-01-15T10:00:00Z",
          "tags": ["professional", "blue", "business", "modern"],
          "category": "business",
          "compatibility": ["web", "mobile", "desktop"],
          "supportedModes": ["light", "dark", "auto"]
        }
      }
      
      currentTheme.value = fallbackTheme
      ElMessage.success(t('themeTranslation.fallbackThemeLoaded'))
    }
  } catch (error) {
    ElMessage.error(t('themeTranslation.loadThemeError') + ': ' + error.message)
  }
}

const showQuickStart = () => {
  showQuickStartGuide.value = true
}

const onLanguageChange = () => {
  // Handle language change
  if (currentTheme.value) {
    // Reload theme with new language settings
    ElMessage.info(t('themeTranslation.languageChanged'))
  }
}

const onThemeLoaded = (theme) => {
  currentTheme.value = theme
  updateTranslationStats()
}

const onTranslationsUpdated = (translations) => {
  updateTranslationStats(translations)
}

const updateTranslationStats = (translations = {}) => {
  if (!currentTheme.value) return
  
  // Calculate statistics based on current theme and translations
  const totalItems = Object.keys(translations).length || 0
  const translatedItems = Object.values(translations).filter(value => value && value.trim() !== '').length
  const pendingItems = totalItems - translatedItems
  const progress = totalItems > 0 ? (translatedItems / totalItems) * 100 : 0
  
  translationStats.value = {
    total: totalItems,
    translated: translatedItems,
    pending: pendingItems,
    progress: Math.round(progress * 10) / 10
  }
}

const resetTranslations = async () => {
  try {
    await ElMessageBox.confirm(
      t('themeTranslation.resetConfirmMessage'),
      t('themeTranslation.resetConfirmTitle'),
      {
        confirmButtonText: t('common.confirm'),
        cancelButtonText: t('common.cancel'),
        type: 'warning'
      }
    )
    
    // Reset translations logic here
    translationStats.value = {
      total: 0,
      translated: 0,
      pending: 0,
      progress: 0
    }
    
    ElMessage.success(t('themeTranslation.translationsReset'))
  } catch {
    // User cancelled
  }
}

const saveTranslations = async () => {
  isSaving.value = true
  
  try {
    // Save translations logic here
    await new Promise(resolve => setTimeout(resolve, 1000)) // Simulate API call
    
    ElMessage.success(t('themeTranslation.translationsSaved'))
  } catch (error) {
    ElMessage.error(t('themeTranslation.saveError'))
  } finally {
    isSaving.value = false
  }
}

// Lifecycle
onMounted(() => {
  // Auto-load default theme on mount
  loadDefaultTheme()
})
</script>

<style scoped>
.theme-translation-manager {
  @apply p-6 max-w-7xl mx-auto;
}

.page-header {
  @apply flex items-center justify-between mb-6;
}

.header-content h1 {
  @apply text-2xl font-semibold text-gray-900 dark:text-white;
}

.header-description {
  @apply text-sm text-gray-600 dark:text-gray-400 mt-1;
}

.header-actions {
  @apply flex gap-2;
}

.quick-start-alert {
  @apply mb-6;
}

.quick-start-steps {
  @apply list-decimal list-inside space-y-1 text-sm;
}

.language-selection-card,
.translation-editor-card,
.stats-card {
  @apply mb-6;
}

.card-header {
  @apply flex items-center justify-between;
}

.editor-actions {
  @apply flex gap-2;
}

.empty-state {
  @apply my-12;
}
</style>