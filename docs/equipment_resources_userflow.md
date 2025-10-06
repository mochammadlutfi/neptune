# EQUIPMENT & RESOURCES MODULE - User Flow & SOP
**DVR-DCR Management System - Pakarti Tirtoagung**

---

## ⚙️ **MODULE OVERVIEW**

**Objective**: Mengelola status equipment, maintenance activities, spare parts inventory, dan fuel management untuk memastikan operational readiness dan regulatory compliance.

**Key Tables**: 
- `equipment_status` - Real-time equipment monitoring
- `maintenance_activities` - PM/PdM work orders
- `spare_parts_inventory` - Inventory management
- `fuel_inventory` - Fuel consumption tracking

**User Roles**: Equipment Technician, Maintenance Supervisor, Inventory Controller, Fuel Manager

---

## 🗂️ **MENU STRUCTURE**

```
⚙️ EQUIPMENT & RESOURCES
├── 4.1 Equipment Status           → equipment_status
├── 4.2 Maintenance Activities     → maintenance_activities
├── 4.3 Spare Parts Management     → spare_parts
├── 4.4 Spare Parts Inventory      → spare_parts_inventory
├── 4.5 Fuel Management           → fuel_tanks
└── 4.6 Fuel Inventory            → fuel_inventory
```

---

## 🔄 **USER FLOW OVERVIEW**

### **Daily Equipment Management Cycle**
1. **Equipment Status Update** (Every 2 hours) → Real-time monitoring
2. **Maintenance Execution** (As scheduled) → PM/PdM activities
3. **Spare Parts Consumption** (During maintenance) → Auto-deduct inventory
4. **Fuel Consumption Tracking** (Daily) → Fuel management
5. **Equipment Summary Generation** (End of day) → DVR integration

---

## 📊 **4.1 EQUIPMENT STATUS - User Flow**

### **Access Control**
- **Primary Users**: Equipment Technician, Shift Operator
- **Supervisory Access**: Maintenance Supervisor, OIM
- **Real-time Updates**: Every 2 hours monitoring

### **SOP Step-by-Step**

#### **Step 1: Equipment Round Schedule**
```
Shift Times: 00:00, 02:00, 04:00, 06:00, 08:00, 10:00, 
             12:00, 14:00, 16:00, 18:00, 20:00, 22:00

Round Categories:
- Critical Equipment (Every 2 hours)
- Non-critical Equipment (Every 8 hours)
- Standby Equipment (Daily check)
```

#### **Step 2: Equipment Status Entry**
**Data Fields (Table: equipment_status)**:
```sql
equipment_id                -- FK to equipment table
status_datetime             -- Timestamp of reading
operational_status          -- Running/Standby/Shutdown/Maintenance
availability_hours          -- Hours available in current period
downtime_hours             -- Hours down in current period
real_time_parameters       -- JSON field for equipment-specific readings
condition_notes           -- Text field for observations
maintenance_due_date      -- Next scheduled maintenance
critical_alerts          -- JSON array of current alerts
recorded_by              -- Technician name/ID
```

#### **Step 3: Equipment-Specific Parameters**

**A. Gas Turbine Generator (GTG)**
```json
{
    "electrical_parameters": {
        "load_kw": 1250,
        "load_percent": 85.5,
        "frequency_hz": 50.01,
        "voltage_v": 415,
        "current_amp": 1890,
        "power_factor": 0.85
    },
    "engine_parameters": {
        "engine_speed_rpm": 3600,
        "exhaust_temp_c": 485,
        "oil_pressure_psi": 65,
        "oil_temp_c": 78,
        "coolant_temp_c": 85,
        "fuel_consumption_lph": 285,
        "running_hours": 10982.5
    },
    "condition_monitoring": {
        "vibration_overall": 2.8,
        "bearing_temp_de": 75.5,
        "bearing_temp_nde": 72.8,
        "lube_oil_quality": "Good"
    }
}
```

**B. Centrifugal Pump**
```json
{
    "process_parameters": {
        "suction_pressure_psi": 25.5,
        "discharge_pressure_psi": 120.8,
        "differential_pressure_psi": 95.3,
        "flow_rate_gpm": 450,
        "fluid_temp_f": 85,
        "npsh_available_ft": 15.2
    },
    "performance_parameters": {
        "speed_rpm": 1750,
        "motor_current_amp": 45.2,
        "efficiency_percent": 78.5,
        "power_consumption_kw": 37.5
    },
    "condition_monitoring": {
        "vibration_overall": 1.8,
        "bearing_temp_de": 68.5,
        "bearing_temp_nde": 65.2,
        "seal_condition": "Good"
    }
}
```

