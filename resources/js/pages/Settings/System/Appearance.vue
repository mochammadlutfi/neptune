<template>
  <div>
    <el-card class="shadow-sm sm:rounded-lg">
      <template #header>
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ $t('settings.system.appearance.title') }}
          </h3>
          <el-button @click="resetTheme" size="small" type="danger" plain>
            {{ $t('common.actions.reset') }}
          </el-button>
        </div>
      </template>

      <div class="space-y-8">
        <!-- Theme Mode -->
        <div class="space-y-3">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $t('settings.system.appearance.theme') }}
          </label>
          <el-radio-group v-model="currentTheme.mode" @change="setMode">
            <el-radio-button value="light">
              <icon icon="mingcute:sun-line" class="mr-2" />
              {{ $t('settings.system.appearance.light_mode') }}
            </el-radio-button>
            <el-radio-button value="dark">
              <icon icon="mingcute:moon-line" class="mr-2" />
              {{ $t('settings.system.appearance.dark_mode') }}
            </el-radio-button>
          </el-radio-group>
        </div>

        <!-- Primary Color -->
        <div class="space-y-3">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $t('settings.system.appearance.primary_color') }}
          </label>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div
              v-for="(preset, key) in themePresets"
              :key="key"
              @click="setPrimaryColor(preset.primaryColor)"
              :class="[
                'relative p-4 rounded-lg border-2 cursor-pointer transition-all hover:scale-105',
                currentTheme.primaryColor === preset.primaryColor
                  ? 'border-primary ring-2 ring-primary/20'
                  : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'
              ]"
            >
              <div class="flex items-center space-x-3">
                <div
                  class="w-6 h-6 rounded-full"
                  :style="{ backgroundColor: preset.primaryColor }"
                ></div>
                <span class="text-sm font-medium">{{ preset.name }}</span>
              </div>
              <div
                v-if="currentTheme.primaryColor === preset.primaryColor"
                class="absolute top-2 right-2"
              >
                <icon icon="mingcute:check-circle-fill" class="text-primary w-5 h-5" />
              </div>
            </div>
          </div>
        </div>

        <!-- Border Radius -->
        <div class="space-y-3">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Border Radius
          </label>
          <el-select v-model="currentTheme.radius" @change="setRadius" class="w-full">
            <el-option
              v-for="(option, key) in radiusOptions"
              :key="key"
              :label="option.name"
              :value="option.value"
            />
          </el-select>
          <div class="mt-2">
            <div
              class="w-16 h-16 bg-primary/20 border-2 border-primary"
              :style="{ borderRadius: currentTheme.radius }"
            ></div>
          </div>
        </div>

        <!-- Font Family -->
        <div class="space-y-3">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Font Family
          </label>
          <el-select v-model="currentTheme.fontFamily" @change="setFontFamily" class="w-full">
            <el-option
              v-for="(font, key) in fontFamilies"
              :key="key"
              :label="font.name"
              :value="key"
            >
              <span :style="{ fontFamily: font.value }">{{ font.name }}</span>
            </el-option>
          </el-select>
        </div>

        <!-- Font Size -->
        <div class="space-y-3">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Font Size
          </label>
          <div class="flex items-center space-x-4">
            <el-slider
              v-model="fontSizeValue"
              @change="handleFontSizeChange"
              :min="12"
              :max="18"
              :step="1"
              :format-tooltip="(val) => `${val}px`"
              class="flex-1"
            />
            <span class="text-sm text-gray-500 min-w-[40px]">{{ fontSizeValue }}px</span>
          </div>
        </div>

        <!-- Font Weight -->
        <div class="space-y-3">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Font Weight
          </label>
          <el-select v-model="currentTheme.fontWeight" @change="setFontWeight" class="w-full">
            <el-option
              v-for="(weight, key) in fontWeightOptions"
              :key="key"
              :label="weight.name"
              :value="key"
            >
              <span :style="{ fontWeight: weight.value }">{{ weight.name }}</span>
            </el-option>
          </el-select>
        </div>

        <!-- Spacing -->
        <div class="space-y-3">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Spacing
          </label>
          <el-radio-group v-model="currentTheme.spacing" @change="setSpacing">
            <el-radio-button
              v-for="(option, key) in spacingOptions"
              :key="key"
              :value="key"
            >
              {{ option.name }}
            </el-radio-button>
          </el-radio-group>
        </div>

        <!-- Shadow -->
        <div class="space-y-3">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Shadow
          </label>
          <el-select v-model="currentTheme.shadow" @change="setShadow" class="w-full">
            <el-option
              v-for="(option, key) in shadowOptions"
              :key="key"
              :label="option.name"
              :value="key"
            />
          </el-select>
          <div class="mt-2">
            <div
              class="w-16 h-16 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg"
              :style="{ boxShadow: shadowOptions[currentTheme.shadow]?.value }"
            ></div>
          </div>
        </div>

        <!-- Import CSS Theme -->
        <div class="space-y-3">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Import CSS Theme
          </label>
          <div class="space-y-3">
            <el-alert
              title="Tip: Anda dapat menyalin CSS dari tweakcn.com atau membuat CSS custom sendiri"
              type="info"
              :closable="false"
              show-icon
            />
            <el-input
              v-model="cssInput"
              type="textarea"
              :rows="8"
              placeholder="Paste CSS dari tweakcn.com atau CSS custom di sini..."
              class="font-mono text-sm"
            />
            <div class="flex space-x-2">
              <el-button @click="clearCSS" size="small">
                Clear
              </el-button>
              <el-button 
                @click="parseCSS" 
                type="primary" 
                size="small"
                :loading="parsing"
                :disabled="!cssInput.trim()"
              >
                Parse & Apply CSS
              </el-button>
              <el-button @click="showCSSExample" size="small" type="info">
                Contoh CSS
              </el-button>
            </div>
            <div v-if="lastParsedTheme" class="text-sm text-green-600 dark:text-green-400">
              ✓ CSS berhasil diparsing dan diterapkan
            </div>
          </div>
        </div>

        <!-- Preview Section -->
        <div class="space-y-3">
          <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Preview
          </label>
          <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-lg bg-background">
            <div class="space-y-4">
              <h4 class="text-lg font-semibold text-foreground">Sample Content</h4>
              <p class="text-muted-foreground">
                This is a preview of how your theme will look. You can see the font family, 
                size, weight, and spacing applied here.
              </p>
              <div class="flex space-x-3">
                <el-button type="primary">Primary Button</el-button>
                <el-button>Default Button</el-button>
                <el-button type="success">Success Button</el-button>
              </div>
              <el-card class="mt-4">
                <template #header>
                  <span>Sample Card</span>
                </template>
                <p>This card shows how the border radius and shadow settings affect components.</p>
              </el-card>
            </div>
          </div>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Icon } from '@iconify/vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { useTheme } from '@/composables/useTheme'
