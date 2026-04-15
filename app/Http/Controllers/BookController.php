<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
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

        // Kita panggil relasi category-nya biar gak error di view
        $books = $query->with('category')->latest()->get();

        return view('welcome', compact('books'));
    }

    // --- FUNGSI SHOW UNTUK DETAIL BUKU (SUDAH DITAMBAHKAN) ---
    public function show($id)
    {
        // Ambil data buku berdasarkan ID, sertakan relasi kategori dan riwayat pinjamnya
        $book = Book::with(['category', 'loans.user'])->findOrFail($id);
        
        return view('book-detail', compact('book'));
    }

    // Fungsi Riwayat Pinjam Siswa
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

        return view('my-loans', compact('loans'));
    }
}