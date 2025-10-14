<template>
  <div class="content">
    <!-- Page Header -->
    <PageHeader :title="$t(title)">
      <template #actions>
        <el-tooltip :content="$t('common.form.form_help')" placement="bottom">
          <el-button circle>
            <Icon icon="mingcute:question-line" />
          </el-button>
        </el-tooltip>
      </template>
    </PageHeader>

    <!-- Form Content -->
    <el-card body-class="!p-0" class="!rounded-lg !shadow-lg" v-loading="loading">
      <el-form
        ref="formRef"
        :model="form"
        :rules="formRules"
        label-position="top"
        @submit.prevent="onSubmit"
      >
        <div class="p-6">
          <!-- Basic Information -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-700">
              {{ $t('production.vessel_operation.sections.basic_info') }}
            </h3>
            <el-row :gutter="24">
              <!-- Vessel -->
              <el-col :md="12" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.vessel_id')" 
                  prop="vessel_id"
                >
                  <select-vessel
                    v-model="form.vessel_id"
                    :vessel-id="form.vessel_id"
                    :placeholder="$t('production.vessel_operation.placeholder.vessel_id')"
                    @change="loadWells"
                  />
                </el-form-item>
              </el-col>

              <!-- Operation Date -->
              <el-col :md="12" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.date')" 
                  prop="date"
                >
                  <el-date-picker
                    v-model="form.date"
                    type="date"
                    :placeholder="$t('production.vessel_operation.placeholder.date')"
                    format="DD-MM-YYYY"
                    value-format="YYYY-MM-DD"
                    style="width: 100%"
                    :disabled-date="disabledDate"
                    @change="() => formRef?.validateField('date')"
                  />
                </el-form-item>
              </el-col>
            </el-row>
          </div>

          <!-- Gas Operations Section -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-700 flex items-center">
              <Icon icon="mdi:gas-cylinder" class="mr-2 text-blue-600" />
              {{ $t('production.vessel_operation.sections.gas_operations') }}
            </h3>
            <el-row :gutter="24">
              <!-- Gas Export Uptime -->
              <el-col :md="8" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.gas_export_uptime')" 
                  prop="gas_export_uptime"
                >
                  <el-time-picker
                    v-model="form.gas_export_uptime"
                    format="HH:mm"
                    value-format="HH:mm:ss"
                    :placeholder="$t('production.vessel_operation.placeholder.gas_export_uptime')"
                    style="width: 100%"
                  />
                </el-form-item>
              </el-col>

              <!-- Inlet Gas -->
              <el-col :md="8" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.inlet_gas_mmscf')" 
                  prop="inlet_gas_mmscf"
                >
                  <el-input
                    v-model.number="form.inlet_gas_mmscf"
                    type="number"
                    :min="0"
                    :step="0.0001"
                    :placeholder="$t('production.vessel_operation.placeholder.inlet_gas_mmscf')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCF</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Sales Gas -->
              <!-- <el-col :md="8" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.total_sales_gas_mmscfd')" 
                  prop="total_sales_gas_mmscfd"
                >
                  <el-input
                    v-model.number="form.total_sales_gas_mmscfd"
                    type="number"
                    :min="0"
                    :step="0.0001"
                    :placeholder="$t('production.vessel_operation.placeholder.total_sales_gas_mmscfd')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCFD</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col> -->

              <!-- Fuel Gas -->
              <el-col :md="8" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.fuel_gas_mmscfd')" 
                  prop="fuel_gas_mmscfd"
                >
                  <el-input
                    v-model.number="form.fuel_gas_mmscfd"
                    type="number"
                    :min="0"
                    :step="0.0001"
                    :placeholder="$t('production.vessel_operation.placeholder.fuel_gas_mmscfd')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCFD</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Flare HP -->
              <el-col :md="8" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.flare_hp_mmscfd')" 
                  prop="flare_hp_mmscfd"
                >
                  <el-input
                    v-model.number="form.flare_hp_mmscfd"
                    type="number"
                    :min="0"
                    :step="0.0001"
                    @input="calculateTotalFlare"
                    :placeholder="$t('production.vessel_operation.placeholder.flare_hp_mmscfd')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCFD</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Flare LP -->
              <el-col :md="8" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.flare_lp_mmscfd')" 
                  prop="flare_lp_mmscfd"
                >
                  <el-input
                    v-model.number="form.flare_lp_mmscfd"
                    type="number"
                    :min="0"
                    :step="0.0001"
                    @input="calculateTotalFlare"
                    :placeholder="$t('production.vessel_operation.placeholder.flare_lp_mmscfd')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCFD</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Total Flare (Read-only) -->
              <el-col :md="8" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.total_flare_gas_mmscfd')" 
                  prop="total_flare_gas_mmscfd"
                >
                  <el-input
                    v-model.number="form.total_flare_gas_mmscfd"
                    type="number"
                    readonly
                    class="!bg-gray-100"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">MMSCFD</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>


              <!-- Inlet Pressure -->
              <el-col :md="6" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.inlet_pressure_psi')" 
                  prop="inlet_pressure_psi"
                >
                  <el-input
                    v-model.number="form.inlet_pressure_psi"
                    type="number"
                    :step="0.01"
                    :placeholder="$t('production.vessel_operation.placeholder.inlet_pressure_psi')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">PSI</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Inlet Temp -->
              <el-col :md="6" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.inlet_temp_f')" 
                  prop="inlet_temp_f"
                >
                  <el-input
                    v-model.number="form.inlet_temp_f"
                    type="number"
                    :step="0.01"
                    :placeholder="$t('production.vessel_operation.placeholder.inlet_temp_f')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">°F</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Outlet Pressure -->
              <el-col :md="6" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.outlet_pressure_psi')" 
                  prop="outlet_pressure_psi"
                >
                  <el-input
                    v-model.number="form.outlet_pressure_psi"
                    type="number"
                    :step="0.01"
                    :placeholder="$t('production.vessel_operation.placeholder.outlet_pressure_psi')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">PSI</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Outlet Temp -->
              <el-col :md="6" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.outlet_temp_f')" 
                  prop="outlet_temp_f"
                >
                  <el-input
                    v-model.number="form.outlet_temp_f"
                    type="number"
                    :step="0.01"
                    :placeholder="$t('production.vessel_operation.placeholder.outlet_temp_f')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">°F</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>
            </el-row>
          </div>

          <!-- Condensate Operations Section -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-700 flex items-center">
              <Icon icon="mdi:oil" class="mr-2 text-amber-600" />
              {{ $t('production.vessel_operation.sections.condensate_operations') }}
            </h3>
            <el-row :gutter="24">
              <!-- Condensate Produced (Liters) -->
              <el-col :md="8" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.condensate_produced_lts')" 
                  prop="condensate_produced_lts"
                >
                  <el-input
                    v-model.number="form.condensate_produced_lts"
                    type="number"
                    :min="0"
                    :step="0.01"
                    @input="convertLitersToBbls"
                    :placeholder="$t('production.vessel_operation.placeholder.condensate_produced_lts')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">Liters</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Condensate Produced (Barrels) - Auto-calculated -->
              <el-col :md="8" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.condensate_produced_bbls')" 
                  prop="condensate_produced_bbls"
                >
                  <el-input
                    v-model.number="form.condensate_produced_bbls"
                    type="number"
                    readonly
                    class="!bg-gray-100"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">bbls</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Condensate Skimmed -->
              <el-col :md="8" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.condensate_skimmed_bbls')" 
                  prop="condensate_skimmed_bbls"
                >
                  <el-input
                    v-model.number="form.condensate_skimmed_bbls"
                    type="number"
                    :min="0"
                    :step="0.01"
                    @input="calculateCondensateTotal"
                    :placeholder="$t('production.vessel_operation.placeholder.condensate_skimmed_bbls')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">bbls</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Condensate Total Production (Read-only) -->
              <el-col :md="8" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.condensate_production_total_bbls')" 
                  prop="condensate_production_total_bbls"
                >
                  <el-input
                    v-model.number="form.condensate_production_total_bbls"
                    type="number"
                    readonly
                    class="!bg-gray-100"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">bbls</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Condensate Stock -->
              <el-col :md="8" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.condensate_stock_bbls')" 
                  prop="condensate_stock_bbls"
                >
                  <el-input
                    v-model.number="form.condensate_stock_bbls"
                    type="number"
                    :min="0"
                    :step="0.01"
                    :placeholder="$t('production.vessel_operation.placeholder.condensate_stock_bbls')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">bbls</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Condensate Consumed GTG -->
              <el-col :md="8" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.condensate_consumed_gtg_bbls')" 
                  prop="condensate_consumed_gtg_bbls"
                >
                  <el-input
                    v-model.number="form.condensate_consumed_gtg_bbls"
                    type="number"
                    :min="0"
                    :step="0.01"
                    :placeholder="$t('production.vessel_operation.placeholder.condensate_consumed_gtg_bbls')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">bbls</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Condensate Temperature -->
              <el-col :md="8" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.condensate_temp_f')" 
                  prop="condensate_temp_f"
                >
                  <el-input
                    v-model.number="form.condensate_temp_f"
                    type="number"
                    :step="0.01"
                    :placeholder="$t('production.vessel_operation.placeholder.condensate_temp_f')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">°F</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Condensate Uptime -->
              <el-col :md="8" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.condensate_uptime')" 
                  prop="condensate_uptime"
                >
                  <el-time-picker
                    v-model="form.condensate_uptime"
                    format="HH:mm:ss"
                    value-format="HH:mm:ss"
                    :placeholder="$t('production.vessel_operation.placeholder.condensate_uptime')"
                    style="width: 100%"
                  />
                </el-form-item>
              </el-col>
            </el-row>
          </div>

          <!-- Diesel Fuel & Water Operations -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-700 flex items-center">
              <Icon icon="mdi:fuel" class="mr-2 text-red-600" />
              {{ $t('production.vessel_operation.sections.fuel_water_operations') }}
            </h3>
            <el-row :gutter="24">
              <!-- Diesel MOPU -->
              <el-col :md="6" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.diesel_fuel_mopu_ltr')" 
                  prop="diesel_fuel_mopu_ltr"
                >
                  <el-input
                    v-model.number="form.diesel_fuel_mopu_ltr"
                    type="number"
                    :min="0"
                    :step="0.01"
                    :placeholder="$t('production.vessel_operation.placeholder.diesel_fuel_mopu_ltr')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">Liters</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Diesel HCML -->
              <el-col :md="6" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.diesel_fuel_hcml_ltr')" 
                  prop="diesel_fuel_hcml_ltr"
                >
                  <el-input
                    v-model.number="form.diesel_fuel_hcml_ltr"
                    type="number"
                    :min="0"
                    :step="0.01"
                    :placeholder="$t('production.vessel_operation.placeholder.diesel_fuel_hcml_ltr')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">Liters</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Produced Water Total -->
              <el-col :md="6" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.produced_water_total_bbls')" 
                  prop="produced_water_total_bbls"
                >
                  <el-input
                    v-model.number="form.produced_water_total_bbls"
                    type="number"
                    :min="0"
                    :step="0.01"
                    :placeholder="$t('production.vessel_operation.placeholder.produced_water_total_bbls')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">bbls</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Water Off-spec -->
              <el-col :md="6" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.produced_water_offspec_bbls')" 
                  prop="produced_water_offspec_bbls"
                >
                  <el-input
                    v-model.number="form.produced_water_offspec_bbls"
                    type="number"
                    :min="0"
                    :step="0.01"
                    :placeholder="$t('production.vessel_operation.placeholder.produced_water_offspec_bbls')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">bbls</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- Water Overboard -->
              <el-col :md="6" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.produced_water_overboard_bbls')" 
                  prop="produced_water_overboard_bbls"
                >
                  <el-input
                    v-model.number="form.produced_water_overboard_bbls"
                    type="number"
                    :min="0"
                    :step="0.01"
                    :placeholder="$t('production.vessel_operation.placeholder.produced_water_overboard_bbls')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">bbls</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>

              <!-- OIW Content -->
              <el-col :md="6" :sm="12" :xs="24">
                <el-form-item 
                  :label="$t('production.vessel_operation.fields.water_oiw_content_ppm')" 
                  prop="water_oiw_content_ppm"
                >
                  <el-input
                    v-model.number="form.water_oiw_content_ppm"
                    type="number"
                    :min="0"
                    :step="0.01"
                    :placeholder="$t('production.vessel_operation.placeholder.water_oiw_content_ppm')"
                  >
                    <template #suffix>
                      <span class="text-gray-500 text-sm">ppm</span>
                    </template>
                  </el-input>
                </el-form-item>
              </el-col>
            </el-row>
          </div>

          <!-- Well Flow Rates (Dynamic) -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-700 flex items-center">
              <Icon icon="mdi:oil-well" class="mr-2 text-green-600" />
              {{ $t('production.vessel_operation.sections.well_flows') }}
              <el-tag 
                v-if="totalWellFlow !== null" 
                :type="isWellBalanced ? 'success' : 'danger'" 
                class="ml-3"
                size="small"
              >
                Total: {{ totalWellFlow.toFixed(4) }} MMSCFD
                <span v-if="!isWellBalanced" class="ml-1">⚠️</span>
              </el-tag>
            </h3>
            <div class="overflow-x-auto">
              <el-table :data="form.well_flows" class="base-table" stripe>
                <el-table-column
                  :label="$t('production.vessel_operation.fields.well')"
                  prop="well.name"
                  width="200"
                />
                <el-table-column
                  :label="$t('production.vessel_operation.fields.well_flow_rate')">
                  <template #default="scope">
                    <el-input
                      v-model.number="scope.row.gas_flow_rate_mmscfd"
                      type="number"
                      :min="0"
                      :step="0.0001"
                      @input="calculateWellTotal"
                      :placeholder="$t('production.vessel_operation.placeholder.well_flow_rate')"
                    >
                      <template #suffix>
                        <span class="text-gray-500 text-sm">MMSCFD</span>
                      </template>
                    </el-input>
                  </template>
                </el-table-column>
              </el-table>
            </div>
            <el-alert
              v-if="!isWellBalanced && totalWellFlow !== null"
              :title="$t('production.vessel_operation.validation.well_balance_warning')"
              type="warning"
              class="mt-3"
              show-icon
              :closable="false"
            >
              <template #default>
                Inlet Gas: {{ form.inlet_gas_mmscfd || 0 }} MMSCFD | 
                Well Total: {{ totalWellFlow.toFixed(4) }} MMSCFD | 
                Difference: {{ Math.abs((form.inlet_gas_mmscfd || 0) - totalWellFlow).toFixed(4) }} MMSCFD
              </template>
            </el-alert>
          </div>

          <!-- Remarks -->
          <div class="mb-2">
            <el-form-item :label="$t('production.vessel_operation.fields.remarks')" prop="remarks">
              <el-input
                v-model="form.remarks"
                type="textarea"
                :rows="3"
                :placeholder="$t('production.vessel_operation.placeholder.remarks')"
                maxlength="500"
                show-word-limit
              />
            </el-form-item>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="p-6 border-t bg-gray-50">
          <div class="flex justify-between items-center">
            <!-- Left side -->
            <div class="flex space-x-3">
              <el-button v-if="isEdit" @click="onDuplicate" type="info" plain>
                <Icon icon="mingcute:copy-line" class="mr-2" />
                {{ $t('common.actions.duplicate') }}
              </el-button>
            </div>
            
            <!-- Right side -->
            <div class="flex space-x-3">
              <el-button @click="onBack">
                <Icon icon="mingcute:arrow-left-line" class="mr-2" />
                {{ $t('common.actions.cancel') }}
              </el-button>
              <el-button native-type="submit" type="primary" :loading="loading">
                <Icon icon="mingcute:check-fill" class="mr-2" />
                {{ $t('common.actions.save') }}
              </el-button>
            </div>
          </div>
        </div>
      </el-form>
    </el-card>
  </div>
