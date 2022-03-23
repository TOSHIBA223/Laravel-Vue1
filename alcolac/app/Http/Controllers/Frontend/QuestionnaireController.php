<?php

namespace App\Http\Controllers\Frontend;

use App\Exports\AccessControlExport;
use App\Exports\DeclarationExport;
use App\Models\Address;
use App\Models\QuestionnaireCompleteSendQueue;
use App\Notifications\FailedTest;
use App\Notifications\FailedSunshineTest;
use App\Notifications\IncorrectAddress;
use App\Services\SMBClientService;
use App\User;
use Carbon\Carbon;
use Icewind\SMB\ServerFactory;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Users;
use App\Models\QuestionnaireSent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Crypt;
use App\SMS;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Maatwebsite\Excel\Facades\Excel;
use SKAgarwal\GoogleApi\PlacesApi;

class QuestionnaireController extends BaseController
{
    use Notifiable;

    public function showQuestionnaire($q_token, $verified = false)
    {
        $questionnaire_sent = new QuestionnaireSent();
        $users = new Users();

        if ($q_token && $questionnaire_sent->exists($q_token)) {

            $completed = $questionnaire_sent->questionnaireComplete($q_token);
            $void = $questionnaire_sent->isVoid($q_token);
            $completionDate = $questionnaire_sent->getQuestionnaireCreationDate($q_token);
            $location = $users->getEmployeeLocation($q_token);
            $self_generated = $questionnaire_sent->isSelfGenerated($q_token);
            $time_active = true;
            $inactive = false;
            $admin_verified = $questionnaire_sent->isAdminVerified($q_token);

            if (($self_generated || $admin_verified) &&
                Carbon::now('Australia/Melbourne')
                ->greaterThan(Carbon::parse($completionDate, 'Australia/Melbourne')
                    ->addMinutes(15))
            ) {
                $inactive = true;
            } elseif (
                $location === 'Sunshine' &&
                !Carbon::now('Australia/Melbourne')
                    ->greaterThan(Carbon::parse($completionDate, 'Australia/Melbourne')
                        ->addDay()->setHour(4)->setMinute(0)->setSecond(0))
            ) {
                $time_active = false;
            }

            $name = $users->getFullNameForQuestionnaire($q_token);

            $data = [
                'complete' => $completed,
                'void' => $void,
                'token' => $q_token,
                'title' => 'ALC - Declaration',
                'name' => $name,
                'time_active' => $time_active,
                'inactive' => $inactive,
                'self_generated' => $self_generated,
                'date_complete' => Carbon::parse($completionDate . ' +1 day', 'Australia/Melbourne')->format('d/m/Y')
            ];


            if (($self_generated && $verified === 'true') || $admin_verified) {
                $user_id = $users->getIdForQuestionnaire($q_token);
                $data['address'] = $users->getAddress($user_id);
                return view('questionnaires.pre-verified', $data);
            } else {
                return view('questionnaires.main', $data);
            }
        }

        return view('questionnaires.global.none');
    }

    public function findAddress(Request $r)
    {
        $googlePlaces = new PlacesApi('AIzaSyDheZKX2UPKow1dTIkaCYUAXUAqzGyZrso');
        $general =  $googlePlaces->placeAutocomplete(
            $r->address,
            [
                'components' => 'country:au'
            ]
        );
        $details = [];

        $i = 0;
        foreach ($general['predictions'] as $location) {
            $details[$i]['address'] = $googlePlaces->placeDetails(
                $location['place_id'],
                [
                    'fields' => 'formatted_address'
                ]
            );
            $details[$i]['place_id'] = $location['place_id'];
            $i++;
        }

        return $details;
    }

    public function verifyDob(Request $r)
    {
        $users = new Users();

        if ($r->manual === 'true')
            $r->dob = $r->{"dob-year"} . '-' . $r->{"dob-month"} . '-' . $r->{"dob-day"};

        $dob = $users->verifyDob($r->dob, $r->q_token);

        if ($dob) {
            $user_id = $users->getIdForQuestionnaire($r->q_token);
            $address = $users->getAddress($user_id);

            if ($r->manual === 'true')
                return redirect('/dec/' . $r->q_token . '/declaration')->with(['address' => $address, 'token' => $r->q_token, 'user' => $user_id]);

            return view('questionnaires.global.address-verification', ['address' => $address, 'token' => $r->q_token]);
        }

        if ($r->manual === 'true')
            return redirect('/dec/' . $r->q_token . '/manual?fail=true');

        return response()->json($dob);
    }

