<?php

namespace App\Http\Controllers\Frontend;

use App\Builders\Tokens;
use App\Models\Declaration;
use App\Models\DeclarationSent;
use App\Models\Poll as PollModel;
use App\Models\PollSent;
use App\Models\SMSTemplate;
use App\Models\User;
use App\Models\QuestionnaireSent;
use App\Models\File;
use App\Models\UserAddress;
use App\Services\GooglePlaces;
use App\Services\SMS;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;
use App\Builders\Dates;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\DefaultDeclaration;
use SKAgarwal\GoogleApi\PlacesApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Services\SMBClientService;
use Illuminate\Support\Facades\Storage;
use App\Exports\AccessControlExport;
use App\Exports\AccessControlExportDec;
use App\Exports\DeclarationExport;
use Maatwebsite\Excel\Facades\Excel;
use Stevebauman\Location\Facades\Location;
use App\Models\FileLog;
use App\Models\DecLog;
use App\Models\QrChecks;
use App\Models\Users;
use App\Models\Address;
use App\Notifications\FailedTest;
use App\Notifications\FailedSunshineTest;
use App\Notifications\IncorrectAddress;
use Validator;
use Browser;


class Frontend extends BaseController
{


    /**
     * Scope this function get data and render data on home page.
     *
     */

    public function index()
    {
        return Inertia::render('Frontend/Home', ['systemSuccess' => session('systemSuccess')]);
    }

    /**
     * Scope this function create new declaration and render data to page.
     *
     */

    public function newDeclaration()
    {
        $data = [
            'days' => Dates::days(),
            'months' => Dates::months(),
            'years' => Dates::years(),
            'systemSuccess' => session('systemSuccess'),
            'submitted' => session('submitted')
        ];

        return Inertia::render('Frontend/NewDeclaration', $data);
    }

    /**
     * Scope this function check file and sent file url.
     *
     */

    public function file($token, Request $request)
    {

        $file = File::where('token', $token)->first();

        if ($file->archived === 0) {
            $url = asset('storage/admin-uploads/' . $file->name);
            File::where('token', $token)->update(['views' => $file->views + 1]);

            $ip = request()->ip();
            //$ip = '111.93.41.194';
            $data = \Location::get($ip);


            $fileData = [
                'file_id' => $file->id,
                'ip_address' => $ip,
                'file_name' => $file->name,

            ];


            $savedlog = FileLog::create($fileData);

            return redirect($url);
        } else {
            return view('archive-image');
        }
    }

    /**
     * Scope this function get poll data with status expired , complete and render to poll page.
     *
     */

    public function poll($token)
    {
        $poll = PollSent::where('token', $token)->first();
        $poll_template = $poll->template;
        $status = false;

        if (Carbon::now(config('constant.timeZone'))->greaterThan(Carbon::parse($poll_template->valid_to)))
            $status = config('constant.status.expired');

        if ($poll->complete === 1)
            $status = config('constant.status.complete');

        if ($poll->void === 1)
            $status = config('constant.status.void');

        $data['pollQuestions'] = json_decode($poll_template->fields);
        $data['description'] = $poll_template->description;
        $data['currentTitle'] = $poll_template->name;
        $data['token'] = $token;

        return Inertia::render('Frontend/Poll', ['data' => $data, 'status' => $status]);
    }

    public function declaration($token, $xyz = null)
    {
        $data = [
            'days' => Dates::days(),
            'months' => Dates::months(),
            'years' => Dates::years(),
            'token' => $token
        ];

        $data['entry'] = DeclarationSent::where('token', $token)->first();

        if (isset($data['entry'])) {
            $data['declaration'] = $data['entry']->declaration;
            $data['user'] = $data['entry']->user;
            $data['address'] = UserAddress::where('id', $data['user']->address)->first();

            $ip = request()->ip();
            $datalog = \Location::get($ip);
            $browser = Browser::browserName() . ' / ' . Browser::platformName();

            $fileData = [
                'dec_id' => $data['entry']->declaration_id,
                'ip_address' => $ip,
                'token' => $token,
                'browser' => $browser,
                'employee_id' => $data['entry']->user->employee_code,
            ];

            $savedlog = DecLog::create($fileData);
        } else {
            $data['declaration'] = [];
            $data['user'] = [];
            $data['address'] = [];
        }

        if (!isset($data['address'])) {
            $data['address'] = [];
        }

        $dobValidation = session('dobValidation') ? 1 : 2;
        $addressList = session('addressList');
        if (!empty($data['user'])) {
            $newAddress = UserAddress::where([['current', 1], ['user_id', $data['user']->id]])->first();;
        } else {
            $newAddress = [];
        }
        $status = $this->getDeclarationStatus($token);

        return Inertia::render('Frontend/Declaration', [
            'data' => $data,
            'dobValidation' => $dobValidation,
            'addressList' => $addressList,
            'newAddress' => $newAddress,
            'status' => $status,
            'decPassed' => session('decPassed'),
            'template' => session('template')
        ]);
    }


