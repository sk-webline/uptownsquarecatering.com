<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\LoginLog;
use App\Models\Organisation;
use App\Models\PlatformSetting;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use Socialite;
use Session;
use App\User;
use App\Customer;
use Illuminate\Http\Request;
use CoreComponentRepository;
use Illuminate\Support\Str;
use App\Models\Cart;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    private string $auth_type = 'email';

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    /*protected $redirectTo = '/';*/

    /**
      * Redirect the user to the Google authentication page.
      *
      * @return \Illuminate\Http\Response
      */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request, $provider)
    {
        try {
            if($provider == 'twitter'){
                $user = Socialite::driver('twitter')->user();
            }
            else{
                $user = Socialite::driver($provider)->stateless()->user();
            }
        } catch (\Exception $e) {
            flash("Something Went wrong. Please try again.")->error();
            return redirect()->route('user.login');
        }

        // check if they're an existing user
        $existingUser = User::where('provider_id', $user->id)->orWhere('email', $user->email)->first();

        if($existingUser){
            // log them in
            $response = auth()->login($existingUser, true);
        } else {
            // create a new user
            $newUser                  = new User;
            $newUser->name            = $user->name;
            $newUser->email           = $user->email;
            $newUser->email_verified_at = date('Y-m-d H:m:s');
            $newUser->provider_id     = $user->id;
            $newUser->save();

            $customer = new Customer;
            $customer->user_id = $newUser->id;
            $customer->save();

            auth()->login($newUser, true);
        }
        if(session('link') != null){
            return redirect(session('link'));
        }
        else{
            return redirect()->route('dashboard');
        }
    }

    /**
        * Get the needed authorization credentials from the request.
        *
        * @param  \Illuminate\Http\Request  $request
        * @return array
        */
       protected function credentials(Request $request)
       {
//           if(filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)){
               return $request->only($this->username(), 'password');
//               return $request->only($request->get('username'), 'password');
//           }
//           return ['phone'=>$request->get('email'),'password'=>$request->get('password')];
           /*if(filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)){
               return $request->only($this->username(), 'password');
//               return $request->only($request->get('username'), 'password');
           }
           return ['phone'=>$request->get('email'),'password'=>$request->get('password')];*/
       }

    /**
     * Check user's role and redirect user based on their role
     * @return
     */
    public function authenticated(Request $request)
    {
        if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff')
        {
            return redirect()->route('admin.dashboard');
        }else if(auth()->user()->user_type == 'cashier'){

            if(auth()->user()->active==1){
                return redirect()->route('cashier.select_location');
            }else{
                return redirect()->route('logout');
            }

        } else {

            $request->session()->put('checked_full_dates_card_id', '-');

            if($request->has('card_to_register')){
                $card = Card::where('rfid_no', $request->card_to_register)->first();

                if($card != null && ($card->user_id == null)){
                    $card->user_id = auth()->user()->id;
                    $card->required_field_value = $request->required_field_value;
                    $card->name = $request->card_name;
                    $card->save();
                }else{

                    flash(translate("Something went wrong!"))->error();
                    return redirect()->route('logout');

                }


            }

            return redirect()->route('dashboard');

        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {

        if($request->card_to_register!=''){
            $request_data_log = $request->all();
            $request_data_log['login_and_register'] = true;
            flash(translate('Invalid email or password'))->error();
            return back()->withInput($request_data_log);
        }


        flash(translate('Invalid email or password'))->error();
        return back();
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        if(auth()->user() != null && (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff')){
            $redirect_route = 'login';
        }else if(auth()->user() != null && auth()->user()->user_type == 'cashier'){

            flash(translate('Inactive User'))->error();
            $redirect_route = 'cashier.login';
        }
        else{
            $redirect_route = 'home';
        }

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect()->route($redirect_route);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
//        $this->auth_type = 'username';

        if ($request->has('username')) {
            $this->auth_type = 'username';
        }

        $this->middleware('guest')->except('logout');
    }

    public function username(): string
    {
        return $this->auth_type;
    }

    public function login(Request $request)
    {

        $ip_address = $this->get_client_ip();

        $login_lock_check_minutes = PlatformSetting::where('type', 'login_lock_check_minutes')->first()->value;
        $login_mistakes_lock_num = PlatformSetting::where('type', 'login_mistakes_lock_num')->first()->value;

        $login_lock_minutes = PlatformSetting::where('type', 'login_lock_minutes')->first()->value;
        $minutes_ago = Carbon::now()->subMinutes($login_lock_check_minutes+$login_mistakes_lock_num);

         $fail_login_attempts = LoginLog::where('ip_address', $ip_address)->where('status', '=', 'fail')->where('created_at', '>=', ($minutes_ago))
           ->orderBy('created_at','desc')->get();


        if(count($fail_login_attempts)>=$login_mistakes_lock_num){

             $time = Carbon::create($fail_login_attempts->first()->created_at)->addMinutes($login_lock_minutes);

             $now = Carbon::now();

             if(!($now->gte($time))){
                 throw \Illuminate\Validation\ValidationException::withMessages([
                     'fail_attempts_login_error' => ["You have excited the number of allowed login tries. Please come back in $login_lock_minutes minutes."],
                 ]);
             }

        }

        $this->validateLogin($request);


        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {

            $login_log = new LoginLog();
            $login_log->ip_address = $ip_address;
            $login_log->status = 'success';
            $login_log->save();

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        $login_log = new LoginLog();
        $login_log->ip_address = $ip_address;
        $login_log->status = 'fail';
        $login_log->save();

        return $this->sendFailedLoginResponse($request);
    }

    // Function to get the client IP address
     function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}
