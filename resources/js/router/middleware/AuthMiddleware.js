import Cookies from 'js-cookie';

// Flag untuk memastikan validasi profil hanya dilakukan sekali
let hasValidatedProfile = false;
let profileValidationPromise = null;

// Counter untuk mencegah infinite loop
let redirectCount = 0;
const MAX_REDIRECTS = 3;

/**
 * Middleware untuk authentication guard
 * Mengecek status login dan mengarahkan ke halaman yang sesuai
 * Menggunakan cookie langsung untuk menghindari masalah timing dengan Pinia store
 * Dilengkapi dengan validasi token dan pencegahan infinite loop
 */
export default async (to, from, next) => {
	// Cek apakah sudah terlalu banyak redirect untuk mencegah infinite loop
	if (redirectCount >= MAX_REDIRECTS) {
		// console.error('Too many redirects detected, forcing clear auth state')
		// Force clear semua auth state
		Cookies.remove('token')
		Cookies.remove('token', { path: '/' })
		Cookies.remove('token', { path: '', domain: window.location.hostname })
		if (window.axios) {
			delete window.axios.defaults.headers.common['Authorization']
		}
		// Reset counter dan paksa ke login
		redirectCount = 0
		next({ name: 'login', replace: true })
		return
	}

	// Cek token langsung dari cookie tanpa bergantung pada store
	const cookieToken = Cookies.get('token')
	
	// Jika ada token, cek apakah masih valid dengan quick API call
	let isLoggedIn = false
	if (cookieToken) {
		// Pastikan header Authorization terpasang sebelum panggil composable
		if (window.axios) {
			window.axios.defaults.headers.common['Authorization'] = `Bearer ${cookieToken}`
		}

		// Gunakan composable useUser agar hanya satu panggilan ke /api/profile
		try {
			const { useUser } = await import('@/composables/auth/useUser')
			const userStore = useUser()

			// Jika user sudah ada di store, anggap sudah login tanpa panggilan API
			if (userStore.user) {
				isLoggedIn = true
				redirectCount = 0
			} else if (!hasValidatedProfile) {
				// Lakukan validasi profil hanya sekali
				if (!profileValidationPromise) {
					profileValidationPromise = userStore.getUser()
				}
				try {
					await profileValidationPromise
					isLoggedIn = !!userStore.user
					redirectCount = 0
				} catch (error) {
					// Jika error 401, bersihkan token dan header
					if (error.status === 401 || error.response?.status === 401) {
						console.log('Token expired detected in AuthMiddleware, clearing cookie')
						Cookies.remove('token')
						Cookies.remove('token', { path: '/' })
						Cookies.remove('token', { path: '', domain: window.location.hostname })
						if (window.axios) {
							delete window.axios.defaults.headers.common['Authorization']
						}
						redirectCount++
					}
					isLoggedIn = false
				} finally {
					hasValidatedProfile = true
					profileValidationPromise = null
				}
			} else {
				// Sudah divalidasi sebelumnya, jangan panggil API lagi
				isLoggedIn = !!userStore.user
			}
		} catch (importError) {
			console.warn('Failed to import useUser store for auth validation:', importError)
			isLoggedIn = false
		}
	} else {
		// Reset counter jika tidak ada token
		redirectCount = 0
	}

	// Debug logging untuk troubleshooting
	console.log('AuthMiddleware Debug:', {
		to: to.name,
		from: from.name,
		isLoggedIn: isLoggedIn,
		cookieToken: cookieToken ? 'present' : 'absent'
	})

	let exceptionalRoutes = ['login', 'register', 'forgot-password']
	let isGoingExceptionalRoutes = exceptionalRoutes.includes(to.name)

	/**
	 * IF THE USER IS NOT LOGGED IN
	 */
	if (!isLoggedIn) {
		if (isGoingExceptionalRoutes) {
			// Reset counter untuk navigasi yang berhasil
			redirectCount = 0
			next() // The user is not logged in but it's going to exceptional routes ? fine
			return
		} else {
			// Increment counter sebelum redirect ke login
			redirectCount++
			next({ name: 'login', replace: true })
			return
		} // other routes than exceptional paths => /login
	}

	/**
	 * IF THE USER IS LOGGED IN
	 */
	if (isLoggedIn && isGoingExceptionalRoutes) {
		console.log('User is logged in, redirecting to dashboard from:', to.name)
		// Reset counter untuk navigasi yang berhasil
		redirectCount = 0
		next({ name: 'dashboard', query: { 'redirect-reason': 'already logged' }, replace: true })
	} else {
		// User sudah login dan menuju route yang valid
		// Reset counter untuk navigasi yang berhasil
		redirectCount = 0
		next()
	}
}