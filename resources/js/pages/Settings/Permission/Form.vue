<template>
  <div class="content">
    <PageHeader
      :title="route.params.id ? $t('settings.role_permission.edit') : $t('settings.role_permission.create')"
    />

    <el-card body-class="p-0" v-loading="isLoading" class="shadow-sm rounded-xl">
      <el-form :model="form" ref="formRef" @submit.prevent="onSubmit" label-position="top">
        <!-- Role name -->
        <div class="p-4">
          <el-form-item
            :label="$t('settings.role_permission.fields.role_name')"
            prop="name"
            :rules="[{ required: true, message: $t('common.validation.required_field', { field: $t('settings.role_permission.fields.role_name') }) }]"
          >
            <el-input v-model="form.name" />
          </el-form-item>
        </div>

        <!-- Permissions -->
        <el-collapse v-model="activeModules" class="border-t" accordion>
          <el-collapse-item
            v-for="(m, i) in permissionList"
            :key="i"
            :name="m.module"
            class="px-4"
          >
            <template #title>
              <div class="w-full flex items-center justify-between pr-2">
                <div class="flex items-center gap-2">
                  <h3 class="text-base font-semibold">
                    {{ $t(`${m.module}.title`) }}
                  </h3>
                  <el-tag size="small" effect="plain">
                    {{ selectedCountByModule(m.module) }} / {{ totalCountByModule(m.module) }}
                  </el-tag>
                </div>

                <!-- Select all (module) -->
                <el-checkbox
                  @click.stop
                  :indeterminate="isModuleIndeterminate(m.module)"
                  :model-value="isModuleAllChecked(m.module)"
                  @change="(val) => toggleModule(m.module, val)"
                >
                  {{ $t('common.actions.select_all') }}
                </el-checkbox>
              </div>
            </template>

            <el-row :gutter="16" class="pb-4">
              <el-col :md="12" :xs="24" v-for="(menu, j) in m.menu" :key="j">
                <el-card
                  body-class="!p-0"
                  class="mb-4 rounded-lg border border-gray-100 shadow-sm hover:shadow-md transition"
                >
                  <!-- Menu header -->
                  <div class="flex items-center justify-between border-b p-3 bg-gray-50">
                    <div class="font-medium">
                      {{ $t(`${m.module}.${menu.label}.title`) }}
                    </div>

                    <!-- Select all (menu) -->
                    <el-checkbox
                      :indeterminate="isMenuIndeterminate(m.module, menu.label)"
                      :model-value="isMenuAllChecked(m.module, menu.label)"
                      @change="(val) => toggleMenu(m.module, menu.label, val)"
                    >
                      {{ $t('common.actions.select_all') }}
                    </el-checkbox>
                  </div>

                  <!-- Actions -->
                  <div class="p-3">
                    <el-checkbox-group v-model="form.lines" class="grid grid-cols-2 md:grid-cols-3 gap-2">
                      <el-checkbox
                        v-for="action in menu.actions"
                        :key="action.key"
                        :label="action.key"
                      >
                        <span v-if="menu.label == 'system'">
                          {{ $t(`settings.system.${action.name}.title`) }}
                        </span>
                        <span v-else>
                          {{ $t(`common.actions.${action.name}`) }}
                        </span>
                      </el-checkbox>
                    </el-checkbox-group>
                  </div>
                </el-card>
              </el-col>
            </el-row>
          </el-collapse-item>
        </el-collapse>

        <!-- Footer -->
        <div class="p-4 flex gap-2">
          <el-button type="danger" plain @click.prevent="$router.go(-1)">
            {{ $t('common.actions.cancel') }}
          </el-button>
          <el-button native-type="submit" type="primary" :loading="isLoading">
            {{ $t('common.actions.save') }}
          </el-button>
        </div>
      </el-form>
    </el-card>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import PageHeader from '@/components/PageHeader.vue'

const { t } = useI18n()
const router = useRouter()
const route = useRoute()

const formRef = ref(null)
const isLoading = ref(false)
const permissionList = ref([]) // [{ module, menu:[{label, actions:[{key,name}]}] }]
const activeModules = ref([])  // default expanded modules

const form = ref({
  id: null,
  name: null,
  lines: [], // selected permission keys (string[])
})

