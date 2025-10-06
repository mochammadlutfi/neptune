# MASTER DATA MODULE - User Flow & SOP
**DVR-DCR Management System - Pakarti Tirtoagung**

---

## 🗃️ **MODULE OVERVIEW**

**Objective**: Mengelola semua master data dan reference data yang menjadi foundation untuk seluruh system operations, termasuk contracts, vessels, wells, equipment registry, dan gas buyers untuk memastikan data integrity dan operational consistency.

**Key Tables**: 
- `contracts` - Contract management dan revenue sharing
- `vessels` - Vessel registry dan specifications
- `wells` - Well registry dan design parameters
- `equipment` - Equipment registry dan specifications
- `gas_buyers` - Gas buyer information dan contract terms

**User Roles**: System Administrator, Data Manager, Operations Manager, Contract Administrator

---

## 🗂️ **MENU STRUCTURE**

```
🗃️ MASTER DATA
├── 2.1 Contracts                  → contracts
├── 2.2 Vessels                    → vessels  
├── 2.3 Wells                      → wells
├── 2.4 Equipment Registry         → equipment
├── 2.5 Gas Buyers                 → gas_buyers
└── 2.6 Chemical Registry          → chemicals
```

---

## 🔄 **USER FLOW OVERVIEW**

### **Master Data Management Hierarchy**
1. **Contracts Setup** (Foundation) → Define PSC terms dan revenue sharing
2. **Vessels Registration** (Assets) → Register MOPU/FPU specifications  
3. **Wells Creation** (Production) → Configure well design parameters
4. **Equipment Registry** (Operations) → Register all equipment dan specifications
5. **Supporting Data** (Operations) → Gas buyers, chemicals, other references

---

## 📋 **2.1 CONTRACTS - User Flow**

### **Access Control**
- **Primary Users**: Contract Administrator, Data Manager
- **Approval Required**: Operations Manager approval for any contract modifications
- **Security Level**: High - impacts revenue calculations

### **SOP Step-by-Step**

#### **Step 1: Contract Registration**
**Data Fields (Table: contracts)**:
```sql
contract_id                -- Unique contract identifier
contract_number            -- Official contract number
contract_name              -- Contract descriptive name
contract_type              -- PSC/JOA/Service_Contract/Transportation
operator_name              -- Operating company name
contractor_name            -- Contractor company name (Pakarti Tirtoagung)
effective_date             -- Contract effective date
expiry_date                -- Contract expiration date
contract_status            -- Active/Suspended/Expired/Terminated
field_location             -- Geographical location
area_size_km2              -- Contract area size in square kilometers
water_depth_m              -- Water depth in meters
```

#### **Step 2: Revenue Sharing Configuration**
**Data Fields (Continued: contracts)**:
```sql
-- PSC Financial Terms
contractor_share_pct       -- Contractor share percentage
government_share_pct       -- Government share percentage
cost_recovery_limit_pct    -- Cost recovery ceiling percentage
profit_oil_split          -- JSON: Profit oil sharing tiers
gas_sales_terms            -- JSON: Gas sales agreement terms
taxation_terms             -- JSON: Taxation and fiscal terms
currency                   -- Contract currency (USD/IDR)
price_escalation          -- JSON: Price escalation mechanism
bonus_payments            -- JSON: Bonus payment structure
work_commitments          -- JSON: Work commitment obligations
```

#### **Step 3: PSC Financial Structure**
```json
{
    "psc_financial_terms": {
        "cost_recovery": {
            "ceiling_percentage": 75.0,
            "operating_cost_deductible": true,
            "capital_cost_depreciation": "declining_balance",
            "depreciation_rate_percent": 25.0,
            "cost_recovery_mechanism": "monthly_lifting"
        },
        "profit_oil_sharing": {
            "tier_structure": [
                {
                    "production_tier": "0-25000_bopd",
                    "contractor_share_percent": 55.0,
                    "government_share_percent": 45.0
                },
                {
                    "production_tier": "25001-50000_bopd",
                    "contractor_share_percent": 50.0,
                    "government_share_percent": 50.0
                },
                {
                    "production_tier": "50001+_bopd",
                    "contractor_share_percent": 45.0,
                    "government_share_percent": 55.0
                }
            ]
        },
        "gas_sales_terms": {
            "base_price_mscf": 6.50,
            "price_adjustment_mechanism": "quarterly_review",
            "heating_value_adjustment": true,
            "minimum_take_mmscfd": 8.0,
            "contract_quantity_mmscfd": 15.0,
            "delivery_point": "Platform outlet"
        },
        "taxation": {
            "income_tax_rate_percent": 25.0,
            "withholding_tax_percent": 2.0,
            "vat_rate_percent": 11.0,
            "regional_tax_applicable": true
        }
    }
}
```

