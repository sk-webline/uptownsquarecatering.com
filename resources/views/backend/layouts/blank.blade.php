<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ env('APP_URL')}}">

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicon -->
  	<title>{{ config('app.name', 'eCommerce') }}</title>

    <!-- google font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">

    <!-- sk core css -->
    <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/master.css') }}">

    <script>
        var SK = SK || {};
    </script>
</head>
<body>
    <div class="sk-main-wrapper d-flex">

        <div class="flex-grow-1">
            @yield('content')
        </div>

    </div><!-- .sk-main-wrapper -->
    <script src="{{ static_asset('assets/js/vendors.js') }}" ></script>
    <script src="{{ static_asset('assets/js/master.js') }}" ></script>

    @yield('script')

    <script type="text/javascript">
    @foreach (session('flash_notification', collect())->toArray() as $message)
        SK.plugins.notify('{{ $message['level'] }}', '{{ $message['message'] }}');
    @endforeach
    </script>
</body>
</html>
