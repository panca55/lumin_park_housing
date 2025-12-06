import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './app/**/*.php',
        './resources/views/**/*.blade.php',
    ],
    safelist: [
        // Colors
        'bg-gray-50', 'bg-white', 'bg-zinc-900', 'text-black', 'text-white', 
        'text-gray-300', 'text-gray-400', 'text-gray-500', 'text-gray-600', 'text-gray-700',
        'border-gray-100', 'border-gray-200', 'border-zinc-800',
        'bg-indigo-600', 'hover:bg-indigo-700', 'text-indigo-400', 'text-indigo-600',
        'from-indigo-600', 'to-cyan-400', 'to-white/50', 'to-zinc-900/50',
        'text-yellow-500', 'text-green-500', 'bg-white/90', 'bg-black/60',
        // Spacing
        'px-2', 'px-3', 'px-4', 'px-6', 'py-1', 'py-2', 'py-4', 'py-6', 'py-10', 'py-12', 'py-16', 'py-20',
        'p-4', 'p-6', 'mt-2', 'mt-3', 'mt-4', 'mt-6', 'mt-8', 'mt-10',
        'gap-2', 'gap-3', 'gap-4', 'gap-6', 'gap-8',
        'w-10', 'h-10', 'h-48', 'h-56', 'max-w-md', 'max-w-xl', 'max-w-7xl',
        // Layout
        'flex', 'flex-col', 'items-center', 'justify-between',
        'grid', 'grid-cols-1', 'sm:grid-cols-2', 'lg:grid-cols-2', 'lg:grid-cols-3',
        'rounded-md', 'rounded-lg', 'rounded-2xl',
        'shadow-sm', 'shadow-lg', 'hover:shadow-lg',
        'border', 'border-b', 'border-t',
        'transform', 'hover:-translate-y-1', 'transition',
        // Text
        'text-xs', 'text-sm', 'text-lg', 'text-xl', 'text-2xl', 'text-3xl', 'sm:text-4xl',
        'font-medium', 'font-semibold', 'font-bold', 'font-extrabold',
        'lg:py-20',
        // Dark mode
        'dark:bg-black', 'dark:bg-zinc-900', 'dark:text-white', 'dark:text-gray-300', 
        'dark:text-gray-400', 'dark:border-zinc-800', 'dark:hover:text-white', 'dark:hover:text-gray-300'
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
};