#### **Step 4: Work Commitments & Obligations**
```json
{
    "work_commitments": {
        "exploration_phase": {
            "duration_years": 3,
            "minimum_wells": 2,
            "seismic_survey_km2": 500,
            "minimum_expenditure_usd": 25000000,
            "completion_deadline": "2026-12-31"
        },
        "development_phase": {
            "duration_years": 20,
            "development_wells": 12,
            "facility_construction": "MOPU_installation",
            "minimum_expenditure_usd": 150000000,
            "first_production_deadline": "2027-06-30"
        },
        "operating_obligations": {
            "local_content_percent": 35.0,
            "training_programs": "annual_minimum_20_personnel",
            "environmental_monitoring": "continuous",
            "community_development_fund_percent": 1.0
        }
    }
}
```

#### **Step 5: Contract Performance Monitoring**
```sql
-- Track contract performance metrics
CREATE VIEW v_contract_performance AS
SELECT 
    c.contract_id,
    c.contract_name,
    c.operator_name,
    
    -- Production performance
    COALESCE(SUM(ds.total_oil_bbl), 0) as cumulative_oil_bbl,
    COALESCE(SUM(ds.total_gas_mmscf), 0) as cumulative_gas_mmscf,
    
    -- Revenue calculations
    ROUND(
        (COALESCE(SUM(ds.total_oil_bbl), 0) * c.contractor_share_pct / 100) * 
        (SELECT AVG(oil_price_usd) FROM market_prices WHERE price_date >= CURDATE() - INTERVAL 30 DAY),
        2
    ) as estimated_contractor_revenue_usd,
    
    -- Contract compliance
    DATEDIFF(c.expiry_date, CURDATE()) as days_to_expiry,
    CASE 
        WHEN c.expiry_date < CURDATE() THEN 'EXPIRED'
        WHEN c.expiry_date < CURDATE() + INTERVAL 180 DAY THEN 'EXPIRING_SOON'
        ELSE 'ACTIVE'
    END as contract_status_alert,
    
    -- Work commitment status
    JSON_OBJECT(
        'wells_drilled', (SELECT COUNT(*) FROM wells w WHERE w.contract_id = c.contract_id),
        'production_achieved', CASE 
            WHEN COALESCE(SUM(ds.total_oil_bbl), 0) > 0 THEN 'YES' 
            ELSE 'NO' 
        END,
        'commitment_compliance', 'UNDER_REVIEW'
    ) as work_commitment_status

FROM contracts c
LEFT JOIN vessels v ON c.contract_id = v.contract_id
LEFT JOIN daily_summary ds ON v.id = ds.vessel_id
WHERE c.contract_status = 'Active'
GROUP BY c.contract_id, c.contract_name, c.operator_name, c.contractor_share_pct, c.expiry_date;
```

---

## 🚢 **2.2 VESSELS - User Flow**

### **SOP Step-by-Step**

#### **Step 1: Vessel Registration**
**Data Fields (Table: vessels)**:
```sql
vessel_id                  -- Unique vessel identifier
code                       -- Vessel code (MOPU-01, FPU-02)
name                       -- Vessel name (MOPU Prameswari 08)
vessel_type                -- MOPU/FPU/FSO/FPSO
field_name                 -- Field where vessel operates
operator                   -- Operating company
contractor                 -- Contractor company
contract_id                -- FK to contracts table
commissioning_date         -- Vessel commissioning date
location_coordinates       -- JSON: vessel coordinates
water_depth_m              -- Water depth at location
design_life_years          -- Design operational life
status                     -- Active/Standby/Maintenance/Decommissioned
```

#### **Step 2: Vessel Technical Specifications**
**Data Fields (Continued: vessels)**:
```sql
technical_specifications   -- JSON: detailed vessel specs
processing_capacity        -- JSON: processing design capacity
accommodation_capacity     -- Maximum personnel capacity
safety_systems            -- JSON: safety systems configuration
environmental_systems     -- JSON: environmental protection systems
certification_details     -- JSON: vessel certifications
maintenance_schedule      -- JSON: major maintenance schedule
operational_parameters    -- JSON: operational design parameters
```

#### **Step 3: MOPU Technical Specifications**
```json
{
    "mopu_technical_specifications": {
        "general_particulars": {
            "length_overall_m": 85.0,
            "beam_m": 45.0,
            "depth_m": 12.0,
            "displacement_tonnes": 8500,
            "deck_area_m2": 2500,
            "crane_capacity_tonnes": 50,
            "accommodation_pax": 120,
            "helicopter_deck": true
        },
        "processing_facilities": {
            "oil_processing_capacity_bpd": 15000,
            "gas_processing_capacity_mmscfd": 25.0,
            "water_processing_capacity_bpd": 25000,
            "separation_stages": 3,
            "test_separator_capacity_bpd": 5000,
            "export_pumping_capacity_bpd": 18000
        },
        "storage_systems": {
            "oil_storage_bbls": 5000,
            "fuel_storage_m3": 350,
            "fresh_water_storage_m3": 200,
            "chemical_storage_m3": 50,
            "mud_storage_m3": 100
        },
        "utilities": {
            "power_generation_kw": 2500,
            "backup_power_kw": 1500,
            "fresh_water_production_lpd": 50000,
            "compressed_air_pressure_bar": 7.0,
            "instrument_air_pressure_bar": 7.0
        },
        "safety_systems": {
            "fire_fighting_system": "deluge_foam_co2",
            "gas_detection_system": "fixed_portable",
            "emergency_shutdown_levels": 3,
            "life_boats_capacity_pax": 150,
            "life_rafts_capacity_pax": 200
        }
    }
}
```

