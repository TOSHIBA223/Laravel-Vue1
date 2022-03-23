<?php

namespace App\Console;

use App\Exports\AccessControlExport;
use App\Http\Controllers\Frontend\Imports;
use App\Http\Controllers\Admin\Pages\Settings\Setting;
use App\Http\Controllers\Frontend\QuestionnaireController;
use App\Models\QuestionnaireCompleteSendQueue;
use App\Models\QuestionnaireSent;
use App\Models\SmbSentFiles;
use App\Models\Users;
use App\Models\DeclarationSent;
use App\Notifications\FailedTest;
use App\Notifications\GeneralSummarySunshine;
use App\Notifications\IncorrectAddress;
use App\Notifications\NoncompleteSunshineTest;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Expr\Cast\Object_;
use App\Services\SMBClientService;
use App\Models\Settings;
use App\Models\SetCrons;
use \Datetime;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $reset_address_date_string = Settings::where('id', 31)->first()->value;
        $reset_address_date_string = str_replace('T', ' ', $reset_address_date_string);
        $reset_date = DateTime::createFromFormat('Y-m-d H:i', $reset_address_date_string);
        
        if ($reset_date->diff(new DateTime())->i == 0) {
            Setting::sendResetAddressSMS();
        }

        $getCronData = SetCrons::where('cronenbale', 'Enable')->get();
        foreach ($getCronData as $crons) {
            error_log(date('N', strtotime($crons->cronday)));
            $schedule->call(new RunCronForDeclaration($crons->declaration_id))->WeeklyOn(date('N', strtotime($crons->cronday)), $crons->settime)->timezone('Australia/Melbourne');
        }

        $lock_card_time = Settings::where('id', 17)->first()->value;
        $schedule->call(function () {
            $date = Carbon::now('Australia/Melbourne')->format('Y-m-d');
            $time = Carbon::now('Australia/Melbourne')->format('H:i:s');
            file_put_contents(
                storage_path() . "/logs/smbLog_". $date . '.log',
                $time . ' ' . 'Start of lock cron job' . "\n",
                FILE_APPEND
            );

            error_log("Started cron job for writing csv file for not completed employees");

            $sent = new DeclarationSent();
            $data = $sent->getNightlyCompletionStats();

            $date = Carbon::now('Australia/Melbourne')->format('Y-m-d');
            $time = Carbon::now('Australia/Melbourne')->format('H:i:s');
            file_put_contents(
                storage_path() . "/logs/smbLog_". $date . '.log',
                $time . ' ' . 'returning users for the nightly completion stats' . "\n",
                FILE_APPEND
            );
            file_put_contents(
                storage_path() . "/logs/smbLog_". $date . '.log',
                $time . ' ' . print_r($data, true) . "\n",
                FILE_APPEND
            );

            foreach ($data as $key => $user) {
                $user->complete = 'TRUE';
                unset($user->answers);
            }

            file_put_contents(
                storage_path() . "/logs/smbLog_". $date . '.log',
                $time . ' ' . 'returning data entered after the user completion stats are evaluated' . "\n",
                FILE_APPEND
            );
            file_put_contents(
                storage_path() . "/logs/smbLog_". $date . '.log',
                $time . ' ' . print_r($data, true) . "\n",
                FILE_APPEND
            );

            $file_name = 'ams_false_' . Carbon::now('Australia/Melbourne')->format('dmY_His') . '.csv';
            Excel::store(
                new AccessControlExport($data),
                $file_name
            );

            $smbClient = new SMBClientService();

            $upload_file = $smbClient->uploadTo(
                Storage::disk('local')->path($file_name),
                '/Integriti/' . $file_name
            );
            file_put_contents(
                storage_path() . "/logs/smbLog_". $date . '.log',
                $time . ' ' . $upload_file . "\n",
                FILE_APPEND
            );
        })->dailyAt($lock_card_time)->timezone('Australia/Melbourne');

        $schedule->call(function () {
            $import = new Imports();
            $import->importColac();
            $import->importSunshine();
        })->everyMinute();

        $schedule->call(function () {
            $user_list = new Users();
            $users = $user_list->getAllForSundayIntegriti();
            $date = Carbon::now('Australia/Melbourne')->format('Y-m-d');
            $time = Carbon::now('Australia/Melbourne')->format('H:i:s');

            foreach ($users as $user) {
                $user->status = 'TRUE';
            }
            file_put_contents(
                "storage/logs/colac/integriti_sunday" . $date . '.log',
                $time . ' ' . print_r($users, true) . "\n",
                FILE_APPEND
            );

            $file_name = 'ams_sunday_block_' . Carbon::now('Australia/Melbourne')->format('dmY_His') . '.csv';
            Excel::store(
                new AccessControlExport($users),
                $file_name,
                'colac_local'
            );

            $smbClient = new SMBClientService();
            $upload_file = $smbClient->uploadTo(
                Storage::disk('colac_local')->path($file_name),
                '/Integriti/' . $file_name
            );
        })->weekly()
            ->timezone('Australia/Melbourne');

        $sunshine_incomplete_time = Settings::where('id', 14)->first()->value;
        $sunshine_incomplete_again_time = Settings::where('id', 15)->first()->value;
        $sunshine_incomplete_last_time = Settings::where('id', 16)->first()->value;
        $lock_sunshine_cards_time = Settings::where('id', 17)->first()->value;
        $get_sunshine_general_time = Settings::where('id', 18)->first()->value;
        $get_sunshine_general_again_time = Settings::where('id', 32)->first()->value;
        $schedule->call(new GetSunshineIncomplete)->dailyAt($sunshine_incomplete_time)
            ->weekdays()
            ->timezone('Australia/Melbourne');
        $schedule->call(new GetSunshineIncomplete)->dailyAt($sunshine_incomplete_again_time)
            ->weekdays()
            ->timezone('Australia/Melbourne');
        $schedule->call(new GetSunshineIncomplete)->dailyAt($sunshine_incomplete_last_time)
            ->weekdays()
            ->timezone('Australia/Melbourne');
        $schedule->call(new LockSunshineCards)->dailyAt($lock_sunshine_cards_time)
            ->weekdays()
            ->timezone('Australia/Melbourne');
        $schedule->call(new GetSunshineGeneralInfo)->dailyAt($get_sunshine_general_time)
            ->weekdays()
            ->timezone('Australia/Melbourne');
        $schedule->call(new GetSunshineGeneralInfo)->dailyAt($get_sunshine_general_again_time)
            ->weekdays()
            ->timezone('Australia/Melbourne');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}