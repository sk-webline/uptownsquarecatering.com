@extends('frontend.layouts.app_cashier')

@section('content')

    <div id="content_body" class="mh-100-svh">

        <?php

        $buffet_choice = null;

        $max_snack = $organisation_setting->max_snack_quantity;
        $max_meal = $organisation_setting->max_meal_quantity;
        ?>

        @include('frontend.inc.cashier_nav', ['route' => null, 'route_text' => null])

        <div class="cashier-body">
            <div class="cashier-grid flex-grow-1 d-flex flex-column">
                <h1 class="fw-300 fs-20 md-fs-35 xxl-fs-50 m-0 l-space-05 text-center">{{translate('What is being served now?')}}</h1>
                <div class="flex-grow-1 mt-10px mt-md-30px mt-lg-50px cashier-body-buffet-boxes">
                    <div class="row xl-gutters-35 justify-content-center">

                        @if($max_snack>0)
                            <div class=" col-lg-6 mb-20px mb-lg-0">
                                <button id="snack" class="w-100 c-pointer catering-choice clickedSelection ">
                                    <div class="text-center background-soft-grey cashier-buffet-box">
                                        <div class="d-flex flex-column cashier-buffet-box-wrap">
                                            <div class="d-flex flex-grow-1 flex-column justify-content-center">
                                                <svg class="h-40px h-md-160px h-xxl-270px cashier-buffet-box-image"
                                                     xmlns="http://www.w3.org/2000/svg"
                                                     {{--                                 height="300" width="300"--}}
                                                     viewBox="0 0 290.03 299.7" fill="var(--primary)">
                                                    <use
                                                        xlink:href="{{static_asset('assets/img/icons/snack_icon.svg')}}#snack_icon"></use>
                                                </svg>
                                            </div>

                                            <div
                                                class="fs-15 md-fs-20 text-primary-60 fw-700 l-space-05 mt-10px mt-md-20px">{{toUpper(translate('Snack'))}}</div>
                                        </div>
                                    </div>
                                </button>
                            </div>

                        @endif
                        @if($max_meal>0)
                            <div class="col-lg-6 mb-20px mb-lg-0">
                                <button id="lunch" class="w-100 c-pointer catering-choice clickedSelection">
                                    <div class="text-center background-soft-grey cashier-buffet-box">
                                        <div class="d-flex flex-column cashier-buffet-box-wrap">
                                            <div class="d-flex flex-grow-1 flex-column justify-content-center">
                                                <svg
                                                    class="h-40px h-md-160px h-xxl-270px cashier-buffet-box-image"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    {{--                             height="300" width="300"--}}
                                                    viewBox="0 0 296.31 248.78" fill="var(--primary)">
                                                    <use
                                                        xlink:href="{{static_asset('assets/img/icons/lunch_icon.svg')}}#lunch_icon"></use>
                                                </svg>
                                            </div>

                                            <div
                                                class="fs-15 md-fs-20 text-primary-60 fw-700 l-space-05 mt-10px mt-md-20px">{{toUpper(translate('Lunch'))}}</div>

                                        </div>
                                    </div>
                                </button>
                            </div>

                        @endif

                    </div>
                </div>
                <div class="text-right mt-10px mt-md-20px">
                    <a id="okay_btn" class="btn border-none bg-transparent p-0"
                       style="opacity: 0.5">
                        <svg
                            class="h-50px h-md-100px h-xl-150px"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 149.51 149.51">
                            <use
                                xlink:href="{{static_asset('assets/img/icons/catering_ok_button.svg')}}#catering-ok-btn"></use>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @include('frontend.inc.cashier_footer')
    </div>

@endsection

@section('script')
    <script type="text/javascript">

        function selectBuffetType() {
            console.log(this.id);
        }

        $(".clickedSelection").click(function (e) {
            e.preventDefault();

            if (this.id == 'snack') {
                console.log(this.id);
                $('#snack').addClass('active');
                $('#lunch').removeClass('active');
                $('#okay_btn').attr("href", '{{route('cashier.buffet_serving', ['type' => 'snack'])}}');

                <?php

                ?>
            } else {
                $('#lunch').addClass('active');
                $('#snack').removeClass('active');

                $('#okay_btn').attr("href", '{{route('cashier.buffet_serving', ['type' => 'lunch'])}}');
            }

            $('#okay_btn').css('opacity', '1');

        });
    </script>
@endsection