#### **Step 4: FPU Technical Specifications**
```json
{
    "fpu_technical_specifications": {
        "general_particulars": {
            "length_overall_m": 120.0,
            "beam_m": 55.0,
            "depth_m": 15.0,
            "displacement_tonnes": 15000,
            "deck_area_m2": 4500,
            "crane_capacity_tonnes": 100,
            "accommodation_pax": 80,
            "dp_system": "DP2_class"
        },
        "processing_facilities": {
            "gas_processing_capacity_mmscfd": 150.0,
            "gas_compression_stages": 2,
            "export_pressure_psi": 1000,
            "fuel_gas_supply_pressure_psi": 150,
            "condensate_processing_bpd": 2000,
            "water_processing_bpd": 10000
        },
        "compression_systems": {
            "low_pressure_compressor": {
                "capacity_mmscfd": 75.0,
                "suction_pressure_psi": 125,
                "discharge_pressure_psi": 350
            },
            "high_pressure_compressor": {
                "capacity_mmscfd": 75.0,
                "suction_pressure_psi": 350,
                "discharge_pressure_psi": 1000
            },
            "fuel_gas_compressor": {
                "capacity_mmscfd": 8.0,
                "discharge_pressure_psi": 150
            }
        },
        "utilities": {
            "power_generation_kw": 5000,
            "backup_power_kw": 2500,
            "emergency_power_kw": 500,
            "cooling_water_capacity_m3h": 500,
            "instrument_air_capacity_nm3h": 1000
        }
    }
}
```

#### **Step 5: Vessel Performance Monitoring**
```sql
-- Create vessel performance dashboard view
CREATE VIEW v_vessel_performance AS
SELECT 
    v.vessel_id,
    v.code,
    v.name,
    v.vessel_type,
    v.status,
    
    -- Current operational status
    (SELECT operational_status FROM equipment_status es 
     INNER JOIN equipment e ON es.equipment_id = e.equipment_tag
     WHERE e.vessel_id = v.id AND e.is_critical = true
     AND DATE(es.status_datetime) = CURDATE()
     ORDER BY es.status_datetime DESC LIMIT 1) as critical_equipment_status,
    
    -- Production performance (last 30 days)
    COALESCE(AVG(ds.total_oil_bbl), 0) as avg_oil_production_bpd,
    COALESCE(AVG(ds.total_gas_mmscf), 0) as avg_gas_production_mmscfd,
    COALESCE(AVG(ds.equipment_availability_pct), 0) as avg_equipment_availability,
    
    -- Utilization vs design capacity
    ROUND(
        COALESCE(AVG(ds.total_oil_bbl), 0) / 
        JSON_EXTRACT(v.processing_capacity, '$.oil_processing_capacity_bpd') * 100, 
        1
    ) as oil_capacity_utilization_pct,
    
    ROUND(
        COALESCE(AVG(ds.total_gas_mmscf), 0) / 
        JSON_EXTRACT(v.processing_capacity, '$.gas_processing_capacity_mmscfd') * 100, 
        1
    ) as gas_capacity_utilization_pct,
    
    -- Safety and environmental performance
    (SELECT COUNT(*) FROM hse_operations ho 
     WHERE ho.vessel_id = v.id 
     AND ho.operation_date >= CURDATE() - INTERVAL 30 DAY
     AND ho.incident_count > 0) as incidents_last_30_days,
     
    (SELECT AVG(ho.ltif_rate) FROM hse_operations ho 
     WHERE ho.vessel_id = v.id 
     AND ho.operation_date >= CURDATE() - INTERVAL 30 DAY) as avg_ltif_rate,
    
    -- Maintenance status
    JSON_OBJECT(
        'overdue_maintenance', (
            SELECT COUNT(*) FROM maintenance_activities ma
            INNER JOIN equipment e ON ma.equipment_id = e.equipment_tag
            WHERE e.vessel_id = v.id
            AND ma.activity_date < CURDATE()
            AND ma.completion_status = 'Scheduled'
        ),
        'next_major_maintenance', (
            SELECT MIN(ma.activity_date) FROM maintenance_activities ma
            INNER JOIN equipment e ON ma.equipment_id = e.equipment_tag
            WHERE e.vessel_id = v.id
            AND ma.activity_type = 'Major_Overhaul'
            AND ma.activity_date > CURDATE()
        )
    ) as maintenance_status

FROM vessels v
LEFT JOIN daily_summary ds ON v.id = ds.vessel_id 
    AND ds.summary_date >= CURDATE() - INTERVAL 30 DAY
WHERE v.status = 'Active'
GROUP BY v.vessel_id, v.code, v.name, v.vessel_type, v.status, v.processing_capacity;
```