import { CSSThemeParser } from '@/utils/CSSThemeParser'

// Emit untuk parent component
const emit = defineEmits(['childinit'])

// Theme composable
const {
  theme,
  themePresets,
  fontFamilies,
  radiusOptions,
  spacingOptions,
  shadowOptions,
  fontWeightOptions,
  loadTheme,
  setMode,
  setPrimaryColor,
  setRadius,
  setFontFamily,
  setFontSize,
  setSpacing,
  setShadow,
  setFontWeight,
  resetTheme: originalResetTheme
} = useTheme()

// Override resetTheme to also reset CSS input
const resetTheme = () => {
  // Reset CSS input and parsed theme
  cssInput.value = ''
  lastParsedTheme.value = null
  
  // Remove custom CSS variables
  const root = document.documentElement
  const customVars = ['background', 'foreground', 'card', 'card-foreground', 'popover', 'popover-foreground', 
                     'secondary', 'secondary-foreground', 'muted', 'muted-foreground', 'accent', 'accent-foreground',
                     'destructive', 'destructive-foreground', 'border', 'input', 'ring']
  customVars.forEach(varName => {
    root.style.removeProperty(`--${varName}`)
  })
  
  // Call original reset function
  originalResetTheme()
}

// Current theme reactive reference
const currentTheme = computed(() => theme.value)

// Font size slider value
const fontSizeValue = ref(14)

// CSS parsing variables
const parsing = ref(false)
const cssInput = ref('')
const lastParsedTheme = ref(null)

// Handle font size change
const handleFontSizeChange = (value) => {
  setFontSize(`${value}px`)
}

// Watch for font size changes
watch(() => currentTheme.value.fontSize, (newSize) => {
  fontSizeValue.value = parseInt(newSize.replace('px', ''))
}, { immediate: true })

// CSS parsing functions
const parseCSS = async () => {
  if (!cssInput.value.trim()) {
    ElMessage.warning('Silakan masukkan CSS terlebih dahulu')
    return
  }

  parsing.value = true
  
  try {
    const parser = new CSSThemeParser()
    
    if (!parser.validateCSS(cssInput.value)) {
      throw new Error('Format CSS tidak valid')
    }

    const parsedTheme = parser.parseCSS(cssInput.value)
    lastParsedTheme.value = parsedTheme

    // Apply parsed theme to current settings
    applyParsedTheme(parsedTheme)
    
    // Apply CSS variables to document
    applyCSSVariables(parsedTheme)
    
    ElMessage.success('CSS berhasil diparsing dan diterapkan!')
    
  } catch (error) {
    console.error('Error parsing CSS:', error)
    ElMessage.error(`Error parsing CSS: ${error.message}`)
  } finally {
    parsing.value = false
  }
}

const applyParsedTheme = (parsedTheme) => {
  // Apply primary color if found
  if (parsedTheme.primaryColor) {
    setPrimaryColor(parsedTheme.primaryColor)
  }
  
  // Apply border radius if found
  if (parsedTheme.borderRadius) {
    setRadius(parsedTheme.borderRadius)
  }
  
  // Apply font family if found
  if (parsedTheme.fontFamily) {
    const fontKey = Object.keys(fontFamilies.value).find(key => 
      fontFamilies.value[key].value.includes(parsedTheme.fontFamily)
    )
    if (fontKey) {
      setFontFamily(fontKey)
    }
  }
}

