<template>
  <div class="content">
    <PageHeader :title="$t('settings.role_permission.detail')" />

    <el-card body-class="p-0" class="rounded-xl shadow-sm" v-loading="isLoading">
      <!-- Header: Role Summary -->
      <div class="p-4">
        <el-descriptions :column="2" border>
          <el-descriptions-item :label="$t('settings.role_permission.fields.role_name')">
            <div class="font-semibold">{{ role.name || '—' }}</div>
          </el-descriptions-item>
          <el-descriptions-item :label="$t('settings.role_permission.fields.total_permissions')">
            <div class="flex items-center gap-2">
              <el-tag type="success" effect="plain">
                {{ grantedCount }} / {{ totalCount }}
              </el-tag>
              <span class="text-gray-500 text-xs">
                {{ $t('common.labels.granted') }} / {{ $t('common.labels.total') }}
              </span>
            </div>
          </el-descriptions-item>
        </el-descriptions>
      </div>

      <!-- Toolbar -->
      <div class="px-4 pb-4 flex flex-col md:flex-row gap-3 md:items-center md:justify-between">
        <div class="flex items-center gap-2 w-full md:w-1/2">
          <el-input
            v-model="query"
            clearable
            :placeholder="$t('common.placeholders.search_permissions')"
          >
            <template #prefix>
              <el-icon><Search /></el-icon>
            </template>
          </el-input>
          <el-switch
            v-model="showAll"
            :active-text="$t('common.labels.show_all_actions')"
            :inactive-text="$t('common.labels.only_granted')"
          />
        </div>

        <div class="flex items-center gap-2">
          <el-button @click="copyAll" :disabled="grantedCount === 0" plain>
            {{ $t('common.actions.copy_all') }}
          </el-button>
          <el-button type="primary" @click="goEdit" :disabled="!role.id">
            {{ $t('common.actions.edit') }}
          </el-button>
          <el-button type="danger" plain @click="goBack">
            {{ $t('common.actions.back') }}
          </el-button>
        </div>
      </div>

      <!-- Permissions grouped -->
      <div class="border-t">
        <el-collapse v-model="activeModules" class="px-2" accordion>
          <template v-if="filteredModules.length">
            <el-collapse-item
              v-for="mod in filteredModules"
              :key="mod.module"
              :name="mod.module"
              class="px-2"
            >
              <template #title>
                <div class="w-full flex items-center justify-between pr-2">
                  <div class="flex items-center gap-2">
                    <h3 class="text-base font-semibold">
                      {{ $t(`${mod.module}.title`) || titleCase(mod.module) }}
                    </h3>
                    <el-tag size="small" effect="plain">
                      {{ countGrantedByModule(mod) }} / {{ countTotalByModule(mod) }}
                    </el-tag>
                  </div>
                </div>
              </template>

              <el-row :gutter="16" class="pb-4">
                <el-col :md="12" :xs="24" v-for="menu in mod.menu" :key="menu.label">
                  <el-card body-class="!p-0" class="mb-4 rounded-lg border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between border-b p-3 bg-gray-50">
                      <div class="font-medium">
                        {{ $t(`${mod.module}.${menu.label}.title`) || titleCase(menu.label) }}
                      </div>
                      <el-tag size="small" effect="plain">
                        {{ countGrantedByMenu(menu) }} / {{ menu.actions.length }}
                      </el-tag>
                    </div>

                    <div class="p-3">
                      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                        <div
                          v-for="action in menu.actions"
                          :key="action.key"
                          class="flex items-center justify-between gap-2 rounded border px-3 py-2"
                          :class="isGranted(action.key) ? 'border-green-200 bg-green-50' : 'border-gray-100 bg-white'"
                        >
                          <div class="text-sm">
                            {{ $t(`common.actions.${action.name}`) || titleCase(action.name) }}
                            <div class="text-xs text-gray-500">{{ action.key }}</div>
                          </div>
                          <el-tag
                            :type="isGranted(action.key) ? 'success' : 'info'"
                            effect="plain"
                            size="small"
                          >
                            <el-icon v-if="isGranted(action.key)" class="mr-1"><CircleCheck /></el-icon>
                            <el-icon v-else class="mr-1"><Remove /></el-icon>
                            {{ isGranted(action.key) ? $t('common.labels.granted') : $t('common.labels.not_granted') }}
                          </el-tag>
                        </div>
                      </div>
                    </div>
                  </el-card>
                </el-col>
              </el-row>
            </el-collapse-item>
          </template>

          <template v-else>
            <div class="p-6">
              <el-empty :description="$t('common.labels.no_data')" />
            </div>
          </template>
        </el-collapse>
      </div>
    </el-card>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import PageHeader from '@/components/PageHeader.vue'
