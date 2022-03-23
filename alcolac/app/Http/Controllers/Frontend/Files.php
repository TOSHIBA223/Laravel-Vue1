<?php
namespace App\Http\Controllers\Frontend;

use App\Models\Upload;
use Illuminate\Routing\Controller as BaseController;

class Files extends BaseController
{
    public function view($id)
    {
        $file = Upload::where('key', $id)->first();

        if($file->archived === 0 ) {
            $url = '/uploads/admin-uploads/' . $file->name;
            Upload::where('key', $id)->update(['views' => $file->views + 1]);

            return redirect($url);
        } else {
            return view('archive-image');
        }
    }
}
