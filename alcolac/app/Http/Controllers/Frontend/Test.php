<?php
namespace App\Http\Controllers\Frontend;

use App\Console\GetSunshineGeneralInfo;
use App\Console\GetSunshineIncomplete;
use App\Exports\AccessControlExport;
use App\Imports\ExcelImport;
use App\Models\QuestionnaireSent;
use App\Models\UserImportFiles;
use App\Models\Users;
use App\Services\SMBClientService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class Test
{
    public function importSunshine()
    {
        (new Imports)->importSunshine();
    }

    public function importColac()
    {
        (new Imports)->importColac();
    }

    public function cronCreateDeclaration()
    {
        (new QuestionnaireController())->sendSms('Test Group');
    }

    public function sunshineIncomplete()
    {
        (new GetSunshineIncomplete)->__invoke();
    }

    public function sunshineFinal()
    {
        (new GetSunshineGeneralInfo())->__invoke();
    }

    public function integritiDump()
    {
        $sent = new QuestionnaireSent();
        $data = $sent->getNightlyCompletionStats();

        $date = Carbon::now('Australia/Melbourne')->format('Y-m-d');
        $time = Carbon::now('Australia/Melbourne')->format('H:i:s');
//        file_put_contents(
//            "storage/logs/smbLog_". $date . '.log',
//            $time . ' ' . 'returning users for the nightly completion stats' . "\n",
//            FILE_APPEND
//        );
//        file_put_contents(
//            "storage/logs/smbLog_". $date . '.log',
//            $time . ' ' . print_r($data, true) . "\n",
//            FILE_APPEND
//        );

        foreach($data as $key => $user)
        {
            if($user->complete !== 0) {
                $user->complete = array_search('yes', (array)json_decode($user->answers)) ? 'TRUE' : 'FALSE';

                if ($user->complete === 'FALSE')
                    unset($data[$key]);
                else
                    unset($user->answers);
            } else {
                $user->complete = 'TRUE';
                unset($user->answers);
            }
        }

//        file_put_contents(
//            "storage/logs/smbLog_". $date . '.log',
//            $time . ' ' . 'returning data entered after the user completion stats are evaluated' . "\n",
//            FILE_APPEND
//        );
//        file_put_contents(
//            "storage/logs/smbLog_". $date . '.log',
//            $time . ' ' . print_r($data, true) . "\n",
//            FILE_APPEND
//        );

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
//        file_put_contents(
//            "storage/logs/smbLog_". $date . '.log',
//            $time . ' ' . $upload_file . "\n",
//            FILE_APPEND
//        );
    }
}
