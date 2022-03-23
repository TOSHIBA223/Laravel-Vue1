<?php

namespace App\Http\Controllers\Admin\Pages\Declarations;

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
use App\Models\SetCrons;
use App\Models\Settings;
use Illuminate\Support\Facades\DB;
use App\Models\MenuItem;
use App\Models\DefaultDeclaration;
use App\Models\DecLog;
use App\Models\SystemLog;
use App\Models\Address;
use App\Models\Users;

class Declaration extends AdminController
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
            $this->data['templates'] = DeclarationModel::withTrashed()->with('smsTemplate', 'cronTimes')->get();
        }

        $this->data['locations'] =   DB::table('users')->select('location')->distinct()->get();
        $this->data['defaultDeclaration'] = DefaultDeclaration::get();

        $this->data['smsTemplates'] = SMSTemplate::select('id', 'name')->orderby('name', 'ASC')->get();
        $this->data['groups'] = User::select('groups')->distinct()->get();
        $this->data['csrf'] = csrf_token();
        $roleId = \Auth::user()->user_role_id;
        $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 4])->first();
        $this->data['permission'] = $permission;


        if ($permission->perm_view == 1) {
            return Inertia::render('Admin/Declarations/Index', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);
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

    public function show_create(Request $r)
    {
        $this->data['menus'] = Menu::all();
        $this->data['templates'] = DeclarationModel::withTrashed()->with('smsTemplate', 'cronTimes')->get();
        $this->data['locations'] =   DB::table('users')->select('location')->distinct()->get();
        $this->data['defaultDeclaration'] = DefaultDeclaration::get();
        $this->data['smsTemplates'] = SMSTemplate::select('id', 'name')->orderby('name', 'ASC')->get();
        $this->data['groups'] = User::select('groups')->distinct()->get();
        $this->data['csrf'] = csrf_token();

        $roleId = \Auth::user()->user_role_id;
        $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 4])->first();
        $this->data['permission'] = $permission;

        if ($permission->perm_view == 1) {
            return Inertia::render('Admin/Declarations/Create', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);
        } else {
            return back()->withErrors(['systemFail' => 'You are not authorised this access.']);
        }
    }

    public function show_update(Request $r)
    {
        $this->data['menus'] = Menu::all();
        $this->data['templates'] = DeclarationModel::withTrashed()->with('smsTemplate', 'cronTimes')->get();
        $this->data['locations'] =   DB::table('users')->select('location')->distinct()->get();
        $this->data['defaultDeclaration'] = DefaultDeclaration::get();
        $this->data['smsTemplates'] = SMSTemplate::select('id', 'name')->orderby('name', 'ASC')->get();
        $this->data['groups'] = User::select('groups')->distinct()->get();
        $this->data['csrf'] = csrf_token();

        $this->data['newDeclaration'] = $r->new_declaration;

        $roleId = \Auth::user()->user_role_id;
        $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 4])->first();
        $this->data['permission'] = $permission;

        if ($permission->perm_view == 1) {
            return Inertia::render('Admin/Declarations/Create', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);
        } else {
            return back()->withErrors(['systemFail' => 'You are not authorised this access.']);
        }
    }

    /**
     * Scope this function create declaration data.
     *
     */

    public function create(Request $r)
    {
        try {
            $fields = json_encode($r->fields);
            DeclarationModel::create(
                [
                    'name' => $r->name,
                    'fields' => $fields,
                    'dob_validation' => $r->dob_validation,
                    'address_validation' => $r->address_validation,
                    'valid_until' => $r->valid_until ?? config('constant.time.day'),
                    'short_valid_until' => $r->short_valid_until ?? config('constant.time.minute'),
                    'sms_template_id' => (int) $r->sms_template_id,
                    'location_id' => $r->location_id,
                    'success' => $r->success,
                    'failure' => $r->failure,
                    'warn' => $r->warn,
                    'pre_sms_template' => $r->pre_sms_template_id,
                    'expire_day' => $r->expireday, //TODO: remove expire_day
                    'expire_hour' => $r->expirehours,
                    'never_expire' => $r->never_expire,
                    'sms_dob_req' => $r->sms_dob_req,
                    'sms_address_req' => $r->sms_address_req,
                    'sms_enable' => $r->smscrondayselect,
                    'success_color' => $r->successcolor,
                    'success_font' => $r->successfont,
                    'failure_color' => $r->failurecolor,
                    'failure_font' => $r->failurefont,
                    'warn_color' => $r->warncolor,
                    'warn_font' => $r->warnfont,
                    'fail_sms_template' => $r->fail_sms_template_id,
                    'csv_upload' => $r->csv_upload,
                ]
            );

            $this->data['templates'] = DeclarationModel::withTrashed()->with('smsTemplate')->get();
            return ['systemSuccess' => 'New declaration created successfully.', 'data' => $this->data];
        } catch (\Exception $e) {
            return ['systemFail' => 'We couldn\'t create your new declaration' . $e->getMessage()];
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
                DeclarationModel::onlyTrashed()->where('id', $r->id)->restore();

                $this->data['templates'] = DeclarationModel::withTrashed()->with('smsTemplate', 'cronTimes')->get();

                return ['systemSuccess' => 'Declaration enabled successfully.', 'data' => $this->data];
            } catch (\Exception $e) {
                return ['systemFail' => 'We couldn\'t enable this Declaration. Please try again.'];
            }
        }

        try {
            $new_fields = $r->fields;
            foreach ($new_fields as $key => $field) {
                $new_fields[$key]['name'] = $field['label'];
            }
            $fields = json_encode($new_fields);
            $updatedata = [
                'name' => $r['name'],
                'fields' => $fields,
                'dob_validation' => $r['dob_validation'],
                'address_validation' => $r['address_validation'],
                'valid_until' => $r['valid_until'] ?? config('constant.time.day'),
                'short_valid_until' => $r['short_valid_until'] ?? config('constant.time.minute'),
                'sms_template_id' => (int) $r['sms_template_id'],
                'location_id' => $r['location_id'],
                'success' => $r['success'],
                'failure' => $r['failure'],
                'warn' => $r['warn'],
                'pre_sms_template' => $r['pre_sms_template_id'],
                'expire_day' => $r['expireday'],
                'expire_hour' => $r['expirehours'],
                'never_expire' => $r['never_expire'],
                'sms_dob_req' => $r['sms_dob_req'],
                'sms_address_req' => $r['sms_address_req'],
                'sms_enable' => $r['smscrondayselect'],
                'success_color' => $r['successcolor'],
                'success_font' => $r['successfont'],
                'failure_color' => $r['failurecolor'],
                'failure_font' => $r['failurefont'],
                'warn_color' => $r['warncolor'],
                'warn_font' => $r['warnfont'],
                'fail_sms_template' => $r['fail_sms_template_id'],
                'csv_upload' => $r->csv_upload,
            ];

            DeclarationModel::find($r->id)->update($updatedata);

            $this->data['templates'] = DeclarationModel::withTrashed()->with('smsTemplate', 'cronTimes')->get();

            return ['systemSuccess' => 'Declaration updated successfully.', 'data' => $this->data];
        } catch (\Exception $e) {
            return ['systemFail' => 'We couldn\'t update this Declaration. Please try again.' . $e->getMessage()];
        }
    }


    public function delete($id)
    {
        try {
            DeclarationModel::withTrashed()->find($id)->delete();

            $this->data['templates'] = DeclarationModel::withTrashed()->with('smsTemplate', 'cronTimes')->orderBy('id')->get();

            return ['systemSuccess' => 'Declaration deleted successfully.', 'data' => $this->data];
        } catch (\Exception $e) {
            return ['systemFail' => 'Something went wrong on the server. Please try again.'];
        }
    }

    /**
     * Scope this function sending out the ALC Employee verification.
     *
     */


    public function send(Request $r)
    {
        try {
            //TODO Test this


            if (isset($r->data)) {
                $declaration = DeclarationModel::find($r->declarationId);
                $locations = DB::table('users')->select('location')->distinct()->pluck('location')->toArray();

                $locationId = !empty($declaration) ? $declaration->location_id : 0;


                $data = $r->data;
                $data['location'] = !empty($locations) && $locationId > 0 ? $locations[$locationId - 1] : $data['location'];
                $r->merge(['data' => $data]);

                $data = $r->data;

                if ($r->data['location'] === 'all' && $r->data['group'] === 'all') {
                    $users = User::select('id', 'location', 'phone', 'first_name', 'last_name')->where([['phone', '!=', 'null'], ['is_qr_employee', 0]])->get();
                } elseif ($r->data['location'] !== 'all' && $r->data['group'] === 'all') {
                    $users = User::select('id', 'location', 'phone', 'first_name', 'last_name')->where(
                        [
                            ['phone', '!=', 'null'],
                            ['is_qr_employee', 0],
                            ['location', $r->data['location']]
                        ]
                    )->get();
                } elseif ($r->data['location'] === 'all' && $r->data['group'] !== 'all') {
                    $users = User::select('id', 'location', 'phone', 'first_name', 'last_name')->where(
                        [
                            ['phone', '!=', 'null'],
                            ['is_qr_employee', 0],
                            ['groups', $r->data['group']]
                        ]
                    )->get();
                } else {
                    $users = User::select('id', 'location', 'phone', 'first_name', 'last_name')->where(
                        [
                            ['phone', '!=', 'null'],
                            ['is_qr_employee', 0],
                            // ['groups', $r->data['group']],
                            ['location', $r->data['location']]
                        ]
                    )->when(($r->data['group'] != 'all'), function ($query) use ($data) {
                        $query->where('groups', $data['group']);
                    })->get();
                }

                $location_groups = $users->groupBy('location');



                foreach ($location_groups as $key => $location) {
                    $user_list = [];
                    foreach ($location as $user) {
                        $token = Tokens::verification();
                        $this->voidDeclaration($user->id, $r->declarationId);
                        $new_dec = DeclarationSent::create(
                            [
                                'user_id' => $user->id,
                                'declaration_id' => $r->declarationId,
                                'token' => $token,
                                'short_valid' => 0
                            ]
                        );

                        if ($new_dec)
                            $user_list[] = $user;
                    }

                    $datetime = new \DateTime(date('Y-m-d H:i:s', strtotime('+1 day')));
                    $day_name = $datetime->format('l');
                    $day_formatted = $datetime->format('jS');
                    $month_formatted = $datetime->format('F');
                    $url_base = env('APP_URL');

                    $mesaageTemplate = SMSTemplate::where('id', $r->send_template_id)->first();
                    $template = $mesaageTemplate->content;
                    $template = str_replace('##day_name##', $day_name, $template);
                    $template = str_replace('##day_formatted##', $day_formatted, $template);
                    $template = str_replace('##month_formatted##', $month_formatted, $template);
                    $template = str_replace('##url_base##', $url_base, $template);

                    $sms_service = new SMS();
                    $sms_service->sendMultipletest($template, $user_list, $r->declarationId);
                }
            }


            if (isset($r->userId)) {
                $token = Tokens::verification();
                $this->voidDeclaration($r->userId, $r->declarationId);
                $new_dec = DeclarationSent::create(
                    [
                        'user_id' => $r->userId,
                        'declaration_id' => $r->declarationId,
                        'token' => $token,
                        'short_valid' => 1
                    ]
                );

                $declarationName = DeclarationModel::where('id', $r->declarationId)->first();

                if (!$new_dec) {
                    return back()->withErrors(['systemFail' => 'We couldn\'t send this declaration']);
                }

                $userdata = User::where('id', $r->userId)->first();

                $log_data = [
                    'user_id' => $r->user()->id,
                    'message' => "Run " . $declarationName->name . " on " . $userdata->first_name . " " . $userdata->last_name,
                    'type' => 'declaration',
                ];

                SystemLog::create($log_data);

                $datetime = new \DateTime(date('Y-m-d H:i:s', strtotime('+1 day')));
                $day_name = $datetime->format('l');
                $day_formatted = $datetime->format('jS');
                $month_formatted = $datetime->format('F');
                $url_base = env('APP_URL');

                $messageTemplate = SMSTemplate::where('id', '3')->first();
                $template = $messageTemplate->content;

                $template = str_replace('##day_name##', $day_name, $template);
                $template = str_replace('##name##', $userdata->first_name, $template);
                $template = str_replace('##day_formatted##', $day_formatted, $template);
                $template = str_replace('##month_formatted##', $month_formatted, $template);
                $template = str_replace('##url_base##', $url_base, $template);

                $sms_service = new SMS();
                $sms_service->sendSingleDecAdmin($template, $r->userId, $r->phone, $r->declarationId);

                return back()->with(['systemSuccess' => $declarationName->name . ' Declaration Send Successfully']);
            } else {
                return back()->with(['systemSuccess' => 'Run Cron Successfully.']);
            }
        } catch (\Exception $e) {

            return back()->withErrors(['systemFail' => 'We couldn\'t send this declaration' . $e->getMessage()]);
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


        SetCrons::where('declaration_id', $request->declarationId)->delete();
        foreach ($request->multislect as $val) {
            $setCrons =  SetCrons::create(
                [
                    'declaration_id' => $request->declarationId,
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

    public function setdeclarationdefault(Request $request)
    {

        if ($request->selectlocation != '' && $request->selectedDeclaration != '') {

            DefaultDeclaration::where('location', $request->selectlocation)->delete();

            $setCrons =  DefaultDeclaration::create(
                [
                    'declaration_id' => $request->selectedDeclaration,
                    'location' => $request->selectlocation,
                    'status' => 1
                ]
            );
        }

        if ($request->selectlocation1 != '' && $request->selectedDeclaration1 != '') {

            DefaultDeclaration::where('location', $request->selectlocation1)->delete();
            $setCrons =  DefaultDeclaration::create(
                [
                    'declaration_id' => $request->selectedDeclaration1,
                    'location' => $request->selectlocation1,
                    'status' => 1
                ]
            );
        }

        return back()->with(['systemSuccess' => 'Default Declaration Save Successfully.']);
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


    public function declog($id)
    {

        $this->data['templates'] = DecLog::where(['dec_id' => $id])->orderBy('id', 'DESC')->take(30)->get();
        $fileData = DeclarationModel::where('id', $id)->first();
        $this->data['userName'] = $fileData->name;
        return Inertia::render(
            'Admin/Declarations/declog',
            [
                'data' => $this->data,
                'appUrl' => env('APP_URL'),
                'systemSuccess' => session('systemSuccess')
            ]
        );
    }

    public function merge()
    {
        ini_set('max_execution_time', '0');
        // $this->merge_users();
        $this->merge_declarations();
    }

    public function merge_declarations()
    {
        $old_decs = \DB::table('old_questionnaire_sent')
            ->select('*')
            ->get();
        $i = 0;
        foreach ($old_decs as $old_dec) {
            $i++;
            $old_user = \DB::table('old_users')->where('id', $old_dec->user_id)->first();
            if (!$old_user) {
                continue;
            }
            $new_user = \DB::table('users')->where('employee_code', $old_user->employee_code)->first();

            $new_dec_passed = 0;
            if ($old_dec->answers != '0') {
                $answers = json_decode($old_dec->answers);
                if ($answers->contact == 'no' && $answers->symptoms == 'no' && $answers->contact_live == 'no') {
                    $new_dec_passed = 1;
                }
            }

            $new_dec_short_valid = 0;
            if ($old_dec->self_generated == 1) {
                $new_dec_short_valid = 2;
            }

            $new_dec_data = array(
                'declaration_id' => 7,
                'user_id' => $new_user->id,
                'token' => $old_dec->key,
                'answers' => $old_dec->answers == '0' ? null : $old_dec->answers,
                'passed' => $new_dec_passed,
                'complete' => $old_dec->complete,
                'void' => $old_dec->void,
                'sent' => 0,
                'short_valid' => $new_dec_short_valid,
                'created_at' => $old_dec->created_at,
                'updated_at' => $old_dec->updated_at,
            );
            $new_dec = DeclarationSent::create($new_dec_data);
            error_log("New Dec " . $new_dec->id . " created");
            error_log("Processed " . $i);
        }
        return "Done";
    }

    public function merge_users()
    {
        $now = \Carbon\Carbon::now()->toDateTimeString();
        $old_users = \DB::table('old_users')
            ->select('*')
            ->get();
        $i = 0;
        foreach ($old_users as $old_user) {
            if (\DB::table('users')->where('employee_code', $old_user->employee_code)->first()) {
                continue;
            }

            $old_address = \DB::table('old_user_address')->where('id', $old_user->address)->first();

            $new_address = Address::create([
                'address' => $old_address->address . ', ' . $old_address->suburb . ' ' . $old_address->state . ' ' . $old_address->post_code . ', Australia',
                'created_at' => $now,
                'updated_at' => $now
            ]);

            $new_user_data = array(
                'employee_code' => $old_user->employee_code,
                'first_name' => $old_user->first_name,
                'email' => $old_user->email,
                'created_at' => $old_user->created_at,
                'updated_at' => $old_user->updated_at,
                'dob' => $old_user->dob,
                'address' => $new_address->id,
                'last_name' => $old_user->last_name,
                'groups' => $old_user->groups,
                'location' => $old_user->location,
                'phone' => $old_user->phone,
                'is_inactive' => $old_user->is_inactive,
            );
            $new_user = Users::create($new_user_data);

            Address::updateOrCreate([
                'id' => $new_address->id,
            ], [
                'user_id' => $new_user->id,
            ]);

            echo "New User " . $new_user->id . " created";
            error_log("Processed " . $i++);
        }
        return 'Done';
    }
}
