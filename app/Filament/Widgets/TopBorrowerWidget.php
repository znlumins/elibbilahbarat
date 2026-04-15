<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopBorrowerWidget extends BaseWidget
{
    protected static ?string $heading = 'Top Skor Peminjam';
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Kita ambil semua user yang punya minimal 1 pinjaman
                User::query()
                    ->has('loans') 
                    ->withCount('loans')
                    ->orderBy('loans_count', 'desc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Siswa')
                    ->description(fn (User $record): string => "Kelas: {$record->class}"),
                Tables\Columns\TextColumn::make('loans_count')
                    ->label('Total')
                    ->badge()
                    ->color('success')
                    ->suffix(' Buku'),
            ])
            // Menghilangkan pagination bawah supaya hemat tempat
            ->paginated(false);
    }
}