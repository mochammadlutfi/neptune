
import { computed } from 'vue';
import { useAuth } from '@/composables/auth';


const useGeneralHelper = () => {
    const { hasPermission } = useAuth();

    const dayjsObject = (date) => {
        if (date == undefined) {
            return dayjs().tz(appSetting.value.timezone);
        } else {
            return dayjs(date).tz(appSetting.value.timezone);
        }
    }

    const can = (permission) => {
        return computed(() => {
            return hasPermission(permission);
        });
    };

    return {
        can
    };
}


export default useGeneralHelper;