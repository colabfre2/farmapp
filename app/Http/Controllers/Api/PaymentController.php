<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends BaseApiController
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
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

    /**
     * Midtrans Notification Webhook
     */
    public function notification(Request $request)
    {
        $payload = $request->all();
        $orderNumberFull = $payload['order_id'] ?? '';

        // Handle test notifications
        if (str_contains($orderNumberFull, 'payment_notif_test')) {
            return response()->json(['message' => 'Webhook test success'], 200);
        }

        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];
        $signatureKey = $payload['signature_key'];
        $serverKey = config('services.midtrans.server_key');

        // Verify Signature
        $localSignature = hash('sha512', $orderNumberFull . $statusCode . $grossAmount . $serverKey);

        if ($localSignature !== $signatureKey) {
            return response()->json(['message' => 'Invalid Signature'], 403);
        }

        // Extract order number (handle potential suffixes)
        $parts = explode('-', $orderNumberFull);
        $orderNumber = $parts[0] . '-' . $parts[1];

        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            return response()->json(['message' => 'Order Not Found'], 404);
        }

        $transactionStatus = $payload['transaction_status'];
        $type = $payload['payment_type'];
        $fraudStatus = $payload['fraud_status'] ?? 'accept';

        if ($transactionStatus == 'capture') {
            if ($type == 'credit_card') {
                if ($fraudStatus == 'challenge') {
                    $order->update(['status' => 'Pending', 'payment_status' => 'challenge']);
                } else {
                    $order->update(['status' => 'Processing', 'payment_status' => 'success']);
                }
            }
        } else if ($transactionStatus == 'settlement') {
            $order->update(['status' => 'Processing', 'payment_status' => 'success']);
        } else if ($transactionStatus == 'pending') {
            $order->update(['payment_status' => 'pending']);
        } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            $oldStatus = $order->status;
            $order->update(['status' => 'Cancelled', 'payment_status' => 'failed']);

            if ($oldStatus !== 'Cancelled') {
                foreach ($order->items as $item) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
        }

        return response()->json(['message' => 'Notification Handled']);
    }
}
