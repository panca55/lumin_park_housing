import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    // Optimized content scanning for better performance
    content: [
        './app/Filament/**/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './vendor/filament/**/*.blade.php',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        // Only scan cached views if they exist
        './storage/framework/views/*.php'
    ],
    
    // Mode configuration for production optimization
    mode: process.env.NODE_ENV ? 'jit' : undefined,
    
    // Reduce safelist to only essential classes to decrease bundle size
    safelist: [
        // Core colors that are dynamically generated
        'text-success-600', 'text-danger-600', 'text-warning-600', 
        'bg-success-50', 'bg-danger-50', 'bg-warning-50',
        // Filament specific classes that might be generated dynamically
        'fi-color-success', 'fi-color-danger', 'fi-color-warning',
        // Badge colors
        'badge-success', 'badge-danger', 'badge-warning', 'badge-primary', 'badge-info'
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