**C. Gas Compressor**
```json
{
    "process_parameters": {
        "suction_pressure_psi": 125.5,
        "discharge_pressure_psi": 850.2,
        "compression_ratio": 6.8,
        "gas_flow_mmscfd": 12.5,
        "suction_temp_f": 85,
        "discharge_temp_f": 185
    },
    "performance_parameters": {
        "speed_rpm": 3600,
        "power_consumption_kw": 2850,
        "efficiency_percent": 78.5,
        "fuel_consumption_mmscfd": 0.85
    },
    "condition_monitoring": {
        "vibration_overall": 3.2,
        "bearing_temp_1": 85.5,
        "bearing_temp_2": 82.8,
        "lube_oil_pressure": 45.0
    }
}
```

#### **Step 4: Real-time Validation & Alerts**
```sql
-- Trigger for equipment alerts
DELIMITER //
CREATE TRIGGER equipment_status_alerts
AFTER INSERT ON equipment_status
FOR EACH ROW
BEGIN
    DECLARE equipment_type VARCHAR(50);
    DECLARE alert_message TEXT;
    DECLARE alert_level VARCHAR(20);
    
    -- Get equipment type
    SELECT e.equipment_type INTO equipment_type
    FROM equipment e WHERE e.equipment_tag = NEW.equipment_id;
    
    -- GTG specific alerts
    IF equipment_type = 'Gas Turbine Generator' THEN
        -- Exhaust temperature alarm
        IF JSON_EXTRACT(NEW.real_time_parameters, '$.engine_parameters.exhaust_temp_c') > 520 THEN
            INSERT INTO equipment_alerts (equipment_id, alert_type, alert_level, message, created_at)
            VALUES (NEW.equipment_id, 'Temperature', 'CRITICAL', 
                   CONCAT('GTG exhaust temperature alarm: ', 
                          JSON_EXTRACT(NEW.real_time_parameters, '$.engine_parameters.exhaust_temp_c'), '°C'), 
                   NOW());
        END IF;
        
        -- Oil pressure low alarm
        IF JSON_EXTRACT(NEW.real_time_parameters, '$.engine_parameters.oil_pressure_psi') < 40 THEN
            INSERT INTO equipment_alerts (equipment_id, alert_type, alert_level, message, created_at)
            VALUES (NEW.equipment_id, 'Pressure', 'CRITICAL', 
                   CONCAT('GTG oil pressure low: ', 
                          JSON_EXTRACT(NEW.real_time_parameters, '$.engine_parameters.oil_pressure_psi'), ' psi'), 
                   NOW());
        END IF;
    END IF;
    
    -- Pump specific alerts
    IF equipment_type = 'Centrifugal Pump' THEN
        -- Low suction pressure
        IF JSON_EXTRACT(NEW.real_time_parameters, '$.process_parameters.suction_pressure_psi') < 10 THEN
            INSERT INTO equipment_alerts (equipment_id, alert_type, alert_level, message, created_at)
            VALUES (NEW.equipment_id, 'Pressure', 'WARNING', 
                   'Pump suction pressure low - check strainer', NOW());
        END IF;
        
        -- High vibration
        IF JSON_EXTRACT(NEW.real_time_parameters, '$.condition_monitoring.vibration_overall') > 4.5 THEN
            INSERT INTO equipment_alerts (equipment_id, alert_type, alert_level, message, created_at)
            VALUES (NEW.equipment_id, 'Vibration', 'CRITICAL', 
                   'Pump vibration high - investigate immediately', NOW());
        END IF;
    END IF;
END//
DELIMITER ;
```

#### **Step 5: Availability Calculation**
```sql
-- Auto-calculate equipment availability
UPDATE equipment_status es
INNER JOIN equipment e ON es.equipment_id = e.equipment_tag
SET 
    es.availability_hours = CASE 
        WHEN es.operational_status IN ('Running', 'Standby') 
        THEN COALESCE(es.availability_hours, 0) + 2 
        ELSE COALESCE(es.availability_hours, 0)
    END,
    es.downtime_hours = CASE 
        WHEN es.operational_status IN ('Shutdown', 'Maintenance') 
        THEN COALESCE(es.downtime_hours, 0) + 2 
        ELSE COALESCE(es.downtime_hours, 0)
    END,
    es.availability_percentage = 
        CASE 
            WHEN (COALESCE(es.availability_hours, 0) + COALESCE(es.downtime_hours, 0)) > 0
            THEN (COALESCE(es.availability_hours, 0) / 
                  (COALESCE(es.availability_hours, 0) + COALESCE(es.downtime_hours, 0))) * 100
            ELSE 100
        END
WHERE DATE(es.status_datetime) = CURDATE()
  AND es.id = NEW.id;
```

---

## 🔧 **4.2 MAINTENANCE ACTIVITIES - User Flow**

