@php

    use Carbon\Carbon;
    use App\Models\CanteenProduct;
    use App\Models\CanteenPurchase;

    $cart = [];

     $user = auth()->guard('application')->user(); // canteen user
     $rfid_card = $user->card;
     $organisation = $rfid_card->organisation;
     $canteen_setting = $organisation->current_canteen_settings();

     $minimum_preorder_minutes = $canteen_setting->minimum_preorder_minutes;
     $minimum_cancellation_minutes = $canteen_setting->minimum_cancellation_minutes;

     $today = Carbon::today();
     $now = Carbon::now();
     $time_now = $now->format('H:i:s');
     $upcoming_purchases = CanteenPurchase::where('canteen_app_user_id', $user->id )
                                     ->where('date', '>', $today->format('Y-m-d'))
                                     ->orWhere(function ($query) use ($today, $time_now) {
                                         $query->where('date', '=', $today->format('Y-m-d'))
                                               ->where('break_hour_from', '>', $time_now);
                                     })
                                     ->orderBy('date')->get()->toArray();


    // Custom comparison function for sorting by date and then by break
    function sortByDateAndBreak($a, $b) {
        $dateComparison = strtotime($a['date']) - strtotime($b['date']);

        // If dates are equal, compare by 'break'
        if ($dateComparison == 0) {
        return $a['break_num'] - $b['break_num'];
        }

        return $dateComparison;
    }


    // Use usort to sort the array using the custom function
    usort($upcoming_purchases, 'sortByDateAndBreak');

     $cart_days = [];
     $break_keys = [];
     $dates = [];
     $breaks = [];
     $titles = [];
     $meal_title = [];
     $break_passed = [];

     foreach($upcoming_purchases as $key => $meal){

             $day = Carbon::create($meal['date'])->format('l');
             $title = ucfirst($day) . ' ' . Carbon::create($meal['date'])->format('d/m') . ' - ' . ordinal($meal['break_num']) . ' ' . translate('Break');
             $titles[] = $title;
             $meal_title[$key] = $title;

             $dates[$title] = $meal['date'];
             $breaks[$title] = $meal['break_num'];

     }

     $titles = array_unique($titles);

@endphp

@foreach($titles as $title)

    @php

        $break = \App\Models\OrganisationBreak::where('organisation_id', $organisation->id)
                    ->where('canteen_setting_id', $canteen_setting->id)->where('break_num', $breaks[$title])->first();

        $preorder_availability = preorder_availability($dates[$title], $break, $minimum_preorder_minutes);

        $cancellation_availability = preorder_availability($dates[$title], $break, $minimum_cancellation_minutes);

    @endphp

    <div class="cart-break-item">
        <div class="container py-2px fw-700  @if(!$preorder_availability) bg-green text-white @else text-green @endif">
            {{$title}}
        </div>

        <div class="cart-break-products">

            @php

                $products = [];
                $quantities = [];
                $prices = [];
                $meal_code = null;

                foreach ($upcoming_purchases as $key => $purchase){

//                                   dd($purchase, $purchase['canteen_product_id']);
                     if(isset($meal_title[$key]) && $meal_title[$key] == $title){
                          if(!isset($products[$purchase['canteen_product_id']])){
                                $pr = \App\Models\CanteenProduct::find($purchase['canteen_product_id']);
                                if($pr!=null){
                                    $products[$purchase['canteen_product_id']] = $pr->getTranslation('name');
                                }else{
                                     $products[$purchase['canteen_product_id']] = 'Not available';
                                }

                                $prices[$purchase['canteen_product_id']] = $purchase['price'];

                            }

                            if(!isset($quantities[$purchase['canteen_product_id']])){
                                $quantities[$purchase['canteen_product_id']] = $purchase['quantity'];
                            }else{
                                $quantities[$purchase['canteen_product_id']] += $purchase['quantity'];
                            }

                            if($meal_code == null){
                                $meal_code = $purchase['meal_code'];
                            }
                     }

                }

            @endphp

            @foreach($products as $product_id => $product)

                <div class="cart-break-product-item" data-productID="{{$product_id}}" data-date="{{$dates[$title]}}" data-breakNum="{{$breaks[$title]}}" data-quantity="{{$quantities[$product_id]}}">
                    <div class="row gutters-10 align-items-center">
                        <div class="col">
                            <div class="title">({{$quantities[$product_id]}}) {{$product}}</div>
                        </div>
                        <div class="col-auto">
                            <div class="price">{{single_price($prices[$product_id] * $quantities[$product_id])}}</div>
                        </div>


                        @if($cancellation_availability)
                        <div class="col-auto">
                            <div class="delete">
                                <svg class="h-13px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 7.73 10.92">
                                    <use xlink:href="{{static_asset('assets/img/icons/cart-delete.svg')}}#content"></use>
                                </svg>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            @endforeach

            <div class="bg-login-box-fill fw-700 text-black-50 container fs-12">
                {{translate('Meal Code')}}: {{$meal_code}}
            </div>

            @if($preorder_availability)
                <div class="p-10px py-5px">
                <a  href="{{ route('application.choose_snack', ['date' => encrypt($meal['date']), 'break_id' => encrypt($break->id) ]) }}" class="cart-add-more">
                    <span class="add">+</span>
                    <span class="text">{{translate('Add More')}}</span>
                </a>
                </div>
            @endif

        </div>


    </div>



@endforeach

{{--<div class="snack-total-cart pb-70px">--}}
{{--    <div class="container">--}}
{{--        <button class="btn btn-block btn-secondary fs-16 fw-700" type="submit" style="display: block;">--}}
{{--                            <span class="row gutters-2 align-items-center">--}}
{{--                                <span class="d-block col-auto">--}}
{{--                                    <span class="d-block cart-totals">--}}
{{--                                        @if(Session::has('total_items') && Session::get('total_items')>0)--}}
{{--                                            <span class="d-block cart-totals">{{Session::get('total_items')}}</span>--}}
{{--                                        @else--}}
{{--                                            0--}}
{{--                                        @endif--}}
{{--                                    </span>--}}
{{--                                </span>--}}
{{--                                <span class="d-block col">{{translate('Continue')}}</span>--}}
{{--                                <span class="d-block col-auto">--}}
{{--                                    <span class="d-block fs-15 cart-total-price">--}}
{{--                                        @if(Session::has('app_total'))--}}
{{--                                            {{single_price(Session::get('app_total'))}}--}}
{{--                                        @else--}}
{{--                                            {{single_price(0)}}--}}
{{--                                        @endif--}}
{{--                                    </span>--}}
{{--                                </span>--}}
{{--                            </span>--}}
{{--        </button>--}}
{{--    </div>--}}
{{--</div>--}}
