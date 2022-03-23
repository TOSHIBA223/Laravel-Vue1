<?php

namespace App\Console;

use App\Exports\AccessControlExport;
use App\Exports\SunshineIncompleteCSV;
use App\Models\QuestionnaireSent;
use App\Models\DeclarationSent;
use App\Models\SystemCrons;
use App\Models\Users;
use App\Notifications\GeneralSummarySunshine;
use App\Notifications\NoncompleteSunshineTest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;

class GetSunshineIncomplete
{
    public function __invoke()
    {
        $sent = new DeclarationSent();
        $data = $sent->getIncompleteSunshineTests();

        $users = [];

        $date = Carbon::now('Australia/Melbourne')->format('Y-m-d');
        $time = Carbon::now('Australia/Melbourne')->format('H:i:s');

        foreach ($data as $key => $user) {

            $user->created_at = Carbon::parse($user->created_at)->format('d/m/Y H:i');

            $users[] = [
                'employee_code' => $user->employee_code,
                'name' => (new Users)->getFullNameByEmployeeCode($user->employee_code)
            ];
        }

        file_put_contents(
            storage_path("logs/sunshine/sunshine_logs" . $date . '.log'),
            $time . ' ' . 'returning data entered after the user completion stats are evaluated' . "\n",
            FILE_APPEND
        );
        file_put_contents(
            storage_path("logs/sunshine/sunshine_logs" . $date . '.log'),
            $time . ' ' . print_r($data, true) . "\n",
            FILE_APPEND
        );

        $file_name = 'sunshine_incomplete_status_' . Carbon::now('Australia/Melbourne')->format('dmY_His') . '.csv';
        Excel::store(
            new SunshineIncompleteCSV($data),
            $file_name,
            'sunshine_local'
        );
        $route = (env('APP_ENV') === 'dev') ?
            env('MAIL_FOR_DEV') :
            env('MAIL_FOR_SUNSHINE');
        $systemCron = SystemCrons::find(1);
        if (!empty($systemCron->email_to)) {
            $route = $systemCron->email_to;
        }
        Notification::route('mail', $route)
            ->notify(new NoncompleteSunshineTest($users, $file_name));
    }
}
