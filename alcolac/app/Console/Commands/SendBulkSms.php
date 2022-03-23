<?php

namespace App\Console\Commands;

use App\Models\QuestionnaireSent;
use App\Models\SmbSentFiles;
use App\Models\Preference;
use App\Models\Users;
use App\Models\SMSTemplate;
use App\Services\SMS;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Settings;

class SendBulkSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send-daily-bulk-sms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle($group = null)
    {
        $sms = new SMS();
        $users = new Users();
        $friday = Carbon::now('Australia/Melbourne')->format('l') == 'Friday' ?? false;
        $settingsData = Settings::get();
        $settingsData['13']['value'];

        //TODO test this works
        if ($friday === false)
            $userIds = $users->getAllIdsAsArray($group);

        if ($friday === true)
            $userIds = $users->getColacIdsAsArray($group);

        $questionnaire_sent = new QuestionnaireSent();

        if (Carbon::now('Australia/Melbourne')->format('l') == 'Friday') {
            $user_list = $users->getMultipleColacForMessaging($group)->toArray();
        } else {
            $user_list = $users->getMultipleForMessaging($group)->toArray();
        }

        $datetime = new \DateTime(date('Y-m-d H:i:s', strtotime('+1 day')));
        $day_name = $datetime->format('l');
        $day_formatted = $datetime->format('jS');
        $month_formatted = $datetime->format('F');

        $url_base = env('APP_URL');

        if (Carbon::now('Australia/Melbourne')->format('l') == 'Friday') {
            $mesaageTemplate = SMSTemplate::where('id', 5)->first();
            $template = $mesaageTemplate->content;
            $template = str_replace('##day_formatted##', $day_formatted, $template);
            $template = str_replace('##month_formatted##', $month_formatted, $template);
            $template = str_replace('##url_base##', $url_base, $template);
        } else {
            $mesaageTemplate = SMSTemplate::where('id', 6)->first();
            $template = $mesaageTemplate->content;
            $template = str_replace('##day_name##', $day_name, $template);
            $template = str_replace('##day_formatted##', $day_formatted, $template);
            $template = str_replace('##month_formatted##', $month_formatted, $template);
            $template = str_replace('##url_base##', $url_base, $template);
        }

        $sendStatus = $sms->sendMultipletest($template, $user_list);

        \Log::info('message');

        return true;
    }
}
