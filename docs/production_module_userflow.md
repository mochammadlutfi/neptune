# PRODUCTION MODULE - User Flow & SOP
**DVR-DCR Management System - Pakarti Tirtoagung**

---

## 📊 **MODULE OVERVIEW**

**Objective**: Mengelola data produksi harian well dan FPU operations untuk menghasilkan production summary yang akurat dalam DVR.

**Key Tables**: 
- `well_production` - Data produksi per well
- `fpu_operations` - Data operasi FPU 2-hourly
- `gas_sales` - Data penjualan gas ke buyer
- `daily_summary` - Summary produksi harian (auto-generated)

**User Roles**: Production Operator, Senior Production Technician, Production Supervisor

---

## 🗂️ **MENU STRUCTURE**

```
📊 PRODUCTION
├── 3.1 Well Production          → well_production
├── 3.2 FPU Operations          → fpu_operations
├── 3.3 Sales Gas Metering      → gas_sales
├── 3.4 Gas Balance             → v_gas_balance (view)
└── 3.5 Production Summary      → daily_summary
```

---

## 🔄 **USER FLOW OVERVIEW**

### **Daily Production Cycle**
1. **Well Production Entry** (Every 2-4 hours) → Individual well readings
2. **FPU Operations Entry** (Every 2 hours) → Process facility data
3. **Gas Sales Recording** (Daily) → Export gas allocation
4. **Gas Balance Validation** (End of day) → Production vs Export reconciliation
5. **Production Summary** (Auto-generated) → Daily totals compilation

---

## 📋 **3.1 WELL PRODUCTION - User Flow**

### **Access Control**
- **Primary Users**: Production Operator, Senior Production Technician
- **View Access**: Production Supervisor, OIM
- **Edit Restrictions**: Cannot modify data older than 24 hours without supervisor approval

### **SOP Step-by-Step**

#### **Step 1: Daily Login & Well Selection**
```
1. Login → Navigate to Production → Well Production
2. Select Vessel from dropdown (MOPU/FPU filter)
3. View well list filtered by selected vessel
4. Select specific well for data entry
```

#### **Step 2: Production Data Entry**
**Frequency Options (Configurable per Well)**:
- **Standard Operations**: Per Shift (Day: 06:00-18:00, Night: 18:00-06:00)
- **High Monitoring**: Every 2-4 hours (06:00, 10:00, 14:00, 18:00, 22:00, 02:00)  
- **Testing/Critical**: Hourly monitoring as required
- **Stable Wells**: Daily readings (06:00 only)

**Data Fields (Table: well_production)**:
```sql
-- Primary production parameters
well_id                 -- FK to wells table
reading_datetime         -- Exact time of reading
oil_rate_bph            -- Oil production rate (barrels per hour)
gas_rate_mscfh          -- Gas production rate (thousand standard cubic feet per hour)
water_rate_bph          -- Water production rate (barrels per hour)
wellhead_pressure_psi   -- Wellhead pressure (pounds per square inch)
wellhead_temp_f         -- Wellhead temperature (Fahrenheit)
choke_size_64th         -- Choke size in 64th of inch
test_separator          -- Test separator used (A/B/C)
recorded_by             -- Operator name/ID
```

#### **Step 3: Data Validation Rules**
```javascript
// Real-time validation
const wellProductionValidation = {
    oil_rate_bph: {
        min: 0,
        max: (well) => well.design_oil_rate * 1.2, // 120% of design rate
        warning: (well) => well.design_oil_rate * 0.9 // Below 90% design
    },
    gas_rate_mscfh: {
        min: 0,
        max: (well) => well.design_gas_rate * 1.2,
        gor_check: true // Gas-Oil Ratio consistency
    },
    wellhead_pressure_psi: {
        min: 50,
        max: 2000,
        alarm: (well) => well.design_pressure * 0.7 // Below 70% design pressure
    },
    water_cut_calculation: {
        formula: "water_rate_bph / (oil_rate_bph + water_rate_bph) * 100",
        warning_threshold: 50, // >50% water cut
        alarm_threshold: 80    // >80% water cut
    }
};
```

