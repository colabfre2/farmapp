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
        Schema::create('feed_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('feed_id')->constrained()->cascadeOnDelete();
    $table->foreignId('livestock_id')->constrained('livestocks')->cascadeOnDelete();
    $table->decimal('amount', 10, 2);
    $table->date('fed_at');
    $table->enum('time_of_day', ['Pagi', 'Siang', 'Sore', 'Malam']);
    $table->text('notes')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feed_logs');
    }
};
