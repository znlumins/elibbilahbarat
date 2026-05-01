<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class FineHistoryWidget extends BaseWidget
{
    public static function canView(): bool 
    { 
        return str_contains(request()->url(), 'settings'); 
    }

    protected static ?string $heading = 'Monitoring Denda & Konfirmasi Pembayaran';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Loan::query()->where('is_paid', false))
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Siswa')
                    ->description(fn($record) => "Buku: " . ($record->book->title ?? '-')),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode')
                    ->badge()
                    ->state(function ($record) {
                        return $record->payment_method ?? ($record->payment_proof ? 'QRIS' : 'Tunai');
                    })
                    ->color(fn ($state) => match ($state) {
                        'QRIS', 'qris' => 'info',
                        'Tunai', 'cash', 'Cash' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn ($state) => match ($state) {
                        'QRIS', 'qris' => 'heroicon-m-qr-code',
                        'Tunai', 'cash', 'Cash' => 'heroicon-m-banknotes',
                        default => 'heroicon-m-clock',
                    }),

                Tables\Columns\TextColumn::make('hari_telat') 
                    ->label('Telat')
                    ->badge()
                    ->suffix(' Hari')
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('total_denda')
                    ->label('Total Denda')
                    ->money('IDR')
                    ->color('danger')
                    ->weight('bold'),

                Tables\Columns\ImageColumn::make('payment_proof')
                    ->label('Bukti Siswa')
                    ->disk('public')
                    ->size(50)
                    ->url(fn ($record) => $record->payment_proof ? asset('storage/' . $record->payment_proof) : null)
                    ->openUrlInNewTab()
                    ->placeholder('Tanpa Bukti'),
            ])
            ->actions([
                /* 1. AKSI: TAMBAH DENDA EXTRA */
                Action::make('addExtraFine')
                    ->label('Denda +')
                    ->icon('heroicon-o-plus-circle')
                    ->color('warning')
                    ->slideOver()
                    ->form([
                        TextInput::make('additional_fine')
                            ->label('Nominal Denda Tambahan')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(fn($record) => $record->additional_fine),
                        
                        TextInput::make('fine_reason')
                            ->label('Alasan Denda')
                            ->required()
                            ->default(fn($record) => $record->fine_reason),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'additional_fine' => $data['additional_fine'],
                            'fine_reason' => $data['fine_reason'], 
                        ]);
                        Notification::make()->title('Denda Tambahan Dicatat')->success()->send();
                    }),

                /* 2. AKSI: LUNASKAN (LANGSUNG KONFIRMASI TANPA FORM) */
                Action::make('confirmPayment')
                    ->label('Lunaskan')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation() // Langsung muncul pop-up Yes/No[cite: 1]
                    ->modalHeading('Konfirmasi Pelunasan')
                    ->modalDescription('Apakah Anda yakin denda ini sudah dibayar lunas?')
                    ->action(function ($record) {
                        $record->update([
                            'is_paid' => true,
                            // Jika metode kosong, otomatis isi 'Tunai'[cite: 1]
                            'payment_method' => $record->payment_method ?? 'Tunai', 
                        ]);

                        Notification::make()
                            ->title('Denda Berhasil Dilunaskan!')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}