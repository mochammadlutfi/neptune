export function convertToFormData(data) {
    const formData = new FormData();
    for (const key in data) {
        const value = data[key];

        // Jika nilai adalah array
        if(value === null || value === "null"){
            formData.append(key, null)
        }else if (Array.isArray(value)){
            value.forEach((file, index) => {
                formData.append(`${key}[${index}]`, file);
            });
        } else if (value instanceof File) {
            // Jika nilai adalah file
            formData.append(key, value);
        } else if (typeof value === 'object' && value !== null) {
            // Jika nilai adalah objek (non-array), ubah ke string JSON
            formData.append(key, JSON.stringify(value));
        } else if (value !== undefined) {
            // Tambahkan nilai primitif (string, number, boolean)
            formData.append(key, value);
        } 
    }
    return formData;
}
