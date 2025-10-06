# Menu Table Mapping - DVR Management System

## **📋 APLIKASI OVERVIEW**
**Sistem Manajemen DVR (Daily Vessel Report) - DCR (Daily Contract Report)**  
**Client**: Pakarti Tirto Agung  
**Tech Stack**: Laravel 11 + Vue.js 3 + MySQL 8.0

---

## **🗂️ MENU STRUCTURE & TABLE MAPPING**
**2-Level Menu Structure untuk User Experience yang Optimal**

### **1. 🗃️ MASTER DATA**

| Menu Item | Table | Description |
|-----------|-------|-------------|
| **Vessels** | `vessels` | Kelola Data Vessel MOPU atau FPU |
| **Wells** | `wells` | Kelola Data master Well |
| **Equipment** | `equipment` | Kelola data master equipment |
| **Chemicals** | `chemicals` | Kelola data master chemical |
| **Contracts** | `contracts` | Vessel contracts & PSC |

---

### **2. ⚙️ OPERATIONS**

| Menu Item | Table | Description |
|-----------|-------|-------------|
| **Gas Sales Nomination** | `gas_sales_nomination`, `gas_sales_nomination_buyers` | Kelola data gas nominations |
| **Gas Sales Metering** | `gas_sales_metering`, `gas_sales_metering_flowrates` | Kelola data sales gas metering untuk setiap jam |
| **Gas Sales Allocation** | `gas_sales_allocations` | Buyer-specific allocations |
| **Vessel Operations** | `vessel_operations` | Daily operations log |
| **Daily Production** | `production_daily` | Main daily production report |
| **HSE Operations** | `hse_operations` | Health, Safety, Environment |
| **Marine Operations** | `marine_operations` | Marine conditions & logistics |
| **Chemical Operations** | `chemical_operations` | Chemical consumption tracking |
| **Maintenance** | `maintenance_activities` | Maintenance work orders |
| **Equipment Monitoring** | `equipment_availability`, `equipment_overrides` | Equipment performance & overrides |

---

### **3. 📊 REPORTS**

| Menu Item | Primary Tables | Description | Key Features |
|-----------|----------------|-------------|--------------|
| **DVR Generator** | All production tables | Complete DVR Excel generation | Multi-sheet export, cross-references |
| **Production Analytics** | `production_daily_summary`, `vessel_operations` | Production performance analysis | Daily/monthly/yearly trends, KPIs |
| **Equipment Reports** | `equipment_availability`, `maintenance_activities` | Equipment performance & maintenance | Uptime analysis, maintenance schedules |
| **Operations Dashboard** | Multiple tables | Real-time operational overview | Live KPIs, alerts, performance metrics |

---

### **4. ⚙️ SETTINGS**

| Menu Item | Table | Description | Key Features |
|-----------|-------|-------------|--------------|
| **User Management** | `users`, `user_vessels` | System users administration | User CRUD, vessel assignments |
| **Role & Permissions** | `roles`, `permissions`, `model_has_roles` | Access control management | Role management, permission assignment |
| **System** | `settings` | Application settings | Global configurations, preferences |
| **Activity Logs** | `activity_log` | System audit trail | User activity monitoring, change tracking |

