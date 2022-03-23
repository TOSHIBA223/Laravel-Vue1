<?php

namespace App\Http\Controllers\Admin\Pages\SystemCrons;

use App\Builders\Tokens;
use App\Exports\DeclarationExport;
use App\Models\DeclarationSent;
use App\Models\Location;
use App\Models\SMSTemplate;
use App\Models\User;
use App\Services\SMS;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Menu;
use App\Models\Declaration as DeclarationModel;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PermissionRoles;
use App\Models\SetSystemCrons;
use App\Models\Settings;
use Illuminate\Support\Facades\DB;
use App\Models\SystemCrons;
use App\Models\MenuItem;

use App\Exports\AccessControlExport;
use App\Exports\SunshineIncompleteCSV;
use App\Models\QuestionnaireSent;
use App\Models\Users;
use App\Notifications\GeneralSummarySunshine;
use App\Notifications\NoncompleteSunshineTest;
use Illuminate\Support\Facades\Notification;
use App\Exports\SunshineGeneralCSV;
use App\Services\SMBClientService;
use Illuminate\Support\Facades\Storage;



class SystemCron extends AdminController
{
    /**
     * Scope this function get declaration, menu , location data.
     *
     */

    public function index()
    {

        if (session('data'))
            $this->data = session('data');
        else {
            $this->data['menus'] = Menu::all();
            // $this->data['templates'] = SystemCrons::withTrashed()->with('smsTemplate','cronTimes')->get();
            $this->data['templates'] = SystemCrons::withTrashed()->with('cronTimes')->get();
        }

        $this->data['locations'] =   DB::table('users')->select('location')->distinct()->get();

        $this->data['smsTemplates'] = SMSTemplate::select('id', 'name')->orderby('name', 'ASC')->get();
        $this->data['groups'] = User::select('groups')->distinct()->get();
        $this->data['csrf'] = csrf_token();
        $roleId = \Auth::user()->user_role_id;
        $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 10])->first();
        $this->data['permission'] = $permission;
        if ($permission->perm_view == 1) {

            return Inertia::render('Admin/SystemCrons/Index', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);
        } else {

            return back()->withErrors(['systemFail' => 'You are not authorised this access.']);
        }
    }


    /**
     * Scope this function get declaration data.
     *
     */


    public function get($id)
    {
        $this->data['templates'] = DeclarationModel::withTrashed()->with('smsTemplate')->get();;
        if ($id)
            $this->data['templates'] = DeclarationModel::withTrashed()->find($id);

        return back()->with(['data' => $this->data]);
    }

    /**
     * Scope this function create declaration data.
     *
     */


    public function create(Request $r)
    {

        unset($r['id']);
        try {
            $fields = json_encode($r->fields);
            SystemCrons::create(
                [
                    'name' => $r->name,
                    'description' => $r->description
                ]
            );

            $this->data['templates'] = SystemCrons::withTrashed()->get();
            return back()->with(['systemSuccess' => 'System Cron Update created successfully', 'data' => $this->data]);
        } catch (\Exception $e) {
            return back()->withErrors(['systemFail' => 'We couldn\'t create your new System Cron.' . $e->getMessage()]);
        }
    }

    /**
     * Scope this function update declaration data.
     *
     */

    public function update(Request $r)
    {
        if ($r->enable) {
            try {
                SystemCrons::onlyTrashed()->where('id', $r->id)->restore();

                // $this->data['templates'] = DeclarationModel::withTrashed()->with('smsTemplate','cronTimes')->get();
                $this->data['templates'] = SystemCrons::withTrashed()->with('cronTimes')->get();


                return back()->with(['systemSuccess' => 'System Cron enabled successfully', 'data' => $this->data]);
            } catch (\Exception $e) {
                return back()->withErrors(['systemFail' => 'We couldn\'t enable this System Cron. Please try again.']);
            }
        }

        try {
            SystemCrons::find($r->id)->update($r->all());

            $this->data['templates'] = SystemCrons::withTrashed()->get();

            return back()->with(['systemSuccess' => 'System Cro updated successfully', 'data' => $this->data]);
        } catch (\Exception $e) {
            return back()->withErrors(['systemFail' => 'We couldn\'t update this System Cron. Please try again.']);
        }
    }


    public function delete($id)
    {
        try {
            DeclarationModel::withTrashed()->find($id)->delete();

            $this->data['templates'] = DeclarationModel::withTrashed()->orderBy('id')->get();


            return back()->with(['systemSuccess' => 'Declaration deleted successfully', 'data' => $this->data]);
        } catch (\Exception $e) {

            return back()->withErrors(['systemFail' => 'Something went wrong on the server. Please try again.']);
        }
    }

    /**
     * Scope this function sending out the ALC Employee verification.
     *
     */


    public function send(Request $r)
    {

        try {

            if ($r->declarationId == 1) {
                (new \App\Console\GetSunshineIncomplete)->__invoke();
                return back()->with(['systemSuccess' => 'Run Cron Successfully.']);
            } elseif ($r->declarationId == 3) {
                $sent = new DeclarationSent();
                $data = $sent->getNightlyCompletionStats();

                $date = Carbon::now('Australia/Melbourne')->format('Y-m-d');
                $time = Carbon::now('Australia/Melbourne')->format('H:i:s');
                file_put_contents(
                    "storage/logs/smbLog_" . $date . '.log',
                    $time . ' ' . 'returning users for the nightly completion stats' . "\n",
                    FILE_APPEND
                );
                file_put_contents(
                    "storage/logs/smbLog_" . $date . '.log',
                    $time . ' ' . print_r($data, true) . "\n",
                    FILE_APPEND
                );

                foreach ($data as $key => $user) {
                    $user->complete = 'TRUE';
                    unset($user->answers);
                }

                file_put_contents(
                    "storage/logs/smbLog_" . $date . '.log',
                    $time . ' ' . 'returning data entered after the user completion stats are evaluated' . "\n",
                    FILE_APPEND
                );
                file_put_contents(
                    "storage/logs/smbLog_" . $date . '.log',
                    $time . ' ' . print_r($data, true) . "\n",
                    FILE_APPEND
                );

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
                file_put_contents(
                    "storage/logs/smbLog_" . $date . '.log',
                    $time . ' ' . $upload_file . "\n",
                    FILE_APPEND
                );
                return back()->with(['systemSuccess' => 'Run Cron Successfully.']);
            } elseif ($r->declarationId == 4) {
                (new \App\Console\GetSunshineGeneralInfo)->__invoke();
                return back()->with(['systemSuccess' => 'Run Cron Successfully.']);
            }
        } catch (\Exception $e) {
            return back()->with(['systemSuccess' => 'Run Cron Successfully.']);
            //      return back()->withErrors(['systemFail' => 'We couldn\'t run this cron' . $e->getMessage()]);
        }
    }

    /**
     * @param Request $r includes the id for the declaration template we want to export
     */
    public function csv(Request $r)
    {

        $date_start = $r->dateStart;
        $date_end = $r->dateEnd;

        return Excel::download(
            new DeclarationExport($r->id, $date_start, $date_end),
            'declarations-all-' . Carbon::parse('today')->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Scope this function set void declaration data.
     *
     */

    public function voidDeclaration($userId, $declarationId)
    {
        $exists = DeclarationSent::where([
            'user_id' => $userId,
            'declaration_id' => $declarationId,
            'complete' => 0,
            'void' => 0
        ])->orderBy('id', 'DESC')->first();

        if ($exists)
            $exists->update(['void' => 1]);
    }


    public function setcron(Request $request)
    {


        SetSystemCrons::where('system_crons_id', $request->declarationId)->delete();
        foreach ($request->multislect as $val) {
            $setCrons =  SetSystemCrons::create(
                [
                    'system_crons_id' => $request->declarationId,
                    'locations' => $request['data']['location'],
                    'settime' => $request->time,
                    'cronenbale' => $request->crondayselect,
                    'cronday' => $val['id'],
                    'status' => 1


                ]
            );
        }




        return back()->with(['systemSuccess' => 'Cron Data Save Successfully.']);
    }

    public function getsetting(Request $request)
    {

        $this->data['logo'] = Settings::get();

        return $this->data['logo'];

        //dd($this->data['logo']);
        //return back()->with(['data' => $this->data['logo']]);

    }

    public function getmenu(Request $request)
    {

        $this->data['sidebar'] = MenuItem::orderBy('order', 'ASC')->get();;

        return $this->data['sidebar'];

        //dd($this->data['logo']);
        //return back()->with(['data' => $this->data['logo']]);

    }
}
