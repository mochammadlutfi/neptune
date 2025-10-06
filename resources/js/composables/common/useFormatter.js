import { useSetting } from "./useSetting";
import currency from "currency.js";
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

    const formatCurrency = (value) => {
        const appBase = useSetting();
        const separator = appBase.app.currency?.separator || ",";
        const decimal = appBase.app.currency?.decimal || ".";
        const precision = appBase.app.currency?.precision || 0;
        const symbol = appBase.app.currency?.symbol || "$";

        // Format nilai dengan currency.js
        return currency(value ?? 0, {
            symbol: symbol, // Simbol mata uang
            separator: separator, // Pemisah ribuan
            decimal: decimal, // Pemisah desimal
            precision: precision, // Jumlah angka desimal
        }).format();
    };

    const formatNumber = (value) => {
        const appBase = useSetting();
        const separator = appBase.app.currency?.separator || ",";
        const decimal = appBase.app.currency?.decimal || ".";

        // Format angka tanpa simbol mata uang
        return currency(value ?? 0, {
            symbol: "", // Tanpa simbol
            separator: separator, // Pemisah ribuan
            decimal: decimal, // Pemisah desimal
            precision: 0, // Tanpa desimal untuk number formatting
        }).format();
    };

    return {
        formatDate,
        formatCurrency,
        formatNumber,
    };
};