    public function correctAddress(Request $r)
    {
        $user = new Users();
        $user_id = $user->getIdForQuestionnaire($r->token);
        $old_address = $user->getAddress($user_id);
        $new_address = $r->address_id ?? false;

        return view(
            'questionnaires.covid.test',
            [
                'token' => $r->token,
                'new_address_id' => $new_address,
                'new_address_string' => $r->address_string,
                'old_address' => $old_address->address . ', ' . $old_address->suburb . ' ' . $old_address->state . ' ' . $old_address->post_code . ', Australia'
            ]
        );
    }

    public function testCompleteWithAddress(Request $r)
    {

        $user = new Users();
        $user_id = $user->getIdForQuestionnaire($r->token);
        $old_address = $user->getAddress($user_id);
        $update_address = $r->address;
        $location = $user->getEmployeeLocation($r->token);

        if ($update_address !== 'false') {

            $address = new Address();
            $new_address = $address->saveFromGooglePlaces($update_address, $old_address->id, $user_id);

            if (!empty($new_address)) {
                $route = env('MAIL_FOR_DEV');
                Notification::route('mail', $route)
                    ->notify(new IncorrectAddress(
                        $user->getFullNameForQuestionnaire($r->token),
                        $user->getEmployeeCode($r->token),
                        $old_address,
                        $new_address
                    ));
            }
        }

        $questionnaire_sent = new QuestionnaireSent();
        $questionnaire_sent->testComplete($r->token, $r->answers);

        $data = $questionnaire_sent->getSingleCompletionStats($r->token);

        $date = Carbon::now('Australia/Melbourne')->format('Y-m-d');
        $time = Carbon::now('Australia/Melbourne')->format('H:i:s');


        foreach ($data as $key => $user_complete) {
            if ($user_complete->complete !== 0) {
                $user_complete->complete = array_search('yes', (array)json_decode($user_complete->answers)) ? 'TRUE' : 'FALSE';
            } else {
                $user_complete->complete = 'TRUE';
            }
            unset($user_complete->answers);
            unset($user_complete->id);
        }

        if ($location !== 'Sunshine') {
            $file_name = 'ams_access_eid' . $user->getEmployeeCode($r->token) . '_' . Carbon::now('Australia/Melbourne')->format('dmY_His') . '.csv';
            Excel::store(
                new AccessControlExport($data),
                $file_name
            );
            $smbClient = new SMBClientService();
            $upload_file = $smbClient->uploadTo(
                Storage::disk('local')->path($file_name),
                '/Integriti/' . $file_name
            );
        }
    }

