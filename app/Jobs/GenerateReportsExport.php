<?php

namespace App\Jobs;

use App\Exports\ReportsExport;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class GenerateReportsExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    public $tries = 3;

    protected $date1;
    protected $date2;
    protected $userId;
    protected $areaId;
    protected $staffIdCard;
    protected $cacheKey;
    protected $filePath;
    protected $currentUserId;

    public function __construct(
        $date1,
        $date2,
        $userId,
        $areaId,
        $staffIdCard,
        $cacheKey,
        $filePath,
        $currentUserId
    ) {
        $this->date1 = $date1;
        $this->date2 = $date2;
        $this->userId = $userId;
        $this->areaId = $areaId;
        $this->staffIdCard = $staffIdCard;
        $this->cacheKey = $cacheKey;
        $this->filePath = $filePath;
        $this->currentUserId = $currentUserId;
    }

    public function handle()
    {
        /*
        |--------------------------------------------------------------------------
        | If file already exists
        |--------------------------------------------------------------------------
        */
        if (Storage::disk('local')->exists($this->filePath)) {

            Cache::put(
                $this->cacheKey,
                [
                    'status' => 'completed',
                    'file_path' => $this->filePath,
                ],
                now()->addHours(24)
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Mark processing
        |--------------------------------------------------------------------------
        */
        Cache::put(
            $this->cacheKey,
            [
                'status' => 'processing',
                'file_path' => $this->filePath,
            ],
            now()->addHours(24)
        );

        try {

            /*
            |--------------------------------------------------------------------------
            | Get staff ID card
            |--------------------------------------------------------------------------
            */
            $staffIdCard = $this->staffIdCard;

            if ($this->userId && !$staffIdCard) {

                $staffIdCard = User::where(
                    'id',
                    $this->userId
                )->value('staff_id_card');
            }

            /*
            |--------------------------------------------------------------------------
            | Generate Excel
            |--------------------------------------------------------------------------
            */
            Excel::store(
                new ReportsExport(
                    $this->date1,
                    $this->date2,
                    $this->userId,
                    $this->areaId,
                    $staffIdCard,
                    $this->currentUserId
                ),
                $this->filePath,
                'local'
            );

            /*
            |--------------------------------------------------------------------------
            | Mark completed
            |--------------------------------------------------------------------------
            */
            Cache::put(
                $this->cacheKey,
                [
                    'status' => 'completed',
                    'file_path' => $this->filePath,
                ],
                now()->addHours(24)
            );

        } catch (Throwable $e) {

            Cache::put(
                $this->cacheKey,
                [
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                ],
                now()->addHours(1)
            );

            Log::error('Reports Export Failed', [
                'error' => $e->getMessage(),
                'user_id' => $this->userId,
            ]);

            throw $e;
        }
    }
}
