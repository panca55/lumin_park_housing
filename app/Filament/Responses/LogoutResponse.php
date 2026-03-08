<?php

namespace App\Filament\Responses;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        // Pastikan user benar-benar logout
        Auth::logout();

        // Invalidate session untuk mencegah session fixation
        $request->session()->invalidate();

        // Regenerate CSRF token untuk keamanan
        $request->session()->regenerateToken();

        // Regenerate session ID
        $request->session()->regenerate();

        return redirect()->to('/')->with('status', 'Successfully logged out.');
    }
}
