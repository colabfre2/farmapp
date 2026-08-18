<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Order;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized  = config('services.midtrans.is_sanitized', true);
        Config::$is3ds        = config('services.midtrans.is_3ds', true);
    }

    /**
     * Create Midtrans Snap Transaction
     */
    public function createTransaction(Order $order)
    {
        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number . '-' . $order->id,
                'gross_amount' => (int) $order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->shipping_name ?? 'Buyer FarmApp',
                'email'      => $order->user->email ?? 'buyer@farmapp.test',
                'phone'      => $order->shipping_phone ?? '08123456789',
            ],
            'shipping_address' => [
                'first_name'   => $order->shipping_name,
                'phone'        => $order->shipping_phone,
                'address'      => $order->shipping_address,
                'city'         => $order->shipping_city,
                'country_code' => 'IDN'
            ],
        ];

        return Snap::createTransaction($params);
    }

    /**
     * Generate Midtrans Snap Token
     */
    public function createSnapToken(Order $order): string
    {
        return $this->createTransaction($order)->token;
    }
}
