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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->enum('status', ['Pending', 'Processing', 'Shipped', 'Completed', 'Cancelled'])->default('Pending');
            $table->decimal('total_amount', 12, 2);
            // Informasi Penerima & Pengiriman
            $table->string('shipping_name');
            $table->string('shipping_phone', 20);
            $table->text('shipping_address');
            $table->string('shipping_city')->nullable();
            $table->string('shipping_district')->nullable();
            $table->string('province')->nullable();
            $table->string('destination_id')->nullable(); // district_id dari RajaOngkir API v1 Komerce
            
            // Kurir & Ongkir (RajaOngkir)
            $table->string('courier')->nullable();        // jne / jnt / sicepat
            $table->string('courier_service')->nullable(); // REG, YES, dll
            $table->decimal('shipping_cost', 10, 2)->default(0);
            
            // Pembayaran
            $table->enum('payment_method', ['card', 'transfer', 'cod', 'midtrans'])->default('cod');
            
            // KOLOM MIDTRANS (Tanpa after() karena ini buat tabel baru)
            $table->string('payment_status')->default('pending'); // pending, success, failed
            $table->string('snap_token')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};