---

## 🛢️ **2.3 WELLS - User Flow**

### **SOP Step-by-Step**

#### **Step 1: Well Registration**
**Data Fields (Table: wells)**:
```sql
well_id                    -- Unique well identifier
well_code                  -- Well code (MAC-01, MAC-02)
well_name                  -- Well descriptive name
well_type                  -- Producer/Injector/Observation
field_name                 -- Field name
block_name                 -- Block designation
vessel_id                  -- FK to vessels table (connected platform)
contract_id                -- FK to contracts table
spud_date                  -- Well spud date
completion_date            -- Well completion date
well_status                -- Producing/Shut_in/Suspended/Abandoned
```

#### **Step 2: Well Design Parameters**
**Data Fields (Continued: wells)**:
```sql
design_oil_rate_bpd        -- Design oil production rate
design_gas_rate_mscfd      -- Design gas production rate  
design_water_rate_bpd      -- Design water production rate
design_pressure_psi        -- Design wellhead pressure
design_temperature_f       -- Design wellhead temperature
maximum_drawdown_psi       -- Maximum allowable drawdown
reservoir_pressure_psi     -- Initial reservoir pressure
bubble_point_pressure_psi  -- Oil bubble point pressure
gas_oil_ratio_scf_bbl     -- Solution gas-oil ratio
formation_volume_factor    -- Oil formation volume factor
water_cut_design_pct      -- Design water cut percentage
```

#### **Step 3: Well Completion Details**
**Data Fields (Continued: wells)**:
```sql
completion_details         -- JSON: well completion information
reservoir_data            -- JSON: reservoir characteristics
production_history        -- JSON: historical production data
well_interventions        -- JSON: well intervention history
artificial_lift           -- JSON: artificial lift systems
surface_equipment         -- JSON: surface equipment configuration
flow_assurance            -- JSON: flow assurance considerations
```

#### **Step 4: Reservoir & Completion Data Structure**
```json
{
    "completion_details": {
        "wellbore_configuration": {
            "total_depth_tvd_ft": 8500,
            "total_depth_md_ft": 9200,
            "casing_program": [
                {
                    "casing_size_inch": 20.0,
                    "setting_depth_ft": 500,
                    "casing_weight_ppf": 94,
                    "cement_top_ft": 0
                },
                {
                    "casing_size_inch": 13.375,
                    "setting_depth_ft": 3500,
                    "casing_weight_ppf": 68,
                    "cement_top_ft": 3000
                },
                {
                    "casing_size_inch": 9.625,
                    "setting_depth_ft": 8500,
                    "casing_weight_ppf": 47,
                    "cement_top_ft": 7500
                }
            ],
            "production_tubing": {
                "tubing_size_inch": 5.5,
                "tubing_weight_ppf": 17,
                "packer_depth_ft": 8200,
                "landing_nipple_depths_ft": [7500, 8000]
            }
        },
        "completion_equipment": {
            "downhole_safety_valve": {
                "type": "surface_controlled",
                "setting_depth_ft": 7800,
                "fail_safe_position": "closed"
            },
            "gas_lift_system": {
                "installed": true,
                "valve_count": 6,
                "deepest_valve_ft": 8000,
                "injection_pressure_psi": 1200
            },
            "artificial_lift": {
                "type": "gas_lift",
                "design_injection_rate_mscfd": 0.5,
                "operating_injection_rate_mscfd": 0.3
            }
        }
    },
    "reservoir_data": {
        "reservoir_properties": {
            "formation_name": "Miocene_Sand",
            "gross_thickness_ft": 150,
            "net_thickness_ft": 120,
            "porosity_percent": 18.5,
            "permeability_md": 250,
            "water_saturation_percent": 25.0
        },
        "fluid_properties": {
            "oil_gravity_api": 35.8,
            "oil_viscosity_cp": 1.2,
            "gas_specific_gravity": 0.65,
            "formation_volume_factor": 1.35,
            "bubble_point_pressure_psi": 2850,
            "solution_gor_scf_bbl": 450
        },
        "pressure_data": {
            "initial_reservoir_pressure_psi": 4200,
            "current_reservoir_pressure_psi": 3850,
            "pressure_depletion_psi": 350,
            "wellhead_flowing_pressure_psi": 1250
        }
    }
}
```