#### **Step 4: Auto-Calculations**
```sql
-- Trigger auto-calculations on data insert/update
DELIMITER //
CREATE TRIGGER well_production_calculations
BEFORE INSERT ON well_production
FOR EACH ROW
BEGIN
    -- Calculate total liquid rate
    SET NEW.total_liquid_bph = NEW.oil_rate_bph + NEW.water_rate_bph;
    
    -- Calculate water cut percentage
    SET NEW.water_cut_pct = CASE 
        WHEN NEW.total_liquid_bph > 0 
        THEN (NEW.water_rate_bph / NEW.total_liquid_bph) * 100 
        ELSE 0 
    END;
    
    -- Calculate Gas-Oil Ratio (GOR)
    SET NEW.gor_scf_bbl = CASE 
        WHEN NEW.oil_rate_bph > 0 
        THEN (NEW.gas_rate_mscfh * 1000) / NEW.oil_rate_bph 
        ELSE 0 
    END;
    
    -- Set data quality flag
    SET NEW.data_quality = CASE
        WHEN NEW.oil_rate_bph > 0 AND NEW.wellhead_pressure_psi > 100 THEN 'Good'
        WHEN NEW.oil_rate_bph = 0 AND NEW.wellhead_pressure_psi < 100 THEN 'Well_Shut_In'
        ELSE 'Questionable'
    END;
END//
DELIMITER ;
```

#### **Step 5: Alert Generation**
```sql
-- Generate alerts for abnormal conditions
INSERT INTO production_alerts (well_id, alert_type, alert_level, message, created_at)
SELECT 
    NEW.well_id,
    'Production_Decline',
    'WARNING',
    CONCAT('Oil rate dropped to ', NEW.oil_rate_bph, ' bph (', 
           ROUND(((w.design_oil_rate - NEW.oil_rate_bph) / w.design_oil_rate) * 100, 1), 
           '% below design)'),
    NOW()
FROM wells w 
WHERE w.id = NEW.well_id 
  AND NEW.oil_rate_bph < (w.design_oil_rate * 0.8)
  AND NEW.oil_rate_bph > 0;
```

---

## 🏭 **3.2 FPU OPERATIONS - User Flow**

### **Access Control**
- **Primary Users**: FPU Operator, Process Technician
- **Frequency**: Every 2 hours (24/7 operation)

### **SOP Step-by-Step**

#### **Step 1: 2-Hourly Data Entry Schedule**
```
Reading Times: 00:00, 02:00, 04:00, 06:00, 08:00, 10:00, 
               12:00, 14:00, 16:00, 18:00, 20:00, 22:00
```

#### **Step 2: FPU Process Parameters**
**Data Fields (Table: fpu_operations)**:
```sql
vessel_id                    -- FK to vessels table  
reading_date                 -- Date of reading
reading_hour                 -- Hour of reading (0-23)
inlet_pressure_psi          -- Inlet manifold pressure
inlet_temperature_f         -- Inlet temperature
separator_pressure_psi      -- Separator operating pressure
total_gas_rate_mmscfd       -- Total gas production (million standard cubic feet per day)
export_gas_rate_mmscfd      -- Export gas rate
fuel_gas_rate_mmscfd        -- Fuel gas consumption
flare_gas_rate_mmscfd       -- Flare gas rate
oil_production_bpd          -- Oil production (barrels per day)
water_production_bpd        -- Water production
process_data                -- JSON field for additional parameters
recorded_by                 -- Operator name/ID
```

#### **Step 3: Process Data JSON Structure**
```json
{
    "separator_levels": {
        "hp_separator_level": 45.5,
        "lp_separator_level": 38.2,
        "test_separator_level": 42.1
    },
    "compressor_data": {
        "suction_pressure": 125.5,
        "discharge_pressure": 850.2,
        "compression_ratio": 6.8,
        "efficiency_percent": 78.5
    },
    "heat_exchanger": {
        "inlet_temp": 185.5,
        "outlet_temp": 95.2,
        "duty_mmbtu_hr": 12.5
    },
    "utilities": {
        "instrument_air_pressure": 85.0,
        "cooling_water_flow": 450.0,
        "power_consumption_kw": 2850
    }
}
```

