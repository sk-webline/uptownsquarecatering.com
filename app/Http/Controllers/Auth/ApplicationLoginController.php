<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Controller;
use App\Models\CanteenAppUser;
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
use Auth;

class ApplicationLoginController extends Controller
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

    private string $auth_type = 'username';

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    /*protected $redirectTo = '/';*/

    public function __construct()
    {
        $this->middleware('guest:application')->except('logout');
    }

    // Override the guard method to specify the 'employee' guard
    protected function guard()
    {
        return Auth::guard('application');
    }

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
            return redirect()->route('application.login');
        }

        // check if they're an existing user
        $existingUser = CanteenAppUser::where('username', $user->username)->first();

        if($existingUser){
            // log them in
            $response = auth()->guard('application')->login($existingUser, true);
        } else {
//            // create a new user
//            $newUser                  = new User;
//            $newUser->name            = $user->name;
//            $newUser->email           = $user->email;
//            $newUser->email_verified_at = date('Y-m-d H:m:s');
//            $newUser->provider_id     = $user->id;
//            $newUser->save();
//
//            $customer = new Customer;
//            $customer->user_id = $newUser->id;
//            $customer->save();
//
//            auth()->login($newUser, true);

            flash("Something Went wrong. Please try again.")->error();
            return redirect()->route('application.login');
        }

        if(session('link') != null){
            return redirect(session('link'));
        }
        else{
            return redirect()->route('application.home');
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

        return $request->only($this->username(), 'password');

    }

    /**
     * Check user's role and redirect user based on their role
     * @return
     */
    public function authenticated(Request $request)
    {
        Cart::mergeApplicationSessionAndDBProductsCart();
        ApplicationController::updateTotals();
        return redirect()->route('application.home');

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

        flash(translate('Invalid username or password'))->error();
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

        $redirect_route = 'application.login';

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect()->route($redirect_route);
    }


    public function username(): string
    {
        return $this->auth_type;
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        // You may customize this method based on your custom guard and login logic.
        // For example, if you have a 'custom' guard:
        return Auth::guard('application')->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }


    public function login(Request $request)
    {
        $this->validateLogin($request);


        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
//        if (method_exists($this, 'hasTooManyLoginAttempts') &&
//            $this->hasTooManyLoginAttempts($request)) {
//            $this->fireLockoutEvent($request);
//
//            return $this->sendLockoutResponse($request);
//        }

        if ($this->attemptLogin($request)) {

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

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
