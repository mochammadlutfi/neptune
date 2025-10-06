<template>
    <Popover>
      <PopoverTrigger as-child>
        <Button variant="outline" size="sm" class="h-8 border-dashed">
        <Icon icon="fluent:filter-add-20-filled" class="mr-2 h-4 w-4" />
          {{ label }}
          <template v-if="select && select.length > 0">
            <Separator orientation="vertical" class="mx-2 h-4" />
            <Badge
              variant="secondary"
              class="rounded-sm px-1 font-normal lg:hidden"
            >
              {{ select.length }}
            </Badge>
            <div class="hidden lg:flex space-x-1">
              <Badge
                v-if="select.length > 1"
                variant="secondary"
                class="rounded-sm px-1 font-normal"
              >
                {{ select.length }} selected
              </Badge>
  
              <template v-else>
                <Badge
                  v-for="item in getSelectedOptions"
                  :key="item.value"
                  variant="secondary"
                  class="rounded-sm px-1 font-normal"
                >
                  {{ item.label }}
                </Badge>
              </template>
            </div>
          </template>
        </Button>
      </PopoverTrigger>
      <PopoverContent class="w-[200px] p-0" align="start">
        <Command
          :filter-function="(list, term) => list.filter(i => i.label.toLowerCase()?.includes(term))"
        >
          <CommandInput :placeholder="label" />
          <CommandList>
            <CommandEmpty>No results found.</CommandEmpty>
            <CommandGroup>
              <CommandItem
                v-for="option in options"
                :key="option.value"
                :value="option"
                @select="toggleOption(option)"
              class="cursor-pointer"
              >
                <div
                  :class="cn(
                    'mr-2 flex h-4 w-4 items-center justify-center rounded-sm border border-primary',
                    isSelected(option.value)
                      ? 'bg-primary text-primary-foreground'
                      : 'opacity-50 [&_svg]:invisible',
                  )"
                >
                  <Icon icon="fluent:checkmark-24-filled" :class="cn('h-4 w-4')" />
                </div>
                <component :is="option.icon" v-if="option.icon" class="mr-2 h-4 w-4 text-muted-foreground" />
                <span>{{ option.label }}</span>
              </CommandItem>
            </CommandGroup>
  
            <template v-if="select && select.length > 0">
              <CommandSeparator />
              <CommandGroup>
                <CommandItem
                  :value="{ label: 'Clear filters' }"
                  class="justify-center text-center"
                  @select="clearFilters"
                >
                  Clear filters
                </CommandItem>
              </CommandGroup>
            </template>
          </CommandList>
        </Command>
      </PopoverContent>
    </Popover>
  </template>
  
  <script setup>
  import { computed, ref, watch } from 'vue'
  import { Button } from '@/components/ui/button'
  import { Badge } from '@/components/ui/badge'
  import { Separator } from '@/components/ui/separator'
  import {
    Popover,
    PopoverContent,
    PopoverTrigger,
  } from '@/components/ui/popover'
  import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
    CommandSeparator,
  } from '@/components/ui/command'
  import { Icon } from '@iconify/vue'
  import { cn } from '@/utils/utils'
  
  // Props
  const props = defineProps({
    options: {
      type: Array,
      required: true,
      default: () => []
      // Expected format: [{ value: 'draft', label: 'Draft', icon: 'component-name', count: 5 }]
    },
    label: {
      type: String,
      required: true,
      default: 'Filter'
    },
    modelValue: {
      type: Array,
      default: () => []
    }
  })
  
  // Emits
  const emit = defineEmits(['update:modelValue'])
  
  const select = ref(props.modelValue);

  // Computed properties
  const getSelectedOptions = computed(() => {
    return props.options.filter(option => 
        select.value.includes(option.value)
    )
  })
  
  watch(() => props.modelValue, (newValue) => {
      select.value = newValue;
  });
  // Methods
  const isSelected = (value) => {
    return select.value.includes(value)
  }
  
  const toggleOption = (option) => {
    const index = select.value.indexOf(option.value)
    
    if (index > -1) {
    select.value.splice(index, 1)
    } else {
      select.value.push(option.value)
    }
    
    emit('update:modelValue', select.value)
  }
  
  const clearFilters = () => {
    emit('update:modelValue', [])
  }
  </script>