#### **Step 5: Well Performance Tracking**
```sql
-- Create well performance monitoring view
CREATE VIEW v_well_performance AS
SELECT 
    w.well_id,
    w.well_code,
    w.well_name,
    w.well_status,
    w.design_oil_rate_bpd,
    w.design_gas_rate_mscfd,
    
    -- Current production rates (last reading)
    (SELECT wp.oil_rate_bph * 24 FROM well_production wp 
     WHERE wp.well_id = w.id 
     ORDER BY wp.reading_datetime DESC LIMIT 1) as current_oil_rate_bpd,
     
    (SELECT wp.gas_rate_mscfh * 24 FROM well_production wp 
     WHERE wp.well_id = w.id 
     ORDER BY wp.reading_datetime DESC LIMIT 1) as current_gas_rate_mscfd,
     
    (SELECT wp.water_cut_pct FROM well_production wp 
     WHERE wp.well_id = w.id 
     ORDER BY wp.reading_datetime DESC LIMIT 1) as current_water_cut_pct,
    
    -- Performance vs design
    ROUND(
        (SELECT wp.oil_rate_bph * 24 FROM well_production wp 
         WHERE wp.well_id = w.id 
         ORDER BY wp.reading_datetime DESC LIMIT 1) / 
        w.design_oil_rate_bpd * 100, 1
    ) as oil_performance_vs_design_pct,
    
    ROUND(
        (SELECT wp.gas_rate_mscfh * 24 FROM well_production wp 
         WHERE wp.well_id = w.id 
         ORDER BY wp.reading_datetime DESC LIMIT 1) / 
        w.design_gas_rate_mscfd * 100, 1
    ) as gas_performance_vs_design_pct,
    
    -- Production trends (30-day average vs 7-day average)
    (SELECT AVG(wp.oil_rate_bph * 24) FROM well_production wp 
     WHERE wp.well_id = w.id 
     AND wp.reading_datetime >= CURDATE() - INTERVAL 30 DAY) as avg_oil_30day_bpd,
     
    (SELECT AVG(wp.oil_rate_bph * 24) FROM well_production wp 
     WHERE wp.well_id = w.id 
     AND wp.reading_datetime >= CURDATE() - INTERVAL 7 DAY) as avg_oil_7day_bpd,
    
    -- Well uptime
    ROUND(
        (SELECT COUNT(*) FROM well_production wp 
         WHERE wp.well_id = w.id 
         AND wp.reading_datetime >= CURDATE() - INTERVAL 30 DAY
         AND wp.oil_rate_bph > 0) /
        (SELECT COUNT(*) FROM well_production wp 
         WHERE wp.well_id = w.id 
         AND wp.reading_datetime >= CURDATE() - INTERVAL 30 DAY) * 100, 1
    ) as uptime_30day_pct,
    
    -- Well interventions
    JSON_EXTRACT(w.well_interventions, '$.last_workover_date') as last_workover_date,
    JSON_EXTRACT(w.well_interventions, '$.next_planned_intervention') as next_planned_intervention

FROM wells w
WHERE w.well_status IN ('Producing', 'Shut_in');
```

---

## ⚙️ **2.4 EQUIPMENT REGISTRY - User Flow**

### **SOP Step-by-Step**

#### **Step 1: Equipment Registration**
**Data Fields (Table: equipment)**:
```sql
equipment_id               -- Unique equipment identifier
equipment_tag              -- Equipment tag number (GTG-A-001)
equipment_name             -- Equipment descriptive name
equipment_type             -- Gas Turbine Generator/Pump/Compressor/etc.
equipment_category         -- Rotating/Static/Electrical/Instrumentation
vessel_id                  -- FK to vessels table
area_location              -- Physical area location
manufacturer               -- Equipment manufacturer
model                      -- Manufacturer model number
serial_number              -- Serial number
installation_date          -- Installation date
commissioning_date         -- Commissioning date
design_life_years          -- Design operational life
```

#### **Step 2: Equipment Specifications**
**Data Fields (Continued: equipment)**:
```sql
technical_specifications   -- JSON: detailed technical specs
design_parameters          -- JSON: design operating parameters
performance_data           -- JSON: performance curves and data
maintenance_requirements   -- JSON: maintenance schedule and requirements
criticality_classification -- Critical/Important/Standard
redundancy_available       -- Boolean: backup equipment available
safety_classification     -- Safety critical/Non-safety critical
environmental_classification -- Hydrocarbon/Water/Chemical service
spare_parts_list          -- JSON: recommended spare parts
```

