<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('produk')->latest();

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(15);

        return view('admin.payment.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load('produk');
        return view('admin.payment.show', compact('payment'));
    }

    public function approve(Payment $payment)
    {
        $payment->update([
            'status' => 'approved',
            'approved_by' => 'admin@luminpark.com',
            'approved_at' => now()
        ]);

        // If payment is for a property, optionally mark it as sold
        if ($payment->produk) {
            $payment->produk->update(['is_available' => false]);
        }

        return redirect()->route('admin.payment.index')
            ->with('success', 'Payment approved successfully');
    }

    public function reject(Payment $payment)
    {
        $payment->update([
            'status' => 'rejected',
            'approved_by' => 'admin@luminpark.com',
            'approved_at' => now()
        ]);

        return redirect()->route('admin.payment.index')
            ->with('success', 'Payment rejected successfully');
    }

    public function updateNotes(Request $request, Payment $payment)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $payment->update([
            'admin_notes' => $request->admin_notes
        ]);

        return redirect()->route('admin.payment.show', $payment)
            ->with('success', 'Admin notes updated successfully');
    }
}