#### **Step 4: Material Balance Validation**
```sql
-- Automatic material balance check
CREATE TRIGGER fpu_material_balance_check
AFTER INSERT ON fpu_operations
FOR EACH ROW
BEGIN
    DECLARE total_input DECIMAL(10,2);
    DECLARE total_output DECIMAL(10,2);
    DECLARE balance_variance DECIMAL(10,2);
    
    -- Calculate input (from wells)
    SELECT SUM(wp.gas_rate_mscfh * 24 / 1000) INTO total_input
    FROM well_production wp
    INNER JOIN wells w ON wp.well_id = w.id
    WHERE w.vessel_id = NEW.vessel_id
      AND DATE(wp.reading_datetime) = NEW.reading_date
      AND HOUR(wp.reading_datetime) BETWEEN NEW.reading_hour-1 AND NEW.reading_hour+1;
    
    -- Calculate output
    SET total_output = NEW.export_gas_rate_mmscfd + NEW.fuel_gas_rate_mmscfd + NEW.flare_gas_rate_mmscfd;
    
    -- Calculate variance
    SET balance_variance = ABS(total_input - total_output);
    
    -- Insert alert if variance > 5%
    IF balance_variance > (total_input * 0.05) THEN
        INSERT INTO production_alerts (vessel_id, alert_type, alert_level, message, created_at)
        VALUES (NEW.vessel_id, 'Material_Balance', 'WARNING', 
                CONCAT('Gas balance variance: ', ROUND(balance_variance, 2), ' MMSCFD'), NOW());
    END IF;
END;
```

---

## ⛽ **3.3 SALES GAS METERING - User Flow**

### **SOP Step-by-Step**

#### **Step 1: Daily Gas Sales Entry**
**Data Fields (Table: gas_sales)**:
```sql
buyer_id                    -- FK to gas_buyers table
vessel_id                   -- FK to vessels table
sales_date                  -- Date of gas delivery
nomination_mmscf           -- Nominated delivery volume
actual_delivery_mmscf      -- Actual delivered volume
meter_reading_start        -- Starting meter reading
meter_reading_end          -- Ending meter reading
heating_value_btu          -- Heating value (BTU per standard cubic foot)
specific_gravity           -- Gas specific gravity
export_pressure_psi        -- Export line pressure
export_temperature_f       -- Export line temperature
quality_parameters         -- JSON field for gas quality
recorded_by                -- Operator name/ID
```

#### **Step 2: Gas Quality Parameters**
```json
{
    "composition": {
        "methane_percent": 85.5,
        "ethane_percent": 8.2,
        "propane_percent": 3.1,
        "co2_percent": 1.8,
        "n2_percent": 1.4
    },
    "quality_specs": {
        "water_content_ppm": 5.2,
        "h2s_content_ppm": 0.1,
        "total_sulfur_ppm": 2.5
    },
    "contract_compliance": {
        "heating_value_min": 980,
        "heating_value_actual": 1025,
        "spec_compliance": true
    }
}
```

#### **Step 3: Revenue Calculation**
```sql
-- Auto-calculate revenue based on gas sales
UPDATE gas_sales gs
INNER JOIN gas_buyers gb ON gs.buyer_id = gb.id
SET gs.revenue_calculation = JSON_OBJECT(
    'base_price_mscf', gb.base_price_mscf,
    'heating_value_adjustment', 
        CASE 
            WHEN gs.heating_value_btu > gb.base_heating_value 
            THEN (gs.heating_value_btu - gb.base_heating_value) * gb.btu_adjustment_factor
            ELSE 0
        END,
    'total_revenue', 
        gs.actual_delivery_mmscf * 1000 * 
        (gb.base_price_mscf + 
         CASE 
             WHEN gs.heating_value_btu > gb.base_heating_value 
             THEN (gs.heating_value_btu - gb.base_heating_value) * gb.btu_adjustment_factor
             ELSE 0
         END)
)
WHERE gs.id = NEW.id;
```

