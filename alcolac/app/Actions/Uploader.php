<?php
namespace App\Actions;

use App\Models\File;
use App\Builders\Tokens;

class Uploader
{

    static public function upload($file, $disk, $dbStore = false)
    {
        $file_name = $file->getClientOriginalName();

        try {
           /* $path = $file->storeAs(
                '',
                $file_name,
                $disk
            );*/

            $path = $file->storeAs(
                'public/admin-uploads',
                $file_name
            );



            if( $path && $dbStore === true) {
                $upload = new File();
                $upload->path = $path;
                $upload->name = $file_name;
                $upload->views = 0;
                $upload->token = Tokens::adminFiles();
                $upload->archived = 0;
                $upload->save();
            }

            return $path;
        } catch( \Exception $e) {
            return $e->getMessage();
        }
    }
}
