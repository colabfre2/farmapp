<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('Rumah'); // Rumah, Kantor, dll
            $table->string('recipient_name');
            $table->string('phone', 20);
            $table->string('province');
            $table->string('city');
            $table->string('district');
            $table->string('subdistrict')->nullable();
            $table->string('destination_id'); // district_id dari RajaOngkir API v1 Komerce, dipakai buat hitung ongkir
            $table->text('address_detail');   // jalan, no rumah, RT/RW, kelurahan
            $table->string('postal_code', 10)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};