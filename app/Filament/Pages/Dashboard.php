<?php

namespace App\Filament\Pages;

use App\Models\Loan;
use App\Models\User;
use App\Models\Book;
use App\Models\Category;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Response;

class Dashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadFullReport')
                ->label('Download Laporan Keseluruhan')
                ->icon('heroicon-o-document-chart-bar')
                ->color('success')
                ->action(function () {
                    // 1. Data Inventaris (Semua Buku & Kategori)
                    $books = Book::with('category')->get();
                    $categories = Category::withCount('books')->get();

                    // 2. Data Denda Lunas
                    $data_lunas = Loan::where('is_paid', true)
                        ->with(['user', 'book'])
                        ->get();
                    
                    // 3. Setting denda per hari
                    $fine_per_day = Setting::where('key', 'fine_per_day')->first()->value ?? 0;
                    
                    // 4. Data Piutang (Belum Bayar / Masih Dipinjam & Telat)
                    $data_hutang = Loan::where('is_paid', false)
                        ->where(function ($q) {
                            $q->where('additional_fine', '>', 0)
                              ->orWhere(function($sq) {
                                  $sq->whereNotNull('return_date')
                                     ->whereColumn('return_date', '>', 'due_date');
                              })
                              ->orWhere(function($sq) {
                                  $sq->whereNull('return_date')
                                     ->where('due_date', '<', now());
                              });
                        })
                        ->with(['user', 'book'])
                        ->get();

                    // 5. Top 10 Siswa Teraktif (Sesuaikan role ke 'student')
                    $top_borrowers = User::where('role', 'student')
                        ->withCount('loans')
                        ->orderBy('loans_count', 'desc')
                        ->limit(10)
                        ->get();

                    // --- LOGIC PEMANGGILAN VIEW ---
                    $viewName = view()->exists('reports.fines') ? 'reports.fines' : 'fines';

                    $pdf = Pdf::loadView($viewName, [ 
                        'books'         => $books,
                        'categories'    => $categories,
                        'data_lunas'    => $data_lunas,
                        'data_hutang'   => $data_hutang,
                        'fine_per_day'  => $fine_per_day,
                        'top_borrowers' => $top_borrowers,
                    ]);

                    $pdf->setPaper('a4', 'portrait');

                    return response()->streamDownload(
                        fn() => print($pdf->output()), 
                        'LAPORAN_PERPUS_LENGKAP_' . date('Y-m-d') . '.pdf'
                    );
                }),
        ];
    }
}