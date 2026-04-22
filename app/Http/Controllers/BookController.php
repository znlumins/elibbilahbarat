<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Setting; // Pastikan Model Setting diimport
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    // Halaman Katalog Utama
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%');
        }

        $books = $query->with('category')->latest()->get();

        return view('welcome', compact('books'));
    }

    // Detail Buku
    public function show($id)
    {
        $book = Book::with(['category', 'loans.user'])->findOrFail($id);
        return view('book-detail', compact('book'));
    }

    // Fungsi Riwayat Pinjam Siswa dengan Pengambilan Data QRIS
    public function myLoans()
    {
        // Pastikan siswa sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        $loans = Loan::where('user_id', Auth::id())
                    ->with('book')
                    ->orderBy('created_at', 'desc')
                    ->get();

        // AMBIL DATA QRIS DARI TABEL SETTINGS [berdasarkan EditSetting.php]
        $qris = Setting::where('key', 'qris_image')->first();

        // Kirim $loans dan $qris ke view
        return view('my-loans', compact('loans', 'qris'));
    }
}