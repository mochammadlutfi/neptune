// @/config/equipmentParametersMapping.js
// Equipment Parameters Mapping Configuration

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
          { key: 'start_attempts_today', label: 'Start Attempts Today', type: 'number', min: 0 }
        ]
      }
    ]
  },

  // ============================================
  // 3. PUMP
  // ============================================
  'Pump': {
    category: 'rotating_equipment',
    real_time_parameters: [
      {
        group: 'Process Parameters',
        fields: [
          { key: 'suction_pressure_psi', label: 'Suction Pressure', unit: 'psi', type: 'number', step: 0.1, required: true },
          { key: 'discharge_pressure_psi', label: 'Discharge Pressure', unit: 'psi', type: 'number', step: 0.1, required: true },
          { key: 'flow_rate_gpm', label: 'Flow Rate', unit: 'GPM', type: 'number', step: 0.1, min: 0 },
          { key: 'pump_head_ft', label: 'Pump Head', unit: 'ft', type: 'number', step: 0.1 }
        ]
      },
      {
        group: 'Performance Parameters',
        fields: [
          { key: 'speed_rpm', label: 'Speed', unit: 'RPM', type: 'number', step: 1, required: true },
          { key: 'efficiency_percent', label: 'Efficiency', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 },
          { key: 'power_consumption_kw', label: 'Power Consumption', unit: 'kW', type: 'number', step: 0.1 }
        ]
      },
      {
        group: 'Condition Monitoring',
        fields: [
          { key: 'vibration_overall', label: 'Overall Vibration', unit: 'mm/s', type: 'number', step: 0.1 },
          { key: 'bearing_temp', label: 'Bearing Temperature', unit: '°C', type: 'number', step: 0.1 },
          { key: 'seal_condition', label: 'Seal Condition', type: 'select', options: ['Good', 'Fair', 'Poor'] }
        ]
      }
    ],
    daily_assessment_parameters: [
      {
        group: 'Availability Assessment',
        fields: [
          { key: 'availability_status', label: 'Availability Status', type: 'select', options: ['Available', 'Not Available', 'Degraded'], required: true },
          { key: 'performance_pct', label: 'Performance', unit: '%', type: 'number', step: 0.1, min: 0, max: 100 }
        ]
      }
    ]
  },

  // ============================================
  // 4. CRANE
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
  // 5. OTHER EQUIPMENT
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