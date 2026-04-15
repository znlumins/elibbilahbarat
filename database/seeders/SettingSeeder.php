<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat pengaturan denda default Rp 500 per hari
        Setting::updateOrCreate(
            ['key' => 'fine_per_day'], // Cek berdasarkan key ini
            [
                'label' => 'Denda Per Hari',
                'value' => '500',
                'type' => 'number',
            ]
        );
    }
}