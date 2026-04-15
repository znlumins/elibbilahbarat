<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction; // Menghubungkan ke tabel transaksi [cite: 6]
use Carbon\Carbon; // Library untuk urusan tanggal

class TransactionController extends Controller
{
    public function kembalikanBuku($id)
    {
        // 1. Cari data peminjaman berdasarkan ID [cite: 7, 8]
        $transaksi = Transaction::findOrFail($id);
        
        // 2. Tentukan variabel waktu [cite: 9]
        $tanggalSekarang = Carbon::now();
        $jatuhTempo = Carbon::parse($transaksi->due_date);
        
        // 3. Logika Perhitungan Denda 
        $denda = 0;
        $tarifPerHari = 1000; // Contoh tarif di SMPN 1 Bilah Barat [cite: 2, 7]

        if ($tanggalSekarang->gt($jatuhTempo)) {
            $selisihHari = $tanggalSekarang->diffInDays($jatuhTempo);
            $denda = $selisihHari * $tarifPerHari; // Rumus: Selisih Hari x Tarif 
        }

        // 4. Update data ke database MySQL [cite: 5, 7]
        $transaksi->update([
            'return_date' => $tanggalSekarang,
            'fine_amount' => $denda,
            'status' => 'returned'
        ]);

        return redirect()->back()->with('status', 'Buku berhasil dikembalikan. Denda: Rp ' . $denda);
    }
}