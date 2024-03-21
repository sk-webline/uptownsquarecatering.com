<div class="row no-gutters align-items-center p-10px px-md-15px">
    <div class="col lh-1 fw-700 ">
        {{toUpper(translate('Canteen Wallet'))}}
    </div>

    <div class="col-auto text-primary py-5px px-10px hov-opacity-80 c-pointer text-underline">
        <a href="{{route('dashboard.canteen_orders_history', encrypt($canteen_user->id))}}">
            {{toUpper(translate('Order History'))}}
        </a>
    </div>

</div>



<div class="col-12 border-width-2 border-primary border-top px-0"></div>


@if($canteen_user->credit_card_token_id == null)

    <div class="pb-50px">
        <div class="row justify-content-start px-15px pt-20px">

            <div class="col-12 col-sm mb-2 mb-sm-0 mb-lg-2 mb-xl-0 d-flex xs-justify-content-between align-items-center">
                <span>
                    <span class="fw-600 opacity-70">{{translate('Username')}}: </span>
                    <span class="fw-400">{{$canteen_user->username}}</span>
                </span>

                <a class="c-pointer p-5px update_username" data-canteenUser="{{$canteen_user->id}}" data-username="{{$canteen_user->username}}">
                    <svg class="h-15px h-md-20px"
                         xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 21 21">
                        <use
                            xlink:href="{{static_asset('assets/img/icons/pencil-primary.svg')}}#content"></use>
                    </svg>
                </a>

            </div>

            <div class="col-12 col-sm-auto col-lg-12 col-xl-auto d-flex justify-content-between align-items-center">

                <span>
                     <span class="fw-600 opacity-70">{{translate('Daily Limit')}}: </span>
                    <span class="fw-400">{{single_price($canteen_user->daily_limit)}}</span>
                </span>

                <a class="c-pointer p-5px update_daily_limit" data-canteenUser="{{$canteen_user->id}}" data-dailyLimit="{{$canteen_user->daily_limit}}">
                    <svg class="h-15px h-md-20px"
                         xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 21 21">
                        <use
                            xlink:href="{{static_asset('assets/img/icons/pencil-primary.svg')}}#content"></use>
                    </svg>
                </a>

            </div>

        </div>

        <div class="col-12 p-10px pt-20px">
            <div class="p-10px bg-primary text-white border-radius-10px py-5px px-10px hov-opacity-80 c-pointer box-shadow fw-600 lh-1
                                        @if(count($credit_cards)>0) assign-credit-card @else add-credit-card @endif" data-canteenUser="{{$canteen_user->id}}">
                {{translate('Assign Credit Card')}}
            </div>
        </div>
    </div>

    <div class="p-15px position-absolute bottom-0 text-underline">
        <a class="c-pointer opacity-60 hov-opacity-100 canteen-user-pass-change" data-canteenUser="{{$canteen_user->id}}">
            <svg class="h-10px opacity-30" fill="var(--primary)"
                 xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 7.2 8.61">
                <use
                    xlink:href="{{static_asset('assets/img/icons/lock.svg')}}#content"></use>
            </svg>
            {{translate('Change Login Password')}}
        </a>
    </div>

