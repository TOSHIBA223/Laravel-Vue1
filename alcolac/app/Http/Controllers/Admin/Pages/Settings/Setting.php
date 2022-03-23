<?php

namespace App\Http\Controllers\Admin\Pages\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Roles;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Menu;
use App\Models\SMSTemplate;
use App\Builders\ContentTags;
use App\Models\Users;
use App\Models\PermissionRoles;
use App\Models\Settings;
use App\Services\SMS;
use App\Services\SMBClientService;
use Mail;
use DateInterval;
use DateTime;

class Setting extends AdminController
{
    public function index()
    {
        if (session('data')) {
            $this->data = session('data');
        } else {
            $this->data['templates'] = Settings::get();
        }

        $roleId = \Auth::user()->user_role_id;
        $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 9])->first();

        $this->data['permission'] = $permission;

        if ($permission->perm_view == 1) {
            return Inertia::render(
                'Admin/Settings/Assign',
                [
                    'data' => $this->data,
                    'systemSuccess' => session('systemSuccess'),
                    'contentTags' => ContentTags::getContentTags(),

                ]
            );
        } else {
            return back()->withErrors(['systemFail' => 'You are not authorised this access.']);
        }
    }

    public function testsmtp(Request $request)
    {
        try {
            Mail::raw('Hi, this is test email!', function($email) {
                $email->to(env('MAIL_FOR_DEV'))->subject('Configuration Test');
            });
            return ['systemSuccess' => 'SMTP Test Success.'];
        } catch (\Exception $e) {
            return ['systemFail' => 'SMTP Test Failed.'];
        }
    }

    public function testsmb(Request $request)
    {
        $smbClient = new SMBClientService();
        if ($smbClient->test()) {
            return ['systemSuccess' => 'SMB Test Success.'];
        } else {
            return ['systemFail' => 'SMB Test Failed.'];
        }
    }

    public function reset_address(Request $request)
    {
        if (Setting::sendResetAddressSMS()) {
            $this->data['templates'] = Settings::get();
            return back()->with(['systemSuccess' => 'Reset successfully.', 'data' => $this->data]);
        } else {
            $this->data['templates'] = Settings::get();
            return back()->with(['systemFail' => 'Reset failed.', 'data' => $this->data]);
        }
    }

    public static function sendResetAddressSMS()
    {
        $reset_interval = Settings::where('id', 30)->first()->value;
        $date = new DateTime();
        $date->add(new DateInterval('P' . $reset_interval . 'D'));
        $date_string = $date->format('Y-m-d') . 'T' . $date->format('H:i');
        Settings::where('id', 31)->update(['value' => $date_string]);

        $user_list = Users::get();
        $messageTemplate = SMSTemplate::where('id', '7')->first();
        $template = $messageTemplate->content;

        try {
            $sms_service = new SMS();
            $sms_service->sendMultiple($template, $user_list);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function savesetting(Request $request)
    {
        $dataRequest = $request->all();
        $data =  $dataRequest['module'];

        foreach ($data as $k => $val) {
            if (isset($val['view'])) {
                Settings::where('id', $val['id'])->update(['value' => $val['view']]);
            }
        }
        $this->data['templates'] = Settings::get();
        return back()->with(['systemSuccess' => 'updated successfully.', 'data' => $this->data]);
    }

    public function saveimage(Request $request)
    {
        $dataRequest = $request->all();

        if ($request->file('Logo')) {
            $file     = $request->file('Logo');
            $filename = $file->getClientOriginalName();

            $path = $request->file('Logo')->storeAs(
                'profileImage',
                $filename
            );
            $array['LOGO'] = $path;

            Settings::where('id', '19')->update(['value' => $array['LOGO']]);
        }

        if ($request->file('favicon')) {
            $file     = $request->file('favicon');
            $filename = $file->getClientOriginalName();

            $path = $request->file('favicon')->storeAs(
                'profileImage',
                $filename
            );
            $array['favicon'] = $path;
            Settings::where('id', '20')->update(['value' => $array['favicon']]);
        }

        return back()->with(['systemSuccess' => 'updated successfully.']);
    }
}