### **Access Control**
- **Primary Users**: Maintenance Supervisor, Maintenance Technician
- **Planning Access**: Maintenance Planner
- **Approval Required**: Work orders >$5000 value

### **SOP Step-by-Step**

#### **Step 1: Work Order Types**
```
PM (Preventive Maintenance):
- Weekly inspections
- Monthly services  
- Quarterly overhauls
- Annual certifications

PdM (Predictive Maintenance):
- Vibration analysis
- Thermography surveys
- Oil analysis
- Performance monitoring

CM (Corrective Maintenance):
- Emergency repairs
- Breakdown maintenance
- Modifications
- Upgrades
```

#### **Step 2: Work Order Creation**
**Data Fields (Table: maintenance_activities)**:
```sql
equipment_id                -- FK to equipment table
activity_date              -- Scheduled/actual date
activity_type              -- PM/PdM/CM/Emergency
work_order_no             -- Unique WO number
description               -- Work description
priority                  -- High/Medium/Low/Emergency
estimated_hours           -- Planned labor hours
actual_hours             -- Actual labor hours completed
completion_status         -- Scheduled/In_Progress/Completed/Cancelled
maintenance_details       -- JSON field for detailed information
parts_used               -- JSON array of spare parts consumed
performed_by             -- Technician name/ID
supervised_by            -- Supervisor name/ID
completion_date          -- Actual completion timestamp
cost_labor              -- Labor cost
cost_materials          -- Materials cost
cost_total              -- Total maintenance cost
```

#### **Step 3: Maintenance Details JSON Structure**
```json
{
    "work_scope": {
        "inspection_points": [
            "Visual inspection of casing",
            "Check oil levels and quality",
            "Verify pressure readings",
            "Test emergency shutdown systems"
        ],
        "measurements_taken": [
            {"parameter": "Vibration", "value": 2.8, "unit": "mm/s", "acceptable": true},
            {"parameter": "Oil pressure", "value": 65, "unit": "psi", "acceptable": true},
            {"parameter": "Temperature", "value": 78, "unit": "°C", "acceptable": true}
        ],
        "issues_found": [
            "Minor oil leak at seal",
            "Air filter requires replacement"
        ],
        "corrective_actions": [
            "Tightened seal bolts",
            "Scheduled air filter replacement"
        ]
    },
    "safety_procedures": {
        "permit_required": true,
        "permit_number": "WP-2025-001234",
        "safety_measures": [
            "Lockout/Tagout applied",
            "Gas test completed",
            "Fire watch posted"
        ],
        "hazards_identified": [
            "Pressurized system",
            "Hot surfaces",
            "Confined space"
        ]
    },
    "quality_control": {
        "inspection_completed": true,
        "test_run_performed": true,
        "performance_verified": true,
        "documentation_complete": true
    }
}
```

#### **Step 4: Spare Parts Integration**
```sql
-- Auto-deduct spare parts on maintenance completion
DELIMITER //
CREATE TRIGGER maintenance_parts_consumption
AFTER UPDATE ON maintenance_activities
FOR EACH ROW
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE part_id INT;
    DECLARE quantity_used DECIMAL(10,2);
    DECLARE parts_cursor CURSOR FOR 
        SELECT 
            JSON_EXTRACT(part.value, '$.spare_part_id'),
            JSON_EXTRACT(part.value, '$.quantity_used')
        FROM JSON_TABLE(NEW.parts_used, '$[*]' COLUMNS (
            value JSON PATH '$'
        )) AS part;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Only process when status changes to Completed
    IF NEW.completion_status = 'Completed' AND OLD.completion_status != 'Completed' THEN
        OPEN parts_cursor;
        
        parts_loop: LOOP
            FETCH parts_cursor INTO part_id, quantity_used;
            IF done THEN
                LEAVE parts_loop;
            END IF;
            
            -- Update spare parts inventory
            UPDATE spare_parts_inventory spi
            SET 
                spi.quantity_onboard = spi.quantity_onboard - quantity_used,
                spi.last_updated = NOW()
            WHERE spi.spare_part_id = part_id
              AND spi.inventory_date = (
                  SELECT MAX(inventory_date) 
                  FROM spare_parts_inventory 
                  WHERE spare_part_id = part_id
              );
              
            -- Insert usage record
            INSERT INTO spare_parts_usage (
                spare_part_id, maintenance_activity_id, quantity_used, 
                usage_date, recorded_by
            ) VALUES (
                part_id, NEW.id, quantity_used, NOW(), NEW.performed_by
            );
            
        END LOOP;
        
        CLOSE parts_cursor;
    END IF;
END//
DELIMITER ;
```

