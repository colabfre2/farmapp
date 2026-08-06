<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set konfigurasi Midtrans langsung dari env untuk menghindari cache services
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function getSnapToken(Order $order)
    {
        // Jika status masih pending, kita buatkan token baru dengan suffix unik agar tidak kena error "already been taken"
        $grossAmount = (int) $order->total_amount;

        $params = [
            'transaction_details' => [
                // Tambahkan uniqid() agar setiap kali klik bayar ulang, order_id di Midtrans selalu fresh & unik
                'order_id' => $order->order_number . '-' . $order->id . '-' . substr(uniqid(), -4),
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $order->shipping_name ?? 'Buyer FarmApp',
                'email' => $order->user->email ?? 'buyer@farmapp.test',
                'phone' => $order->shipping_phone ?? '08123456789',
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            
            $order->update([
                'snap_token' => $snapToken,
                'payment_status' => 'pending'
            ]);

            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Midtrans Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ── WEBHOOK / NOTIFICATION HANDLER DARI MIDTRANS ──────────
    public function notification(Request $request)
    {
        $payload = $request->all();
        
        Log::info('WEBHOOK HIT RECEIVED:', $payload);

        $orderIdFull = $payload['order_id'] ?? ''; // Format: ORD-ZHK2RH80-5-ab12

        // ── FITUR PENCEGAT TEST DUMMY MIDTRANS BIAR GAK 404 🔥 ──
        if (str_contains($orderIdFull, 'payment_notif_test')) {
            return response()->json(['message' => 'Webhook test berhasil bro! Mantap!'], 200);
        }
        // ────────────────────────────────────────────────────────

        $statusCode  = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? 'accept';

        // Validasi Signature Key untuk keamanan
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $mySignature = hash("sha512", $orderIdFull . $statusCode . $grossAmount . $serverKey);

        if ($mySignature !== $signatureKey) {
            Log::warning('WEBHOOK SIGNATURE FAILED:', ['expected' => $mySignature, 'sent' => $signatureKey]);
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        // Ambil ID asli order dari string order_id (Format: ORD-XXXX-ID-UNIQ)
        // Karena ID order ada di bagian sebelum 4 karakter terakhir, kita explode pakai dash (-)
        $exploded = explode('-', $orderIdFull);
        // Ambil elemen kedua dari belakang sebagai ID order asli (Contoh: ORD(0) - DED7WPRI(1) - 5(2) - ab12(3))
        $orderId = count($exploded) >= 3 ? $exploded[count($exploded) - 2] : end($exploded);

        Log::info('LOOKING FOR ORDER ID IN DB:', ['order_id' => $orderId]);

        $order = Order::find($orderId);
        if (!$order) {
            Log::error('WEBHOOK ORDER NOT FOUND:', ['order_id' => $orderId]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Mapping status transaksi dari Midtrans ke database FarmApp
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $order->payment_status = 'challenge';
                $order->save();
            } else if ($fraudStatus == 'accept') {
                $order->payment_status = 'success';
                $order->status = 'Processing';
                $order->save();
            }
        } else if ($transactionStatus == 'settlement') {
            // EKSEKUSI UTAMA SAAT PEMBAYARAN BERHASIL DI SIMULATOR
            $order->payment_status = 'success';
            $order->status = 'Processing';
            $order->save();
        } else if ($transactionStatus == 'pending') {
            $order->payment_status = 'pending';
            $order->save();
        } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            $order->payment_status = 'failed';
            $order->status = 'Cancelled';
            $order->save();
        }

        Log::info('ORDER STATUS UPDATED SUCCESSFULLY:', ['order_id' => $order->id, 'new_payment_status' => $order->payment_status, 'new_order_status' => $order->status]);

        return response()->json(['message' => 'Notification successfully processed']);
    }
}