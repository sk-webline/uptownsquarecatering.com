<?php

namespace App\Http\Controllers\Auth;

use App\Models\Card;
use App\Models\Organisation;
use App\User;
use App\Customer;
use App\BusinessSetting;
use App\OtpConfiguration;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OTPVerificationController;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Cookie;
use Nexmo;
use Twilio\Rest\Client;
use App\Http\Controllers\CardController;
use Mail;
use App\Mail\RegisterAdminMailManager;
use App\Mail\RegisterUserMailManager;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone_code' => 'required',
            'phone' => 'required|numeric',
            'country' => 'required|numeric',
            'city' => 'required|numeric',
            'password' => 'required|string|min:6|confirmed',
            'g-recaptcha-response' => 'required|google_captcha',
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function customer_validator(array $data)
    {
        $rules = [
            'card_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed'
        ];

        if ($data['required_field_name'] !== null) {
            $rules['required_field_value'] = 'required';
        }
        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $user = User::create([
                'name' => $data['name'] . " " . $data['surname'],
                'email' => $data['email'],
                'phone_code' => $data['phone_code'],
                'phone' => $data['phone'],
                'country' => $data['country'],
                'city' => $data['city'],
                'password' => Hash::make($data['password']),
            ]);

            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->save();
        }
        else {
            if (\App\Addon::where('unique_identifier', 'otp_system')->first() != null && \App\Addon::where('unique_identifier', 'otp_system')->first()->activated){
                $user = User::create([
                    'name' => $data['name'] . " " . $data['surname'],
                    'phone_code' => $data['phone_code'],
                    'phone' => $data['phone'],
                    'country' => $data['country'],
                    'city' => $data['city'],
                    'password' => Hash::make($data['password']),
                    'verification_code' => rand(100000, 999999)
                ]);

                $customer = new Customer;
                $customer->user_id = $user->id;
                $customer->save();

                $otpController = new OTPVerificationController;
                $otpController->send_code($user);
            }
        }

        if(Cookie::has('referral_code')){
            $referral_code = Cookie::get('referral_code');
            $referred_by_user = User::where('referral_code', $referral_code)->first();
            if($referred_by_user != null){
                $user->referred_by = $referred_by_user->id;
                $user->save();
            }
        }

        return $user;
    }

    public function register(Request $request)
    {
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            if(User::where('email', $request->email)->first() != null){
                flash(translate('Email or Phone already exists.'));
                return back();
            }


        }
        elseif (User::where('phone', '+'.$request->country_code.$request->phone)->first() != null) {
            flash(translate('Phone already exists.'));
            return back();
        }

        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        $this->guard()->login($user);

        if($user->email != null){
            if(BusinessSetting::where('type', 'email_verification')->first()->value != 1){
                $user->email_verified_at = date('Y-m-d H:m:s');
                $user->save();
                flash(translate('Registration successfull.'))->success();
            }
            else {
                event(new Registered($user));
                flash(translate('Registration successfull. Please verify your email.'))->success();
            }
        }

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    protected function registered(Request $request, $user)
    {
        if ($user->email == null) {
            return redirect()->route('verification');
        }
        else {
            return redirect()->route('dashboard');
        }
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create_customer(array $data)
    {
        if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $user = User::create([
                'name' => $data['name'] . " " . $data['surname'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->save();
        }

        if(Cookie::has('referral_code')){
            $referral_code = Cookie::get('referral_code');
            $referred_by_user = User::where('referral_code', $referral_code)->first();
            if($referred_by_user != null){
                $user->referred_by = $referred_by_user->id;
                $user->save();
            }
        }

        return $user;
    }

    public function register_customer(Request $request)
    {

        $customer_validator = $this->customer_validator($request->all());

        if ($customer_validator->fails()) {
            $request_data = $request->all();
            $request_data['create_account'] = true;
            return back()->withErrors($customer_validator)->withInput($request_data);
        }

        $card = Card::where('rfid_no',$request->card_to_register)->first();

        if ($card!=null && ($card->user_id == null || $card->user_id == '')) {
            $user = $this->create_customer($request->all());
            $user->save();

            $organisation = Organisation::findorfail($card->organisation_id);
            $card->user_id = $user->id;
            $card->required_field_name = $organisation->required_field_name;
            $card->required_field_value = $request->required_field_value;
            $card->name = $request->card_name;
            $card->save();

        } else {
            flash(translate('This card is already registerd in a different account!'))->error();
            return back();
        }

        $this->guard()->login($user);


        if($user->email != null){
            if(BusinessSetting::where('type', 'email_verification')->first()->value != 1){
                $user->email_verified_at = date('Y-m-d H:m:s');
                $user->save();
                flash(translate('Registration successfull.'))->success();
            }
            else {
                event(new Registered($user));
                flash(translate('Registration successfull. Please verify your email.'))->success();
            }

            $array['from'] = env('MAIL_USERNAME');
            $array['name'] = $user->name;
            $array['email'] = $user->email;

            $array['view'] = 'emails.register_admin';
            $array['subject'] = 'You have new user registration from '.env('APP_NAME');
            $sender = config('app.contact_email');
            Mail::to($sender)->queue(new RegisterAdminMailManager($array));

            $array['view'] = 'emails.register_user';
            $array['subject'] = 'Registration confirmation from '.env('APP_NAME');
            $sender = $user->email;
            Mail::to($sender)->queue(new RegisterUserMailManager($array));
        }

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }
}
