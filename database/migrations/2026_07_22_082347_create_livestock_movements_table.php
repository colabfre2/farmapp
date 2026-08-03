<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livestock_id')->constrained('livestocks')->cascadeOnDelete();
            $table->foreignId('kandang_id')->nullable()->constrained('kandangs')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['in', 'out', 'transfer']); // 'transfer' = pindah kandang
            $table->integer('quantity');
            $table->date('date');
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_movements');
    }
};