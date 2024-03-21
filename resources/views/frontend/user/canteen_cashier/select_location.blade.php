@extends('frontend.layouts.app_cashier')

@section('content')
    <section class="my-55px my-lg-90px my-xxl-125px">
        <div class="container">
            <div class="mb-40px mb-lg-70px mb-md-100px text-center">
                <svg class="h-90px h-md-120px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.47 76.24">
                    <use xlink:href="{{static_asset('assets/img/icons/logo-25.svg')}}#uptown-logo"></use>
                </svg>
            </div>
            <div class="profile">
                <div class="mw-600px mx-auto background-brand-grey px-20px px-md-50px pt-20px pt-md-30px pb-40px pb-md-70px fs-16 sm-fs-20 md-fs-25">
                    <h1 class="fs-25 sm-fs-30 md-fs-35 fw-700 text-center mb-25px mb-sm-30px mb-md-35px">{{toUpper(translate('Login'))}}</h1>

                    <form class="form-default ff-Manrope fw-500" role="form" action="{{route('canteen_cashier.location_selection')}}" method="POST">
                        @csrf

                        <div class="form-group mb-20px mb-md-25px">
                            <select id="organisation_select"
                                    class="form-control sk-selectpicker"
                                    name="organisation_id" onchange="show_locations()">
                                <option value="" >{{translate('Please Choose Organisation')}}</option>

                                @foreach($organisations as $organisation)
                                    <option value={{$organisation->id}}>{{$organisation->name}}</option>
                                @endforeach

                            </select>

                        </div>

                        <div class="form-group mb-20px mb-md-25px">
                            {{--                            <input type="text" class="fs-25 sm-fs-25 bg-white form-control {{ $errors->has('username') ? ' is-invalid' : '' }}" placeholder="{{translate('Username')}}" value="{{ old('username') }}" name="username">--}}
                            <select id="location_select" name="location_id"
                                    class="form-control sk-selectpicker">
                                <option value="" >{{translate('Please Choose Location')}}</option>
                            </select>
                        </div>

                        <button type="submit"
                                class="btn btn-primary btn-block fw-500">{{toUpper(translate('Continue'))}}</button>
                    </form>

                    @if (env("DEMO_MODE") == "On")
                        <div class="mb-5">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                <tr>
                                    <td>{{ translate('Seller Account')}}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm"
                                                onclick="autoFillSeller()">{{ translate('Copy credentials') }}</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ translate('Customer Account')}}</td>
                                    <td>
                                        <button class="btn btn-info btn-sm"
                                                onclick="autoFillCustomer()">{{ translate('Copy credentials') }}</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <footer class="fs-13 md-fs-15 lh-1 cashier-footer">
            <div class="py-5px">

                <?php

                use Illuminate\Support\Facades\Session;
                $user = \Illuminate\Support\Facades\Auth::user();

//                ECHO $user;
                $organisation = '-';
                $location = '-';
                ?>
                <div class="cashier-grid">
                    <div class="row align-items-center align-text-bottom">
                        <div class="col-auto d-none d-md-block border-right-white ">
                            <div class="row">
                                <div class="col sk-side-nav-icon d-inline-block ">
                                    <svg class="h-10px" xmlns="http://www.w3.org/2000/svg" fill="white"
                                         viewBox="0 0 15.61 16.1">
                                        <use
                                            xlink:href="{{static_asset('assets/img/icons/my-account-icon.svg')}}#my_account"></use>
                                    </svg>
                                </div>
                                <div class="col-auto text-left pl-0">{{ $user->name }}</div>
                            </div>
                        </div>

                        <div class="col text-right">
                            <a href="{{route('logout')}}">{{translate('Logout')}}
                                <svg class="h-10px" xmlns="http://www.w3.org/2000/svg" fill="white"
                                     viewBox="0 0 16.19 11.86">
                                    <use xlink:href="{{static_asset('assets/img/icons/logout_icon.svg')}}#logout_svg"></use>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </section>



@endsection

@section('script')
    <script type="text/javascript">
        function show_locations(){
            console.log($('#organisation_select').val());

            $('#location_select').empty();
            $('#location_select').append(new Option('Please Choose Location', ""))
            $('#location_select').selectpicker('refresh');

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('canteen_cashier.get_canteen_locations') }}',
                data: {
                    organisation_id: $('#organisation_select').val()
                },
                dataType: "JSON",
                success: function (data) {

                    console.log('data: ');
                    console.log('response: ', data);

                    for (var i = 0; i<data.length; i++){
                        $('#location_select').append(new Option(data[i].name, data[i].id))
                    }

                    $('#location_select').selectpicker('refresh');

                    console.log(' te');
                },
                error: function () {



                }
            });
        }

        // function autoFillCustomer(){
        //     $('#username').val('customer@example.com');
        //     $('#password').val('123456');
        // }
    </script>
@endsection
