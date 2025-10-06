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
        
        <!-- Purchase Dashboard Content -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Purchase Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Total Purchases</h3>
                <p class="text-2xl font-bold text-gray-900">$0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Monthly Spending</h3>
                <p class="text-2xl font-bold text-red-600">$0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Pending Orders</h3>
                <p class="text-2xl font-bold text-orange-600">0</p>
            </div>
        </div>
        
        <!-- Purchase Charts and Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Purchase Trend</h3>
                <div class="h-64 bg-gray-100 rounded flex items-center justify-center">
                    <span class="text-gray-500">Purchase Chart</span>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Recent Purchase Orders</h3>
                <div class="space-y-3">
                    <div class="text-sm text-gray-600">No recent purchase orders</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()
const title = route.meta.title || 'Purchase Dashboard'
</script>