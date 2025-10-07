import { createRouter, createWebHistory } from "vue-router";
import AuthMiddleware from "./middleware/AuthMiddleware";



import ProfileIndex from "../pages/Profile/Index.vue";
import ProfileEdit from "../pages/Profile/Edit.vue";
import ProfilePassword from "../pages/Profile/Password.vue";
import ProfileDevice from "../pages/Profile/Device.vue";

import SettingRoutes from "../router/settings";
import DashboardRoutes from "../router/dashboard";
import MasterRoutes from "../router/modules/master";
import ProductionRoutes from "../router/modules/production";
// import ReportRoutes from "../router/modules/report";
// import MaintenanceRoutes from '../router/modules/maintenance';
// import FlareRoutes from '../router/modules/flare';
// import FuelRoutes from '../router/modules/fuel';
// import HSERoutes from '../router/modules/hse';
// import LaboratoryRoutes from '../router/modules/laboratory';
// import MarineRoutes from '../router/modules/marine';

import Login from "../pages/Auth/Login.vue";

const moduleRoutes = [];

// Dinamis import semua file routes.js dalam folder modules di root
const modules = import.meta.glob("../../../modules/**/resources/routes.js");

// Muat semua rute secara asinkron
const loadModuleRoutes = async () => {
    const routes = await Promise.all(
        Object.keys(modules).map((path) => modules[path]())
    );

    routes.forEach((mod) => {
        moduleRoutes.push(...mod.default);
    });
};

// await loadModuleRoutes();
const router = createRouter({
    history: createWebHistory("/"),
    routes: [
        {
            name: "login",
            path: "/",
            component: Login,
            meta: { 
                requiredAuth: false,
                layout: "Guest",
                title : "auth.login.title",
                breadcrumb : "auth.login.title"
            },
        },
        {
            path: "/profile/",
            name: "profile",
            component: ProfileIndex,
            children: [
                {
                    path: "",
                    name: "profile.edit",
                    component: ProfileEdit,
                },
                {
                    path: "password",
                    name: "profile.password",
                    component: ProfilePassword,
                },
                {
                    path: "devices",
                    name: "profile.device",
                    component: ProfileDevice,
                },
            ],
            meta: {
                requiredAuth: true,
            },
        },
        ...moduleRoutes,
        ...SettingRoutes,
        ...DashboardRoutes,
        ...MasterRoutes,
        // ...ProductionRoutes,
        // ...ReportRoutes,
        // ...MaintenanceRoutes,
        // ...FlareRoutes,
        // ...FuelRoutes,
        // ...HSERoutes,
        // ...LaboratoryRoutes,
        // ...MarineRoutes,
        
        {
            path: "/:pathMatch(.*)*",
            name: "not-found",
            component: () => import("../pages/Errors/NotFound.vue"),
            meta: {
                layout: "Guest",
            },
        },
    ],
});
router.afterEach((to) => {
  if (to.name && String(to.name).startsWith("master.")) {
    localStorage.setItem("master:last", to.fullPath)
  }
})
router.beforeEach(AuthMiddleware);

export default router;
