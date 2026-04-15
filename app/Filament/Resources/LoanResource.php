<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Models\Loan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    // --- PERUBAHAN LABEL NAVIGASI ---
    protected static ?string $navigationLabel = 'Peminjaman Buku';
    protected static ?string $navigationGroup = 'MASTER DATA';
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?int $navigationSort = 4;
    
    // Mengganti judul di halaman (Plural & Singular)
    protected static ?string $pluralLabel = 'Peminjaman Buku';
    protected static ?string $modelLabel = 'Peminjaman Buku';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Peminjaman')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name', fn ($query) => $query->where('role', 'student'))
                            ->label('Nama Siswa')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('book_id')
                            ->relationship('book', 'title')
                            ->label('Buku yang Dipinjam')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Section::make('Waktu Pengembalian')
                    ->schema([
                        DatePicker::make('loan_date')
                            ->label('Tanggal Pinjam')
                            ->default(now())
                            ->required()
                            ->native(false),
                        
                        DatePicker::make('due_date')
                            ->label('Batas Pengembalian') 
                            ->default(now()->addDays(7))
                            ->required()
                            ->native(false),

                        DatePicker::make('return_date')
                            ->label('Tanggal Buku Kembali')
                            ->native(false)
                            ->placeholder('Belum dikembalikan'),

                        Select::make('status')
                            ->options([
                                'borrowed' => 'Masih Dipinjam',
                                'returned' => 'Sudah Kembali',
                            ])
                            ->label('Status')
                            ->default('borrowed')
                            ->required()
                            ->native(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Siswa')
                    ->searchable(),
                TextColumn::make('book.title')
                    ->label('Buku')
                    ->limit(30),
                TextColumn::make('loan_date')
                    ->label('Tgl Pinjam')
                    ->date('d/m/Y'),
                TextColumn::make('due_date')
                    ->label('Batas Kembali')
                    ->date('d/m/Y')
                    ->color(fn ($state, $record) => 
                        $record->status === 'borrowed' && now()->gt($state) ? 'danger' : 'gray'
                    )
                    ->weight('bold'),
                
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'borrowed' => 'warning',
                        'returned' => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'borrowed' => 'Dipinjam',
                        'returned' => 'Selesai',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'borrowed' => 'Dipinjam',
                        'returned' => 'Selesai',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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