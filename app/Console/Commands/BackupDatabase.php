<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup the database locally and copy to shared folders';

    public function handle()
    {
        $filename = "backup-" . Carbon::now()->format('Y-m-d-H-i-s') . ".sql";
        $localBackupPath = storage_path("app/backups/{$filename}");

        $externalBackupPathD = "D:/mysql_backup(CRM)/{$filename}";
        $externalBackupPathC = "C:/file/{$filename}";

        $friendSharedFolder = "\\\\LAPTOP-VANNY\\test_crm_backup";
        $friendSharedPath = "{$friendSharedFolder}\\{$filename}";

        // Step 1: Ensure local backup directory exists
        if (!file_exists(dirname($localBackupPath))) {
            mkdir(dirname($localBackupPath), 0755, true);
        }

        // Step 2: Locate mysqldump.exe
        $mysqldumpPath = 'D:\Xampp\mysql\bin\mysqldump.exe';
        if (!file_exists($mysqldumpPath)) {
            $mysqldumpPath = 'C:\Xampp\mysql\bin\mysqldump.exe';
        }

        if (!file_exists($mysqldumpPath)) {
            $this->error("mysqldump.exe not found on D: or C: drive.");
            return;
        }

        // Step 3: Run backup command
        $command = sprintf(
            '"%s" --user=%s --password=%s --host=%s %s > "%s"',
            $mysqldumpPath,
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            env('DB_HOST'),
            env('DB_DATABASE'),
            $localBackupPath
        );
        system($command, $returnVar);

        if ($returnVar !== 0) {
            $this->error('Backup failed');
            return;
        }

        $this->info("Backup created locally at: {$localBackupPath}");

        // Step 4: Copy to D: drive if available
        if (file_exists('D:/')) {
            try {
                if (!file_exists(dirname($externalBackupPathD))) {
                    mkdir(dirname($externalBackupPathD), 0755, true);
                }

                if (copy($localBackupPath, $externalBackupPathD)) {
                    $this->info("Backup copied to D: drive: {$externalBackupPathD}");
                } else {
                    $this->error("Failed to copy to D: drive");
                }
            } catch (\Exception $e) {
                $this->error("Error copying to D: drive — {$e->getMessage()}");
            }
        } else {
            $this->warn("D: drive not found. Skipping backup to D:");
        }

        // Step 5: Copy to C: drive
        try {
            if (!file_exists(dirname($externalBackupPathC))) {
                mkdir(dirname($externalBackupPathC), 0755, true);
            }

            if (copy($localBackupPath, $externalBackupPathC)) {
                $this->info("Backup copied to C: drive: {$externalBackupPathC}");
            } else {
                $this->error("Failed to copy to C: drive");
            }
        } catch (\Exception $e) {
            $this->error("Error copying to C: drive — {$e->getMessage()}");
        }

        // Step 6: Copy to friend's shared folder
        if (is_dir($friendSharedFolder)) {
            if (copy($localBackupPath, $friendSharedPath)) {
                $this->info("Backup copied to shared folder: {$friendSharedPath}");
            } else {
                $this->error("Failed to copy to shared folder — file might be locked or access denied");
            }
        } else {
            $this->warn("Shared folder not found or not accessible: {$friendSharedFolder}");
        }

        // Step 7: Clean up old backups
        $this->cleanOldBackups(storage_path('app/backups'));

        if (file_exists('D:/')) {
            $this->cleanOldBackups('D:/mysql_backup(CRM)');
        }

        $this->cleanOldBackups('C:/file');

        if (is_dir($friendSharedFolder)) {
            $this->cleanOldBackups($friendSharedFolder);
        }
    }

    protected function cleanOldBackups($directory)
    {
        $files = glob("{$directory}/*.sql");
        $now = time();
        $daysToKeep = 30;

        foreach ($files as $file) {
            if (is_file($file)) {
                $fileTime = filemtime($file);
                $ageInDays = ($now - $fileTime) / (60 * 60 * 24);

                if ($ageInDays > $daysToKeep) {
                    unlink($file);
                    $this->info("🧹 Deleted old backup: " . basename($file));
                }
            }
        }
    }
}