---

## ⚖️ **3.4 GAS BALANCE - User Flow**

### **Automated Balance Calculation (View: v_gas_balance)**
```sql
CREATE OR REPLACE VIEW v_gas_balance AS
SELECT 
    fo.vessel_id,
    fo.reading_date,
    
    -- Production Input
    SUM(fo.total_gas_rate_mmscfd) as total_production_mmscfd,
    
    -- Consumption/Export Output  
    SUM(fo.export_gas_rate_mmscfd) as total_export_mmscfd,
    SUM(fo.fuel_gas_rate_mmscfd) as total_fuel_mmscfd,
    SUM(fo.flare_gas_rate_mmscfd) as total_flare_mmscfd,
    
    -- Calculated Balance
    SUM(fo.export_gas_rate_mmscfd + fo.fuel_gas_rate_mmscfd + fo.flare_gas_rate_mmscfd) as total_output_mmscfd,
    
    -- Balance Variance
    SUM(fo.total_gas_rate_mmscfd) - 
    SUM(fo.export_gas_rate_mmscfd + fo.fuel_gas_rate_mmscfd + fo.flare_gas_rate_mmscfd) as balance_variance_mmscfd,
    
    -- Percentage Variance
    ((SUM(fo.total_gas_rate_mmscfd) - 
      SUM(fo.export_gas_rate_mmscfd + fo.fuel_gas_rate_mmscfd + fo.flare_gas_rate_mmscfd)) / 
     SUM(fo.total_gas_rate_mmscfd)) * 100 as variance_percentage,
     
    -- Balance Status
    CASE 
        WHEN ABS(((SUM(fo.total_gas_rate_mmscfd) - 
                   SUM(fo.export_gas_rate_mmscfd + fo.fuel_gas_rate_mmscfd + fo.flare_gas_rate_mmscfd)) / 
                  SUM(fo.total_gas_rate_mmscfd)) * 100) <= 2 THEN 'BALANCED'
        WHEN ABS(((SUM(fo.total_gas_rate_mmscfd) - 
                   SUM(fo.export_gas_rate_mmscfd + fo.fuel_gas_rate_mmscfd + fo.flare_gas_rate_mmscfd)) / 
                  SUM(fo.total_gas_rate_mmscfd)) * 100) <= 5 THEN 'ACCEPTABLE'
        ELSE 'UNBALANCED'
    END as balance_status

FROM fpu_operations fo
WHERE fo.reading_date >= CURDATE() - INTERVAL 30 DAY
GROUP BY fo.vessel_id, fo.reading_date
ORDER BY fo.vessel_id, fo.reading_date DESC;
```

### **Daily Balance Reconciliation SOP**
```
1. Review daily gas balance report (end of each day)
2. Investigate variances > 5%
3. Cross-check with meter readings
4. Validate well production data
5. Document discrepancies and corrective actions
6. Approve balance for DVR inclusion
```

---

## 📈 **3.5 PRODUCTION SUMMARY - User Flow**