#### **Step 5: Automatic PM Scheduling**
```sql
-- Generate preventive maintenance schedules
DELIMITER //
CREATE PROCEDURE generate_pm_schedule(IN vessel_id INT, IN schedule_date DATE)
BEGIN
    -- Weekly PM generation
    INSERT INTO maintenance_activities (
        equipment_id, activity_date, activity_type, work_order_no, 
        description, priority, estimated_hours, completion_status
    )
    SELECT 
        e.equipment_tag,
        schedule_date + INTERVAL 7 DAY,
        'PM',
        CONCAT('PM-', DATE_FORMAT(schedule_date + INTERVAL 7 DAY, '%Y%m%d'), '-', e.equipment_tag),
        CONCAT('Weekly PM - ', e.equipment_name),
        'Medium',
        e.pm_weekly_hours,
        'Scheduled'
    FROM equipment e
    WHERE e.vessel_id = vessel_id
      AND e.pm_weekly_required = TRUE
      AND DAYOFWEEK(schedule_date + INTERVAL 7 DAY) = e.pm_weekly_day;
      
    -- Monthly PM generation
    INSERT INTO maintenance_activities (
        equipment_id, activity_date, activity_type, work_order_no, 
        description, priority, estimated_hours, completion_status
    )
    SELECT 
        e.equipment_tag,
        LAST_DAY(schedule_date) + INTERVAL 1 DAY + INTERVAL (e.pm_monthly_day - 1) DAY,
        'PM',
        CONCAT('PM-', DATE_FORMAT(LAST_DAY(schedule_date) + INTERVAL 1 DAY, '%Y%m'), '-', e.equipment_tag),
        CONCAT('Monthly PM - ', e.equipment_name),
        'High',
        e.pm_monthly_hours,
        'Scheduled'
    FROM equipment e
    WHERE e.vessel_id = vessel_id
      AND e.pm_monthly_required = TRUE;
END//
DELIMITER ;
```

---

## 📦 **4.3 SPARE PARTS MANAGEMENT - User Flow**

### **SOP Step-by-Step**

#### **Step 1: Parts Catalog Management**
**Data Fields (Table: spare_parts)**:
```sql
part_number                 -- Unique part identifier
part_name                   -- Descriptive name
part_category              -- Category (Mechanical/Electrical/Instrumentation)
equipment_compatibility    -- JSON array of compatible equipment
manufacturer              -- OEM manufacturer
supplier_name             -- Primary supplier
unit_price                -- Current unit price
currency                  -- Price currency
unit_of_measure           -- Each/Meter/Kilogram/Liter
lead_time_days            -- Supplier lead time
minimum_stock_level       -- Minimum inventory level
reorder_point             -- Automatic reorder trigger
safety_stock              -- Emergency stock level
criticality               -- Critical/Important/Standard
storage_location          -- Physical location code
storage_requirements      -- Special storage needs
specifications            -- Technical specifications JSON
created_at                -- Catalog entry date
```

#### **Step 2: Equipment Compatibility Matrix**
```json
{
    "compatible_equipment": [
        {
            "equipment_tag": "GTG-A-001",
            "equipment_type": "Gas Turbine Generator",
            "application": "Oil filter element",
            "replacement_interval": "500 hours",
            "quantity_per_service": 1
        },
        {
            "equipment_tag": "GTG-B-001", 
            "equipment_type": "Gas Turbine Generator",
            "application": "Oil filter element",
            "replacement_interval": "500 hours",
            "quantity_per_service": 1
        }
    ],
    "specifications": {
        "outer_diameter": "89 mm",
        "inner_diameter": "25 mm", 
        "height": "135 mm",
        "filtration_rating": "10 micron",
        "flow_rate": "45 LPM",
        "pressure_rating": "10 bar"
    },
    "alternatives": [
        {
            "part_number": "OF-2024-ALT",
            "manufacturer": "Alternative Supplier",
            "interchangeable": true,
            "performance_equivalent": true
        }
    ]
}
```

---

## 📊 **4.4 SPARE PARTS INVENTORY - User Flow**

### **SOP Step-by-Step**

#### **Step 1: Daily Inventory Management**
**Data Fields (Table: spare_parts_inventory)**:
```sql
spare_part_id              -- FK to spare_parts table
vessel_id                  -- FK to vessels table
inventory_date             -- Date of inventory count
quantity_onboard          -- Current quantity on board
quantity_received         -- Quantity received today
quantity_consumed         -- Quantity used today
quantity_transferred      -- Quantity transferred to other vessels
rob_quantity              -- Remaining on board quantity
min_stock_level           -- Minimum required stock
reorder_point             -- Automatic reorder trigger
condition_status          -- New/Good/Fair/Condemned
storage_location          -- Physical storage location
last_movement_date        -- Last inventory movement
recorded_by               -- Inventory controller name
```

