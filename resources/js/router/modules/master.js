export default [
    {
        path: "/master",
        meta: {
            title: "master.title",
        },
        children: [
            {
                path: "vessels",
                meta: {
                    title: "master.vessels.title",
                    hideBreadcrumb: true
                },
                children: [
                    {
                        path: "",
                        name: "master.vessels.index",
                        component: () => import("../../pages/Master/Vessels/Index.vue"),
                        meta: {
                            title: "master.vessels.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "master.vessels.create",
                        component: () => import("../../pages/Master/Vessels/Form.vue"),
                        meta: {
                            title: "master.vessels.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "master.vessels.show",
                        component: () =>
                            import("../../pages/Master/Vessels/Show.vue"),
                        meta: {
                            title: "master.vessels.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "master.vessels.edit",
                        component: () => import("../../pages/Master/Vessels/Form.vue"),
                        meta: {
                            title: "master.vessels.edit",
                        }
                    },
                ],
            },
            {
                path: "wells",
                meta: {
                    title: "master.wells.title",
                },
                children: [
                    {
                        path: "",
                        name: "master.wells.index",
                        component: () => import("../../pages/Master/Wells/Index.vue"),
                        meta: {
                            title: "master.wells.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "master.wells.create",
                        component: () => import("../../pages/Master/Wells/Form.vue"),
                        meta: {
                            title: "master.wells.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "master.wells.show",
                        component: () =>
                            import("../../pages/Master/Wells/Show.vue"),
                        meta: {
                            title: "master.wells.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "master.wells.edit",
                        component: () => import("../../pages/Master/Wells/Form.vue"),
                        meta: {
                            title: "master.wells.edit",
                        }
                    },
                ],
            },
            {
                path: "equipment",
                meta: {
                    title: "master.equipment.title",
                },
                children: [
                    {
                        path: "",
                        name: "master.equipment.index",
                        component: () => import("../../pages/Master/Equipment/Index.vue"),
                        meta: {
                            title: "master.equipment.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "master.equipment.create",
                        component: () => import("../../pages/Master/Equipment/Form.vue"),
                        meta: {
                            title: "master.equipment.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "master.equipment.show",
                        component: () =>
                            import("../../pages/Master/Equipment/Show.vue"),
                        meta: {
                            title: "master.equipment.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "master.equipment.edit",
                        component: () => import("../../pages/Master/Equipment/Form.vue"),
                        meta: {
                            title: "master.equipment.edit",
                        }
                    },
                ],
            },
            {
                path: "chemicals",
                meta: {
                    title: "master.chemicals.title",
                },
                children: [
                    {
                        path: "",
                        name: "master.chemicals.index",
                        component: () => import("../../pages/Master/Chemicals/Index.vue"),
                        meta: {
                            title: "master.chemicals.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "master.chemicals.create",
                        component: () => import("../../pages/Master/Chemicals/Form.vue"),
                        meta: {
                            title: "master.chemicals.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "master.chemicals.show",
                        component: () =>
                            import("../../pages/Master/Chemicals/Show.vue"),
                        meta: {
                            title: "master.chemicals.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "master.chemicals.edit",
                        component: () => import("../../pages/Master/Chemicals/Form.vue"),
                        meta: {
                            title: "master.chemicals.edit",
                        }
                    },
                ],
            },
            {
                path: "contracts",
                meta: {
                    title: "master.contracts.title",
                },
                children: [
                    {
                        path: "",
                        name: "master.contracts.index",
                        component: () => import("../../pages/Master/Contracts/Index.vue"),
                        meta: {
                            title: "master.contracts.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "master.contracts.create",
                        component: () => import("../../pages/Master/Contracts/Form.vue"),
                        meta: {
                            title: "master.contracts.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "master.contracts.show",
                        component: () =>
                            import("../../pages/Master/Contracts/Show.vue"),
                        meta: {
                            title: "master.contracts.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "master.contracts.edit",
                        component: () => import("../../pages/Master/Contracts/Form.vue"),
                        meta: {
                            title: "master.contracts.edit",
                        }
                    },
                ],
            },
            {
                path: "gas-buyer",
                children: [
                    {
                        path: "",
                        name: "master.gas_buyer.index",
                        component: () => import("../../pages/Master/GasBuyer/Index.vue"),
                        meta: {
                            requiredAuth: false,
                            title: "master.gas_buyer.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: ":id",
                        name: "master.gas_buyer.show",
                        component: () => import("../../pages/Master/GasBuyer/Show.vue"),
                        meta: {
                            requiredAuth: false,
                            title: "master.gas_buyer.detail"
                        }
                    },
                ]
            },
            {
                path: "tank",
                children: [
                    {
                        path: "",
                        component: () => import("../../pages/Master/Tank/Index.vue"),
                        meta: {
                            requiredAuth: false,
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: ":id",
                        component: () => import("../../pages/Master/Tank/Show.vue"),
                        meta: {
                            requiredAuth: false,
                            title: "master.gas_buyer.detail"
                        }
                    },
                ]
            },
        ],
    },
  ];