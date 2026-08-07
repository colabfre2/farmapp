<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_schedules', function (Blueprint $table) {
            $table->id();
            $table->time('time');                 // Jam pemberian pakan, misal 07:00:00
            $table->string('label')->nullable();   // Opsional: "Pagi", "Sore", dll
            $table->boolean('is_active')->default(true);
            $table->date('last_notified_at')->nullable(); // Cegah notifikasi dobel di hari yang sama
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_schedules');
    }
};