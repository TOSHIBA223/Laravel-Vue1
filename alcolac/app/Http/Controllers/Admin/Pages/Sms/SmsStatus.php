<?php
namespace App\Http\Controllers\Admin\Pages\Sms;

use App\Models\File;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminController;
use App\Models\SMSTemplate as SmsTemplateModel;
use App\Builders\ContentTags;
use App\Models\SystemLog;

class SmsStatus extends AdminController
{

     /**
     * Scope this function get sms status.
     *
    */

    public function index()
    {
        $this->data['templates'] = SystemLog::select('*')->where('type', 'sms')->orderBy('created_at', 'DESC')->with('admin')->take(30)->get();
//        $this->data['users'] = User::select(['id', 'first_name', 'last_name'])->get();
//        $this->data['locations'] = Location::select('id', 'name')->orderby('name', 'ASC')->get();
//        $this->data['groups'] = User::select('groups')->distinct()->get();
//        $this->data['files'] = File::where('archived', 0)->get();
//        $this->data['templates'] = SmsTemplateModel::all();
//        $this->data['builtMessage'] = session('builtMessage');
//
        return Inertia::render('Admin/Sms/Status/Index',
            [
                'data' => $this->data,
//                'systemSuccess' => session('systemSuccess'),
//                'systemFail' => session('systemFail'),
//                'contentTags' => ['Users' => ContentTags::getContentTags()['Users']]
            ]
        );
    }
}
