<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class NotificationController extends BaseApiController
{
    /**
     * Nampilin semua notifikasi punya si user (admin) yang lagi login
     */
    public function index(Request $request)
    {
        // Pake fitur trait Notifiable bawaan Laravel dari model User
        $user = $request->user();
        
        $notifications = $user->notifications()->paginate(10);
        $unreadCount = $user->unreadNotifications()->count();

        return $this->success([
            'unread_count'  => $unreadCount,
            'notifications' => $notifications
        ], 'Notifikasi berhasil ditarik bro.');
    }

    /**
     * Tandain 1 notifikasi udah dibaca
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return $this->success(null, 'Mantap, notifikasi udah ditandai dibaca.');
        }

        return $this->error('Notifikasi gak ketemu bro!', 404);
    }

    /**
     * Tandain semua notifikasi udah dibaca (Clear All)
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        
        return $this->success(null, 'Semua notifikasi udah dibersihin!');
    }
}