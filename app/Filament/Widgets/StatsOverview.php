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
            Stat::make('ANGGOTA', User::where('role', '!=', 'admin')->count()) 
                ->description('Total Siswa Terdaftar'),
            Stat::make('PETUGAS', User::where('role', 'admin')->count()) 
                ->description('Admin Perpustakaan'),
            Stat::make('BUKU', Book::count())
                ->description('Total Koleksi Judul'),
            Stat::make('PEMINJAMAN AKTIF', Loan::whereNull('return_date')->count())
                ->description('Buku yang masih dibawa siswa'),
        ];
    }
}