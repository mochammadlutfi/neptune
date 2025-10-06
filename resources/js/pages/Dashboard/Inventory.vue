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
        
        <!-- Inventory Dashboard Content -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Inventory Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Total Items</h3>
                <p class="text-2xl font-bold text-gray-900">0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Low Stock Items</h3>
                <p class="text-2xl font-bold text-red-600">0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Out of Stock</h3>
                <p class="text-2xl font-bold text-red-800">0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Total Value</h3>
                <p class="text-2xl font-bold text-green-600">$0</p>
            </div>
        </div>
        
        <!-- Inventory Charts and Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Stock Levels</h3>
                <div class="h-64 bg-gray-100 rounded flex items-center justify-center">
                    <span class="text-gray-500">Stock Level Chart</span>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Low Stock Alerts</h3>
                <div class="space-y-3">
                    <div class="text-sm text-gray-600">No low stock alerts</div>
                </div>
            </div>
        </div>
        
        <!-- Recent Movements -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-lg font-semibold mb-4">Recent Inventory Movements</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                No recent movements
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()
const title = route.meta.title || 'Inventory Dashboard'
</script>