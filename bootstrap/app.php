<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',          // opsional (biarkan ada meski kosong)
        commands: __DIR__ . '/../routes/console.php', // opsional
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
    $middleware->alias([
        'auth.guard' => \App\Http\Middleware\AuthGuard::class,
    ]);


})

    ->withSchedule(function (Schedule $schedule) {
        // Jadwal harian: update status kontrak (ACTIVE / SIGNED / EXPIRED / WAITING_SIGN / DRAFT)
        $schedule->call(function () {
            $today = \Carbon\Carbon::today();

            $rows = \Illuminate\Support\Facades\DB::table('contracts')->get();
            foreach ($rows as $c) {
                $start = \Carbon\Carbon::parse($c->start_date);
                $end   = \Carbon\Carbon::parse($c->end_date);

                $hrSigned  = \Illuminate\Support\Facades\DB::table('contract_signatures')
                                ->where(['contract_id' => $c->id, 'signer_role' => 'HR'])
                                ->whereNotNull('signed_at')
                                ->exists();

                $empSigned = \Illuminate\Support\Facades\DB::table('contract_signatures')
                                ->where(['contract_id' => $c->id, 'signer_role' => 'Employee'])
                                ->whereNotNull('signed_at')
                                ->exists();

                $status = $c->status;
                if ($hrSigned && $empSigned) {
                    if ($today->between($start, $end)) {
                        $status = 'ACTIVE';
                    } elseif ($today->lt($start)) {
                        $status = 'SIGNED';
                    } else {
                        $status = 'EXPIRED';
                    }
                } else {
                    $status = $c->pdf_path ? 'WAITING_SIGN' : 'DRAFT';
                }

                if ($status !== $c->status) {
                    \Illuminate\Support\Facades\DB::table('contracts')
                        ->where('id', $c->id)
                        ->update(['status' => $status, 'updated_at' => now()]);
                }
            }
        })->dailyAt('01:10');

        // Tambahkan jadwal lain di sini bila perlu
        // $schedule->job(new \App\Jobs\SendContractReminderJob)->dailyAt('07:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom exception/reporting jika perlu
    })
    ->create();
