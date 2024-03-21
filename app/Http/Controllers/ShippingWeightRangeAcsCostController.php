<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ShippingWeightRangeAcsCost;

class ShippingWeightRangeAcsCostController extends Controller
{

    public function shipping_weight_range_cost_acs_update(Request $request){

        foreach (ShippingWeightRangeAcsCost::all() as $key => $shipping_cost) {
            $shipping_cost->delete();
        }

        if($request->has('price')) {
            foreach ($request->price as $key => $price) {
                $shipping_cost = new ShippingWeightRangeAcsCost;
                $shipping_cost->from = $request['from'][$key];
                $shipping_cost->to = $request['to'][$key];
                $shipping_cost->price = $price;
                $shipping_cost->save();
            }
        }


        return back();
    }
}
