export default [

    // Equipment Management
    {
        path: '/equipment',
        meta: {
            hideBreadcrumb: true
        },
        children: [
            // Equipment Status
            {
                path: "status",
                meta: {
                    title: "equipment.status.title",
                },
                children: [
                    {
                        path: "",
                        name: "equipment.status.index",
                        component: () => import("../../pages/Equipment/Status/Index.vue"),
                        meta: {
                            title: "equipment.status.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "equipment.status.create",
                        component: () => import("../../pages/Equipment/Status/Form.vue"),
                        meta: {
                            title: "equipment.status.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "equipment.status.show",
                        component: () =>
                            import("../../pages/Equipment/Status/Show.vue"),
                        meta: {
                            title: "equipment.status.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "equipment.status.edit",
                        component: () => import("../../pages/Equipment/Status/Form.vue"),
                        meta: {
                            title: "equipment.status.edit",
                        }
                    },
                ],
            },
            // Equipment Maintenance
            {
                path: "maintenance",
                meta: {
                    title: "equipment.maintenance.title",
                },
                children: [
                    {
                        path: "",
                        name: "equipment.maintenance.index",
                        component: () => import("../../pages/Equipment/Maintenance/Index.vue"),
                        meta: {
                            title: "equipment.maintenance.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "equipment.maintenance.create",
                        component: () => import("../../pages/Equipment/Maintenance/Form.vue"),
                        meta: {
                            title: "equipment.maintenance.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "equipment.maintenance.show",
                        component: () =>
                            import("../../pages/Equipment/Maintenance/Show.vue"),
                        meta: {
                            title: "equipment.maintenance.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "equipment.maintenance.edit",
                        component: () => import("../../pages/Equipment/Maintenance/Form.vue"),
                        meta: {
                            title: "equipment.maintenance.edit",
                        }
                    },
                ],
            },

            // Sparepart
            {
                path: "sparepart",
                meta: {
                    title: "equipment.sparepart.title",
                },
                children: [
                    {
                        path: "",
                        name: "equipment.sparepart.index",
                        component: () => import("../../pages/Equipment/SpareParts/Index.vue"),
                        meta: {
                            title: "equipment.sparepart.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "equipment.sparepart.create",
                        component: () => import("../../pages/Equipment/SpareParts/Form.vue"),
                        meta: {
                            title: "equipment.sparepart.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "equipment.sparepart.show",
                        component: () =>
                            import("../../pages/Equipment/SpareParts/Show.vue"),
                        meta: {
                            title: "equipment.sparepart.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "equipment.sparepart.edit",
                        component: () => import("../../pages/Equipment/SpareParts/Form.vue"),
                        meta: {
                            title: "equipment.sparepart.edit",
                        }
                    },
                ],
            },

            // Sparepartt Inventory
            
            {
                path: "sparepart-inventory",
                meta: {
                    title: "equipment.sparepart_inventory.title",
                },
                children: [
                    {
                        path: "",
                        name: "equipment.sparepart_inventory.index",
                        component: () => import("../../pages/Equipment/SparePartsInventory/Index.vue"),
                        meta: {
                            title: "equipment.sparepart_inventory.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "equipment.sparepart_inventory.create",
                        component: () => import("../../pages/Equipment/SparePartsInventory/Form.vue"),
                        meta: {
                            title: "equipment.sparepart_inventory.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "equipment.sparepart_inventory.show",
                        component: () =>
                            import("../../pages/Equipment/SparePartsInventory/Show.vue"),
                        meta: {
                            title: "equipment.sparepart_inventory.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "equipment.sparepart_inventory.edit",
                        component: () => import("../../pages/Equipment/SparePartsInventory/Form.vue"),
                        meta: {
                            title: "equipment.sparepart_inventory.edit",
                        }
                    },
                ],
            },


            // Fuel Tanks
            {
                path: "fuel-tanks",
                meta: {
                    title: "equipment.fuel_tanks.title",
                },
                children: [
                    {
                        path: "",
                        name: "equipment.fuel_tanks.index",
                        component: () => import("../../pages/Equipment/FuelTanks/Index.vue"),
                        meta: {
                            title: "equipment.fuel_tanks.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "equipment.fuel_tanks.create",
                        component: () => import("../../pages/Equipment/FuelTanks/Form.vue"),
                        meta: {
                            title: "equipment.fuel_tanks.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "equipment.fuel_tanks.show",
                        component: () =>
                            import("../../pages/Equipment/FuelTanks/Show.vue"),
                        meta: {
                            title: "equipment.fuel_tanks.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "equipment.fuel_tanks.edit",
                        component: () => import("../../pages/Equipment/FuelTanks/Form.vue"),
                        meta: {
                            title: "equipment.fuel_tanks.edit",
                        }
                    },
                ],
            },

            // Fuel Inventory
            {
                path: "fuel-inventory",
                meta: {
                    title: "equipment.fuel_inventory.title",
                },
                children: [
                    {
                        path: "",
                        name: "equipment.fuel_inventory.index",
                        component: () => import("../../pages/Equipment/FuelInventory/Index.vue"),
                        meta: {
                            title: "equipment.fuel_inventory.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "equipment.fuel_inventory.create",
                        component: () => import("../../pages/Equipment/FuelInventory/Form.vue"),
                        meta: {
                            title: "equipment.fuel_inventory.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "equipment.fuel_inventory.show",
                        component: () =>
                            import("../../pages/Equipment/FuelInventory/Show.vue"),
                        meta: {
                            title: "equipment.fuel_inventory.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "equipment.fuel_inventory.edit",
                        component: () => import("../../pages/Equipment/FuelInventory/Form.vue"),
                        meta: {
                            title: "equipment.fuel_inventory.edit",
                        }
                    },
                ],
            },

            {
                path: "summary",
                name: "equipment.summary.index",
                component: () => import("../../pages/Equipment/Summary.vue"),
                meta: {
                    title: "equipment.summary.title",
                    hideBreadcrumb: true
                }
            }
        ]
    },
];