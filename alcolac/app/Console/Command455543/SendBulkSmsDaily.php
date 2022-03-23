<?php


namespace App\Console\Command;

use App\Models\QuestionnaireSent;
use App\Models\SmbSentFiles;
use App\Models\Preference;
use App\Models\Users;
use App\SMS;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SendBulkSmsDaily extends Command
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
    protected $description = 'Sends daily|weekly summary of notifications to members based on parameter';

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
     * @return mixed
     */
    public function handle($group = null)
    {

        $sms = new SMS();
        $users = new Users();
        $friday = Carbon::now('Australia/Melbourne')->format('l') == 'Friday' ?? false;

        //TODO test this works
        if( $friday === false)
            $userIds = $users->getAllIdsAsArray($group);

        if( $friday === true)
            $userIds = $users->getColacIdsAsArray($group);

        $questionnaire_sent = new QuestionnaireSent();

        if( Carbon::now('Australia/Melbourne')->format('l') == 'Friday') {
            $user_list = $users->getMultipleColacForMessaging($group)->toArray();
        } else {
            $user_list = $users->getMultipleForMessaging($group)->toArray();
        }

        $datetime = new \DateTime(date('Y-m-d H:i:s', strtotime('+1 day')));
        $day_name = $datetime->format('l');
        $day_formatted = $datetime->format('jS');
        $month_formatted = $datetime->format('F');

        $url_base = URL::to('/');

        if( Carbon::now('Australia/Melbourne')->format('l') == 'Friday') {

            $template = "Hi %s %s,

If you are rostered on to work tomorrow, Saturday the $day_formatted of $month_formatted please complete
the COVID-19 declaration prior to attending work.

$url_base/dec/%s

Thank you,
ALC Management Team";
        } else {

            $template = "Hi %s %s,

Please complete the COVID-19 declaration prior to attending work for $day_name the $day_formatted of $month_formatted.

$url_base/dec/%s

Thank you,
ALC Management Team";
        }

        $sendStatus = $sms->sendMultiple($template, $user_list);

        return true;
    }

}
