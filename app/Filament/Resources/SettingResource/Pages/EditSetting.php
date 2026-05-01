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

    // 1. Mengambil data dari DB agar muncul di form saat halaman dibuka
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['fine_value'] = Setting::where('key', 'fine_per_day')->first()?->value;
        $data['qris_path'] = Setting::where('key', 'qris_image')->first()?->value;
        
        return $data;
    }

    // 2. Menampilkan Form
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Konfigurasi Universal')
                    ->description('Atur nilai denda dan upload QRIS pembayaran di sini.')
                    ->schema([
                        TextInput::make('fine_value')
                            ->label('Denda Telat Per Hari')
                            ->numeric()
                            ->required()
                            ->prefix('Rp'),

                        FileUpload::make('qris_path')
                            ->label('Foto QRIS Perpus')
                            ->image()
                            ->directory('settings')
                            ->imageEditor(),
                    ])
                    ->columns(1),
            ]);
    }

    // 3. Menyimpan data kembali ke database (Key-Value)
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // Update Nilai Denda
        Setting::updateOrCreate(
            ['key' => 'fine_per_day'], 
            ['value' => $data['fine_value'], 'label' => 'Denda Per Hari', 'type' => 'number']
        );
        
        // Update Path QRIS
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
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}