// ============================================
// EQUIPMENT PARAMETERS MAPPING
// Input Fields Configuration by Equipment Type
// For equipment_status.parameters JSON field
// ============================================

export const equipmentParametersMapping = {

  // ============================================
  // 1. GAS TURBINE COMPRESSOR
  // ============================================
  'Compressor': {
    category: 'rotating_equipment',
    real_time_parameters: [
      {
        group: 'Process Parameters',
        fields: [
          { key: 'suction_pressure_psi', label: 'Suction Pressure', unit: 'psi', type: 'number', step: 0.1, required: true, min: 0, max: 2000 },
          { key: 'discharge_pressure_psi', label: 'Discharge Pressure', unit: 'psi', type: 'number', step: 0.1, required: true, min: 0, max: 3000 },
          { key: 'differential_pressure_psi', label: 'Differential Pressure', unit: 'psi', type: 'number', step: 0.1, calculated: true },
          { key: 'suction_temp_f', label: 'Suction Temperature', unit: '°F', type: 'number', step: 0.1, min: 50, max: 300 },
          { key: 'discharge_temp_f', label: 'Discharge Temperature', unit: '°F', type: 'number', step: 0.1, min: 50, max: 400 }
        ]
      },
      {
        group: 'Performance Parameters',
        fields: [
          { key: 'speed_rpm', label: 'Speed', unit: 'RPM', type: 'number', step: 1, required: true, min: 0, max: 15000 },
          { key: 'load_percent', label: 'Load', unit: '%', type: 'number', step: 0.1, required: true, min: 0, max: 100 },
          { key: 'flow_rate_mmscfd', label: 'Flow Rate', unit: 'MMSCFD', type: 'number', step: 0.001, min: 0 },
          { key: 'efficiency_percent', label: 'Efficiency', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'fuel_consumption_lph', label: 'Fuel Consumption', unit: 'LPH', type: 'number', step: 0.1, min: 0 }
        ]
      },
      {
        group: 'Condition Monitoring',
        fields: [
          { key: 'vibration_overall', label: 'Overall Vibration', unit: 'mm/s', type: 'number', step: 0.1, alarm_threshold: 4.5 },
          { key: 'vibration_axial', label: 'Axial Vibration', unit: 'mm/s', type: 'number', step: 0.1 },
          { key: 'bearing_temp_de', label: 'Bearing Temp (DE)', unit: '°C', type: 'number', step: 0.1, alarm_threshold: 85 },
          { key: 'bearing_temp_nde', label: 'Bearing Temp (NDE)', unit: '°C', type: 'number', step: 0.1, alarm_threshold: 85 },
          { key: 'seal_gas_flow', label: 'Seal Gas Flow', unit: 'MMSCFD', type: 'number', step: 0.001 },
          { key: 'lube_oil_pressure', label: 'Lube Oil Pressure', unit: 'psi', type: 'number', step: 0.1 },
          { key: 'lube_oil_temp', label: 'Lube Oil Temperature', unit: '°C', type: 'number', step: 0.1 }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Availability Assessment',
        fields: [
          { key: 'availability_status', label: 'Availability Status', type: 'select', options: ['Available', 'Not Available', 'Degraded'], required: true },
          { key: 'redundancy_available', label: 'Redundancy Available', type: 'boolean', required: true },
          { key: 'performance_pct', label: 'Performance', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'reliability_pct', label: 'Reliability', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 }
        ]
      },
      {
        group: 'Issues & Actions',
        fields: [
          { key: 'known_issues', label: 'Known Issues', type: 'textarea', rows: 3 },
          { key: 'mitigation_measures', label: 'Mitigation Measures', type: 'textarea', rows: 3 },
          { key: 'maintenance_due_days', label: 'Maintenance Due (Days)', type: 'number', min: 0 },
          { key: 'spare_parts_available', label: 'Spare Parts Available', type: 'boolean' }
        ]
      }
    ]
  },

  // ============================================
  // 2. GAS TURBINE GENERATOR
  // ============================================
  'Gas Turbine': {
    category: 'power_generation',
    real_time_parameters: [
      {
        group: 'Power Generation',
        fields: [
          { key: 'load_kw', label: 'Load', unit: 'kW', type: 'number', step: 1, required: true, min: 0 },
          { key: 'load_percent', label: 'Load', unit: '%', type: 'number', step: 0.1, required: true, min: 0, max: 100 },
          { key: 'frequency_hz', label: 'Frequency', unit: 'Hz', type: 'number', step: 0.01, required: true, min: 49, max: 51 },
          { key: 'voltage_kv', label: 'Voltage', unit: 'kV', type: 'number', step: 0.01, required: true },
          { key: 'current_amp', label: 'Current', unit: 'A', type: 'number', step: 0.1 },
          { key: 'power_factor', label: 'Power Factor', unit: '', type: 'number', step: 0.01, min: 0, max: 1 }
        ]
      },
      {
        group: 'Turbine Parameters',
        fields: [
          { key: 'turbine_speed_rpm', label: 'Turbine Speed', unit: 'RPM', type: 'number', step: 1, min: 0 },
          { key: 'exhaust_temp_c', label: 'Exhaust Temperature', unit: '°C', type: 'number', step: 0.1, alarm_threshold: 500 },
          { key: 'inlet_temp_c', label: 'Inlet Temperature', unit: '°C', type: 'number', step: 0.1 },
          { key: 'fuel_type', label: 'Fuel Type', type: 'select', options: ['Gas', 'Diesel', 'Condensate'] },
          { key: 'fuel_consumption_lph', label: 'Fuel Consumption', unit: 'LPH', type: 'number', step: 0.1, min: 0 },
          { key: 'fuel_pressure_psi', label: 'Fuel Pressure', unit: 'psi', type: 'number', step: 0.1 }
        ]
      },
      {
        group: 'Condition Monitoring',
        fields: [
          { key: 'vibration_overall', label: 'Overall Vibration', unit: 'mm/s', type: 'number', step: 0.1, alarm_threshold: 4.5 },
          { key: 'bearing_temp_de', label: 'Bearing Temp (DE)', unit: '°C', type: 'number', step: 0.1, alarm_threshold: 85 },
          { key: 'bearing_temp_nde', label: 'Bearing Temp (NDE)', unit: '°C', type: 'number', step: 0.1, alarm_threshold: 85 },
          { key: 'lube_oil_pressure', label: 'Lube Oil Pressure', unit: 'psi', type: 'number', step: 0.1 },
          { key: 'lube_oil_temp', label: 'Lube Oil Temperature', unit: '°C', type: 'number', step: 0.1 }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Availability Assessment',
        fields: [
          { key: 'availability_status', label: 'Availability Status', type: 'select', options: ['Available', 'Not Available', 'Degraded'], required: true },
          { key: 'redundancy_available', label: 'Redundancy Available', type: 'boolean', required: true },
          { key: 'performance_pct', label: 'Performance', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'reliability_pct', label: 'Reliability', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 }
        ]
      },
      {
        group: 'Issues & Actions',
        fields: [
          { key: 'known_issues', label: 'Known Issues', type: 'textarea', rows: 3 },
          { key: 'mitigation_measures', label: 'Mitigation Measures', type: 'textarea', rows: 3 },
          { key: 'maintenance_due_days', label: 'Maintenance Due (Days)', type: 'number', min: 0 },
          { key: 'start_attempts_today', label: 'Start Attempts Today', type: 'number', min: 0 }
        ]
      }
    ]
  },

  // ============================================
  // 3. GENERATOR (Diesel/Electric)
  // ============================================
  'Generator': {
    category: 'power_generation',
    real_time_parameters: [
      {
        group: 'Electrical Parameters',
        fields: [
          { key: 'load_kw', label: 'Load', unit: 'kW', type: 'number', step: 1, required: true, min: 0 },
          { key: 'load_percent', label: 'Load', unit: '%', type: 'number', step: 0.1, required: true, min: 0, max: 100 },
          { key: 'frequency_hz', label: 'Frequency', unit: 'Hz', type: 'number', step: 0.01, required: true, min: 49, max: 51 },
          { key: 'voltage_v', label: 'Voltage', unit: 'V', type: 'number', step: 1, required: true },
          { key: 'current_amp', label: 'Current', unit: 'A', type: 'number', step: 0.1 },
          { key: 'power_factor', label: 'Power Factor', unit: '', type: 'number', step: 0.01, min: 0, max: 1 }
        ]
      },
      {
        group: 'Engine Parameters',
        fields: [
          { key: 'engine_speed_rpm', label: 'Engine Speed', unit: 'RPM', type: 'number', step: 1, min: 0 },
          { key: 'coolant_temp_c', label: 'Coolant Temperature', unit: '°C', type: 'number', step: 0.1, alarm_threshold: 95 },
          { key: 'oil_pressure_psi', label: 'Oil Pressure', unit: 'psi', type: 'number', step: 0.1, min: 0 },
          { key: 'oil_temp_c', label: 'Oil Temperature', unit: '°C', type: 'number', step: 0.1 },
          { key: 'fuel_consumption_lph', label: 'Fuel Consumption', unit: 'LPH', type: 'number', step: 0.1, min: 0 },
          { key: 'running_hours', label: 'Running Hours', unit: 'hrs', type: 'number', step: 0.1, min: 0 }
        ]
      },
      {
        group: 'Condition Monitoring',
        fields: [
          { key: 'vibration_overall', label: 'Overall Vibration', unit: 'mm/s', type: 'number', step: 0.1, alarm_threshold: 4.5 },
          { key: 'exhaust_temp_c', label: 'Exhaust Temperature', unit: '°C', type: 'number', step: 0.1 },
          { key: 'battery_voltage_v', label: 'Battery Voltage', unit: 'V', type: 'number', step: 0.1 }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Availability Assessment',
        fields: [
          { key: 'availability_status', label: 'Availability Status', type: 'select', options: ['Available', 'Not Available', 'Degraded'], required: true },
          { key: 'redundancy_available', label: 'Redundancy Available', type: 'boolean', required: true },
          { key: 'performance_pct', label: 'Performance', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'auto_start_tested', label: 'Auto Start Tested', type: 'boolean' }
        ]
      }
    ]
  },

  // ============================================
  // 4. PUMP
  // ============================================
  'Pump': {
    category: 'rotating_equipment',
    real_time_parameters: [
      {
        group: 'Process Parameters',
        fields: [
          { key: 'suction_pressure_psi', label: 'Suction Pressure', unit: 'psi', type: 'number', step: 0.1, required: true },
          { key: 'discharge_pressure_psi', label: 'Discharge Pressure', unit: 'psi', type: 'number', step: 0.1, required: true },
          { key: 'differential_pressure_psi', label: 'Differential Pressure', unit: 'psi', type: 'number', step: 0.1, calculated: true },
          { key: 'flow_rate_gpm', label: 'Flow Rate', unit: 'GPM', type: 'number', step: 0.1, min: 0 },
          { key: 'fluid_temp_f', label: 'Fluid Temperature', unit: '°F', type: 'number', step: 0.1 }
        ]
      },
      {
        group: 'Performance Parameters',
        fields: [
          { key: 'speed_rpm', label: 'Speed', unit: 'RPM', type: 'number', step: 1, min: 0 },
          { key: 'motor_current_amp', label: 'Motor Current', unit: 'A', type: 'number', step: 0.1 },
          { key: 'efficiency_percent', label: 'Efficiency', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'npsh_available_ft', label: 'NPSH Available', unit: 'ft', type: 'number', step: 0.1 }
        ]
      },
      {
        group: 'Condition Monitoring',
        fields: [
          { key: 'vibration_overall', label: 'Overall Vibration', unit: 'mm/s', type: 'number', step: 0.1, alarm_threshold: 4.5 },
          { key: 'bearing_temp_de', label: 'Bearing Temp (DE)', unit: '°C', type: 'number', step: 0.1, alarm_threshold: 80 },
          { key: 'bearing_temp_nde', label: 'Bearing Temp (NDE)', unit: '°C', type: 'number', step: 0.1, alarm_threshold: 80 },
          { key: 'seal_leakage', label: 'Seal Leakage', type: 'select', options: ['None', 'Slight', 'Moderate', 'Severe'] }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Availability Assessment',
        fields: [
          { key: 'availability_status', label: 'Availability Status', type: 'select', options: ['Available', 'Not Available', 'Degraded'], required: true },
          { key: 'redundancy_available', label: 'Redundancy Available', type: 'boolean', required: true },
          { key: 'performance_pct', label: 'Performance', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 }
        ]
      }
    ]
  },

  // ============================================
  // 5. FIRE PUMP
  // ============================================
  'Fire Pump': {
    category: 'safety_equipment',
    real_time_parameters: [
      {
        group: 'Process Parameters',
        fields: [
          { key: 'suction_pressure_psi', label: 'Suction Pressure', unit: 'psi', type: 'number', step: 0.1 },
          { key: 'discharge_pressure_psi', label: 'Discharge Pressure', unit: 'psi', type: 'number', step: 0.1 },
          { key: 'flow_rate_gpm', label: 'Flow Rate', unit: 'GPM', type: 'number', step: 0.1, min: 0 },
          { key: 'test_duration_min', label: 'Test Duration', unit: 'min', type: 'number', step: 1, min: 0 }
        ]
      },
      {
        group: 'Engine Parameters',
        fields: [
          { key: 'engine_speed_rpm', label: 'Engine Speed', unit: 'RPM', type: 'number', step: 1, min: 0 },
          { key: 'oil_pressure_psi', label: 'Oil Pressure', unit: 'psi', type: 'number', step: 0.1 },
          { key: 'coolant_temp_c', label: 'Coolant Temperature', unit: '°C', type: 'number', step: 0.1 },
          { key: 'battery_voltage_v', label: 'Battery Voltage', unit: 'V', type: 'number', step: 0.1 }
        ]
      },
      {
        group: 'Test Results',
        fields: [
          { key: 'auto_start_ok', label: 'Auto Start OK', type: 'boolean' },
          { key: 'pressure_switch_ok', label: 'Pressure Switch OK', type: 'boolean' },
          { key: 'flow_test_ok', label: 'Flow Test OK', type: 'boolean' },
          { key: 'weekly_test_date', label: 'Last Weekly Test', type: 'date' }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Availability Assessment',
        fields: [
          { key: 'availability_status', label: 'Availability Status', type: 'select', options: ['Available', 'Not Available', 'Degraded'], required: true },
          { key: 'redundancy_available', label: 'Redundancy Available', type: 'boolean', required: true },
          { key: 'test_status', label: 'Test Status', type: 'select', options: ['Passed', 'Failed', 'Overdue'] }
        ]
      }
    ]
  },

  // ============================================
  // 6. SEPARATOR / VESSEL
  // ============================================
  'Separator': {
    category: 'process_equipment',
    real_time_parameters: [
      {
        group: 'Process Parameters',
        fields: [
          { key: 'operating_pressure_psi', label: 'Operating Pressure', unit: 'psi', type: 'number', step: 0.1, required: true },
          { key: 'operating_temp_f', label: 'Operating Temperature', unit: '°F', type: 'number', step: 0.1, required: true },
          { key: 'liquid_level_percent', label: 'Liquid Level', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'gas_outlet_flow_mmscfd', label: 'Gas Outlet Flow', unit: 'MMSCFD', type: 'number', step: 0.001 },
          { key: 'liquid_outlet_flow_bph', label: 'Liquid Outlet Flow', unit: 'BPH', type: 'number', step: 0.1 }
        ]
      },
      {
        group: 'Control Parameters',
        fields: [
          { key: 'level_control_output', label: 'Level Control Output', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'pressure_control_output', label: 'Pressure Control Output', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'safety_valve_set_psi', label: 'Safety Valve Setting', unit: 'psi', type: 'number', step: 0.1 }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Process Assessment',
        fields: [
          { key: 'separation_efficiency', label: 'Separation Efficiency', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'carry_over_status', label: 'Carry Over Status', type: 'select', options: ['None', 'Slight', 'Moderate', 'Severe'] },
          { key: 'fouling_status', label: 'Fouling Status', type: 'select', options: ['Clean', 'Slight', 'Moderate', 'Severe'] }
        ]
      }
    ]
  },

  // ============================================
  // 7. VESSEL (TEG, Storage Tank, etc)
  // ============================================
  'Vessel': {
    category: 'process_equipment',
    real_time_parameters: [
      {
        group: 'Process Parameters',
        fields: [
          { key: 'operating_pressure_psi', label: 'Operating Pressure', unit: 'psi', type: 'number', step: 0.1 },
          { key: 'operating_temp_f', label: 'Operating Temperature', unit: '°F', type: 'number', step: 0.1 },
          { key: 'liquid_level_percent', label: 'Liquid Level', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'inlet_flow_rate', label: 'Inlet Flow Rate', unit: 'GPM', type: 'number', step: 0.1 },
          { key: 'outlet_flow_rate', label: 'Outlet Flow Rate', unit: 'GPM', type: 'number', step: 0.1 }
        ]
      },
      {
        group: 'Quality Parameters',
        fields: [
          { key: 'purity_percent', label: 'Purity', unit: '%', type: 'number', step: 0.01, min: 0, max: 100 },
          { key: 'water_content_ppm', label: 'Water Content', unit: 'ppm', type: 'number', step: 1 },
          { key: 'ph_value', label: 'pH Value', unit: '', type: 'number', step: 0.01, min: 0, max: 14 }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Quality Assessment',
        fields: [
          { key: 'quality_within_spec', label: 'Quality Within Spec', type: 'boolean' },
          { key: 'regeneration_required', label: 'Regeneration Required', type: 'boolean' },
          { key: 'cleaning_required', label: 'Cleaning Required', type: 'boolean' }
        ]
      }
    ]
  },

  // ============================================
  // 8. HEAT EXCHANGER
  // ============================================
  'Heat Exchanger': {
    category: 'process_equipment',
    real_time_parameters: [
      {
        group: 'Process Side',
        fields: [
          { key: 'process_inlet_temp_f', label: 'Process Inlet Temp', unit: '°F', type: 'number', step: 0.1, required: true },
          { key: 'process_outlet_temp_f', label: 'Process Outlet Temp', unit: '°F', type: 'number', step: 0.1, required: true },
          { key: 'process_inlet_pressure_psi', label: 'Process Inlet Pressure', unit: 'psi', type: 'number', step: 0.1 },
          { key: 'process_flow_rate', label: 'Process Flow Rate', unit: 'GPM', type: 'number', step: 0.1 }
        ]
      },
      {
        group: 'Utility Side',
        fields: [
          { key: 'utility_inlet_temp_f', label: 'Utility Inlet Temp', unit: '°F', type: 'number', step: 0.1 },
          { key: 'utility_outlet_temp_f', label: 'Utility Outlet Temp', unit: '°F', type: 'number', step: 0.1 },
          { key: 'utility_flow_rate', label: 'Utility Flow Rate', unit: 'GPM', type: 'number', step: 0.1 }
        ]
      },
      {
        group: 'Performance',
        fields: [
          { key: 'heat_duty_mmbtu_hr', label: 'Heat Duty', unit: 'MMBTU/hr', type: 'number', step: 0.01 },
          { key: 'overall_heat_transfer_coeff', label: 'Overall U', unit: 'BTU/hr-ft²-°F', type: 'number', step: 0.1 },
          { key: 'fouling_factor', label: 'Fouling Factor', unit: 'hr-ft²-°F/BTU', type: 'number', step: 0.0001 }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Performance Assessment',
        fields: [
          { key: 'heat_transfer_efficiency', label: 'Heat Transfer Efficiency', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'fouling_status', label: 'Fouling Status', type: 'select', options: ['Clean', 'Slight', 'Moderate', 'Severe'] }
        ]
      }
    ]
  },

  // ============================================
  // 9. MOTOR
  // ============================================
  'Motor': {
    category: 'electrical_equipment',
    real_time_parameters: [
      {
        group: 'Electrical Parameters',
        fields: [
          { key: 'current_amp', label: 'Current', unit: 'A', type: 'number', step: 0.1, required: true },
          { key: 'voltage_v', label: 'Voltage', unit: 'V', type: 'number', step: 1, required: true },
          { key: 'power_kw', label: 'Power', unit: 'kW', type: 'number', step: 0.1 },
          { key: 'power_factor', label: 'Power Factor', unit: '', type: 'number', step: 0.01, min: 0, max: 1 },
          { key: 'frequency_hz', label: 'Frequency', unit: 'Hz', type: 'number', step: 0.01 }
        ]
      },
      {
        group: 'Mechanical Parameters',
        fields: [
          { key: 'speed_rpm', label: 'Speed', unit: 'RPM', type: 'number', step: 1, min: 0 },
          { key: 'load_percent', label: 'Load', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'torque_nm', label: 'Torque', unit: 'Nm', type: 'number', step: 0.1 }
        ]
      },
      {
        group: 'Condition Monitoring',
        fields: [
          { key: 'winding_temp_c', label: 'Winding Temperature', unit: '°C', type: 'number', step: 0.1, alarm_threshold: 120 },
          { key: 'bearing_temp_de', label: 'Bearing Temp (DE)', unit: '°C', type: 'number', step: 0.1, alarm_threshold: 80 },
          { key: 'bearing_temp_nde', label: 'Bearing Temp (NDE)', unit: '°C', type: 'number', step: 0.1, alarm_threshold: 80 },
          { key: 'vibration_overall', label: 'Overall Vibration', unit: 'mm/s', type: 'number', step: 0.1, alarm_threshold: 4.5 }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Performance Assessment',
        fields: [
          { key: 'efficiency_percent', label: 'Efficiency', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'insulation_resistance', label: 'Insulation Resistance', unit: 'MΩ', type: 'number', step: 0.1 }
        ]
      }
    ]
  },

  // ============================================
  // 10. HVAC
  // ============================================
  'HVAC': {
    category: 'utility_equipment',
    real_time_parameters: [
      {
        group: 'Air Conditioning',
        fields: [
          { key: 'supply_air_temp_f', label: 'Supply Air Temperature', unit: '°F', type: 'number', step: 0.1 },
          { key: 'return_air_temp_f', label: 'Return Air Temperature', unit: '°F', type: 'number', step: 0.1 },
          { key: 'humidity_percent', label: 'Humidity', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'air_flow_cfm', label: 'Air Flow', unit: 'CFM', type: 'number', step: 1 }
        ]
      },
      {
        group: 'System Performance',
        fields: [
          { key: 'compressor_status', label: 'Compressor Status', type: 'select', options: ['Running', 'Standby', 'Off'] },
          { key: 'filter_differential_pressure', label: 'Filter ΔP', unit: 'inWC', type: 'number', step: 0.01 },
          { key: 'power_consumption_kw', label: 'Power Consumption', unit: 'kW', type: 'number', step: 0.1 }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'System Assessment',
        fields: [
          { key: 'cooling_efficiency', label: 'Cooling Efficiency', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'filter_condition', label: 'Filter Condition', type: 'select', options: ['Good', 'Fair', 'Poor', 'Replace'] }
        ]
      }
    ]
  },

  // ============================================
  // 11. CRANE
  // ============================================
  'Crane': {
    category: 'marine_equipment',
    real_time_parameters: [
      {
        group: 'Operating Parameters',
        fields: [
          { key: 'load_capacity_tons', label: 'Load Capacity', unit: 'tons', type: 'number', step: 0.1, min: 0 },
          { key: 'boom_angle_deg', label: 'Boom Angle', unit: '°', type: 'number', step: 0.1, min: 0, max: 90 },
          { key: 'hook_height_m', label: 'Hook Height', unit: 'm', type: 'number', step: 0.1 },
          { key: 'slew_angle_deg', label: 'Slew Angle', unit: '°', type: 'number', step: 0.1, min: 0, max: 360 }
        ]
      },
      {
        group: 'Hydraulic System',
        fields: [
          { key: 'hydraulic_pressure_psi', label: 'Hydraulic Pressure', unit: 'psi', type: 'number', step: 0.1 },
          { key: 'hydraulic_oil_temp_c', label: 'Hydraulic Oil Temp', unit: '°C', type: 'number', step: 0.1 },
          { key: 'hydraulic_oil_level', label: 'Hydraulic Oil Level', type: 'select', options: ['Low', 'Normal', 'High'] }
        ]
      },
      {
        group: 'Safety Systems',
        fields: [
          { key: 'load_moment_indicator', label: 'Load Moment Indicator', type: 'select', options: ['Normal', 'Warning', 'Alarm'] },
          { key: 'anti_two_block_ok', label: 'Anti Two Block OK', type: 'boolean' },
          { key: 'emergency_stop_ok', label: 'Emergency Stop OK', type: 'boolean' }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Inspection Results',
        fields: [
          { key: 'daily_inspection_ok', label: 'Daily Inspection OK', type: 'boolean' },
          { key: 'wire_rope_condition', label: 'Wire Rope Condition', type: 'select', options: ['Good', 'Fair', 'Poor', 'Replace'] },
          { key: 'hook_block_condition', label: 'Hook Block Condition', type: 'select', options: ['Good', 'Fair', 'Poor'] }
        ]
      }
    ]
  },

  // ============================================
  // 12. VALVE
  // ============================================
  'Valve': {
    category: 'process_equipment',
    real_time_parameters: [
      {
        group: 'Process Parameters',
        fields: [
          { key: 'inlet_pressure_psi', label: 'Inlet Pressure', unit: 'psi', type: 'number', step: 0.1 },
          { key: 'outlet_pressure_psi', label: 'Outlet Pressure', unit: 'psi', type: 'number', step: 0.1 },
          { key: 'differential_pressure_psi', label: 'Differential Pressure', unit: 'psi', type: 'number', step: 0.1, calculated: true },
          { key: 'flow_rate', label: 'Flow Rate', unit: 'GPM', type: 'number', step: 0.1 }
        ]
      },
      {
        group: 'Control Parameters',
        fields: [
          { key: 'valve_position_percent', label: 'Valve Position', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'control_signal_percent', label: 'Control Signal', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'actuator_pressure_psi', label: 'Actuator Pressure', unit: 'psi', type: 'number', step: 0.1 }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Performance Assessment',
        fields: [
          { key: 'response_time_sec', label: 'Response Time', unit: 'sec', type: 'number', step: 0.1 },
          { key: 'seat_leakage', label: 'Seat Leakage', type: 'select', options: ['None', 'Slight', 'Moderate', 'Severe'] },
          { key: 'packing_leakage', label: 'Packing Leakage', type: 'select', options: ['None', 'Slight', 'Moderate', 'Severe'] }
        ]
      }
    ]
  },

  // ============================================
  // 13. TANK
  // ============================================
  'Tank': {
    category: 'storage_equipment',
    real_time_parameters: [
      {
        group: 'Tank Parameters',
        fields: [
          { key: 'liquid_level_percent', label: 'Liquid Level', unit: '%', type: 'number', step: 0.1, min: 0, max: 100, required: true },
          { key: 'liquid_level_m', label: 'Liquid Level', unit: 'm', type: 'number', step: 0.01 },
          { key: 'volume_m3', label: 'Volume', unit: 'm³', type: 'number', step: 0.1 },
          { key: 'tank_pressure_psi', label: 'Tank Pressure', unit: 'psi', type: 'number', step: 0.1 },
          { key: 'liquid_temp_f', label: 'Liquid Temperature', unit: '°F', type: 'number', step: 0.1 }
        ]
      },
      {
        group: 'Safety Parameters',
        fields: [
          { key: 'high_level_alarm', label: 'High Level Alarm', type: 'boolean' },
          { key: 'low_level_alarm', label: 'Low Level Alarm', type: 'boolean' },
          { key: 'pressure_relief_valve_ok', label: 'PRV OK', type: 'boolean' }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Inspection Results',
        fields: [
          { key: 'external_condition', label: 'External Condition', type: 'select', options: ['Good', 'Fair', 'Poor'] },
          { key: 'foundation_condition', label: 'Foundation Condition', type: 'select', options: ['Good', 'Fair', 'Poor'] },
          { key: 'corrosion_present', label: 'Corrosion Present', type: 'boolean' }
        ]
      }
    ]
  },

  // ============================================
  // 14. INSTRUMENT
  // ============================================
  'Instrument': {
    category: 'control_equipment',
    real_time_parameters: [
      {
        group: 'Measurement',
        fields: [
          { key: 'measured_value', label: 'Measured Value', unit: 'varies', type: 'number', step: 0.01, required: true },
          { key: 'setpoint_value', label: 'Setpoint Value', unit: 'varies', type: 'number', step: 0.01 },
          { key: 'output_signal_percent', label: 'Output Signal', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'deviation_percent', label: 'Deviation', unit: '%', type: 'number', step: 0.1 }
        ]
      },
      {
        group: 'Calibration',
        fields: [
          { key: 'zero_error_percent', label: 'Zero Error', unit: '%', type: 'number', step: 0.01 },
          { key: 'span_error_percent', label: 'Span Error', unit: '%', type: 'number', step: 0.01 },
          { key: 'linearity_error_percent', label: 'Linearity Error', unit: '%', type: 'number', step: 0.01 },
          { key: 'last_calibration_date', label: 'Last Calibration', type: 'date' }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Performance Assessment',
        fields: [
          { key: 'accuracy_within_spec', label: 'Accuracy Within Spec', type: 'boolean' },
          { key: 'calibration_due_days', label: 'Calibration Due (Days)', type: 'number', min: 0 }
        ]
      }
    ]
  },

  // ============================================
  // 15. COOLER / FAN
  // ============================================
  'Cooler': {
    category: 'heat_transfer_equipment',
    real_time_parameters: [
      {
        group: 'Process Parameters',
        fields: [
          { key: 'inlet_temp_f', label: 'Inlet Temperature', unit: '°F', type: 'number', step: 0.1, required: true },
          { key: 'outlet_temp_f', label: 'Outlet Temperature', unit: '°F', type: 'number', step: 0.1, required: true },
          { key: 'cooling_water_flow_gpm', label: 'Cooling Water Flow', unit: 'GPM', type: 'number', step: 0.1 },
          { key: 'air_flow_cfm', label: 'Air Flow', unit: 'CFM', type: 'number', step: 1 }
        ]
      },
      {
        group: 'Performance Parameters',
        fields: [
          { key: 'fan_speed_rpm', label: 'Fan Speed', unit: 'RPM', type: 'number', step: 1 },
          { key: 'motor_current_amp', label: 'Motor Current', unit: 'A', type: 'number', step: 0.1 },
          { key: 'cooling_efficiency_percent', label: 'Cooling Efficiency', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Performance Assessment',
        fields: [
          { key: 'heat_removal_rate', label: 'Heat Removal Rate', unit: 'BTU/hr', type: 'number', step: 1 },
          { key: 'fouling_status', label: 'Fouling Status', type: 'select', options: ['Clean', 'Slight', 'Moderate', 'Severe'] }
        ]
      }
    ]
  },

  'Fan': {
    category: 'ventilation_equipment',
    real_time_parameters: [
      {
        group: 'Performance Parameters',
        fields: [
          { key: 'speed_rpm', label: 'Speed', unit: 'RPM', type: 'number', step: 1, min: 0 },
          { key: 'air_flow_cfm', label: 'Air Flow', unit: 'CFM', type: 'number', step: 1 },
          { key: 'static_pressure_inwc', label: 'Static Pressure', unit: 'inWC', type: 'number', step: 0.01 },
          { key: 'motor_current_amp', label: 'Motor Current', unit: 'A', type: 'number', step: 0.1 }
        ]
      },
      {
        group: 'Condition Monitoring',
        fields: [
          { key: 'vibration_overall', label: 'Overall Vibration', unit: 'mm/s', type: 'number', step: 0.1, alarm_threshold: 4.5 },
          { key: 'bearing_temp_c', label: 'Bearing Temperature', unit: '°C', type: 'number', step: 0.1, alarm_threshold: 80 }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Performance Assessment',
        fields: [
          { key: 'efficiency_percent', label: 'Efficiency', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 }
        ]
      }
    ]
  },

  // ============================================
  // 16. LIFEBOAT
  // ============================================
  'Lifeboat': {
    category: 'safety_equipment',
    real_time_parameters: [
      {
        group: 'Engine Parameters',
        fields: [
          { key: 'engine_running', label: 'Engine Running', type: 'boolean' },
          { key: 'engine_oil_pressure_psi', label: 'Engine Oil Pressure', unit: 'psi', type: 'number', step: 0.1 },
          { key: 'coolant_temp_c', label: 'Coolant Temperature', unit: '°C', type: 'number', step: 0.1 },
          { key: 'fuel_level_percent', label: 'Fuel Level', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 }
        ]
      },
      {
        group: 'Safety Equipment',
        fields: [
          { key: 'davit_winch_ok', label: 'Davit Winch OK', type: 'boolean' },
          { key: 'release_hook_ok', label: 'Release Hook OK', type: 'boolean' },
          { key: 'communication_ok', label: 'Communication OK', type: 'boolean' }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Inspection Results',
        fields: [
          { key: 'weekly_test_ok', label: 'Weekly Test OK', type: 'boolean' },
          { key: 'equipment_complete', label: 'Equipment Complete', type: 'boolean' },
          { key: 'hull_condition', label: 'Hull Condition', type: 'select', options: ['Good', 'Fair', 'Poor'] }
        ]
      }
    ]
  },

  // ============================================
  // 17. OTHER EQUIPMENT
  // ============================================
  'Other': {
    category: 'general_equipment',
    real_time_parameters: [
      {
        group: 'General Parameters',
        fields: [
          { key: 'parameter_1', label: 'Parameter 1', unit: '', type: 'number', step: 0.1 },
          { key: 'parameter_2', label: 'Parameter 2', unit: '', type: 'number', step: 0.1 },
          { key: 'parameter_3', label: 'Parameter 3', unit: '', type: 'text' },
          { key: 'operating_status', label: 'Operating Status', type: 'select', options: ['Normal', 'Warning', 'Alarm'] }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'General Assessment',
        fields: [
          { key: 'overall_condition', label: 'Overall Condition', type: 'select', options: ['Good', 'Fair', 'Poor'] },
          { key: 'maintenance_required', label: 'Maintenance Required', type: 'boolean' }
        ]
      }
    ]
  }
};

// ============================================
// COMMON PARAMETERS FOR ALL EQUIPMENT
// ============================================
export const commonParameters = {
  basic_info: [
    { key: 'operational_status', label: 'Operational Status', type: 'select', options: ['Running', 'Standby', 'Shutdown', 'Maintenance'], required: true },
    { key: 'running_hours', label: 'Running Hours', unit: 'hrs', type: 'number', step: 0.1, min: 0 },
    { key: 'shift_operator', label: 'Shift Operator', type: 'text', maxlength: 100 }
  ],
  reading_info: [
    { key: 'reading_type', label: 'Reading Type', type: 'select', options: ['real_time', 'daily_assessment'], required: true },
    { key: 'reading_datetime', label: 'Reading Date/Time', type: 'datetime-local', required: true },
    { key: 'recorded_by', label: 'Recorded By', type: 'select', source: 'users', required: true }
  ]
};

// ============================================
// FORM GENERATION HELPER FUNCTIONS
// ============================================

/**
 * Get parameters for specific equipment type and reading type
 * @param {string} equipmentType - Equipment type from database
 * @param {string} readingType - 'real_time' or 'daily_assessment'
 * @returns {Array} Array of parameter groups and fields
 */
export function getEquipmentParameters(equipmentType, readingType = 'real_time') {
  const mapping = equipmentParametersMapping[equipmentType];
  if (!mapping) {
    return equipmentParametersMapping['Other'][`${readingType}_parameters`] || [];
  }
  
  return mapping[`${readingType}_parameters`] || [];
}

/**
 * Get all field keys for specific equipment type
 * @param {string} equipmentType - Equipment type from database
 * @returns {Object} Object with real_time and daily_assessment field keys
 */
export function getEquipmentFieldKeys(equipmentType) {
  const realTimeParams = getEquipmentParameters(equipmentType, 'real_time');
  const dailyAssessmentParams = getEquipmentParameters(equipmentType, 'daily_assessment');
  
  const extractKeys = (params) => {
    return params.flatMap(group => group.fields.map(field => field.key));
  };
  
  return {
    real_time: extractKeys(realTimeParams),
    daily_assessment: extractKeys(dailyAssessmentParams)
  };
}

/**
 * Validate parameter values against field definitions
 * @param {string} equipmentType - Equipment type from database
 * @param {string} readingType - 'real_time' or 'daily_assessment'
 * @param {Object} values - Parameter values to validate
 * @returns {Object} Validation result with errors array
 */
export function validateEquipmentParameters(equipmentType, readingType, values) {
  const parameters = getEquipmentParameters(equipmentType, readingType);
  const errors = [];
  
  parameters.forEach(group => {
    group.fields.forEach(field => {
      const value = values[field.key];
      
      // Required field validation
      if (field.required && (value === undefined || value === null || value === '')) {
        errors.push(`${field.label} is required`);
      }
      
      // Type validation
      if (value !== undefined && value !== null && value !== '') {
        if (field.type === 'number') {
          const numValue = parseFloat(value);
          if (isNaN(numValue)) {
            errors.push(`${field.label} must be a valid number`);
          } else {
            // Min/max validation
            if (field.min !== undefined && numValue < field.min) {
              errors.push(`${field.label} must be at least ${field.min}`);
            }
            if (field.max !== undefined && numValue > field.max) {
              errors.push(`${field.label} must be at most ${field.max}`);
            }
            
            // Alarm threshold warning
            if (field.alarm_threshold !== undefined && numValue > field.alarm_threshold) {
              errors.push(`Warning: ${field.label} exceeds alarm threshold (${field.alarm_threshold})`);
            }
          }
        }
      }
    });
  });
  
  return {
    valid: errors.length === 0,
    errors: errors
  };
}

// ============================================
// EXAMPLE USAGE IN VUE COMPONENT
// ============================================

/*
// In Vue component
import { getEquipmentParameters, validateEquipmentParameters, commonParameters } from './equipmentParametersMapping';

export default {
  data() {
    return {
      equipment: {
        equipment_type: 'Compressor',
        // ... other equipment data
      },
      reading_type: 'real_time',
      formData: {
        operational_status: 'Running',
        // ... parameter values
      }
    }
  },
  computed: {
    parameterGroups() {
      return getEquipmentParameters(this.equipment.equipment_type, this.reading_type);
    },
    commonFields() {
      return commonParameters;
    }
  },
  methods: {
    validateForm() {
      return validateEquipmentParameters(
        this.equipment.equipment_type, 
        this.reading_type, 
        this.formData
      );
    },
    submitForm() {
      const validation = this.validateForm();
      if (validation.valid) {
        // Submit to API
        const payload = {
          vessel_id: this.vessel_id,
          equipment_id: this.equipment.id,
          reading_datetime: this.formData.reading_datetime,
          operational_status: this.formData.operational_status,
          running_hours: this.formData.running_hours,
          shift_operator: this.formData.shift_operator,
          parameters: this.formData, // JSON parameters
          reading_type: this.reading_type,
          recorded_by: this.user_id
        };
        
        this.$api.post('/equipment-status', payload);
      } else {
        this.showErrors(validation.errors);
      }
    }
  }
}
*/