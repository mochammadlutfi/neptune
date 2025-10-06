<template>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content Skeleton -->
    <div class="lg:col-span-2 space-y-6">
      <!-- Basic Information Card Skeleton -->
      <el-card class="!rounded-lg !shadow-md">
        <template #header>
          <div class="flex items-center">
            <el-skeleton-item variant="circle" style="width: 20px; height: 20px; margin-right: 8px" />
            <el-skeleton-item variant="text" style="width: 150px" />
          </div>
        </template>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-for="i in 6" :key="i" class="space-y-2">
            <el-skeleton-item variant="text" style="width: 80px; height: 14px" />
            <el-skeleton-item variant="text" style="width: 120px; height: 16px" />
          </div>
        </div>
      </el-card>

      <!-- Location Information Card Skeleton -->
      <el-card class="!rounded-lg !shadow-md">
        <template #header>
          <div class="flex items-center">
            <el-skeleton-item variant="circle" style="width: 20px; height: 20px; margin-right: 8px" />
            <el-skeleton-item variant="text" style="width: 180px" />
          </div>
        </template>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-for="i in 7" :key="i" class="space-y-2">
            <el-skeleton-item variant="text" style="width: 90px; height: 14px" />
            <el-skeleton-item variant="text" style="width: 140px; height: 16px" />
          </div>
        </div>
      </el-card>

      <!-- Technical Specifications Card Skeleton -->
      <el-card class="!rounded-lg !shadow-md">
        <template #header>
          <div class="flex items-center">
            <el-skeleton-item variant="circle" style="width: 20px; height: 20px; margin-right: 8px" />
            <el-skeleton-item variant="text" style="width: 200px" />
          </div>
        </template>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-for="i in 5" :key="i" class="space-y-2">
            <el-skeleton-item variant="text" style="width: 100px; height: 14px" />
            <el-skeleton-item variant="text" style="width: 80px; height: 16px" />
          </div>
        </div>
      </el-card>

      <!-- Vessel Details Card Skeleton -->
      <el-card class="!rounded-lg !shadow-md">
        <template #header>
          <div class="flex items-center">
            <el-skeleton-item variant="circle" style="width: 20px; height: 20px; margin-right: 8px" />
            <el-skeleton-item variant="text" style="width: 120px" />
          </div>
        </template>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-for="i in 7" :key="i" class="space-y-2">
            <el-skeleton-item variant="text" style="width: 85px; height: 14px" />
            <el-skeleton-item variant="text" style="width: 110px; height: 16px" />
          </div>
        </div>
      </el-card>
    </div>

    <!-- Sidebar Skeleton -->
    <div class="space-y-6">
      <!-- Quick Stats Card Skeleton -->
      <el-card class="!rounded-lg !shadow-md">
        <template #header>
          <div class="flex items-center">
            <el-skeleton-item variant="circle" style="width: 20px; height: 20px; margin-right: 8px" />
            <el-skeleton-item variant="text" style="width: 100px" />
          </div>
        </template>
        
        <div class="space-y-4">
          <div v-for="i in 3" :key="i" class="flex justify-between items-center">
            <el-skeleton-item variant="text" style="width: 60px; height: 14px" />
            <el-skeleton-item variant="button" style="width: 50px; height: 20px" />
          </div>
        </div>
      </el-card>

      <!-- Actions Card Skeleton -->
      <el-card class="!rounded-lg !shadow-md">
        <template #header>
          <div class="flex items-center">
            <el-skeleton-item variant="circle" style="width: 20px; height: 20px; margin-right: 8px" />
            <el-skeleton-item variant="text" style="width: 70px" />
          </div>
        </template>
        
        <div class="space-y-3">
          <el-skeleton-item variant="button" style="width: 100%; height: 32px" />
          <el-skeleton-item variant="button" style="width: 100%; height: 32px" />
        </div>
      </el-card>
    </div>
  </div>
</template>

<script setup>
// No props or logic needed for skeleton component
</script>

<style scoped>
/* Add any specific styles for skeleton here */
</style>