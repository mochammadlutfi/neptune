export default [

    // Production Operations
    {
        path: '/operations',
        meta: {
            hideBreadcrumb: true
        },
        children: [
            {
                path: "sales-gas",
                meta: {
                    title: "production.sales_gas_metering.title",
                },
                children: [
                    {
                        path: "",
                        name: "production.sales_gas_metering.index",
                        component: () => import("../../pages/Production/SalesGas/Index.vue"),
                        meta: {
                            title: "production.sales_gas_metering.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "production.sales_gas_metering.create",
                        component: () => import("../../pages/Production/SalesGas/Form.vue"),
                        meta: {
                            title: "production.sales_gas_metering.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "production.sales_gas_metering.show",
                        component: () =>
                            import("../../pages/Production/SalesGas/Show.vue"),
                        meta: {
                            title: "production.sales_gas_metering.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "production.sales_gas_metering.edit",
                        component: () => import("../../pages/Production/SalesGas/Form.vue"),
                        meta: {
                            title: "production.sales_gas_metering.edit",
                        }
                    },
                ],
            },
            {
                path: "fpu",
                meta: {
                    title: "production.fpu.title",
                },
                children: [
                    {
                        path: "",
                        name: "production.fpu.index",
                        component: () => import("../../pages/Production/FPU/Index.vue"),
                        meta: {
                            title: "production.fpu.title",
                            hideBreadcrumb: true
                        }
                    },
                    {
                        path: "create",
                        name: "production.fpu.create",
                        component: () => import("../../pages/Production/FPU/Form.vue"),
                        meta: {
                            title: "production.fpu.create",
                        }
                    },
                    {
                        path: ":id",
                        name: "production.fpu.show",
                        component: () =>
                            import("../../pages/Production/FPU/Show.vue"),
                        meta: {
                            title: "production.fpu.detail",
                        }
                    },
                    {
                        path: ":id/edit",
                        name: "production.fpu.edit",
                        component: () => import("../../pages/Production/FPU/Form.vue"),
                        meta: {
                            title: "production.fpu.edit",
                        }
                    },
                ],
            },
            {
                path: "summary",
                name: "production.summary.index",
                component: () => import("../../pages/Production/Summary.vue"),
                meta: {
                    title: "production.summary.title",
                    hideBreadcrumb: true
                }
            }
        ]
    },
];