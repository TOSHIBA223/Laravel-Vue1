<?php
namespace App\Http\Controllers\Frontend;

use App\Imports\ColacUserImport;
use App\Imports\SunshineUserImport;
use App\Models\UserImportFiles;
use App\Models\Users;
use App\Services\SMBClientService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class Imports
{

    public function importColac()
    {
        $smbClient = new SMBClientService();
        $download_files = $smbClient->downloadFrom(
            '/Rockfast/',
            storage_path() . '/smb/import/'
        );



        $date = Carbon::now('Australia/Melbourne')->format('Y-m-d');
        $time = Carbon::now('Australia/Melbourne')->format('H:i:s');

        file_put_contents(
            storage_path() . "/logs/download_test_". $date . '.log',
            $time . ' ' . print_r($download_files, true) . "\n",
            FILE_APPEND
        );

        foreach( $download_files as $file )
        {
            $file_path = storage_path() . '/smb/import/' . $file;
            if( strpos($file, '.csv') !== false ) {
                try {
                    $import_codes = new ColacUserImport();
                    $import = Excel::import( $import_codes, $file_path);
                } catch( \Exception $e) {
                    $upload = $smbClient->uploadTo(
                        $file_path,
                        '/Rockfast/FAILED/' . $file
                    );

                    if( $upload ) {
                        $smbClient->delete('/Rockfast/' . $file);
                    }

                    file_put_contents(
                        storage_path() . "/logs/colac/{$file}_FAILED" . $date . '.txt',
                        $time . $file . " processed\n",
                        FILE_APPEND
                    );

                    return false;
                }

                Users::whereNotIn('employee_code', $import_codes->getEmployeeCodes())
                    ->where('location', '!=', 'Sunshine')
                    ->update(['is_inactive' => 1]);

                file_put_contents(
                    storage_path() . "/smb/export/{$file}_" . $date . '.txt',
                    $time . $file . " processed\n",
                    FILE_APPEND
                );
                $upload = $smbClient->uploadTo(
                    $file_path,
                    '/Rockfast/PROCESSED/' . $file
                );

                if ($upload) {
                    $smbClient->delete('/Rockfast/' . $file);
                }
            }

        }
    }

    public function importSunshine()
    {
        $smbClient = new SMBClientService();
        $download_files = $smbClient->downloadFrom(
            '/Sunshine/',
            storage_path() . '/smb/import/'
        );

        $date = Carbon::now('Australia/Melbourne')->format('Y-m-d');
        $time = Carbon::now('Australia/Melbourne')->format('H:i:s');

        file_put_contents(
            storage_path() . "/logs/download_test_". $date . '.log',
            $time . ' ' . print_r($download_files, true) . "\n",
            FILE_APPEND
        );

        foreach( $download_files as $file )
        {
            $file_path = storage_path() . '/smb/import/' . $file;
            if( strpos($file, '.csv') !== false ) {
                try {
                    $import_codes = new SunshineUserImport();
                    Excel::import( $import_codes, $file_path);
                } catch( \Exception $e) {
                    $upload = $smbClient->uploadTo(
                        $file_path,
                        '/Sunshine/FAILED/' . $file
                    );

                    if( $upload ) {
                        $smbClient->delete('/Sunshine/' . $file);
                    }

                    file_put_contents(
                        storage_path() . "/logs/sunshine/{$file}_FAILED" . $date . '.txt',
                        $time . $file . " processed\n",
                        FILE_APPEND
                    );

                    return false;
                }

                Users::whereNotIn('employee_code', $import_codes->getEmployeeCodes())
                    ->where('location', '!=', 'Colac')
                    ->update(['is_inactive' => 1]);

                file_put_contents(
                    storage_path() . "/smb/export/{$file}_". $date . '.txt',
                    $time . $file . " processed\n",
                    FILE_APPEND
                );
                $upload = $smbClient->uploadTo(
                    $file_path,
                    '/Sunshine/PROCESSED/' . $file
                );

                if( $upload ) {
                    $smbClient->delete('/Sunshine/' . $file);
                }
            }
        }
    }
}
