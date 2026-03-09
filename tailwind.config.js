import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './app/**/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './vendor/filament/**/*.blade.php',
        './app/Filament/**/*.php',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    
    safelist: [
        // Filament essential classes
        'fi-*',
        'filament-*',
        // Colors yang sering digunakan
        'text-primary-600',
        'bg-primary-50',
        'border-primary-300',
        // Layout classes
        'mt-6',
        'text-center',
        'rounded-lg',
        'shadow-lg',
    ],
    
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // Only extend what you actually need
        },
    },
    
    plugins: [
        // Include Filament form plugin if using forms
    ],
    
    // Optimize build for better performance
    corePlugins: {
        // Disable unused core plugins to reduce bundle size
        // preflight: false, // Uncomment if you don't need CSS reset
    }
};
