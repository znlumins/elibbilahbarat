<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::table('loans', function (Blueprint $table) {
        $table->integer('additional_fine')->default(0); // Untuk denda rusak/hilang
        $table->string('fine_reason')->nullable(); // Alasan: "Buku Rusak" atau "Buku Hilang"
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            //
        });
    }
};
