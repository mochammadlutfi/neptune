/*
 * NEPTUNE DVR & DCR Management System
 * Main navigation array - Transaction-based architecture
 * Optimized for offshore operations workflow
 */

export default {main: [
    {
        name: "base.dashboard",
        to: "/dashboard",
        icon: "fluent:home-24-regular"
    },
    {
        name: "production.title",
        icon: "material-symbols:oil-barrel",
        subActivePaths: "/production/",
        children: [
            {
                name: "production.wells.title",
                to: "/production/wells",
                permission: "production.view",
                frequency: "Every 2-4 hours"
            },
            {
                name: "production.sales_gas.title",
                to: "/production/sales-gas",
                permission: "production.view"
            },
            {
                name: "production.compression.title",
                to: "/production/compression",
                permission: "production.view"
            }
        ]
    },
    {
        name: "equipment.title",
        icon: "fluent:settings-24-regular",
        subActivePaths: "/equipment/",
        children: [
            {
                name: "equipment.status.title",
                to: "/equipment/status",
                permission: "equipment.view",
                badge: "live"
            },
            {
                name: "equipment.critical.title",
                to: "/equipment/critical",
                permission: "equipment.view",
                badge: "alerts"
            }
        ]
    },
    {
        name: "hse.title",
        icon: "ic:outline-health-and-safety",
        subActivePaths: "/hse/",
        children: [
            {
                name: "hse.incidents.title",
                to: "/hse/incidents",
                permission: "hse.view",
                urgent: true
            },
            {
                name: "hse.personnel.title",
                to: "/hse/personnel",
                permission: "hse.view",
                frequency: "Daily updates"
            },
            {
                name: "hse.activities.title",
                to: "/hse/activities",
                permission: "hse.view"
            },
            {
                name: "hse.environmental.title",
                to: "/hse/environmental",
                permission: "hse.view"
            },
            {
                name: "hse.statistics.title",
                to: "/hse/statistics",
                permission: "hse.view"
            }
        ]
    },
    {
        name: "maintenance.title",
        icon: "fluent:wrench-screwdriver-24-regular",
        subActivePaths: "/maintenance/",
        children: [
            {
                name: "maintenance.work_orders.title",
                to: "/maintenance/work-orders",
                permission: "maintenance.view"
            },
            {
                name: "maintenance.pdm.title",
                to: "/maintenance/pdm",
                permission: "maintenance.view"
            },
            {
                name: "maintenance.permits.title",
                to: "/maintenance/permits",
                permission: "maintenance.view"
            }
        ]
    },
    {
        name: "marine.title",
        icon: "fluent:vehicle-ship-24-regular",
        subActivePaths: "/marine/",
        children: [
            {
                name: "marine.ballast.title",
                to: "/marine/ballast",
                permission: "marine.view",
                frequency: "Every 4 hours"
            },
            {
                name: "marine.weather.title",
                to: "/marine/weather",
                permission: "marine.view",
                badge: "live"
            },
            {
                name: "marine.stability.title",
                to: "/marine/stability",
                permission: "marine.view"
            },
            {
                name: "marine.mooring.title",
                to: "/marine/mooring",
                permission: "marine.view"
            }
        ]
    },
    {
        name: "laboratory.title",
        icon: "fluent:beaker-24-regular",
        subActivePaths: "/laboratory/",
        children: [
            {
                name: "laboratory.analysis.title",
                to: "/laboratory/analysis",
                permission: "laboratory.view"
            },
            {
                name: "laboratory.chemicals.title",
                to: "/laboratory/chemicals",
                permission: "laboratory.view"
            },
            {
                name: "laboratory.inventory.title",
                to: "/laboratory/inventory",
                permission: "laboratory.view"
            },
            {
                name: "laboratory.consumption.title",
                to: "/laboratory/consumption",
                permission: "laboratory.view"
            }
        ]
    },
    {
        name: "fuel.title",
        icon: "fluent:gas-pump-24-regular",
        subActivePaths: "/fuel/",
        children: [
            {
                name: "fuel.consumption.title",
                to: "/fuel/consumption",
                permission: "fuel.view",
                frequency: "Daily"
            },
            {
                name: "fuel.inventory.title",
                to: "/fuel/inventory",
                permission: "fuel.view"
            },
            {
                name: "fuel.receipts.title",
                to: "/fuel/receipts",
                permission: "fuel.view"
            }
        ]
    },
    {
        name: "flare.title",
        icon: "fluent:fire-24-regular",
        subActivePaths: "/flare/",
        children: [
            {
                name: "flare.events.title",
                to: "/flare/events",
                permission: "flare.view",
                urgent: true
            },
            {
                name: "flare.compliance.title",
                to: "/flare/compliance",
                permission: "flare.view"
            }
        ]
    },
    {
        name: "contract.title",
        icon: "fluent:document-contract-16-regular",
        subActivePaths: "/contract/",
        children: [
            {
                name: "contract.management.title",
                to: "/contract/management",
                permission: "contract.view"
            },
            {
                name: "contract.targets.title",
                to: "/contract/targets",
                permission: "contract.view"
            },
            {
                name: "contract.performance.title",
                to: "/contract/performance",
                permission: "contract.view"
            },
            {
                name: "contract.lifting.title",
                to: "/contract/lifting",
                permission: "contract.view"
            }
        ]
    },
    {
        name: "reports.title",
        icon: "fluent:document-text-24-regular",
        subActivePaths: "/reports/",
        children: [
            {
                name: "reports.dvr.title",
                to: "/reports/dvr",
                permission: "reports.generate",
                frequency: "Daily",
                important: true
            },
            {
                name: "reports.dcr.title",
                to: "/reports/dcr",
                permission: "reports.generate",
                frequency: "Daily",
                important: true
            },
            {
                name: "reports.production.title",
                to: "/reports/production",
                permission: "reports.view"
            },
            {
                name: "reports.equipment.title",
                to: "/reports/equipment",
                permission: "reports.view"
            },
            {
                name: "reports.hse.title",
                to: "/reports/hse",
                permission: "reports.view"
            },
            {
                name: "reports.marine.title",
                to: "/reports/marine",
                permission: "reports.view"
            },
            {
                name: "reports.custom.title",
                to: "/reports/custom",
                permission: "reports.view"
            }
        ]
    },
    {
        name: "analytics.title",
        icon: "fluent:data-area-24-regular",
        subActivePaths: "/analytics/",
        children: [
            {
                name: "analytics.dashboard.title",
                to: "/analytics/dashboard",
                permission: "analytics.view"
            },
            {
                name: "analytics.production.title",
                to: "/analytics/production",
                permission: "analytics.view"
            },
            {
                name: "analytics.equipment.title",
                to: "/analytics/equipment",
                permission: "analytics.view"
            },
            {
                name: "analytics.contract.title",
                to: "/analytics/contract",
                permission: "analytics.view"
            }
        ]
    },
    {
        name: "master.title",
        icon: "fluent:book-database-24-regular",
        subActivePaths: "/master/",
        children: [
            {
                name: "master.vessels.title",
                to: "/master/vessels",
                permission: "vessels.view"
            },
            {
                name: "master.wells.title",
                to: "/master/wells",
                permission: "wells.view"
            },
            {
                name: "master.equipment.title",
                to: "/master/equipment",
                permission: "equipment.view"
            },
            {
                name: "master.contracts.title",
                to: "/master/contracts",
                permission: "contracts.view"
            },
            {
                name: "master.personnel_positions.title",
                to: "/master/personnel-positions",
                permission: "personnel.view"
            },
            {
                name: "master.chemicals.title",
                to: "/master/chemicals",
                permission: "chemicals.view"
            },
            {
                name: "master.fuel_types.title",
                to: "/master/fuel-types",
                permission: "fuel_types.view"
            }
        ]
    },
    {
        name: "settings.title",
        icon: "fluent:settings-cog-multiple-24-regular",
        subActivePaths: "/settings/",
        children: [
            {
                name: "settings.system.title",
                to: "/settings/system",
                permission: "system.view"
            },
            {
                name: "settings.user.title",
                to: "/settings/users",
                permission: "users.view"
            },
            {
                name: "settings.role_permission.title",
                to: "/settings/roles",
                permission: "roles.view"
            },
            {
                name: "settings.audit.title",
                to: "/settings/audit",
                permission: "audit.view"
            },
            {
                name: "settings.backup.title",
                to: "/settings/backup",
                permission: "backup.view"
            }
        ]
    }
],
};