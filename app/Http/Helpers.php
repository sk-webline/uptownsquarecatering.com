<?php

use App\Color;
use App\Models\PlatformSetting;
use App\User;
use App\Models\Btms\Items;
use App\Models\Btms\ItemSize;
use App\Models\Cart;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use Illuminate\Pagination\LengthAwarePaginator;use Illuminate\Pagination\Paginator;use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Currency;
use App\BusinessSetting;
use App\Product;
use App\SubSubCategory;
use App\FlashDealProduct;
use App\FlashDeal;
use App\OtpConfiguration;
use App\Upload;
use App\Translation;
use App\Country;
use App\City;
use App\Utility\TranslationUtility;
use App\Utility\CategoryUtility;
use App\Utility\MimoUtility;
use PhpParser\Node\Expr\Cast\Double;
use Psr\Http\Message\ResponseInterface;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Redis;

//highlights the selected navigation on admin panel
if (!function_exists('sendSMS')) {
    function sendSMS($to, $from, $text)
    {
        if (OtpConfiguration::where('type', 'nexmo')->first()->value == 1) {
            $api_key = env("NEXMO_KEY"); //put ssl provided api_token here
            $api_secret = env("NEXMO_SECRET"); // put ssl provided sid here

            $params = [
                "api_key" => $api_key,
                "api_secret" => $api_secret,
                "from" => $from,
                "text" => $text,
                "to" => $to
            ];

            $url = "https://rest.nexmo.com/sms/json";
            $params = json_encode($params);

            $ch = curl_init(); // Initialize cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params),
                'accept:application/json'
            ));
            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        } elseif (OtpConfiguration::where('type', 'twillo')->first()->value == 1) {
            $sid = env("TWILIO_SID"); // Your Account SID from www.twilio.com/console
            $token = env("TWILIO_AUTH_TOKEN"); // Your Auth Token from www.twilio.com/console

            $client = new Client($sid, $token);
            try {
                $message = $client->messages->create(
                    $to, // Text this number
                    array(
                        'from' => env('VALID_TWILLO_NUMBER'), // From a valid Twilio number
                        'body' => $text
                    )
                );
            } catch (\Exception $e) {

            }

        } elseif (OtpConfiguration::where('type', 'ssl_wireless')->first()->value == 1) {
            $token = env("SSL_SMS_API_TOKEN"); //put ssl provided api_token here
            $sid = env("SSL_SMS_SID"); // put ssl provided sid here

            $params = [
                "api_token" => $token,
                "sid" => $sid,
                "msisdn" => $to,
                "sms" => $text,
                "csms_id" => date('dmYhhmi') . rand(10000, 99999)
            ];

            $url = env("SSL_SMS_URL");
            $params = json_encode($params);

            $ch = curl_init(); // Initialize cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params),
                'accept:application/json'
            ));

            $response = curl_exec($ch);

            curl_close($ch);

            return $response;
        } elseif (OtpConfiguration::where('type', 'fast2sms')->first()->value == 1) {

            if (strpos($to, '+91') !== false) {
                $to = substr($to, 3);
            }

            $fields = array(
                "sender_id" => env("SENDER_ID"),
                "message" => $text,
                "language" => env("LANGUAGE"),
                "route" => env("ROUTE"),
                "numbers" => $to,
            );

            $auth_key = env('AUTH_KEY');

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($fields),
                CURLOPT_HTTPHEADER => array(
                    "authorization: $auth_key",
                    "accept: */*",
                    "cache-control: no-cache",
                    "content-type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            return $response;
        } elseif (OtpConfiguration::where('type', 'mimo')->first()->value == 1) {
            $token = MimoUtility::getToken();

            MimoUtility::sendMessage($text, $to, $token);
            MimoUtility::logout($token);
        }
    }
}

//highlights the selected navigation on admin panel
if (!function_exists('areActiveRoutes')) {
    function areActiveRoutes(array $routes, $output = "active")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }

    }
}

//highlights the selected navigation on frontend
if (!function_exists('areActiveRoutesHome')) {
    function areActiveRoutesHome(array $routes, $output = "active")
    {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) return $output;
        }

    }
}

if (!function_exists('array_paginate')) {

    function array_paginate($items, $perPage = 5, $page = null)
    {

        $page = $page?: (Paginator::resolveCurrentPage() ?: 1);
        $total = count($items);
        $currentpage = $page;
        $offset = ($currentpage * $perPage) - $perPage;
        $itemstoshow = array_slice($items, $offset, $perPage);
        return new LengthAwarePaginator($itemstoshow, $total, $perPage);

    }
}


//highlights the selected navigation on frontend
if (!function_exists('default_language')) {
    function default_language()
    {
        return env("DEFAULT_LANGUAGE");
    }
}

/**
 * Save JSON File
 * @return Response
 */
if (!function_exists('convert_to_usd')) {
    function convert_to_usd($amount)
    {
        $business_settings = BusinessSetting::where('type', 'system_default_currency')->first();
        if ($business_settings != null) {
            $currency = Currency::find($business_settings->value);
            return (floatval($amount) / floatval($currency->exchange_rate)) * Currency::where('code', 'USD')->first()->exchange_rate;
        }
    }
}

if (!function_exists('convert_to_kes')) {
    function convert_to_kes($amount)
    {
        $business_settings = BusinessSetting::where('type', 'system_default_currency')->first();
        if ($business_settings != null) {
            $currency = Currency::find($business_settings->value);
            return (floatval($amount) / floatval($currency->exchange_rate)) * Currency::where('code', 'KES')->first()->exchange_rate;
        }
    }
}

//filter products based on vendor activation system
if (!function_exists('filter_products')) {
    function filter_products($products)
    {
        $verified_sellers = verified_sellers_id();
        if (get_setting('vendor_system_activation') == 1) {
            return $products->where('published', '1')->orderBy('created_at', 'desc')->where(function ($p) use ($verified_sellers) {
                $p->where('added_by', 'admin')->orWhere(function ($q) use ($verified_sellers) {
                    $q->whereIn('user_id', $verified_sellers);
                });
            });
        } else {
            return $products->where('published', '1')->where('added_by', 'admin');
        }
    }
}

//cache products based on category
if (!function_exists('get_cached_products')) {
    function get_cached_products($category_id = null)
    {
        $products = \App\Product::where('published', 1);
        $verified_sellers = verified_sellers_id();
        if (BusinessSetting::where('type', 'vendor_system_activation')->first()->value == 1) {
            $products = $products->where(function ($p) use ($verified_sellers) {
                $p->where('added_by', 'admin')->orWhere(function ($q) use ($verified_sellers) {
                    $q->whereIn('user_id', $verified_sellers);
                });
            });
        } else {
            $products = $products->where('added_by', 'admin');
        }

        if ($category_id != null) {
            return Cache::remember('products-category-' . $category_id, 86400, function () use ($category_id, $products) {
                $category_ids = CategoryUtility::children_ids($category_id);
                $category_ids[] = $category_id;
                return $products->whereIn('category_id', $category_ids)->latest()->take(12)->get();
            });
        } else {
            return Cache::remember('products', 86400, function () use ($products) {
                return $products->latest()->get();
            });
        }
    }
}

if (!function_exists('verified_sellers_id')) {
    function verified_sellers_id()
    {
        return App\Seller::where('verification_status', 1)->get()->pluck('user_id')->toArray();
    }
}

//converts currency to home default currency
if (!function_exists('convert_price')) {
    function convert_price($price)
    {
        return $price;
        /*$business_settings = BusinessSetting::where('type', 'system_default_currency')->first();
        if($business_settings != null){
            $currency = Currency::find($business_settings->value);
            $price = floatval($price) / floatval($currency->exchange_rate);
        }

        $code = \App\Currency::findOrFail(\App\BusinessSetting::where('type', 'system_default_currency')->first()->value)->code;
        if(Session::has('currency_code')){
            $currency = Currency::where('code', Session::get('currency_code', $code))->first();
        }
        else{
            $currency = Currency::where('code', $code)->first();
        }

        $price = floatval($price) * floatval($currency->exchange_rate);

        return $price;*/
    }
}

//formats currency
if (!function_exists('format_price')) {
    function format_price($price, $with_symbol = true)
    {
        $fomated_price = number_format($price, 2);
        if ($with_symbol) {
            return currency_symbol() . $fomated_price;
        } else {
            return $fomated_price;
        }


        /// OLD CODE
        /*if (BusinessSetting::where('type', 'decimal_separator')->first()->value == 1) {
            $fomated_price = number_format($price, BusinessSetting::where('type', 'no_of_decimals')->first()->value);
        }
        else {
            $fomated_price = number_format($price, BusinessSetting::where('type', 'no_of_decimals')->first()->value , ',' , ' ');
        }

        if ($with_symbol) {
            if (BusinessSetting::where('type', 'symbol_format')->first()->value == 1) {
                return <div class="row text-primary fs-15 pt-4 pl-4 "> . $fomated_price;
            }
            return $fomated_price . currency_symbol();
        }
        else {
            return $fomated_price;
        }*/
    }
}

if (!function_exists('format_rfid_no')) {
    function format_rfid_no($rfid_no)
    {

        $response = '';
        $times = number_format(strlen($rfid_no) / 4);

        for ($i = 0; $i < $times; $i++) {
            $s = $i * 4;
            $response = $response . substr($rfid_no, $s, 4) . ' ';

        }
        return $response;
    }

}

//formats price to home default price with convertion
if (!function_exists('single_price')) {
    function single_price($price)
    {
        return format_price(convert_price($price));
    }
}

//Shows Price on page based on low to high
if (!function_exists('home_price')) {
    function home_price($id)
    {
        $product = Cache::remember('product_' . $id, config('cache.expiry.product'), function () use ($id) {
            return Product::findOrFail($id);
        });
        return $product->getPriceForCurrentUser(false, true);
//        if(isPartner()) {
//            $lowest_price = $product->wholesale_price;
//            $highest_price = $product->wholesale_price;
//        } else {
//            $lowest_price = $product->unit_price;
//            $highest_price = $product->unit_price;
//        }
//
//        if ($product->variant_product) {
//            foreach ($product->stocks as $key => $stock) {
//                if(isPartner()) {
//                    if($lowest_price > $stock->whole_price){
//                        $lowest_price = $stock->whole_price;
//                    }
//                    if($highest_price < $stock->whole_price){
//                        $highest_price = $stock->whole_price;
//                    }
//                } else {
//                    if ($lowest_price > $stock->price) {
//                        $lowest_price = $stock->price;
//                    }
//                    if ($highest_price < $stock->price) {
//                        $highest_price = $stock->price;
//                    }
//                }
//            }
//        }
//
//        /*if($product->tax_type == 'percent'){
//            $lowest_price += ($lowest_price*$product->tax)/100;
//            $highest_price += ($highest_price*$product->tax)/100;
//        }
//        elseif($product->tax_type == 'amount'){
//            $lowest_price += $product->tax;
//            $highest_price += $product->tax;
//        }*/
//
//        $lowest_price = calcVatPrice($lowest_price);
//        $highest_price = calcVatPrice($highest_price);
//
//        $lowest_price = convert_price($lowest_price);
//        $highest_price = convert_price($highest_price);
//
//        if($lowest_price == $highest_price){
//            return format_price($lowest_price);
//        }
//        else{
//            return format_price($lowest_price).' - '.format_price($highest_price);
//        }
    }
}

