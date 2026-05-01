<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'loan_id'        => 'required|exists:loans,id',
            'proof_file'     => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'payment_method' => 'required',
        ]);

        $loan = Loan::findOrFail($request->loan_id);

        // Hapus file lama jika ada
        if ($loan->payment_proof) {
            Storage::disk('public')->delete($loan->payment_proof);
        }

        // Simpan file baru
        $path = $request->file('proof_file')->store('payment-proofs', 'public');

        // Update database
        $loan->update([
            'payment_method' => $request->payment_method,
            'payment_proof'  => $path,
            'is_paid'        => false, // Opsional: reset status jika perlu
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil dikirim!');
    }
}