#### **Step 2: Automatic Reorder System**
```sql
-- Trigger automatic reorder alerts
DELIMITER //
CREATE TRIGGER inventory_reorder_check
AFTER UPDATE ON spare_parts_inventory
FOR EACH ROW
BEGIN
    -- Check if inventory dropped below reorder point
    IF NEW.quantity_onboard <= NEW.reorder_point AND OLD.quantity_onboard > OLD.reorder_point THEN
        INSERT INTO inventory_alerts (
            spare_part_id, vessel_id, alert_type, alert_level, 
            message, current_stock, reorder_point, created_at
        ) VALUES (
            NEW.spare_part_id, NEW.vessel_id, 'REORDER', 'HIGH',
            CONCAT('Stock below reorder point for part: ', 
                   (SELECT part_name FROM spare_parts WHERE id = NEW.spare_part_id)),
            NEW.quantity_onboard, NEW.reorder_point, NOW()
        );
    END IF;
    
    -- Critical stock level alert
    IF NEW.quantity_onboard <= NEW.min_stock_level THEN
        INSERT INTO inventory_alerts (
            spare_part_id, vessel_id, alert_type, alert_level, 
            message, current_stock, min_stock_level, created_at
        ) VALUES (
            NEW.spare_part_id, NEW.vessel_id, 'CRITICAL_STOCK', 'CRITICAL',
            CONCAT('Critical stock level for part: ', 
                   (SELECT part_name FROM spare_parts WHERE id = NEW.spare_part_id)),
            NEW.quantity_onboard, NEW.min_stock_level, NOW()
        );
    END IF;
END//
DELIMITER ;
```

#### **Step 3: Monthly Inventory Reconciliation**
```sql
-- Monthly inventory reconciliation procedure
DELIMITER //
CREATE PROCEDURE monthly_inventory_reconciliation(IN vessel_id INT, IN reconcile_month DATE)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE part_id INT;
    DECLARE theoretical_stock DECIMAL(10,2);
    DECLARE actual_stock DECIMAL(10,2);
    DECLARE variance DECIMAL(10,2);
    
    DECLARE inventory_cursor CURSOR FOR 
        SELECT DISTINCT spare_part_id FROM spare_parts_inventory 
        WHERE vessel_id = vessel_id 
          AND MONTH(inventory_date) = MONTH(reconcile_month)
          AND YEAR(inventory_date) = YEAR(reconcile_month);
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN inventory_cursor;
    
    reconcile_loop: LOOP
        FETCH inventory_cursor INTO part_id;
        IF done THEN
            LEAVE reconcile_loop;
        END IF;
        
        -- Calculate theoretical stock
        SELECT 
            (opening_stock + total_received - total_consumed - total_transferred) INTO theoretical_stock
        FROM (
            SELECT 
                COALESCE(opening.quantity_onboard, 0) as opening_stock,
                COALESCE(SUM(spi.quantity_received), 0) as total_received,
                COALESCE(SUM(spi.quantity_consumed), 0) as total_consumed,
                COALESCE(SUM(spi.quantity_transferred), 0) as total_transferred
            FROM spare_parts_inventory spi
            LEFT JOIN spare_parts_inventory opening ON opening.spare_part_id = part_id
                AND opening.inventory_date = DATE_SUB(reconcile_month, INTERVAL 1 MONTH)
            WHERE spi.spare_part_id = part_id
              AND spi.vessel_id = vessel_id
              AND MONTH(spi.inventory_date) = MONTH(reconcile_month)
              AND YEAR(spi.inventory_date) = YEAR(reconcile_month)
        ) calc;
        
        -- Get actual stock (latest count)
        SELECT quantity_onboard INTO actual_stock
        FROM spare_parts_inventory 
        WHERE spare_part_id = part_id 
          AND vessel_id = vessel_id
          AND inventory_date = LAST_DAY(reconcile_month)
        LIMIT 1;
        
        -- Calculate variance
        SET variance = actual_stock - theoretical_stock;
        
        -- Insert reconciliation record if variance exists
        IF ABS(variance) > 0.01 THEN
            INSERT INTO inventory_reconciliation (
                spare_part_id, vessel_id, reconcile_month,
                theoretical_stock, actual_stock, variance,
                variance_percentage, investigation_required, created_at
            ) VALUES (
                part_id, vessel_id, reconcile_month,
                theoretical_stock, actual_stock, variance,
                (variance / theoretical_stock) * 100,
                CASE WHEN ABS(variance) > (theoretical_stock * 0.05) THEN TRUE ELSE FALSE END,
                NOW()
            );
        END IF;
        
    END LOOP;
    
    CLOSE inventory_cursor;
END//
DELIMITER ;
```

---

## ⛽ **4.5 FUEL MANAGEMENT - User Flow**

### **SOP Step-by-Step**

