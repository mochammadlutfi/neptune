<template>
  <div class="content">
    <!-- Page Header -->
    <PageHeader :title="$t(title)">
      <template #actions>
        <div class="flex items-center gap-3">
          <!-- Status Badge -->
          <el-tag v-if="isEdit" type="info" size="large">
            {{ form.operation_date }}
          </el-tag>
          
          <el-tooltip content="Help" placement="bottom">
            <el-button circle>
              <Icon icon="mingcute:question-line" />
            </el-button>
          </el-tooltip>
        </div>
      </template>
    </PageHeader>

    <!-- Main Form Card -->
    <el-card class="!rounded-xl !shadow-sm border-0" v-loading="loading">
      <el-form
        ref="formRef"
        :model="form"
        :rules="formRules"
        label-position="top"
        @submit.prevent="onSubmit"
      >
        <!-- Basic Info Section (Always Visible) -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 -m-6 mb-6 p-6 rounded-t-xl">
          <h3 class="text-base font-semibold text-gray-800 mb-4 flex items-center gap-2">
            <Icon icon="mdi:information-outline" class="text-xl text-blue-600" />
            {{ $t('production.vessel_operation.sections.basic_info') }}
          </h3>
          <el-row :gutter="24">
            <el-col :lg="12" :md="12" :sm="24" :xs="24">
              <el-form-item 
                :label="$t('production.vessel_operation.fields.vessel_id')" 
                prop="vessel_id"
              >
                <select-vessel
                  v-model="form.vessel_id"
                  :vessel-id="form.vessel_id"
                  :placeholder="$t('production.vessel_operation.placeholder.vessel_id')"
                  @change="loadWells"
                  size="large"
                />
              </el-form-item>
            </el-col>

            <el-col :lg="12" :md="12" :sm="24" :xs="24">
              <el-form-item 
                :label="$t('production.vessel_operation.fields.operation_date')" 
                prop="operation_date"
              >
                <el-date-picker
                  v-model="form.operation_date"
                  type="date"
                  :placeholder="$t('production.vessel_operation.placeholder.operation_date')"
                  format="DD-MM-YYYY"
                  value-format="YYYY-MM-DD"
                  style="width: 100%"
                  size="large"
                  :disabled-date="disabledDate"
                />
              </el-form-item>
            </el-col>
          </el-row>
        </div>

        <!-- Tabs for Different Sections -->
        <el-tabs v-model="activeTab" type="card" class="custom-tabs">
          
          <!-- Tab 1: Gas Operations -->
          <el-tab-pane name="gas">
            <template #label>
              <div class="flex items-center gap-2 px-2 py-1">
                <Icon icon="mdi:gas-cylinder" class="text-lg" />
                <span class="font-medium">Gas Operations</span>
              </div>
            </template>
            
            <div class="p-6 space-y-6">
              <!-- Production Section -->
              <div class="space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-gray-200">
                  <Icon icon="mdi:chart-line" class="text-blue-600 text-xl" />
                  <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Production</h4>
                </div>
                <el-row :gutter="20">
                  <el-col :lg="8" :md="12" :xs="24">
                    <el-form-item label="Inlet Gas" prop="inlet_gas_mmscfd">
                      <el-input
                        v-model.number="form.inlet_gas_mmscfd"
                        type="number"
                        :min="0"
                        :step="0.0001"
                        placeholder="0.0000"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">MMSCFD</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="8" :md="12" :xs="24">
                    <el-form-item label="Sales Gas" prop="total_sales_gas_mmscfd">
                      <el-input
                        v-model.number="form.total_sales_gas_mmscfd"
                        type="number"
                        :min="0"
                        :step="0.0001"
                        placeholder="0.0000"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">MMSCFD</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="8" :md="12" :xs="24">
                    <el-form-item label="Fuel Gas" prop="fuel_gas_mmscfd">
                      <el-input
                        v-model.number="form.fuel_gas_mmscfd"
                        type="number"
                        :min="0"
                        :step="0.0001"
                        placeholder="0.0000"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">MMSCFD</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>
                </el-row>
              </div>

              <!-- Flare Section -->
              <div class="space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-gray-200">
                  <Icon icon="mdi:fire" class="text-orange-600 text-xl" />
                  <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Flare</h4>
                </div>
                <el-row :gutter="20">
                  <el-col :lg="8" :md="12" :xs="24">
                    <el-form-item label="Flare HP" prop="flare_hp_mmscfd">
                      <el-input
                        v-model.number="form.flare_hp_mmscfd"
                        type="number"
                        :min="0"
                        :step="0.0001"
                        @input="calculateTotalFlare"
                        placeholder="0.0000"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">MMSCFD</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="8" :md="12" :xs="24">
                    <el-form-item label="Flare LP" prop="flare_lp_mmscfd">
                      <el-input
                        v-model.number="form.flare_lp_mmscfd"
                        type="number"
                        :min="0"
                        :step="0.0001"
                        @input="calculateTotalFlare"
                        placeholder="0.0000"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">MMSCFD</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="8" :md="12" :xs="24">
                    <el-form-item label="Total Flare">
                      <el-input
                        v-model.number="form.total_flare_gas_mmscfd"
                        type="number"
                        readonly
                        size="large"
                        class="readonly-input"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">MMSCFD</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>
                </el-row>
              </div>

              <!-- Conditions Section -->
              <div class="space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-gray-200">
                  <Icon icon="mdi:gauge" class="text-indigo-600 text-xl" />
                  <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Conditions & Uptime</h4>
                </div>
                <el-row :gutter="20">
                  <el-col :lg="12" :md="12" :xs="24">
                    <el-form-item label="Gas Export Uptime" prop="gas_export_uptime">
                      <el-time-picker
                        v-model="form.gas_export_uptime"
                        format="HH:mm:ss"
                        value-format="HH:mm:ss"
                        placeholder="00:00:00"
                        style="width: 100%"
                        size="large"
                      />
                    </el-form-item>
                  </el-col>

                  <el-col :lg="6" :md="12" :xs="24">
                    <el-form-item label="Inlet Pressure" prop="inlet_pressure_psi">
                      <el-input
                        v-model.number="form.inlet_pressure_psi"
                        type="number"
                        :step="0.01"
                        placeholder="0.00"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">PSI</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="6" :md="12" :xs="24">
                    <el-form-item label="Inlet Temp" prop="inlet_temp_f">
                      <el-input
                        v-model.number="form.inlet_temp_f"
                        type="number"
                        :step="0.01"
                        placeholder="0.00"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">°F</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="6" :md="12" :xs="24">
                    <el-form-item label="Outlet Pressure" prop="outlet_pressure_psi">
                      <el-input
                        v-model.number="form.outlet_pressure_psi"
                        type="number"
                        :step="0.01"
                        placeholder="0.00"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">PSI</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="6" :md="12" :xs="24">
                    <el-form-item label="Outlet Temp" prop="outlet_temp_f">
                      <el-input
                        v-model.number="form.outlet_temp_f"
                        type="number"
                        :step="0.01"
                        placeholder="0.00"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">°F</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>
                </el-row>
              </div>
            </div>
          </el-tab-pane>

          <!-- Tab 2: Condensate Operations -->
          <el-tab-pane name="condensate">
            <template #label>
              <div class="flex items-center gap-2 px-2 py-1">
                <Icon icon="mdi:oil" class="text-lg" />
                <span class="font-medium">Condensate</span>
              </div>
            </template>
            
            <div class="p-6 space-y-6">
              <!-- Production -->
              <div class="space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-gray-200">
                  <Icon icon="mdi:chart-bar" class="text-amber-600 text-xl" />
                  <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Production</h4>
                </div>
                <el-row :gutter="20">
                  <el-col :lg="8" :md="12" :xs="24">
                    <el-form-item label="Produced (Liters)" prop="condensate_produced_lts">
                      <el-input
                        v-model.number="form.condensate_produced_lts"
                        type="number"
                        :min="0"
                        :step="0.01"
                        @input="convertLitersToBbls"
                        placeholder="0.00"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">Liters</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="8" :md="12" :xs="24">
                    <el-form-item label="Produced (Barrels)">
                      <el-input
                        v-model.number="form.condensate_produced_bbls"
                        type="number"
                        readonly
                        size="large"
                        class="readonly-input"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">bbls</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="8" :md="12" :xs="24">
                    <el-form-item label="Skimmed" prop="condensate_skimmed_bbls">
                      <el-input
                        v-model.number="form.condensate_skimmed_bbls"
                        type="number"
                        :min="0"
                        :step="0.01"
                        @input="calculateCondensateTotal"
                        placeholder="0.00"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">bbls</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="8" :md="12" :xs="24">
                    <el-form-item label="Total Production">
                      <el-input
                        v-model.number="form.condensate_production_total_bbls"
                        type="number"
                        readonly
                        size="large"
                        class="readonly-input"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">bbls</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="8" :md="12" :xs="24">
                    <el-form-item label="Stock" prop="condensate_stock_bbls">
                      <el-input
                        v-model.number="form.condensate_stock_bbls"
                        type="number"
                        :min="0"
                        :step="0.01"
                        placeholder="0.00"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">bbls</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="8" :md="12" :xs="24">
                    <el-form-item label="Consumed by GTG" prop="condensate_consumed_gtg_bbls">
                      <el-input
                        v-model.number="form.condensate_consumed_gtg_bbls"
                        type="number"
                        :min="0"
                        :step="0.01"
                        placeholder="0.00"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">bbls</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>
                </el-row>
              </div>

              <!-- Conditions -->
              <div class="space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-gray-200">
                  <Icon icon="mdi:thermometer" class="text-amber-600 text-xl" />
                  <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Conditions & Uptime</h4>
                </div>
                <el-row :gutter="20">
                  <el-col :lg="12" :md="12" :xs="24">
                    <el-form-item label="Temperature" prop="condensate_temp_f">
                      <el-input
                        v-model.number="form.condensate_temp_f"
                        type="number"
                        :step="0.01"
                        placeholder="0.00"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">°F</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="12" :md="12" :xs="24">
                    <el-form-item label="Uptime" prop="condensate_uptime">
                      <el-time-picker
                        v-model="form.condensate_uptime"
                        format="HH:mm:ss"
                        value-format="HH:mm:ss"
                        placeholder="00:00:00"
                        style="width: 100%"
                        size="large"
                      />
                    </el-form-item>
                  </el-col>
                </el-row>
              </div>
            </div>
          </el-tab-pane>

          <!-- Tab 3: Fuel & Water -->
          <el-tab-pane name="fuel">
            <template #label>
              <div class="flex items-center gap-2 px-2 py-1">
                <Icon icon="mdi:fuel" class="text-lg" />
                <span class="font-medium">Fuel & Water</span>
              </div>
            </template>
            
            <div class="p-6 space-y-6">
              <!-- Diesel Fuel -->
              <div class="space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-gray-200">
                  <Icon icon="mdi:gas-station" class="text-red-600 text-xl" />
                  <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Diesel Fuel</h4>
                </div>
                <el-row :gutter="20">
                  <el-col :lg="12" :md="12" :xs="24">
                    <el-form-item label="Diesel MOPU" prop="diesel_fuel_mopu_ltr">
                      <el-input
                        v-model.number="form.diesel_fuel_mopu_ltr"
                        type="number"
                        :min="0"
                        :step="0.01"
                        placeholder="0.00"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">Liters</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="12" :md="12" :xs="24">
                    <el-form-item label="Diesel HCML" prop="diesel_fuel_hcml_ltr">
                      <el-input
                        v-model.number="form.diesel_fuel_hcml_ltr"
                        type="number"
                        :min="0"
                        :step="0.01"
                        placeholder="0.00"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">Liters</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>
                </el-row>
              </div>

              <!-- Produced Water -->
              <div class="space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-gray-200">
                  <Icon icon="mdi:water" class="text-blue-600 text-xl" />
                  <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Produced Water</h4>
                </div>
                <el-row :gutter="20">
                  <el-col :lg="6" :md="12" :xs="24">
                    <el-form-item label="Total" prop="produced_water_total_bbls">
                      <el-input
                        v-model.number="form.produced_water_total_bbls"
                        type="number"
                        :min="0"
                        :step="0.01"
                        placeholder="0.00"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">bbls</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="6" :md="12" :xs="24">
                    <el-form-item label="Off-spec" prop="produced_water_offspec_bbls">
                      <el-input
                        v-model.number="form.produced_water_offspec_bbls"
                        type="number"
                        :min="0"
                        :step="0.01"
                        placeholder="0.00"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">bbls</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="6" :md="12" :xs="24">
                    <el-form-item label="Overboard" prop="produced_water_overboard_bbls">
                      <el-input
                        v-model.number="form.produced_water_overboard_bbls"
                        type="number"
                        :min="0"
                        :step="0.01"
                        placeholder="0.00"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">bbls</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>

                  <el-col :lg="6" :md="12" :xs="24">
                    <el-form-item label="OIW Content" prop="water_oiw_content_ppm">
                      <el-input
                        v-model.number="form.water_oiw_content_ppm"
                        type="number"
                        :min="0"
                        :step="0.01"
                        placeholder="0.00"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">ppm</span>
                        </template>
                      </el-input>
                    </el-form-item>
                  </el-col>
                </el-row>
              </div>
            </div>
          </el-tab-pane>

          <!-- Tab 4: Wells -->
          <el-tab-pane name="wells">
            <template #label>
              <div class="flex items-center gap-2 px-2 py-1">
                <Icon icon="mdi:oil-well" class="text-lg" />
                <span class="font-medium">Wells</span>
                <el-badge 
                  v-if="form.well_flows.length > 0" 
                  :value="form.well_flows.length" 
                  type="primary"
                  class="ml-1"
                />
              </div>
            </template>
            
            <div class="p-6">
              <!-- Summary Card -->
              <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-gray-600 mb-1">Total Well Flow</p>
                    <p class="text-2xl font-bold text-green-700">
                      {{ totalWellFlow !== null ? totalWellFlow.toFixed(4) : '0.0000' }} 
                      <span class="text-sm font-normal text-gray-500">MMSCFD</span>
                    </p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm text-gray-600 mb-1">Inlet Gas</p>
                    <p class="text-2xl font-bold" :class="isWellBalanced ? 'text-green-700' : 'text-orange-600'">
                      {{ form.inlet_gas_mmscfd || '0.0000' }}
                      <span class="text-sm font-normal text-gray-500">MMSCFD</span>
                    </p>
                  </div>
                  <div>
                    <el-tag :type="isWellBalanced ? 'success' : 'warning'" size="large">
                      <Icon :icon="isWellBalanced ? 'mdi:check-circle' : 'mdi:alert'" class="mr-1" />
                      {{ isWellBalanced ? 'Balanced' : 'Unbalanced' }}
                    </el-tag>
                  </div>
                </div>
              </div>

              <!-- Wells Table -->
              <div class="rounded-lg border border-gray-200 overflow-hidden">
                <el-table :data="form.well_flows" style="width: 100%" stripe>
                  <el-table-column label="Well" width="200">
                    <template #default="scope">
                      <div class="flex items-center gap-2">
                        <Icon icon="mdi:oil-well" class="text-green-600" />
                        <span class="font-medium">{{ scope.row.well.name }}</span>
                      </div>
                    </template>
                  </el-table-column>
                  
                  <el-table-column label="Gas Flow Rate (MMSCFD)" min-width="250">
                    <template #default="scope">
                      <el-input
                        v-model.number="scope.row.gas_flow_rate_mmscfd"
                        type="number"
                        :min="0"
                        :step="0.0001"
                        placeholder="0.0000"
                        size="large"
                      >
                        <template #suffix>
                          <span class="text-xs text-gray-400 font-medium">MMSCFD</span>
                        </template>
                      </el-input>
                    </template>
                  </el-table-column>

                  <el-table-column label="% of Total" width="150" align="right">
                    <template #default="scope">
                      <span class="text-sm font-medium text-gray-600">
                        {{ totalWellFlow > 0 ? ((scope.row.gas_flow_rate_mmscfd || 0) / totalWellFlow * 100).toFixed(1) : '0.0' }}%
                      </span>
                    </template>
                  </el-table-column>
                </el-table>
              </div>

              <!-- Warning Alert -->
              <el-alert
                v-if="!isWellBalanced && totalWellFlow !== null"
                type="warning"
                class="mt-4"
                show-icon
                :closable="false"
              >
                <template #title>
                  <span class="font-semibold">Balance Warning</span>
                </template>
                <div class="text-sm">
                  Difference: <strong>{{ Math.abs((form.inlet_gas_mmscfd || 0) - totalWellFlow).toFixed(4) }} MMSCFD</strong>
                  <span class="text-gray-600 ml-2">Please verify your data</span>
                </div>
              </el-alert>
            </div>
          </el-tab-pane>

          <!-- Tab 5: Remarks -->
          <el-tab-pane name="remarks">
            <template #label>
              <div class="flex items-center gap-2 px-2 py-1">
                <Icon icon="mdi:note-text" class="text-lg" />
                <span class="font-medium">Remarks</span>
              </div>
            </template>
            
            <div class="p-6">
              <el-form-item label="Additional Notes" prop="remarks">
                <el-input
                  v-model="form.remarks"
                  type="textarea"
                  :rows="8"
                  placeholder="Enter any additional remarks or notes about this operation..."
                  maxlength="500"
                  show-word-limit
                  size="large"
                />
              </el-form-item>
            </div>
          </el-tab-pane>
        </el-tabs>

        <!-- Form Actions (Sticky Bottom) -->
        <div class="sticky bottom-0 -mx-6 -mb-6 px-6 py-4 bg-white border-t border-gray-200 rounded-b-xl">
          <div class="flex justify-between items-center">
            <div>
              <el-button v-if="isEdit" @click="onDuplicate" size="large">
                <Icon icon="mingcute:copy-line" class="mr-2" />
                Duplicate
              </el-button>
            </div>
            
            <div class="flex gap-3">
              <el-button @click="onBack" size="large">
                <Icon icon="mingcute:arrow-left-line" class="mr-2" />
                Cancel
              </el-button>
              <el-button native-type="submit" type="primary" :loading="loading" size="large">
                <Icon icon="mingcute:check-fill" class="mr-2" />
                {{ isEdit ? 'Update' : 'Save' }} Operation
              </el-button>
            </div>
          </div>
        </div>
      </el-form>
    </el-card>
  </div>
