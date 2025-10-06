// src/composables/usePageTitle.js
import { useTitle } from "@vueuse/core";
import { ref, computed } from "vue";
import { useSetting } from "./useSetting";
import { useAuth } from "@/composables/auth";

export function useHead() {
    const base = useSetting();
    const dynamicTitle = ref("");

    const pageTitle = computed(() => {
        return dynamicTitle.value
            ? `${dynamicTitle.value} | ${base.app.app_name}`
            : base.app.app_name;
    });

    useTitle(pageTitle);

    function setTitle(title) {
        dynamicTitle.value = title;
    }

    return {
        setTitle,
    };
}

export function useBase() {
    const { permissions } = useAuth();
    console.log(permissions.value);
    const can = (permission) => {
        if (!permission || permissions.length === 0) {
            return false;
        }

        // Jika permission adalah array, periksa apakah semua izin ada di daftar izin pengguna
        if (Array.isArray(permission)) {
            return permission.every((perm) => permissions.value.includes(perm));
        }

        // Jika permission adalah string, periksa apakah izin ada di daftar izin pengguna
        return permissions.value.includes(permission);
    };

    return {
        can,
    };
}
