import defaultTheme from 'tailwindcss/defaultTheme';
import animate from 'tailwindcss-animate';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: ['class', 'class'],
    corePlugins: {
        preflight: true,
    },
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './components/**/*.{ts,tsx,vue}',
        './pages/**/*.{ts,tsx,vue}',
        './app/**/*.{ts,tsx,vue}',
        './src/**/*.{ts,tsx,vue}',
    ],

    theme: {
    	container: {
    		center: true,
    		padding: '2rem',
    		screens: {
    			'2xl': '1400px'
    		}
    	},
    	extend: {
			colors: {
				border: "hsl(var(--border))",
				input: "hsl(var(--input))",
				ring: "hsl(var(--ring))",
				background: "hsl(var(--background))",
				foreground: "hsl(var(--foreground))",
				primary: {
					DEFAULT: "hsl(var(--primary))",
					foreground: "hsl(var(--primary-foreground))",
				},
				secondary: {
					DEFAULT: "hsl(var(--secondary))",
					foreground: "hsl(var(--secondary-foreground))",
				},
				destructive: {
					DEFAULT: "hsl(var(--destructive))",
					foreground: "hsl(var(--destructive-foreground))",
				},
				muted: {
					DEFAULT: "hsl(var(--muted))",
					foreground: "hsl(var(--muted-foreground))",
				},
				accent: {
					DEFAULT: "hsl(var(--accent))",
					foreground: "hsl(var(--accent-foreground))",
				},
				popover: {
					DEFAULT: "hsl(var(--popover))",
					foreground: "hsl(var(--popover-foreground))",
				},
				card: {
					DEFAULT: "hsl(var(--card))",
					foreground: "hsl(var(--card-foreground))",
				},
				sidebar: {
					DEFAULT: "hsl(var(--sidebar-background))",
					foreground: "hsl(var(--sidebar-foreground))",
					primary: "hsl(var(--sidebar-primary))",
					"primary-foreground": "hsl(var(--sidebar-primary-foreground))",
					accent: "hsl(var(--sidebar-accent))",
					"accent-foreground": "hsl(var(--sidebar-accent-foreground))",
					border: "hsl(var(--sidebar-border))",
					ring: "hsl(var(--sidebar-ring))",
				},
			},
			borderRadius: {
				lg: "var(--radius)",
				md: "calc(var(--radius) - 2px)",
				sm: "calc(var(--radius) - 4px)",
			},
    		borderRadius: {
    			lg: "var(--radius)",
    			md: "calc(var(--radius) - 2px)",
    			sm: "calc(var(--radius) - 4px)",
    		},
    		fontFamily: {
				sans: ['var(--font-family)', 'Poppins', 'sans-serif'],
				base: ['var(--font-family)', 'Poppins', 'sans-serif'],
			},
    		fontSize: {
    			'base': 'var(--font-size-base)',
    			'dynamic-sm': 'calc(var(--font-size-base) * 0.875)',
    			'dynamic-lg': 'calc(var(--font-size-base) * 1.125)',
    			'dynamic-xl': 'calc(var(--font-size-base) * 1.25)',
    		},
    		fontWeight: {
    			'dynamic': 'var(--font-weight-base)',
    		},
    		spacing: {
    			'dynamic-1': 'calc(0.25rem * var(--spacing-scale))',
    			'dynamic-2': 'calc(0.5rem * var(--spacing-scale))',
    			'dynamic-3': 'calc(0.75rem * var(--spacing-scale))',
    			'dynamic-4': 'calc(1rem * var(--spacing-scale))',
    			'dynamic-5': 'calc(1.25rem * var(--spacing-scale))',
    			'dynamic-6': 'calc(1.5rem * var(--spacing-scale))',
    			'dynamic-8': 'calc(2rem * var(--spacing-scale))',
    		},
    		boxShadow: {
    			'dynamic': 'var(--shadow-default)',
    		},
            width: {
                'sidebar': 'var(--sidebar-width)',
                'sidebar-icon': 'var(--sidebar-width-icon)', // Custom width for icon mode
                'sidebar-mobile': 'var(--sidebar-width-mobile)',
            },
    		keyframes: {
    			"accordion-down": {
    				from: { height: "0" },
    				to: { height: "var(--radix-accordion-content-height)" },
    			},
    			"accordion-up": {
    				from: { height: "var(--radix-accordion-content-height)" },
    				to: { height: "0" },
    			},
    		},
    		animation: {
    			"accordion-down": "accordion-down 0.2s ease-out",
    			"accordion-up": "accordion-up 0.2s ease-out",
    		},
    	}
    },

    plugins: [animate],
};