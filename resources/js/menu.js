/*
 * NEPTUNE DVR & DCR Management System
 * Main navigation array - Transaction-based architecture
 * Optimized for offshore operations workflow
 * Priority-based ordering (1-14) based on operational frequency
 */

export default {main: [
    {
        name: "base.dashboard",
        to: "/dashboard",
        icon: "fluent:home-24-regular",
        priority: 1,
        metadata: {
            description: "base.dashboard_desc",
            frequency: "continuous",
            category: "operational"
        }
    },
    {
        name: "master.title",
        icon: "fluent:database-24-regular",
        subActivePaths: "/master/",
        priority: 2,
        metadata: {
            description: "master.description",
            frequency: "low",
            category: "administrative"
        },
        children: [
            {
                name: "master.contracts.title",
                to: "/master/contracts",
                permission: "contracts.view",
                metadata: {
                    description: "master.contracts_desc"
                }
            },
            {
                name: "master.vessels.title",
                to: "/master/vessels",
                permission: "vessels.view",
                metadata: {
                    description: "master.vessels_desc"
                }
            },
            {
                name: "master.wells.title",
                to: "/master/wells",
                permission: "wells.view",
                metadata: {
                    description: "master.wells_desc"
                }
            },
            {
                name: "master.equipment.title",
                to: "/master/equipment",
                permission: "equipment.view",
                metadata: {
                    description: "master.equipment_desc"
                }
            },
            {
                name: "master.chemicals.title",
                to: "/master/chemicals",
                permission: "chemicals.view",
                metadata: {
                    description: "master.chemicals_desc"
                }
            },
            {
                name: "master.gas_buyer.title",
                to: "/master/gas-buyer",
                permission: "gas_buyer.view",
                metadata: {
                    description: "master.gas_buyer_desc"
                }
            }
        ]
    },
    {
        name: "operations.title",
        icon: "fluent:production-24-regular",
        subActivePaths: "/operations/",
        priority: 3,
        metadata: {
            description: "operations.description",
            frequency: "high",
            category: "operational",
            badge: "urgent"
        },
        children: [
            {
                name: "operations.sales_gas_metering.title",
                to: "/operations/sales-gas-metering",
                permission: "operations.sales_gas_metering.view"
            },
            {
                name: "operations.vessel_operation.title",
                to: "/operations/vessel-operation",
                permission: "operations.vessel_operation.view"
            },
            {
                name: "operations.sales_gas_allocation.title",
                to: "/operations/sales-gas-allocation",
                permission: "operations.sales_gas_allocation.view"  
            },
            {
                name: "operations.daily_production.title",
                to: "/operations/daily-production",
                permission: "operations.daily_production.view"
            },
        ]
    },
    {
        name: "settings.title",
        icon: "fluent:settings-24-regular",
        subActivePaths: "/settings/",
        children: [
            {
                name: "settings.user.title",
                to: "/settings/user",
                permission: "user.view",
            },
            {
                name: "settings.role_permission.title",
                to: "/settings/permission",
                permission: "role_permission.view"
            },
            {
                name: "settings.system.title",
                to: "/settings/system",
                permission: "system.view",
                metadata: {
                    description: "settings.system_desc"
                }
            },
            // {
            //     name: "settings.notifications.title",
            //     to: "/settings/notifications",
            //     permission: "settings.view",
            //     metadata: {
            //         description: "settings.notifications_desc"
            //     }
            // },
            // {
            //     name: "settings.backup.title",
            //     to: "/settings/backup",
            //     permission: "settings.view",
            //     metadata: {
            //         description: "settings.backup_desc"
            //     }
            // }
        ]
    }
],
};