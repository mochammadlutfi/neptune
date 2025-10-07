import SettingSystem from '../pages/Settings/System/Index.vue';

export default [{
    path: '/settings/',
    meta: {
        title: 'base.setting',
        hideBreadcrumb: true
    },
    children: [
        {
            path: 'system',
            name: 'settings.system',
            component: SettingSystem,
            meta: {
                title: 'settings.system.title',
                breadcrumb: 'settings.system.title'
            },
            children: [{
                    path: '',
                    name: 'settings.system.general',
                    component: () => import("../pages/Settings/System/General.vue"),
                    meta: {
                        title: 'settings.system.general.title',
                        breadcrumb: 'settings.system.general.title'
                    }
                },
                {
                    path: 'email',
                    name: 'settings.system.email',
                    component: () => import("../pages/Settings/System/Email.vue"),
                    meta: {
                        title: 'settings.system.email.title',
                        breadcrumb: 'settings.system.email.title'
                    }
                }
            ]
        },
        {
            path: 'user',
            meta: {
                title: 'settings.user.title',
                breadcrumb: 'settings.user.title'
            },
            children: [{
                    path: '',
                    name: 'settings.user.index',
                    component: () => import("../pages/Settings/User/Index.vue"),
                    meta: {
                        title: 'settings.user.title',
                        hideBreadcrumb: true
                    }
                },
                {
                    path: 'create',
                    name: 'settings.user.add',
                    component: () => import("../pages/Settings/User/Form.vue"),
                    meta: {
                        title: 'settings.user.add',
                        breadcrumb: 'settings.user.add'
                    }
                },
                {
                    path: ':id',
                    name: 'settings.user.detail',
                    component: () => import("../pages/Settings/User/Show.vue"),
                    meta: {
                        title: 'settings.user.detail',
                        breadcrumb: 'settings.user.detail'
                    }
                },
                {
                    path: ':id/edit',
                    name: 'settings.user.edit',
                    component: () => import("../pages/Settings/User/Form.vue"),
                    meta: {
                        title: 'settings.user.edit',
                        breadcrumb: 'settings.user.edit'
                    }
                },
            ]
        },
        {
            path: 'permission',
            meta: {
                title: 'settings.role_permission.title',
                breadcrumb: 'settings.role_permission.title'
            },
            children: [{
                    path: '',
                    name: 'permission.index',
                    component: () => import("../pages/Settings/Permission/Index.vue"),
                    meta: {
                        title: 'settings.role_permission.title',
                        hideBreadcrumb: true
                    }
                },
                {
                    path: 'create',
                    name: 'permission.create',
                    component: () => import("../pages/Settings/Permission/Form.vue"),
                    meta: {
                        title: 'settings.role_permission.create',
                        breadcrumb: 'settings.role_permission.create'
                    }
                },
                {
                    path: ':id/edit',
                    name: 'permission.edit',
                    component: () => import("../pages/Settings/Permission/Form.vue"),
                    meta: {
                        title: 'settings.role_permission.edit',
                        breadcrumb: 'settings.role_permission.edit'
                    }
                },
            ]
        },
    ]

}]
