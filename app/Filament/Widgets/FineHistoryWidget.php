<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use App\Models\Setting;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
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

                Tables\Columns\TextColumn::make('hari_telat') // Menggunakan Atribut Model
                    ->label('Telat')
                    ->badge()
                    ->suffix(' Hari')
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('total_denda') // Menggunakan Atribut Model
                    ->label('Total Denda')
                    ->money('IDR')
                    ->color('danger')
                    ->weight('bold')
                    ->description(fn($record) => $record->additional_fine > 0 ? "Ket: " . ($record->fine_reason ?? 'Denda Kerusakan/Hilang') : null),

                Tables\Columns\ImageColumn::make('payment_proof')
                    ->label('Bukti')
                    ->disk('public')
                    ->size(50)
                    // AGAR FOTO BISA DIKLIK FULL SCREEN
                    ->url(fn ($record) => $record->payment_proof ? asset('storage/' . $record->payment_proof) : null)
                    ->openUrlInNewTab()
                    ->placeholder('Belum Upload'),
            ])
            ->actions([
                /* 1. INPUT DENDA KERUSAKAN / HILANG */
                Action::make('addExtraFine')
                    ->label('Denda +')
                    ->icon('heroicon-o-plus-circle')
                    ->color('warning')
                    ->slideOver()
                    ->form([
                        TextInput::make('additional_fine')
                            ->label('Nominal Denda Tambahan')
                            ->helperText('Input jika buku hilang atau rusak (di luar denda telat)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(fn($record) => $record->additional_fine),
                        
                        TextInput::make('fine_reason')
                            ->label('Alasan Denda')
                            ->placeholder('Contoh: Buku Hilang / Sampul Sobek')
                            ->required()
                            ->default(fn($record) => $record->fine_reason),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'additional_fine' => $data['additional_fine'],
                            'fine_reason' => $data['fine_reason'], 
                        ]);

                        Notification::make()
                            ->title('Denda Tambahan Berhasil Dicatat')
                            ->success()
                            ->send();
                    }),

                /* 2. TOMBOL LUNASKAN */
                Action::make('confirmPayment')
                    ->label('Lunaskan')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pelunasan')
                    ->modalDescription('Apakah Anda yakin denda ini sudah dibayar lunas?')
                    ->action(function ($record) {
                        $record->update([
                            'is_paid' => true,
                        ]);

                        Notification::make()->title('Denda Berhasil Dilunaskan!')->success()->send();
                    }),
            ]);
    }
}