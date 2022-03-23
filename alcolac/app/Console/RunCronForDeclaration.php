<?php

namespace App\Console;

use App\Exports\AccessControlExport;
use App\Exports\SunshineIncompleteCSV;
use App\Models\QuestionnaireSent;
use App\Models\Users;
use App\Models\SetCrons;
use App\Notifications\GeneralSummarySunshine;
use App\Notifications\NoncompleteSunshineTest;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SMSTemplate;
use App\Services\SMS;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Settings;
use App\Models\Declaration;
use App\Models\DeclarationSent;

class RunCronForDeclaration
{
  function __construct($declaraioniD)
  {
    $this->id = $declaraioniD;
  }

  public function __invoke()
  {
    error_log("Started cron job for sending declaration SMS:" . $this->id);
    $declaration_sent = new DeclarationSent();

    $sms = new SMS();
    $users = new Users();
    $declaration = Declaration::where('id', $this->id)->first();

    $location = $declaration->location_id == 1 ? 'Colac' : 'Sunshine';
    $user_list = $users->getMultipleForMessagingForLocation(null, $location)->toArray();
    $declaration_sent->createBlankBatch($user_list, $declaration->id);

    $datetime = new \DateTime(date('Y-m-d H:i:s', strtotime('+1 day')));
    $day_name = $datetime->format('l');
    $day_formatted = $datetime->format('jS');
    $month_formatted = $datetime->format('F');

    $url_base = env('APP_URL');

    $messageTemplate = SMSTemplate::where('id', $declaration->sms_template_id)->first();
    $template = $messageTemplate->content;

    $template = str_replace('##day_name##', $day_name, $template);
    $template = str_replace('##day_formatted##', $day_formatted, $template);
    $template = str_replace('##month_formatted##', $month_formatted, $template);
    $template = str_replace('##url_base##', $url_base, $template);

    $sms->sendMultipletestCron($template, $user_list, $declaration->id);
  }
}