    /**
     * Scope this function set declaration status.
     *
     */

    private function getDeclarationStatus($token)
    {
        $declaration = DeclarationSent::where('token', $token)->first();

        if (isset($declaration)) {

            $user = $declaration->user;
            $declaration_template = $declaration->declaration;
            $declaration_short = $declaration_template->short_valid_until;
            $declaration_long = $declaration_template->valid_until;
            $declaration_valid_time_type = $declaration->short_valid;

            if ($declaration->complete === 1)
                return 'complete';

            if ($declaration->void === 1)
                return config('constant.status.void');

            if ($declaration_template->id === 3 && $user->location === 1) {
                if (!Carbon::now(config('constant.timeZone'))
                    ->greaterThan(Carbon::parse($declaration->created_at, config('constant.timeZone'))
                        ->addDay()->setHour(4)->setMinute(0)->setSecond(0))) {
                    return 'sunshine_inactive';
                }
            }

            if ($declaration_valid_time_type == 1 && $declaration_template->pre_sms_template === 3) {
                if ($declaration_template->expire_hour == 0) {
                    return;
                }
                if (Carbon::now(config('constant.timeZone'))->greaterThan(
                    Carbon::parse(
                        $declaration->created_at,
                        config('constant.timeZone')
                    )->addMinutes($declaration_template->expire_hour)
                )) {
                    return 'expired';
                }
            }

            if ($declaration_valid_time_type === 0) {
                if (Carbon::now(config('constant.timeZone'))->greaterThan(
                    Carbon::parse(
                        $declaration->created_at,
                        config('constant.timeZone')
                    )->addDays($declaration_long)
                ))
                    return 'expired';
            } else if ($declaration_template->pre_sms_template != 3) {
                if (Carbon::now(config('constant.timeZone'))->greaterThan(
                    Carbon::parse(
                        $declaration->created_at,
                        config('constant.timeZone')
                    )->addMinutes($declaration_short)
                ))
                    return 'expired';
            }
        } else {
            return 'invalid_token';
        }
    }

    /**
     * Scope this function check dob validation.
     *
     */

    public function dobValidation(Request $r)
    {
        $user = User::find($r->userId);
        $chosen_dob = "{$r->year}-{$r->month}-{$r->day}";
        if ($chosen_dob === $user->dob)
            return ['dobValidation' => true];
        return ["fail" => true];
    }

    /**
     * Scope this function find address list.
     *
     */

    public function findAddress(Request $r)
    {
        $address_list = GooglePlaces::findAddress($r->partialAddress);
        return $address_list;
    }

    /**
     * Scope this function update address.
     *
     */

    public function updateAddress(Request $r)
    {
        $cur_address = UserAddress::where([['user_id', $r->id]])->orderBy('created_at', 'desc')->first();
        if ($cur_address) {
            $cur_address->current = 0;
            $cur_address->save();
        }

        if (!empty($r->address)) {
            $locationData = [
                'user_id' => $r->id,
                'address' => $r->address,
                'suburb' => $r->suburb,
                'state' => $r->state,
                'post_code' => $r->post_code,
                'current' => 1
            ];

            $new_address = UserAddress::create($locationData);

            User::where([['id', $r->id]])->update(['address' => $new_address->id]);
            if (!empty($new_address)) {
                $user = new Users();
                $user_data = $user->getUserById($r->id)[0];
                $location = $user_data->location;
                $route = $location == 'Colac' ?
                    env('MAIL_FOR_INCORRECT_ADDRESS') :
                    env('MAIL_FOR_SUNSHINE');
                if (env('APP_ENV') == 'dev') {
                    $route = env('MAIL_FOR_DEV');
                }
                Notification::route('mail', $route)
                    ->notify(new IncorrectAddress(
                        $user_data->first_name . ' ' . $user_data->last_name,
                        $user_data->employee_code,
                        $cur_address,
                        $new_address
                    ));
            }
        }
        $newAddress = UserAddress::where([['current', 1], ['user_id', $r->id]])->first();

        // session($newAddress);
        return back()->with(['newAddress', $newAddress]);
    }