import { ElMessage } from 'element-plus'
import { Search, CircleCheck, Remove } from '@element-plus/icons-vue'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()

const isLoading = ref(false)
const permissionList = ref([]) // [{ module, menu:[{label, actions:[{key,name}]}] }]
const role = ref({ id: null, name: null })
const grantedSet = ref(new Set()) // Set<string> keys granted

const query = ref('')
const showAll = ref(false)
const activeModules = ref([]) // expanded modules

const MODULE_ORDER = ['settings', 'master', 'production', 'equipment']

/* ===== Utils ===== */
const titleCase = (s) =>
  (s || '').replace(/[_-]/g, ' ').replace(/\w\S*/g, (w) => w[0].toUpperCase() + w.slice(1).toLowerCase())

const isGranted = (key) => grantedSet.value.has(key)

/* ===== Counters ===== */
const totalCount = computed(() =>
  permissionList.value.reduce((sum, m) => sum + m.menu.reduce((s, me) => s + me.actions.length, 0), 0)
)
const grantedCount = computed(() => Array.from(grantedSet.value).length)

const countGrantedByModule = (mod) =>
  mod.menu.reduce((s, me) => s + me.actions.filter(a => isGranted(a.key)).length, 0)
const countTotalByModule = (mod) =>
  mod.menu.reduce((s, me) => s + me.actions.length, 0)
const countGrantedByMenu = (menu) => menu.actions.filter(a => isGranted(a.key)).length

/* ===== Filtering & Ordering ===== */
const normalizedModules = computed(() => {
  // Place known modules by fixed order first; append unknown modules sorted by name
  const known = []
  const others = []
  const map = new Map(permissionList.value.map(m => [m.module, m]))

  MODULE_ORDER.forEach(name => {
    if (map.has(name)) known.push(map.get(name))
  })

  permissionList.value.forEach(m => {
    if (!MODULE_ORDER.includes(m.module)) others.push(m)
  })
  others.sort((a, b) => a.module.localeCompare(b.module))

  return [...known, ...others]
})

const filteredModules = computed(() => {
  const q = query.value.trim().toLowerCase()
  if (!q && showAll.value) return normalizedModules.value

  const match = (str) => (str || '').toLowerCase().includes(q)

  return normalizedModules.value
    .map(mod => {
      const menus = mod.menu
        .map(me => {
          // filter actions by query
          let actions = me.actions
          if (q) {
            actions = actions.filter(a =>
              match(a.key) || match(a.name) || match(me.label) || match(mod.module)
            )
          }
          // if not showAll: keep only granted
          if (!showAll.value) {
            actions = actions.filter(a => isGranted(a.key))
          }
          return { ...me, actions }
        })
        // remove menu that ends up empty
        .filter(me => me.actions.length > 0)

      return { ...mod, menu: menus }
    })
    // remove module that ends up empty
    .filter(mod => mod.menu.length > 0)
})

/* ===== Actions ===== */
const copyAll = async () => {
  const keys = Array.from(grantedSet.value)
  if (!keys.length) return
  try {
    await navigator.clipboard.writeText(keys.join('\n'))
    ElMessage({ type: 'success', message: t('common.success.copied_to_clipboard') })
  } catch {
    ElMessage({ type: 'error', message: t('common.errors.copy_failed') })
  }
}

const goEdit = () => {
  if (!role.value.id) return
  router.push({ path: `/settings/permissions/${role.value.id}/edit` })
}
const goBack = () => router.back()

/* ===== Fetching ===== */
const fetchPermissionList = async () => {
  const res = await axios.get('/settings/permissions/list')
  if (res.status === 200) permissionList.value = res.data
  // expand all by default
  activeModules.value = permissionList.value.map(m => m.module)
}

const fetchRoleDetail = async (id) => {
  const res = await axios.get(`/settings/permissions/${id}`)
  if (res.status === 200) {
    role.value.id = res.data.id
    role.value.name = res.data.name
    const set = new Set()
    ;(res.data.permissions || []).forEach(p => p?.name && set.add(p.name))
    grantedSet.value = set
  }
}

onMounted(async () => {
  try {
    isLoading.value = true
    await Promise.all([
      fetchPermissionList(),
      route.params.id ? fetchRoleDetail(route.params.id) : Promise.resolve()
    ])
  } catch (e) {
    console.error(e)
    ElMessage({ type: 'error', message: t('common.errors.server_error') })
  } finally {
    isLoading.value = false
  }
})
</script>

<style lang="css">
.el-collapse-item__header { border-bottom: 0 !important; }
</style>
