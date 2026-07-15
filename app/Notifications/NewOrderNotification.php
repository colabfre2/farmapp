<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'        => 'Pesanan Baru!',
            'message'      => 'Ada pesanan baru dari ' . $this->order->Shipping_name . ' dengan total ' . rupiah($this->order->total_amount),
            'order_number' => $this->order->order_number,
            'order_id'     => $this->order->id,
            'type'         => 'order',
        ];
    }
}