    /**
     * Scope this function save declaration data.
     *
     */

    public function submitDeclaration(Request $r)
    {

        $declaration = DeclarationSent::where([['token', $r->token]])->first();

        UserAddress::where([['current', 1], ['user_id', $declaration->user_id]])->update(['current' => 0]);

        DeclarationSent::where([['token', $r->token]])->update(['short_valid' => 1]);


        $declaration_template = $declaration->declaration;

        $user = new Users();
        $location = $user->getEmployeeLocation($r->token);

        if ($declaration !== null) {
            $flag = 1;
            $warn_flag = 0;
            foreach ($r->answers as $answer) {
                $answerArray[] = [
                    $answer['name'] => $answer['answer'],
                ];
                if ($answer['failed'] === true) {
                    $flag = 0;
                }
                if ($answer['warned'] === true) {
                    $warn_flag = 1;
                }
            }

            $declaration->answers = json_encode($answerArray);
            $declaration->complete = 1;
            $declaration->passed = $flag;
            $save = $declaration->save();

            $data = DeclarationSent::select('user_id', 'answers', 'complete', 'passed')
                ->where([['token', $r->token]])->get();
            $employ_data = User::where('id', $declaration->user_id)->first();
            foreach ($data as $key => $user_complete) {
                $user_complete->user_id = $employ_data->employee_code;
                if ($user_complete->complete == 0) {
                    $user_complete->complete = 'TRUE';
                } else {
                    $user_complete->complete = $user_complete->passed ? 'FALSE' : 'TRUE';
                }
                unset($user_complete->answers);
                unset($user_complete->passed);
            }

            $user_data = User::where('id', $declaration->user_id)->first();

            if ($declaration_template->csv_upload == 1) {
                $file_name = '';
                if ($location !== 'Sunshine') {
                    $file_name = 'ams_access_eid' . $user_data->employee_code . '_' . Carbon::now('Australia/Melbourne')->format('dmY_His') . '.csv';
                } else if (!$flag) {
                    $file_name = 'sunshine_failed_test' . $user_data->employee_code . '_' . Carbon::now('Australia/Melbourne')->format('dmY_His') . '.csv';
                }
                if ($file_name != '') {
                    // error_log('Data Stored ' . json_encode($data));
                    Excel::store(
                        new AccessControlExport($data),
                        $file_name
                    );
                    $smbClient = new SMBClientService();
                    $smbClient->uploadTo(
                        Storage::disk('local')->path($file_name),
                        '/Integriti/' . $file_name
                    );
                }
            }

            if (!$flag) {
                switch ($location) {
                    case "Sunshine":
                        $route = (env('APP_ENV') === 'dev') ?
                            env('MAIL_FOR_DEV') :
                            env('MAIL_FOR_SUNSHINE');
                        Notification::route('mail', $route)
                            ->notify(new FailedSunshineTest(
                                $employ_data->first_name . ' ' . $employ_data->last_name,
                                $r->answers,
                                $employ_data->employee_code
                            ));
                        break;
                    case "Colac":
                    default:
                        $route = (env('APP_ENV') === 'dev') ?
                            env('MAIL_FOR_DEV') :
                            env('MAIL_FOR_FAILED_TEST');
                        Notification::route('mail', $route)
                            ->notify(new FailedTest(
                                $employ_data->first_name . ' ' . $employ_data->last_name,
                                $r->answers,
                                $employ_data->employee_code
                            ));
                }
            } else if ($warn_flag == 1){
                $flag = 2; // If all answers are not failed and at least one answer is warned
                // Send SMS if it's warned
                $template_model = SMSTemplate::firstWhere('slug', 'sms_rat_fail');
                $sms = new SMS();
                $template = $template_model->content;
                $sms->sendSingleUser($template, $user_data->id, $user_data->phone);
            }

            if ($save) {
                return back()->with(['systemSuccess' => true, 'decPassed' => $flag, 'template' => $declaration_template, 'submitted' => true]);
            }

            return back()->withErrors(['systemFail' => 'Your declaration answers could not be saved']);
        }

        return back()->withErrors(['systemFail' => 'Your declaration can not be found']);
    }

