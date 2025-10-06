# DVR-DCR Management System - Index Pages Design

> **Project**: DVR (Daily Vessel Report) - DCR (Daily Contract Report) Management System  
> **Company**: Pakarti Tirtoagung  
> **Design**: User-friendly index dengan maksimal 7 kolom per tabel  
> **Layout**: Optimized untuk lebar 1280px (180px per kolom)

---

## 🎯 **1. DASHBOARD**

### **1.1 Main Dashboard**
**Real-time KPI Cards + Charts - Tidak menggunakan tabel tradisional**

**Components:**
- **Production Overview** (Charts + Cards)
- **Equipment Status Grid** (Visual indicators)
- **HSE Summary** (KPI Cards)
- **Marine Conditions** (Weather widget)
- **Gas Balance** (Balance validation widget)

---

## 🏢 **2. MASTER DATA**

### **2.1 Contracts Management**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Contract Code | `contract_code` | Badge | 120px |
| Contract Name | `contract_name` | Text | 220px |
| Contractor | `contractor_name` | Text | 160px |
| Start Date | `contract_start_date` | Date | 120px |
| End Date | `contract_end_date` | Date | 120px |
| Status | `status` | Badge | 100px |
| Actions | - | Buttons | 100px |

### **2.2 Vessels Management**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Vessel Code | `vessel_code` | Badge | 120px |
| Vessel Name | `vessel_name` | Text | 200px |
| Vessel Type | `vessel_type` | Badge | 120px |
| Contract | `contract_name` | Link | 160px |
| Location | `current_location` | Text | 140px |
| Status | `status` | Badge | 100px |
| Actions | - | Buttons | 100px |

### **2.3 Wells Management**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Well Code | `code` | Badge | 120px |
| Well Name | `name` | Text | 180px |
| Well Type | `type` | Badge | 120px |
| Status | `status` | Badge | 120px |
| Max Oil Rate | `max_oil_rate` | Number | 120px |
| Max Gas Rate | `max_gas_rate` | Number | 120px |
| Actions | - | Buttons | 100px |

### **2.4 Equipment Management**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Equipment Tag | `tag` | Badge | 140px |
| Equipment Name | `name` | Text | 200px |
| Type | `type` | Badge | 120px |
| Critical | `is_critical` | Badge | 100px |
| Manufacturer | `manufacturer` | Text | 140px |
| Status | `status` | Badge | 120px |
| Actions | - | Buttons | 100px |

### **2.5 Gas Buyers Management**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Buyer Code | `code` | Badge | 120px |
| Buyer Name | `name` | Text | 200px |
| Buyer Type | `type` | Badge | 140px |
| Status | `is_active` | Badge | 120px |
| Created Date | `created_at` | Date | 140px |
| Updated Date | `updated_at` | Date | 140px |
| Actions | - | Buttons | 100px |

---

## 🏭 **3. PRODUCTION OPERATIONS**

### **3.1 Well Production**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Well Code | `well_code` | Badge | 120px |
| Production Date | `production_date` | Date | 120px |
| Shift | `shift` | Badge | 100px |
| Oil Rate | `oil_rate_bph` | Number + Unit | 120px |
| Gas Rate | `gas_rate_mscfh` | Number + Unit | 120px |
| Water Rate | `water_rate_bph` | Number + Unit | 120px |
| Recorded By | `recorded_by` | User | 120px |

### **3.2 FPU Operations**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Vessel | `vessel_name` | Badge | 120px |
| Reading Date | `reading_date` | Date | 120px |
| Hour | `reading_hour` | Time | 80px |
| Inlet Pressure | `inlet_pressure_psi` | Number + Unit | 140px |
| Gas Rate | `total_gas_rate_mmscfd` | Number + Unit | 140px |
| Fuel Gas | `fuel_gas_rate_mmscfd` | Number + Unit | 140px |
| Recorded By | `recorded_by` | User | 120px |

