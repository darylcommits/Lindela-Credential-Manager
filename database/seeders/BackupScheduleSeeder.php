<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BackupSchedule;

class BackupScheduleSeeder extends Seeder
{
    public function run()
    {
        BackupSchedule::create([
            'name' => 'Daily Credentials Backup',
            'frequency' => 'daily',
            'scheduled_time' => '02:00:00',
            'backup_types' => ['credentials', 'logs'],
            'storage_path' => 'backups/daily',
            'encrypt_backup' => true,
            'retention_days' => 30,
            'next_run_at' => now()->addDay()->setTime(2, 0, 0),
            'is_active' => true,
        ]);

        BackupSchedule::create([
            'name' => 'Weekly System Backup',
            'frequency' => 'weekly',
            'scheduled_time' => '01:00:00',
            'backup_types' => ['credentials', 'logs', 'system'],
            'storage_path' => 'backups/weekly',
            'encrypt_backup' => true,
            'retention_days' => 90,
            'next_run_at' => now()->addWeek()->setTime(1, 0, 0),
            'is_active' => true,
        ]);
    }
}
