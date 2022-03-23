<?php
/*
    16.12.2019
    RolesService.php
*/

namespace App\Services;

use Icewind\SMB\ServerFactory;
use Icewind\SMB\BasicAuth;
use App\Models\Settings;
class SMBClientService
{
    public function __construct()
    {
        $host = env('SMB_HOST');
        $user = env('SMB_USER');
        $workgroup = env('SMB_WORKGROUP');
        $password = env('SMB_PASSWORD');
        $share = env('SMB_SHARE_INSTANCE');

        if (Settings::where('name', 'Server Path')->first()) {
            $host = Settings::where('name', 'Server Path')->first()->value;
            $user = Settings::where('name', 'SMB Username')->first()->value;
            $password = Settings::where('name', 'SMB Password')->first()->value;
        }
        
        $auth = new BasicAuth($user, $workgroup, $password);
        $serverFactory = new ServerFactory();
        $server = $serverFactory->createServer($host, $auth);

        $this->shareObj = $server->getShare($share);
    }

    private $shareObj;

    public function test() {
        return $this->shareObj != null;
    }

    public function uploadTo($localFile, $shareFile)
    {
        return $this->shareObj->put($localFile, $shareFile);
    }

    public function seeExistingFiles($path)
    {
        $files = $this->shareObj->dir($path);
        $file_names = [];
        foreach ($files as $file) {
            echo $file->getName() . "\n";
        }

        return $files;
    }

    public function downloadFrom($sharePath, $localFilePath )
    {

        $files = $this->shareObj->dir($sharePath);
        $downloaded_files = [];
        foreach( $files as $file ) {
            $file_name = $file->getName();
            if( strpos($file_name, '.csv') !== false) {
                $local_file = file_exists($localFilePath . '/' . $file_name);
//            if( !$local_file ) {
                $this->shareObj->get($sharePath . $file_name, $localFilePath . $file_name);
                $downloaded_files[] = $file_name;
//            }
            }
        }

        return $downloaded_files;
    }

    public function delete($sharePath)
    {
        return $this->shareObj->del($sharePath);
    }
}
