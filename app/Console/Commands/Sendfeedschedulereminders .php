<?php

namespace App\Console\Commands;

use App\Models\FeedSchedule;
use App\Models\User;
use App\Notifications\DailyReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendFeedScheduleReminders extends Command
{
    protected $signature = 'feed:send-schedule-reminders';

    protected $description = 'Kirim notifikasi ke admin saat jam jadwal pemberian pakan tiba';

    public function handle(): void
    {
        // 1. Pastikan jam mengikuti zona waktu lokal (default Asia/Jakarta jika tidak diset)
        $now       = Carbon::now(config('app.timezone', 'Asia/Jakarta'));
        $today     = $now->toDateString();
        $currentHM = $now->format('H:i');

        $schedules = FeedSchedule::where('is_active', true)->get();

        $matched = $schedules->filter(function ($schedule) use ($currentHM, $today) {
            // 2. Di-parse pakai Carbon dulu karena datanya dari DB berbentuk string (misal "08:00:00")
            $scheduleHM  = Carbon::parse($schedule->time)->format('H:i');
            
            // 3. Parsing juga last_notified_at untuk mencegah error fungsi toDateString() pada string
            $alreadySent = $schedule->last_notified_at 
                           && Carbon::parse($schedule->last_notified_at)->toDateString() === $today;

            return $scheduleHM === $currentHM && !$alreadySent;
        });

        if ($matched->isEmpty()) {
            return; // Tidak ada jadwal yang cocok saat ini, tidak perlu proses lebih lanjut
        }

        $admins = User::where('role', 'admin')->get();

        foreach ($matched as $schedule) {
            $label   = $schedule->label ? " ({$schedule->label})" : '';
            // 4. Parsing juga saat nyetak variabel pesannya
            $jamText = Carbon::parse($schedule->time)->format('H:i');

            foreach ($admins as $admin) {
                $admin->notify(new DailyReminderNotification(
                    title: 'Waktunya Memberi Pakan!',
                    message: 'Sudah waktunya pemberian pakan jadwal ' . $jamText . $label . '. Jangan lupa dicatat di Log Pakan ya!',
                    type: 'feed_schedule'
                ));
            }

            // 5. Simpan waktu saat ini ke database agar tidak terkirim berulang kali di hari yang sama
            $schedule->update(['last_notified_at' => Carbon::now()]);

            $this->info("Notifikasi jadwal pakan jam {$jamText} berhasil dikirim ke " . $admins->count() . ' admin.');
        }
    }
}