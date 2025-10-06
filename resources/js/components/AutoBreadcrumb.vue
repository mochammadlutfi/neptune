<!-- components/AutoBreadcrumb.vue -->
<template>
    <Breadcrumb>
      <BreadcrumbList>
        <template v-for="(crumb, index) in breadcrumbs" :key="index">
          <BreadcrumbItem>
            <BreadcrumbLink 
              v-if="!crumb.active && crumb.to"
              @click="navigateTo(crumb.to)"
              class="cursor-pointer"
            >
              {{ crumb.label }}
            </BreadcrumbLink>
            <BreadcrumbPage v-else>
              {{ crumb.label }}
            </BreadcrumbPage>
          </BreadcrumbItem>
          
          <BreadcrumbSeparator 
            v-if="index < breadcrumbs.length - 1" 
          />
        </template>
      </BreadcrumbList>
    </Breadcrumb>
  </template>
  
  <script setup>
  import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
  } from '@/components/ui/breadcrumb'
  import { usePageMeta } from '@/composables/common/usePageMeta'
  
  const { breadcrumbs, navigateTo } = usePageMeta()
  </script>