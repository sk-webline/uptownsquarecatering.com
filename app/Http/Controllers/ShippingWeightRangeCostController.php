<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ShippingWeightRangeCost;

class ShippingWeightRangeCostController extends Controller
{

    public function shipping_weight_range_cost_update(Request $request){

        foreach (ShippingWeightRangeCost::all() as $key => $shipping_cost) {
            $shipping_cost->delete();
        }

        if($request->has('price')) {
            foreach ($request->price as $key => $price) {
                $shipping_cost = new ShippingWeightRangeCost;
                $shipping_cost->from = $request['from'][$key];
                $shipping_cost->to = $request['to'][$key];
                $shipping_cost->price = $price;
                $shipping_cost->save();
            }
        }


        return back();
    }
}