#### **Step 3: Equipment Technical Specifications Matrix**
```json
{
    "gas_turbine_generator_specs": {
        "general_data": {
            "manufacturer": "Solar Turbines",
            "model": "Taurus 60",
            "serial_number": "TG22110",
            "installation_date": "2023-05-15",
            "design_life_years": 25
        },
        "performance_data": {
            "rated_power_kw": 1500,
            "rated_speed_rpm": 3600,
            "heat_rate_btu_kwh": 12500,
            "exhaust_flow_kg_s": 6.8,
            "exhaust_temperature_c": 485,
            "fuel_consumption_lph": 285,
            "efficiency_percent": 27.5
        },
        "operating_conditions": {
            "ambient_temperature_range_c": "-10_to_50",
            "altitude_limit_m": 1000,
            "relative_humidity_max_percent": 95,
            "fuel_gas_pressure_min_psi": 120,
            "fuel_gas_heating_value_btu_scf": 1000
        },
        "protection_systems": {
            "overspeed_trip_rpm": 3960,
            "high_exhaust_temp_trip_c": 520,
            "low_oil_pressure_trip_psi": 40,
            "high_vibration_trip_mm_s": 6.0,
            "flame_out_protection": true,
            "emergency_shutdown": true
        }
    },
    "centrifugal_pump_specs": {
        "general_data": {
            "manufacturer": "Sulzer",
            "model": "CPT-200",
            "serial_number": "SP45678",
            "pump_type": "horizontal_split_case"
        },
        "performance_data": {
            "rated_flow_gpm": 2500,
            "rated_head_ft": 350,
            "rated_speed_rpm": 1780,
            "efficiency_percent": 78,
            "npsh_required_ft": 12,
            "motor_power_hp": 300
        },
        "construction_materials": {
            "casing_material": "carbon_steel",
            "impeller_material": "316_stainless_steel",
            "shaft_material": "410_stainless_steel",
            "seal_type": "mechanical_seal"
        },
        "operating_limits": {
            "minimum_flow_gpm": 500,
            "maximum_flow_gpm": 3000,
            "maximum_head_ft": 400,
            "temperature_limit_f": 200,
            "pressure_limit_psi": 300
        }
    }
}
```

#### **Step 4: Criticality Assessment Matrix**
```json
{
    "criticality_assessment": {
        "critical_equipment": {
            "definition": "Equipment whose failure results in production shutdown or safety risk",
            "examples": ["Emergency shutdown system", "Fire and gas detection", "Main power generators"],
            "redundancy_required": true,
            "maintenance_priority": "highest",
            "spare_parts_stocking": "onboard_inventory"
        },
        "important_equipment": {
            "definition": "Equipment whose failure reduces production or operational efficiency",
            "examples": ["Production pumps", "Compressors", "Process heaters"],
            "redundancy_recommended": true,
            "maintenance_priority": "high",
            "spare_parts_stocking": "strategic_inventory"
        },
        "standard_equipment": {
            "definition": "Equipment whose failure has minimal operational impact",
            "examples": ["Utility pumps", "General lighting", "Non-critical instrumentation"],
            "redundancy_optional": false,
            "maintenance_priority": "normal",
            "spare_parts_stocking": "vendor_support"
        }
    }
}
```

---

## ⛽ **2.5 GAS BUYERS - User Flow**

### **SOP Step-by-Step**

#### **Step 1: Gas Buyer Registration**
**Data Fields (Table: gas_buyers)**:
```sql
buyer_id                   -- Unique buyer identifier
code                       -- Buyer code (PGN, PLN, PUPUK)
name                       -- Buyer company name
buyer_type                 -- Utility/Industrial/Pipeline/LNG
contact_person             -- Primary contact person
contact_email              -- Contact email address
contact_phone              -- Contact phone number
billing_address           -- Billing address
delivery_point            -- Gas delivery point
contract_start_date       -- Contract start date
contract_end_date         -- Contract end date
is_active                 -- Boolean: active status
```

#### **Step 2: Commercial Terms Configuration**
**Data Fields (Continued: gas_buyers)**:
```sql
commercial_terms           -- JSON: commercial agreement terms
pricing_structure          -- JSON: pricing mechanism
delivery_terms            -- JSON: delivery obligations
quality_specifications    -- JSON: gas quality requirements
measurement_standards     -- JSON: measurement and billing standards
force_majeure_terms       -- JSON: force majeure provisions
contract_capacity_mmscfd  -- Contract capacity
minimum_take_mmscfd       -- Minimum take requirement
maximum_daily_quantity    -- Maximum daily quantity
interruptible_volume      -- Interruptible volume availability
```

#### **Step 3: Gas Sales Contract Structure**
```json
{
    "commercial_terms": {
        "pricing_mechanism": {
            "base_price_mscf": 6.50,
            "price_adjustment_frequency": "quarterly",
            "price_escalation_rate_percent": 2.5,
            "heating_value_adjustment": {
                "base_heating_value_btu": 1000,
                "adjustment_factor_per_btu": 0.0065,
                "minimum_heating_value": 980,
                "maximum_heating_value": 1100
            },
            "currency": "USD",
            "payment_terms": "net_30_days"
        },
        "delivery_obligations": {
            "contract_quantity_mmscfd": 15.0,
            "minimum_take_mmscfd": 12.0,
            "maximum_daily_quantity_mmscfd": 18.0,
            "annual_contract_quantity_mmscf": 5475.0,
            "tolerance_percent": 5.0,
            "delivery_point": "Platform_outlet_flange",
            "delivery_pressure_psi": 850
        },
        "quality_specifications": {
            "heating_value_min_btu": 980,
            "specific_gravity_max": 0.70,
            "water_content_max_ppm": 50,
            "hydrogen_sulfide_max_ppm": 4,
            "total_sulfur_max_ppm": 25,
            "carbon_dioxide_max_percent": 3.0,
            "nitrogen_max_percent": 5.0,
            "oxygen_max_percent": 0.1
        }
    }
}
```

