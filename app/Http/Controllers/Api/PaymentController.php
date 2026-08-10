<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends BaseApiController
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    // GET /orders/{order}/snap-token
    public function getSnapToken(Order $order)
    {
        // Buyer hanya boleh generate token untuk order miliknya sendiri
        if (Auth::id() !== $order->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Anda tidak berhak mengakses pembayaran order ini.');
        }

        $grossAmount = (int) $order->total_amount;

        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number . '-' . $order->id . '-' . substr(uniqid(), -4),
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $order->shipping_name ?? 'Buyer FarmApp',
                'email'      => $order->user->email ?? 'buyer@farmapp.test',
                'phone'      => $order->shipping_phone ?? '08123456789',
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            $order->update([
                'snap_token'     => $snapToken,
                'payment_status' => 'pending',
            ]);

            return $this->success(
                ['snap_token' => $snapToken],
                'Snap token berhasil dibuat.'
            );
        } catch (\Exception $e) {
            return $this->error('Midtrans Error: ' . $e->getMessage(), 500);
        }
    }
}
