<x-filament-panels::page.simple>
    {{-- Optimized inline CSS untuk custom login --}}
    <style>
        .login-form-container {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .dark .login-form-container {
            background: #111827;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.6);
        }

        .login-title {
            font-size: 1.875rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .dark .login-title {
            color: #f9fafb;
        }

        .login-subtitle {
            color: #6b7280;
            text-align: center;
            margin-bottom: 2rem;
        }

        .dark .login-subtitle {
            color: #9ca3af;
        }

        .back-to-home {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background-color: #3b82f6;
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .dark .back-to-home {
            background-color: #2563eb;
        }

        .submit-button {
            display: inline-flex;
            width: 100%;
            margin-top: 1rem;
            justify-content: center;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background-color: #3b82f6;
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-button:hover {
            background-color: #2563eb;
        }

        .dark .submit-button {
            background-color: #2563eb;
        }

        .dark .submit-button:hover {
            background-color: #1d4ed8;
        }
    </style>


    <div class="login-form-container">
        <h2 class="login-title">Selamat Datang Kembali</h2>
        <p class="login-subtitle">Silakan masuk ke akun Anda</p>

        <form wire:submit.prevent="authenticate">
            {{ $this->form }}

            <button type="submit" class="submit-button">
                Masuk
            </button>
        </form>
    </div>

    <div class="mt-6 text-center">
        <a href="{{ url('/') }}" class="back-to-home">
            <span class="arrow-left">→</span>
            Kembali ke Landing Page
        </a>
    </div>
</x-filament-panels::page.simple>