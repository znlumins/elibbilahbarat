<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Models\Loan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Peminjaman';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Peminjaman')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->label('Peminjam'),
                        
                        Forms\Components\Select::make('book_id')
                            ->relationship('book', 'title')
                            ->required()
                            ->label('Buku'),
                        
                        DatePicker::make('due_date')
                            ->label('Tanggal Jatuh Tempo')
                            ->required(),
                            
                        TextInput::make('additional_fine')
                            ->label('Denda Tambahan')
                            ->numeric()
                            ->prefix('Rp'),

                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'qris' => 'QRIS',
                                'transfer' => 'Transfer Bank',
                            ]),

                        FileUpload::make('payment_proof')
                            ->label('Bukti Pembayaran')
                            ->image()
                            ->directory('payment-proofs') // Harus sesuai dengan controller
                            ->disk('public'),
                        
                        Forms\Components\Toggle::make('is_paid')
                            ->label('Status Lunas')
                            ->onColor('success')
                            ->offColor('danger'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Peminjam')->searchable(),
                TextColumn::make('book.title')->label('Buku')->searchable(),
                
                TextColumn::make('payment_method')
                    ->label('Metode')
                    ->badge()
                    ->color('info'),
                
                // Ini bagian penting untuk menampilkan gambar
                ImageColumn::make('payment_proof')
                    ->label('Bukti')
                    ->disk('public') // Sangat penting agar gambar terbaca
                    ->size(80)
                    ->circular()
                    ->simpleLightbox(), // Bisa diklik untuk perbesar
                
                IconColumn::make('is_paid')
                    ->label('Lunas')
                    ->boolean(),
                    
                TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }
}