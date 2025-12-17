<x-filament-panels::page.simple>
    {{ $this->form }}

    <div class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
        Belum punya akun?
        <a
            href="{{ route('filament.admin.auth.register') }}"
            class="font-semibold text-primary-600 hover:text-primary-500"
        >
            Daftar di sini
        </a>
    </div>
</x-filament-panels::page.simple>