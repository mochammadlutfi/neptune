export default [{
    path: '/dashboard',
    name: 'dashboard',
    component: () => import("../pages/Dashboard/Index.vue"),
    meta: {
        title: 'base.dashboard',
        hideBreadcrumb: false
    },
    children: [
        {
            path: '/',
            name: 'dashboard.overview',
            component: () => import("../pages/Dashboard/Overview.vue"),
            meta: {
                title: 'base.dashboard',
                hideBreadcrumb: false
            }
        },
        {
            path: 'sales',
            name: 'dashboard.sales',
            component: () => import("../pages/Dashboard/Sales.vue"),
            meta: {
                title: 'base.sales',
                breadcrumb: 'base.sales'
            }
        },
        {
            path: 'purchase',
            name: 'dashboard.purchase',
            component: () => import("../pages/Dashboard/Purchase.vue"),
            meta: {
                title: 'base.purchase',
                breadcrumb: 'base.purchase'
            }
        },
        {
            path: 'inventory',
            name: 'dashboard.inventory',
            component: () => import("../pages/Dashboard/Inventory.vue"),
            meta: {
                title: 'base.inventory',
                breadcrumb: 'base.inventory'
            }
        },
        {
            path: 'accounting',
            name: 'dashboard.accounting',
            component: () => import("../pages/Dashboard/Accounting.vue"),
            meta: {
                title: 'base.accounting',
                breadcrumb: 'base.accounting'
            }
        },
    ]
}]
