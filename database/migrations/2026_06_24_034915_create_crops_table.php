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
        Schema::create('crops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('crop_type_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->date('planted_at');
            $table->date('expected_harvest_at');
            $table->date('actual_harvest_at')->nullable();
            $table->enum('status', ['Bibit', 'Pertumbuhan', 'Dipanen'])->default('Bibit');
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
        Schema::dropIfExists('crops');
    }
};
