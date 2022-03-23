<?php

namespace App\Http\Controllers\Admin\Pages\Sms;

use App\Models\File;
use App\Models\Location;
use App\Models\User;
use App\Models\SystemLog;
use App\Models\MessageQueue;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminController;
use App\Models\SMSTemplate as SmsTemplateModel;
use App\Builders\ContentTags;
use App\Services\SMS;
use Illuminate\Support\Facades\URL;
use App\Models\PermissionRoles;
use App\Console\RunCronForDeclaration;

class SmsSend extends AdminController
{

    /**
     * Scope this function get all data like user, locations,files,groups for send.
     *
     */

    public function index()
    {
        $this->data['users'] = User::select(['id', 'first_name', 'last_name'])->get();
        $this->data['locations'] = User::select('location')->distinct()->get();
        $this->data['groups'] = User::select('groups')->distinct()->get();
        $this->data['files'] = File::where('archived', 0)->get();
        $this->data['templates'] = SmsTemplateModel::all();
        $this->data['builtMessage'] = session('builtMessage');
        $this->data['baseUrl'] = URL::to('/uploads');
        $roleId = \Auth::user()->user_role_id;
        $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 6])->first();
        $this->data['permission'] = $permission;
        if ($permission->perm_view == 1) {
            return Inertia::render(
                'Admin/Sms/Send/Index',
                [
                    'data' => $this->data,
                    'systemSuccess' => session('systemSuccess'),
                    'systemFail' => session('systemFail'),
                    'contentTags' => ['Users' => ContentTags::getContentTags()['Users']]
                ]
            );
        } else {
            return back()->withErrors(['systemFail' => 'You are not authorised this access.']);
        }
    }

    /**
     * Scope this function send message to multiple user.
     *
     */

    public function send(Request $r)
    {
        try {
            switch ($r->type) {
                case 'user':
                    $users = self::getUsers($r);
                    break;
                case 'group':
                    $users = self::getGroupUsers($r);
                    break;
                case 'location': default:
                    $users = self::getLocationUsers($r);
                    break;
            }

            $smsService = new SMS();
            $smsService->sendMultipleSMS($r->message, $users);

            return back()->with(['systemSuccess' => 'Your SMS was sent successfully.']);
        } catch (\Exception $e) {
            return back()->withErrors(['systemFail' => $e->getMessage()]);
        }
    }

    public function schedule(Request $r)
    {
        try {
            $queue_data = [
                'user_id' => $r->user()->id,
                'message' => $r->message,
                'type' => $r->type,
                'to' => json_encode($r->to),
                'sent' => 0,
                'result' => 0,
                'schedule' => $r->date,
            ];

            MessageQueue::create($queue_data);
            
            return ['systemSuccess' => 'Your SMS was scheduled successfully.'];
        } catch (\Exception $e) {
            return ['systemFail' => $e->getMessage()];
        }
    }


    /**
     * Scope this function get user data.
     *
     */

    private function getUsers($r)
    {
        if ($r['to'][0] == 'all') {
            $log_data = [
                'user_id' => $r->user()->id,
                'message' => "Sent message to all employees",
                'type' => 'sms',
            ];
            SystemLog::create($log_data);

            return User::get();
        } else {
            $user_data = User::whereIn('id', $r['to'])->get();
            $message = "Sent message to ";
            foreach ($user_data as $user) {
                $message .= "\"" . $user->first_name . " " . $user->last_name . "\", ";
            }
            $message = trim($message, ", ");

            $log_data = [
                'user_id' => $r->user()->id,
                'message' => $message,
                'type' => 'sms',
            ];
            SystemLog::create($log_data);

            return $user_data;
        }
    }

    /**
     * Scope this function get group user data.
     *
     */

    private function getGroupUsers($r)
    {
        if ($r->to === 'all') {
            $log_data = [
                'user_id' => $r->user()->id,
                'message' => "Sent message to all group employees",
                'type' => 'sms',
            ];
            SystemLog::create($log_data);

            return User::get();
        }

        $log_data = [
            'user_id' => $r->user()->id,
            'message' => "Sent message to \"" . $r->to . "\" employees",
            'type' => 'sms',
        ];
        SystemLog::create($log_data);

        return User::where('groups', $r->to)->get();
    }

    /**
     * Scope this function get user location data.
     *
     */

    private function getLocationUsers($r)
    {
        if ($r->to === 'all') {
            $log_data = [
                'user_id' => $r->user()->id,
                'message' => "Sent message to all location employees",
                'type' => 'sms',
            ];
            SystemLog::create($log_data);

            return User::get();
        }

        $log_data = [
            'user_id' => $r->user()->id,
            'message' => "Sent message to \"" . $r->to . "\" employees",
            'type' => 'sms',
        ];

        SystemLog::create($log_data);

        return User::where('location', $r->to)->get();
    }
}
