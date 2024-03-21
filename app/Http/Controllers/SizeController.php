<?php

namespace App\Http\Controllers;

use App\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sizes = Size::orderBy('sort')->get();
        return view('backend.product.sizes.index', compact('sizes'));
    }

    public function update_order(Request $request)
    {

        if ($request->has('sort_sizes')) {
            foreach ($request->sort_sizes as $key => $sort_size) {
                $size = Size::find($sort_size);
                if ($size != null) {
                    $size->sort = $key+1;
                    $size->save();
                }
            }
        }

        return redirect()->route('sizes.index');
    }
}