### **3.3 Sales Gas Metering**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Buyer | `gas_buyer_name` | Badge | 140px |
| Sales Date | `sales_date` | Date | 120px |
| Actual Delivery | `actual_delivery_mmscf` | Number + Unit | 140px |
| Nomination | `nomination_mmscf` | Number + Unit | 120px |
| Heating Value | `heating_value_btu` | Number + Unit | 140px |
| Export Pressure | `export_pressure_psi` | Number + Unit | 140px |
| Recorded By | `recorded_by` | User | 100px |

### **3.4 Gas Balance View**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Vessel | `vessel_name` | Badge | 120px |
| Date | `reading_date` | Date | 120px |
| Production | `total_production` | Number + Unit | 140px |
| Export | `total_export` | Number + Unit | 140px |
| Fuel Consumption | `fuel_consumption` | Number + Unit | 140px |
| Variance | `balance_variance` | Number + Alert | 140px |
| Status | `status` | Badge | 100px |

### **3.5 Production Summary**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Vessel | `vessel_name` | Badge | 120px |
| Date | `summary_date` | Date | 120px |
| Total Oil | `total_oil_bbl` | Number + Unit | 140px |
| Total Gas | `total_gas_mmscf` | Number + Unit | 140px |
| Equipment Availability | `equipment_availability_pct` | Progress Bar | 160px |
| POB | `total_pob` | Number | 100px |
| Actions | - | Buttons | 100px |

---

## ⚙️ **4. EQUIPMENT & RESOURCES**

### **4.1 Equipment Status**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Equipment Tag | `equipment_tag` | Badge | 140px |
| Equipment Name | `equipment_name` | Text | 180px |
| Reading Time | `reading_time` | DateTime | 160px |
| Status | `operational_status` | Badge + Icon | 120px |
| Shift | `shift` | Badge | 100px |
| Running Hours | `running_hours` | Number + Unit | 120px |
| Recorded By | `recorded_by` | User | 120px |

### **4.2 Maintenance Activities**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Equipment Tag | `equipment_tag` | Badge | 140px |
| Activity Date | `activity_date` | Date | 120px |
| Work Type | `work_type` | Badge | 120px |
| Work Order | `work_order_no` | Link | 120px |
| Work Hours | `work_hours` | Number | 100px |
| Status | `status` | Badge | 120px |
| Completed By | `completed_by` | User | 140px |

### **4.3 Spare Parts Management**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Part Number | `part_number` | Badge | 140px |
| Part Name | `part_name` | Text | 200px |
| Compatibility | `equipment_compatibility` | Tags | 160px |
| Supplier | `supplier_name` | Text | 140px |
| Unit Price | `unit_price` | Currency | 120px |
| Lead Time | `lead_time_days` | Number + Days | 120px |
| Actions | - | Buttons | 100px |

### **4.4 Spare Parts Inventory**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Part Number | `part_number` | Badge | 140px |
| Inventory Date | `inventory_date` | Date | 120px |
| Qty Onboard | `quantity_onboard` | Number + Alert | 120px |
| Min Stock | `min_stock_level` | Number | 100px |
| Reorder Point | `reorder_point` | Alert | 120px |
| Condition | `condition_status` | Badge | 120px |
| Recorded By | `recorded_by` | User | 120px |

### **4.5 Fuel Tank Configuration**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Vessel | `vessel_name` | Badge | 120px |
| Tank Name | `tank_name` | Text | 160px |
| Tank Number | `tank_number` | Badge | 100px |
| Capacity | `capacity_liters` | Number + Unit | 140px |
| Fuel Type | `fuel_type` | Badge | 120px |
| Location | `tank_location` | Text | 140px |
| Actions | - | Buttons | 100px |

### **4.6 Fuel Inventory**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Vessel | `vessel_name` | Badge | 120px |
| Tank | `tank_name` | Text | 120px |
| Date | `inventory_date` | Date | 120px |
| Received | `received_volume_liters` | Number + Unit | 140px |
| Consumed | `consumed_volume_liters` | Number + Unit | 140px |
| ROB | `rob_volume_liters` | Number + Unit | 140px |
| Recorded By | `recorded_by` | User | 100px |

---

## 🌊 **5. MARINE OPERATIONS**

