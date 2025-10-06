export default [

    // Marine Operations
    {
        path: '/marine/',
        meta: {
            hideBreadcrumb: true
        },
        children: [
            // Ballast Operations
            {
                path: 'ballast',
                children: [
                    {
                        path: '',
                        name: 'marine.ballast.index',
                        component: () => import("../../pages/Marine/Ballast/Index.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'marine.view',
                            title: 'marine.ballast.title',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: 'create',
                        name: 'marine.ballast.create',
                        component: () => import("../../pages/Marine/Ballast/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'marine.create',
                            title: 'marine.ballast.create',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id',
                        name: 'marine.ballast.show',
                        component: () => import("../../pages/Marine/Ballast/Show.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'marine.view',
                            title: 'marine.ballast.detail',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id/edit',
                        name: 'marine.ballast.edit',
                        component: () => import("../../pages/Marine/Ballast/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'marine.update',
                            title: 'marine.ballast.edit',
                            breadcrumb: true,
                        }
                    },
                ]
            },
            // Weather Monitoring
            {
                path: 'weather',
                children: [
                    {
                        path: '',
                        name: 'marine.weather.index',
                        component: () => import("../../pages/Marine/Weather/Index.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'marine.view',
                            title: 'marine.weather.title',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: 'create',
                        name: 'marine.weather.create',
                        component: () => import("../../pages/Marine/Weather/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'marine.create',
                            title: 'marine.weather.create',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id',
                        name: 'marine.weather.show',
                        component: () => import("../../pages/Marine/Weather/Show.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'marine.view',
                            title: 'marine.weather.detail',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id/edit',
                        name: 'marine.weather.edit',
                        component: () => import("../../pages/Marine/Weather/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'marine.update',
                            title: 'marine.weather.edit',
                            breadcrumb: true,
                        }
                    },
                ]
            },
        ]
    },
];