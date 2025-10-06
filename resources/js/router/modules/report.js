export default [

    // Reports
    {
        path: '/reports/',
        meta: {
            hideBreadcrumb: true
        },
        children: [
            // DVR Generation
            {
                path: 'dvr',
                children: [
                    {
                        path: '',
                        name: 'reports.dvr.index',
                        component: () => import("../../pages/Reports/Dvr/Index.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'reports.generate',
                            title: 'reports.dvr.title',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: 'generate',
                        name: 'reports.dvr.generate',
                        component: () => import("../../pages/Reports/Dvr/Generate.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'reports.generate',
                            title: 'reports.dvr.generate',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':date/view',
                        name: 'reports.dvr.view',
                        component: () => import("../../pages/Reports/Dvr/View.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'reports.view',
                            title: 'reports.dvr.view',
                            breadcrumb: true,
                        }
                    },
                ]
            },
            // Custom Reports
            {
                path: 'custom',
                children: [
                    {
                        path: '',
                        name: 'reports.custom.index',
                        component: () => import("../../pages/Reports/Custom/Index.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'reports.view',
                            title: 'reports.custom.title',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: 'builder',
                        name: 'reports.custom.builder',
                        component: () => import("../../pages/Reports/Custom/Builder.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'reports.create',
                            title: 'reports.custom.builder',
                            breadcrumb: true,
                        }
                    },
                ]
            },
        ]
    },
];