#### **Step 1: Fuel Tank Configuration**
**Data Fields (Table: fuel_tanks)**:
```sql
vessel_id                  -- FK to vessels table
tank_name                  -- Tank identifier (Storage Tank #22)
tank_number               -- Tank number (22, 23, 26, 27)
tank_type                 -- Storage/Daily/Service
capacity_liters           -- Maximum tank capacity
fuel_type                 -- Diesel/Gas Oil/Heavy Fuel Oil
tank_location             -- Physical location on vessel
calibration_curve         -- JSON tank calibration data
low_level_alarm           -- Low level alarm setting
high_level_alarm          -- High level alarm setting
tank_status               -- Active/Inactive/Maintenance
installation_date         -- Tank installation date
last_calibration_date     -- Last calibration verification
```

#### **Step 2: Tank Calibration Data**
```json
{
    "calibration_curve": {
        "measurement_method": "Ullage",
        "calibration_points": [
            {"ullage_cm": 0, "volume_liters": 63359.5},
            {"ullage_cm": 50, "volume_liters": 58000.0},
            {"ullage_cm": 100, "volume_liters": 52500.0},
            {"ullage_cm": 150, "volume_liters": 47000.0},
            {"ullage_cm": 200, "volume_liters": 41500.0},
            {"ullage_cm": 250, "volume_liters": 36000.0},
            {"ullage_cm": 300, "volume_liters": 30500.0}
        ],
        "interpolation_method": "linear",
        "accuracy_tolerance": "±2%"
    },
    "tank_specifications": {
        "material": "Steel",
        "coating": "Epoxy",
        "design_pressure": "atmospheric",
        "temperature_range": "-10°C to +50°C",
        "heating_system": false
    }
}
```

---

## 📊 **4.6 FUEL INVENTORY - User Flow**

### **SOP Step-by-Step**

#### **Step 1: Daily Fuel Tracking**
**Data Fields (Table: fuel_inventory)**:
```sql
vessel_id                  -- FK to vessels table
tank_id                    -- FK to fuel_tanks table
inventory_date             -- Date of measurement
ullage_measurement_cm      -- Ullage depth measurement
calculated_volume_liters   -- Volume from calibration curve
received_volume_liters     -- Fuel received today
consumed_volume_liters     -- Fuel consumed today
rob_volume_liters         -- Remaining on board
fuel_temperature_c        -- Fuel temperature for correction
fuel_density_kg_m3        -- Fuel density measurement
water_content_ppm         -- Water contamination level
equipment_consumption     -- JSON breakdown by equipment
recorded_by               -- Person taking measurement
measurement_time          -- Time of measurement
```

#### **Step 2: Equipment Fuel Consumption Breakdown**
```json
{
    "equipment_consumption": [
        {
            "equipment_id": "GTG-A-001",
            "equipment_name": "Gas Turbine Generator A",
            "consumption_liters": 0.0,
            "running_hours": 0.0,
            "consumption_rate_lph": 0.0,
            "fuel_efficiency": "Standby"
        },
        {
            "equipment_id": "GTG-B-001", 
            "equipment_name": "Gas Turbine Generator B",
            "consumption_liters": 0.0,
            "running_hours": 0.0,
            "consumption_rate_lph": 0.0,
            "fuel_efficiency": "Standby"
        },
        {
            "equipment_id": "EDG-001",
            "equipment_name": "Emergency Diesel Generator",
            "consumption_liters": 1930.0,
            "running_hours": 8.5,
            "consumption_rate_lph": 227.1,
            "fuel_efficiency": "Normal"
        },
        {
            "equipment_id": "HULL-G-01",
            "equipment_name": "Hull Generator #1",
            "consumption_liters": 72.0,
            "running_hours": 2.0,
            "consumption_rate_lph": 36.0,
            "fuel_efficiency": "Normal"
        }
    ],
    "daily_summary": {
        "total_consumption_liters": 2002.0,
        "total_received_liters": 0.0,
        "net_consumption_liters": 2002.0,
        "consumption_efficiency": "Within normal range"
    }
}
```

