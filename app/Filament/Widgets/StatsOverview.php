<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Book;
use App\Models\Loan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // Hitung User yang rolenya 'siswa' (atau sesuaikan dengan string di DB-mu)
            Stat::make('ANGGOTA', User::where('role', '!=', 'admin')->count()) 
                ->description('Total Siswa Terdaftar')
                ->color('info'),

            // Hitung Admin secara spesifik
            Stat::make('PETUGAS', User::where('role', 'admin')->count()) 
                ->description('Admin Perpustakaan')
                ->color('primary'),

            Stat::make('BUKU', Book::count())
                ->description('Total Koleksi Judul')
                ->color('warning'),

            // Hitung peminjaman yang sedang berlangsung (belum kembali)
            Stat::make('PEMINJAMAN AKTIF', Loan::whereNull('return_date')->count())
                ->description('Buku yang masih dibawa siswa')
                ->color('danger'),
        ];
    }
}