#### **Step 4: Performance Tracking & Compliance**
```sql
-- Gas sales performance monitoring
CREATE VIEW v_gas_sales_performance AS
SELECT 
    gb.buyer_id,
    gb.code,
    gb.name,
    gb.contract_capacity_mmscfd,
    gb.minimum_take_mmscfd,
    
    -- Monthly performance
    YEAR(gs.sales_date) as year,
    MONTH(gs.sales_date) as month,
    SUM(gs.actual_delivery_mmscf) as total_delivered_mmscf,
    SUM(gs.nomination_mmscf) as total_nominated_mmscf,
    AVG(gs.heating_value_btu) as avg_heating_value_btu,
    AVG(gs.export_pressure_psi) as avg_delivery_pressure_psi,
    
    -- Contract compliance
    ROUND(
        SUM(gs.actual_delivery_mmscf) / 
        (gb.minimum_take_mmscfd * DAY(LAST_DAY(gs.sales_date))) * 100, 1
    ) as minimum_take_compliance_pct,
    
    ROUND(
        SUM(gs.actual_delivery_mmscf) / SUM(gs.nomination_mmscf) * 100, 1
    ) as nomination_compliance_pct,
    
    -- Quality compliance
    SUM(CASE 
        WHEN gs.heating_value_btu >= JSON_EXTRACT(gb.quality_specifications, '$.heating_value_min_btu') 
        THEN gs.actual_delivery_mmscf 
        ELSE 0 
    END) / SUM(gs.actual_delivery_mmscf) * 100 as quality_compliance_pct,
    
    -- Revenue calculation
    SUM(
        gs.actual_delivery_mmscf * 1000 * 
        (JSON_EXTRACT(gb.commercial_terms, '$.pricing_mechanism.base_price_mscf') +
         CASE 
             WHEN gs.heating_value_btu > JSON_EXTRACT(gb.commercial_terms, '$.pricing_mechanism.heating_value_adjustment.base_heating_value_btu')
             THEN (gs.heating_value_btu - JSON_EXTRACT(gb.commercial_terms, '$.pricing_mechanism.heating_value_adjustment.base_heating_value_btu')) *
                  JSON_EXTRACT(gb.commercial_terms, '$.pricing_mechanism.heating_value_adjustment.adjustment_factor_per_btu')
             ELSE 0
         END)
    ) as calculated_revenue_usd

FROM gas_buyers gb
INNER JOIN gas_sales gs ON gb.buyer_id = gs.buyer_id
WHERE gb.is_active = true
  AND gs.sales_date >= CURDATE() - INTERVAL 12 MONTH
GROUP BY gb.buyer_id, gb.code, gb.name, gb.contract_capacity_mmscfd, 
         gb.minimum_take_mmscfd, YEAR(gs.sales_date), MONTH(gs.sales_date),
         gb.quality_specifications, gb.commercial_terms;
```

---

## 📊 **MASTER DATA DASHBOARD**

### **Master Data Health Check Dashboard**
```javascript
// Vue.js Master Data Dashboard Component
const MasterDataDashboard = {
    data() {
        return {
            contracts: [],
            vessels: [],
            wells: [],
            equipment: [],
            gasBuyers: [],
            dataQualityIssues: []
        };
    },
    
    computed: {
        activeContracts() {
            return this.contracts.filter(c => c.status === 'Active').length;
        },
        
        operationalVessels() {
            return this.vessels.filter(v => v.status === 'Active').length;
        },
        
        producingWells() {
            return this.wells.filter(w => w.status === 'Producing').length;
        },
        
        criticalEquipmentCount() {
            return this.equipment.filter(e => 
                e.criticality_classification === 'Critical'
            ).length;
        },
        
        activeGasBuyers() {
            return this.gasBuyers.filter(gb => gb.is_active).length;
        },
        
        dataCompleteness() {
            const totalFields = this.contracts.length * 10 + 
                              this.vessels.length * 15 + 
                              this.wells.length * 12 + 
                              this.equipment.length * 20;
            const completedFields = totalFields - this.dataQualityIssues.length;
            return totalFields > 0 ? Math.round((completedFields / totalFields) * 100) : 0;
        }
    },
    
    methods: {
        async refreshMasterData() {
            try {
                const response = await fetch('/api/master-data/dashboard');
                const data = await response.json();
                
                this.contracts = data.contracts;
                this.vessels = data.vessels;
                this.wells = data.wells;
                this.equipment = data.equipment;
                this.gasBuyers = data.gas_buyers;
                this.dataQualityIssues = data.data_quality_issues;
                
            } catch (error) {
                console.error('Error fetching master data:', error);
            }
        },
        
        getStatusColor(status) {
            switch(status) {
                case 'Active': return '#22c55e';
                case 'Inactive': return '#6b7280';
                case 'Expired': return '#ef4444';
                default: return '#f59e0b';
            }
        },
        
        formatCurrency(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(amount);
        }
    },
    
    mounted() {
        this.refreshMasterData();
        // Refresh every hour
        setInterval(this.refreshMasterData, 3600000);
    }
};
```

