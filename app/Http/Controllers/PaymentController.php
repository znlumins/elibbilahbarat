<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'proof_file' => 'required|image|max:2048',
            'payment_method' => 'required'
        ]);

        $loan = Loan::findOrFail($request->loan_id);

        // Jika siswa upload ulang (edit), hapus foto lama agar tidak nyampah di storage
        if ($loan->payment_proof) {
            Storage::disk('public')->delete($loan->payment_proof);
        }

        $path = $request->file('proof_file')->store('payment-proofs', 'public');

        $loan->update([
            'payment_method' => $request->payment_method,
            'payment_proof' => $path,
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil dikirim/diperbarui!');
    }
}