    public function selfGenerate(Request $r)
    {

        $token = Tokens::verification();
        $count = 1;

        //echo substr($r->phone, 0, 1);
        // echo str_replace('0', '+61', $r->phone, $count);
        //die;
        //$phone = substr($r->phone, 0, 1) == '0' ? $r->phone : str_replace('0', '+61', $r->phone, $count);
        if (substr($r->phone, 0, 1) == '0') {


            $phone = substr_replace($r->phone, "+61", 0, ($r->phone[0] == '0'));

            $user = User::where(
                [
                    'dob' => "{$r->year}-{$r->month}-{$r->day}",
                    'phone' => $phone
                ]
            )->first();



            if ($user !== null) {

                $this->voidDeclaration($user->id);
                /****Modified code */


                $CheckSetDefa =  DefaultDeclaration::where([
                    'location' => $user->location
                ])->first();

                if (isset($CheckSetDefa)) {

                    $declarationId = $CheckSetDefa->declaration_id;
                    $new_dec = DeclarationSent::create(
                        [
                            'user_id' => $user->id,
                            'declaration_id' => $CheckSetDefa->declaration_id,
                            'token' => $token,
                            'short_valid' => 2
                        ]
                    );
                } else {

                    $record = DeclarationSent::where([
                        'user_id' => $user->id,
                        'complete' => 0,
                    ])->orderBy('id', 'DESC')->first();


                    $new_dec = DeclarationSent::create(
                        [
                            'user_id' => $user->id,
                            'declaration_id' => $record->declaration_id,
                            'token' => $token,
                            'short_valid' => 2
                        ]
                    );

                    $declarationId = $record->declaration_id;
                }

                if (!$new_dec)
                    return back()->withErrors(['systemFail' => 'We couldn\'t send this declaration']);

                $sms_content = SMSTemplate::firstWhere('slug', 'send_declaration')->content;
                $sms_service = new SMS();
                $sms_service->sendSingleDec($sms_content, $user->id, $phone, $token, $declarationId, 3);

                return redirect('/')->with(['systemSuccess' => 'Your new declaration is on it\'s way']);
            }
        }

        return back()->withErrors(['systemFail' => 'Your phone number or Date of Birth are incorrect']);
    }

    /**
     * Scope this function check declaration data.
     *
     */

    private function voidDeclaration($userId)
    {
        $exists = DeclarationSent::where([
            'user_id' => $userId,
            'complete' => 0,
            'void' => 0
        ])->orderBy('id', 'DESC')->first();

        if ($exists)
            $exists->update(['void' => 1]);
        return $exists;
    }
    /****
     * Scope this function show declaration data
     */

    public function declarationShow($id)
    {

        $declaration = Declaration::find($id);
        $sent_declaration = $declaration->sent->where('void', 0);
        $complete_declaration = $sent_declaration->where('complete', 1);
        $data['complete_count'] = count($complete_declaration);
        $data['total_count'] = count($sent_declaration);
        $data['declaration_answers'] = json_decode($declaration->fields);
        foreach ($complete_declaration->groupBy('answer') as $key => $answer) {
            $data['user_answers'][$key] = count($answer);
        }
        return Inertia::render('Frontend/DeclarationShow', ['data' => $data]);
    }


    /**
     * Scope this function show poll data.
     *
     */


    public function pollShow($id)
    {
        $poll = PollModel::find($id);
        $sent_polls = $poll->sent->where('void', 0);
        $complete_polls = $sent_polls->where('complete', 1);

        $data['complete_count'] = count($complete_polls);
        $data['total_count'] = count($sent_polls);
        $data['poll_answers'] = json_decode($poll->fields);

        foreach ($complete_polls->groupBy('answer') as $key => $answer) {
            $data['user_answers'][$key] = count($answer);
        }

        return Inertia::render('Frontend/PollShow', ['data' => $data]);
    }

    /**
     * Scope this function update poll data.
     *
     */
    public function submitPoll(Request $r)
    {
        $declaration = DeclarationSent::where('token', $r->token)->first();
        $declaration->complete = 1;
        $declaration->answers = $r->answers;

        $declaration->save();
    }