</template>

<script setup lang="js">
import { Icon } from '@iconify/vue'
import { ref, computed, onMounted, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { ElMessage } from 'element-plus'
import PageHeader from '@/components/PageHeader.vue'
import SelectVessel from '@/components/select/SelectVessel.vue'
import { useUser } from '@/composables/auth/useUser'
import dayjs from 'dayjs'

const { userVesselId } = useUser()
const router = useRouter()
const route = useRoute()
const { t } = useI18n()

const title = ref(route.meta.title ?? 'production.vessel_operation.create')
const formRef = ref(null)
const loading = ref(false)
const isEdit = computed(() => !!route.params.id)

// ============================================
// FORM STATE
// ============================================
const form = reactive({
  vessel_id: userVesselId || null,
  date: dayjs().format('YYYY-MM-DD'),
  
  // Gas Operations
  inlet_gas_mmscfd: null,
  total_sales_gas_mmscfd: null,
  fuel_gas_mmscfd: null,
  flare_hp_mmscfd: null,
  flare_lp_mmscfd: null,
  total_flare_gas_mmscfd: null,
  gas_export_uptime: null,
  inlet_pressure_psi: null,
  inlet_temp_f: null,
  outlet_pressure_psi: null,
  outlet_temp_f: null,
  
  // Condensate Operations
  condensate_produced_lts: null,
  condensate_produced_bbls: null,
  condensate_skimmed_bbls: null,
  condensate_production_total_bbls: null,
  condensate_stock_bbls: null,
  condensate_consumed_gtg_bbls: null,
  condensate_temp_f: null,
  condensate_uptime: null,
  
  // Diesel Fuel
  diesel_fuel_mopu_ltr: null,
  diesel_fuel_hcml_ltr: null,
  
  // Water Operations
  produced_water_total_bbls: null,
  produced_water_offspec_bbls: null,
  produced_water_overboard_bbls: null,
  water_oiw_content_ppm: null,
  
  // Well Flows (dynamic)
  well_flows: [],
  
  remarks: ''
})

// ============================================
// COMPUTED PROPERTIES
// ============================================

// Total well flow (sum of all wells)
const totalWellFlow = computed(() => {
  if (!form.well_flows || form.well_flows.length === 0) return null
  return form.well_flows.reduce((sum, wf) => sum + (wf.gas_flow_rate_mmscfd || 0), 0)
})

// Check if well balance is correct (inlet gas ≈ sum of wells)
const isWellBalanced = computed(() => {
  if (!form.inlet_gas_mmscfd || totalWellFlow.value === null) return true
  const difference = Math.abs(form.inlet_gas_mmscfd - totalWellFlow.value)
  return difference < 0.01 // Tolerance 0.01 MMSCFD
})

// ============================================
// VALIDATION HELPERS
// ============================================

const now = () => new Date()

// Disable future dates
const disabledDate = (date) => date.getTime() > now().getTime()

const requiredMsg = (attr) => t('common.validation.required', { attribute: attr })

// Generic numeric range validator
const numberRange = (min, max, attr, opts = {}) => ({
  validator: (_rule, value, callback) => {
    const allowNull = !!opts.allowNull
    if (allowNull && (value === null || value === undefined || value === '')) return callback()
    if (!allowNull && (value === null || value === undefined || value === '')) {
      return callback(new Error(requiredMsg(attr)))
    }
    if (value !== null && value !== undefined && value !== '') {
      const numValue = Number(value)
      if (Number.isNaN(numValue)) {
        return callback(new Error(t('common.validation.numeric', { attribute: attr })))
      }
      if (min !== undefined && numValue < min) {
        return callback(new Error(t('common.validation.min.numeric', { attribute: attr, min })))
      }
      if (max !== undefined && numValue > max) {
        return callback(new Error(t('common.validation.max.numeric', { attribute: attr, max })))
      }
    }
    callback()
  },
  trigger: opts.trigger || ['change', 'blur']
})

// Date validator (must not be future)
const notFutureDate = {
  validator: (_rule, value, callback) => {
    if (!value) return callback(new Error(requiredMsg('Operation Date')))
    const opDate = dayjs(value)
    if (!opDate.isValid()) {
      return callback(new Error('Invalid date format'))
    }
    if (opDate.isAfter(dayjs(), 'day')) {
      return callback(new Error('Date cannot be in the future'))
    }
    callback()
  },
  trigger: ['change', 'blur']
}

// ============================================
// VALIDATION RULES
// ============================================
const formRules = ref({
  vessel_id: [
    { required: true, message: 'Vessel is required', trigger: 'change' }
  ],
  date: [notFutureDate],
  remarks: [
    { max: 500, message: 'Remarks cannot exceed 500 characters', trigger: 'blur' }
  ]
})

// ============================================
// AUTO-CALCULATIONS
// ============================================

// Calculate total flare = HP + LP
const calculateTotalFlare = () => {
  const hp = form.flare_hp_mmscfd || 0
  const lp = form.flare_lp_mmscfd || 0
  form.total_flare_gas_mmscfd = hp + lp
}

// Convert Liters to Barrels (1 bbl = 158.987 L)
const convertLitersToBbls = () => {
  if (form.condensate_produced_lts) {
    form.condensate_produced_bbls = form.condensate_produced_lts / 158.987
    calculateCondensateTotal()
  } else {
    form.condensate_produced_bbls = null
  }
}

// Calculate condensate total = produced + skimmed
const calculateCondensateTotal = () => {
  const produced = form.condensate_produced_bbls || 0
  const skimmed = form.condensate_skimmed_bbls || 0
  form.condensate_production_total_bbls = produced + skimmed
}

// ============================================
// DATA LOADING
// ============================================

// Load wells when vessel changes
const loadWells = async () => {
  if (!form.vessel_id) return
  
  try {
    loading.value = true
    form.well_flows = []
    
    // NOTE: Replace axios with your HTTP client
    const { data } = await axios.get(`/master/wells`, { 
      params: { vessel_id: form.vessel_id } 
    })
    
    const wells = data?.data || data
    if (wells && wells.length > 0) {
      wells.forEach(well => {
        form.well_flows.push({
          well_id: well.id,
          well: {
            id: well.id,
            code: well.code,
            name: well.name,
          },
          gas_flow_rate_mmscfd: null,
        })
      })
    }
  } catch (e) {
    console.error('Load wells error:', e)
    ElMessage.error('Failed to load wells')
  } finally {
    loading.value = false
  }
}

// Load existing data (edit mode)
const loadData = async () => {
  if (!isEdit.value) return
  
  try {
    loading.value = true
    
    // NOTE: Replace axios with your HTTP client
    const { data } = await axios.get(`/production/vessel-operation/${route.params.id}`)
    const d = data?.data || data
    
    if (d) {
      // Basic info
      form.vessel_id = d.vessel_id
      form.date = d.date
      
      // Gas Operations
      form.inlet_gas_mmscfd = d.inlet_gas_mmscfd
      form.total_sales_gas_mmscfd = d.total_sales_gas_mmscfd
      form.fuel_gas_mmscfd = d.fuel_gas_mmscfd
      form.flare_hp_mmscfd = d.flare_hp_mmscfd
      form.flare_lp_mmscfd = d.flare_lp_mmscfd
      form.total_flare_gas_mmscfd = d.total_flare_gas_mmscfd
      form.gas_export_uptime = d.gas_export_uptime
      form.inlet_pressure_psi = d.inlet_pressure_psi
      form.inlet_temp_f = d.inlet_temp_f
      form.outlet_pressure_psi = d.outlet_pressure_psi
      form.outlet_temp_f = d.outlet_temp_f
      
      // Condensate Operations
      form.condensate_produced_lts = d.condensate_produced_lts
      form.condensate_produced_bbls = d.condensate_produced_bbls
      form.condensate_skimmed_bbls = d.condensate_skimmed_bbls
      form.condensate_production_total_bbls = d.condensate_production_total_bbls
      form.condensate_stock_bbls = d.condensate_stock_bbls
      form.condensate_consumed_gtg_bbls = d.condensate_consumed_gtg_bbls
      form.condensate_temp_f = d.condensate_temp_f
      form.condensate_uptime = d.condensate_uptime
      
      // Diesel & Water
      form.diesel_fuel_mopu_ltr = d.diesel_fuel_mopu_ltr
      form.diesel_fuel_hcml_ltr = d.diesel_fuel_hcml_ltr
      form.produced_water_total_bbls = d.produced_water_total_bbls
      form.produced_water_offspec_bbls = d.produced_water_offspec_bbls
      form.produced_water_overboard_bbls = d.produced_water_overboard_bbls
      form.water_oiw_content_ppm = d.water_oiw_content_ppm
      
      // Well Flows
      if (d.well_flows && d.well_flows.length > 0) {
        form.well_flows = d.well_flows.map(wf => ({
          well_id: wf.well_id,
          well: wf.well,
          gas_flow_rate_mmscfd: wf.gas_flow_rate_mmscfd
        }))
      }
      
      form.remarks = d.remarks || ''
    }
  } catch (e) {
    console.error('Load error:', e)
    ElMessage.error('Failed to load data')
    onBack()
  } finally {
    loading.value = false
  }
}

// ============================================
// FORM SUBMIT
// ============================================
const onSubmit = async () => {
  if (!formRef.value) return
  
  formRef.value.validate(async (valid) => {
    if (!valid) {
      return ElMessage({ message: 'Please correct the form errors', type: 'error' })
    }

    try {
      loading.value = true

      const url = isEdit.value
        ? `/production/vessel-operation/${route.params.id}/update`
        : '/production/vessel-operation/store'
      const method = isEdit.value ? 'put' : 'post'

      // NOTE: Replace axios with your HTTP client
      const { status, data } = await axios({ method, url, data: form })

      if (status === 200 || status === 201) {
        ElMessage({ message: 'Data saved successfully', type: 'success' })
        const id = data?.id || data?.data?.id
        if (id) {
          router.replace({ path: `/production/operation/${id}` })
        } else {
          router.push({ name: 'production.operation.index' })
        }
      }
    } catch (error) {
      console.error('Submit error:', error)
      if (error?.response?.status === 422) {
        const errors = error.response.data.errors
        let msg = 'Validation failed'
        if (errors) {
          const first = Object.values(errors)[0]
          if (first && first[0]) msg = first[0]
        }
        ElMessage({ message: msg, type: 'error' })
      } else if (error?.response?.status === 409) {
        ElMessage({ message: 'Duplicate entry: Operation for this vessel and date already exists', type: 'error' })
      } else {
        ElMessage({ message: 'Server error occurred', type: 'error' })
      }
    } finally {
      loading.value = false
    }
  })
}

// ============================================
// ACTIONS
// ============================================

// Back to list
const onBack = () => {
  router.push({ name: 'production.operation.index' })
}

// Duplicate current entry
const onDuplicate = () => {
  const duplicateData = { ...form }
  duplicateData.date = dayjs().format('YYYY-MM-DD')
  duplicateData.remarks = `Copy of: ${form.remarks || 'Previous entry'}`
  
  router.push({ 
    name: 'production.operation.create',
    query: { duplicate: JSON.stringify(duplicateData) }
  })
}

// ============================================
// LIFECYCLE
// ============================================
onMounted(() => {
  // Handle duplicate data from query
  if (route.query.duplicate) {
    try {
      const duplicateData = JSON.parse(route.query.duplicate)
      Object.assign(form, duplicateData)
    } catch (e) {
      console.error('Error parsing duplicate data:', e)
    }
  }
  
  // Load wells if vessel is pre-selected
  if (form.vessel_id) {
    loadWells()
  }
  
  // Load existing data (edit mode)
  loadData()
})
</script>

<style scoped>
.base-table :deep(.el-input) {
  width: 100%;
}

.base-table :deep(.el-input__inner) {
  text-align: right;
}
</style>