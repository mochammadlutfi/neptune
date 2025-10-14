import { useSetting } from "./useSetting";
import dayjs from "dayjs";

export const useFormatter = () => {
    const formatDate = (value) => {
        const appBase = useSetting();

        const dateFormat = appBase.app.date_format || "YYYY-MM-DD";
        const timeFormat = appBase.app.time_format || "HH:mm:ss";

        const isTimeIncluded =
            dayjs(value).hour() ||
            dayjs(value).minute() ||
            dayjs(value).second();
        const formatString = isTimeIncluded
            ? `${dateFormat} ${timeFormat}`
            : dateFormat;

        return dayjs(value).format(formatString);
    };

    const formatTime = (value) => {
        const appBase = useSetting();
        const timeFormat = appBase.app.time_format || "HH:mm:ss";

        // If value is a valid time string like "21:00:00", parse it as a time and format
        return dayjs(value, "HH:mm").format(timeFormat);
    };

    const formatNumber = (value, decimals = 0) => {
        const appBase = useSetting();
        const separator = appBase.app.number?.separator || ",";
        const decimal = appBase.app.number?.decimal || ".";
        const precision = decimals ?? appBase.app.number?.precision ?? 0;

        const num = Number(value ?? 0);
        if (!isFinite(num)) return "";

        const absFixed = Math.abs(num).toFixed(precision);
        const [intPart, fracPart] = absFixed.split(".");
        const intWithSep = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, separator);
        const formatted = fracPart ? intWithSep + decimal + fracPart : intWithSep;
        return num < 0 ? "-" + formatted : formatted;
    };

    return {
        formatDate,
        formatTime,
        formatNumber,
    };
};
