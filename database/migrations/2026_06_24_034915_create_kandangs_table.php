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
        Schema::create('kandangs', function (Blueprint $table) {
            $table->id();
            // Setiap kandang terikat ke 1 jenis hewan tertentu, mengikuti pola crop_varieties -> crop_types
            $table->foreignId('livestock_type_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // cth: Kandang Ayam #1, Kolam Bebek A
            $table->integer('capacity')->nullable(); // kapasitas maksimal ekor
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kandangs');
    }
};