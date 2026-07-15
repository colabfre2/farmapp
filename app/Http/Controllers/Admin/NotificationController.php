<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);
        auth()->user()->unreadNotifications->markAsRead();
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', 'Semua notifikasi telah dibaca!');
    }

    public function destroy($id)
    {
        auth()->user()->notifications()->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Notifikasi dihapus!');
    }
}