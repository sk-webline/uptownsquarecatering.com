<!doctype html>
@if(\App\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
<html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@else
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endif
<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="app-url" content="{{ getBaseURL() }}">
	<meta name="file-base-url" content="{{ getFileBaseURL() }}">

	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- Favicon -->
	<link rel="icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">
	<title>{{ get_setting('website_name').' | '.get_setting('site_motto') }}</title>

	<!-- google font -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.css" rel="stylesheet">

	<!-- sk core css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/nano.min.css"/>
	<link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
    @if(\App\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
    <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-rtl.min.css') }}">
    @endif
    <link rel="stylesheet" href="{{ static_asset('assets/css/draganddrop.css') }}">
	<link rel="stylesheet" href="{{ static_asset('assets/css/master.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/custom_admin.css') }}">

    <script defer src='https://static.cloudflareinsights.com/beacon.min.js' data-cf-beacon='{"token": "dc4641f860664c6e824b093274f50291"}'></script>

    <style>
        body {
            font-size: 12px;
        }
    </style>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.es5.min.js"></script>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.global.min.js'></script>
    



	<script>
    	var SK = SK || {};
        SK.local = {
            nothing_found: '{{ translate('Nothing found') }}',
            choose_file: '{{ translate('Choose file') }}',
            file_selected: '{{ translate('File selected') }}',
            files_selected: '{{ translate('Files selected') }}',
            add_more_files: '{{ translate('Add more files') }}',
            adding_more_files: '{{ translate('Adding more files') }}',
            drop_files_here_paste_or: '{{ translate('Drop files here, paste or') }}',
            browse: '{{ translate('Browse') }}',
            upload_complete: '{{ translate('Upload complete') }}',
            upload_paused: '{{ translate('Upload paused') }}',
            resume_upload: '{{ translate('Resume upload') }}',
            pause_upload: '{{ translate('Pause upload') }}',
            retry_upload: '{{ translate('Retry upload') }}',
            cancel_upload: '{{ translate('Cancel upload') }}',
            uploading: '{{ translate('Uploading') }}',
            processing: '{{ translate('Processing') }}',
            complete: '{{ translate('Complete') }}',
            file: '{{ translate('File') }}',
            files: '{{ translate('Files') }}',
        }
	</script>

</head>
<body class="">

	<div class="sk-main-wrapper">
        @include('backend.inc.admin_sidenav')
		<div class="sk-content-wrapper">
            @include('backend.inc.admin_nav')
			<div class="sk-main-content">
				<div class="px-15px px-lg-25px">
                    @yield('content')
				</div>
				<div class="bg-white text-center py-3 px-15px px-lg-25px mt-auto">
					<p class="mb-0">&copy; {{ get_setting('site_name') }} @if(Auth::user()->user_type == 'admin') v{{ get_setting('current_version') }} @endif</p>
				</div>
			</div><!-- .sk-main-content -->
		</div><!-- .sk-content-wrapper -->
	</div><!-- .sk-main-wrapper -->

    @yield('modal')


	<script src="{{ static_asset('assets/js/vendors.js') }}" ></script>
	<script src="{{ static_asset('assets/js/master.js') }}" ></script>
	<script src="{{ static_asset('assets/js/custom-admin.js') }}" ></script>
    <script src="{{ static_asset('assets/js/table2excel.js') }}" ></script>



    @yield('script')

    <script type="text/javascript">
	    @foreach (session('flash_notification', collect())->toArray() as $message)
	        SK.plugins.notify('{{ $message['level'] }}', '{{ $message['message'] }}');
	    @endforeach


        if ($('#lang-change').length > 0) {
            $('#lang-change .dropdown-menu a').each(function() {
                $(this).on('click', function(e){
                    e.preventDefault();
                    var $this = $(this);
                    var locale = $this.data('flag');
                    $.post('{{ route('language.change') }}',{_token:'{{ csrf_token() }}', locale:locale}, function(data){
                        location.reload();
                    });

                });
            });
        }
        function menuSearch(){
			var filter, item;
			filter = $("#menu-search").val().toUpperCase();
			items = $("#main-menu").find("a");
			items = items.filter(function(i,item){
				if($(item).find(".sk-side-nav-text")[0].innerText.toUpperCase().indexOf(filter) > -1 && $(item).attr('href') !== '#'){
					return item;
				}
			});

			if(filter !== ''){
				$("#main-menu").addClass('d-none');
				$("#search-menu").html('')
				if(items.length > 0){
					for (i = 0; i < items.length; i++) {
						const text = $(items[i]).find(".sk-side-nav-text")[0].innerText;
						const link = $(items[i]).attr('href');
						 $("#search-menu").append(`<li class="sk-side-nav-item"><a href="${link}" class="sk-side-nav-link"><i class="las la-ellipsis-h sk-side-nav-icon"></i><span>${text}</span></a></li`);
					}
				}else{
					$("#search-menu").html(`<li class="sk-side-nav-item"><span	class="text-center text-muted d-block">{{ translate('Nothing Found') }}</span></li>`);
				}
			}else{
				$("#main-menu").removeClass('d-none');
				$("#search-menu").html('')
			}
        }
    </script>

</body>
</html>
