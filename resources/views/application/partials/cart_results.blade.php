@php

    use Carbon\Carbon;
    use App\Models\CanteenProduct;

    $cart = [];

    if(Session::has('app_cart')){
        $cart = Session::get('app_cart')->toArray();
    }

    // Custom comparison function for sorting by date and then by break
    function sortByDateAndBreak($a, $b) {
        $dateComparison = strtotime($a['date']) - strtotime($b['date']);
        // If dates are equal, compare by 'break'
        if ($dateComparison == 0) {
        return $a['break_id'] - $b['break_id'];
        }

        return $dateComparison;
    }

    // Use usort to sort the array using the custom function
    usort($cart, 'sortByDateAndBreak');

    $cart_days = [];
    $break_keys = [];
    $dates = [];
    $breaks = [];
    $titles = [];
    $cart_title = [];

    foreach($cart as $key => $cart_item){

        $title = ucfirst($cart_item['day']) . ' ' . Carbon::create($cart_item['date'])->format('d/m') . ' - ' . ordinal($cart_item['break_sort']) . ' ' . translate('Break');
        $titles[] = $title;
        $cart_title[$key] = $title;

        $dates[$title] = $cart_item['date'];
        $breaks[$title] = $cart_item['break_id'];

        $break_keys[$cart_item['break_id']] = $cart_item['break_sort'];

    }

    $titles = array_unique($titles);

@endphp

@foreach($titles as $title)

    <div class="cart-break-item">
        <div class="container">
            <div class="mb-2px text-green fw-700">{{$title}}</div>
        </div>

        <div class="cart-break-products">
            @foreach($cart as $key => $cart_item)
                @if($cart_title[$key] == $title)
                    @php
                        $product = CanteenProduct::find($cart_item['product_id']);
                    @endphp
                    <div class="cart-break-product-item ">
                        <div class="row gutters-10 align-items-center @if(isset($cart_item['disabled']) && $cart_item['disabled'] == true) opacity-50 @endif">
                            <div class="col-auto">
                                <div class="cart-break-add" data-productID="{{$product->id}}"
                                     data-date="{{$cart_item['date']}}"
                                     data-breakID="{{$cart_item['break_id']}}" data-breakSort="{{$cart_item['break_sort']}}">
                                    <div class="quantity">
                                        <div class="row gutters-2">
                                            <div class="col-auto">
                                                <div class="control quantity-minus">-</div>
                                            </div>
                                            <div class="col">
                                                <div class="quantity-total">{{$cart_item['quantity']}}</div>
                                            </div>
                                            <div class="col-auto">
                                                <div class="control quantity-plus">+</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="added">
                                        <span class="added_amount pr-2px">{{$cart_item['quantity']}}</span>
                                        <span class="arrow"></span></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="title">{{$product->getTranslation('name')}}</div>
                            </div>
                            <div class="col-auto">
                                <div class="price">{{single_price($cart_item['price'])}}</div>
                            </div>
                            <div class="col-auto">
                                <div class="delete">
                                    <svg class="h-13px" xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 7.73 10.92">
                                        <use
                                            xlink:href="{{static_asset('assets/img/icons/cart-delete.svg')}}#content"></use>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        @if(isset($cart_item['disabled']) && $cart_item['disabled'] == true)
                            <div class="d-flex fw-500 w-100 align-items-center justify-content-center pt-5px">
                                <div class="shadow bg-white border rounded-10px p-10px text-center fs-12 md-fs-14 lg-fs-16">
                                    <span class="d-block">{{$cart_item['message']}}</span>
                                    <span class="d-block">Please <strong class="text-underline delete"> remove </strong> to continue</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach

        </div>
        <div class="container my-5px">
            <a href="{{ route('application.choose_snack', ['date' => encrypt($dates[$title]), 'break_id' => encrypt($breaks[$title]) ]) }}" class="cart-add-more">
                <span class="add">+</span>
                <span class="text">{{translate('Add More')}}</span>
            </a>
        </div>
    </div>
@endforeach
