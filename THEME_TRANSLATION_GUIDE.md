# Theme Translation System - LovAERP

## Overview
Sistem Theme Translation memungkinkan pengelolaan terjemahan untuk file `theme.json` yang mendukung multi-bahasa dalam aplikasi LovAERP.

## Struktur File

### 1. Theme JSON Format
File theme utama: `/public/uploads/theme/theme.json`
```json
{
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
    "categories": {
      "primary": "Primary Colors",
      "secondary": "Secondary Colors"
    },
    "components": {
      "button": "Button Styles",
      "card": "Card Components"
    },
    "modes": {
      "light": "Light Mode",
      "dark": "Dark Mode"
    }
  },
  "cssVars": {
    "theme": {
      "font-sans": "Inter, system-ui, sans-serif",
      "radius": "0.5rem"
    },
    "light": {
      "background": "0 0% 100%",
      "foreground": "222.2 84% 4.9%",
      "primary": "221.2 83.2% 53.3%"
    },
    "dark": {
      "background": "222.2 84% 4.9%",
      "foreground": "210 40% 98%",
      "primary": "217.2 91.2% 59.8%"
    }
  },
  "metadata": {
    "createdAt": "2024-01-15T10:00:00Z",
    "tags": ["professional", "blue", "business"],
    "category": "business",
    "supportedModes": ["light", "dark", "auto"]
  }
}
```

### 2. Komponen Utama

#### ThemeTranslationManager.vue
- **Lokasi**: `/resources/js/Pages/Settings/ThemeTranslationManager.vue`
- **Fungsi**: Halaman utama untuk mengelola terjemahan theme
- **Fitur**:
  - Load theme dari file atau fallback
  - Pilih bahasa sumber dan target
  - Statistik progress terjemahan
  - Quick start guide
  - Integrasi dengan ThemeTranslationEditor

#### ThemeTranslationEditor.vue
- **Lokasi**: `/resources/js/Components/Theme/ThemeTranslationEditor.vue`
- **Fungsi**: Komponen editor untuk mengedit terjemahan
- **Fitur**:
  - Load theme dari file atau text input
  - Filter dan search terjemahan
  - Edit terjemahan dalam tabel
  - Export ke JSON/CSV
  - Auto-translate (simulasi)

#### ThemeTranslationUtils.js
- **Lokasi**: `/resources/js/Utils/ThemeTranslationUtils.js`
- **Fungsi**: Utility functions untuk mengelola terjemahan
- **Methods**:
  - `extractTranslatableTexts()`: Ekstrak teks yang bisa diterjemahkan
  - `applyTranslations()`: Terapkan terjemahan ke theme
  - `validateThemeStructure()`: Validasi struktur theme
  - `createEmptyTranslation()`: Buat template terjemahan kosong
  - `exportToCSV()`: Export ke format CSV
  - `importFromCSV()`: Import dari format CSV

### 3. File Terjemahan

#### Bahasa Indonesia
- **Lokasi**: `/resources/js/locales/id/themeTranslation.json`
- **Konten**: Terjemahan UI untuk Theme Translation Manager

#### Bahasa Inggris
- **Lokasi**: `/resources/js/locales/en/themeTranslation.json`
- **Konten**: Terjemahan UI untuk Theme Translation Manager

### 4. Routing
Routes ditambahkan di `/resources/js/Router/settings.js`:
```javascript
{
  path: '/settings/theme-manager',
  name: 'ThemeManager',
  component: () => import('@/Pages/Settings/ThemeManager.vue'),
  meta: {
    title: 'Theme Manager',
    breadcrumb: [
      { text: 'Settings', to: '/settings' },
      { text: 'System', to: '/settings/system' },
      { text: 'Theme Manager' }
    ]
  }
},
{
  path: '/settings/theme-translation',
  name: 'ThemeTranslationManager',
  component: () => import('@/Pages/Settings/ThemeTranslationManager.vue'),
  meta: {
    title: 'Theme Translation Manager',
    breadcrumb: [
      { text: 'Settings', to: '/settings' },
      { text: 'System', to: '/settings/system' },
      { text: 'Theme Translation' }
    ]
  }
}
```

## Cara Penggunaan

### 1. Akses Theme Translation Manager
- Buka aplikasi LovAERP
- Navigasi ke Settings > System > Theme Translation Manager
- URL: `/settings/theme-translation`

### 2. Load Theme
- Sistem akan otomatis load theme dari `/uploads/theme/theme.json`
- Jika file tidak ada, akan menggunakan fallback theme
- Bisa juga load theme manual melalui file upload atau text input

### 3. Pilih Bahasa
- Pilih bahasa sumber (default: English)
- Pilih bahasa target (default: Bahasa Indonesia)

### 4. Edit Terjemahan
- Gunakan filter dan search untuk menemukan teks
- Edit terjemahan langsung di tabel
- Lihat progress terjemahan secara real-time

### 5. Export/Import
- Export terjemahan ke format JSON atau CSV
- Import terjemahan dari file CSV
- Save terjemahan ke local storage

## Fitur Utama

### 1. Multi-format Support
- JSON theme files
- CSV export/import
- Text input untuk theme JSON

### 2. Translation Management
- Visual editor dengan tabel
- Search dan filter
- Progress tracking
- Auto-translate simulation

### 3. Theme Integration
- Load dari file system
- Fallback theme support
- Real-time preview
- CSS variables integration

### 4. User Experience
- Responsive design
- Element Plus UI components
- Multi-language interface
- Quick start guide

## Technical Details

### Dependencies
- Vue 3 Composition API
- Element Plus UI Framework
- Vue I18n untuk internationalization
- Vue Router untuk routing

### File Structure
```
resources/js/
├── Pages/Settings/
│   ├── ThemeManager.vue
│   └── ThemeTranslationManager.vue
├── Components/Theme/
│   └── ThemeTranslationEditor.vue
├── Utils/
│   └── ThemeTranslationUtils.js
├── Services/
│   └── ThemeService.js
├── composables/
│   └── useTheme.js
└── locales/
    ├── en/
    │   ├── theme.json
    │   └── themeTranslation.json
    └── id/
        ├── theme.json
        └── themeTranslation.json
```

### Sample Theme Files
- `/public/uploads/theme/theme.json` - Theme utama
- `/public/uploads/theme/theme-with-translation.json` - Theme dengan terjemahan lengkap
- `/public/uploads/theme/theme-with-translation-id.json` - Theme terjemahan Indonesia

## Troubleshooting

### Theme tidak ter-load
1. Pastikan file `/public/uploads/theme/theme.json` ada
2. Cek format JSON valid
3. Periksa console browser untuk error

### Terjemahan tidak tersimpan
1. Cek local storage browser
2. Pastikan tidak ada error JavaScript
3. Reload halaman dan coba lagi

### Export/Import error
1. Pastikan format file sesuai (JSON/CSV)
2. Cek struktur data
3. Periksa permission file system

## Future Enhancements

1. **Backend Integration**: Simpan terjemahan ke database
2. **Real Auto-translate**: Integrasi dengan Google Translate API
3. **Version Control**: Track perubahan terjemahan
4. **Collaboration**: Multi-user editing
5. **Theme Marketplace**: Share dan download theme
6. **Advanced Preview**: Live preview dengan theme switching

---

**Dibuat oleh**: LovAERP Development Team  
**Tanggal**: 2024-01-15  
**Versi**: 1.0.0