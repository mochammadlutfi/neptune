export default [

    // Laboratory & Chemicals
    {
        path: '/laboratory/',
        meta: {
            hideBreadcrumb: true
        },
        children: [
            // Laboratory Analysis
            {
                path: 'analysis',
                children: [
                    {
                        path: '',
                        name: 'laboratory.analysis.index',
                        component: () => import("../../pages/Laboratory/Analysis/Index.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'laboratory.view',
                            title: 'laboratory.analysis.title',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: 'create',
                        name: 'laboratory.analysis.create',
                        component: () => import("../../pages/Laboratory/Analysis/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'laboratory.create',
                            title: 'laboratory.analysis.create',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id',
                        name: 'laboratory.analysis.show',
                        component: () => import("../../pages/Laboratory/Analysis/Show.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'laboratory.view',
                            title: 'laboratory.analysis.detail',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id/edit',
                        name: 'laboratory.analysis.edit',
                        component: () => import("../../pages/Laboratory/Analysis/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'laboratory.update',
                            title: 'laboratory.analysis.edit',
                            breadcrumb: true,
                        }
                    },
                ]
            },
            // Chemical Management
            {
                path: 'chemicals',
                children: [
                    {
                        path: '',
                        name: 'laboratory.chemicals.index',
                        component: () => import("../../pages/Laboratory/Chemicals/Index.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'laboratory.view',
                            title: 'laboratory.chemicals.title',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: 'create',
                        name: 'laboratory.chemicals.create',
                        component: () => import("../../pages/Laboratory/Chemicals/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'laboratory.create',
                            title: 'laboratory.chemicals.create',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id',
                        name: 'laboratory.chemicals.show',
                        component: () => import("../../pages/Laboratory/Chemicals/Show.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'laboratory.view',
                            title: 'laboratory.chemicals.detail',
                            breadcrumb: true,
                        }
                    },
                    {
                        path: ':id/edit',
                        name: 'laboratory.chemicals.edit',
                        component: () => import("../../pages/Laboratory/Chemicals/Form.vue"),
                        meta: {
                            requiredAuth: true,
                            permission: 'laboratory.update',
                            title: 'laboratory.chemicals.edit',
                            breadcrumb: true,
                        }
                    },
                ]
            },
        ]
    },
];