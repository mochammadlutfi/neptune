<template>
    <div class="content">
        <div class="content-header">
            <div class="mt-auto mb-0">
                <div class="text-lg font-semibold">{{ $t(title, 2) }}</div>
            </div>
            <div class="mt-auto mb-0">
                <!-- Action buttons atau filter bisa ditambahkan di sini -->
            </div>
        </div>
        
        <!-- Dashboard Overview Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Stats Cards -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Total Production</h3>
                <p class="text-2xl font-bold text-gray-900">0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Active Wells</h3>
                <p class="text-2xl font-bold text-gray-900">0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Equipment Status</h3>
                <p class="text-2xl font-bold text-green-600">Normal</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Personnel On Board</h3>
                <p class="text-2xl font-bold text-gray-900">0</p>
            </div>
        </div>
        
        <!-- Charts and additional content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Production Trend</h3>
                <!-- Chart placeholder -->
                <div class="h-64 bg-gray-100 rounded flex items-center justify-center">
                    <span class="text-gray-500">Production Chart</span>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Recent Activities</h3>
                <!-- Activities list placeholder -->
                <div class="space-y-3">
                    <div class="text-sm text-gray-600">No recent activities</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()
const title = route.meta.title || 'Dashboard Overview'
</script>