<?php

namespace App\Http\Controllers;

use App\Store;
use Illuminate\Http\Request;
use App\Order;
use Illuminate\Support\Facades\Artisan;
use Session;
use PDF;
use Auth;
use Config;

class InvoiceController extends Controller
{
    //download invoice
    public function invoice_download($id)
    {

        ini_set('memory_limit', '256M');
        Artisan::call('view:clear');
        if(Session::has('currency_code')){
            $currency_code = Session::get('currency_code');
        }
        else{
            $currency_code = \App\Currency::findOrFail(get_setting('system_default_currency'))->code;
        }
        $language_code = Session::get('locale', Config::get('app.locale'));

        if(\App\Language::where('code', $language_code)->first()->rtl == 1){
            $direction = 'rtl';
            $text_align = 'right';
            $not_text_align = 'left';
        }else{
            $direction = 'ltr';
            $text_align = 'left';
            $not_text_align = 'right';
        }

        if($currency_code == 'BDT' || $language_code == 'bd'){
            // bengali font
            $font_family = "'Hind Siliguri','sans-serif'";
        }elseif($currency_code == 'KHR' || $language_code == 'kh'){
            // khmer font
            $font_family = "'Hanuman','sans-serif'";
        }elseif($currency_code == 'AMD'){
            // Armenia font
            $font_family = "'arnamu','sans-serif'";
        }elseif($currency_code == 'ILS'){
            // Israeli font
            $font_family = "'Varela Round','sans-serif'";
        }elseif($currency_code == 'AED' || $currency_code == 'EGP' || $language_code == 'sa'){
            // middle east/arabic font
            $font_family = "'XBRiyaz','sans-serif'";
        }else{
            // general for all
            $font_family = "'Roboto','sans-serif'";
        }
        $store =  Store::where('show_contact', '1')->first();
        $order = Order::findOrFail($id);

        /*return view('backend.invoices.invoice', [
            'order' => $order,
            'font_family' => $font_family,
            'direction' => $direction,
            'text_align' => $text_align,
            'not_text_align' => $not_text_align,
            'store' => $store
        ]);*/

//        return view('backend.invoices.invoice', [
//            'order' => $order,
//            'font_family' => $font_family,
//            'direction' => $direction,
//            'text_align' => $text_align,
//            'not_text_align' => $not_text_align,
//            'store' => $store
//        ], [], []);
        return PDF::loadView('backend.invoices.invoice', [
            'order' => $order,
            'font_family' => $font_family,
            'direction' => $direction,
            'text_align' => $text_align,
            'not_text_align' => $not_text_align,
            'store' => $store
        ], [], [])->download('order-' . $order->code . '.pdf');
    }
}
