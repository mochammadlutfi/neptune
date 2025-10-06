<template>
    <div>
        <!-- Enhanced Preview untuk mode multiple -->
        <template v-if="multiple && selectedItems.length">
            <div class="mb-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-sm font-semibold text-gray-800">
                        {{ $t('settings.media.selected_files') }} 
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                            {{ selectedItems.length }}{{ max ? `/${max}` : '' }}
                        </span>
                    </label>
                    <el-button size="small" @click="clearAll" type="danger" plain>
                        <Icon icon="mingcute:delete-2-line" class="mr-1" />
                        {{ $t('settings.media.clear_all') }}
                    </el-button>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-3">
                    <div v-for="item in selectedItems" :key="item.id" :class="previewClass"
                        class="relative aspect-square border-2 border-blue-200 rounded-lg overflow-hidden group bg-gradient-to-br from-blue-50 to-blue-100 hover:border-blue-400 hover:shadow-lg transition-all duration-300">
                        <component :is="isImage(item.mime_type) ? 'img' : 'div'"
                            :src="isImage(item.mime_type) ? item.original_url : undefined"
                            class="w-full h-full object-cover"
                            :class="{ 'flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100': !isImage(item.mime_type) }"
                        >
                            <div v-if="!isImage(item.mime_type)" class="flex flex-col items-center justify-center p-2">
                                <Icon 
                                    :icon="getFileIcon(item.mime_type)" 
                                    class="text-2xl sm:text-3xl mb-1"
                                    :style="{ color: getFileIconColor(item.mime_type) }"
                                />
                                <span class="text-xs font-bold text-gray-600 uppercase tracking-wide">
                                    {{ getFileExtension(item.filename) }}
                                </span>
                            </div>
                        </component>
                        
                        <!-- File info overlay untuk non-image -->
                        <div v-if="!isImage(item.mime_type)"
                            class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent text-white p-2">
                            <div class="text-xs font-medium truncate">{{ item.filename }}</div>
                            <div class="text-xs text-gray-200">{{ item.readable_size }}</div>
                        </div>
                        
                        <!-- Image overlay dengan info -->
                        <div v-else
                            class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all duration-300 flex items-end">
                            <div class="w-full bg-gradient-to-t from-black/70 to-transparent text-white p-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="text-xs font-medium truncate">{{ item.filename }}</div>
                                <div class="text-xs text-gray-200">{{ item.readable_size }}</div>
                            </div>
                        </div>
                        
                        <!-- Remove button -->
                        <button type="button" @click="removeSelected(item.id)"
                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-lg flex items-center justify-center transform hover:scale-110">
                            <Icon icon="mingcute:close-fill" class="text-sm" />
                        </button>
                        
                        <!-- Selection indicator -->
                        <div class="absolute top-2 left-2 w-5 h-5 bg-green-500 text-white rounded-full flex items-center justify-center shadow-md">
                            <Icon icon="mingcute:check-fill" class="text-xs" />
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Enhanced Single Preview -->
        <template v-else-if="selectedItems.length === 1">
            <div class="mb-4">
                <div class="relative w-28 h-28 border-2 border-blue-200 rounded-lg overflow-hidden bg-gradient-to-br from-blue-50 to-blue-100 group hover:border-blue-400 hover:shadow-lg transition-all duration-300">
                    <component :is="isImage(selectedItems[0].mime_type) ? 'img' : 'div'"
                        :src="isImage(selectedItems[0].mime_type) ? selectedItems[0].original_url : undefined"
                        class="w-full h-full object-cover"
                        :class="{ 'flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100': !isImage(selectedItems[0].mime_type) }"
                    >
                        <div v-if="!isImage(selectedItems[0].mime_type)" class="flex flex-col items-center justify-center p-3">
                            <Icon 
                                :icon="getFileIcon(selectedItems[0].mime_type)" 
                                class="text-4xl mb-2"
                                :style="{ color: getFileIconColor(selectedItems[0].mime_type) }"
                            />
                            <span class="text-sm font-bold text-gray-600 uppercase tracking-wide">
                                {{ getFileExtension(selectedItems[0].filename) }}
                            </span>
                        </div>
                    </component>
                    
                    <!-- File info overlay -->
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent text-white p-2">
                        <div class="text-xs font-medium truncate">{{ selectedItems[0].filename }}</div>
                        <div class="text-xs text-gray-200">{{ selectedItems[0].readable_size }}</div>
                    </div>
                    
                    <!-- Remove button -->
                    <button type="button" @click="removeSelected(selectedItems[0].id)"
                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full opacity-0 group-hover:opacity-100 transition-all duration-300 shadow-lg flex items-center justify-center transform hover:scale-110">
                        <Icon icon="mingcute:close-fill" class="text-sm" />
                    </button>
                </div>
            </div>
        </template>

        <!-- Enhanced Button untuk membuka modal -->
        <template v-else>
            <div v-if="type == 'picture'" :class="buttonClass || 'w-full h-32'">
                <button type="button"
                    class="relative w-full h-full border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-400 flex flex-col items-center justify-center transition-all duration-300 group hover:bg-gradient-to-br hover:from-blue-50 hover:to-blue-100"
                    @click.prevent="onOpenModal">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-2 group-hover:bg-blue-200 transition-colors duration-300">
                        <Icon icon="fluent:image-globe-24-regular" class="text-2xl text-blue-500 group-hover:text-blue-600" />
                    </div>
                    <span class="text-sm text-gray-600 group-hover:text-blue-700 font-medium">{{ $t('settings.media.select_image') }}</span>
                    <span class="text-xs text-gray-400 group-hover:text-blue-500 mt-1">{{ $t('common.actions.browse') }}</span>
                </button>
            </div>
            <el-button 
                class="flex items-center justify-center gap-2 transition-all duration-300 hover:shadow-md" 
                @click.prevent="onOpenModal" 
                v-else
                type="primary"
                plain
            >
                <Icon icon="mingcute:attachment-line" />
                {{ $t('settings.media.browse_files') }}
            </el-button>
        </template>

        <!-- Professional Enhanced Modal -->
        <el-dialog 
            v-model="modalOpen" 
            class="media-picker-dialog" 
            :close-on-click-modal="false"
            destroy-on-close
            width="95%"
            :style="{ maxWidth: '1400px' }"
        >
            <template #header>
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center shadow-md">
                            <Icon icon="mingcute:pic-line" class="text-white text-xl" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $t('settings.media.media_library') }}</h3>
                            <p class="text-sm text-gray-500">{{ $t('settings.media.manage_files') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <el-badge :value="selectedItems.length" :hidden="selectedItems.length === 0" type="primary" class="mr-2">
                            <Icon icon="mingcute:check-circle-line" class="text-xl text-blue-500" />
                        </el-badge>
                        <div class="text-sm text-gray-600">
                            {{ $t('settings.media.selected_count', { count: selectedItems.length }) }}
                        </div>
                    </div>
                </div>
            </template>

            <div class="flex h-[75vh] bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg overflow-hidden shadow-inner">
                <!-- Main Content Area -->
                <div class="flex-1 flex flex-col bg-white rounded-lg shadow-sm">
                    <!-- Enhanced Filters and Controls -->
                    <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex flex-col gap-4">
                            <!-- Search Row -->
                            <div class="flex flex-col lg:flex-row gap-4">
                                <!-- Search -->
                                <div class="flex-1">
                                    <el-input 
                                        v-model="params.q"
                                        :placeholder="$t('settings.media.search_files')"
                                        clearable
                                        @input="debouncedSearch"
                                        class="search-input"
                                        size="large"
                                    >
                                        <template #prefix>
                                            <Icon icon="mingcute:search-line" class="text-gray-400" />
                                        </template>
                                    </el-input>
                                </div>

                                <!-- Filter Controls -->
                                <div class="flex items-center gap-3 flex-wrap">
                                    <!-- Filter by Type -->
                                    <el-select 
                                        v-model="params.filter_type" 
                                        :placeholder="$t('settings.media.filter_type')"
                                        @change="refetch"
                                        class="w-full lg:w-auto"
                                        style="min-width: 160px"
                                        size="large"
                                    >
                                        <el-option :label="$t('settings.media.all_files')" value="">
                                            <div class="flex items-center gap-2">
                                                <Icon icon="mingcute:file-line" class="text-gray-400" />
                                                {{ $t('settings.media.all_files') }}
                                            </div>
                                        </el-option>
                                        <el-option :label="$t('settings.media.images')" value="image">
                                            <div class="flex items-center gap-2">
                                                <Icon icon="mingcute:image-line" class="text-green-500" />
                                                {{ $t('settings.media.images') }}
                                            </div>
                                        </el-option>
                                        <el-option :label="$t('settings.media.documents')" value="document">
                                            <div class="flex items-center gap-2">
                                                <Icon icon="mingcute:file-text-line" class="text-blue-500" />
                                                {{ $t('settings.media.documents') }}
                                            </div>
                                        </el-option>
                                        <el-option :label="$t('settings.media.videos')" value="video">
                                            <div class="flex items-center gap-2">
                                                <Icon icon="mingcute:video-line" class="text-purple-500" />
                                                {{ $t('settings.media.videos') }}
                                            </div>
                                        </el-option>
                                        <el-option :label="$t('settings.media.audio')" value="audio">
                                            <div class="flex items-center gap-2">
                                                <Icon icon="mingcute:music-line" class="text-orange-500" />
                                                {{ $t('settings.media.audio') }}
                                            </div>
                                        </el-option>
                                    </el-select>

                                    <!-- View Mode Toggle -->
                                    <el-button-group class="hidden lg:block">
                                        <el-button 
                                            :type="viewMode === 'grid' ? 'primary' : ''"
                                            @click="viewMode = 'grid'"
                                            size="large"
                                        >
                                            <Icon icon="mingcute:grid-line" class="mr-1" />
                                            Grid
                                        </el-button>
                                        <el-button 
                                            :type="viewMode === 'list' ? 'primary' : ''"
                                            @click="viewMode = 'list'"
                                            size="large"
                                        >
                                            <Icon icon="mingcute:list-check-line" class="mr-1" />
                                            List
                                        </el-button>
                                    </el-button-group>

                                    <!-- Refresh Button -->
                                    <el-button @click="refetch" :loading="isLoading" circle size="large" class="shadow-sm">
                                        <Icon icon="mingcute:refresh-1-line" />
                                    </el-button>
                                </div>
                            </div>

                            <!-- Upload Area Toggle -->
                            <div class="w-full">
                                <el-button 
                                    @click="showUploadArea = !showUploadArea"
                                    class="w-full"
                                    :type="showUploadArea ? 'success' : 'primary'"
                                    size="large"
                                    :class="{ 'shadow-md': showUploadArea }"
                                >
                                    <Icon :icon="showUploadArea ? 'mingcute:upload-2-fill' : 'mingcute:add-circle-line'" class="mr-2" />
                                    {{ showUploadArea ? $t('settings.media.hide_upload') : $t('settings.media.upload_files') }}
                                    <Icon :icon="showUploadArea ? 'mingcute:up-line' : 'mingcute:down-line'" class="ml-auto" />
                                </el-button>
                                
                                <!-- Professional Upload Area -->
                                <el-collapse-transition>
                                    <div v-show="showUploadArea" class="mt-4">
                                        <div class="upload-area-container">
                                            <el-upload 
                                                class="upload-area" 
                                                drag 
                                                :http-request="onUpload" 
                                                :show-file-list="false" 
                                                multiple
                                                :accept="acceptTypes"
                                            >
                                                <div class="p-6 lg:p-8 text-center">
                                                    <div class="w-14 h-14 lg:w-16 lg:h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                                                        <Icon icon="mingcute:cloud-upload-line" class="text-2xl lg:text-3xl text-blue-500" />
                                                    </div>
                                                    <div class="text-base lg:text-lg font-bold text-gray-800 mb-2">{{ $t('settings.media.drop_files') }}</div>
                                                    <div class="text-sm text-gray-600 mb-1">{{ $t('settings.media.or_click_upload') }}</div>
                                                    <div class="text-xs text-gray-500 mt-2">{{ $t('settings.media.max_file_size') }}</div>
                                                </div>
                                            </el-upload>
                                        </div>
                                    </div>
                                </el-collapse-transition>
                            </div>

                            <!-- Mobile View Mode Toggle -->
                            <div class="flex lg:hidden justify-center">
                                <el-button-group>
                                    <el-button 
                                        :type="viewMode === 'grid' ? 'primary' : ''"
                                        @click="viewMode = 'grid'"
                                        size="large"
                                    >
                                        <Icon icon="mingcute:grid-line" class="mr-2" />
                                        Grid
                                    </el-button>
                                    <el-button 
                                        :type="viewMode === 'list' ? 'primary' : ''"
                                        @click="viewMode = 'list'"
                                        size="large"
                                    >
                                        <Icon icon="mingcute:list-check-line" class="mr-2" />
                                        List
                                    </el-button>
                                </el-button-group>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Media Grid/List -->
                    <div class="flex-1 overflow-auto p-4">
                        <!-- Enhanced Loading State -->
                        <div v-if="isLoading" class="flex flex-col items-center justify-center h-64">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mb-4 shadow-md">
                                <Icon icon="mingcute:loading-line" class="animate-spin text-2xl text-blue-500" />
                            </div>
                            <div class="text-lg font-semibold text-gray-800">{{ $t('settings.media.loading') }}</div>
                            <div class="text-sm text-gray-500 mt-1">{{ $t('settings.media.loading_description') }}</div>
                        </div>

                        <!-- Empty State -->
                        <div v-else-if="!mediaList?.data?.length" class="flex flex-col items-center justify-center h-64">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                                <Icon icon="mingcute:file-search-line" class="text-4xl text-gray-400" />
                            </div>
                            <h3 class="text-xl font-bold text-gray-700 mb-3">{{ $t('settings.media.no_files') }}</h3>
                            <p class="text-gray-500 mb-4 text-center max-w-md">{{ $t('settings.media.upload_first_file') }}</p>
                            <el-button @click="showUploadArea = true" type="primary" size="large">
                                <Icon icon="mingcute:upload-line" class="mr-2" />
                                {{ $t('settings.media.upload_files') }}
                            </el-button>
                        </div>

                        <!-- Enhanced Grid View -->
                        <div v-else-if="viewMode === 'grid'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-8 gap-4">
                            <div 
                                v-for="item in mediaList?.data" 
                                :key="item.id" 
                                class="media-item group relative aspect-square border-2 rounded-xl cursor-pointer transition-all duration-300 overflow-hidden transform hover:scale-105 hover:shadow-xl"
                                :class="{ 
                                    'border-blue-500 bg-blue-50 shadow-lg ring-2 ring-blue-200 scale-105': isSelected(item.id),
                                    'border-gray-200 hover:border-blue-300 bg-white': !isSelected(item.id)
                                }"
                                @click="toggleSelect(item)"
                                @dblclick="selectedForInfo = item"
                            >
                                <!-- Enhanced Media Preview -->
                                <div class="w-full h-full overflow-hidden">
                                    <component 
                                        :is="isImage(item.mime_type) ? 'img' : 'div'"
                                        :src="isImage(item.mime_type) ? item.original_url : undefined"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                        :class="{ 'flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100': !isImage(item.mime_type) }"
                                        loading="lazy"
                                    >
                                        <div v-if="!isImage(item.mime_type)" class="flex flex-col items-center justify-center p-3">
                                            <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-2 shadow-sm" :style="{ backgroundColor: getFileIconColor(item.mime_type) + '20' }">
                                                <Icon 
                                                    :icon="getFileIcon(item.mime_type)" 
                                                    class="text-2xl"
                                                    :style="{ color: getFileIconColor(item.mime_type) }"
                                                />
                                            </div>
                                            <div class="text-xs font-bold text-gray-600 uppercase tracking-wide px-2 py-1 bg-white rounded-full shadow-sm">
                                                {{ getFileExtension(item.filename) }}
                                            </div>
                                        </div>
                                    </component>
                                </div>

                                <!-- Enhanced Selection Indicator -->
                                <div v-if="isSelected(item.id)" 
                                    class="absolute top-3 right-3 w-7 h-7 bg-green-500 text-white rounded-full flex items-center justify-center shadow-lg ring-2 ring-white">
                                    <Icon icon="mingcute:check-fill" class="text-sm" />
                                </div>

                                <!-- Enhanced Action Buttons -->
                                <div class="absolute top-3 left-3 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    <div class="flex gap-2">
                                        <button 
                                            @click.stop="selectedForInfo = item"
                                            class="w-8 h-8 bg-white/90 hover:bg-white backdrop-blur-sm rounded-full flex items-center justify-center transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-110"
                                        >
                                            <Icon icon="mingcute:information-line" class="text-sm text-gray-700" />
                                        </button>
                                        <button 
                                            @click.stop="downloadFile(item)"
                                            class="w-8 h-8 bg-white/90 hover:bg-white backdrop-blur-sm rounded-full flex items-center justify-center transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-110"
                                        >
                                            <Icon icon="mingcute:download-line" class="text-sm text-gray-700" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Enhanced Info Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-end">
                                    <div class="w-full text-white p-3">
                                        <div class="text-sm font-semibold truncate mb-1">{{ item.filename }}</div>
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-200">{{ item.readable_size }}</span>
                                            <span class="text-gray-300">{{ formatDate(item.created_at) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced List View -->
                        <div v-else class="space-y-2">
                            <div 
                                v-for="item in mediaList?.data" 
                                :key="item.id"
                                class="group flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-300 hover:shadow-lg"
                                :class="{ 
                                    'border-blue-500 bg-blue-50 shadow-md ring-2 ring-blue-200': isSelected(item.id),
                                    'border-gray-200 hover:border-blue-300 hover:bg-gray-50 bg-white': !isSelected(item.id)
                                }"
                                @click="toggleSelect(item)"
                                @dblclick="selectedForInfo = item"
                            >
                                <!-- Enhanced Thumbnail -->
                                <div class="w-16 h-16 flex-shrink-0 mr-4 rounded-xl overflow-hidden shadow-md">
                                    <component 
                                        :is="isImage(item.mime_type) ? 'img' : 'div'"
                                        :src="isImage(item.mime_type) ? item.original_url : undefined"
                                        class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                        :class="{ 'flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100': !isImage(item.mime_type) }"
                                        loading="lazy"
                                    >
                                        <div v-if="!isImage(item.mime_type)" class="w-8 h-8 rounded-lg flex items-center justify-center" :style="{ backgroundColor: getFileIconColor(item.mime_type) + '20' }">
                                            <Icon 
                                                :icon="getFileIcon(item.mime_type)" 
                                                class="text-xl"
                                                :style="{ color: getFileIconColor(item.mime_type) }"
                                            />
                                        </div>
                                    </component>
                                </div>

                                <!-- Enhanced File Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-gray-900 truncate text-lg mb-1">{{ item.filename }}</div>
                                    <div class="text-sm text-gray-500 flex items-center space-x-3">
                                        <span class="bg-gray-100 px-2 py-1 rounded-full font-medium">{{ item.readable_size }}</span>
                                        <span class="bg-gray-100 px-2 py-1 rounded-full font-medium">{{ formatFileType(item.mime_type) }}</span>
                                        <span class="text-gray-400">{{ formatDate(item.created_at) }}</span>
                                    </div>
                                </div>

                                <!-- Enhanced Actions -->
                                <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                    <button 
                                        @click.stop="selectedForInfo = item"
                                        class="p-3 text-gray-500 hover:text-blue-600 rounded-xl hover:bg-blue-50 transition-all duration-200 transform hover:scale-110"
                                    >
                                        <Icon icon="mingcute:information-line" class="text-xl" />
                                    </button>
                                    <button 
                                        @click.stop="downloadFile(item)"
                                        class="p-3 text-gray-500 hover:text-green-600 rounded-xl hover:bg-green-50 transition-all duration-200 transform hover:scale-110"
                                    >
                                        <Icon icon="mingcute:download-line" class="text-xl" />
                                    </button>
                                </div>

                                <!-- Enhanced Selection Indicator -->
                                <div v-if="isSelected(item.id)" class="ml-3">
                                    <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center shadow-lg ring-2 ring-white">
                                        <Icon icon="mingcute:check-fill" class="text-sm" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Empty State (already handled above) -->
                    </div>

                    <!-- Enhanced Pagination -->
                    <div v-if="mediaList?.total > mediaList?.per_page" class="p-3 border-t border-gray-100 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                {{ $t('media.showing_files', { 
                                    from: mediaList.from, 
                                    to: mediaList.to, 
                                    total: mediaList.total 
                                }) }}
                            </div>
                            <el-pagination
                                background
                                layout="prev, pager, next"
                                :page-size="mediaList.per_page"
                                :total="mediaList.total"
                                :current-page="mediaList.current_page"
                                @current-change="changePage"
                                small
                            />
                        </div>
                    </div>
                </div>

                <!-- Enhanced Sidebar for Selected Media Info -->
                <div v-if="selectedForInfo" class="w-72 border-l border-gray-200 bg-white flex flex-col">
                    <!-- Sidebar Header -->
                    <div class="p-3 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-gray-900">{{ $t('media.file_details') }}</h3>
                            <button 
                                @click="selectedForInfo = null"
                                class="p-1 hover:bg-white rounded-md transition-colors"
                            >
                                <Icon icon="mingcute:close-line" class="text-gray-500" />
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-auto p-3">
                        <!-- Enhanced Preview -->
                        <div class="mb-4">
                            <div class="aspect-square bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-gray-200 rounded-md overflow-hidden">
                                <component 
                                    :is="isImage(selectedForInfo.mime_type) ? 'img' : 'div'"
                                    :src="isImage(selectedForInfo.mime_type) ? selectedForInfo.original_url : undefined"
                                    class="w-full h-full object-contain"
                                    :class="{ 'flex items-center justify-center': !isImage(selectedForInfo.mime_type) }"
                                >
                                    <div v-if="!isImage(selectedForInfo.mime_type)" class="flex flex-col items-center justify-center p-4">
                                        <Icon 
                                            :icon="getFileIcon(selectedForInfo.mime_type)" 
                                            class="text-6xl mb-3"
                                            :style="{ color: getFileIconColor(selectedForInfo.mime_type) }"
                                        />
                                        <div class="text-sm font-bold text-gray-600 uppercase">
                                            {{ getFileExtension(selectedForInfo.filename) }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ formatFileType(selectedForInfo.mime_type) }}
                                        </div>
                                    </div>
                                </component>
                            </div>
                        </div>

                        <!-- Enhanced File Information -->
                        <div class="space-y-3">
                            <div class="bg-gray-50 rounded-md p-2">
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">{{ $t('media.filename') }}</label>
                                <div class="text-sm text-gray-900 font-medium break-words mt-1">{{ selectedForInfo.filename }}</div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-2">
                                <div class="bg-gray-50 rounded-md p-2">
                                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">{{ $t('media.file_size') }}</label>
                                    <div class="text-sm text-gray-900 font-medium mt-1">{{ selectedForInfo.readable_size }}</div>
                                </div>
                                
                                <div class="bg-gray-50 rounded-md p-2">
                                    <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">{{ $t('media.file_type') }}</label>
                                    <div class="text-sm text-gray-900 font-medium mt-1">{{ formatFileType(selectedForInfo.mime_type) }}</div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 rounded-md p-2">
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">{{ $t('media.uploaded_at') }}</label>
                                <div class="text-sm text-gray-900 font-medium mt-1">{{ formatDate(selectedForInfo.created_at) }}</div>
                            </div>

                            <div v-if="isImage(selectedForInfo.mime_type) && selectedForInfo.dimensions" class="bg-gray-50 rounded-md p-2">
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">{{ $t('media.dimensions') }}</label>
                                <div class="text-sm text-gray-900 font-medium mt-1">{{ selectedForInfo.dimensions }}</div>
                            </div>
                        </div>

                        <!-- Enhanced Actions -->
                        <div class="mt-4 space-y-2">
                            <el-button 
                                :type="isSelected(selectedForInfo.id) ? 'success' : 'primary'"
                                class="w-full"
                                @click="toggleSelect(selectedForInfo)"
                            >
                                <Icon :icon="isSelected(selectedForInfo.id) ? 'mingcute:check-fill' : 'mingcute:add-line'" class="mr-2" />
                                {{ isSelected(selectedForInfo.id) ? $t('media.selected') : $t('media.select') }}
                            </el-button>
                            
                            <el-button 
                                class="w-full"
                                @click="downloadFile(selectedForInfo)"
                                plain
                            >
                                <Icon icon="mingcute:download-line" class="mr-2" />
                                {{ $t('media.download') }}
                            </el-button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Footer -->
            <template #footer>
                <div class="flex items-center justify-between bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-t border-gray-200">
                    <!-- Enhanced Selection Info -->
                    <div class="flex items-center gap-4">
                        <div v-if="selectedItems.length > 0" class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center shadow-md">
                                <Icon icon="mingcute:check-fill" class="text-sm" />
                            </div>
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-800 text-sm">
                                    {{ $t('settings.media.selected_files', { count: selectedItems.length }) }}
                                </span>
                                <span v-if="multiple && max" class="text-xs text-gray-500">
                                    {{ $t('settings.media.max_files', { max: max }) }}
                                </span>
                            </div>
                        </div>
                        <div v-else class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-gray-300 text-white rounded-full flex items-center justify-center">
                                <Icon icon="mingcute:file-line" class="text-sm" />
                            </div>
                            <span class="text-sm text-gray-500 font-medium">
                                {{ $t('settings.media.no_files_selected') }}
                            </span>
                        </div>
                    </div>

                    <!-- Enhanced Action Buttons -->
                    <div class="flex gap-3">
                        <el-button 
                            @click="modalOpen = false"
                            size="large"
                            class="px-6"
                        >
                            <Icon icon="mingcute:close-line" class="mr-2" />
                            {{ $t('common.actions.cancel') }}
                        </el-button>
                        <el-button 
                            type="primary" 
                            @click="onSubmit"
                            :disabled="selectedItems.length === 0"
                            size="large"
                            class="px-6 shadow-md"
                        >
                            <Icon icon="mingcute:check-line" class="mr-2" />
                            {{ $t('settings.media.use_selected') }}
                            <span v-if="selectedItems.length > 0" class="ml-2 bg-white/20 px-2 py-1 rounded-full text-xs">
                                {{ selectedItems.length }}
                            </span>
                        </el-button>
                    </div>
                </div>
            </template>
        </el-dialog>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { Icon } from '@iconify/vue';
