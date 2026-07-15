<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Livestock;
use App\Models\Crop;
use App\Notifications\DailyReminderNotification;

class SendDailyReminders extends Command
{
    protected $signature   = 'reminders:daily';
    protected $description = 'Kirim reminder harian untuk pakan dan perawatan tanaman';

    public function handle()
    {
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            // Reminder pakan ternak
            $livestockCount = Livestock::count();
            if ($livestockCount > 0) {
                $admin->notify(new DailyReminderNotification(
                    '⏰ Reminder Pakan Ternak',
                    'Jangan lupa memberikan pakan untuk ' . $livestockCount . ' kandang ternak hari ini!',
                    'reminder'
                ));
            }

            // Reminder perawatan tanaman
            $cropCount = Crop::where('status', '!=', 'Harvested')->count();
            if ($cropCount > 0) {
                $admin->notify(new DailyReminderNotification(
                    '⏰ Reminder Perawatan Tanaman',
                    'Ada ' . $cropCount . ' tanaman yang perlu dirawat hari ini!',
                    'reminder'
                ));
            }
        }

        $this->info('Reminder harian berhasil dikirim!');
    }
}