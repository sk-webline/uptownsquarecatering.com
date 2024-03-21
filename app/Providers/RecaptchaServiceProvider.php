<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class RecaptchaServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
        $request = $this->app->request;
        Validator::extend('google_captcha', function ($attribute, $value, $parameters, $validator) use ($request) {

            $recaptcha_keys = getRecaptchaKeys($request->getHost());

            $post_data = [
                'secret' => $recaptcha_keys->secret,
                'response' => $value,
            ];

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $response = json_decode(curl_exec($curl));
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                return false;
            } else {
                if (!$response->success) {

                    $errorMessage = null;
                    collect($response->{"error-codes"})->each(function ($item) use (&$errorMessage) {
                        $errorMessage .= config('google_captcha.error_codes')[$item];
                    });

                    $validator->addReplacer('google_captcha',
                        function ($message, $attribute, $rule, $parameters) use ($errorMessage) {
                            return \str_replace(':message', $errorMessage, $message);
                        }
                    );
                }

                return $response->success;
            }
        },":message");
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }
}