import { useQuery } from '@tanstack/vue-query';
import { useI18n } from 'vue-i18n';
import { ElMessage } from 'element-plus';
import _ from 'lodash';

const { t } = useI18n();

const props = defineProps({
    modelValue: [Array, Object],
    size: String,
    multiple: Boolean,
    max: Number,
    type: {
        type: String,
        default: 'picture',
    },
    accept: String,
    buttonClass: String,
    previewClass: String,
    filterType: {
        type: String,
        default: '', // 'image', 'document', 'video', 'audio'
    }
});

const emit = defineEmits(['update:modelValue']);

// Reactive state
const selectedItems = ref([]);
const modalOpen = ref(false);
const viewMode = ref('grid'); // 'grid' or 'list'
const selectedForInfo = ref(null);
const showUploadArea = ref(false);

// Query parameters
const params = ref({ 
    limit: 24, 
    page: 1, 
    q: '', 
    filter_type: props.filterType
});

// Computed properties
const acceptTypes = computed(() => {
    if (props.accept) return props.accept;
    if (props.type === 'picture') return 'image/*';
    return '*/*';
});

// Utility functions
const isImage = (mime) => mime?.startsWith('image');

const getFileIcon = (mimeType) => {
    if (mimeType?.startsWith('image')) return 'mingcute:image-line';
    if (mimeType?.startsWith('video')) return 'mingcute:video-line';
    if (mimeType?.startsWith('audio')) return 'mingcute:music-line';
    if (mimeType?.includes('pdf')) return 'mingcute:file-pdf-line';
    if (mimeType?.includes('word')) return 'mingcute:file-word-line';
    if (mimeType?.includes('excel') || mimeType?.includes('spreadsheet')) return 'mingcute:file-excel-line';
    if (mimeType?.includes('powerpoint') || mimeType?.includes('presentation')) return 'mingcute:file-ppt-line';
    if (mimeType?.includes('zip') || mimeType?.includes('rar')) return 'mingcute:file-zip-line';
    return 'mingcute:file-line';
};

