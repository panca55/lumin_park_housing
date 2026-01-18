<?php

namespace App\Filament\Responses;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $user = auth()->user();

        // Redirect admin to admin panel
        if ($user->hasRole('admin')) {
            return redirect()->intended(route('filament.admin.pages.dashboard'));
        }

        // Redirect regular users to dashboard
        return redirect()->intended(route('filament.dashboard.pages.dashboard'));
    }
}