    public function contactWithCovid(Request $r)
    {
        $user = new Users();
        $user_id = $user->getIdForQuestionnaire($r->token);
        $old_address = $user->getAddress($user_id);
        $update_address = $r->address;

        $location = $user->getEmployeeLocation($r->token);

        if ($update_address !== 'false') {
            $address = new Address();
            $new_address = $address->saveFromGooglePlaces($update_address, $old_address->id, $user_id);

            if (!empty($new_address)) {
                $route = $location == 'Colac' ?
                    env('MAIL_FOR_INCORRECT_ADDRESS') :
                    env('MAIL_FOR_SUNSHINE');
                if (env('APP_ENV') == 'dev') {
                    $route = env('MAIL_FOR_DEV');
                }
                if ($location === 'Sunshine') {
                    Notification::route('mail', $route)
                        ->notify(new FailedSunshineTest(
                            $user->getFullNameForQuestionnaire($r->token),
                            $r->answers,
                            $user->getEmployeeCode($r->token)
                        ));
                } else {
                    Notification::route('mail', $route)
                        ->notify(new IncorrectAddress(
                            $user->getFullNameForQuestionnaire($r->token),
                            $user->getEmployeeCode($r->token),
                            $old_address,
                            $new_address
                        ));
                }
            }
        }

        $questionnaire_sent = new QuestionnaireSent();
        $success = $questionnaire_sent->testComplete($r->token, $r->answers);

        $data = $questionnaire_sent->getSingleCompletionStats($r->token);

        $date = Carbon::now('Australia/Melbourne')->format('Y-m-d');
        $time = Carbon::now('Australia/Melbourne')->format('H:i:s');

        foreach ($data as $key => $user_complete) {
            if ($user_complete->complete !== 0) {
                $user_complete->complete = array_search('yes', (array)json_decode($user_complete->answers)) ? 'TRUE' : 'FALSE';
            } else {
                $user_complete->complete = 'TRUE';
            }

            if ($location !== 'Sunshine')
                unset($user_complete->answers);
            unset($user_complete->id);
        }

        if ($location !== 'Sunshine') {
            $file_name = 'ams_access_eid' . $user->getEmployeeCode($r->token) . '_' . Carbon::now('Australia/Melbourne')->format('dmY_His') . '.csv';
            if ($location === 'Sunshine')
                $file_name = 'sunshine_failed_test' . $user->getEmployeeCode($r->token) . '_' . Carbon::now('Australia/Melbourne')->format('dmY_His') . '.csv';

            Excel::store(
                new AccessControlExport($data),
                $file_name
            );
            $smbClient = new SMBClientService();
            $upload_file = $smbClient->uploadTo(
                Storage::disk('local')->path($file_name),
                '/Integriti/' . $file_name
            );
        }

        switch ($location) {
            case "Sunshine":
                $route = (env('APP_ENV') === 'dev') ?
                    env('MAIL_FOR_DEV') :
                    env('MAIL_FOR_SUNSHINE');
                Notification::route('mail', $route)
                    ->notify(new FailedSunshineTest(
                        $user->getFullNameForQuestionnaire($r->token),
                        $r->answers,
                        $user->getEmployeeCode($r->token)
                    ));
                break;
            case "Colac":
                $route = (env('APP_ENV') === 'dev') ?
                    env('MAIL_FOR_DEV') :
                    env('MAIL_FOR_FAILED_TEST');
                Notification::route('mail', $route)
                    ->notify(new FailedTest($user->getFullNameForQuestionnaire($r->token), $r->answers, $user->getEmployeeCode($r->token)));
        }

        return $success;
    }

    public function backStep(Request $r)
    {
        $users = new Users();
        $user_id = $users->getIdForQuestionnaire($r->q_token);
        $address = $users->getAddress($user_id);

        return view('questionnaires.global.address-verification', ['address' => $address, 'token' => $r->q_token]);
    }

    public function selfGenerate(Request $r)
    {
        $users = new Users();
        $format_phone = $users->formatPhone($r->phone);
        $user = $users->verifyUserWithDobPhone($r->dob, $format_phone)[0];

        if (isset($user->id)) {
            $questionnaire_sent = new QuestionnaireSent();
            $user_key = $questionnaire_sent->generateKey();

            $success = $questionnaire_sent->createBlank($user->id, 1, $user_key, 1);
            $location = $users->getEmployeeLocation($user_key);

            if ($success) {
                $sms = new SMS();
                $datetime = new \DateTime(date('Y-m-d H:i:s', strtotime('+1 day')));
                $day_formatted = $datetime->format('jS');
                $month_formatted = $datetime->format('F');

                $user_single = $user;

                $url_base = URL::to('/');

                $template = "Hi %s %s,

Please complete your new COVID-19 declaration.
This link will expire in 15 minutes.

$url_base/dec/%s/true

Thank you,
ALC Management Team";

                $message = sprintf($template, $user_single->first_name, $user_single->last_name, $user_key);

                $sms->sendSingle($message, $format_phone);

                $sent = new QuestionnaireSent();
                $sent->updateSentStatus($user_key);
                return response()->json($success);
            }
            return response()->json(
                [
                    'error' => $success
                ]
            );
        }
        return response()->json(
            [
                'error' => 'There was a problem verifying your details. Please try again.'
            ]
        );
    }

