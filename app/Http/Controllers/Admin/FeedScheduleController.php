<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedSchedule;
use Illuminate\Http\Request;

class FeedScheduleController extends Controller
{
    public function index()
    {
        $schedules = FeedSchedule::orderBy('time')->get();
        return view('admin.feed-schedules.index', compact('schedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'time'  => 'required|date_format:H:i',
            'label' => 'nullable|string|max:50',
        ]);

        FeedSchedule::create([
            'time'      => $request->time,
            'label'     => $request->label,
            'is_active' => true,
        ]);

        return redirect()->route('admin.feed-schedules.index')->with('success', 'Jadwal pakan berhasil ditambahkan!');
    }

    public function toggleActive(FeedSchedule $feedSchedule)
    {
        $feedSchedule->update(['is_active' => !$feedSchedule->is_active]);

        return redirect()->route('admin.feed-schedules.index')->with('success', 'Status jadwal berhasil diubah!');
    }

    public function destroy(FeedSchedule $feedSchedule)
    {
        $feedSchedule->delete();

        return redirect()->route('admin.feed-schedules.index')->with('success', 'Jadwal pakan berhasil dihapus!');
    }
}