<x-filament-panels::page.simple>
    <style>
        .back-to-home {
            justify-content: center;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background-color: #3b82f6;
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .back-to-home:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .arrow-left {
            display: inline-block;
            transform: rotate(180deg);
            font-size: 1.25rem;
        }

        .login-form-container {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .login-title {
            font-size: 1.875rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .login-subtitle {
            color: #6b7280;
            text-align: center;
            margin-bottom: 2rem;
        }

        .submit-button {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            margin-top: 1.5rem;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.5);
        }

        .submit-button:active {
            transform: translateY(0);
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