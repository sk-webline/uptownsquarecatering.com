<p>{{ translate('Dear') }} {{ $name }}</p>
<p>{{ translate('Thank you for registering in our platform.') }} {{translate('You can')}} <a href="{{ route('user.login') }}">{{translate('login here')}}</a> {{ translate('with your email:') }} {{ $email }} {{ translate('and your password.') }}</p>
