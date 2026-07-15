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
       Schema::create('plant_cares', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->enum('type', ['Pupuk', 'Penyiraman', 'Pestisida', 'Pemangkasan', 'Lainnya']);
    $table->string('unit')->nullable(); // kg, liter, ml
    $table->decimal('stock', 10, 2)->default(0);
    $table->decimal('price_per_unit', 10, 2)->default(0);
    $table->text('description')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_cares');
    }
};
