<?php

namespace App\Console;

use App\Exports\AccessControlExport;
use App\Exports\SunshineGeneralCSV;
use App\Models\QuestionnaireSent;
use App\Models\DeclarationSent;
use App\Models\SystemCrons;
use App\Notifications\GeneralSummarySunshine;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;

class GetSunshineGeneralInfo
{
    public function __invoke()
    {
        $sent = new DeclarationSent();
        $data = $sent->getAllSunshineTests();

        $date = Carbon::now('Australia/Melbourne')->format('Y-m-d');
        $time = Carbon::now('Australia/Melbourne')->format('H:i:s');

        $void_ids = [];

        foreach ($data as $key => $user) {
            $void_ids[] = $user->user_id;
            unset($user->user_id);
        }

        file_put_contents(
            storage_path("logs/sunshine/sunshine_logs" . $date . '.log'),
            $time . ' ' . 'Returning General User stats for Daily Declarations' . "\n",
            FILE_APPEND
        );
        file_put_contents(
            storage_path("logs/sunshine/sunshine_logs" . $date . '.log'),
            $time . ' ' . print_r($data, true) . "\n",
            FILE_APPEND
        );
        file_put_contents(
            storage_path("logs/sunshine/sunshine_logs" . $date . '.log'),
            $time . ' ' . print_r($void_ids, true) . "\n",
            FILE_APPEND
        );

        $file_name = 'sunshine_general_status_' . Carbon::now('Australia/Melbourne')->format('dmY_His') . '.csv';
        Excel::store(
            new SunshineGeneralCSV($data),
            $file_name,
            'sunshine_local'
        );

        $route = (env('APP_ENV') === 'dev') ?
            env('MAIL_FOR_DEV') :
            env('MAIL_FOR_SUNSHINE');
        $systemCron = SystemCrons::find(4);
        if (!empty($systemCron->email_to)) {
            $route = $systemCron->email_to;
        }
        Notification::route('mail', $route)
            ->notify(new GeneralSummarySunshine($file_name));
    }
}
