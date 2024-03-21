<?php

namespace App\Http\Controllers;

use App\Models\CanteenExtraDay;
use App\Models\CanteenLanguage;
use App\Models\CanteenMenu;
use App\Models\CanteenProduct;
use App\Models\CanteenProductCategory;
use App\Models\CanteenProductTranslation;
use App\Models\CanteenSetting;
use App\Models\OrganisationBreak;
use App\Product;
use App\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use App\Models\Organisation;

class CanteenMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $canteen_setting_id)
    {

        $canteen_setting = CanteenSetting::find($canteen_setting_id);

        $menu = $canteen_setting->canteen_menus;

        $business_days = json_decode($canteen_setting->working_week_days);

        $breaks = $canteen_setting->breaks;


        return view('backend.organisation.canteen.canteen_settings.canteen_menu.index', compact('canteen_setting', 'menu', 'business_days', 'breaks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($canteen_setting_id)
    {

        $canteen_setting = CanteenSetting::find($canteen_setting_id);

        $breaks = OrganisationBreak::where('canteen_setting_id', $canteen_setting->id)->orderBy('hour_from')->get();

//        $menus = $canteen_setting->canteen_menus;

        return view('backend.organisation.canteen.canteen_settings.canteen_menu.create', compact('canteen_setting', 'breaks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $canteen_setting_id)
    {
        $canteen_setting = CanteenSetting::find($canteen_setting_id);

        if ($canteen_setting == null) {
            flash(translate('Sorry! Something went wrong!'))->error();
            return back();
        }

        $business_days = json_decode($canteen_setting->working_week_days);
        $breaks = $canteen_setting->breaks;

        $selected_product_ids = explode(',', $request->products);

        // products which are going to be in the menu
        $products = CanteenProduct::whereIn('id', $selected_product_ids)->get();

        foreach ($products as $key => $product) {

            $custom_price_status = 0;
            $custom_price = 0;
            $custom_price_status_key = 'custom_price_status_' . $product->id;

            if ($request->has($custom_price_status_key)) {
                $custom_price_status = 1;
                $custom_price_key = 'custom_price_' . $product->id;
                $custom_price = $request->{$custom_price_key};
            }

            foreach ($business_days as $day_key => $day) {

                foreach ($breaks as $break_key => $break) {

                    $request_key = 'product_' . $product->id . '_day_' . $day . '_break_' . $break->id;  // product_4_day_Tue_break_48

                    if ($request->has($request_key)) {

                        $menu_addition = new CanteenMenu();
                        $menu_addition->canteen_setting_id = $canteen_setting->id;
                        $menu_addition->canteen_product_id = $product->id;
                        $menu_addition->organisation_break_id = $break->id;
                        $menu_addition->organisation_break_num = $break->break_num;
                        $menu_addition->day = strtolower(day_name($day));
                        $menu_addition->custom_price_status = $custom_price_status;
                        $menu_addition->custom_price = $custom_price;

                        $menu_addition->save();

                    }
                }
            }

        }

        $menu = CanteenMenu::where('canteen_setting_id', $canteen_setting_id)->get();

        flash(translate('Canteen Menu created successfully!'))->success();
        return view('backend.organisation.canteen.canteen_settings.canteen_menu.index', compact('canteen_setting', 'menu'));


    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $canteen_setting_id)
    {

        $search = null;
        $category_filter = null;

        $canteen_setting = CanteenSetting::find($canteen_setting_id);

        $breaks = OrganisationBreak::where('canteen_setting_id', $canteen_setting->id)->orderBy('break_num')->get();

        $menus = $canteen_setting->canteen_menus;

        $products = CanteenProduct::where('status', 1);

        if ($request->reset == null) {

            if ($request->has('search') && $request->search != null) {
                $search = $request->search;
                $products = $products->where('canteen_products.name', 'like', '%' . $search . '%');

            }

            if ($request->has('category_filter') && $request->category_filter != null) {
                $products = $products->where('canteen_products.canteen_product_category_id', '=', $request->category_filter);
                $category_filter = $request->category_filter;
            }

        }

        $products = $products->paginate(100);

        return view('backend.organisation.canteen.canteen_settings.canteen_menu.edit', compact('canteen_setting', 'breaks', 'menus', 'products', 'category_filter', 'search'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $canteen_setting_id)
    {

        $canteen_setting = CanteenSetting::find($canteen_setting_id);

        if ($canteen_setting == null) {
            flash(translate('Sorry! Something went wrong!'))->error();
            return back();
        }

        //delete old menu

        $old_menu_count = CanteenMenu::where('canteen_setting_id', $canteen_setting->id)->count();
        if ($old_menu_count > 0 && CanteenMenu::where('canteen_setting_id', $canteen_setting->id)->delete()) {

        } else {
            flash(translate('Sorry! Something went wrong!'))->error();
            return back();
        }

        $business_days = json_decode($canteen_setting->working_week_days);
        $breaks = $canteen_setting->breaks;

        $selected_product_ids = explode(',', $request->products);

        // products which are going to be in the menu
        $products = CanteenProduct::whereIn('id', $selected_product_ids)->get();

        foreach ($products as $key => $product) {

            $custom_price_status = 0;
            $custom_price = 0;
            $custom_price_status_key = 'custom_price_status_' . $product->id;

            if ($request->has($custom_price_status_key)) {
                $custom_price_status = 1;
                $custom_price_key = 'custom_price_' . $product->id;
                $custom_price = $request->{$custom_price_key};
            }

            foreach ($business_days as $day_key => $day) {

                foreach ($breaks as $break_key => $break) {

                    $request_key = 'product_' . $product->id . '_day_' . $day . '_break_' . $break->id;  // product_4_day_Tue_break_48

                    if ($request->has($request_key)) {

                        $menu_addition = new CanteenMenu();
                        $menu_addition->canteen_setting_id = $canteen_setting->id;
                        $menu_addition->canteen_product_id = $product->id;
                        $menu_addition->organisation_break_id = $break->id;
                        $menu_addition->organisation_break_num = $break->break_num;
                        $menu_addition->day = strtolower(day_name($day));
                        $menu_addition->custom_price_status = $custom_price_status;
                        $menu_addition->custom_price = $custom_price;

                        $menu_addition->save();

                    }
                }
            }

        }

        $menu = CanteenMenu::where('canteen_setting_id', $canteen_setting_id)->get();

        flash(translate('Canteen Menu created successfully!'))->success();
        return view('backend.organisation.canteen.canteen_settings.canteen_menu.index', compact('canteen_setting', 'menu'));


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


    }

    public function change_canteen_menu_ajax(Request $request)
    {

        $canteen_setting = CanteenSetting::find($request->canteen_setting_id);
        $product = CanteenProduct::find($request->product_id);

        if ($canteen_setting == null) {
            return response()->json(['status' => 0, 'msg' => translate('Canteen Setting not found')]);
        }

        if ($product == null) {
            return response()->json(['status' => 0, 'msg' => translate('Product not found')]);
        }

        $break_num = $request->break_num;
        $break = OrganisationBreak::where('break_num', $break_num)->where('canteen_setting_id', $canteen_setting->id)->first();

        if ($break == null) {
            return response()->json(['status' => 0, 'msg' => translate('Break not found')]);
        }


        if ($request->change == 'insert') {

            $menu_addition = new CanteenMenu();
            $menu_addition->canteen_setting_id = $request->canteen_setting_id;
            $menu_addition->canteen_product_id = $request->product_id;
            $menu_addition->organisation_break_id = $break->id;
            $menu_addition->organisation_break_num = $break->break_num;
            $menu_addition->day = strtolower(day_name($request->day));
            $menu_addition->custom_price_status = $request->custom_price_status;
            $menu_addition->custom_price = $request->custom_price;

            if ($menu_addition->save()) {

                CanteenMenu::where('canteen_setting_id', $request->canteen_setting_id)
                    ->where('canteen_product_id', '=', $request->product_id)
                    ->update(['custom_price_status' => $request->custom_price_status, 'custom_price' => $request->custom_price]);

                return response()->json(['status' => 1, 'msg' => translate('Successfully added')]);
            }


        } elseif ($request->change == 'delete') {

            $menu = CanteenMenu::where('canteen_setting_id', $request->canteen_setting_id)
                ->where('canteen_product_id', '=', $request->product_id)
                ->where('day', '=', strtolower(day_name($request->day)))
                ->where('organisation_break_num', '=', $break->break_num)->first();

            if ($menu != null) {
                if (CanteenMenu::where('canteen_setting_id', $request->canteen_setting_id)
                    ->where('canteen_product_id', '=', $request->product_id)
                    ->where('day', '=', strtolower(day_name($request->day)))
                    ->where('organisation_break_num', '=', $break->break_num)->delete()) {
                    return response()->json(['status' => 1, 'msg' => translate('Successfully removed')]);

                }
            }

        }

        return response()->json(['status' => 0, 'msg' => translate('Sorry! Something went wrong!')]);


    }

    public function update_custom_price(Request $request, $canteen_setting_id)
    {

        $canteen_setting = CanteenSetting::find($canteen_setting_id);

        if ($canteen_setting == null) {
            flash(translate('Sorry! Something went wrong!'))->error();
            return back();
        }

        $selected_product_ids = explode(',', $request->products);

        // products which are going to be in the menu
        $products = CanteenProduct::whereIn('id', $selected_product_ids)->get();

        foreach ($products as $key => $product) {

            $custom_price_status = 0;
            $custom_price = 0;
            $custom_price_status_key = 'custom_price_status_' . $product->id;

            if ($request->has($custom_price_status_key)) {
                $custom_price_status = 1;
                $custom_price_key = 'custom_price_' . $product->id;
                $custom_price = $request->{$custom_price_key};
            }

            if (CanteenMenu::where('canteen_setting_id', $canteen_setting->id)
                    ->where('canteen_product_id', '=', $product->id)
                    ->update(['custom_price_status' => $custom_price_status, 'custom_price' => $custom_price]) == false) {

                flash(translate('Sorry! Something went wrong!'))->error();
                return back();
            }

        }

        flash(translate('Canteen Menu created successfully!'))->success();
        return back();

    }

    public function change_canteen_menu_ajax_all(Request $request)
    {

        $canteen_setting = CanteenSetting::find($request->canteen_setting_id);
        $product = CanteenProduct::find($request->product_id);

        if ($canteen_setting == null) {
            return response()->json(['status' => 0, 'msg' => translate('Canteen Setting not found')]);
        }

        if ($product == null) {
            return response()->json(['status' => 0, 'msg' => translate('Product not found')]);
        }

        $start_of_week = Carbon::today()->startOfWeek();
        $end_of_week = Carbon::today()->endOfWeek();
        $days = [];

        // Loop through each day of the week
        for ($date = $start_of_week; $date->lte($end_of_week); $date->addDay()) {
            $days[] = $date->format('D');
        }


        if ($request->change == 'select_all') {

//            $business_days = json_decode($canteen_setting->working_week_days);
            $breaks = $canteen_setting->breaks;

            foreach ($days as $day_key => $day) {

                foreach ($breaks as $break_key => $break) {

                    $menu_addition = CanteenMenu::where('canteen_setting_id', '=', $request->canteen_setting_id)
                        ->where('canteen_product_id', '=', $request->product_id)
                        ->where('organisation_break_num', '=', $break->break_num)
                        ->where('day', '=', strtolower(day_name($day)))->first();

                    if ($menu_addition == null) {
                        $menu_addition = new CanteenMenu();
                        $menu_addition->canteen_setting_id = $request->canteen_setting_id;
                        $menu_addition->canteen_product_id = $request->product_id;
                        $menu_addition->organisation_break_id = $break->id;
                        $menu_addition->organisation_break_num = $break->break_num;
                        $menu_addition->day = strtolower(day_name($day));
                    }

                    $menu_addition->custom_price_status = $request->custom_price_status;
                    $menu_addition->custom_price = $request->custom_price;

                    $menu_addition->save();

                    if ($menu_addition->save() == false) {
                        return response()->json(['status' => 0, 'msg' => translate('Sorry! Something went wrong!'), 'change' => $request->change]);
                    }

                }
            }

            return response()->json(['status' => 1, 'msg' => translate('Successfully added all'), 'change' => $request->change]);


        } elseif ($request->change == 'delete_all') {

            if (CanteenMenu::where('canteen_setting_id', $request->canteen_setting_id)
                ->where('canteen_product_id', '=', $request->product_id)->delete()) {
                return response()->json(['status' => 1, 'msg' => translate('Successfully deleted all'), 'change' => $request->change]);
            }

        }

        return response()->json(['status' => 0, 'msg' => translate('Sorry! Something went wrong!'), 'change' => $request->change]);


    }

    public function ajax_change_custom_price(Request $request)
    {

        $canteen_setting = CanteenSetting::find($request->canteen_setting_id);
        $product = CanteenProduct::find($request->product_id);


        if ($canteen_setting == null) {
            return response()->json(['status' => 0, 'msg' => translate('Canteen Setting not found')]);
        }

        if ($product == null) {
            return response()->json(['status' => 0, 'msg' => translate('Product not found')]);
        }

        if (CanteenMenu::where('canteen_setting_id', $canteen_setting->id)
            ->where('canteen_product_id', '=', $product->id)
            ->update(['custom_price_status' => $request->custom_price_status, 'custom_price' => $request->custom_price])) {

            return response()->json(['status' => 1, 'msg' => translate('Successfully custom price update')]);
        }

        return response()->json(['status' => 0, 'msg' => translate('Sorry! Something went wrong!')]);

    }

    public function ajax_delete_custom_price(Request $request)
    {

        $canteen_setting = CanteenSetting::find($request->canteen_setting_id);
        $product = CanteenProduct::find($request->product_id);


        if ($canteen_setting == null) {
            return response()->json(['status' => 0, 'msg' => translate('Canteen Setting not found')]);
        }

        if ($product == null) {
            return response()->json(['status' => 0, 'msg' => translate('Product not found')]);
        }

        if (CanteenMenu::where('canteen_setting_id', $canteen_setting->id)
            ->where('canteen_product_id', '=', $product->id)
            ->update(['custom_price_status' => '0', 'custom_price' => '0'])) {

            return response()->json(['status' => 1, 'msg' => translate('Successfully custom price update')]);
        }

        return response()->json(['status' => 0, 'msg' => translate('Sorry! Something went wrong!')]);

    }

    public function select_all_products_of_page(Request $request)
    {

        $canteen_setting = CanteenSetting::find($request->canteen_setting_id);

        if ($canteen_setting == null) {
            return response()->json(['status' => 0, 'msg' => translate('Canteen Setting not found')]);
        }

        $product_ids = json_decode($request->page_products);
        $products = CanteenProduct::whereIn('id', $product_ids)->get();

        $business_days = json_decode($canteen_setting->working_week_days);
        $breaks = $canteen_setting->breaks;

        foreach ($products as $key => $product) {

            $custom_price_status = 0;
            $custom_price = 0;

            $existing_menu = CanteenMenu::where('canteen_setting_id', $canteen_setting->id)
                ->where('canteen_product_id', '=', $product->id)->first();

            if ($existing_menu != null) {
                $custom_price_status = $existing_menu->custom_price_status;
                $custom_price = $existing_menu->custom_price;
            }

            foreach ($business_days as $day_key => $day) {

                foreach ($breaks as $break_key => $break) {

                    $menu_addition = CanteenMenu::where('canteen_setting_id', '=', $canteen_setting->id)
                        ->where('canteen_product_id', '=', $product->id)
                        ->where('organisation_break_num', '=', $break->break_num)
                        ->where('day', '=', strtolower(day_name($day)))->first();

                    if ($menu_addition == null) {
                        $menu_addition = new CanteenMenu();
                        $menu_addition->canteen_setting_id = $canteen_setting->id;
                        $menu_addition->canteen_product_id = $product->id;
                        $menu_addition->organisation_break_id = $break->id;
                        $menu_addition->organisation_break_num = $break->break_num;
                        $menu_addition->day = strtolower(day_name($day));
                    }

                    $menu_addition->custom_price_status = $custom_price_status;
                    $menu_addition->custom_price = $custom_price;

                    $menu_addition->save();

                    if ($menu_addition->save() == false) {
                        return response()->json(['status' => 0, 'msg' => translate('Sorry! Something went wrong!')]);
                    }

                }
            }


        }

        $menus = $canteen_setting->canteen_menus;
        $search = $request->search;
        $category_filter = $request->category_filter;
        $products = CanteenProduct::whereIn('id', $product_ids)->paginate(100);

        return response()->json(['status' => 1, 'view' => view('backend.organisation.canteen.canteen_settings.canteen_menu.update_menu_table', compact('canteen_setting', 'menus',
            'products', 'breaks', 'search', 'category_filter'))->render(), 'msg' => translate('Successfully selected all')]);


    }


    public function select_all_products_of_filters(Request $request)
    {

        $canteen_setting = CanteenSetting::find($request->canteen_setting_id);

        if ($canteen_setting == null) {
            return response()->json(['status' => 0, 'msg' => translate('Canteen Setting not found')]);
        }

        $search = $request->search;
        $category_filter = $request->category_filter;

        $products = CanteenProduct::where('status', 1);


        if ($request->has('search') && $request->search != null) {
            $search = $request->search;
            $products = $products->where('canteen_products.name', 'like', '%' . $search . '%');

        }

        if ($request->has('category_filter') && $request->category_filter != null) {
            $products = $products->where('canteen_products.canteen_product_category_id', '=', $request->category_filter);
            $category_filter = $request->category_filter;
        }

        $business_days = json_decode($canteen_setting->working_week_days);
        $breaks = $canteen_setting->breaks;

        foreach ($products->get() as $key => $product) {

            $custom_price_status = 0;
            $custom_price = 0;

            $existing_menu = CanteenMenu::where('canteen_setting_id', $canteen_setting->id)
                ->where('canteen_product_id', '=', $product->id)->first();

            if ($existing_menu != null) {
                $custom_price_status = $existing_menu->custom_price_status;
                $custom_price = $existing_menu->custom_price;
            }

            foreach ($business_days as $day_key => $day) {

                foreach ($breaks as $break_key => $break) {

                    $menu_addition = CanteenMenu::where('canteen_setting_id', '=', $canteen_setting->id)
                        ->where('canteen_product_id', '=', $product->id)
                        ->where('organisation_break_num', '=', $break->break_num)
                        ->where('day', '=', strtolower(day_name($day)))->first();

                    if ($menu_addition == null) {
                        $menu_addition = new CanteenMenu();
                        $menu_addition->canteen_setting_id = $canteen_setting->id;
                        $menu_addition->canteen_product_id = $product->id;
                        $menu_addition->organisation_break_id = $break->id;
                        $menu_addition->organisation_break_num = $break->break_num;
                        $menu_addition->day = strtolower(day_name($day));
                    }

                    $menu_addition->custom_price_status = $custom_price_status;
                    $menu_addition->custom_price = $custom_price;

                    $menu_addition->save();

                    if ($menu_addition->save() == false) {
                        return response()->json(['status' => 0, 'msg' => translate('Sorry! Something went wrong!')]);
                    }

                }
            }


        }

        $menus = $canteen_setting->canteen_menus;
        $products = $products->paginate(100);

        return response()->json(['status' => 1, 'view' => view('backend.organisation.canteen.canteen_settings.canteen_menu.update_menu_table', compact('canteen_setting', 'menus',
            'products', 'breaks', 'search', 'category_filter'))->render(), 'msg' => translate('Successfully selected all')]);


    }


}
