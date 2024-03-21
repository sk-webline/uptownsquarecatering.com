<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Btms\Items;

class BtmsController extends Controller
{
    public function get_item(Request $request, $item_code) {
        $item = Items::where('Company Code', config('btms.company_code'))->where('Item Code', $item_code)->first();
        if ($item == null) abort(404);
        // Barcode
        //        dd($item, $item->barcode());

        // Price
        dd($item, $item->price(), $item->WeightInKilos);
    }
}
