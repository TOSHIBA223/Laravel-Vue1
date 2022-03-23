<?php
namespace App\Console;

use App\Exports\AccessControlExport;
use App\Models\Users;
use App\Services\SMBClientService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class BlockAllUsersSunday
{
    public function __invoke()
    {
        file_put_contents(
            "storage/logs/integriti_sunday_TEST.log",
            "TEST \n",
            FILE_APPEND
        );
    }
}