const getFileIconColor = (mimeType) => {
    if (mimeType?.startsWith('image')) return '#10b981';
    if (mimeType?.startsWith('video')) return '#8b5cf6';
    if (mimeType?.startsWith('audio')) return '#f59e0b';
    if (mimeType?.includes('pdf')) return '#ef4444';
    if (mimeType?.includes('word')) return '#3b82f6';
    if (mimeType?.includes('excel') || mimeType?.includes('spreadsheet')) return '#10b981';
    if (mimeType?.includes('powerpoint') || mimeType?.includes('presentation')) return '#f59e0b';
    if (mimeType?.includes('zip') || mimeType?.includes('rar')) return '#6b7280';
    return '#6b7280';
};

const getFileExtension = (filename) => {
    return filename.split('.').pop()?.toUpperCase() || 'FILE';
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatFileType = (mimeType) => {
    if (mimeType?.startsWith('image')) return 'Image';
    if (mimeType?.startsWith('video')) return 'Video';
    if (mimeType?.startsWith('audio')) return 'Audio';
    if (mimeType?.includes('pdf')) return 'PDF';
    if (mimeType?.includes('word')) return 'Word';
    if (mimeType?.includes('excel') || mimeType?.includes('spreadsheet')) return 'Excel';
    if (mimeType?.includes('powerpoint') || mimeType?.includes('presentation')) return 'PowerPoint';
    if (mimeType?.includes('zip') || mimeType?.includes('rar')) return 'Archive';
    return 'Document';
};

// API functions
const fetchData = async ({ queryKey }) => {
    const [_key, queryParams] = queryKey;
    const { data } = await axios.get('/media', { params: queryParams });
    return data;
};

const { data: mediaList, refetch, isLoading } = useQuery({
    queryKey: ['MediaList', params.value],
    queryFn: fetchData,
});

// Debounced search
const debouncedSearch = _.debounce(() => {
    params.value.page = 1;
    refetch();
}, 500);

// Selection functions
const isSelected = (id) => selectedItems.value.some((item) => item.id === id);

const toggleSelect = (item) => {
    if (props.multiple) {
        const exists = selectedItems.value.find((i) => i.id === item.id);
        if (exists) {
            selectedItems.value = selectedItems.value.filter((i) => i.id !== item.id);
        } else if (!props.max || selectedItems.value.length < props.max) {
            selectedItems.value.push({
                id: item.id,
                filename: item.filename,
                mime_type: item.mime_type,
                original_url: item.original_url,
                readable_size: item.readable_size,
            });
        } else {
            ElMessage.warning(t('media.max_files_reached', { max: props.max }));
        }
    } else {
        const exists = selectedItems.value.find((i) => i.id === item.id);
        if (exists) {
            selectedItems.value = [];
        } else {
            selectedItems.value = [{
                id: item.id,
                filename: item.filename,
                mime_type: item.mime_type,
                original_url: item.original_url,
                readable_size: item.readable_size,
            }];
        }
    }
};

const removeSelected = (id) => {
    selectedItems.value = selectedItems.value.filter((i) => i.id !== id);
    emit('update:modelValue', props.multiple ? selectedItems.value : selectedItems.value[0] || null);
};

const clearAll = () => {
    selectedItems.value = [];
    emit('update:modelValue', props.multiple ? [] : null);
};

// Upload function
const onUpload = async (options) => {
    const formData = new FormData();
    formData.append('file', options.file);
    
    try {
        const { data } = await axios.post('/media/store', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
            onUploadProgress: options.onProgress,
        });
        
        options.onSuccess(data, options.file);
        ElMessage.success(t('media.upload_success'));
        refetch();
    } catch (error) {
        options.onError(error);
        ElMessage.error(t('media.upload_failed'));
    }
};