@else

    @php

        $user_credit_card = \App\Models\CreditCard::find($canteen_user->credit_card_token_id);
    @endphp



    <div class="row px-15px pt-20px pb-30px pb-sm-0 pb-lg-20px pb-xl-0">

        <div class="col px-auto pb-15px">

            <div class="row justify-content-start px-15px pt-sm-5px pt-md-20px">

                <div class="col-12 col-lg col-xl-12 mb-2 mb-sm-0 mb-lg-2 mb-xl-0 d-flex xs-justify-content-between align-items-center py-sm-10px">
                    <span>
                         <span class="d-sm-block fw-700 pb-5px opacity-50 fs-12"> {{translate('Daily Limit')}}:</span>
                        <span class="fw-400 fs-12 md-fs-16">{{single_price($canteen_user->daily_limit)}}</span>
                    </span>

                    <a class="c-pointer p-5px update_daily_limit" data-canteenUser="{{$canteen_user->id}}" data-dailyLimit="{{$canteen_user->daily_limit}}">
                        <svg class="h-15px h-md-20px"
                             xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 21 21">
                            <use
                                xlink:href="{{static_asset('assets/img/icons/pencil-primary.svg')}}#content"></use>
                        </svg>
                    </a>

                </div>

                <div class="col-12 col-md-auto col-lg-12 col-xl-auto d-flex xs-justify-content-between align-items-center py-sm-10px">

                    <span>
                        <span class="d-sm-block fw-700 pb-5px opacity-50 fs-12"> {{translate('Username')}}:</span>
                        <span class="fw-400 fs-12 md-fs-16">{{$canteen_user->username}}</span>
                    </span>

                    <a class="c-pointer p-5px update_username" data-canteenUser="{{$canteen_user->id}}" data-username="{{$canteen_user->username}}">
                        <svg class="h-15px h-md-20px"
                             xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 21 21">
                            <use
                                xlink:href="{{static_asset('assets/img/icons/pencil-primary.svg')}}#content"></use>
                        </svg>
                    </a>

                </div>


            </div>



        </div>

        <div class="col-12 col-sm-6 col-lg-7 col-xl-6 pl-sm-0 pb-10px">

            @if($user_credit_card!=null)

                <div class="h-100 bg-primary opacity-30 px-15px border-radius-20px text-white pt-15px fs-10 md-fs-13 letter-spacing-1px">
                    <div class="py-10px">
                        <span class="d-block fw-700 pb-5px"> {{translate('Credit Card Info')}}:</span>
                        <span class="">**** **** **** {{substr($user_credit_card->credit_card_number,-4)}}</span>
                    </div>
                    <div class="py-10px">
                                                <span
                                                    class="d-block fw-700 pb-5px"> {{translate('Card Nickname')}}:</span>
                        <span class="">{{$user_credit_card->nickname}}</span>
                        <a class="c-pointer px-5px update-nickname" data-creditCardID="{{$user_credit_card->id}}" data-nickname="{{$user_credit_card->nickname}}">
                            <svg class="h-15px h-md-20px"
                                 xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 20 20">
                                <use
                                    xlink:href="{{static_asset('assets/img/icons/pencil-icon-2.svg')}}#content"></use>
                            </svg>
                        </a>
                    </div>
                    <div class="py-10px">
                        @if($user_credit_card->expiration_date != null)
                            <span
                                class="d-block fw-700 pb-5px"> {{translate('Expiration Date')}}:</span>
                            <span class="">{{\Carbon\Carbon::create($user_credit_card->expiration_date)->format('m/y')}}</span>
                            <a class="c-pointer edit-credit-card" data-creditCardID="{{$user_credit_card->id}}">
                                <svg class="h-15px h-md-20px px-5px"
                                     xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 20 20">
                                    <use
                                        xlink:href="{{static_asset('assets/img/icons/pencil-icon-2.svg')}}#content"></use>
                                </svg>
                            </a>
                        @endif
                    </div>

                    <div class="text-right fs-10 md-fs-12 pt-10px fw-200 text-underline pb-15px">
                        <a class="c-pointer unassign-credit-card" data-canteenUser="{{$canteen_user->id}}">
                                                    <span>
                                                        <svg class="h-10px mb-3px" fill="#fff"
                                                             xmlns="http://www.w3.org/2000/svg"
                                                             viewBox="0 0 10 10">
                                                                <use
                                                                    xlink:href="{{static_asset('assets/img/icons/trash-icon.svg')}}#content"></use>
                                                            </svg>
                                                        {{translate('Unassign Credit Card')}}
                                                    </span>
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>

    <div class="p-15px position-absolute bottom-0 text-underline">
        <a class="c-pointer opacity-60 hov-opacity-100 canteen-user-pass-change" data-canteenUser="{{$canteen_user->id}}">
            <svg class="h-10px opacity-30" fill="var(--primary)"
                 xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 7.2 8.61">
                <use
                    xlink:href="{{static_asset('assets/img/icons/lock.svg')}}#content"></use>
            </svg>
            {{translate('Change Login Password')}}
        </a>
    </div>




@endif
