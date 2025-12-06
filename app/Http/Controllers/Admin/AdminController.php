<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        if (!Auth::check() || Auth::user()->email !== 'admin@luminpark.com') {
            return redirect()->route('admin.login');
        }

        $totalProducts = Produk::count();
        $availableProducts = Produk::where('is_available', true)->count();
        $soldProducts = Produk::where('is_available', false)->count();

        $pendingPayments = Payment::where('status', 'pending')->count();
        $totalRevenue = Payment::where('status', 'approved')->sum('amount');

        $recentPayments = Payment::with('produk')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'availableProducts',
            'soldProducts',
            'pendingPayments',
            'totalRevenue',
            'recentPayments'
        ));
    }
}