#### **Step 3: Fuel Balance Calculation**
```sql
-- Auto-calculate fuel balance and ROB
DELIMITER //
CREATE TRIGGER fuel_inventory_calculation
BEFORE INSERT ON fuel_inventory
FOR EACH ROW
BEGIN
    DECLARE tank_capacity DECIMAL(10,2);
    DECLARE previous_rob DECIMAL(10,2) DEFAULT 0;
    
    -- Get tank capacity
    SELECT ft.capacity_liters INTO tank_capacity
    FROM fuel_tanks ft WHERE ft.id = NEW.tank_id;
    
    -- Get previous day ROB
    SELECT rob_volume_liters INTO previous_rob
    FROM fuel_inventory fi
    WHERE fi.tank_id = NEW.tank_id
      AND fi.inventory_date = NEW.inventory_date - INTERVAL 1 DAY
    LIMIT 1;
    
    -- Calculate volume from ullage using calibration curve
    SELECT calculate_volume_from_ullage(NEW.tank_id, NEW.ullage_measurement_cm) 
    INTO NEW.calculated_volume_liters;
    
    -- Calculate ROB with material balance
    SET NEW.rob_volume_liters = GREATEST(0, 
        previous_rob + NEW.received_volume_liters - NEW.consumed_volume_liters
    );
    
    -- Validate calculated vs measured volume
    IF ABS(NEW.calculated_volume_liters - NEW.rob_volume_liters) > (NEW.rob_volume_liters * 0.05) THEN
        INSERT INTO fuel_alerts (vessel_id, tank_id, alert_type, alert_level, message, created_at)
        VALUES (NEW.vessel_id, NEW.tank_id, 'VOLUME_VARIANCE', 'WARNING',
                CONCAT('Fuel volume variance detected: Calculated=', NEW.calculated_volume_liters, 
                       'L, Expected=', NEW.rob_volume_liters, 'L'), NOW());
    END IF;
    
    -- Low fuel level alert
    IF NEW.rob_volume_liters < (tank_capacity * 0.10) THEN
        INSERT INTO fuel_alerts (vessel_id, tank_id, alert_type, alert_level, message, created_at)
        VALUES (NEW.vessel_id, NEW.tank_id, 'LOW_FUEL', 'CRITICAL',
                CONCAT('Low fuel level: ', NEW.rob_volume_liters, 'L (', 
                       ROUND((NEW.rob_volume_liters/tank_capacity)*100, 1), '% capacity)'), NOW());
    END IF;
END//
DELIMITER ;
```

#### **Step 4: Fuel Quality Monitoring**
```sql
-- Weekly fuel quality checks
INSERT INTO fuel_quality_tests (
    vessel_id, tank_id, test_date, test_type,
    test_results, quality_status, performed_by
) VALUES (
    1, -- vessel_id
    2, -- tank_id
    CURDATE(),
    'Weekly_Quality_Check',
    JSON_OBJECT(
        'density_kg_m3', 845.5,
        'water_content_ppm', 125.0,
        'sediment_content_mg_l', 15.0,
        'flash_point_c', 68.0,
        'pour_point_c', -12.0,
        'viscosity_cst', 4.2,
        'sulfur_content_ppm', 850.0
    ),
    CASE 
        WHEN JSON_EXTRACT(test_results, '$.water_content_ppm') > 200 THEN 'FAIL'
        WHEN JSON_EXTRACT(test_results, '$.sediment_content_mg_l') > 25 THEN 'FAIL'
        ELSE 'PASS'
    END,
    'Lab_Analyst_1'
);
```

---

## 📊 **INTEGRATED EQUIPMENT DASHBOARD**

### **Real-time Equipment Status Overview**
```javascript
// Vue.js Equipment Dashboard Component
const EquipmentDashboard = {
    data() {
        return {
            equipmentStatus: [],
            maintenanceActivities: [],
            sparePartsAlerts: [],
            fuelStatus: [],
            criticalAlerts: []
        };
    },
    
    computed: {
        equipmentAvailability() {
            const total = this.equipmentStatus.length;
            const available = this.equipmentStatus.filter(e => 
                e.operational_status === 'Running' || e.operational_status === 'Standby'
            ).length;
            return total > 0 ? (available / total * 100).toFixed(1) : 0;
        },
        
        maintenanceBacklog() {
            return this.maintenanceActivities.filter(m => 
                m.completion_status === 'Scheduled' && 
                new Date(m.activity_date) < new Date()
            ).length;
        },
        
        criticalSparePartsCount() {
            return this.sparePartsAlerts.filter(s => 
                s.alert_level === 'CRITICAL'
            ).length;
        },
        
        totalFuelROB() {
            return this.fuelStatus.reduce((sum, tank) => 
                sum + tank.rob_volume_liters, 0
            );
        }
    },
    
    methods: {
        async refreshData() {
            try {
                const response = await fetch('/api/equipment/dashboard');
                const data = await response.json();
                
                this.equipmentStatus = data.equipment;
                this.maintenanceActivities = data.maintenance;
                this.sparePartsAlerts = data.spare_parts_alerts;
                this.fuelStatus = data.fuel;
                this.criticalAlerts = data.critical_alerts;
                
            } catch (error) {
                console.error('Error fetching equipment data:', error);
            }
        },
        
        formatNumber(value, decimals = 1) {
            return Number(value).toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        }
    },
    
    mounted() {
        this.refreshData();
        // Refresh every 5 minutes
        setInterval(this.refreshData, 300000);
    }
};
```

---

## 📋 **DVR INTEGRATION**

