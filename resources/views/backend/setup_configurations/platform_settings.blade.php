@extends('backend.layouts.app')

@section('content')

    <h4 class="text-center text-muted">{{translate('Catering')}}</h4>
    <div class="row">

        <?php

        use App\Models\PlatformSetting;

        $vat = PlatformSetting::where('type', 'vat_percentage')->first();
        $cancel_minutes = PlatformSetting::where('type', 'minutes_for_cancel_meals')->first();

        $max_attempts = PlatformSetting::where('type', 'login_mistakes_lock_num')->first()->value;

        $lock_minutes = PlatformSetting::where('type', 'login_lock_minutes')->first()->value;

        $login_lock_check_minutes = PlatformSetting::where('type', 'login_lock_check_minutes')->first()->value;

        ?>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6 text-center">{{translate('VAT Percentage %')}}</h5>
                </div>
                <div class="card-body text-center">
                    <form class="form-horizontal" class="form-default" action="{{ route('platform_settings.set_vat') }}"
                          method="POST" enctype="multipart/form-data">

                        @csrf

                        <div class="form-group mb-10px">
                            <div class="form-control-with-label small-focus small-field">
                                <input type="number" class="form-control" name="vat_percentage" value="{{$vat->value}}"
                                       required>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6 text-center">{{translate('Minutes For Meal Cancellation')}}</h5>
                </div>
                <div class="card-body text-center">
                    <form class="form-horizontal" class="form-default" action="{{ route('platform_settings.set_minutes_cancel') }}"
                          method="POST" enctype="multipart/form-data">

                        @csrf

                        <div class="form-group mb-10px">
                            <div class="form-control-with-label small-focus small-field">
                                <input type="number" class="form-control" name="cancel_minutes" value="{{$cancel_minutes->value}}"
                                       required>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <h4 class="text-center text-muted">{{translate('Login Lock')}}</h4>
    <div class="row">


        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6 text-center">{{translate('Max Failed Login Attempts')}}</h5>
                </div>
                <div class="card-body text-center">
                    <form class="form-horizontal" class="form-default" action="{{ route('platform_settings.set_max_failed_login_attempts') }}"
                          method="POST" enctype="multipart/form-data">

                        @csrf

                        <div class="form-group mb-10px">
                            <div class="form-control-with-label small-focus small-field">
                                <input type="number" class="form-control" name="max_attempts" value="{{$max_attempts}}"
                                       required>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6 text-center">{{translate('Lock Minutes')}}</h5>
                </div>
                <div class="card-body text-center">
                    <form class="form-horizontal" class="form-default" action="{{ route('platform_settings.set_lock_minutes') }}"
                          method="POST" enctype="multipart/form-data">

                        @csrf

                        <div class="form-group mb-10px">
                            <div class="form-control-with-label small-focus small-field">
                                <input type="number" class="form-control" name="lock_minutes" value="{{$lock_minutes}}"
                                       required>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6 text-center">{{translate('Check Lock Minutes')}}</h5>
                </div>
                <div class="card-body text-center">
                    <form class="form-horizontal" class="form-default" action="{{ route('platform_settings.set_check_lock_minutes') }}"
                          method="POST" enctype="multipart/form-data">

                        @csrf

                        <div class="form-group mb-10px">
                            <div class="form-control-with-label small-focus small-field">
                                <input type="number" class="form-control" name="lock_check_minutes" value="{{$login_lock_check_minutes}}"
                                       required>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>




@endsection

@section('script')
    <script type="text/javascript">
        function updateSettings(el, type) {
            if ($(el).is(':checked')) {
                var value = 1;
            } else {
                var value = 0;
            }
            $.post('{{ route('business_settings.update.activation') }}', {
                _token: '{{ csrf_token() }}',
                type: type,
                value: value
            }, function (data) {
                if (data == '1') {
                    SK.plugins.notify('success', 'Settings updated successfully');
                } else {
                    SK.plugins.notify('danger', 'Something went wrong');
                }
            });
        }
    </script>
@endsection
