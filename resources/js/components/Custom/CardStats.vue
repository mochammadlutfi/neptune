<template>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <Icon :icon="icon"/>
                </div>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900">{{ value }}</h3>
                <p class="text-sm text-gray-600">{{ label }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>


const props = defineProps({
    value : {
        type : Number,
        default : 0,
    },
    label : {
        type : String,
        default : '',
    },
    icon : {
        type : String,
        default : '',
    },

})

</script>