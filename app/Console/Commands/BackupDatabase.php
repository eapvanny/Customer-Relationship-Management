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
        $externalBackupPath = "D:/mysql_backup(CRM)/{$filename}";
        $friendSharedFolder = "\\\\LAPTOP-VSOKHENG\\CRM-Backup";
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

        // Step 4: Copy to your own D: drive
        if (!file_exists(dirname($externalBackupPath))) {
            mkdir(dirname($externalBackupPath), 0755, true);
        }

        if (copy($localBackupPath, $externalBackupPath)) {
            $this->info("Backup copied to your D: drive: {$externalBackupPath}");
        } else {
            $this->error("Failed to copy to your D: drive");
        }

        // Step 5: Copy to your friend's shared folder (on D:)
        if (is_dir($friendSharedFolder)) {
            if (copy($localBackupPath, $friendSharedPath)) {
                $this->info("Backup copied to friend's shared folder: {$friendSharedPath}");
            } else {
                $this->error("Copy failed — file might be locked or access denied");
            }
        } else {
            $this->error("Shared folder not found — check if {$friendSharedFolder} is accessible");
        }

        // Step 6: Clean up old backups
        $this->cleanOldBackups(storage_path('app/backups'));
        $this->cleanOldBackups('D:/mysql_backup(CRM)');
        $this->cleanOldBackups($friendSharedFolder);
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
