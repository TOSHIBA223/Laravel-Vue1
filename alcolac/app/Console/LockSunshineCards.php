<?php
namespace App\Console;

use App\Exports\AccessControlExport;
use App\Exports\SunshineGeneralCSV;
use App\Models\DeclarationSent;
use App\Models\QuestionnaireSent;
use App\Notifications\GeneralSummarySunshine;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;

class LockSunshineCards
{
    public function __invoke()
    {
        $sent = new DeclarationSent();
        $data = $sent->getAllSunshineTests();

        $date = Carbon::now('Australia/Melbourne')->format('Y-m-d');
        $time = Carbon::now('Australia/Melbourne')->format('H:i:s');

        $void_ids = [];

        foreach($data as $key => $user)
        {
            $void_ids[] = $user->user_id;
            unset($user->user_id);
        }

        file_put_contents(
            "storage/logs/sunshine/sunshine_logs". $date . '.log',
            $time . ' ' . 'Returning General User stats for Daily Declarations' . "\n",
            FILE_APPEND
        );
        file_put_contents(
            "storage/logs/sunshine/sunshine_logs". $date . '.log',
            $time . ' ' . print_r($data, true) . "\n",
            FILE_APPEND
        );
        file_put_contents(
            "storage/logs/sunshine/sunshine_logs". $date . '.log',
            $time . ' ' . print_r($void_ids, true) . "\n",
            FILE_APPEND
        );

        $void_update = (new DeclarationSent())->voidOldTests($void_ids);
        file_put_contents(
            "storage/logs/sunshine/sunshine_logs". $date . '.log',
            $time . ' ' . print_r($void_update, true) . "\n",
            FILE_APPEND
        );
    }
}