### **Equipment Summary for DVR Generation**
```sql
-- Generate equipment section for DVR
SELECT 
    v.id as vessel_id,
    v.name as vessel_name,
    CURDATE() - INTERVAL 1 DAY as report_date,
    
    -- Equipment availability summary
    JSON_OBJECT(
        'total_equipment', COUNT(DISTINCT e.equipment_tag),
        'running_count', SUM(CASE WHEN es.operational_status = 'Running' THEN 1 ELSE 0 END),
        'standby_count', SUM(CASE WHEN es.operational_status = 'Standby' THEN 1 ELSE 0 END),
        'shutdown_count', SUM(CASE WHEN es.operational_status = 'Shutdown' THEN 1 ELSE 0 END),
        'maintenance_count', SUM(CASE WHEN es.operational_status = 'Maintenance' THEN 1 ELSE 0 END),
        'overall_availability', ROUND(AVG(es.availability_percentage), 1)
    ) as equipment_summary,
    
    -- Critical equipment overrides
    JSON_ARRAYAGG(
        CASE 
            WHEN ea.alert_level = 'CRITICAL' THEN
                JSON_OBJECT(
                    'equipment_tag', e.equipment_tag,
                    'equipment_name', e.equipment_name,
                    'alert_type', ea.alert_type,
                    'message', ea.message,
                    'duration_hours', TIMESTAMPDIFF(HOUR, ea.created_at, NOW())
                )
            ELSE NULL
        END
    ) as equipment_overrides,
    
    -- Maintenance activities completed
    JSON_ARRAYAGG(
        CASE 
            WHEN ma.completion_status = 'Completed' AND DATE(ma.completion_date) = CURDATE() - INTERVAL 1 DAY THEN
                JSON_OBJECT(
                    'work_order_no', ma.work_order_no,
                    'equipment_tag', ma.equipment_id,
                    'activity_type', ma.activity_type,
                    'description', ma.description,
                    'actual_hours', ma.actual_hours,
                    'performed_by', ma.performed_by
                )
            ELSE NULL
        END
    ) as maintenance_completed,
    
    -- Fuel consumption summary
    JSON_OBJECT(
        'total_consumption_liters', COALESCE(SUM(fi.consumed_volume_liters), 0),
        'total_rob_liters', COALESCE(SUM(fi.rob_volume_liters), 0),
        'consumption_by_equipment', (
            SELECT JSON_ARRAYAGG(
                JSON_OBJECT(
                    'equipment_name', JSON_EXTRACT(fi.equipment_consumption, CONCAT('$[', idx, '].equipment_name')),
                    'consumption_liters', JSON_EXTRACT(fi.equipment_consumption, CONCAT('$[', idx, '].consumption_liters'))
                )
            )
            FROM fuel_inventory fi
            CROSS JOIN JSON_TABLE(fi.equipment_consumption, '$[*]' COLUMNS (idx FOR ORDINALITY)) j
            WHERE fi.vessel_id = v.id AND fi.inventory_date = CURDATE() - INTERVAL 1 DAY
        )
    ) as fuel_summary

FROM vessels v
LEFT JOIN equipment e ON v.id = e.vessel_id
LEFT JOIN equipment_status es ON e.equipment_tag = es.equipment_id 
    AND DATE(es.status_datetime) = CURDATE() - INTERVAL 1 DAY
LEFT JOIN equipment_alerts ea ON e.equipment_tag = ea.equipment_id 
    AND DATE(ea.created_at) = CURDATE() - INTERVAL 1 DAY 
    AND ea.acknowledged = FALSE
LEFT JOIN maintenance_activities ma ON e.equipment_tag = ma.equipment_id
    AND DATE(ma.completion_date) = CURDATE() - INTERVAL 1 DAY
LEFT JOIN fuel_inventory fi ON v.id = fi.vessel_id 
    AND fi.inventory_date = CURDATE() - INTERVAL 1 DAY

WHERE v.status = 'Active'
GROUP BY v.id, v.name;
```

---

## 🎯 **SUCCESS METRICS**

### **Equipment & Resources Module KPIs**
- **Equipment Availability**: >95% for critical equipment
- **Maintenance Compliance**: 100% PM completion on schedule
- **Spare Parts Availability**: 99% parts availability when needed
- **Fuel Consumption Accuracy**: ±2% variance in fuel balance
- **Alert Response Time**: <30 minutes for critical equipment alerts

### **Operational Benefits**
✅ **Proactive Maintenance** - Predictive maintenance reduces unplanned downtime  
✅ **Inventory Optimization** - Automated reorder prevents stockouts  
✅ **Fuel Management** - Accurate consumption tracking and forecasting  
✅ **Compliance Assurance** - Complete maintenance documentation for audits  
✅ **Real-time Visibility** - Immediate awareness of equipment status changes