    public function generateQuestionnaire(Request $r)
    {
        $questionnaire_sent = new QuestionnaireSent();
        $user_key = $questionnaire_sent->generateKey();
        $admin_verified = 0;

        if ($r->pre_verified)
            $admin_verified = 1;

        $datetime = new \DateTime(date('Y-m-d H:i:s', strtotime('+1 day')));
        $day_name = $datetime->format('l');
        $day_formatted = $datetime->format('jS');
        $month_formatted = $datetime->format('F');

        $success = $questionnaire_sent->createBlank($r->user_id, $r->questionnaire_id, $user_key, 0, $admin_verified);

        if ($success) {
            $sms = new SMS();
            $users = new Users();
            $location = $users->getEmployeeLocation($user_key);

            $user_single = $users->getUserById($r->user_id)[0];

            $url_base = URL::to('/');

            $template = "Hi %s %s,

Please complete the COVID-19 declaration prior to attending work for $day_name the $day_formatted of $month_formatted.

$url_base/dec/%s

Thank you,
ALC Management Team";

            if ($location === 'Sunshine') {
                $template = "Hi %s %s,

Please complete the COVID-19 declaration prior to attending work for $day_name the $day_formatted of $month_formatted.
Your declaration will not be available until 4am and will be valid until 8am.

$url_base/dec/%s

Thank you,
ALC Management Team";
            }

            if ($admin_verified === 1) {
                $template = "Hi %s %s,

Please complete your new COVID-19 declaration.
This link will expire in 15 minutes.

$url_base/dec/%s/true

Thank you,
ALC Management Team";
            }

            $message = sprintf($template, $user_single->first_name, $user_single->last_name, $user_key);

            $sms->sendSingle($message, $user_single->phone);

            $sent = new QuestionnaireSent();
            $sent->updateSentStatus($user_key);
        }
        return response()->json($success);
    }

    public function generateQuestionnaireBatch($group = null)
    {
        $users = new Users();
        $friday = Carbon::now('Australia/Melbourne')->format('l') == 'Friday' ?? false;

        //TODO test this works
        if ($friday === false)
            $userIds = $users->getAllIdsAsArray($group);

        if ($friday === true)
            $userIds = $users->getColacIdsAsArray($group);

        $questionnaire_sent = new QuestionnaireSent();
        return response()->json([$questionnaire_sent->createBlankBatch($userIds, 1)]);
    }

    public function sendSms($group = null)
    {
        $sms = new SMS();
        $users = new Users();

        $questionnaires = $this->generateQuestionnaireBatch($group);

        if (Carbon::now('Australia/Melbourne')->format('l') == 'Friday') {
            $user_list = $users->getMultipleColacForMessaging($group)->toArray();
        } else {
            $user_list = $users->getMultipleForMessaging($group)->toArray();
        }

        $datetime = new \DateTime(date('Y-m-d H:i:s', strtotime('+1 day')));
        $day_name = $datetime->format('l');
        $day_formatted = $datetime->format('jS');
        $month_formatted = $datetime->format('F');

        $url_base = URL::to('/');

        if (Carbon::now('Australia/Melbourne')->format('l') == 'Friday') {

            $template = "Hi %s %s,

If you are rostered on to work tomorrow, Saturday the $day_formatted of $month_formatted please complete
the COVID-19 declaration prior to attending work.

$url_base/dec/%s

Thank you,
ALC Management Team";
        } else {

            $template = "Hi %s %s,

Please complete the COVID-19 declaration prior to attending work for $day_name the $day_formatted of $month_formatted.

$url_base/dec/%s

Thank you,
ALC Management Team";
        }

        $sendStatus = $sms->send($template, $user_list);

        return true;
    }

    public function declarationStartManual($q_token)
    {
        $questionnaire_sent = new QuestionnaireSent();
        $users = new Users();

        if ($q_token && $questionnaire_sent->exists($q_token)) {

            $completed = $questionnaire_sent->questionnaireComplete($q_token);
            $void = $questionnaire_sent->isVoid($q_token);
            $completionDate = $questionnaire_sent->getQuestionnaireCreationDate($q_token);
            $location = $users->getEmployeeLocation($q_token);
            $self_generated = $questionnaire_sent->isSelfGenerated($q_token);
            $time_active = true;
            $inactive = false;

            if (
                $self_generated &&
                Carbon::now('Australia/Melbourne')
                ->greaterThan(Carbon::parse($completionDate, 'Australia/Melbourne')
                    ->addMinutes(15))
            ) {
                $inactive = true;
            } elseif (
                $location === 'Sunshine' &&
                !Carbon::now('Australia/Melbourne')
                    ->greaterThan(Carbon::parse($completionDate, 'Australia/Melbourne')
                        ->addDay()->setHour(4)->setMinute(0)->setSecond(0))
            ) {
                $time_active = false;
            }

            $name = $users->getFullNameForQuestionnaire($q_token);

            $data = [
                'complete' => $completed,
                'void' => $void,
                'token' => $q_token,
                'title' => 'ALC - Declaration',
                'name' => $name,
                'time_active' => $time_active,
                'inactive' => $inactive,
                'self_generated' => $self_generated,
                'date_complete' => Carbon::parse($completionDate . ' +1 day', 'Australia/Melbourne')->format('d/m/Y')
            ];

            return view('questionnaires.manual.main', $data);
        }

        return view('questionnaires.global.none');
    }

