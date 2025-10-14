import { breadcrumbKey } from "element-plus";

export default [

    // Production Operations
    {
        path: '/production',
        meta: {
            title: "production.title",
        },
        children: [
            {
                path: "sales-gas-metering",
                meta: {
                    title: "production.sales_gas_metering.title",
                },
                children: [
                    {
                        path: "",
                        name: "production.sales_gas_metering.index",
                        component: () => import("../../pages/Production/SalesGasMetering/Index.vue"),
                        meta: {
                            title: "production.sales_gas_metering.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "production.sales_gas_metering.create",
                        component: () => import("../../pages/Production/SalesGasMetering/Form.vue"),
                        meta: {
                            title: "production.sales_gas_metering.create",
                        }
                    },
                    {
                        path: ":date",
                        name: "production.sales_gas_metering.hourly",
                        component: () =>
                            import("../../pages/Production/SalesGasMetering/Hourly.vue"),
                        meta: {
                            title: "production.sales_gas_metering.hourly",
                        }
                    },
                    {
                        path: ":date/:id",
                        name: "production.sales_gas_metering.show",
                        component: () =>
                            import("../../pages/Production/SalesGasMetering/Show.vue"),
                        meta: {
                            title: "production.sales_gas_metering.detail",
                        }
                    },
                    {
                        path: ":date/:id/edit",
                        name: "production.sales_gas_metering.edit",
                        component: () => import("../../pages/Production/SalesGasMetering/Form.vue"),
                        meta: {
                            title: "production.sales_gas_metering.edit",
                        }
                    },
                ],
            },
            {
                path: "sales-gas-nomination",
                meta: {
                    title: "production.sales_gas_nomination.title",
                },
                children: [
                    {
                        path: "",
                        name: "production.sales_gas_nomination.index",
                        component: () => import("../../pages/Production/SalesGasNomination/Index.vue"),
                        meta: {
                            title: "production.sales_gas_nomination.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "production.sales_gas_nomination.create",
                        component: () => import("../../pages/Production/SalesGasNomination/Form.vue"),
                        meta: {
                            title: "production.sales_gas_nomination.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "production.sales_gas_nomination.show",
                        component: () =>
                            import("../../pages/Production/SalesGasNomination/Show.vue"),
                        meta: {
                            title: "production.sales_gas_nomination.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "production.sales_gas_nomination.edit",
                        component: () => import("../../pages/Production/SalesGasNomination/Form.vue"),
                        meta: {
                            title: "production.sales_gas_nomination.edit",
                        }
                    },
                ],
            },
            {
                path: "sales-gas-allocation",
                meta: {
                    title: "production.sales_gas_allocation.title",
                },
                children: [
                    {
                        path: "",
                        name: "production.sales_gas_allocation.index",
                        component: () => import("../../pages/Production/SalesGasAllocation/Index.vue"),
                        meta: {
                            title: "production.sales_gas_allocation.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "production.sales_gas_allocation.create",
                        component: () => import("../../pages/Production/SalesGasAllocation/Form.vue"),
                        meta: {
                            title: "production.sales_gas_allocation.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "production.sales_gas_allocation.show",
                        component: () =>
                            import("../../pages/Production/SalesGasAllocation/Show.vue"),
                        meta: {
                            title: "production.sales_gas_allocation.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "production.sales_gas_allocation.edit",
                        component: () => import("../../pages/Production/SalesGasAllocation/Form.vue"),
                        meta: {
                            title: "production.sales_gas_allocation.edit",
                        }
                    },
                ],
            },
            {
                path: "operation",
                meta: {
                    title: "production.operation.title",
                },
                children: [
                    {
                        path: "",
                        name: "production.operation.index",
                        component: () => import("../../pages/Production/Operation/Index.vue"),
                        meta: {
                            title: "production.operation.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "production.operation.create",
                        component: () => import("../../pages/Production/Operation/Form.vue"),
                        meta: {
                            title: "production.operation.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "production.operation.show",
                        component: () =>
                            import("../../pages/Production/Operation/Show.vue"),
                        meta: {
                            title: "production.operation.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "production.operation.edit",
                        component: () => import("../../pages/Production/Operation/Form.vue"),
                        meta: {
                            title: "production.operation.edit",
                        }
                    },
                ],
            },
            {
                path: "daily_production",
                meta: {
                    title: "production.daily_production.title",
                },
                children: [
                    {
                        path: "",
                        name: "production.daily_production.index",
                        component: () => import("../../pages/Production/DailyProduction/Index.vue"),
                        meta: {
                            title: "production.daily_production.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "production.daily_production.create",
                        component: () => import("../../pages/Production/DailyProduction/Form.vue"),
                        meta: {
                            title: "production.daily_production.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "production.daily_production.show",
                        component: () =>
                            import("../../pages/Production/DailyProduction/Show.vue"),
                        meta: {
                            title: "production.daily_production.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "production.daily_production.edit",
                        component: () => import("../../pages/Production/DailyProduction/Form.vue"),
                        meta: {
                            title: "production.daily_production.edit",
                        }
                    },
                ],
            },
        ]
    },
];