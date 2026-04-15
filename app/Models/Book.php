<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'author',
        'publisher',
        'year',
        'isbn',
        'stock',
        'cover',
        'description',
    ];

    /**
     * Hubungan ke kategori
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Hubungan ke peminjaman (diurutkan dari yang terbaru)
     */
    public function loans()
    {
        return $this->hasMany(Loan::class)->orderBy('created_at', 'desc');
    }

    /**
     * Logika cek apakah buku tersedia
     * Digunakan di halaman detail untuk menampilkan status 'Tersedia' atau 'Dipinjam'
     */
    public function isAvailable()
    {
        // Buku dianggap tidak tersedia jika sedang dipinjam (status borrowed)
        // Dan stok fisik di database adalah 0
        return $this->stock > 0 && !$this->loans()->where('status', 'borrowed')->exists();
    }
}