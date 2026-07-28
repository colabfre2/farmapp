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
        Schema::create('livestocks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('livestock_type_id')->constrained()->restrictOnDelete();
        $table->foreignId('kandang_id')->nullable()->constrained()->nullOnDelete(); // ← tambah
        $table->string('name'); // tetap ada, tapi jadi nama batch/kelompok, bukan nama kandang
        $table->date('arrival_date')->nullable();
        $table->integer('quantity');
        $table->decimal('avg_weight', 10, 2)->nullable();
        $table->enum('health_status', ['Sehat', 'Pemantauan', 'Sakit'])->default('Sehat');
        $table->text('notes')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livestocks');
    }
};
