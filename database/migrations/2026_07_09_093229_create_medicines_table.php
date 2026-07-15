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
        Schema::create('medicines', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('type'); // antibiotik, vitamin, vaksin, dll
    $table->foreignId('unit_id')->nullable()->constrained('units');
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
        Schema::dropIfExists('medicines');
    }
};