    public function manualDeclaration()
    {
        $users = new Users();
        $address = $users->getAddress(session('user'));

        return view(
            'questionnaires.manual.declaration',
            ['address' => $address, 'token' => session('token'), 'user' => session('user'), 'user_wrong' => session('user_wrong')]
        );
    }

    public function manualOverview(Request $r)
    {
        $sent = new QuestionnaireSent();

        if ($sent->doesUserMatchToken($r->token, $r->user_id) === true) {
            return view(
                'questionnaires.manual.overview',
                [
                    'token' => $r->token,
                    'user' => $r->user_id,
                    'contact' => $r->{'covid-contact'},
                    'live' => $r->live,
                    'symptoms' => $r->symptoms,
                    'fail' => session('fail')
                ]
            );
        }

        $users = new Users();
        $address = $users->getAddress($r->user_id);
        return redirect('/dec/' . $r->token . '/declaration')->with(['address' => $address, 'token' => $r->token, 'user' => $r->user_id, 'user_wrong' => true]);
    }

    public function manualOverviewGet(Request $r)
    {
        $sent = new QuestionnaireSent();

        if ($sent->doesUserMatchToken($r->token, $r->user_id) === true) {
            return view(
                'questionnaires.manual.overview',
                [
                    'token' => session('token'),
                    'user' => session('user'),
                    'contact' => session('contact'),
                    'live' => session('live'),
                    'symptoms' => session('symptoms'),
                    'fail' => session('fail')
                ]
            );
        }

        $users = new Users();
        $address = $users->getAddress($r->user_id);
        return redirect('/dec/' . $r->token . '/declaration')->with(['address' => $address, 'token' => $r->token, 'user' => $r->user_id, 'user_wrong' => true]);
    }

    public function submitManual(Request $r)
    {
        $user = new Users();
        if ($r->restart) {
            $address = $user->getAddress($r->user_id);
            return redirect('/dec/' . $r->token . '/declaration')->with(['address' => $address, 'token' => $r->token, 'user' => $r->user_id]);
        }

        $sent = new QuestionnaireSent();
        if ($sent->doesUserMatchToken($r->token, $r->user_id) === true) {
            $answers = json_encode(['contact' => $r->{'covid-contact'}, 'symptoms' => $r->symptoms, 'contact_live' => $r->live]);
            $location = $user->getEmployeeLocation($r->token);

            $sent->testComplete($r->token, $answers);

            $data = $sent->getSingleCompletionStats($r->token);
            $success = '';

            foreach ($data as $key => $user_complete) {
                if ($user_complete->complete !== 0) {
                    $user_complete->complete = $success = array_search('yes', (array)json_decode($user_complete->answers)) ? 'TRUE' : 'FALSE';
                } else {
                    $user_complete->complete = $success = 'TRUE';
                }
                unset($user_complete->answers);
                unset($user_complete->id);
            }

            if ($location !== 'Sunshine') {
                $file_name = 'ams_access_eid' . $user->getEmployeeCode($r->token) . '_' . Carbon::now('Australia/Melbourne')->format('dmY_His') . '.csv';
                Excel::store(
                    new AccessControlExport($data),
                    $file_name
                );
                $smbClient = new SMBClientService();
                $upload_file = $smbClient->uploadTo(
                    Storage::disk('local')->path($file_name),
                    '/Integriti/' . $file_name
                );
            }

            return view('questionnaires.manual.complete', ['success' => $success]);
        }

        return redirect('/dec/' . $r->token . '/overview')->with(['live' => $r->live, 'contact' => $r->contact, 'symptoms' => $r->symptoms, 'token' => $r->token, 'user' => $r->user_id, 'fail' => true]);
    }

    public function tryAgain(Request $r)
    {
        return redirect('/dec/' . $r->token . '/manual');
    }
}