const applyCSSVariables = (parsedTheme) => {
  const root = document.documentElement
  
  // Apply all CSS variables from parsed theme
  Object.entries(parsedTheme.cssVars || {}).forEach(([key, value]) => {
    root.style.setProperty(`--${key}`, value)
  })
}

const convertToHex = (color) => {
  if (color.startsWith('hsl(')) {
    return hslToHex(color)
  }
  return color
}

const hslToHex = (hsl) => {
  const match = hsl.match(/hsl\((\d+),\s*(\d+)%,\s*(\d+)%\)/)
  if (!match) return hsl
  
  const h = parseInt(match[1]) / 360
  const s = parseInt(match[2]) / 100
  const l = parseInt(match[3]) / 100
  
  const hue2rgb = (p, q, t) => {
    if (t < 0) t += 1
    if (t > 1) t -= 1
    if (t < 1/6) return p + (q - p) * 6 * t
    if (t < 1/2) return q
    if (t < 2/3) return p + (q - p) * (2/3 - t) * 6
    return p
  }
  
  let r, g, b
  if (s === 0) {
    r = g = b = l
  } else {
    const q = l < 0.5 ? l * (1 + s) : l + s - l * s
    const p = 2 * l - q
    r = hue2rgb(p, q, h + 1/3)
    g = hue2rgb(p, q, h)
    b = hue2rgb(p, q, h - 1/3)
  }
  
  const toHex = (c) => {
    const hex = Math.round(c * 255).toString(16)
    return hex.length === 1 ? '0' + hex : hex
  }
  
  return `#${toHex(r)}${toHex(g)}${toHex(b)}`
}

const clearCSS = () => {
  cssInput.value = ''
  lastParsedTheme.value = null
}

const showCSSExample = () => {
  cssInput.value = `:root {
  --background: 0 0% 100%;
  --foreground: 222.2 84% 4.9%;
  --card: 0 0% 100%;
  --card-foreground: 222.2 84% 4.9%;
  --popover: 0 0% 100%;
  --popover-foreground: 222.2 84% 4.9%;
  --primary: 221.2 83.2% 53.3%;
  --primary-foreground: 210 40% 98%;
  --secondary: 210 40% 96%;
  --secondary-foreground: 222.2 84% 4.9%;
  --muted: 210 40% 96%;
  --muted-foreground: 215.4 16.3% 46.9%;
  --accent: 210 40% 96%;
  --accent-foreground: 222.2 84% 4.9%;
  --destructive: 0 84.2% 60.2%;
  --destructive-foreground: 210 40% 98%;
  --border: 214.3 31.8% 91.4%;
  --input: 214.3 31.8% 91.4%;
  --ring: 221.2 83.2% 53.3%;
  --radius: 0.5rem;
}

.dark {
  --background: 222.2 84% 4.9%;
  --foreground: 210 40% 98%;
  --card: 222.2 84% 4.9%;
  --card-foreground: 210 40% 98%;
  --popover: 222.2 84% 4.9%;
  --popover-foreground: 210 40% 98%;
  --primary: 217.2 91.2% 59.8%;
  --primary-foreground: 222.2 84% 4.9%;
  --secondary: 217.2 32.6% 17.5%;
  --secondary-foreground: 210 40% 98%;
  --muted: 217.2 32.6% 17.5%;
  --muted-foreground: 215 20.2% 65.1%;
  --accent: 217.2 32.6% 17.5%;
  --accent-foreground: 210 40% 98%;
  --destructive: 0 62.8% 30.6%;
  --destructive-foreground: 210 40% 98%;
  --border: 217.2 32.6% 17.5%;
  --input: 217.2 32.6% 17.5%;
  --ring: 224.3 76.3% 94.1%;
}`
}

// Initialize component
onMounted(() => {
  emit('childinit', t('settings.system.appearance.title'))
  loadTheme()
  
  // Load saved CSS input if exists
  const savedCSS = localStorage.getItem('theme-css-input')
  const savedParsedTheme = localStorage.getItem('theme-parsed-theme')
  
  if (savedCSS) {
    cssInput.value = savedCSS
  }
  
  if (savedParsedTheme) {
    try {
      lastParsedTheme.value = JSON.parse(savedParsedTheme)
      applyCSSVariables(lastParsedTheme.value)
    } catch (error) {
      console.error('Error loading saved parsed theme:', error)
    }
  }
})

// Watch for CSS input changes to save to localStorage
watch(cssInput, (newValue) => {
  localStorage.setItem('theme-css-input', newValue)
})

watch(lastParsedTheme, (newValue) => {
  if (newValue) {
    localStorage.setItem('theme-parsed-theme', JSON.stringify(newValue))
  } else {
    localStorage.removeItem('theme-parsed-theme')
  }
})
</script>

<style scoped>
/* Custom styles untuk preview section */
.preview-section {
  transition: all 0.3s ease;
}
</style>