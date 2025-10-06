import Cookies from 'js-cookie';

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
		console.error('Too many redirects detected, forcing clear auth state')
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
		try {
			// Quick validation dengan endpoint yang ringan
			const response = await window.axios.get('/profile', {
				headers: { 'Authorization': `Bearer ${cookieToken}` },
				timeout: 3000 // 3 detik timeout
			})
			isLoggedIn = response.status === 200
			// Reset counter jika berhasil validasi
			redirectCount = 0
		} catch (error) {
			// Jika error 401 atau timeout, token tidak valid
			if (error.response?.status === 401) {
				console.log('Token expired detected in AuthMiddleware, clearing cookie')
				// Hapus cookie yang expired
				Cookies.remove('token')
				Cookies.remove('token', { path: '/' })
				Cookies.remove('token', { path: '', domain: window.location.hostname })
				
				// Clear axios headers
				if (window.axios) {
					delete window.axios.defaults.headers.common['Authorization']
				}
				// Increment redirect counter
				redirectCount++
			}
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