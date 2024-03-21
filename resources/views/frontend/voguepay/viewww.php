<div class="mt-4">

    <?php

    use Carbon\Carbon;
    use App\Models\PlatformSetting;

    $vat = PlatformSetting::where('type', 'vat_percentage')->first()->value;

    $total = 0;
    $total_vat = 0;
    $total_price = 0;

    ?>

    <div class="row no-gutters text-uppercase opacity-70 border-bottom-grey pb-2">
        <div class="col-8 no-gutters">
            <span>{{translate('Subscriptions')}}</span>
        </div>

        <div class="col-1 no-gutters  my-auto">
        </div>

        <div class="col-1 no-gutters ">
            <span>{{translate('Price')}}</span>
        </div>
        <div class="col-1 no-gutters text-center ">
            <span>{{translate('VAT')}} {{$vat}}%</span>
        </div>
        <div class="col-1 no-gutters text-right">
            <span>{{translate('Amount')}}</span>
        </div>
    </div>

    @foreach(Session::get('cart') as $key => $cart_item)

    <div class="row no-gutters border-bottom-grey py-3">
        <div class="col-8 no-gutters">
            {{--                        <span>{{$cart_item['price']}}</span>--}}

            <div class="d-inline-block my-auto w-50  ">

                <div class="row no-gutters">

                    <div class="col-6 no-gutters border-primary">

                        <div
                            class="w-100  border-bottom-primary text-uppercase text-primary p-2 fw-700">
                            <span> {{$cart_item['name']}} </span>
                        </div>

                        <div class="w-100  text-primary opacity-70 p-2">

                            <div>

                                           <span
                                               class="text-uppercase text-primary fs-16 fw-600 h-10px  mb-1 pr-2 fw-600">
                                               {{ translate('Snack') }}:
                                           </span>
                                <span class=" fs-13 fw-500 opacity-80 "> {{ $cart_item['snack_num'] }}
                                                @if($cart_item['snack_num']==0)
                                                @elseif($cart_item['snack_num']==1)
                                                    {{ translate('Snack per day') }}
                                                @else
                                                    {{ translate('Snacks per day') }}
                                                @endif
                                            </span>

                            </div>

                            <div class="mt-2">
                                           <span class="text-uppercase text-primary fs-16 fw-600 h-10px  mb-1 pr-2 ">
                                               {{ translate('Lunch') }}:
                                           </span>
                                <span class=" fs-13 fw-500 opacity-80"> {{ $cart_item['meal_num'] }}
                                                @if($cart_item['meal_num']==0)
                                                @elseif($cart_item['meal_num']==1)
                                                    {{ translate('Lunch per day') }}
                                                @else
                                                    {{ translate('Lunches per day') }}
                                                @endif
                                            </span>

                            </div>
                        </div>

                    </div>

                    <div class="col-6 no-gutters">


                        <div class="d-inline-block my-auto pl-2">

                            <a href="javascript:void(0)" onclick="removeFromCartView(event, {{ $key }})"
                               class="hov-text-primary fs-10 md-fs-12 fw-600 text-black-30 cart-trash-action">
                                             <span
                                                 class="pl-2 text-uppercase fw-500 fs-12 text-underline c-pointer opacity-70 ">  {{ translate('Delete') }} </span>
                            </a>


                            <?php

                            $card = \App\Models\Card::findorfail($cart_item['card_id']);


                            $card_name = $card->name;
                            //                                            dd($cart_item['card_id']);

                            $start_date = Carbon::create($cart_item['from_date'])->format('d/m/Y');
                            $end_date = Carbon::create($cart_item['to_date'])->format('d/m/Y');


                            ?>

                            <div class="w-100 text-uppercase text-primary px-2 fw-500 mt-70px fs-14">
                                <span class=" ">  {{ translate('To') }}: {{$card_name}} </span>
                            </div>
                            <div
                                class="w-100 text-uppercase opacity-70 text-primary px-2 pt-1 pt-0 fw-500 fs-14">
                                <span class=" "> {{$start_date}} - {{$end_date}} </span>
                            </div>


                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-1 no-gutters  my-auto">
        </div>
        <div class="col-1 no-gutters  my-auto">
            <span class="fs-19 opacity-60 fw-500">{{format_price($cart_item['price'])}}</span>
        </div>
        <div class="col-1 no-gutters text-center my-auto">
            <span class="fs-19 opacity-60 fw-500">{{format_price($cart_item['price'] * ($vat/100) )}}</span>
        </div>
        <div class="col-1 no-gutters text-right my-auto">
                        <span
                            class="fs-19 opacity-60 fw-700">{{format_price($cart_item['price'] + ($cart_item['price'] * ($vat/100) ))}}</span>
        </div>

        <?php
        $total_price = $total_price + $cart_item['price'];

        $total_vat = $total_vat + $cart_item['price'] * ($vat / 100);

        $total = $total + ($cart_item['price'] + ($cart_item['price'] * ($vat / 100)));

        ?>
    </div>

    @endforeach