// Modal functions
const onOpenModal = () => {
    modalOpen.value = true;
    // Reset sidebar selection when opening modal
    selectedForInfo.value = null;
    // Reset upload area state
    showUploadArea.value = false;
};

const onSubmit = () => {
    emit('update:modelValue', props.multiple ? selectedItems.value : selectedItems.value[0] || null);
    modalOpen.value = false;
};

// Pagination
const changePage = (page) => {
    params.value.page = page;
    refetch();
};

// Download function
const downloadFile = (file) => {
    const link = document.createElement('a');
    link.href = file.original_url;
    link.download = file.filename;
    link.click();
};

// Watch props
watch(
    () => props.modelValue,
    (val) => {
        selectedItems.value = Array.isArray(val) ? val : val ? [val] : [];
    },
    { immediate: true }
);
</script>

<style scoped>
.media-picker-dialog :deep(.el-dialog) {
    margin: 2vh auto;
    border-radius: 8px;
    overflow: hidden;
}

.media-picker-dialog :deep(.el-dialog__header) {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 1px solid #e2e8f0;
    padding: 16px 20px;
}

.media-picker-dialog :deep(.el-dialog__body) {
    padding: 0;
}

.media-picker-dialog :deep(.el-dialog__footer) {
    padding: 0;
    border-top: 1px solid #e2e8f0;
}

.upload-area-container {
    border: 2px dashed #e5e7eb;
    border-radius: 8px;
    background: linear-gradient(135deg, #fafbfc 0%, #f8fafc 100%);
    transition: all 0.3s ease;
}

.upload-area-container:hover {
    border-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
}

.upload-area :deep(.el-upload-dragger) {
    border: none;
    border-radius: 6px;
    background: transparent;
    transition: all 0.3s ease;
}

.upload-area :deep(.el-upload-dragger:hover) {
    background: rgba(59, 130, 246, 0.05);
}

.search-input :deep(.el-input__wrapper) {
    border-radius: 8px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.media-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.media-item:hover {
    transform: translateY(-1px);
}

.media-item.selected {
    transform: translateY(-1px) scale(1.02);
}

/* Custom scrollbar */
.overflow-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.overflow-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>