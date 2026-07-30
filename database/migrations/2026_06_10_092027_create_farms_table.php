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
            Schema::create('farms', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // cth: Blok A, Petak 3
        $table->decimal('area_size', 10, 2)->nullable();
        $table->string('area_unit')->default('m²');
        $table->text('description')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};