</template>

<script setup>
// ============================================
// COMPLETE SCRIPT - CLEAN LAYOUT
// ============================================

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

// ============================================
// STATE
// ============================================
const title = ref(route.meta.title ?? 'production.vessel_operation.create')
const formRef = ref(null)
const loading = ref(false)
const activeTab = ref('gas') // Active tab state
const isEdit = computed(() => !!route.params.id)

// Form state
const form = reactive({
  vessel_id: userVesselId || null,
  operation_date: dayjs().format('YYYY-MM-DD'),
  
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

// Total well flow
const totalWellFlow = computed(() => {
  if (!form.well_flows || form.well_flows.length === 0) return null
  return form.well_flows.reduce((sum, wf) => sum + (wf.gas_flow_rate_mmscfd || 0), 0)
})

// Check balance
const isWellBalanced = computed(() => {
  if (!form.inlet_gas_mmscfd || totalWellFlow.value === null) return true
  const difference = Math.abs(form.inlet_gas_mmscfd - totalWellFlow.value)
  return difference < 0.01
})

// ============================================
// VALIDATION
// ============================================

const now = () => new Date()
const disabledDate = (date) => date.getTime() > now().getTime()
const requiredMsg = (attr) => t('common.validation.required', { attribute: attr })

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

const formRules = ref({
  vessel_id: [
    { required: true, message: 'Vessel is required', trigger: 'change' }
  ],
  operation_date: [notFutureDate],
  remarks: [
    { max: 500, message: 'Remarks cannot exceed 500 characters', trigger: 'blur' }
  ]
})

// ============================================
// AUTO-CALCULATIONS
// ============================================

const calculateTotalFlare = () => {
  const hp = form.flare_hp_mmscfd || 0
  const lp = form.flare_lp_mmscfd || 0
  form.total_flare_gas_mmscfd = hp + lp
}

const convertLitersToBbls = () => {
  if (form.condensate_produced_lts) {
    form.condensate_produced_bbls = form.condensate_produced_lts / 158.987
    calculateCondensateTotal()
  } else {
    form.condensate_produced_bbls = null
  }
}

const calculateCondensateTotal = () => {
  const produced = form.condensate_produced_bbls || 0
  const skimmed = form.condensate_skimmed_bbls || 0
  form.condensate_production_total_bbls = produced + skimmed
}

// ============================================
// DATA LOADING
// ============================================

const loadWells = async () => {
  if (!form.vessel_id) return
  
  try {
    loading.value = true
    form.well_flows = []
    
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
      
      // Auto-switch to wells tab if wells loaded
      if (wells.length > 0 && !isEdit.value) {
        ElMessage.success(`${wells.length} wells loaded for this vessel`)
      }
    }
  } catch (e) {
    console.error('Load wells error:', e)
    ElMessage.error('Failed to load wells')
  } finally {
    loading.value = false
  }
}

const loadData = async () => {
  if (!isEdit.value) return
  
  try {
    loading.value = true
    const { data } = await axios.get(`/production/vessel-operations/${route.params.id}`)
    const d = data?.data || data
    
    if (d) {
      // Basic info
      form.vessel_id = d.vessel_id
      form.operation_date = d.operation_date
      
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
      ElMessage({ 
        message: 'Please check all required fields', 
        type: 'error' 
      })
      
      // Auto-navigate to first tab with error
      const firstErrorField = Object.keys(formRef.value.fields).find(
        field => formRef.value.fields[field].validateState === 'error'
      )
      
      if (firstErrorField) {
        // Determine which tab has the error
        if (['vessel_id', 'operation_date'].includes(firstErrorField)) {
          // Basic info is always visible, no tab change needed
        } else if (firstErrorField.includes('gas') || firstErrorField.includes('inlet') || firstErrorField.includes('outlet') || firstErrorField.includes('flare')) {
          activeTab.value = 'gas'
        } else if (firstErrorField.includes('condensate')) {
          activeTab.value = 'condensate'
        } else if (firstErrorField.includes('diesel') || firstErrorField.includes('water')) {
          activeTab.value = 'fuel'
        } else if (firstErrorField.includes('well')) {
          activeTab.value = 'wells'
        } else if (firstErrorField === 'remarks') {
          activeTab.value = 'remarks'
        }
      }
      
      return
    }

    // Validate well balance before submit
    if (!isWellBalanced.value && form.well_flows.length > 0) {
      const confirmed = await ElMessageBox.confirm(
        `Well flow total (${totalWellFlow.value.toFixed(4)}) does not match Inlet Gas (${form.inlet_gas_mmscfd}). Continue anyway?`,
        'Balance Warning',
        {
          confirmButtonText: 'Continue',
          cancelButtonText: 'Cancel',
          type: 'warning'
        }
      ).catch(() => false)
      
      if (!confirmed) {
        activeTab.value = 'wells'
        return
      }
    }

    try {
      loading.value = true

      const url = isEdit.value
        ? `/production/vessel-operations/${route.params.id}`
        : '/production/vessel-operations'
      const method = isEdit.value ? 'put' : 'post'

      const { status, data } = await axios({ method, url, data: form })

      if (status === 200 || status === 201) {
        ElMessage({ 
          message: isEdit.value ? 'Operation updated successfully' : 'Operation saved successfully', 
          type: 'success' 
        })
        
        const id = data?.id || data?.data?.id
        if (id && !isEdit.value) {
          router.replace({ path: `/production/vessel-operations/${id}` })
        } else {
          router.push({ name: 'production.vessel-operations.index' })
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
        ElMessage({ message: msg, type: 'error', duration: 5000 })
      } else if (error?.response?.status === 409) {
        ElMessage({ 
          message: 'Duplicate entry: Operation for this vessel and date already exists', 
          type: 'error',
          duration: 5000
        })
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

const onBack = () => {
  router.push({ name: 'production.vessel-operations.index' })
}

const onDuplicate = () => {
  const duplicateData = { ...form }
  duplicateData.operation_date = dayjs().format('YYYY-MM-DD')
  duplicateData.remarks = `Copy of: ${form.remarks || 'Previous entry'}`
  
  router.push({ 
    name: 'production.vessel-operations.create',
    query: { duplicate: JSON.stringify(duplicateData) }
  })
}

// ============================================
// LIFECYCLE
// ============================================

onMounted(() => {
  // Handle duplicate data
  if (route.query.duplicate) {
    try {
      const duplicateData = JSON.parse(route.query.duplicate)
      Object.assign(form, duplicateData)
    } catch (e) {
      console.error('Error parsing duplicate data:', e)
    }
  }
  
  // Load wells if vessel pre-selected
  if (form.vessel_id) {
    loadWells()
  }
  
  // Load existing data (edit mode)
  loadData()
})
</script>

<style scoped>
/* Clean Input Styling */
:deep(.el-input__wrapper) {
  border-radius: 8px;
  transition: all 0.2s;
}

:deep(.el-input__wrapper:hover) {
  box-shadow: 0 0 0 1px #409eff;
}

:deep(.el-input.is-disabled .el-input__wrapper) {
  background-color: #f5f7fa;
}

/* Readonly Input */
.readonly-input :deep(.el-input__wrapper) {
  background-color: #f8fafc;
  border-color: #e5e7eb;
}

.readonly-input :deep(.el-input__inner) {
  color: #6b7280;
  font-weight: 600;
}

/* Custom Tabs */
.custom-tabs :deep(.el-tabs__header) {
  margin-bottom: 0;
}

.custom-tabs :deep(.el-tabs__item) {
  padding: 12px 20px;
  font-weight: 500;
  border-radius: 8px 8px 0 0;
}

.custom-tabs :deep(.el-tabs__item.is-active) {
  background-color: #f9fafb;
}

.custom-tabs :deep(.el-tabs__content) {
  background-color: #f9fafb;
  border-radius: 0 0 8px 8px;
}

/* Form Labels */
:deep(.el-form-item__label) {
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
}

/* Table Styling */
:deep(.el-table) {
  border-radius: 8px;
}

:deep(.el-table th) {
  background-color: #f9fafb;
  color: #374151;
  font-weight: 600;
}

/* Smooth Transitions */
* {
  transition: all 0.2s ease;
}
</style>