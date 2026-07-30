<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harvests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // WAJIB restrictOnDelete untuk menjaga integritas data transaksi
            $table->foreignId('crop_id')->constrained('crops')->restrictOnDelete();
            $table->date('harvested_at');
            $table->decimal('quantity', 10, 2);
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            
            $table->decimal('selling_price', 15, 2); // Dibesarkan digitnya buat nominal uang Rupiah
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harvests');
    }
};