<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    public function getTitle(): string { return 'Manajemen Denda'; }

    protected function getHeaderActions(): array
    {
        // Ambil data setting pertama sebagai target redirect
        $setting = Setting::first();

        return [
            Action::make('setFineAndQRIS')
                ->label('Set Denda & QRIS Universal')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('primary')
                // REDIRECT: Pindah halaman ke form edit
                ->url(fn () => $setting 
                    ? SettingResource::getUrl('edit', ['record' => $setting->id]) 
                    : '#'
                ),
        ];
    }

    protected function getFooterWidgets(): array 
    { 
        return [ \App\Filament\Widgets\FineHistoryWidget::class ]; 
    }
}