### **5.1 Marine Operations**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Vessel | `vessel_name` | Badge | 120px |
| Operation Time | `operation_datetime` | DateTime | 160px |
| Wind Speed | `wind_speed_knots` | Number + Unit | 120px |
| Wave Height | `wave_height_m` | Number + Unit | 120px |
| Visibility | `visibility_nm` | Number + Unit | 120px |
| Vessel Alongside | `vessel_alongside` | Badge | 140px |
| Recorded By | `recorded_by` | User | 120px |

### **5.2 Fresh Water Operations**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Vessel | `vessel_name` | Badge | 120px |
| Date | `operation_date` | Date | 120px |
| Production | `production_liters` | Number + Unit | 140px |
| Consumption | `consumption_liters` | Number + Unit | 140px |
| ROB | `rob_liters` | Number + Unit | 140px |
| Water Maker Status | `water_maker_status` | Badge | 140px |
| Recorded By | `recorded_by` | User | 100px |

### **5.3 Fresh Water Systems**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Vessel | `vessel_name` | Badge | 120px |
| System Name | `system_name` | Text | 180px |
| System Type | `system_type` | Badge | 120px |
| Capacity | `capacity_lph` | Number + Unit | 140px |
| Status | `status` | Badge | 120px |
| Created Date | `created_at` | Date | 120px |
| Actions | - | Buttons | 100px |

### **5.4 Fresh Water Tanks**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Vessel | `vessel_name` | Badge | 120px |
| Tank Name | `tank_name` | Text | 160px |
| Tank Number | `tank_number` | Badge | 100px |
| Capacity | `capacity_liters` | Number + Unit | 140px |
| Current Level | `current_level_liters` | Number + Unit | 140px |
| Location | `tank_location` | Text | 140px |
| Actions | - | Buttons | 100px |

---

## 🛡️ **6. HSE OPERATIONS**

### **6.1 HSE Operations**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Vessel | `vessel_name` | Badge | 120px |
| Report Date | `report_date` | Date | 120px |
| LTI Count | `lti_count` | Alert Number | 120px |
| Near Miss | `near_miss_count` | Alert Number | 120px |
| Drill Conducted | `drill_conducted` | Badge | 140px |
| Total POB | `pob_total` | Number | 100px |
| Recorded By | `recorded_by` | User | 120px |

---

## 🧪 **7. LABORATORY**

### **7.1 Lab Analysis**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Sample Date | `sample_date` | Date | 120px |
| Sample Time | `sample_time` | Time | 100px |
| Sample Type | `sample_type` | Badge | 120px |
| Sample Point | `sample_point` | Text | 140px |
| Meets Spec | `meets_spec` | Badge | 120px |
| Test Results | `test_results` | JSON Preview | 140px |
| Recorded By | `recorded_by` | User | 120px |

### **7.2 Chemical Operations**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Vessel | `vessel_name` | Badge | 120px |
| Date | `operation_date` | Date | 120px |
| Chemical | `chemical_name` | Text | 160px |
| Operation Type | `operation_type` | Badge | 140px |
| Quantity Used | `quantity_used` | Number + Unit | 140px |
| Injection Rate | `injection_rate` | Number + Unit | 140px |
| Recorded By | `recorded_by` | User | 100px |

---

## 🏭 **8. PROCESS SYSTEMS**

### **8.1 Process Tanks**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Vessel | `vessel_name` | Badge | 120px |
| Tank Name | `tank_name` | Text | 160px |
| Tank Type | `tank_type` | Badge | 120px |
| Capacity | `capacity_bbls` | Number + Unit | 140px |
| Location | `tank_location` | Text | 140px |
| Calibration Curve | `calibration_curve` | Link | 140px |
| Actions | - | Buttons | 100px |

### **8.2 Tank Level Readings**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Tank | `tank_name` | Badge | 140px |
| Reading Date | `reading_date` | Date | 120px |
| Reading Time | `reading_time` | Time | 120px |
| Ullage Level | `ullage_level_cm` | Number + Unit | 140px |
| Volume | `calculated_volume_bbls` | Number + Unit | 140px |
| Fluid Type | `fluid_type` | Badge | 120px |
| Recorded By | `recorded_by` | User | 100px |

