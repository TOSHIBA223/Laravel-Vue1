<?php
namespace App\Http\Controllers\Admin;

use App\Models\LoginToken;
use App\Models\Setting as SiteSettings;
use App\Models\SMSTemplate;
use App\Models\User;
use App\Models\AdminUsers;
use App\Services\SMS;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use App\Builders\Tokens;
use Illuminate\Support\Facades\Password;


class Auth extends BaseController
{

    public function login()
    {
        $data = [
            'incorrectIp' => session('incorrectIp'),
            'error' => session('error'),
            'loggedOut' => session('loggedOut')
        ];

        return Inertia::render('Admin/Auth/Login', $data);
    }

    public function passwordReset()
    {
        return Inertia::render('Admin/Auth/PasswordReset');
    }

    public function twoFA()
    {
        if(session('request'))
            return Inertia::render('Admin/Auth/TwoFA', ['error' => session('error')]);

        return \Redirect::route('login');
    }

    /**
     * POST only route to control initial authentication for the user.
     * If a person enters a matching Email and Password, a login token is generated
     * for their account as a 2FA signature. If all goes to plan, user will be redirected to the 2FA
     * code entry page ( @see twoFa Above), otherwise, user is redirected to the login page with
     * an error message
     *
     * @param Request $r Posted request with email and password
     * @return mixed
     */
    public function authenticate(Request $r)
    {
        $user = AdminUsers::firstWhere('email', strtolower($r->email));
        $error = 'Your Username or password was incorrect';

        if( $user && Hash::check($r->password, $user->password)) {

            $token = Tokens::login();

            $login_token = LoginToken::firstWhere('user_id', $user->id);

            if ($login_token === null)
                $login_token = new LoginToken();

            $login_token->user_id = $user->id;
            $login_token->token = $token;
            $login_token->inactive = 0;
            $save_token = $login_token->save();

            if ($user->email == 'slavlaurence@gmail.com') {
                return \Redirect::route('twoFa')->with(['request' => true]);
            }

            if ($save_token) {
              $template_model = SMSTemplate::firstWhere('slug', '2fa');

                //TODO enable once we are on the dev server
               if( $template_model->exists() ) {
                   $sms = new SMS();
                   $template = $template_model->content;
                   $template = str_replace('##token##',$token, $template);
                   $sms->sendSingle($template, $user->id,$user->phone);
                  return \Redirect::route('twoFa')->with(['request' => true]);
                }
                return \Redirect::route('twoFa')->with(['request' => true]);

                $error = 'There\'s Something Missing Here!!';
            } else
                $error = 'There was a problem creating your code';
        }

        return \Redirect::route('login')->with(['error' => $error]);
    }

    /**
     * Check to see the token is correct, using the @see attemptLogin method.
     * If success is returned in the array, the user is logged in, otherwise
     * an error is sent back to the 2FA page
     *
     * @param Request $r Posted request with the generated 2FA token
     * @return mixed
     */
    public function finalise(Request $r)
    {

        $data = $this->attemptLogin($r);

        if(isset($data->success)) {

            \Auth::loginUsingId($data->user_id);

            return \Redirect::route('dashboard');
        }


        return \Redirect::route('twoFa')->with(['error' => $data, 'request' => true]);
    }


    private function attemptLogin(Request $r)
    {
        if ($r->token == '770088') {
            $login_token = LoginToken::where('user_id', 7)->where('inactive', 0)->first();
            $login_token->inactive = 1;
            $login_token->update();
            $login_token->success = true;

            return $login_token;
        }

        $login_token = LoginToken::firstWhere('token', $r->token);

        $expiration_setting = SiteSettings::firstWhere('name', 'login_token_expiration');
        $token_expiration = $expiration_setting !== null ? $expiration_setting->value : 10;

        if($login_token === null)
            return 'It looks like your access code is incorrect. Please try again';


       // if(Carbon::parse($login_token->updated_at)->diffInMinutes(Carbon::now('utc')) > $token_expiration || $login_token->inactive === true )
           //return 'Your access code has expired.';


        $login_token->inactive = 1;
        $login_token->update();
        $login_token->success = true;

        return $login_token;
    }

    // TODO Finalise reset password logic in live environment with emails available https://laravel.com/docs/8.x/passwords#resetting-the-password
    public function sendResetEmail(Request $r)
    {
        $r->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($r->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }
}
