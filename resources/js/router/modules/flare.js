export default [

    // Flare & Venting
    {
        path: '/flare/',
        meta: {
            hideBreadcrumb: true
        },
        children: [
            // Flare Events
            {
                path: 'events',
                children: [
                    {
                        path: '',
                        name: 'flare.events.index',
                        component: () => import("../../pages/Flare/Events/Index.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'flare.view',
                            title: 'flare.events.title',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: 'create',
                        name: 'flare.events.create',
                        component: () => import("../../pages/Flare/Events/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'flare.create',
                            title: 'flare.events.create',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id',
                        name: 'flare.events.show',
                        component: () => import("../../pages/Flare/Events/Show.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'flare.view',
                            title: 'flare.events.detail',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id/edit',
                        name: 'flare.events.edit',
                        component: () => import("../../pages/Flare/Events/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'flare.update',
                            title: 'flare.events.edit',
                            breadcrumb: true,
                        }
                    },
                ]
            },
            // Environmental Compliance
            {
                path: 'compliance',
                children: [
                    {
                        path: '',
                        name: 'flare.compliance.index',
                        component: () => import("../../pages/Flare/Compliance/Index.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'flare.view',
                            title: 'flare.compliance.title',
                            breadcrumb: true,
                        }
                    },
                ]
            },
        ]
    },
];