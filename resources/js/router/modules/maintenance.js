export default [

    // Maintenance
    {
        path: '/maintenance/',
        meta: {
            hideBreadcrumb: true
        },
        children: [
            // Work Order Management
            {
                path: 'work-orders',
                children: [
                    {
                        path: '',
                        name: 'maintenance.work_orders.index',
                        component: () => import("../../pages/Maintenance/WorkOrders/Index.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'maintenance.view',
                            title: 'maintenance.work_orders.title',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: 'create',
                        name: 'maintenance.work_orders.create',
                        component: () => import("../../pages/Maintenance/WorkOrders/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'maintenance.create',
                            title: 'maintenance.work_orders.create',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id',
                        name: 'maintenance.work_orders.show',
                        component: () => import("../../pages/Maintenance/WorkOrders/Show.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'maintenance.view',
                            title: 'maintenance.work_orders.detail',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id/edit',
                        name: 'maintenance.work_orders.edit',
                        component: () => import("../../pages/Maintenance/WorkOrders/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'maintenance.update',
                            title: 'maintenance.work_orders.edit',
                            breadcrumb: true,
                        }
                    },
                ]
            },
            // Predictive Maintenance
            {
                path: 'pdm',
                children: [
                    {
                        path: '',
                        name: 'maintenance.pdm.index',
                        component: () => import("../../pages/Maintenance/Pdm/Index.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'maintenance.view',
                            title: 'maintenance.pdm.title',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: 'create',
                        name: 'maintenance.pdm.create',
                        component: () => import("../../pages/Maintenance/Pdm/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'maintenance.create',
                            title: 'maintenance.pdm.create',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id',
                        name: 'maintenance.pdm.show',
                        component: () => import("../../pages/Maintenance/Pdm/Show.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'maintenance.view',
                            title: 'maintenance.pdm.detail',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id/edit',
                        name: 'maintenance.pdm.edit',
                        component: () => import("../../pages/Maintenance/Pdm/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'maintenance.update',
                            title: 'maintenance.pdm.edit',
                            breadcrumb: true,
                        }
                    },
                ]
            },
        ]
    },
];