---

## 📈 **9. REPORTS & DVR**

### **9.1 DVR Reports**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Vessel | `vessel_name` | Badge | 120px |
| Report Date | `report_date` | Date | 120px |
| Report Number | `report_number` | Link | 140px |
| Status | `status` | Badge | 120px |
| Generated By | `generated_by` | User | 140px |
| Approved By | `approved_by` | User | 140px |
| Actions | - | Buttons | 100px |

### **9.2 DCR Reports**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Contract | `contract_name` | Badge | 140px |
| Report Date | `report_date` | Date | 120px |
| Oil Produced | `oil_produced_bbl` | Number + Unit | 140px |
| Gas Produced | `gas_produced_mmscf` | Number + Unit | 140px |
| Contractor Share | `contractor_share_bbl` | Number + Unit | 140px |
| Government Share | `government_share_bbl` | Number + Unit | 140px |
| Approved | `is_approved` | Badge | 100px |

### **9.3 Activity Log**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Log Name | `log_name` | Badge | 140px |
| Description | `description` | Text (Truncated) | 220px |
| Subject Type | `subject_type` | Badge | 120px |
| User | `causer_name` | User | 140px |
| Event | `event` | Badge | 100px |
| Created Date | `created_at` | DateTime | 160px |
| Actions | - | Buttons | 100px |

---

## ⚙️ **10. SYSTEM SETTINGS**

### **10.1 Users Management**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Name | `name` | Text + Avatar | 180px |
| Email | `email` | Email | 200px |
| Role | `role` | Badge | 120px |
| Position | `position` | Text | 140px |
| Vessel Access | `vessel_access` | Tags | 160px |
| Status | `is_active` | Badge | 100px |
| Actions | - | Buttons | 100px |

### **10.2 Roles Management**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Role Name | `role_name` | Badge | 160px |
| Guard Name | `guard_name` | Text | 120px |
| Permissions | `permission_count` | Number + Link | 140px |
| Users | `user_count` | Number + Link | 120px |
| Status | `is_active` | Badge | 120px |
| Created Date | `created_at` | Date | 140px |
| Actions | - | Buttons | 100px |

### **10.3 System Settings**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| Key | `key` | Code | 180px |
| Value | `value` | Text (Truncated) | 200px |
| Category | `category` | Badge | 140px |
| Description | `description` | Text (Truncated) | 180px |
| Editable | `is_editable` | Badge | 120px |
| Updated Date | `updated_at` | Date | 160px |
| Actions | - | Buttons | 100px |

### **10.4 Session Management**
| **Column** | **Field** | **Type** | **Width** |
|------------|-----------|----------|-----------|
| User | `user_name` | User + Avatar | 160px |
| IP Address | `ip_address` | Code | 140px |
| Device Type | `device_type` | Badge | 120px |
| Last Activity | `last_activity` | DateTime | 160px |
| Token Name | `token_name` | Text | 140px |
| Expires | `expires_at` | DateTime | 160px |
| Actions | - | Buttons | 100px |

---

## 🎯 **Design Guidelines**

### **Column Type Standards:**
- **Badge**: Colored status indicators, codes, types
- **Progress Bar**: Percentage values, availability metrics
- **Alert**: Critical values with warning indicators
- **Link**: Clickable references to other records
- **User**: User name with avatar/initial
- **Number + Unit**: Numeric values with measurement units
- **DateTime**: Full date and time display
- **Date**: Date only display
- **Time**: Time only display
- **Tags**: Multiple values as tag chips
- **Currency**: Monetary values with currency symbol

### **Responsive Behavior:**
- **Desktop (1280px+)**: All 7 columns visible
- **Tablet (768px-1279px)**: Hide 2 least important columns
- **Mobile (<768px)**: Show only 3 most important columns + Actions

### **Performance Optimizations:**
- **Pagination**: 25 records per page default
- **Search**: Real-time search on primary columns
- **Filters**: Quick filters for status, date range, vessel
- **Sorting**: Sortable on all columns except Actions
- **Export**: CSV/Excel export functionality