### **Automated Daily Summary Generation**
```sql
-- Procedure to generate daily production summary
DELIMITER //
CREATE PROCEDURE generate_daily_production_summary(IN summary_date DATE, IN vessel_id INT)
BEGIN
    DECLARE total_oil_bbl DECIMAL(10,2) DEFAULT 0;
    DECLARE total_gas_mmscf DECIMAL(10,2) DEFAULT 0;
    DECLARE total_water_bbl DECIMAL(10,2) DEFAULT 0;
    DECLARE avg_water_cut DECIMAL(5,2) DEFAULT 0;
    DECLARE well_count INT DEFAULT 0;
    
    -- Calculate oil production (sum of 24-hour readings)
    SELECT SUM(oil_rate_bph * 24), COUNT(DISTINCT well_id)
    INTO total_oil_bbl, well_count
    FROM well_production wp
    INNER JOIN wells w ON wp.well_id = w.id
    WHERE w.vessel_id = vessel_id
      AND DATE(wp.reading_datetime) = summary_date;
    
    -- Calculate gas production from FPU operations
    SELECT SUM(total_gas_rate_mmscfd)
    INTO total_gas_mmscf
    FROM fpu_operations fo
    WHERE fo.vessel_id = vessel_id
      AND fo.reading_date = summary_date;
    
    -- Calculate water production
    SELECT SUM(water_rate_bph * 24), AVG(water_cut_pct)
    INTO total_water_bbl, avg_water_cut
    FROM well_production wp
    INNER JOIN wells w ON wp.well_id = w.id
    WHERE w.vessel_id = vessel_id
      AND DATE(wp.reading_datetime) = summary_date;
    
    -- Insert/Update daily summary
    INSERT INTO daily_summary (
        vessel_id, summary_date, total_oil_bbl, total_gas_mmscf, 
        total_water_bbl, water_cut_pct, active_wells, created_at
    ) VALUES (
        vessel_id, summary_date, total_oil_bbl, total_gas_mmscf,
        total_water_bbl, avg_water_cut, well_count, NOW()
    )
    ON DUPLICATE KEY UPDATE
        total_oil_bbl = VALUES(total_oil_bbl),
        total_gas_mmscf = VALUES(total_gas_mmscf),
        total_water_bbl = VALUES(total_water_bbl),
        water_cut_pct = VALUES(water_cut_pct),
        active_wells = VALUES(active_wells),
        updated_at = NOW();
        
END//
DELIMITER ;
```

### **Scheduled Execution**
```sql
-- Create event to auto-generate daily summaries
CREATE EVENT daily_production_summary_event
ON SCHEDULE EVERY 1 DAY
STARTS TIMESTAMP(CURRENT_DATE + INTERVAL 1 DAY, '01:00:00')
DO
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_id INT;
    DECLARE vessel_cursor CURSOR FOR 
        SELECT id FROM vessels WHERE status = 'Active';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN vessel_cursor;
    
    vessel_loop: LOOP
        FETCH vessel_cursor INTO v_id;
        IF done THEN
            LEAVE vessel_loop;
        END IF;
        
        CALL generate_daily_production_summary(CURDATE() - INTERVAL 1 DAY, v_id);
    END LOOP;
    
    CLOSE vessel_cursor;
END;
```

---

## 🚨 **ALERTS & VALIDATION SYSTEM**

### **Production Alerts Configuration**
```sql
CREATE TABLE production_alerts (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    vessel_id INT,
    well_id INT NULL,
    alert_type VARCHAR(50),
    alert_level ENUM('INFO', 'WARNING', 'CRITICAL'),
    message TEXT,
    parameters JSON,
    acknowledged BOOLEAN DEFAULT FALSE,
    acknowledged_by VARCHAR(100) NULL,
    acknowledged_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_vessel_date (vessel_id, created_at),
    INDEX idx_alert_level (alert_level, acknowledged)
);
```

### **Real-time Validation Dashboard**
```javascript
// Vue.js component for production monitoring
const ProductionMonitor = {
    data() {
        return {
            wellProduction: [],
            fpuOperations: [],
            gasBalance: [],
            alerts: [],
            lastUpdate: null
        };
    },
    
    computed: {
        totalDailyOil() {
            return this.wellProduction.reduce((sum, well) => sum + (well.oil_rate_bph * 24), 0);
        },
        
        totalDailyGas() {
            return this.fpuOperations.reduce((sum, fpu) => sum + fpu.total_gas_rate_mmscfd, 0);
        },
        
        activeAlerts() {
            return this.alerts.filter(alert => !alert.acknowledged);
        },
        
        balanceStatus() {
            const latest = this.gasBalance[0];
            return latest ? latest.balance_status : 'UNKNOWN';
        }
    },
    
    methods: {
        async refreshData() {
            // Fetch latest production data every 2 minutes
            const response = await fetch('/api/production/realtime');
            const data = await response.json();
            
            this.wellProduction = data.wells;
            this.fpuOperations = data.fpu;
            this.gasBalance = data.balance;
            this.alerts = data.alerts;
            this.lastUpdate = new Date();
        }
    },
    
    mounted() {
        this.refreshData();
        setInterval(this.refreshData, 120000); // Refresh every 2 minutes
    }
};
```

