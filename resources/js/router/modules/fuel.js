export default [

    // Fuel Management
    {
        path: '/fuel/',
        meta: {
            hideBreadcrumb: true
        },
        children: [
            // Fuel Consumption
            {
                path: 'consumption',
                children: [
                    {
                        path: '',
                        name: 'fuel.consumption.index',
                        component: () => import("../../pages/Fuel/Consumption/Index.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'fuel.view',
                            title: 'fuel.consumption.title',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: 'create',
                        name: 'fuel.consumption.create',
                        component: () => import("../../pages/Fuel/Consumption/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'fuel.create',
                            title: 'fuel.consumption.create',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id',
                        name: 'fuel.consumption.show',
                        component: () => import("../../pages/Fuel/Consumption/Show.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'fuel.view',
                            title: 'fuel.consumption.detail',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id/edit',
                        name: 'fuel.consumption.edit',
                        component: () => import("../../pages/Fuel/Consumption/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'fuel.update',
                            title: 'fuel.consumption.edit',
                            breadcrumb: true,
                        }
                    },
                ]
            },
            // Inventory Tracking
            {
                path: 'inventory',
                children: [
                    {
                        path: '',
                        name: 'fuel.inventory.index',
                        component: () => import("../../pages/Fuel/Inventory/Index.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'fuel.view',
                            title: 'fuel.inventory.title',
                            breadcrumb: true,
                        }
                    },
                ]
            },
        ]
    },
];