//Shows Price on page based on low to high with discount
if (!function_exists('home_discounted_price')) {
    function home_discounted_price($id)
    {
        $product = Cache::remember('product_' . $id, config('cache.expiry.product'), function () use ($id) {
            return Product::findOrFail($id);
        });
        return $product->getPriceForCurrentUser();
//        if(isPartner()) {
//            $lowest_price = $product->wholesale_price;
//            $highest_price = $product->wholesale_price;
//        } else {
//            $lowest_price = $product->unit_price;
//            $highest_price = $product->unit_price;
//        }
//
//
//        if ($product->variant_product) {
//            foreach ($product->stocks as $key => $stock) {
//                if(isPartner()) {
//                    if($lowest_price > $stock->whole_price){
//                        $lowest_price = $stock->whole_price;
//                    }
//                    if($highest_price < $stock->whole_price){
//                        $highest_price = $stock->whole_price;
//                    }
//                } else {
//                    if($lowest_price > $stock->price){
//                        $lowest_price = $stock->price;
//                    }
//                    if($highest_price < $stock->price){
//                        $highest_price = $stock->price;
//                    }
//                }
//            }
//        }
////        dd($product->id, $lowest_price, $highest_price);
//        /*$flash_deals = \App\FlashDeal::where('status', 1)->get();
//        foreach ($flash_deals as $flash_deal) {
//            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first() != null) {
//                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first();
//                if($flash_deal_product->discount_type == 'percent'){
//                    $lowest_price -= ($lowest_price*$flash_deal_product->discount)/100;
//                    $highest_price -= ($highest_price*$flash_deal_product->discount)/100;
//                }
//                elseif($flash_deal_product->discount_type == 'amount'){
//                    $lowest_price -= $flash_deal_product->discount;
//                    $highest_price -= $flash_deal_product->discount;
//                }
//                $inFlashDeal = true;
//                break;
//            }
//        }*/
//
//        if ($product->discount != null && $product->discount != 0) {
//            if($product->discount_type == 'percent'){
//                $lowest_price -= ($lowest_price*$product->discount)/100;
//                $highest_price -= ($highest_price*$product->discount)/100;
//            }
//            elseif($product->discount_type == 'amount') {
//                $lowest_price -= $product->discount;
//                $highest_price -= $product->discount;
//            }
//        }
//
//        $lowest_price = calcVatPrice($lowest_price);
//        $highest_price = calcVatPrice($highest_price);
//
//
//        $lowest_price = convert_price($lowest_price);
//        $highest_price = convert_price($highest_price);
////        debug_dd($product->id, $lowest_price , $highest_price);
//        if($lowest_price == $highest_price){
//            return format_price($lowest_price);
//        }
//        else{
//            return format_price($lowest_price).' - '.format_price($highest_price);
//        }
    }
}

//Shows Base Price
if (!function_exists('home_base_price')) {
    function home_base_price($id)
    {
        return home_price($id);
        /*$product = Product::findOrFail($id);
        if(isPartner()) {
            $price = $product->wholesale_price;
        } else {
            $price = $product->unit_price;
        }
        if($product->tax_type == 'percent'){
            $price += ($price*$product->tax)/100;
        }
        elseif($product->tax_type == 'amount'){
            $price += $product->tax;
        }
        return format_price(convert_price($price));*/
    }
}

//Shows Retail Price with discount
if (!function_exists('customer_base_price')) {
    function customer_base_price($id)
    {
        $product = Cache::remember('product_' . $id, config('cache.expiry.product'), function () use ($id) {
            return Product::findOrFail($id);
        });
        return $product->getPriceForCurrentUser(false);
        /*$price = $product->unit_price;
        if($product->tax_type == 'percent'){
            $price += ($price*$product->tax)/100;
        }
        elseif($product->tax_type == 'amount'){
            $price += $product->tax;
        }

        return format_price(convert_price($price));*/
    }
}

//Shows Retail Price with discount
if (!function_exists('srp_price')) {
    function srp_price($id)
    {
        $product = Cache::remember('product_' . $id, config('cache.expiry.product'), function () use ($id) {
            return Product::findOrFail($id);
        });
//        Product::findOrFail($id);

        $price = calcVatPrice($product->unit_price);

        return format_price(convert_price($price));
    }
}

//Shows Base Price with discount
if (!function_exists('home_discounted_base_price')) {
    function home_discounted_base_price($id)
    {
        return home_discounted_price($id);
        /*$product = Product::findOrFail($id);

        if(isPartner()) {
            $price = $product->wholesale_price;
        } else {
            $price = $product->unit_price;
        }

        $flash_deals = \App\FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first() != null) {
                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first();
                if($flash_deal_product->discount_type == 'percent'){
                    $price -= ($price*$flash_deal_product->discount)/100;
                }
                elseif($flash_deal_product->discount_type == 'amount'){
                    $price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }

        if (!$inFlashDeal) {
            if($product->discount_type == 'percent'){
                $price -= ($price*$product->discount)/100;
            }
            elseif($product->discount_type == 'amount'){
                $price -= $product->discount;
            }
        }

        if($product->tax_type == 'percent'){
            $price += ($price*$product->tax)/100;
        }
        elseif($product->tax_type == 'amount'){
            $price += $product->tax;
        }

        return format_price(convert_price($price));*/
    }
}

if (!function_exists('currency_symbol')) {
    function currency_symbol()
    {
        return '€';
        /*$code = \App\Currency::findOrFail(\App\BusinessSetting::where('type', 'system_default_currency')->first()->value)->code;
        if(Session::has('currency_code')){
            $currency = Currency::where('code', Session::get('currency_code', $code))->first();
        }
        else{
            $currency = Currency::where('code', $code)->first();
        }
        return $currency->symbol;*/
    }
}

if (!function_exists('renderStarRating')) {
    function renderStarRating($rating, $maxRating = 5)
    {
        $fullStar = "<i class = 'las la-star active'></i>";
        $halfStar = "<i class = 'las la-star half'></i>";
        $emptyStar = "<i class = 'las la-star'></i>";
        $rating = $rating <= $maxRating ? $rating : $maxRating;

        $fullStarCount = (int)$rating;
        $halfStarCount = ceil($rating) - $fullStarCount;
        $emptyStarCount = $maxRating - $fullStarCount - $halfStarCount;

        $html = str_repeat($fullStar, $fullStarCount);
        $html .= str_repeat($halfStar, $halfStarCount);
        $html .= str_repeat($emptyStar, $emptyStarCount);
        echo $html;
    }
}


//Api
if (!function_exists('homeBasePrice')) {
    function homeBasePrice($id)
    {
        $product = Cache::remember('product_' . $id, config('cache.expiry.product'), function () use ($id) {
            return Product::findOrFail($id);
        });
        return $product->getPriceForCurrentUser(false, false);
        /*$price = $product->unit_price;
        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }
        return $price;*/
    }
}

if (!function_exists('homeDiscountedBasePrice')) {
    function homeDiscountedBasePrice($id)
    {
        $product = Cache::remember('product_' . $id, config('cache.expiry.product'), function () use ($id) {
            return Product::findOrFail($id);
        });
        $price = $product->unit_price;

        $flash_deals = FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first() != null) {
                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $price -= ($price * $flash_deal_product->discount) / 100;
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    $price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }

        if (!$inFlashDeal) {
            if ($product->discount_type == 'percent') {
                $price -= ($price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $price -= $product->discount;
            }
        }

        if ($product->tax_type == 'percent') {
            $price += ($price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $price += $product->tax;
        }
        return $price;
    }
}

if (!function_exists('homePrice')) {
    function homePrice($id)
    {
        $product = Cache::remember('product_' . $id, config('cache.expiry.product'), function () use ($id) {
            return Product::findOrFail($id);
        });
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;

        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }

        if ($product->tax_type == 'percent') {
            $lowest_price += ($lowest_price * $product->tax) / 100;
            $highest_price += ($highest_price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $lowest_price += $product->tax;
            $highest_price += $product->tax;
        }

        $lowest_price = convertPrice($lowest_price);
        $highest_price = convertPrice($highest_price);

        return $lowest_price . ' - ' . $highest_price;
    }
}

if (!function_exists('homeDiscountedPrice')) {
    function homeDiscountedPrice($id)
    {
        $product = Cache::remember('product_' . $id, config('cache.expiry.product'), function () use ($id) {
            return Product::findOrFail($id);
        });
        $lowest_price = $product->unit_price;
        $highest_price = $product->unit_price;

        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                if ($lowest_price > $stock->price) {
                    $lowest_price = $stock->price;
                }
                if ($highest_price < $stock->price) {
                    $highest_price = $stock->price;
                }
            }
        }

        $flash_deals = FlashDeal::where('status', 1)->get();
        $inFlashDeal = false;
        foreach ($flash_deals as $flash_deal) {
            if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first() != null) {
                $flash_deal_product = FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $id)->first();
                if ($flash_deal_product->discount_type == 'percent') {
                    $lowest_price -= ($lowest_price * $flash_deal_product->discount) / 100;
                    $highest_price -= ($highest_price * $flash_deal_product->discount) / 100;
                } elseif ($flash_deal_product->discount_type == 'amount') {
                    $lowest_price -= $flash_deal_product->discount;
                    $highest_price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
            }
        }

        if (!$inFlashDeal) {
            if ($product->discount_type == 'percent') {
                $lowest_price -= ($lowest_price * $product->discount) / 100;
                $highest_price -= ($highest_price * $product->discount) / 100;
            } elseif ($product->discount_type == 'amount') {
                $lowest_price -= $product->discount;
                $highest_price -= $product->discount;
            }
        }

        if ($product->tax_type == 'percent') {
            $lowest_price += ($lowest_price * $product->tax) / 100;
            $highest_price += ($highest_price * $product->tax) / 100;
        } elseif ($product->tax_type == 'amount') {
            $lowest_price += $product->tax;
            $highest_price += $product->tax;
        }

        $lowest_price = convertPrice($lowest_price);
        $highest_price = convertPrice($highest_price);

        return $lowest_price . ' - ' . $highest_price;
    }
}

if (!function_exists('brandsOfCategory')) {
    function brandsOfCategory($category_id)
    {
        $brands = [];
        $subCategories = SubCategory::where('category_id', $category_id)->get();
        foreach ($subCategories as $subCategory) {
            $subSubCategories = SubSubCategory::where('sub_category_id', $subCategory->id)->get();
            foreach ($subSubCategories as $subSubCategory) {
                $brand = json_decode($subSubCategory->brands);
                foreach ($brand as $b) {
                    if (in_array($b, $brands)) continue;
                    array_push($brands, $b);
                }
            }
        }
        return $brands;
    }
}

if (! function_exists('random_number_generator')) {
    function random_number_generator()
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomNum = '';

        $length= 8;

        for ($i = 0; $i < $length; $i++) {
            $randomNum .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomNum;
    }
}

if (!function_exists('convertPrice')) {
    function convertPrice($price)
    {
        $business_settings = BusinessSetting::where('type', 'system_default_currency')->first();
        if ($business_settings != null) {
            $currency = Currency::find($business_settings->value);
            $price = floatval($price) / floatval($currency->exchange_rate);
        }
        $code = Currency::findOrFail(BusinessSetting::where('type', 'system_default_currency')->first()->value)->code;
        if (Session::has('currency_code')) {
            $currency = Currency::where('code', Session::get('currency_code', $code))->first();
        } else {
            $currency = Currency::where('code', $code)->first();
        }
        $price = floatval($price) * floatval($currency->exchange_rate);
        return $price;
    }
}


