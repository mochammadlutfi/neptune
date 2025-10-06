<template>
  <div class="pagination-container">
    <div class="flex items-center justify-between">
      <!-- Pagination Info -->
      <div class="pagination-info text-sm text-gray-700">
        Menampilkan {{ pagination.from || 0 }} sampai {{ pagination.to || 0 }} 
        dari {{ pagination.total || 0 }} hasil
      </div>
      
      <!-- Pagination Controls -->
      <div class="pagination-controls flex items-center space-x-2">
        <!-- Previous Button -->
        <button
          @click="goToPage(pagination.current_page - 1)"
          :disabled="pagination.current_page <= 1"
          class="pagination-btn pagination-btn-prev"
          :class="{
            'disabled': pagination.current_page <= 1
          }"
        >
          <i class="fas fa-chevron-left"></i>
          <span class="hidden sm:inline ml-1">Sebelumnya</span>
        </button>
        
        <!-- Page Numbers -->
        <div class="pagination-numbers flex items-center space-x-1">
          <!-- First Page -->
          <button
            v-if="showFirstPage"
            @click="goToPage(1)"
            class="pagination-number"
            :class="{ 'active': pagination.current_page === 1 }"
          >
            1
          </button>
          
          <!-- First Ellipsis -->
          <span v-if="showFirstEllipsis" class="pagination-ellipsis">
            ...
          </span>
          
          <!-- Visible Page Numbers -->
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            class="pagination-number"
            :class="{ 'active': pagination.current_page === page }"
          >
            {{ page }}
          </button>
          
          <!-- Last Ellipsis -->
          <span v-if="showLastEllipsis" class="pagination-ellipsis">
            ...
          </span>
          
          <!-- Last Page -->
          <button
            v-if="showLastPage"
            @click="goToPage(pagination.last_page)"
            class="pagination-number"
            :class="{ 'active': pagination.current_page === pagination.last_page }"
          >
            {{ pagination.last_page }}
          </button>
        </div>
        
        <!-- Next Button -->
        <button
          @click="goToPage(pagination.current_page + 1)"
          :disabled="pagination.current_page >= pagination.last_page"
          class="pagination-btn pagination-btn-next"
          :class="{
            'disabled': pagination.current_page >= pagination.last_page
          }"
        >
          <span class="hidden sm:inline mr-1">Selanjutnya</span>
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>
    
    <!-- Per Page Selector -->
    <div class="per-page-selector mt-4 flex items-center justify-center">
      <label class="text-sm text-gray-700 mr-2">Tampilkan per halaman:</label>
      <select
        :value="pagination.per_page"
        @change="changePerPage($event.target.value)"
        class="text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  pagination: {
    type: Object,
    required: true
  }
})

// Emits
const emit = defineEmits(['page-changed', 'per-page-changed'])

// Computed
const visiblePages = computed(() => {
  const current = props.pagination.current_page
  const last = props.pagination.last_page
  const delta = 2 // Number of pages to show on each side of current page
  
  let start = Math.max(2, current - delta)
  let end = Math.min(last - 1, current + delta)
  
  // Adjust if we're near the beginning or end
  if (current <= delta + 1) {
    end = Math.min(last - 1, 2 * delta + 2)
  }
  if (current >= last - delta) {
    start = Math.max(2, last - 2 * delta - 1)
  }
  
  const pages = []
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

const showFirstPage = computed(() => {
  return props.pagination.last_page > 1 && !visiblePages.value.includes(1)
})

const showLastPage = computed(() => {
  const lastPage = props.pagination.last_page
  return lastPage > 1 && !visiblePages.value.includes(lastPage)
})

const showFirstEllipsis = computed(() => {
  return visiblePages.value.length > 0 && visiblePages.value[0] > 2
})

const showLastEllipsis = computed(() => {
  const lastVisible = visiblePages.value[visiblePages.value.length - 1]
  return lastVisible && lastVisible < props.pagination.last_page - 1
})

// Methods
const goToPage = (page) => {
  if (page >= 1 && page <= props.pagination.last_page && page !== props.pagination.current_page) {
    emit('page-changed', page)
  }
}

const changePerPage = (perPage) => {
  emit('per-page-changed', parseInt(perPage))
}
</script>

<style scoped>
.pagination-container {
  @apply bg-white border rounded-lg p-4;
}

.pagination-btn {
  @apply px-3 py-2 text-sm font-medium border border-gray-300 rounded-md transition-colors;
  @apply hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500;
}

.pagination-btn.disabled {
  @apply opacity-50 cursor-not-allowed;
  @apply hover:bg-white;
}

.pagination-number {
  @apply w-8 h-8 text-sm font-medium border border-gray-300 rounded transition-colors;
  @apply hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500;
}

.pagination-number.active {
  @apply bg-blue-500 text-white border-blue-500;
  @apply hover:bg-blue-600;
}

.pagination-ellipsis {
  @apply px-2 py-1 text-sm text-gray-500;
}

.per-page-selector select {
  @apply min-w-16;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .pagination-numbers {
    @apply hidden;
  }
  
  .pagination-info {
    @apply text-xs;
  }
  
  .per-page-selector {
    @apply mt-2;
  }
}
</style>