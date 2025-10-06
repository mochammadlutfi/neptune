export default [
    // HSE Operations
    {
        path: '/HSE/',
        meta: {
            hideBreadcrumb: true
        },
        children: [
            // Safety Event Recording
            {
                path: 'events',
                children: [
                    {
                        path: '',
                        name: 'HSE.events.index',
                        component: () => import("../../pages/HSE/Events/Index.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'HSE.view',
                            title: 'HSE.events.title',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: 'create',
                        name: 'HSE.events.create',
                        component: () => import("../../pages/HSE/Events/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'HSE.create',
                            title: 'HSE.events.create',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id',
                        name: 'HSE.events.show',
                        component: () => import("../../pages/HSE/Events/Show.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'HSE.view',
                            title: 'HSE.events.detail',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id/edit',
                        name: 'HSE.events.edit',
                        component: () => import("../../pages/HSE/Events/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'HSE.update',
                            title: 'HSE.events.edit',
                            breadcrumb: true,
                        }
                    },
                ]
            },
            // Personnel Tracking
            {
                path: 'personnel',
                children: [
                    {
                        path: '',
                        name: 'HSE.personnel.index',
                        component: () => import("../../pages/HSE/Personnel/Index.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'HSE.view',
                            title: 'HSE.personnel.title',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: 'create',
                        name: 'HSE.personnel.create',
                        component: () => import("../../pages/HSE/Personnel/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'HSE.create',
                            title: 'HSE.personnel.create',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id',
                        name: 'HSE.personnel.show',
                        component: () => import("../../pages/HSE/Personnel/Show.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'HSE.view',
                            title: 'HSE.personnel.detail',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id/edit',
                        name: 'HSE.personnel.edit',
                        component: () => import("../../pages/HSE/Personnel/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'HSE.update',
                            title: 'HSE.personnel.edit',
                            breadcrumb: true,
                        }
                    },
                ]
            },
        ]
    },
];