    public function qrLocation(Request $r, $location)
    {
        $value = $_COOKIE['employee_code'] ?? 0;
        $already_checks = QrChecks::where(['employee_code' => $value, 'location' => $location])->first();
        if (empty($already_checks)) {
            $data = [
                'days' => Dates::days(),
                'months' => Dates::months(),
                'years' => Dates::years(),
                'systemSuccess' => session('systemSuccess'),
                'submitted' => session('submitted'),
                'location' => $location,
            ];

            return Inertia::render('Frontend/Location_Home', $data);
        }
        return Inertia::render('Frontend/Thanks', ['alreadyCheckedIn' => true]);
    }

    public function qrLocationDone()
    {
        return Inertia::render('Frontend/Thanks', ['systemSuccess' => 'Successfully submitted!']);
    }

    public function qrLocationPost(Request $r, $location)
    {

        // if (substr($r->employee_code, 0, 1) == '0') {
            // $employee_code = substr_replace($r->employee_code, "+61", 0, ($r->employee_code[0] == '0'));
            $user = User::where(
                [
                    'dob' => "{$r->year}-{$r->month}-{$r->day}",
                    'employee_code' => $r->employee_code
                ]
            )->first();
            if ($user !== null) {
                setcookie('employee_code', $user->employee_code, time() + (86400 * 30), "/");

                return redirect('/qr-codes/verified')->with(['employee_name' => $user->first_name . ' ' . $user->last_name, 'employee_dob' => $user->dob]);
            }
        // }

        return back()->withErrors(['systemFail' => 'Your Employee Code or Date of Birth are incorrect']);
    }
    public function qrVerified(Request $r)
    {
        $data = [
            'employee_name' => session('employee_name'),
            'employee_dob' => session('employee_dob'),
        ];
        return Inertia::render('Frontend/Qr_Verified', $data);
    }

    public function confirmation(Request $r)
    {
        $data = [
            'employee_name' => session('employee_name'),
            'employee_dob' => session('employee_dob'),
        ];
        return Inertia::render('Frontend/confirmation', $data);
    }

    public function submitResult(Request $r)
    {
        $employee_code = $_COOKIE['employee_code'] ?? 0;
        $user = User::where('employee_code', $employee_code)->first();
        if (!$user) {
            return Inertia::render('Frontend/Thanks', ['systemFail' => 'Invalid Employee!']);
        }
        
        $qr_checks = new QrChecks;
        $qr_checks->employee_code = $user->employee_code;
        $qr_checks->first_name = $user->first_name;
        $qr_checks->last_name = $user->last_name;
        $qr_checks->phone = $user->phone;
        $qr_checks->location = "RATCovidtesting";
        $qr_checks->ip_address = $r->ip();
        $qr_checks->result = $r->result;
        $qr_checks->save();
        
        if ($r->result == 'positive') {
            $template_model = SMSTemplate::firstWhere('slug', 'sms_qr_code');
            $sms = new SMS();
            $template = $template_model->content;
            $sms->sendSingleUser($template, $user->id, $user->employee_code);
    
            return Inertia::render('Frontend/Thanks', ['systemSuccess' => 'Successfully sent SMS!']);
        } else if ($r->result == 'negative') {
            return Inertia::render('Frontend/Thanks', ['systemSuccess' => 'Successfully submitted!']);
        }
    }

    public function register(Request $r)
    {
        if ($r->isMethod('POST')) {
            $user = User::where('email', $r->email)->first();
            if (!empty($user)) {
                return back()->withErrors(['systemFail' => 'The email id already exists.']);
            }
            $user = new User;
            $user->first_name = $r->first_name;
            $user->name = $r->last_name;
            $user->is_qr_employee = 1;
            $user->last_name = $r->last_name;
            $user->employee_code = substr(str_shuffle("0123456789"), 0, 5);
            $user->email = $r->email;
            $user->dob = $r->year . '-' . $r->month . '-' . $r->day;
            $user->phone = substr_replace($r->phone, "+61", 0, ($r->phone[0] == '0'));
            $user->save();

            $message = 'Hi ' . $user->first_name . ' ' . $user->name . ' you are employee now.';

            return back()->with(['systemSuccess' => $message]);
        }
        $data = [
            'days' => Dates::days(),
            'months' => Dates::months(),
            'years' => Dates::years(),
            'systemSuccess' => session('systemSuccess'),
        ];
        return Inertia::render('Frontend/Register', $data);
    }
}