function translate($key, $lang = null)
{
    if ($lang == null) {
        $lang = App::getLocale();
    }

    $cache_key = 'translations_' . $lang . '_key_' . $key;

    if (Redis::exists($cache_key)) {
        return Redis::get($cache_key);
    } else {
        $translation_def = Translation::where('lang', env('DEFAULT_LANGUAGE', 'en'))->where('lang_key', $key)->first();
        if ($translation_def == null) {
            $translation_def = new Translation;
            $translation_def->lang = env('DEFAULT_LANGUAGE', 'en');
            $translation_def->lang_key = $key;
            $translation_def->lang_value = $key;
            $translation_def->save();
        }

        //Check for session lang
        $translation_locale = Translation::where('lang_key', $key)->where('lang', $lang)->first();
        if ($translation_locale != null && $translation_locale->lang_value != null) {
            Redis::setex($cache_key, config('cache.expiry.translations'), $translation_locale->lang_value);
            return $translation_locale->lang_value;
        } elseif ($translation_def->lang_value != null) {
            Redis::setex($cache_key, config('cache.expiry.translations'), $translation_def->lang_value);
            return $translation_def->lang_value;
        } else {
            Redis::setex($cache_key, config('cache.expiry.translations'), $key);
            return $key;
        }
    }
}

/*function translate($key, $lang = null){
    if($lang == null){
        $lang = App::getLocale();
    }

    $translation_def = Translation::where('lang', env('DEFAULT_LANGUAGE', 'en'))->where('lang_key', $key)->first();
    if($translation_def == null){
        $translation_def = new Translation;
        $translation_def->lang = env('DEFAULT_LANGUAGE', 'en');
        $translation_def->lang_key = $key;
        $translation_def->lang_value = $key;
        $translation_def->save();
    }

    //Check for session lang
    $translation_locale = Translation::where('lang_key', $key)->where('lang', $lang)->first();
    if($translation_locale != null && $translation_locale->lang_value != null){
        return $translation_locale->lang_value;
    }
    elseif($translation_def->lang_value != null){
        return $translation_def->lang_value;
    }
    else{
        return $key;
    }
}*/

function remove_invalid_charcaters($str)
{
    $str = str_ireplace(array("\\"), '', $str);
    return str_ireplace(array('"'), '\"', $str);
}

function getShippingCost($index)
{
    $admin_products = array();
    $seller_products = array();
    $calculate_shipping = 0;

    foreach (Session::get('cart') as $key => $cartItem) {
        $product = \App\Product::find($cartItem['id']);
        if ($product->added_by == 'admin') {
            array_push($admin_products, $cartItem['id']);
        } else {
            $product_ids = array();
            if (array_key_exists($product->user_id, $seller_products)) {
                $product_ids = $seller_products[$product->user_id];
            }
            array_push($product_ids, $cartItem['id']);
            $seller_products[$product->user_id] = $product_ids;
        }
    }

    //Calculate Shipping Cost
    if (get_setting('shipping_type') == 'flat_rate') {
        $calculate_shipping = \App\BusinessSetting::where('type', 'flat_rate_shipping_cost')->first()->value;
    } elseif (get_setting('shipping_type') == 'seller_wise_shipping') {
        if (!empty($admin_products)) {
            $calculate_shipping = \App\BusinessSetting::where('type', 'shipping_cost_admin')->first()->value;
        }
        if (!empty($seller_products)) {
            foreach ($seller_products as $key => $seller_product) {
                $calculate_shipping += \App\Shop::where('user_id', $key)->first()->shipping_cost;
            }
        }
    } elseif (get_setting('shipping_type') == 'area_wise_shipping') {
        $city = City::where('name', Session::get('shipping_info')['city'])->first();
        if ($city != null) {
            $calculate_shipping = $city->cost;
        }
    }

    $cartItem = Session::get('cart')[$index];
    $product = \App\Product::find($cartItem['id']);

    if ($product->digital == 1) {
        return $calculate_shipping = 0;
    }

    if (get_setting('shipping_type') == 'flat_rate') {
        return $calculate_shipping / count(Session::get('cart'));
    } elseif (get_setting('shipping_type') == 'seller_wise_shipping') {
        if ($product->added_by == 'admin') {
            return \App\BusinessSetting::where('type', 'shipping_cost_admin')->first()->value / count($admin_products);
        } else {
            return \App\Shop::where('user_id', $product->user_id)->first()->shipping_cost / count($seller_products[$product->user_id]);
        }
    } elseif (get_setting('shipping_type') == 'area_wise_shipping') {
        if ($product->added_by == 'admin') {
            return $calculate_shipping / count($admin_products);
        } else {
            return $calculate_shipping / count($seller_products[$product->user_id]);
        }
    } else {
        return \App\Product::find($cartItem['id'])->shipping_cost;
    }
}

function timezones()
{
    return array(
        '(GMT-12:00) International Date Line West' => 'Pacific/Kwajalein',
        '(GMT-11:00) Midway Island' => 'Pacific/Midway',
        '(GMT-11:00) Samoa' => 'Pacific/Apia',
        '(GMT-10:00) Hawaii' => 'Pacific/Honolulu',
        '(GMT-09:00) Alaska' => 'America/Anchorage',
        '(GMT-08:00) Pacific Time (US & Canada)' => 'America/Los_Angeles',
        '(GMT-08:00) Tijuana' => 'America/Tijuana',
        '(GMT-07:00) Arizona' => 'America/Phoenix',
        '(GMT-07:00) Mountain Time (US & Canada)' => 'America/Denver',
        '(GMT-07:00) Chihuahua' => 'America/Chihuahua',
        '(GMT-07:00) La Paz' => 'America/Chihuahua',
        '(GMT-07:00) Mazatlan' => 'America/Mazatlan',
        '(GMT-06:00) Central Time (US & Canada)' => 'America/Chicago',
        '(GMT-06:00) Central America' => 'America/Managua',
        '(GMT-06:00) Guadalajara' => 'America/Mexico_City',
        '(GMT-06:00) Mexico City' => 'America/Mexico_City',
        '(GMT-06:00) Monterrey' => 'America/Monterrey',
        '(GMT-06:00) Saskatchewan' => 'America/Regina',
        '(GMT-05:00) Eastern Time (US & Canada)' => 'America/New_York',
        '(GMT-05:00) Indiana (East)' => 'America/Indiana/Indianapolis',
        '(GMT-05:00) Bogota' => 'America/Bogota',
        '(GMT-05:00) Lima' => 'America/Lima',
        '(GMT-05:00) Quito' => 'America/Bogota',
        '(GMT-04:00) Atlantic Time (Canada)' => 'America/Halifax',
        '(GMT-04:00) Caracas' => 'America/Caracas',
        '(GMT-04:00) La Paz' => 'America/La_Paz',
        '(GMT-04:00) Santiago' => 'America/Santiago',
        '(GMT-03:30) Newfoundland' => 'America/St_Johns',
        '(GMT-03:00) Brasilia' => 'America/Sao_Paulo',
        '(GMT-03:00) Buenos Aires' => 'America/Argentina/Buenos_Aires',
        '(GMT-03:00) Georgetown' => 'America/Argentina/Buenos_Aires',
        '(GMT-03:00) Greenland' => 'America/Godthab',
        '(GMT-02:00) Mid-Atlantic' => 'America/Noronha',
        '(GMT-01:00) Azores' => 'Atlantic/Azores',
        '(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
        '(GMT) Casablanca' => 'Africa/Casablanca',
        '(GMT) Dublin' => 'Europe/London',
        '(GMT) Edinburgh' => 'Europe/London',
        '(GMT) Lisbon' => 'Europe/Lisbon',
        '(GMT) London' => 'Europe/London',
        '(GMT) UTC' => 'UTC',
        '(GMT) Monrovia' => 'Africa/Monrovia',
        '(GMT+01:00) Amsterdam' => 'Europe/Amsterdam',
        '(GMT+01:00) Belgrade' => 'Europe/Belgrade',
        '(GMT+01:00) Berlin' => 'Europe/Berlin',
        '(GMT+01:00) Bern' => 'Europe/Berlin',
        '(GMT+01:00) Bratislava' => 'Europe/Bratislava',
        '(GMT+01:00) Brussels' => 'Europe/Brussels',
        '(GMT+01:00) Budapest' => 'Europe/Budapest',
        '(GMT+01:00) Copenhagen' => 'Europe/Copenhagen',
        '(GMT+01:00) Ljubljana' => 'Europe/Ljubljana',
        '(GMT+01:00) Madrid' => 'Europe/Madrid',
        '(GMT+01:00) Paris' => 'Europe/Paris',
        '(GMT+01:00) Prague' => 'Europe/Prague',
        '(GMT+01:00) Rome' => 'Europe/Rome',
        '(GMT+01:00) Sarajevo' => 'Europe/Sarajevo',
        '(GMT+01:00) Skopje' => 'Europe/Skopje',
        '(GMT+01:00) Stockholm' => 'Europe/Stockholm',
        '(GMT+01:00) Vienna' => 'Europe/Vienna',
        '(GMT+01:00) Warsaw' => 'Europe/Warsaw',
        '(GMT+01:00) West Central Africa' => 'Africa/Lagos',
        '(GMT+01:00) Zagreb' => 'Europe/Zagreb',
        '(GMT+02:00) Athens' => 'Europe/Athens',
        '(GMT+02:00) Bucharest' => 'Europe/Bucharest',
        '(GMT+02:00) Cairo' => 'Africa/Cairo',
        '(GMT+02:00) Harare' => 'Africa/Harare',
        '(GMT+02:00) Helsinki' => 'Europe/Helsinki',
        '(GMT+02:00) Istanbul' => 'Europe/Istanbul',
        '(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',
        '(GMT+02:00) Kyev' => 'Europe/Kiev',
        '(GMT+02:00) Minsk' => 'Europe/Minsk',
        '(GMT+02:00) Pretoria' => 'Africa/Johannesburg',
        '(GMT+02:00) Riga' => 'Europe/Riga',
        '(GMT+02:00) Sofia' => 'Europe/Sofia',
        '(GMT+02:00) Tallinn' => 'Europe/Tallinn',
        '(GMT+02:00) Vilnius' => 'Europe/Vilnius',
        '(GMT+03:00) Baghdad' => 'Asia/Baghdad',
        '(GMT+03:00) Kuwait' => 'Asia/Kuwait',
        '(GMT+03:00) Moscow' => 'Europe/Moscow',
        '(GMT+03:00) Nairobi' => 'Africa/Nairobi',
        '(GMT+03:00) Riyadh' => 'Asia/Riyadh',
        '(GMT+03:00) St. Petersburg' => 'Europe/Moscow',
        '(GMT+03:00) Volgograd' => 'Europe/Volgograd',
        '(GMT+03:30) Tehran' => 'Asia/Tehran',
        '(GMT+04:00) Abu Dhabi' => 'Asia/Muscat',
        '(GMT+04:00) Baku' => 'Asia/Baku',
        '(GMT+04:00) Muscat' => 'Asia/Muscat',
        '(GMT+04:00) Tbilisi' => 'Asia/Tbilisi',
        '(GMT+04:00) Yerevan' => 'Asia/Yerevan',
        '(GMT+04:30) Kabul' => 'Asia/Kabul',
        '(GMT+05:00) Ekaterinburg' => 'Asia/Yekaterinburg',
        '(GMT+05:00) Islamabad' => 'Asia/Karachi',
        '(GMT+05:00) Karachi' => 'Asia/Karachi',
        '(GMT+05:00) Tashkent' => 'Asia/Tashkent',
        '(GMT+05:30) Chennai' => 'Asia/Kolkata',
        '(GMT+05:30) Kolkata' => 'Asia/Kolkata',
        '(GMT+05:30) Mumbai' => 'Asia/Kolkata',
        '(GMT+05:30) New Delhi' => 'Asia/Kolkata',
        '(GMT+05:45) Kathmandu' => 'Asia/Kathmandu',
        '(GMT+06:00) Almaty' => 'Asia/Almaty',
        '(GMT+06:00) Astana' => 'Asia/Dhaka',
        '(GMT+06:00) Dhaka' => 'Asia/Dhaka',
        '(GMT+06:00) Novosibirsk' => 'Asia/Novosibirsk',
        '(GMT+06:00) Sri Jayawardenepura' => 'Asia/Colombo',
        '(GMT+06:30) Rangoon' => 'Asia/Rangoon',
        '(GMT+07:00) Bangkok' => 'Asia/Bangkok',
        '(GMT+07:00) Hanoi' => 'Asia/Bangkok',
        '(GMT+07:00) Jakarta' => 'Asia/Jakarta',
        '(GMT+07:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
        '(GMT+08:00) Beijing' => 'Asia/Hong_Kong',
        '(GMT+08:00) Chongqing' => 'Asia/Chongqing',
        '(GMT+08:00) Hong Kong' => 'Asia/Hong_Kong',
        '(GMT+08:00) Irkutsk' => 'Asia/Irkutsk',
        '(GMT+08:00) Kuala Lumpur' => 'Asia/Kuala_Lumpur',
        '(GMT+08:00) Perth' => 'Australia/Perth',
        '(GMT+08:00) Singapore' => 'Asia/Singapore',
        '(GMT+08:00) Taipei' => 'Asia/Taipei',
        '(GMT+08:00) Ulaan Bataar' => 'Asia/Irkutsk',
        '(GMT+08:00) Urumqi' => 'Asia/Urumqi',
        '(GMT+09:00) Osaka' => 'Asia/Tokyo',
        '(GMT+09:00) Sapporo' => 'Asia/Tokyo',
        '(GMT+09:00) Seoul' => 'Asia/Seoul',
        '(GMT+09:00) Tokyo' => 'Asia/Tokyo',
        '(GMT+09:00) Yakutsk' => 'Asia/Yakutsk',
        '(GMT+09:30) Adelaide' => 'Australia/Adelaide',
        '(GMT+09:30) Darwin' => 'Australia/Darwin',
        '(GMT+10:00) Brisbane' => 'Australia/Brisbane',
        '(GMT+10:00) Canberra' => 'Australia/Sydney',
        '(GMT+10:00) Guam' => 'Pacific/Guam',
        '(GMT+10:00) Hobart' => 'Australia/Hobart',
        '(GMT+10:00) Melbourne' => 'Australia/Melbourne',
        '(GMT+10:00) Port Moresby' => 'Pacific/Port_Moresby',
        '(GMT+10:00) Sydney' => 'Australia/Sydney',
        '(GMT+10:00) Vladivostok' => 'Asia/Vladivostok',
        '(GMT+11:00) Magadan' => 'Asia/Magadan',
        '(GMT+11:00) New Caledonia' => 'Asia/Magadan',
        '(GMT+11:00) Solomon Is.' => 'Asia/Magadan',
        '(GMT+12:00) Auckland' => 'Pacific/Auckland',
        '(GMT+12:00) Fiji' => 'Pacific/Fiji',
        '(GMT+12:00) Kamchatka' => 'Asia/Kamchatka',
        '(GMT+12:00) Marshall Is.' => 'Pacific/Fiji',
        '(GMT+12:00) Wellington' => 'Pacific/Auckland',
        '(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu'
    );
}

