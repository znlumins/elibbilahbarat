<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationLabel = 'Buku';
    protected static ?string $navigationGroup = 'MASTER DATA';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Buku')->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Judul Buku')
                        ->required()
                        ->placeholder('Masukkan judul lengkap buku'),
                    
                    Forms\Components\TextInput::make('author')
                        ->label('Pengarang')
                        ->required(),

                    // Dropdown Kategori dengan fitur tambah data langsung
                    Forms\Components\Select::make('category_id')
                        ->label('Kategori')
                        ->relationship('category', 'name') 
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            // Form ini muncul saat menekan tombol "+"
                            // Tidak menyertakan slug karena sudah dihapus
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Kategori Baru')
                                ->required()
                                ->unique('categories', 'name'),
                        ]),

                    Forms\Components\TextInput::make('year')
                        ->label('Tahun Terbit')
                        ->numeric()
                        ->minValue(1900)
                        ->maxValue(date('Y')),

                    Forms\Components\TextInput::make('stock')
                        ->label('Stok Buku')
                        ->numeric()
                        ->default(1)
                        ->required(),

                    Forms\Components\FileUpload::make('cover')
                        ->label('Sampul Buku')
                        ->image()
                        ->directory('book-covers')
                        ->imageEditor()
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('description')
                        ->label('Sinopsis/Deskripsi')
                        ->placeholder('Tuliskan ringkasan cerita buku di sini...')
                        ->columnSpanFull(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                    ->label('Sampul')
                    ->circular(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Book $record): string => $record->author),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge() 
                    ->color('primary'),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => $state < 5 ? 'danger' : 'success')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Filter Kategori')
                    ->relationship('category', 'name')
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
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}