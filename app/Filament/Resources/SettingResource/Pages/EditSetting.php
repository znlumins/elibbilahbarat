<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Models\Setting;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    public function getTitle(): string { return 'Update Pengaturan Denda & QRIS'; }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Menggunakan Section dengan columns(1) agar input tersusun vertikal (ke bawah)
                Section::make('Konfigurasi Universal')
                    ->description('Atur nilai denda dan upload QRIS pembayaran di sini.')
                    ->schema([
                        TextInput::make('fine_value')
                            ->label('Denda Telat Per Hari')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->default(fn() => Setting::where('key', 'fine_per_day')->first()?->value),

                        FileUpload::make('qris_path')
                            ->label('Foto QRIS Perpus')
                            ->image()
                            ->directory('settings')
                            // Memastikan file upload lebarnya pas
                            ->imageEditor()
                            ->default(fn() => Setting::where('key', 'qris_image')->first()?->value),
                    ])
                    ->columns(1), // INI KUNCINYA: Memaksa elemen di bawahnya satu baris satu baris
            ]);
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Update Nilai Denda
        Setting::updateOrCreate(
            ['key' => 'fine_per_day'], 
            ['value' => $data['fine_value'], 'label' => 'Denda Per Hari', 'type' => 'number']
        );
        
        // Update Path QRIS (Hanya jika user mengunggah foto baru)
        if (!empty($data['qris_path'])) {
            Setting::updateOrCreate(
                ['key' => 'qris_image'], 
                ['value' => $data['qris_path'], 'label' => 'QRIS Perpus', 'type' => 'image']
            );
        }

        Notification::make()
            ->title('Pengaturan Berhasil Disimpan')
            ->success()
            ->send();

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        // Setelah simpan, balik ke halaman Manajemen Denda (Index)
        return $this->getResource()::getUrl('index');
    }

    // Menghilangkan tombol "Delete" di halaman edit agar setting tidak terhapus tidak sengaja
    protected function getHeaderActions(): array
    {
        return [];
    }
}