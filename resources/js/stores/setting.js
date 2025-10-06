import { defineStore } from 'pinia';
import { ref } from 'vue';
import Cookies from 'js-cookie';
import axios from 'axios';

export const useAppSettingsStore = defineStore('appSettings', () => {
    // State
    const settings = ref({
        app_name: "",
        date_format: "",
        time_format: "",
        timezone: "",
        logo_light: null,
        logo_dark: null,
        locale: 'id-ID',
        currency: null,
    })
    const loading = ref(false)

    // Actions
    const loadSettings = async () => {
        loading.value = true;
        const cookieData = Cookies.get('app_settings');
        if (cookieData) {
            settings.value = JSON.parse(cookieData);
        } else {
            await fetchSettingsFromAPI();
        }
        loading.value = false;
    }

    const fetchSettingsFromAPI = async () => {
        try {
            const response = await axios.get("/settings");
            settings.value = response.data;
            saveSettingsToCookie();
        } catch (error) {
            console.error("Failed to load settings from API:", error);
        }
    }

    const saveSettingsToCookie = () => {
        Cookies.set('app_settings', JSON.stringify(settings.value), {
            expires: 7
        }); // 7 days
    }

    const updateSetting = (key, value) => {
        if (settings.value.hasOwnProperty(key)) {
            settings.value[key] = value;
            saveSettingsToCookie();
        }
    }

    const refreshSettings = async () => {
        await fetchSettingsFromAPI();
    }

    const $reset = () => {
        settings.value = {
            app_name: "",
            date_format: "",
            time_format: "",
            timezone: "",
            logo_light: null,
            logo_dark: null,
            locale: 'id-ID',
            currency: null,
        }
        loading.value = false
    }

    return {
        // State
        settings,
        loading,
        // Actions
        loadSettings,
        fetchSettingsFromAPI,
        saveSettingsToCookie,
        updateSetting,
        refreshSettings,
        $reset
    }
});