</div>

<div class="mt-4">

    <div class="row no-gutters text-uppercase   pb-2">
        <div class="col-9 no-gutters">
            <div class="px-4 opacity-60">

                <svg class=" h-lg-30px colorChange" fill="var(--brand-gray)"
                     xmlns="http://www.w3.org/2000/svg" height="50" width="110"
                     viewBox="0 0 106.72 15.96">
                    <use
                        xlink:href="{{static_asset('assets/img/icons/viva_wallet.svg')}}#viva_wallet_svg"></use>
                </svg>
            </div>
        </div>

        <div class="col-3 no-gutters">


            <div class="row no-gutters">
                <div class="col-6 no-gutters  fs-15 opacity-70">
                    <span>{{translate('Subtotal')}}</span>
                </div>
                <div class="col-6 no-gutters text-right fs-16 fw-600 opacity-70">
                    <span>{{format_price($total_price)}}</span>
                </div>
            </div>

            <div class="row no-gutters">
                <div class="col-6 mt-3 no-gutters  fs-15 opacity-70">
                    <span>{{translate('VAT')}} {{$vat}}%</span>
                </div>
                <div class="col-6 mt-3 no-gutters text-right fs-16 fw-600 opacity-70">
                    <span>{{format_price($total_vat)}}</span>
                </div>
            </div>


            <div class="row no-gutters mt-2 border-bottom-grey h-5px">
            </div>

            <div class="row no-gutters">
                <div class="col-6 mt-3 no-gutters ">
                    <span class="text-primary text-uppercase fw-600 ">{{translate('Total')}}</span>
                </div>
                <div class="col-6 mt-3 no-gutters text-right">
                    <span class="text-primary fs-28 text-uppercase fw-600 ">{{format_price($total)}}</span>
                </div>
            </div>

            {{--                    <div class=" mt-3 mb-10px fs-11 sm-fs-14 ">--}}
                {{--                        <label class="sk-checkbox m-0">--}}
                    {{--                            <input type="checkbox" name="agree_policies">--}}
                    {{--                            <span class="sk-square-check "></span>--}}
                    {{--                            {{ translate('I agree with the')}}--}}
                    {{--                            <a class="text-reset hov-text-primary" href="{{ route('termspolicies') }}"--}}
                                                       {{--                               target="_blank">{{ translate('Terms&Policies')}}</a>--}}
                    {{--                        </label>--}}
                {{--                        <div id="register-form-error-agree" class="invalid-feedback fs-10 d-block mt-0 mb-10px"--}}
                                                 {{--                             role="alert"></div>--}}
                {{--                    </div>--}}

            <div class="mt-3 col-sm col-lg-12 col-xxl">
                <div class="text-right text-default-50 fs-12 sm-fs-16 position-relative mb-10px">
                    <label class="sk-checkbox m-0">
                        <input type="checkbox" required id="agree_checkbox">
                        <span class="sk-square-check"></span>
                        {{ translate('I agree with the')}}
                        <a class="text-reset hov-text-primary" href="{{ route('termspolicies') }}" target="_blank">{{ translate('Terms&Policies')}}</a>
                    </label>
                    <div id="checkout-error-agree" class="invalid-feedback absolute fs-10 d-block mb-0" role="alert"></div>
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-primary w-100 text-uppercase " onclick="submitOrder(this)">
                    {{translate('Continue to payment')}}
                </button>
            </div>


        </div>

    </div>


</div>
