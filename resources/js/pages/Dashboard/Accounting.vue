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
        
        <!-- Accounting Dashboard Content -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Financial Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Total Revenue</h3>
                <p class="text-2xl font-bold text-green-600">$0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Total Expenses</h3>
                <p class="text-2xl font-bold text-red-600">$0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Net Profit</h3>
                <p class="text-2xl font-bold text-blue-600">$0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-medium text-gray-500">Cash Flow</h3>
                <p class="text-2xl font-bold text-gray-900">$0</p>
            </div>
        </div>
        
        <!-- Financial Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Revenue vs Expenses</h3>
                <div class="h-64 bg-gray-100 rounded flex items-center justify-center">
                    <span class="text-gray-500">Financial Chart</span>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Cash Flow Trend</h3>
                <div class="h-64 bg-gray-100 rounded flex items-center justify-center">
                    <span class="text-gray-500">Cash Flow Chart</span>
                </div>
            </div>
        </div>
        
        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Recent Transactions</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                No recent transactions
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Account Summary -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Accounts Receivable</h3>
                <p class="text-2xl font-bold text-blue-600">$0</p>
                <p class="text-sm text-gray-500 mt-2">Outstanding invoices</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Accounts Payable</h3>
                <p class="text-2xl font-bold text-orange-600">$0</p>
                <p class="text-sm text-gray-500 mt-2">Outstanding bills</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Bank Balance</h3>
                <p class="text-2xl font-bold text-green-600">$0</p>
                <p class="text-sm text-gray-500 mt-2">Current balance</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()
const title = route.meta.title || 'Accounting Dashboard'
</script>