---

## 📋 **DVR INTEGRATION**

### **Master Data Summary for DVR Generation**
```sql
-- Generate master data context for DVR
SELECT 
    v.id as vessel_id,
    v.name as vessel_name,
    
    -- Contract information
    JSON_OBJECT(
        'contract_number', c.contract_number,
        'contract_name', c.contract_name,
        'operator', c.operator_name,
        'contractor', c.contractor_name,
        'contractor_share_pct', c.contractor_share_pct,
        'field_location', c.field_location
    ) as contract_info,
    
    -- Vessel specifications
    JSON_OBJECT(
        'vessel_code', v.code,
        'vessel_type', v.vessel_type,
        'field_name', v.field_name,
        'water_depth_m', v.water_depth_m,
        'accommodation_capacity', JSON_EXTRACT(v.technical_specifications, '$.accommodation_pax'),
        'processing_capacity', v.processing_capacity
    ) as vessel_specifications,
    
    -- Wells summary
    JSON_OBJECT(
        'total_wells', (SELECT COUNT(*) FROM wells w WHERE w.vessel_id = v.id),
        'producing_wells', (SELECT COUNT(*) FROM wells w WHERE w.vessel_id = v.id AND w.well_status = 'Producing'),
        'shut_in_wells', (SELECT COUNT(*) FROM wells w WHERE w.vessel_id = v.id AND w.well_status = 'Shut_in'),
        'total_design_oil_bpd', (SELECT SUM(w.design_oil_rate_bpd) FROM wells w WHERE w.vessel_id = v.id AND w.well_status = 'Producing'),
        'total_design_gas_mscfd', (SELECT SUM(w.design_gas_rate_mscfd) FROM wells w WHERE w.vessel_id = v.id AND w.well_status = 'Producing')
    ) as wells_summary,
    
    -- Equipment inventory
    JSON_OBJECT(
        'total_equipment', (SELECT COUNT(*) FROM equipment e WHERE e.vessel_id = v.id),
        'critical_equipment', (SELECT COUNT(*) FROM equipment e WHERE e.vessel_id = v.id AND e.criticality_classification = 'Critical'),
        'rotating_equipment', (SELECT COUNT(*) FROM equipment e WHERE e.vessel_id = v.id AND e.equipment_category = 'Rotating'),
        'static_equipment', (SELECT COUNT(*) FROM equipment e WHERE e.vessel_id = v.id AND e.equipment_category = 'Static')
    ) as equipment_inventory,
    
    -- Gas sales contracts
    JSON_ARRAYAGG(
        JSON_OBJECT(
            'buyer_code', gb.code,
            'buyer_name', gb.name,
            'contract_capacity_mmscfd', gb.contract_capacity_mmscfd,
            'minimum_take_mmscfd', gb.minimum_take_mmscfd,
            'delivery_point', JSON_EXTRACT(gb.delivery_terms, '$.delivery_point')
        )
    ) as gas_buyers_summary

FROM vessels v
INNER JOIN contracts c ON v.contract_id = c.contract_id
LEFT JOIN gas_buyers gb ON gb.is_active = true
WHERE v.status = 'Active'
GROUP BY v.id, v.name, v.code, v.vessel_type, v.field_name, v.water_depth_m,
         v.technical_specifications, v.processing_capacity,
         c.contract_number, c.contract_name, c.operator_name, c.contractor_name,
         c.contractor_share_pct, c.field_location;
```

---

## 🎯 **SUCCESS METRICS**

### **Master Data Module KPIs**
- **Data Completeness**: 100% mandatory fields populated for all master data
- **Data Accuracy**: 99.5% data validation checks passed
- **Reference Integrity**: 100% foreign key constraints maintained
- **Update Timeliness**: All master data changes reflected within 24 hours
- **User Access Control**: 100% role-based access compliance

### **Operational Benefits**
✅ **Data Foundation** - Solid foundation for all operational data and reporting  
✅ **Operational Consistency** - Standardized reference data across all modules  
✅ **Contract Compliance** - Accurate revenue calculations based on contract terms  
✅ **Asset Management** - Complete asset registry for maintenance and performance tracking  
✅ **Commercial Management** - Structured gas sales contract management and performance monitoring