<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Kategori dengan Slug (Wajib ada biar nggak error)
        $kategori1 = Category::updateOrCreate(
            ['name' => 'Novel'],
        );
        $kategori2 = Category::updateOrCreate(
            ['name' => 'Sains'],
        );
        $kategori3 = Category::updateOrCreate(
            ['name' => 'Sejarah'],
        );

        // 2. Data Buku Tanpa ISBN
        $books = [
            [
                'category_id' => $kategori1->id,
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'publisher' => 'Bentang Pustaka',
                'year' => '2005',
                'stock' => 5,
                'description' => 'Kisah persahabatan sepuluh anak dari keluarga miskin di Belitung yang penuh keterbatasan namun punya mimpi besar.',
            ],
            [
                'category_id' => $kategori1->id,
                'title' => 'Bumi',
                'author' => 'Tere Liye',
                'publisher' => 'Gramedia Pustaka Utama',
                'year' => '2014',
                'stock' => 3,
                'description' => 'Petualangan Raib di dunia paralel yang menakjubkan.',
            ],
            [
                'category_id' => $kategori2->id,
                'title' => 'Cosmos',
                'author' => 'Carl Sagan',
                'publisher' => 'Random House',
                'year' => '1980',
                'stock' => 2,
                'description' => 'Eksplorasi tentang alam semesta dan sejarah sains.',
            ],
            [
                'category_id' => $kategori3->id,
                'title' => 'Sapiens',
                'author' => 'Yuval Noah Harari',
                'publisher' => 'Harper',
                'year' => '2011',
                'stock' => 4,
                'description' => 'Riwayat singkat umat manusia dari zaman batu hingga sekarang.',
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}