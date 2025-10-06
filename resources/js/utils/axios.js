import axios from 'axios'
import { useAuth } from '@/composables/auth'
import { useCSRF } from '@/composables/csrf/useCSRF'

// Request interceptor
axios.defaults.baseURL = '/api/';

// Initialize CSRF handling
const { handleCSRFError, initializeCSRF } = useCSRF()
initializeCSRF()

axios.interceptors.request.use(request => {
    try {
        const { token } = useAuth();
        if (token) {
            request.headers.Authorization = `Bearer ${token}`
        } else {
            request.headers.Authorization = ""
        }
    } catch (error) {
        // Jika Pinia store belum diinisialisasi, skip authorization header
        console.warn('Auth store not initialized yet:', error);
        request.headers.Authorization = ""
    }

    return request
});

axios.interceptors.response.use(function (response) {
    return response;
}, function (err) {
    const error = {
        status: err.response?.status,
        original: err,
        validation: {},
        message: null,
    };
    
    switch (err.response?.status) {
        case 422:
            for (let field in err.response.data.result) {
                error.validation[field] = err.response.data.result[field][0];
            }
            break;
        case 403:
            error.message = "You're not allowed to do that.";
            break;
        case 401:
            error.message = "Session expired. Please login again.";
            
            // Auto logout ketika mendapat response 401 (Unauthenticated)
            try {
                const { token, logout } = useAuth();
                if (token && token.value) {
                    console.log("Unauthenticated response detected, logging out...");
                    
                    // Panggil logout untuk membersihkan state dan cookie
                    // Tapi jangan tunggu response, langsung redirect
                    logout().catch(() => {
                        console.warn("Auto logout failed, but continuing with manual cleanup");
                    }).finally(() => {
                        // Pastikan redirect terjadi setelah cleanup
                        if (typeof window !== 'undefined') {
                            setTimeout(() => {
                                // window.location.href = '/login'; // COMMENTED: Mencegah loop redirect
                            }, 100); // Delay kecil untuk memastikan cleanup selesai
                        }
                    });
                } else {
                    // Jika tidak ada token, langsung redirect
                    if (typeof window !== 'undefined') {
                        // window.location.href = '/login'; // COMMENTED: Mencegah loop redirect
                    }
                }
            } catch (authError) {
                console.warn('Auth store not available during 401 handling:', authError);
                // Manual cleanup jika auth store tidak tersedia
                import('js-cookie').then(Cookies => {
                    // Hapus cookie 'token' dengan berbagai path untuk memastikan terhapus
                    Cookies.default.remove("token");
                    Cookies.default.remove("token", { path: '/' });
                    Cookies.default.remove("token", { path: '', domain: window.location.hostname });
                    
                    // Clear axios headers
                    delete axios.defaults.headers.common["Authorization"];
                    
                    console.log('Manual cookie cleanup completed for token');
                    
                    // Redirect ke login setelah cleanup
                    if (typeof window !== 'undefined') {
                        setTimeout(() => {
                            // window.location.href = '/login'; // COMMENTED: Mencegah loop redirect
                        }, 100);
                    }
                });
            }
            break;
        case 419:
            error.message = "CSRF token mismatch. Refreshing token...";
            
            // Handle CSRF token mismatch menggunakan composable
            return handleCSRFError(err.config);
            break;
        case 404:
            error.message = "Not Found.";
            // router.push({ name: 'not-found' });
            break;
        case 500:
            error.message = "Something went really bad. Sorry.";
            break;
        default:
            error.message = "Something went wrong. Please try again later.";
    }
    return Promise.reject(error);
});