if (!function_exists('app_timezone')) {
    function app_timezone()
    {
        return config('app.timezone');
    }
}

if (!function_exists('api_asset')) {
    function api_asset($id)
    {
        if (($asset = \App\Upload::find($id)) != null) {
            return $asset->file_name;
        }
        return "";
    }
}

//return file uploaded via uploader
if (!function_exists('uploaded_asset')) {
    function uploaded_asset($id, $absolute_path = false)
    {

        $absolute_path_status_string = $absolute_path ? 'true' : 'false';
        $cache_key = "uploaded_asset_" . $id . "_" . $absolute_path_status_string;
        $return_value = null;

        if (Redis::exists($cache_key)) {
            return Redis::get($cache_key);
        } else {
            if (($asset = \App\Upload::find($id)) != null) {
                $return_value = my_asset($asset->file_name, null, $absolute_path);
            }
        }
        Redis::setex($cache_key, config('cache.expiry.uploaded_asset'), $return_value);
        return $return_value;
    }
}

if (!function_exists('my_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function my_asset($path, $secure = null, $absolute_path = false)
    {
        if (env('FILESYSTEM_DRIVER') == 's3') {
            return Storage::disk('s3')->url($path);
        } else {
            if ($absolute_path)
                return public_path($path);
            else
                return app('url')->asset('public/' . $path, $secure);
        }
    }
}

if (!function_exists('static_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function static_asset($path, $secure = null)
    {
        return app('url')->asset('public/' . $path, $secure);
    }
}

if (!function_exists('getUserPhone')) {
    function getUserPhone()
    {

        if (substr(Auth::user()->phone, 0, 4) != '+357' && substr(Auth::user()->phone, 0, 3) != '357' && substr(Auth::user()->phone, 0, 1) != '+') {
            return '+357' . Auth::user()->phone;
        } else {

            return Auth::user()->phone;


        }
    }
}


if (!function_exists('isHttps')) {
    function isHttps()
    {
        return !empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS']);
    }
}

if (!function_exists('getBaseURL')) {
    function getBaseURL()
    {
        $root = (isHttps() ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
        $root .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

        return $root;
    }
}


if (!function_exists('getFileBaseURL')) {
    function getFileBaseURL()
    {
        if (env('FILESYSTEM_DRIVER') == 's3') {
            return env('AWS_URL') . '/';
        } else {
            return getBaseURL() . 'public/';
        }
    }
}


if (!function_exists('isUnique')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function isUnique($email)
    {
        $user = \App\User::where('email', $email)->first();

        if ($user == null) {
            return '1'; // $user = null means we did not get any match with the email provided by the user inside the database
        } else {
            return '0';
        }
    }
}

if (!function_exists('get_setting')) {
    function get_setting($key, $default = null)
    {
        if ($key == 'show_webshop' && (request()->ip() == '82.102.76.201' || request()->ip() == '')) {
            return 'on';
        }

        $cache_key = 'business_settings:' . $key;
        if (Redis::exists($cache_key)) {
            return Redis::get($cache_key);
        } else {
            $setting = BusinessSetting::where('type', $key)->first();
            $return_value = $setting == null ? $default : $setting->value;
            Redis::setex($cache_key, config('cache.expiry.business_settings'), $return_value);
            return $return_value;
        }

//        $setting = BusinessSetting::where('type', $key)->first();
//        return $setting == null ? $default : $setting->value;
    }
}

function hex2rgba($color, $opacity = false)
{
    $default = 'rgb(230,46,4)';
    //Return default if no color provided
    if (empty($color))
        return $default;

    //Sanitize $color if "#" is provided
    if ($color[0] == '#') {
        $color = substr($color, 1);
    }

    //Check if color has 6 or 3 characters and get values
    if (strlen($color) == 6) {
        $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
    } elseif (strlen($color) == 3) {
        $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
    } else {
        return $default;
    }

    //Convert hexadec to rgb
    $rgb = array_map('hexdec', $hex);

    //Check if opacity is set(rgba or rgb)
    if ($opacity) {
        if (abs($opacity) > 1)
            $opacity = 1.0;
        $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
    } else {
        $output = 'rgb(' . implode(",", $rgb) . ')';
    }

    //Return rgb(a) color string
    return $output;
}

if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        if (Auth::check() && (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff')) {
            return true;
        }
        return false;
    }
}

if (!function_exists('isCashier')) {
    function isCashier()
    {
        if (Auth::check() && (Auth::user()->user_type == 'cashier')) {
            return true;
        }
        return false;
    }
}

if (!function_exists('isSeller')) {
    function isSeller()
    {
        if (Auth::check() && Auth::user()->user_type == 'seller') {
            return true;
        }
        return false;
    }
}

if (!function_exists('isCustomer')) {
    function isCustomer()
    {
        if (Auth::check() && Auth::user()->user_type == 'customer') {
            return true;
        }
        return false;
    }
}

if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

// duplicates m$ excel's ceiling function
if (!function_exists('ceiling')) {
    function ceiling($number, $significance = 1)
    {
        return (is_numeric($number) && is_numeric($significance)) ? (ceil($number / $significance) * $significance) : false;
    }
}

if (!function_exists('get_images')) {
    function get_images($given_ids, $with_trashed = false)
    {
        $ids = is_array($given_ids) ? $given_ids : (is_null($given_ids) ? [] : explode(",", $given_ids));

        return $with_trashed
            ? Upload::withTrashed()->whereIn('id', $ids)->get()
            : Upload::whereIn('id', $ids)->get();
    }
}

//for api
if (!function_exists('get_images_path')) {
    function get_images_path($given_ids, $with_trashed = false)
    {
        $paths = [];
        $images = get_images($given_ids, $with_trashed);
        if (!$images->isEmpty()) {
            foreach ($images as $image) {
                $paths[] = !is_null($image) ? $image->file_name : "";
            }
        }

        return $paths;

    }
}

//for api
if (!function_exists('checkout_done')) {
    function checkout_done($order_id, $payment)
    {
        $order = Order::findOrFail($order_id);
        $order->payment_status = 'paid';
        $order->payment_details = $payment;
        $order->save();

        if (\App\Addon::where('unique_identifier', 'affiliate_system')->first() != null && \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {
            $affiliateController = new AffiliateController;
            $affiliateController->processAffiliatePoints($order);
        }

        if (\App\Addon::where('unique_identifier', 'club_point')->first() != null && \App\Addon::where('unique_identifier', 'club_point')->first()->activated) {
            if (Auth::check()) {
                $clubpointController = new ClubPointController;
                $clubpointController->processClubPoints($order);
            }
        }
        if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() == null || !\App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
            if (BusinessSetting::where('type', 'category_wise_commission')->first()->value != 1) {
                $commission_percentage = BusinessSetting::where('type', 'vendor_commission')->first()->value;
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->payment_status = 'paid';
                    $orderDetail->save();
                    if ($orderDetail->product->user->user_type == 'seller') {
                        $seller = $orderDetail->product->user->seller;
                        $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
                        $seller->save();
                    }
                }
            } else {
                foreach ($order->orderDetails as $key => $orderDetail) {
                    $orderDetail->payment_status = 'paid';
                    $orderDetail->save();
                    if ($orderDetail->product->user->user_type == 'seller') {
                        $commission_percentage = $orderDetail->product->category->commision_rate;
                        $seller = $orderDetail->product->user->seller;
                        $seller->admin_to_pay = $seller->admin_to_pay + ($orderDetail->price * (100 - $commission_percentage)) / 100 + $orderDetail->tax + $orderDetail->shipping_cost;
                        $seller->save();
                    }
                }
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->payment_status = 'paid';
                $orderDetail->save();
                if ($orderDetail->product->user->user_type == 'seller') {
                    $seller = $orderDetail->product->user->seller;
                    $seller->admin_to_pay = $seller->admin_to_pay + $orderDetail->price + $orderDetail->tax + $orderDetail->shipping_cost;
                    $seller->save();
                }
            }
        }

        $order->commission_calculated = 1;
        $order->save();
    }
}

//for api
if (!function_exists('wallet_payment_done')) {
    function wallet_payment_done($user_id, $amount, $payment_method, $payment_details)
    {
        $user = \App\User::find($user_id);
        $user->balance = $user->balance + $amount;
        $user->save();

        $wallet = new Wallet;
        $wallet->user_id = $user->id;
        $wallet->amount = $amount;
        $wallet->payment_method = $payment_method;
        $wallet->payment_details = $payment_details;
        $wallet->save();

    }
}

if (!function_exists('toUpper')) {
    function toUpper($str)
    {
        $search = array('Ά', 'Έ', 'Ί', 'Ή', 'Ύ', 'Ό', 'Ώ');
        $replace = array('Α', 'Ε', 'Ι', 'Η', 'Υ', 'Ο', 'Ω');
        $str = mb_strtoupper($str, "UTF-8");
        return str_replace($search, $replace, $str);
    }
}

if (!function_exists('getRecaptchaKeys')) {
    function getRecaptchaKeys()
    {
        return (object)array(
            'public' => env('CAPTCHA_KEY'),
            'secret' => env('CAPTCHA_SECRET')
        );
    }
}

if (!function_exists('getCategoryParents')) {
    function getCategoryParents($id, $level = 0)
    {
        $categories = array();

        if ($id != 0) {
            $item = array(
                'id' => $id,
                'name' => \App\Category::find($id)->getTranslation('name'),
                'parent_id' => \App\Category::find($id)->parent_id,
                'slug' => \App\Category::find($id)->slug,
            );
            $categories[] = $item;
            if (\App\Category::find($id)->parent_id != 0) {
                $child_item = getCategoryParents(\App\Category::find($id)->parent_id, 1);
                $categories[] = array(
                    'id' => $child_item[0]['id'],
                    'name' => $child_item[0]['name'],
                    'parent_id' => $child_item[0]['parent_id'],
                    'slug' => $child_item[0]['slug'],
                );
            }
        }

        return $categories;

    }
}
if (!function_exists('getMainCatName')) {
    function getMainCategory($id)
    {
        $category = cache()->remember('main_category_id_' . $id, config('cache.expiry.category'), function () use ($id) {
            return App\Category::find($id);
        });
        while ($category->parent_id > 0) {
            $category_id = $category->parent_id;
            $category = cache()->remember('main_category_id_' . $category_id, config('cache.expiry.category'), function () use ($category_id) {
                return App\Category::findOrFail($category_id);
            });
        }

        return $category;
    }
}
/*if (!function_exists('getMainCatName')) {
    function getMainCategory($id) {
        $category = \App\Category::findOrFail($id);
        while ($category->parent_id > 0) {
            $category = \App\Category::findOrFail($category->parent_id);
        }

        return $category;
    }
}*/

if (!function_exists('getSidebarCategories')) {
    function getSidebarCategories($outlet = 0, $parent = 0)
    {
        $main_cats_array = array();

        if ($parent == 0) {
            $side_categories = DB::table('categories')
                ->join('products', 'categories.id', '=', 'products.category_id')
                ->select('categories.*')
                ->orderBy('categories.order_level', 'desc')
                ->where('categories.for_sale', 1)
                ->where('products.outlet', $outlet)
                ->where('products.used', 0)
                ->where('products.published', 1)
                ->distinct()
                ->get();

            if (count($side_categories) > 0) {
                foreach ($side_categories as $cat) {
                    $this_cat = getMainCategory($cat->id);
                    if (!in_array($this_cat->id, $main_cats_array)) {
                        $main_cats_array[] = $this_cat->id;
                    }
                }
            }

            $categories = \App\Category::whereIn('id', $main_cats_array)->orderBy('order_level', 'desc')->get();
        } else {
            $side_categories = DB::table('categories')
                ->join('products', 'categories.id', '=', 'products.category_id')
                ->select('categories.*')
                ->orderBy('categories.order_level', 'desc')
                ->where('categories.for_sale', 1)
                ->where('categories.parent_id', $parent)
                ->where('products.outlet', $outlet)
                ->where('products.used', 0)
                ->where('products.published', 1)
                ->distinct()
                ->get();
            if (count($side_categories) > 0) {
                foreach ($side_categories as $cat) {
                    if (!in_array($cat->id, $main_cats_array)) {
                        $main_cats_array[] = $cat->id;
                    }
                }
            }
            $categories = \App\Category::whereIn('id', $main_cats_array)->orderBy('order_level', 'desc')->get();
        }

        return $categories;
    }
}

if (!function_exists('getMainCategoriesByType')) {
    function getMainCategoriesByType($type_id, $limit = null, $show_header = null)
    {
        $cat_array = array();
        $all_categories = \App\Category::join('products', 'categories.id', '=', 'products.category_id')
            ->join('product_types', 'product_types.id', '=', 'products.type_id')
            ->select('categories.*')
            ->orderBy('categories.order_level', 'desc')
            ->where('product_types.id', $type_id)
            ->where('products.used', 0)
            ->where('products.outlet', 0)
            ->where('products.published', 1);

        // TODO: George Remove the below code
        if (request()->ip() !== '82.102.76.201') {
            $all_categories->where('categories.for_sale', 0);
        }

        $all_categories->distinct();
        $all_categories = $all_categories->get();

        if (count($all_categories) > 0) {
            foreach ($all_categories as $cat) {
                $this_cat = getMainCategory($cat->id);
                if (!in_array($this_cat->id, $cat_array)) {
                    $cat_array[] = $this_cat->id;
                }
            }
        }

        $categories = \App\Category::whereIn('id', $cat_array)->orderBy('order_level', 'desc');

        if ($show_header != null) {
            $categories = $categories->where('show_header', $show_header);
        }

        if ($limit != null) {
            $categories = $categories->limit($limit);
        }

        $categories = $categories->get();

        return $categories;
    }
}

if (!function_exists('getMainCategoriesByBrand')) {
    function getMainCategoriesByBrand($brand_id)
    {
        $cache_key = 'get_main_categories_by_brand_' . $brand_id;
        return Cache::remember($cache_key, config('cache.expiry.get_brands_by_main_cat'), function () use ($brand_id) {
            $cat_array = array();
            $all_categories = \App\Category::join('products', 'categories.id', '=', 'products.category_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->select('categories.id')
                ->orderBy('categories.order_level', 'desc')
                ->where('brands.id', $brand_id)
                ->where('products.used', 0)
                ->where('products.outlet', 0)
                ->where('products.published', 1)
                ->distinct()
                ->get();

            if (count($all_categories) > 0) {
                foreach ($all_categories as $cat) {
                    $this_cat = getMainCategory($cat->id);
                    if (!in_array($this_cat->id, $cat_array)) {
                        $cat_array[] = $this_cat->id;
                    }
                }
            }
            return \App\Category::whereIn('id', $cat_array)->orderBy('order_level', 'desc')->get();
        });
    }
}

if (!function_exists('getWebshopMainCategories')) {
    function getWebshopMainCategories($limit = null, $show_header = null)
    {

        $limit_string = $limit == null ? '-' : $limit;
        $show_header_string = $show_header == null ? '-' : $show_header;

        $cache_key = 'get_webshop_main_categories_' . $limit_string . '_' . $show_header_string;
        return Cache::remember($cache_key, config('cache.expiry.get_webshop_main_categories'), function () use ($limit, $show_header) {

            $cat_array = array();
            $all_categories = \App\Category::join('products', 'categories.id', '=', 'products.category_id')
                ->join('product_types', 'product_types.id', '=', 'products.type_id')
                ->select('categories.id')
                ->orderBy('categories.order_level', 'desc')
                ->where('categories.for_sale', 1)
                ->where('products.used', 0)
                ->where('products.outlet', 0)
                ->where('products.published', 1)
                ->distinct()
                ->get();


            if (count($all_categories) > 0) {
                foreach ($all_categories as $cat) {
                    $this_cat = getMainCategory($cat->id);
                    if (!in_array($this_cat->id, $cat_array) && $this_cat->for_sale == 1) {
                        $cat_array[] = $this_cat->id;
                    }
                }
            }

            $categories = \App\Category::whereIn('id', $cat_array)->orderBy('order_level', 'desc');

            if ($show_header != null) {
                $categories = $categories->where('show_header', $show_header);
            }

            if ($limit != null) {
                $categories = $categories->limit($limit);
            }

            return $categories->get();
        });
    }
}

if (!function_exists('getAllAvailableMainCategories')) {
    function getAllAvailableMainCategories()
    {
        $cat_array = array();
        $all_categories = \App\Category::join('products', 'categories.id', '=', 'products.category_id')
            ->select('categories.*')
            ->orderBy('categories.order_level', 'desc')
            ->where('products.used', 0)
            ->where('products.outlet', 0)
            ->where('products.published', 1)
            ->distinct()
            ->get();

        if (count($all_categories) > 0) {
            foreach ($all_categories as $cat) {
                $this_cat = getMainCategory($cat->id);
                if (!in_array($this_cat->id, $cat_array)) {
                    $cat_array[] = $this_cat->id;
                }
            }
        }

        $categories = \App\Category::whereIn('id', $cat_array)->orderBy('order_level', 'desc')->get();

        return $categories;
    }
}
if (!function_exists('getAvailableMainFeatureCategories')) {
    function getAvailableMainFeatureCategories()
    {

        $cache_key = 'get_available_main_feature_categories';
        return Cache::remember($cache_key, config('cache.expiry.get_brands_by_main_cat'), function () {
            $cat_array = array();
            $all_categories = \App\Category::join('products', 'categories.id', '=', 'products.category_id')
                ->select('categories.id')
                ->orderBy('categories.order_level', 'desc')
                ->where('products.used', 0)
                ->where('products.outlet', 0)
                ->where('products.published', 1)
                ->distinct()
                ->get();

            if (count($all_categories) > 0) {
                foreach ($all_categories as $cat) {
                    $this_cat = getMainCategory($cat->id);
                    if (!in_array($this_cat->id, $cat_array)) {
                        $cat_array[] = $this_cat->id;
                    }
                }
            }

            return \App\Category::whereIn('id', $cat_array)->where('featured', 1)->orderBy('order_level', 'desc')->get();
        });
    }
}

if (!function_exists('getAvailableSubCategoriesByCategory')) {
    /*function getAvailableSubCategoriesByCategory($category_id) {
      $cat_array = array();
      $cache_key = 'get_available_subcategories_by_category_'.$category_id;

      if (Redis::exists($cache_key)) {
        $all_categories = json_decode(Redis::get($cache_key));
      }
      else {
        $all_categories = DB::table('categories')
          ->join('products', 'categories.id', '=', 'products.category_id')
          ->select('categories.id')
          ->orderBy('categories.order_level', 'desc')
          ->where('products.used', 0)
          ->where('products.outlet', 0)
          ->where('products.published', 1)
          ->where('categories.parent_id', $category_id)
          ->distinct()
          ->get();

        $all_categories_encoded = json_encode($all_categories);
        Redis::setex($cache_key, config('cache.expiry.get_available_subcategories_by_category'), $all_categories_encoded);
      }


  //    $all_categories = Cache::remember('get_available_subcategories_by_category', config('cache.expiry.get_available_subcategories_by_category'), function () use ($category_id) {
  //      $all_categories = DB::table('categories')
  //      ->join('products', 'categories.id', '=', 'products.category_id')
  //      ->select('categories.id')
  //      ->orderBy('categories.order_level', 'desc')
  //      ->where('products.used', 0)
  //      ->where('products.outlet', 0)
  //      ->where('products.published', 1)
  //      ->where('categories.parent_id', $category_id)
  //      ->distinct()
  //      ->get();
  //    });
        if(count($all_categories) > 0) {
          foreach ($all_categories as $cat) {
            if(!in_array($cat->id, $cat_array)) {
              $cat_array[] = $cat->id;
            }
          }
        }

      $categories = \App\Category::whereIn('id', $cat_array)->orderBy('order_level', 'desc')->get();

      return $categories;
    }*/

    function getAvailableSubCategoriesByCategory($category_id)
    {

        $cache_key = 'get_available_subcategories_by_category_' . $category_id;

        return Cache::remember($cache_key, config('cache.expiry.get_available_subcategories_by_category'), function () use ($category_id) {
            $cat_array = array();

            $all_categories = DB::table('categories')
                ->join('products', 'categories.id', '=', 'products.category_id')
                ->select('categories.id')
                ->orderBy('categories.order_level', 'desc')
                ->where('products.used', 0)
                ->where('products.outlet', 0)
                ->where('products.published', 1)
                ->where('categories.parent_id', $category_id)
                ->distinct()
                ->get();

            if (count($all_categories) > 0) {
                foreach ($all_categories as $cat) {
                    if (!in_array($cat->id, $cat_array)) {
                        $cat_array[] = $cat->id;
                    }
                }
            }

            return \App\Category::whereIn('id', $cat_array)->orderBy('order_level', 'desc')->get();
        });
    }
}


if (!function_exists('getAvailableWebSubCategoriesByCategory')) {
    /*function getAvailableWebSubCategoriesByCategory($category_id){
      $cat_array = array();
      $all_categories = DB::table('categories')
        ->join('products', 'categories.id', '=', 'products.category_id')
        ->select('categories.*')
        ->orderBy('categories.order_level', 'desc')
        ->where('products.used', 0)
        ->where('products.outlet', 0)
        ->where('products.published', 1)
        ->where('categories.parent_id', $category_id)
        ->where('categories.for_sale', 1)
        ->distinct()
        ->get();

      if(count($all_categories) > 0) {
        foreach ($all_categories as $cat) {
          if(!in_array($cat->id, $cat_array)) {
            $cat_array[] = $cat->id;
          }
        }
      }

      $categories = \App\Category::whereIn('id', $cat_array)->orderBy('order_level', 'desc')->get();

      return $categories;
    }*/

    function getAvailableWebSubCategoriesByCategory($category_id)
    {

        $cache_key = 'get_available_web_subcategories_by_category_' . $category_id;

        return Cache::remember($cache_key, config('cache.expiry.get_available_web_subcategories_by_category'), function () use ($category_id) {
            $cat_array = array();

            $all_categories = DB::table('categories')
                ->join('products', 'categories.id', '=', 'products.category_id')
                ->select('categories.id')
                ->orderBy('categories.order_level', 'desc')
                ->where('products.used', 0)
                ->where('products.outlet', 0)
                ->where('products.published', 1)
                ->where('categories.parent_id', $category_id)
                ->where('categories.for_sale', 1)
                ->distinct()
                ->get();

            if (count($all_categories) > 0) {
                foreach ($all_categories as $cat) {
                    if (!in_array($cat->id, $cat_array)) {
                        $cat_array[] = $cat->id;
                    }
                }
            }

            return \App\Category::whereIn('id', $cat_array)->orderBy('order_level', 'desc')->get();
        });
    }
}

if (!function_exists('getSubCategoriesByBrand')) {
    function getSubCategoriesByBrand($category_id, $brand_id)
    {
        $cache_key = 'get_subcategories_by_brand_' . $category_id . "_" . $brand_id;
        return Cache::remember($cache_key, config('cache.expiry.get_subcategories_by_brand'), function () use ($category_id, $brand_id) {
            $cat_array = array();

            $all_categories = \App\Category::join('products', 'categories.id', '=', 'products.category_id')
                ->join('brands', 'brands.id', '=', 'products.brand_id')
                ->select('categories.id')
                ->orderBy('categories.order_level', 'desc')
                ->where('brands.id', $brand_id)
                ->where('categories.parent_id', $category_id)
                ->where('products.used', 0)
                ->where('products.outlet', 0)
                ->where('products.published', 1)
                ->distinct()
                ->get();

            if (count($all_categories) > 0) {
                foreach ($all_categories as $cat) {
                    if (!in_array($cat->id, $cat_array)) {
                        $cat_array[] = $cat->id;
                    }
                }
            }

            return \App\Category::whereIn('id', $cat_array)->orderBy('order_level', 'desc')->get();
        });
    }
}

if (!function_exists('getSubCategoriesByTypeAndCat')) {
    function getSubCategoriesByTypeAndCat($category_id, $type_id)
    {

        $cache_key = 'get_subcategories_by_type_and_cat_' . $category_id . "_" . $type_id;
        return Cache::remember($cache_key, config('cache.expiry.get_subcategories_by_type_and_cat'), function () use ($category_id, $type_id) {
            $cat_array = array();

            $all_categories = \App\Category::join('products', 'categories.id', '=', 'products.category_id')
                ->join('product_types', 'product_types.id', '=', 'products.type_id')
                ->select('categories.id')
                ->orderBy('categories.order_level', 'desc')
                ->where('product_types.id', $type_id)
                ->where('categories.parent_id', $category_id)
                ->where('products.used', 0)
                ->where('products.outlet', 0)
                ->where('products.published', 1)
                ->distinct()
                ->get();

            if (count($all_categories) > 0) {
                foreach ($all_categories as $cat) {
                    if (!in_array($cat->id, $cat_array)) {
                        $cat_array[] = $cat->id;
                    }
                }
            }

            return \App\Category::whereIn('id', $cat_array)->orderBy('order_level', 'desc')->get();
        });
    }
}

if (!function_exists('getBrandsByMainCategory')) {
    function getBrandsByMainCategory($category_id)
    {

        $cache_key = 'get_brands_by_main_cat_' . $category_id;
        return Cache::remember($cache_key, config('cache.expiry.get_brands_by_main_cat'), function () use ($category_id) {
            $brand_array = array();

            $category_ids = CategoryUtility::children_ids($category_id);
            $category_ids[] = $category_id;

            $all_brands = DB::table('brands')
                ->join('products', 'brands.id', '=', 'products.brand_id')
                ->select('brands.id')
                ->orderBy('brands.order_level', 'desc')
                ->whereIn('products.category_id', $category_ids)
                ->where('products.used', 0)
                ->where('products.outlet', 0)
                ->where('products.published', 1)
                ->distinct()
                ->get();

            if (count($all_brands) > 0) {
                foreach ($all_brands as $brand) {
                    if (!in_array($brand->id, $brand_array)) {
                        $brand_array[] = $brand->id;
                    }
                }
            }

            return \App\Brand::whereIn('id', $brand_array)->orderBy('order_level', 'desc')->get();

        });
    }
}

if (!function_exists('includeVatText')) {
    function includeVatText()
    {

        $vat = PlatformSetting::where('type', 'vat_percentage')->first()->value;
        $text = translate('Price Includes') . ' ' . $vat . '% ' . translate('VAT');
        return $text;
    }
}

if (!function_exists('VatPercentage')) {
    function VatPercentage()
    {
        /*if(Session::has('shipping_info') && in_array(Route::currentRouteName(), isPaymentPage())) {
            $shipping_info = Session::get('shipping_info');
            $country = \App\Country::find($shipping_info['country']);
            if($country->vat_included == 1) {
                $percentage = $country->vat_percentage;
            } else {
                $percentage = 0;
            }
        } elseif(Auth::check()) {
            $country = \App\Country::find(Auth::user()->country);
            if($country->vat_included == 1) {
                $percentage = $country->vat_percentage;
            } else {
                $percentage = 0;
            }
        } else {
            $country = \App\Country::find(54);
            $percentage = $country->vat_percentage;
        }*/

        if (Session::has('vat')) {
            $percentage = getVatFromSession('included') ? getVatFromSession('percentage') : 0;
        } else {
            if (Auth::check()) {
                $country = \App\Country::find(Auth::user()->country);
                setVatOnSession(((auth()->check() && auth()->user()->excluded_vat) ? 0 : $country->vat_included), $country);
                $percentage = $country->vat_percentage;
            } else {
                $country = \App\Country::find(54);
                $percentage = $country->vat_included ? $country->vat_percentage : 0;
            }
        }
        return $percentage;
    }
}

if (!function_exists('getCountryVatPercentage')) {
    function getCountryVatPercentage($id)
    {
        $country = \App\Country::find($id);
        if ($country->vat_included == 1) {
            $percentage = $country->vat_percentage;
        } else {
            $percentage = 0;
        }
        return $percentage;
    }
}

if (!function_exists('calcPriceBeforeAddVat')) {
    function calcPriceBeforeAddVat($price, $return_type = "price", $vat_percentage = false)
    {
        if (!$vat_percentage) $vat_percentage = VatPercentage();

        $vat_amount = round($price / (1 + $vat_percentage));

        if ($return_type == "vat") {
//          return $vat_amount;
            return round(($vat_percentage * $price) / 100, 2);
        } else {
//          return round($price + $vat_amount);
            return round($price + (($vat_percentage * $price) / 100), 2);
        }
    }
}

if (!function_exists('getAccountName')) {
    function getAccountName($name)
    {
        $name_arr = array();
        $array = explode(' ', $name, 2);
        $name_arr['name'] = (array_key_exists(0, $array)) ? $array[0] : '';
        $name_arr['surname'] = (array_key_exists(1, $array)) ? $array[1] : '';

        return $name_arr;
    }
}

if (!function_exists('findCountryCode')) {
    function findCountryCode($iso)
    {
        $codes = array(
            'US' => 1,
            'AG' => 1,
            'AI' => 1,
            'AS' => 1,
            'BB' => 1,
            'BM' => 1,
            'BS' => 1,
            'CA' => 1,
            'DM' => 1,
            'DO' => 1,
            'GD' => 1,
            'GU' => 1,
            'JM' => 1,
            'KN' => 1,
            'KY' => 1,
            'LC' => 1,
            'MP' => 1,
            'MS' => 1,
            'PR' => 1,
            'SX' => 1,
            'TC' => 1,
            'TT' => 1,
            'VC' => 1,
            'VG' => 1,
            'VI' => 1,
            'RU' => 7,
            'KZ' => 7,
            'EG' => 20,
            'ZA' => 27,
            'GR' => 30,
            'NL' => 31,
            'BE' => 32,
            'FR' => 33,
            'ES' => 34,
            'HU' => 36,
            'IT' => 39,
            'VA' => 39,
            'RO' => 40,
            'CH' => 41,
            'AT' => 43,
            'GB' => 44,
            'GG' => 44,
            'IM' => 44,
            'JE' => 44,
            'DK' => 45,
            'SE' => 46,
            'NO' => 47,
            'SJ' => 47,
            'PL' => 48,
            'DE' => 49,
            'PE' => 51,
            'MX' => 52,
            'CU' => 53,
            'AR' => 54,
            'BR' => 55,
            'CL' => 56,
            'CO' => 57,
            'VE' => 58,
            'MY' => 60,
            'AU' => 61,
            'CC' => 61,
            'CX' => 61,
            'ID' => 62,
            'PH' => 63,
            'NZ' => 64,
            'SG' => 65,
            'TH' => 66,
            'JP' => 81,
            'KR' => 82,
            'VN' => 84,
            'CN' => 86,
            'TR' => 90,
            'IN' => 91,
            'PK' => 92,
            'AF' => 93,
            'LK' => 94,
            'MM' => 95,
            'IR' => 98,
            'SS' => 211,
            'MA' => 212,
            'EH' => 212,
            'DZ' => 213,
            'TN' => 216,
            'LY' => 218,
            'GM' => 220,
            'SN' => 221,
            'MR' => 222,
            'ML' => 223,
            'GN' => 224,
            'CI' => 225,
            'BF' => 226,
            'NE' => 227,
            'TG' => 228,
            'BJ' => 229,
            'MU' => 230,
            'LR' => 231,
            'SL' => 232,
            'GH' => 233,
            'NG' => 234,
            'TD' => 235,
            'CF' => 236,
            'CM' => 237,
            'CV' => 238,
            'ST' => 239,
            'GQ' => 240,
            'GA' => 241,
            'CG' => 242,
            'CD' => 243,
            'AO' => 244,
            'GW' => 245,
            'IO' => 246,
            'AC' => 247,
            'SC' => 248,
            'SD' => 249,
            'RW' => 250,
            'ET' => 251,
            'SO' => 252,
            'DJ' => 253,
            'KE' => 254,
            'TZ' => 255,
            'UG' => 256,
            'BI' => 257,
            'MZ' => 258,
            'ZM' => 260,
            'MG' => 261,
            'RE' => 262,
            'YT' => 262,
            'ZW' => 263,
            'NA' => 264,
            'MW' => 265,
            'LS' => 266,
            'BW' => 267,
            'SZ' => 268,
            'KM' => 269,
            'SH' => 290,
            'TA' => 290,
            'ER' => 291,
            'AW' => 297,
            'FO' => 298,
            'GL' => 299,
            'GI' => 350,
            'PT' => 351,
            'LU' => 352,
            'IE' => 353,
            'IS' => 354,
            'AL' => 355,
            'MT' => 356,
            'CY' => 357,
            'FI' => 358,
            'AX' => 358,
            'BG' => 359,
            'LT' => 370,
            'LV' => 371,
            'EE' => 372,
            'MD' => 373,
            'AM' => 374,
            'BY' => 375,
            'AD' => 376,
            'MC' => 377,
            'SM' => 378,
            'UA' => 380,
            'RS' => 381,
            'ME' => 382,
            'XK' => 383,
            'HR' => 385,
            'SI' => 386,
            'BA' => 387,
            'MK' => 389,
            'CZ' => 420,
            'SK' => 421,
            'LI' => 423,
            'FK' => 500,
            'BZ' => 501,
            'GT' => 502,
            'SV' => 503,
            'HN' => 504,
            'NI' => 505,
            'CR' => 506,
            'PA' => 507,
            'PM' => 508,
            'HT' => 509,
            'GP' => 590,
            'BL' => 590,
            'MF' => 590,
            'BO' => 591,
            'GY' => 592,
            'EC' => 593,
            'GF' => 594,
            'PY' => 595,
            'MQ' => 596,
            'SR' => 597,
            'UY' => 598,
            'CW' => 599,
            'BQ' => 599,
            'TL' => 670,
            'NF' => 672,
            'BN' => 673,
            'NR' => 674,
            'PG' => 675,
            'TO' => 676,
            'SB' => 677,
            'VU' => 678,
            'FJ' => 679,
            'PW' => 680,
            'WF' => 681,
            'CK' => 682,
            'NU' => 683,
            'WS' => 685,
            'KI' => 686,
            'NC' => 687,
            'TV' => 688,
            'PF' => 689,
            'TK' => 690,
            'FM' => 691,
            'MH' => 692,
            'KP' => 850,
            'HK' => 852,
            'MO' => 853,
            'KH' => 855,
            'LA' => 856,
            'BD' => 880,
            'TW' => 886,
            'MV' => 960,
            'LB' => 961,
            'JO' => 962,
            'SY' => 963,
            'IQ' => 964,
            'KW' => 965,
            'SA' => 966,
            'YE' => 967,
            'OM' => 968,
            'PS' => 970,
            'AE' => 971,
            'IL' => 972,
            'BH' => 973,
            'QA' => 974,
            'BT' => 975,
            'MN' => 976,
            'NP' => 977,
            'TJ' => 992,
            'TM' => 993,
            'AZ' => 994,
            'GE' => 995,
            'KG' => 996,
            'UZ' => 998,
        );
        return $codes[$iso];
    }
}

if (!function_exists('getUniqueCode')) {
    function getUniqueCode($length = "")
    {
        $code = md5(uniqid(rand(), true));
        if ($length != "") {
            return substr($code, 0, $length);
        } else
            return $code;
    }
}

if (!function_exists('getGreyHeadersRoute')) {
    function getGreyHeadersRoute()
    {
        $array = array(
            'faqs',
            'stores',
            'blog',
            'blog.details',
            'product',
            'product.brand',
            'product.type',
            'cart',
            'checkout.shipping_info',
            'checkout.store_shipping_infostore',
            'checkout.store_delivery_info',
            'order_confirmed',
            'order_failed',
            'user.login',
            'user.registration',
            'password.request',
            'password.email',
            'dashboard',
            'purchase_history.index',
            'digital_purchase_history.index',
            'customer_refund_request',
            'wishlists.index',
            'seller.products',
            'product_bulk_upload.index',
            'seller.digitalproducts',
            'seller.digitalproducts.upload',
            'seller.digitalproducts.edit',
            'customer_products.index',
            'customer_products.create',
            'customer_products.edit',
            'poin-of-sales.seller_index',
            'orders.index',
            'vendor_refund_request',
            'reason_show',
            'reviews.seller',
            'shops.index',
            'payments.index',
            'withdraw_requests.index',
            'commission-log.index',
            'conversations.index',
            'conversations.show',
            'wallet.index',
            'earnng_point_for_user',
            'affiliate.user.index',
            'affiliate.payment_settings',
            'affiliate.user.index',
            'affiliate.user.payment_history',
            'affiliate.user.withdraw_request_history',
            'support_ticket.index',
            'profile',
            'orders.track',
            'termspolicies',
            'sellerpolicy',
            'returnpolicy',
            'supportpolicy',
            'terms',
            'privacypolicy',
        );
        return $array;
    }
}

if (!function_exists('shippingMethodName')) {
    function shippingMethodName($name)
    {
        $option = "";
        if ($name == "home_delivery") {
            $option = translate('Home Delivery');
        } elseif ($name == "pickup_point") {
            $option = translate('Pick up from our Stores');
        } elseif ($name == "acs_delivery") {
            $option = translate('Collect from ACS Courier');
        } elseif ($name == "epg_parcels") {
            $option = translate('Parcels');
        } elseif ($name == "ems_datapost") {
            $option = translate('EMS Datapost');
        } elseif (!empty($name)) {
            $option = translate('Office to Office');
        }
        return $option;
    }
}

if (!function_exists('isPartner')) {
    function isPartner()
    {
        $partner = false;
        if (Auth::check() && Auth::user()->partner == 1 && Auth::user()->btms_customer_code <> null) {
            $partner = true;
        }
        return $partner;
    }
}

if (!function_exists('isPageWithPrice')) {
    function isPageWithPrice()
    {
        $array = array(
            'new_subscriptions.get_subscriptions',
            'cart',
            'checkout.payment_info',
            'checkout.store_delivery_info'
        );

        return $array;
    }
}

if (!function_exists('isPaymentPage')) {
    function isPaymentPage()
    {
        $array = array(
            'checkout.payment_info',
            'checkout.store_delivery_info'
        );

        return $array;
    }
}

if (!function_exists('getCityName')) {
    function getCityName($city)
    {
        return is_numeric($city) ? City::find($city)->name : $city;
    }
}

if (!function_exists('selected')) {
    function selected($value, $selected_value): string
    {
        return $selected_value == $value ? 'selected' : '';
    }
}


if (!function_exists('getSizeName')) {
    function getSizeName($ids)
    {
        if (is_array($ids)) {
            $array_names = [];
            foreach ($ids as $id) {
                $size = ItemSize::where('Size Id', $id)->first();
                $array_names[] = ($size == null) ? '' : $size->{'Size Name'};
            }
            return $array_names;
        } else {
            $size = ItemSize::where('Size Id', $ids)->first();
            return $size == null ? '' : $size->{'Size Name'};
        }
    }
}
if (!function_exists('getColorName')) {
    function getColorName($ids, $detail = 'name')
    {
        if (is_array($ids)) {
            $array_names = [];
            foreach ($ids as $id) {
                $color = Color::find($id);
                $array_names[] = ($color == null) ? '' : ($detail == 'name' ? customFirstLetterCapitalOfWords($color->{'name'}) : $color->{$detail});
            }
            return $array_names;
        } else {
            $color = Color::find($ids);
            return $color == null ? '' : ($detail == 'name' ? customFirstLetterCapitalOfWords($color->{'name'}) : $color->{$detail});
        }
    }
}

if (!function_exists('hasAccessOnContent')) {
    function hasAccessOnContent()
    {
        $show_all_content = BusinessSetting::where('type', 'show_all_content')->first();
        if ($show_all_content == null) {
            $show_all_content = new BusinessSetting;
            $show_all_content->type = 'show_all_content';
            $show_all_content->value = 0;
            $show_all_content->save();
        }

        // id: 9 = webline
        if ((Auth::check() && Auth::user()->id == 9) && $show_all_content->value == 1) {
            return true;
        }
        return false;
    }
}

if (!function_exists('productHasImported')) {
    function productHasImported(bool $status, $content, $else = false)
    {
        if ($status) return $content;
        return $else ?? '';
    }
}

if (!function_exists('getStrFromProductVariant')) {
    function getStrFromProductVariant($product, $product_variant, $info = 'all')
    {
        $colors_active = (count(json_decode($product->colors, true)) > 0) ? 1 : 0;
        $attributes = json_decode($product->attributes, true);
        $size_active = (count($attributes) > 0 && $attributes[0] == 1) ? 1 : 0;

        $str = '';
        switch ($info):
            case 'size':
                $str = 'size';
                break;
            case 'color':
                $str = 'color';

                break;
            case 'all':

                try {
                    if ($colors_active && $size_active) {
                        list($color, $size) = explode('-', $product_variant);
                        $str = getColorName($color) . "-" . ($product->import_from_btms ? getSizeName($size) : $size);
                    } elseif ($colors_active && !$size_active) {
                        $color_name = getColorName($product_variant);
                        $str = $color_name;
                    } elseif (!$colors_active && $size_active) {
                        $str = ($product->import_from_btms ? getSizeName($product_variant) : $product_variant);
                    }
                } catch (\exception $e) {
                    if (request()->ip() == '82.102.76.201') {
                        dd($e->getMessage(), $product, $product_variant);
                    }
                }

                break;
            default:
                abort('500', 'Function getStrFromProductVariant');
        endswitch;

        return $str;
    }
}


if (!function_exists('customFirstLetterCapitalOfWords')) {
    function customFirstLetterCapitalOfWords($str): string
    {
        $str = trim($str);
        if (ctype_upper($str)) {
            return ucwords(strtolower($str));
        } else {
            $array = preg_split('/(?=[A-Z])/', $str);
            $final_result = implode(' ', $array);
            return ucwords(strtolower($final_result));
        }
    }
}

if (!function_exists('paymentMethodName')) {
    function paymentMethodName($payment_method): string
    {
        $payment_method = trim($payment_method);
        switch ($payment_method):
            case 'viva_wallet':
                return "Viva Wallet";
            case'cash_on_delivery':
                return "Cash on Delivery";
            case 'pay_on_credit':
                return "On Credit";
            default:
                return $payment_method;
        endswitch;
    }
}
if (!function_exists('remove_vat')) {
    function remove_vat(float $price, int $vat_percentage)
    {
        return number_format(($price * 100) / ($vat_percentage + 100), '2');
    }
}
if (!function_exists('calculateCyprusPostShipping')) {
    function calculateCyprusPostShipping(string $shipping_method, int $weight, int $country_id)
    {
        $params = [];
        $endpoint = "";
        switch ($shipping_method) {
            case 'epg_parcels':
            case 'parcels':
                $endpoint = "https://cypruspost.post/api/postal-rates/parcels";
                $params['priority'] = 'A';
                $params['is_domestic_service'] = '';
                break;
            case 'ems_datapost':
                $endpoint = "https://cypruspost.post/api/postal-rates/ems-datapost";
                break;
            default:
                abort('500', 'Not valid shipping method');
        }
        $country = Country::findOrFail($country_id);
        $country_name = toUpper($country->post_name_en);

        $cost = 0;
        $status = 0;
        $message = "";
        $client = new GuzzleClient();
        $response = $client->get($endpoint,
            array_merge_recursive([
                'query' => array_merge_recursive([
                    'lng' => 'en',
                    'weight' => $weight,
                    'country' => $country_name,
                ], $params),
                RequestOptions::HEADERS => [
                    'Authorization' => config('app.cyprus_post_api_key'),
                ]
            ])
        );

        $response = getBody($response);
//        dd($response->status_code, $response->data);
        if ($response->status_code == '200') {
            if (!empty($response->data->errors)) {
                $first_key = array_key_first(get_object_vars($response->data->errors));
                $message = $response->data->errors->{$first_key}[0];
                if ($first_key == 'weight') {
                    $pattern = "/[0-9]+/";

                    preg_match_all($pattern, $message, $matches);

                    $weight = $matches[0][0] ?? null;
                    $message = preg_replace($pattern, ($weight / 1000) . "kg", $message);
                }
            } else {
                $cost = $response->data->result->postal_rate->value;
                $status = 1;
            }
        }
        if ($cost > 0) $cost += get_setting('handling_fees_for_intern_shipp', 0);

        return (object)array(
            'status' => $status,
            'cost' => $cost,
            'message' => $message
        );
    }
}

function getBody(ResponseInterface $response)
{
    /** @var stdClass|null $body */
    $body = json_decode($response->getBody(), false, 512, JSON_BIGINT_AS_STRING);
    return $body;
}

if (!function_exists('calcWeightCart')) {
    function calcWeightCart()
    {
        $total_weight = 0;
        if (Session::has('cart')) {
            foreach (Session::get('cart') as $key => $cartItem) {
                $total_weight += $cartItem['weight'] * $cartItem['quantity'];
            }
        }
        Session::put('total_weight_cart', $total_weight);
    }
}

if (!function_exists('calcVatPrice')) {
    function calcVatPrice($price)
    {
        $vat_percentage = 0;

        if (Session::has('vat')) {
            $vat_percentage = getVatFromSession('percentage');
        } else {
            if (auth()->check() && auth()->user()->country) {
                $country = Country::findOrFail(auth()->user()->country);
            } else {
                $country = Country::findOrFail(54);
            }
            setVatOnSession(((auth()->check() && auth()->user()->excluded_vat) ? 0 : $country->vat_included), $country);
            if ($country->vat_included) {
                $vat_percentage = $country->vat_percentage;
            }
        }
        return $price + ($price * ($vat_percentage / 100));
    }
}

if (!function_exists('setVatOnSession')) {
    function setVatOnSession($included, $country)
    {

        $btms_code = !empty($country->btms_vat_code) ? $country->btms_vat_code : 'Z';
        if (!$included) {
            $btms_vat = \App\Models\Btms\VatCodes::getVatCodeFromCode($btms_code);
            if ($btms_vat == null || $btms_vat->Percentage > 0) {
                $btms_code = 'Z';
            }
        }
        Session::put('vat', [
            'included' => $included,
            'country_id' => $country->id,
            'country_name' => $country->name,
            'btms_code' => $btms_code,
            'percentage' => $included ? $country->vat_percentage : 0
        ]);
    }
}
if (!function_exists('getVatFromSession')) {
    function getVatFromSession($key)
    {
        return Session::get('vat')[$key];
    }
}
if (!function_exists('updatePricesFromBtmsById')) {
    function updatePricesFromBtmsById($id)
    {
        // if you want disabled the price update in each request, uncomment the below line
//        return;
        $product = Product::findOrFail($id);
        if ($product == null) abort('404');

        if (!$product->import_from_btms) return true;

        if ($product->variant_product) {
            foreach ($product->stocks as $key => $stock) {
                $item = Items::where('Item Code', $stock->part_number)->first();
                if ($item == null) abort(404);

                $prices = $item->price(18, true, true);
                $stock->price = $prices->retail ?? $prices;
                $stock->whole_price = $prices->wholesale ?? null;
                $stock->clearance_price = $prices->clearance ?? null;
                $stock->save();
            }
        } else {
            $product_stock = $product->stocks->first();
            $item = Items::where('Item Code', $product_stock->part_number ?? $product->part_number)->first();
            if ($item == null) abort(404);

            $prices = $item->price(18, true, true);
            $product_stock->price = $prices->retail ?? $prices;
            $product_stock->whole_price = $prices->wholesale ?? null;
            $product_stock->clearance_price = $prices->clearance ?? null;
            $product_stock->save();

            $product->unit_price = $product_stock->price;
            $product->wholesale_price = $product_stock->whole_price;
            $product->clearance_price = $product_stock->clearance_price;
            $product->save();
        }
    }
}


if (!function_exists('priceFormatForBtms')) {
    function priceFormatForBtms($price)
    {
        return number_format($price, 2);
    }
}

if (!function_exists('weightConvertToKg')) {
    function weightConvertToKg($weight)
    {
        return number_format($weight / 1000, 2);
    }
}
if (!function_exists('debug_dd')) {
    function debug_dd(...$vars)
    {
        if (request()->ip() == '82.102.76.201') {
            dd($vars);
        }
    }
}

if (!function_exists('getSizeName')) {
    function getSizeName($size_id)
    {
        $size = \App\Size::find($size_id);
        if ($size == null) return null;
        return $size->btms_size_name ?? $size->btms_size_code;
    }
}

if (!function_exists('getSize')) {
    function getSize($size_id)
    {
        $size = \App\Size::where('btms_size_id', $size_id)->first();
        if ($size == null) {
            return null;
        }
        return $size;
    }
}

if (!function_exists('sortSizes')) {
    function sortSizes($sizes)
    {
        $sorted_sizes = [];

        foreach ($sizes as $size) {
            $get_size = getSize($size);
            $sorted_sizes[$get_size->sort ?? 999999] = [
                'btms_id' => $get_size->btms_size_id ?? $size,
                'code' => $get_size->btms_size_code ?? $size,
                'name' => $get_size->btms_size_name ?? $size
            ];
        }
        ksort($sorted_sizes);

        return $sorted_sizes;
    }
}


if (!function_exists('updateVatOnSession')) {
    function updateVatOnSession($country_id)
    {
        $country = Country::find($country_id);
        setVatOnSession(((auth()->check() && auth()->user()->excluded_vat) ? 0 : $country->vat_included), $country);
    }
}


if (!function_exists('getQuery')) {
    function getQuery($query, $data_bindings)
    {
        foreach ($data_bindings as $data) {
            $pos = strpos($query, "?");
            if (is_string($data)) {
                $data = "'$data'";
            }
            $query = substr_replace($query, $data, $pos, 1);
        }
        return $query;
    }
}

if (!function_exists('isWebline')) {
    function isWebline($check_ip = true)
    {
        if (Auth::check() && Auth::user()->id == 9) {
            if ($check_ip) {
                if (request()->ip() != '82.102.76.201') {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}

if (!function_exists('disableInputForBtmsProducts')) {
    function disableInputForBtmsProducts(Product $product, string $attr = "readonly")
    {
        if (!empty($product)) {
            if ($product->import_from_btms) return $attr;
        }
        return "";
    }
}

function removeFromCart($key)
{
    if (Session::has('cart') && count(Session::get('cart')) > 0) {
        $cart = Session::get('cart', collect([]));
        $cart->forget($key);
        Session::put('cart', $cart);
    }
    calcWeightCart();
    Cart::refreshCartInDB();
}

if (!function_exists('getSelectedCheckoutType')) {
    function getSelectedCheckoutType()
    {
        $checkoutType = null;
        $shipping_info = Session::get('shipping_info');
        if ($shipping_info != null) {
            $checkoutType = $shipping_info['checkout_type'] ?? null;
        }

        return $checkoutType;
    }
}

if (!function_exists('makeCombinations')) {
    function makeCombinations($arrays)
    {
        $result = array(array());
        foreach ($arrays as $property => $property_values) {
            $tmp = array();
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, array($property => $property_value));
                }
            }
            $result = $tmp;
        }
        return $result;
    }
}

if (!function_exists('create_custom_log_file')) {
    function create_custom_log_file($filename, $data)
    {

        if (is_array($data)) {
            $data = print_r($data, true);
        }
        $data .= PHP_EOL;
        $data .= "----------------------------------------------------------------------------";
        $data .= PHP_EOL . PHP_EOL;
        $fp = fopen($filename, 'a+');
        fwrite($fp, $data);
        fclose($fp);
    }
}

if (!function_exists('clear_html_enities')) {
    function clear_html_enities($text, $limit = null)
    {
        $text = strip_tags($text);
        if(!empty($text) && $limit != null) {
            $text = substr($text,0,$limit).'...';
        }
        return $text;
    }
}
?>