/* ========== Helpers: getters ========== */
const getMenuActionKeys = (moduleName, label) => {
  const mod = permissionList.value.find(m => m.module === moduleName)
  if (!mod) return []
  const menu = mod.menu.find(x => x.label === label)
  if (!menu) return []
  return menu.actions.map(a => a.key)
}

const getModuleActionKeys = (moduleName) => {
  const mod = permissionList.value.find(m => m.module === moduleName)
  if (!mod) return []
  return mod.menu.flatMap(menu => menu.actions.map(a => a.key))
}

const isAllChecked = (keys) => keys.length > 0 && keys.every(k => form.value.lines.includes(k))
const isIndeterminate = (keys) => {
  const sel = keys.filter(k => form.value.lines.includes(k)).length
  return sel > 0 && sel < keys.length
}

/* ========== UI state: checked/indeterminate ========== */
const isMenuAllChecked = (moduleName, label) => isAllChecked(getMenuActionKeys(moduleName, label))
const isMenuIndeterminate = (moduleName, label) => isIndeterminate(getMenuActionKeys(moduleName, label))
const isModuleAllChecked = (moduleName) => isAllChecked(getModuleActionKeys(moduleName))
const isModuleIndeterminate = (moduleName) => isIndeterminate(getModuleActionKeys(moduleName))

/* ========== Counters (nice UX) ========== */
const selectedCountByModule = (moduleName) => {
  const keys = getModuleActionKeys(moduleName)
  return keys.filter(k => form.value.lines.includes(k)).length
}
const totalCountByModule = (moduleName) => getModuleActionKeys(moduleName).length

/* ========== Togglers ========== */
const addMany = (keys) => {
  const set = new Set(form.value.lines)
  keys.forEach(k => set.add(k))
  form.value.lines = Array.from(set)
}

const removeMany = (keys) => {
  const remove = new Set(keys)
  form.value.lines = form.value.lines.filter(k => !remove.has(k))
}

const toggleMenu = (moduleName, label, checked) => {
  const keys = getMenuActionKeys(moduleName, label)
  if (checked) addMany(keys)
  else removeMany(keys)
}

const toggleModule = (moduleName, checked) => {
  const keys = getModuleActionKeys(moduleName)
  if (checked) addMany(keys)
  else removeMany(keys)
}

/* ========== Data fetching ========== */
const fetchPermission = async () => {
  try {
    isLoading.value = true
    const response = await axios.get('/settings/permissions/list')
    if (response.status === 200) {
      permissionList.value = response.data
      // expand all modules by default (optional)
      activeModules.value = permissionList.value.map(m => m.module)
    }
  } catch (e) {
    console.error(e)
  } finally {
    isLoading.value = false
  }
}

const fetchData = async (id) => {
  try {
    const response = await axios.get('/settings/permissions/' + id)
    if (response.status === 200) {
      const data = response.data
      form.value.id = data.id
      form.value.name = data.name
      // ensure unique
      const set = new Set(form.value.lines)
      data.permissions.forEach(p => set.add(p.name))
      form.value.lines = Array.from(set)
    }
  } catch (e) {
    console.error(e)
  }
}

/* ========== Submit ========== */
const onSubmit = async () => {
  if (!formRef.value) return
  formRef.value.validate(async (valid) => {
    if (!valid) {
      return ElMessage({ message: t('common.errors.validation_failed'), type: 'error' })
    }
    try {
      isLoading.value = true
      const url = form.value.id ? `/settings/permissions/${form.value.id}/update` : '/settings/permissions/store'
      const method = form.value.id ? 'put' : 'post'
      await axios({ method, url, data: form.value })
      ElMessage({ message: t('common.success.changes_saved'), type: 'success' })
      router.replace({ path: '/settings/permission' })
    } catch (e) {
      console.log(e)
      ElMessage({ message: t('common.errors.server_error'), type: 'error' })
    } finally {
      isLoading.value = false
    }
  })
}

onMounted(() => {
  fetchPermission()
  if (route.params.id) {
    fetchData(route.params.id)
  }
})
</script>

<style lang="css">
/* Bersihin border default collapse header */
.el-collapse-item__header { border-bottom: 0 !important; }
</style>
