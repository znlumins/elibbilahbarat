<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Widgets\ChartWidget;

class IncomeChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Peminjaman (7 Hari Terakhir)';
    protected static ?int $sort = 2;

    // Kunci tinggi chart agar sejajar dengan tabel
    protected static ?string $maxHeight = '300px';

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected function getData(): array
    {
        $data = []; $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d M');
            $data[] = Loan::whereDate('loan_date', $date->format('Y-m-d'))->count();
        }
        return [
            'datasets' => [
                [
                    'label' => 'Buku Dipinjam', 
                    'data' => $data, 
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ]
            ],
            'labels' => $labels,
        ];
    }
    protected function getType(): string { return 'line'; }
}