---

## 📊 **DVR INTEGRATION**

### **Production Summary for DVR**
```sql
-- Generate production section for DVR
SELECT 
    ds.vessel_id,
    v.name as vessel_name,
    ds.summary_date,
    ds.total_oil_bbl,
    ds.total_gas_mmscf,
    ds.total_water_bbl,
    ds.water_cut_pct,
    ds.active_wells,
    
    -- Well performance details
    JSON_ARRAYAGG(
        JSON_OBJECT(
            'well_name', w.well_name,
            'oil_bbl', ROUND(wp_summary.daily_oil, 1),
            'gas_mscf', ROUND(wp_summary.daily_gas, 1),
            'water_cut', ROUND(wp_summary.avg_water_cut, 1),
            'status', wp_summary.well_status
        )
    ) as well_details,
    
    -- Gas balance summary
    gb.balance_status,
    gb.variance_percentage,
    
    -- Production efficiency
    ROUND((ds.total_oil_bbl / 
           (SELECT SUM(design_oil_rate * 24) FROM wells WHERE vessel_id = ds.vessel_id AND status = 'Active')
          ) * 100, 1) as oil_efficiency_pct,
          
    ROUND((ds.total_gas_mmscf / 
           (SELECT SUM(design_gas_rate * 24 / 1000) FROM wells WHERE vessel_id = ds.vessel_id AND status = 'Active')
          ) * 100, 1) as gas_efficiency_pct

FROM daily_summary ds
INNER JOIN vessels v ON ds.vessel_id = v.id
LEFT JOIN v_gas_balance gb ON ds.vessel_id = gb.vessel_id AND ds.summary_date = gb.reading_date
LEFT JOIN (
    SELECT 
        w.vessel_id,
        w.id as well_id,
        w.well_name,
        SUM(wp.oil_rate_bph * 24) as daily_oil,
        SUM(wp.gas_rate_mscfh * 24 / 1000) as daily_gas,
        AVG(wp.water_cut_pct) as avg_water_cut,
        CASE 
            WHEN AVG(wp.oil_rate_bph) > 0 THEN 'PRODUCING'
            WHEN AVG(wp.wellhead_pressure_psi) < 100 THEN 'SHUT_IN'
            ELSE 'STANDBY'
        END as well_status
    FROM wells w
    LEFT JOIN well_production wp ON w.id = wp.well_id 
        AND DATE(wp.reading_datetime) = CURDATE() - INTERVAL 1 DAY
    GROUP BY w.id
) wp_summary ON ds.vessel_id = wp_summary.vessel_id

WHERE ds.summary_date = CURDATE() - INTERVAL 1 DAY
GROUP BY ds.vessel_id;
```

---

## 🎯 **SUCCESS METRICS**

### **Production Module KPIs**
- **Data Completeness**: 100% well readings per scheduled intervals
- **Balance Accuracy**: <2% gas balance variance daily
- **Alert Response**: <15 minutes for critical production alerts
- **Production Efficiency**: >85% of design rates for active wells
- **Data Quality**: 99% accuracy in production measurements

### **Operational Benefits**
✅ **Real-time Production Monitoring** - Immediate visibility of well performance  
✅ **Material Balance Control** - Continuous gas balance validation  
✅ **Automated Summary Generation** - Eliminate manual daily calculations  
✅ **Early Problem Detection** - Proactive alerts for production issues  
✅ **Accurate DVR Data** - Reliable production data for regulatory reporting