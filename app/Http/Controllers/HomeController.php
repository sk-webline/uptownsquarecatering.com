<?php

namespace App\Http\Controllers;

use App\Http\Controllers\SearchController;
use App\Mail\InvoiceEmailManager;
use App\Models\AppOrder;
use App\Models\AppRefundDetail;
use App\Models\CanteenAppUser;
use App\Models\Cart;
use App\Models\EmailForOrder;
use App\Models\Order;
use App\Models\Card;
use App\Models\CateringPlanPurchase;
use App\Models\Organisation;
use App\Models\PlatformSetting;
use App\ProductType;
use App\Store;
use Carbon\Carbon;
use Carbon\Exceptions\NotACarbonClassException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Util\Type;
use Session;
use Auth;
use Hash;
use App\Category;
use App\FlashDeal;
use App\Brand;
use App\Product;
use App\PickupPoint;
use App\CustomerPackage;
use App\CustomerProduct;
use App\User;
use App\Seller;
use App\Shop;
use App\Color;
use App\Page;
use App\BusinessSetting;
use App\Country;
use App\PartnershipUser;

//use App\Http\Controllers\SearchController;
use Artisan;
use ImageOptimizer;
use Cookie;
use Illuminate\Support\Str;
use App\Mail\SecondEmailVerifyMailManager;
use Mail;
use App\Mail\ContactMailManager;
use App\Mail\AccountClosureMailManager;
use App\Mail\ProductRequestMailManager;
use App\Mail\UsedProductRequestMailManager;
use App\Mail\PartnershipRequestMailManager;
use App\Mail\SparePartsMailManager;
use App\Utility\TranslationUtility;
use App\Utility\CategoryUtility;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Btms\Customers as BtmsCustomers;
use Illuminate\Support\Facades\Session as FacadesSession;

use PDF;
use Config;

class HomeController extends Controller
{
    protected array $redis_url_scan = [];
    public function sessions()
    {
        if ($_SERVER['REMOTE_ADDR'] == '82.102.76.201' || request()->ip() == '127.0.0.1') {
            dd(Session::all());
        }

        abort(403);
    }

    public function clear_cache(Request $request)
    {
        if ($request->ip() == '82.102.76.201') {
            // Production
            Artisan::call('optimize:clear');
//            Artisan::call('optimize');
            Artisan::call('config:clear');

            // Development
//            Artisan::call('optimize:clear');

            flash("Cache has been updated successfully!")->success();
            return redirect()->back();
        }
        abort(403);
    }

    public function refresh_csrf()
    {
        return csrf_token();
    }

    public function sitemap()
    {
        return base_path('sitemap.xml');
    }

    public function coming_soon()
    {
        return view('coming_soon');
    }

    public function redis_data($search_key = '*')
    {
        if ($search_key != '*') {
            $search_key = "*$search_key*";
        }
        $data = [];
        foreach (Redis::keys($search_key) as $key) {
            $data[$key] = Redis::get($key);
        }
        dd('redis', array_slice($data, 0, 10000));
    }

    public function refresh_cache()
    {
        debugbar()->disable();

        set_time_limit(0);
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 7200);

        Artisan::call('cache:clear');
        Redis::flushDB(); // Διαγραφή όλων στοιχείων της βάσης

//        $this->redis_url_scan[] = route('home');
//        $urlContent = file_get_contents(route('home'));
//        $this->analyzeHtmlPage($urlContent);

//        dd($this->redis_url_scan);
        return PHP_EOL . PHP_EOL . '******************************************************************' . PHP_EOL .
            '********************** Completed Successfully ********************' . PHP_EOL .
            '******************************************************************' . PHP_EOL . PHP_EOL;
    }

    private function analyzeHtmlPage($urlContent)
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($urlContent);
        $xpath = new \DOMXPath($dom);
        $hrefs = $xpath->evaluate("/html/body//a");

        for ($i = 0; $i < $hrefs->length; $i++) {
            $href = $hrefs->item($i);
            $url = $href->getAttribute('href');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            // validate url
            if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
                if (strpos($url, "uptownsquarecatering.com") !== FALSE) {
                    try {
                        if (!in_array($url, $this->redis_url_scan)) {
                            $this->redis_url_scan[] = $url;
                            $content = file_get_contents($url);
                            $this->analyzeHtmlPage($content);
                        }
                    } catch (\Exception $e) {
                        echo '<a href="' . $url . '">' . $url . '</a> ' . $e->getMessage() . '<br />';
                    }
                }
            }
        }
    }


    public function login()
    {

        if (Auth::check() && Auth::user()->user_type == 'customer') {
            return redirect()->route('dashboard');
        } else if (Auth::check() && Auth::user()->user_type == 'cashier') {

            if (auth()->user()->active == 1) {
                return redirect()->route('cashier.select_location');
            } else {
                return redirect()->route('logout');
            }

        }elseif(Auth::check() && Auth::user()->user_type == 'canteen_cashier') {
            if (auth()->user()->active == 1) {
                return redirect()->route('canteen_cashier.select_location');
            } else {
                return redirect()->route('logout');
            }
        }

        return view('frontend.user_login');
    }

    public function login_cashier()
    {
        if (Auth::check()){
            if(Auth::user()->user_type == 'cashier') {
                return redirect()->route('cashier.select_location');
            }else{
                return redirect()->route('logout');
            }
        }

        return view('frontend.cashier_login');
    }


    public function select_location()
    {

        $organisations = Organisation::join('organisation_cashiers', 'organisations.id', '=', 'organisation_cashiers.organisation_id')
            ->where('organisation_cashiers.user_id', '=', Auth::user()->id)->where('organisation_cashiers.deleted_at', '=', null)
            ->select('organisations.id', 'organisations.name')->get();

//        return $organisations;

        return view('frontend.user.cashier.select_location', compact('organisations'));
    }

    public function location_selection(Request $request)
    {

        if ($request->organisation_id != null && $request->organisation_id != '') {
            if ($request->location_id != null && $request->location_id != '') {
                $request->session()->put('organisation_id', $request->organisation_id);
                $request->session()->put('location_id', $request->location_id);

                $cancel_minutes = PlatformSetting::where('type', 'minutes_for_cancel_meals')->first()->value;

                $request->session()->put('cancel_minutes', $cancel_minutes);

                return redirect()->route('cashier.dashboard');
            }
        }

        flash(translate('Please select Organisation and Location!'))->error();

        return redirect()->back();

    }

    public function registration(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        if ($request->has('referral_code') &&
            \App\Addon::where('unique_identifier', 'affiliate_system')->first() != null &&
            \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {

            try {
                $affiliate_validation_time = \App\AffiliateConfig::where('type', 'validation_time')->first();
                $cookie_minute = 30 * 24;
                if ($affiliate_validation_time) {
                    $cookie_minute = $affiliate_validation_time->value * 60;
                }

                Cookie::queue('referral_code', $request->referral_code, $cookie_minute);
                $referred_by_user = User::where('referral_code', $request->product_referral_code)->first();

                $affiliateController = new AffiliateController;
                $affiliateController->processAffiliateStats($referred_by_user->id, 1, 0, 0, 0);
            } catch (\Exception $e) {

            }
        }
        return view('frontend.user_registration');
    }

    public function cart_login(Request $request)
    {
        $user = User::whereIn('user_type', ['customer', 'seller'])->where('email', $request->email)->orWhere('phone', $request->email)->first();
        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {
                if ($request->has('remember')) {
                    auth()->login($user, true);
                } else {
                    auth()->login($user, false);
                }

                $country = auth()->user()->get_country;
                setVatOnSession((auth()->user()->excluded_vat ? 0 : $country->vat_included), $country);

                Cart::mergeSessionAndDBProductsCart();
            } else {
                flash(translate('Invalid email or password!'))->error();
            }
        } else {
            flash(translate('Invalid email or password!'))->error();
        }
        return back();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin_dashboard()
    {
        return view('backend.dashboard');
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function cashier_dashboard(Request $request)
    {

        if ($request->session()->has('today_plan')) {
            $request->session()->forget('today_plan');
        }

        return view('frontend.user.cashier.dashboard');
    }

    /**
     * Show the customer/seller dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        if (Auth::user()->user_type == 'seller') {
            return view('frontend.user.seller.dashboard');
        } elseif (Auth::user()->user_type == 'customer') {

            $cards = Card::where('user_id', Auth::user()->id)->get();

            return view('frontend.user.customer.dashboard', compact('cards'));

        } elseif (Auth::user()->user_type == 'cashier') {

            return redirect()->route('cashier.dashboard');
        } elseif (Auth::user()->user_type == 'admin') {

            return redirect()->route('admin.dashboard');
        } else {
            abort(404);
        }
    }

    public function profile(Request $request)
    {
        if (Auth::user()->user_type == 'customer') {
            return view('frontend.user.customer.profile');
        } elseif (Auth::user()->user_type == 'seller') {
            return view('frontend.user.seller.profile');
        }
    }

    public function customer_update_profile(Request $request)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('Sorry! the action is not permitted in demo '))->error();
            return back();
        }

        $user = Auth::user();

        if ($user->email != $request->email) {
            $request->validate([
                'name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'email' => 'email|unique:users,email'
            ]);

        } else {
            $request->validate([
                'name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
            ]);
        }

        $user->name = $request->name . ' ' . $request->surname;
        $user->email = $request->email;

        if ($request->password != null) {

            $request->validate([
                'password' => 'confirmed'
            ]);

            $user->password = Hash::make($request->password);

        }

//        $user->avatar_original = $request->photo;

        if ($user->save()) {
            flash(translate('Your Profile has been updated successfully!'))->success();
        } else {
            flash(translate('Sorry! Something went wrong.'))->error();
        }


        return back();
    }


    public function seller_update_profile(Request $request)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('Sorry! the action is not permitted in demo '))->error();
            return back();
        }

        $user = Auth::user();
        $user->name = $request->name;
        $user->address = $request->address;
        $user->country = $request->country;
        $user->city = $request->city;
        $user->postal_code = $request->postal_code;
        $user->phone = $request->phone;

        if ($request->new_password != null && ($request->new_password == $request->confirm_password)) {
            $user->password = Hash::make($request->new_password);
        }
        $user->avatar_original = $request->photo;

        $seller = $user->seller;
        $seller->cash_on_delivery_status = $request->cash_on_delivery_status;
        $seller->bank_payment_status = $request->bank_payment_status;
        $seller->bank_name = $request->bank_name;
        $seller->bank_acc_name = $request->bank_acc_name;
        $seller->bank_acc_no = $request->bank_acc_no;
        $seller->bank_routing_no = $request->bank_routing_no;

        if ($user->save() && $seller->save()) {
            flash(translate('Your Profile has been updated successfully!'))->success();
            return back();
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    /**
     * Show the application frontend home.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('frontend.index');
    }

    public function flash_deal_details($slug)
    {
        $flash_deal = FlashDeal::where('slug', $slug)->first();
        if ($flash_deal != null)
            return view('frontend.flash_deal_details', compact('flash_deal'));
        else {
            abort(404);
        }
    }

    public function load_featured_section()
    {
        return view('frontend.partials.featured_products_section');
    }

    public function load_best_selling_section()
    {
        return view('frontend.partials.best_selling_section');
    }

    public function load_home_categories_section()
    {
        return view('frontend.partials.home_categories_section');
    }

    public function load_best_sellers_section()
    {
        return view('frontend.partials.best_sellers_section');
    }

    public function trackOrder(Request $request)
    {
        if ($request->has('order_code')) {
            $order = Order::where('code', $request->order_code)->first();
            if ($order != null) {
                return view('frontend.track_order', compact('order'));
            }
        }
        return view('frontend.track_order');
    }

    public function productBrand(Request $request, $brand_slug, $slug)
    {
        $brand = Brand::where('slug', $brand_slug)->first();
        if ($brand != null) {
            return $this->product($request, $slug, $brand->id);
        }
        abort(404);
    }

    public function productType(Request $request, $type_slug, $slug)
    {
        $type = ProductType::where('slug', $type_slug)->first();
        if ($type != null) {
            return $this->product($request, $slug, null, $type->id);
        }
        abort(404);
    }

    public function product(Request $request, $slug, $brand_id = null, $type_id = null)
    {
        $detailedProduct = Product::where('slug', $slug)->first();

        if ($detailedProduct != null && $detailedProduct->published) {
            updatePricesFromBtmsById($detailedProduct->id);
            //updateCartSetup();
            if ($request->has('product_referral_code') &&
                \App\Addon::where('unique_identifier', 'affiliate_system')->first() != null &&
                \App\Addon::where('unique_identifier', 'affiliate_system')->first()->activated) {

                $affiliate_validation_time = \App\AffiliateConfig::where('type', 'validation_time')->first();
                $cookie_minute = 30 * 24;
                if ($affiliate_validation_time) {
                    $cookie_minute = $affiliate_validation_time->value * 60;
                }
                Cookie::queue('product_referral_code', $request->product_referral_code, $cookie_minute);
                Cookie::queue('referred_product_id', $detailedProduct->id, $cookie_minute);

                $referred_by_user = User::where('referral_code', $request->product_referral_code)->first();

                $affiliateController = new AffiliateController;
                $affiliateController->processAffiliateStats($referred_by_user->id, 1, 0, 0, 0);
            }
            if ($detailedProduct->digital == 1) {
                return view('frontend.digital_product_details', compact('detailedProduct', 'brand_id', 'type_id'));
            } else {
                if ($detailedProduct->used == 1) {
                    return view('frontend.product_details_used', compact('detailedProduct', 'brand_id', 'type_id'));
                } else {
                    if ($detailedProduct->product_layout == 2 && $detailedProduct->category->for_sale == 0) {
                        return view('frontend.product_details_not_for_sale', compact('detailedProduct', 'brand_id', 'type_id'));
                    } else {
                        $price_with_discount = $detailedProduct->getPriceForCurrentUser(true);
                        $price_without_discount = $detailedProduct->getPriceForCurrentUser(false);

                        return view('frontend.product_details', compact('detailedProduct', 'price_with_discount', 'price_without_discount', 'brand_id', 'type_id'));
                    }
                }

            }
            // return view('frontend.product_details', compact('detailedProduct'));
        }
        abort(404);
    }

    public function shop($slug)
    {
        $shop = Shop::where('slug', $slug)->first();
        if ($shop != null) {
            $seller = Seller::where('user_id', $shop->user_id)->first();
            if ($seller->verification_status != 0) {
                return view('frontend.seller_shop', compact('shop'));
            } else {
                return view('frontend.seller_shop_without_verification', compact('shop', 'seller'));
            }
        }
        abort(404);
    }

    public function filter_shop($slug, $type)
    {
        $shop = Shop::where('slug', $slug)->first();
        if ($shop != null && $type != null) {
            return view('frontend.seller_shop', compact('shop', 'type'));
        }
        abort(404);
    }

    public function all_categories(Request $request)
    {
//        $categories = Category::where('level', 0)->orderBy('name', 'asc')->get();
        $categories = Category::where('level', 0)->orderBy('order_level', 'desc')->get();
        return view('frontend.all_category', compact('categories'));
    }

    public function all_brands(Request $request)
    {
        $categories = Category::all();
        return view('frontend.all_brand', compact('categories'));
    }

    public function show_product_upload_form(Request $request)
    {
        if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
            if (Auth::user()->seller->remaining_uploads > 0) {
                $categories = Category::where('parent_id', 0)
                    ->where('digital', 0)
                    ->with('childrenCategories')
                    ->get();
                return view('frontend.user.seller.product_upload', compact('categories'));
            } else {
                flash(translate('Upload limit has been reached. Please upgrade your package.'))->warning();
                return back();
            }
        }
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        return view('frontend.user.seller.product_upload', compact('categories'));
    }

    public function show_product_edit_form(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $lang = $request->lang;
        $tags = json_decode($product->tags);
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();
        return view('frontend.user.seller.product_edit', compact('product', 'categories', 'tags', 'lang'));
    }

    public function seller_product_list(Request $request)
    {
        $search = null;
        $products = Product::where('user_id', Auth::user()->id)->where('digital', 0)->orderBy('created_at', 'desc');
        if ($request->has('search')) {
            $search = $request->search;
            $products = $products->where('name', 'like', '%' . $search . '%');
        }
        $products = $products->paginate(10);
        return view('frontend.user.seller.products', compact('products', 'search'));
    }

    public function ajax_search(Request $request)
    {
        $keywords = array();
        $products_array = array();
        $products = Product::where('published', 1)->where('tags', 'like', '%' . $request->search . '%')->get();
        foreach ($products as $key => $product) {
            foreach (explode(',', $product->tags) as $key => $tag) {
                if (stripos($tag, $request->search) !== false) {
                    if (sizeof($keywords) > 5) {
                        break;
                    } else {
                        if (!in_array(strtolower($tag), $keywords)) {
                            array_push($keywords, strtolower($tag));
                        }
                    }
                }
            }
        }

        $products_ids = DB::table('products')
            ->join('product_stocks', 'products.id', '=', 'product_stocks.product_id')
            ->select('products.*')
            ->where('products.part_number', 'like', '%' . $request->search . '%')
            ->orWhere('products.old_item_code', 'like', '%' . $request->search . '%')
            ->orWhere('product_stocks.part_number', 'like', '%' . $request->search . '%')
            ->orWhere('product_stocks.old_item_code', 'like', '%' . $request->search . '%')
            ->orWhere('products.name', 'like', '%' . $request->search . '%')
            ->distinct()
            ->get();

        if (count($products_ids) > 0) {
            foreach ($products_ids as $products_id) {
                if (!in_array($products_id->id, $products_array)) {
                    $products_array[] = $products_id->id;
                }
            }
        }

        $products = filter_products(Product::where('published', 1)->whereIn('id', $products_array))->get();

        $categories = Category::where('name', 'like', '%' . $request->search . '%')->get()->take(3);

        $shops = Shop::whereIn('user_id', verified_sellers_id())->where('name', 'like', '%' . $request->search . '%')->get()->take(3);

        if (sizeof($products) > 0) {
            return view('frontend.partials.search_content', compact('products', 'categories', 'keywords', 'shops'));
        }
        return '0';
    }

    public function listing(Request $request)
    {
        return $this->search($request);
    }

    public function listingMainCategoryByBrand(Request $request, $brand_slug, $category_slug)
    {
        $category = Category::where('slug', $category_slug)->first();
        $brand = Brand::where('slug', $brand_slug)->first();
        if ($category != null && $brand != null) {
            return $this->main_category($request, $category_slug, $brand->id);
        }
        abort(404);
    }

    public function listingMainCategoryByType(Request $request, $type_slug)
    {
        $type = ProductType::where('slug', $type_slug)->first();
        if ($type != null) {
            return $this->main_category($request, null, null, $type->id);
        }
        abort(404);
    }

    public function listingMainCategoryByCatType(Request $request, $type_slug, $category_slug)
    {
        $category = Category::where('slug', $category_slug)->first();
        $type = ProductType::where('slug', $type_slug)->first();
        if ($category != null && $type != null) {
            return $this->main_category($request, $category_slug, null, $type->id);
        }
        abort(404);
    }

    public function main_category(Request $request, $category_slug = null, $brand_id = null, $type_id = null)
    {
        $category = null;
        $type = null;
        if ($category_slug != null) {
            $category = Category::where('slug', $category_slug)->where('parent_id', 0)->first();
        }
        if ($type_id != null) {
            $type = ProductType::where('id', $type_id)->first();
        }
        if ($brand_id != null) {
            $brand = Brand::where('id', $brand_id)->first();
        }
        if ($type_id != null && $category != null) {
            $categories = getSubCategoriesByTypeAndCat($category->id, $type_id);
            if (count($categories) == 0) {
                return redirect()->route('products.type_category', ['category_slug' => $category->slug, 'type_slug' => $type->slug]);
            }
        } elseif ($brand_id != null && $category != null) {
            $categories = getSubCategoriesByBrand($category->id, $brand_id);
            if (count($categories) == 0) {
                return redirect()->route('products.brand_category', ['category_slug' => $category->slug, 'brand_slug' => $brand->slug]);
            }
        } elseif ($type_id != null) {
            $categories = getMainCategoriesByType($type_id);
            if (count($categories) == 0) {
                return redirect()->route('products.type', $type->slug);
            }
        } else {
            $categories = getAvailableSubCategoriesByCategory($category->id);
            if (count($categories) == 0) {
                return redirect()->route('products.category', $category_slug);
            }
        }

        return view('frontend.category_listing', compact('category', 'categories', 'brand_id', 'type_id', 'type'));
    }

    public function listingByCategory(Request $request, $category_slug)
    {
        $category = Category::where('slug', $category_slug)->first();
        if ($category != null) {
            return $this->search($request, $category->id);
        }
        abort(404);
    }

    public function listingByBrand(Request $request, $brand_slug)
    {
        $brand = Brand::where('slug', $brand_slug)->first();
        if ($brand != null) {
            return $this->search($request, null, $brand->id);
        }
        abort(404);
    }

    public function listingByBrandAndCategory(Request $request, $brand_slug, $category_slug)
    {

        $brand = Brand::where('slug', $brand_slug)->first();
        $category = Category::where('slug', $category_slug)->first();

        if ($brand != null && $category != null) {
            return $this->search($request, $category->id, $brand->id);
        }
        abort(404);
    }

    public function listingByType(Request $request, $type_slug)
    {
        $type = ProductType::where('slug', $type_slug)->first();

        if ($type != null) {
            return $this->search($request, null, null, $type->id);
        }
        abort(404);
    }

    public function listingByTypeAndCategory(Request $request, $type_slug, $category_slug)
    {

        $type = ProductType::where('slug', $type_slug)->first();
        $category = Category::where('slug', $category_slug)->first();

        if ($type != null && $category != null) {
            return $this->search($request, $category->id, null, $type->id);
        }
        abort(404);
    }


    public function search(Request $request, $category_id = null, $brand_id = null, $type_id = null, $outlet = 0, $page = 1, $products_per_page = 12)
    {
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        } else {
            $limit = $products_per_page;
        }

        $query = $request->q;
        $sort_by = $request->sort_by;
        $min_price = $request->min_price;
        $max_price = $request->max_price;
        $seller_id = $request->seller_id;

        if ($request->outlet != null) {
            $outlet = $request->outlet;
        }

        $conditions = ['published' => 1, 'used' => 0, 'outlet' => $outlet];

        if ($brand_id != null) {
            $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
        } elseif ($request->brand != null) {
            $brand_id = (Brand::where('slug', $request->brand)->first() != null) ? Brand::where('slug', $request->brand)->first()->id : null;
            $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
        }

        if ($type_id != null) {
            $conditions = array_merge($conditions, ['type_id' => $type_id]);
        }

        if ($seller_id != null) {
            $conditions = array_merge($conditions, ['user_id' => Seller::findOrFail($seller_id)->user->id]);
        }

        $products = Product::where($conditions);

        if ($category_id != null) {
            $category = Category::find($category_id);

            $category_ids = CategoryUtility::children_ids($category_id);
            $category_ids[] = $category_id;

            $cats_for_sale = array();

            if (count($category_ids) > 0) {
                foreach ($category_ids as $cat_id) {
                    $this_category = Category::find($cat_id);
                    if (!in_array($this_category->id, $cats_for_sale) && $category->for_sale == $this_category->for_sale) {
                        $cats_for_sale[] = $this_category->id;
                    }
                }
            }
            $products = $products->whereIn('category_id', $cats_for_sale);
        } else {
            $cats_for_sale = array();

            foreach (Category::all() as $cat_id) {
                if (!in_array($cat_id->id, $cats_for_sale) && $cat_id->for_sale == 1) {
                    $cats_for_sale[] = $cat_id->id;
                }
            }
            $products = $products->whereIn('category_id', $cats_for_sale);
        }

        if ($min_price != null && $max_price != null) {
            $products = $products->where('unit_price', '>=', $min_price)->where('unit_price', '<=', $max_price);
        }

        if ($query != null) {
            $searchController = new SearchController;
            $searchController->store($request);
            $products = $products->join('product_stocks', 'products.id', '=', 'product_stocks.product_id')
                ->where('products.part_number', 'like', '%' . $query . '%')
                ->orWhere('products.old_item_code', 'like', '%' . $query . '%')
                ->orWhere('product_stocks.part_number', 'like', '%' . $query . '%')
                ->orWhere('product_stocks.old_item_code', 'like', '%' . $query . '%')
                ->orWhere('products.name', 'like', '%' . $query . '%');
        }

        switch ($sort_by) {
            case 'newest':
                $products->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $products->orderBy('created_at', 'asc');
                break;
            case 'price-asc':
                $products->orderBy('unit_price', 'asc');
                break;
            case 'price-desc':
                $products->orderBy('unit_price', 'desc');
                break;
            default:
                $products->orderBy('created_at', 'desc');
                break;
        }


        $non_paginate_products = filter_products($products)->get();

        //Attribute Filter

        $attributes = array();
        foreach ($non_paginate_products as $key => $product) {
            if ($product->attributes != null && is_array(json_decode($product->attributes))) {
                foreach (json_decode($product->attributes) as $key => $value) {
                    $flag = false;
                    $pos = 0;
                    foreach ($attributes as $key => $attribute) {
                        if ($attribute['id'] == $value) {
                            $flag = true;
                            $pos = $key;
                            break;
                        }
                    }
                    if (!$flag) {
                        $item['id'] = $value;
                        $item['values'] = array();
                        foreach (json_decode($product->choice_options) as $key => $choice_option) {
                            if ($choice_option->attribute_id == $value) {
                                $item['values'] = $choice_option->values;
                                break;
                            }
                        }
                        array_push($attributes, $item);
                    } else {
                        foreach (json_decode($product->choice_options) as $key => $choice_option) {
                            if ($choice_option->attribute_id == $value) {
                                foreach ($choice_option->values as $key => $value) {
                                    if (!in_array($value, $attributes[$pos]['values'])) {
                                        array_push($attributes[$pos]['values'], $value);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $selected_attributes = array();

        foreach ($attributes as $key => $attribute) {
            if ($request->has('attribute_' . $attribute['id'])) {
                foreach ($request['attribute_' . $attribute['id']] as $key => $value) {
                    $str = '"' . $value . '"';
                    $products = $products->where('choice_options', 'like', '%' . $str . '%');
                }

                $item['id'] = $attribute['id'];
                $item['values'] = $request['attribute_' . $attribute['id']];
                array_push($selected_attributes, $item);
            }
        }


        //Color Filter
        $all_colors = array();

        foreach ($non_paginate_products as $key => $product) {
            if ($product->colors != null) {
                foreach (json_decode($product->colors) as $key => $color) {
                    if (!in_array($color, $all_colors)) {
                        array_push($all_colors, $color);
                    }
                }
            }
        }

        $selected_color = null;

        if ($request->has('color')) {
            $str = '"' . $request->color . '"';
            $products = $products->where('colors', 'like', '%' . $str . '%');
            $selected_color = $request->color;
        }

        $category = Category::find($category_id);

        if ($category_id != null && $category->for_sale == 0) {
            $products = filter_products($products)->get();
            return view('frontend.product_listing_not_sale', compact('products', 'query', 'category_id', 'brand_id', 'type_id', 'sort_by', 'seller_id', 'min_price', 'max_price', 'attributes', 'selected_attributes', 'all_colors', 'selected_color'));
        } else {
            $has_next_products = false;
            $all_products = count(filter_products($products)->get());
            $products = filter_products($products)->take($limit)->get();
            if ($all_products > count($products)) {
                $has_next_products = true;
            }
            $page_products_count = count($products);
            return view('frontend.product_listing', compact('products', 'query', 'category_id', 'brand_id', 'type_id', 'sort_by', 'seller_id', 'min_price', 'max_price', 'attributes', 'selected_attributes', 'all_colors', 'selected_color', 'outlet', 'has_next_products', 'page_products_count', 'products_per_page', 'page', 'all_products'));
        }
    }

    public function search_ajax(Request $request)
    {
        $query = $request->q;
        $outlet = ($request->outlet) ? $request->outlet : 0;
        $type_id = ($request->data_type_id) ? $request->data_type_id : null;
        $brand_id = ($request->data_brand_id) ? $request->data_brand_id : null;
        $category_id = ($request->data_category_id) ? $request->data_category_id : null;
        $sort_by = $request->sort_by;
        $min_price = $request->min_price;
        $max_price = $request->max_price;
        $seller_id = $request->seller_id;
        $products_per_page = 12;
        $page = 1;

        if ($request->outlet != null) {
            $outlet = $request->outlet;
        } else {
            $outlet = 0;
        }

        $conditions = ['published' => 1, 'used' => 0, 'outlet' => $outlet];

        if ($brand_id != null) {
            $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
        } elseif ($request->brand != null) {
            $brand_id = (Brand::where('slug', $request->brand)->first() != null) ? Brand::where('slug', $request->brand)->first()->id : null;
            $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
        }

        if ($type_id != null) {
            $conditions = array_merge($conditions, ['type_id' => $type_id]);
        }

        if ($seller_id != null) {
            $conditions = array_merge($conditions, ['user_id' => Seller::findOrFail($seller_id)->user->id]);
        }

        $products = Product::where($conditions);

        if ($category_id != null) {
            $category = Category::find($category_id);

            $category_ids = CategoryUtility::children_ids($category_id);
            $category_ids[] = $category_id;

            $cats_for_sale = array();

            if (count($category_ids) > 0) {
                foreach ($category_ids as $cat_id) {
                    $this_category = Category::find($cat_id);
                    if (!in_array($this_category->id, $cats_for_sale) && $category->for_sale == $this_category->for_sale) {
                        $cats_for_sale[] = $this_category->id;
                    }
                }
            }
            $products = $products->whereIn('category_id', $cats_for_sale);
        } else {
            $cats_for_sale = array();

            foreach (Category::all() as $cat_id) {
                if (!in_array($cat_id->id, $cats_for_sale) && $cat_id->for_sale == 1) {
                    $cats_for_sale[] = $cat_id->id;
                }
            }
            $products = $products->whereIn('category_id', $cats_for_sale);
        }

        if ($min_price != null && $max_price != null) {
            $products = $products->where('unit_price', '>=', $min_price)->where('unit_price', '<=', $max_price);
        }

        if ($query != null) {
            $searchController = new SearchController;
            $searchController->store($request);
            $products = $products->join('product_stocks', 'products.id', '=', 'product_stocks.product_id')
                ->where('products.part_number', 'like', '%' . $query . '%')
                ->orWhere('products.old_item_code', 'like', '%' . $query . '%')
                ->orWhere('product_stocks.part_number', 'like', '%' . $query . '%')
                ->orWhere('product_stocks.old_item_code', 'like', '%' . $query . '%')
                ->orWhere('products.name', 'like', '%' . $query . '%');
        }

        switch ($sort_by) {
            case 'newest':
                $products->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $products->orderBy('created_at', 'asc');
                break;
            case 'price-asc':
                $products->orderBy('unit_price', 'asc');
                break;
            case 'price-desc':
                $products->orderBy('unit_price', 'desc');
                break;
            default:
                $products->orderBy('created_at', 'desc');
                break;
        }


        $non_paginate_products = filter_products($products)->get();

        //Attribute Filter

        $attributes = array();
        foreach ($non_paginate_products as $key => $product) {
            if ($product->attributes != null && is_array(json_decode($product->attributes))) {
                foreach (json_decode($product->attributes) as $key => $value) {
                    $flag = false;
                    $pos = 0;
                    foreach ($attributes as $key => $attribute) {
                        if ($attribute['id'] == $value) {
                            $flag = true;
                            $pos = $key;
                            break;
                        }
                    }
                    if (!$flag) {
                        $item['id'] = $value;
                        $item['values'] = array();
                        foreach (json_decode($product->choice_options) as $key => $choice_option) {
                            if ($choice_option->attribute_id == $value) {
                                $item['values'] = $choice_option->values;
                                break;
                            }
                        }
                        array_push($attributes, $item);
                    } else {
                        foreach (json_decode($product->choice_options) as $key => $choice_option) {
                            if ($choice_option->attribute_id == $value) {
                                foreach ($choice_option->values as $key => $value) {
                                    if (!in_array($value, $attributes[$pos]['values'])) {
                                        array_push($attributes[$pos]['values'], $value);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $selected_attributes = array();

        foreach ($attributes as $key => $attribute) {
            if ($request->has('attribute_' . $attribute['id'])) {
                foreach ($request['attribute_' . $attribute['id']] as $key => $value) {
                    $str = '"' . $value . '"';
                    $products = $products->where('choice_options', 'like', '%' . $str . '%');
                }

                $item['id'] = $attribute['id'];
                $item['values'] = $request['attribute_' . $attribute['id']];
                array_push($selected_attributes, $item);
            }
        }


        //Color Filter
        $all_colors = array();

        foreach ($non_paginate_products as $key => $product) {
            if ($product->colors != null) {
                foreach (json_decode($product->colors) as $key => $color) {
                    if (!in_array($color, $all_colors)) {
                        array_push($all_colors, $color);
                    }
                }
            }
        }

        $selected_color = null;

        if ($request->has('color')) {
            $str = '"' . $request->color . '"';
            $products = $products->where('colors', 'like', '%' . $str . '%');
            $selected_color = $request->color;
        }

        $category = Category::find($category_id);

        if (isset($category_id)) {
            $header_title_name = $category->meta_title;
        } elseif (isset($brand_id)) {
            $header_title_name = Brand::find($brand_id)->meta_title;
        } else {
            $header_title_name = get_setting('meta_title');
        }


        if ($category_id != null && $category->for_sale == 0) {
            $products = filter_products($products)->get();
            return view('frontend.product_listing_not_sale', compact('products', 'query', 'category_id', 'brand_id', 'type_id', 'sort_by', 'seller_id', 'min_price', 'max_price', 'attributes', 'selected_attributes', 'all_colors', 'selected_color'));
        } else {
            $has_next_products = false;
            $all_products = count(filter_products($products)->get());
            $products = filter_products($products)->take($products_per_page)->get();
            if ($all_products > count($products)) {
                $has_next_products = true;
            }
            $page_products_count = count($products);
            return array('status' => 1, 'header_title_nam' => $header_title_name, 'view' => view('frontend.partials.product_listing_ajax', compact('products', 'query', 'category_id', 'brand_id', 'type_id', 'sort_by', 'seller_id', 'min_price', 'max_price', 'attributes', 'selected_attributes', 'all_colors', 'selected_color', 'outlet', 'has_next_products', 'page_products_count', 'products_per_page', 'page', 'all_products'))->render());
        }
    }

    public function load_search(Request $request)
    {
        $query = $request->q;
        $outlet = ($request->outlet) ? $request->outlet : 0;
        $type_id = ($request->data_type_id) ? $request->data_type_id : null;
        $brand_id = ($request->data_brand_id) ? $request->data_brand_id : null;
        $category_id = ($request->data_category_id) ? $request->data_category_id : null;
        $all_products = $request->all_products;
        $sort_by = $request->sort_by;
        $min_price = $request->min_price;
        $max_price = $request->max_price;
        $seller_id = $request->seller_id;
        $products_per_page = $request->products_per_page;
        $page = $request->page;

        if ($request->outlet != null) {
            $outlet = $request->outlet;
        }

        $conditions = ['published' => 1, 'used' => 0, 'outlet' => $outlet];

        if ($brand_id != null) {
            $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
        } elseif ($request->brand != null) {
            $brand_id = (Brand::where('slug', $request->brand)->first() != null) ? Brand::where('slug', $request->brand)->first()->id : null;
            $conditions = array_merge($conditions, ['brand_id' => $brand_id]);
        }

        if ($type_id != null) {
            $conditions = array_merge($conditions, ['type_id' => $type_id]);
        }

        if ($seller_id != null) {
            $conditions = array_merge($conditions, ['user_id' => Seller::findOrFail($seller_id)->user->id]);
        }

        $products = Product::where($conditions);

        if ($category_id != null) {
            $category = Category::find($category_id);

            $category_ids = CategoryUtility::children_ids($category_id);
            $category_ids[] = $category_id;

            $cats_for_sale = array();

            if (count($category_ids) > 0) {
                foreach ($category_ids as $cat_id) {
                    $this_category = Category::find($cat_id);
                    if (!in_array($this_category->id, $cats_for_sale) && $category->for_sale == $this_category->for_sale) {
                        $cats_for_sale[] = $this_category->id;
                    }
                }
            }
            $products = $products->whereIn('category_id', $cats_for_sale);
        } else {
            $cats_for_sale = array();

            foreach (Category::all() as $cat_id) {
                if (!in_array($cat_id->id, $cats_for_sale) && $cat_id->for_sale == 1) {
                    $cats_for_sale[] = $cat_id->id;
                }
            }
            $products = $products->whereIn('category_id', $cats_for_sale);
        }

        if ($min_price != null && $max_price != null) {
            $products = $products->where('unit_price', '>=', $min_price)->where('unit_price', '<=', $max_price);
        }

        if ($query != null) {
            $searchController = new SearchController;
            $searchController->store($request);
            $products = $products->join('product_stocks', 'products.id', '=', 'product_stocks.product_id')
                ->where('products.part_number', 'like', '%' . $query . '%')
                ->orWhere('products.old_item_code', 'like', '%' . $query . '%')
                ->orWhere('product_stocks.part_number', 'like', '%' . $query . '%')
                ->orWhere('product_stocks.old_item_code', 'like', '%' . $query . '%')
                ->orWhere('products.name', 'like', '%' . $query . '%');
        }

        switch ($sort_by) {
            case 'newest':
                $products->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $products->orderBy('created_at', 'asc');
                break;
            case 'price-asc':
                $products->orderBy('unit_price', 'asc');
                break;
            case 'price-desc':
                $products->orderBy('unit_price', 'desc');
                break;
            default:
                $products->orderBy('created_at', 'desc');
                break;
        }


        $non_paginate_products = filter_products($products)->get();

        //Attribute Filter

        $attributes = array();
        foreach ($non_paginate_products as $key => $product) {
            if ($product->attributes != null && is_array(json_decode($product->attributes))) {
                foreach (json_decode($product->attributes) as $key => $value) {
                    $flag = false;
                    $pos = 0;
                    foreach ($attributes as $key => $attribute) {
                        if ($attribute['id'] == $value) {
                            $flag = true;
                            $pos = $key;
                            break;
                        }
                    }
                    if (!$flag) {
                        $item['id'] = $value;
                        $item['values'] = array();
                        foreach (json_decode($product->choice_options) as $key => $choice_option) {
                            if ($choice_option->attribute_id == $value) {
                                $item['values'] = $choice_option->values;
                                break;
                            }
                        }
                        array_push($attributes, $item);
                    } else {
                        foreach (json_decode($product->choice_options) as $key => $choice_option) {
                            if ($choice_option->attribute_id == $value) {
                                foreach ($choice_option->values as $key => $value) {
                                    if (!in_array($value, $attributes[$pos]['values'])) {
                                        array_push($attributes[$pos]['values'], $value);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $selected_attributes = array();

        foreach ($attributes as $key => $attribute) {
            if ($request->has('attribute_' . $attribute['id'])) {
                foreach ($request['attribute_' . $attribute['id']] as $key => $value) {
                    $str = '"' . $value . '"';
                    $products = $products->where('choice_options', 'like', '%' . $str . '%');
                }

                $item['id'] = $attribute['id'];
                $item['values'] = $request['attribute_' . $attribute['id']];
                array_push($selected_attributes, $item);
            }
        }


        //Color Filter
        $all_colors = array();

        foreach ($non_paginate_products as $key => $product) {
            if ($product->colors != null) {
                foreach (json_decode($product->colors) as $key => $color) {
                    if (!in_array($color, $all_colors)) {
                        array_push($all_colors, $color);
                    }
                }
            }
        }

        $selected_color = null;

        if ($request->has('color')) {
            $str = '"' . $request->color . '"';
            $products = $products->where('colors', 'like', '%' . $str . '%');
            $selected_color = $request->color;
        }

        $category = Category::find($category_id);

        if ($category_id != null && $category->for_sale == 0) {
            $products = filter_products($products)->get();
            return view('frontend.product_listing_not_sale', compact('products', 'query', 'category_id', 'brand_id', 'type_id', 'sort_by', 'seller_id', 'min_price', 'max_price', 'attributes', 'selected_attributes', 'all_colors', 'selected_color'));
        } else {
            $has_next_products = false;
            $offset = $products_per_page * $page;
            $products = filter_products($products)->skip($offset)->take($products_per_page)->get();
            $total_show = $offset + count($products);
            if ($all_products > $total_show) {
                $has_next_products = true;
            }

            return array('status' => 1, 'has_next_products' => $has_next_products, 'view' => view('frontend.partials.product_listing_load', compact('products', 'brand_id', 'type_id'))->render());
        }
    }

    public function home_settings(Request $request)
    {
        return view('home_settings.index');
    }

    public function top_10_settings(Request $request)
    {
        foreach (Category::all() as $key => $category) {
            if (is_array($request->top_categories) && in_array($category->id, $request->top_categories)) {
                $category->top = 1;
                $category->save();
            } else {
                $category->top = 0;
                $category->save();
            }
        }

        foreach (Brand::all() as $key => $brand) {
            if (is_array($request->top_brands) && in_array($brand->id, $request->top_brands)) {
                $brand->top = 1;
                $brand->save();
            } else {
                $brand->top = 0;
                $brand->save();
            }
        }

        flash(translate('Top 10 categories and brands have been updated successfully'))->success();
        return redirect()->route('home_settings.index');
    }

    public function variant_price(Request $request)
    {
        $product = Product::find($request->id);
        $str = '';
        $part_number = '';
        $price = 0;
        $quantity = 0;
        $max_quantity = 0;
        $color = '';
        $color_code = '';
        $attributes = array();

        if ($request->has('color')) {
            $str = $request['color'];
            $color = $request['color'];
            $color_code = getColorName($request['color'], 'code');
        }

        if (json_decode(Product::find($request->id)->choice_options) != null) {
            foreach (json_decode(Product::find($request->id)->choice_options) as $key => $choice) {
                if ($str != null) {
                    $str .= '-' . str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                } else {
                    $str .= str_replace(' ', '', $request['attribute_id_' . $choice->attribute_id]);
                }
                $attributes[] = array(
                    'id' => $choice->attribute_id,
                    'value' => $request['attribute_id_' . $choice->attribute_id]
                );
            }
        }

        if ($str != null && $product->variant_product) {
            $product_stock = $product->stocks->where('variant', $str)->first();
            if ($product_stock != null) {
                $product_stock->updateStock();
                $price = $product_stock->getPriceForCurrentUser(true, false);
                $quantity = $product_stock->qty;
                $max_quantity = ($product_stock->qty > 0) ? $product_stock->qty : 10;
                $part_number = $product_stock->part_number;
            }
        } else {
            $product->updateStock();
            $price = $product->getPriceForCurrentUser(true, false);
            $quantity = $product->current_stock;
            $max_quantity = ($product->current_stock > 0) ? $product->current_stock : 10;
            $part_number = $product->part_number;
        }

        //Product Stock Visibility
        if ($product->stock_visibility_state == 'text') {
            $quantity = 'Stock';
        }

        //discount calculation
        /*    $flash_deals = \App\FlashDeal::where('status', 1)->get();
            $inFlashDeal = false;
            foreach ($flash_deals as $key => $flash_deal) {
              if ($flash_deal != null && $flash_deal->status == 1 && strtotime(date('d-m-Y')) >= $flash_deal->start_date && strtotime(date('d-m-Y')) <= $flash_deal->end_date && \App\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first() != null) {
                $flash_deal_product = \App\FlashDealProduct::where('flash_deal_id', $flash_deal->id)->where('product_id', $product->id)->first();
                if($flash_deal_product->discount_type == 'percent'){
                  $price -= ($price*$flash_deal_product->discount)/100;
                }
                elseif($flash_deal_product->discount_type == 'amount'){
                  $price -= $flash_deal_product->discount;
                }
                $inFlashDeal = true;
                break;
              }
            }*/
        /*if($product->discount_type == 'percent'){
            $price -= ($price*$product->discount)/100;
        }
        elseif($product->discount_type == 'amount'){
            $price -= $product->discount;
        }*/

        /*if($product->tax_type == 'percent'){
          $price += ($price*$product->tax)/100;
        }
        elseif($product->tax_type == 'amount'){
          $price += $product->tax;
        }*/
        return array('price' => single_price($price * $request->quantity), 'quantity' => $quantity, 'max_quantity' => $max_quantity, 'color' => $color, 'color_code' => $color_code, 'attributes' => $attributes, 'digital' => $product->digital, 'variation' => $str, 'part_number' => $part_number);
    }

    public function terms_policies()
    {
        return view("frontend.policies.termspolicies");
    }

    public function sellerpolicy()
    {
        return view("frontend.policies.sellerpolicy");
    }

    public function returnpolicy()
    {
        return view("frontend.policies.returnpolicy");
    }

    public function supportpolicy()
    {
        return view("frontend.policies.supportpolicy");
    }

    public function terms()
    {
        return view("frontend.policies.terms");
    }

    public function privacypolicy()
    {
        return view("frontend.policies.privacypolicy");
    }

    public function faqs()
    {
        return view("frontend.faqs");
    }

    public function about_us()
    {
        $page = Page::where('type', 'about_us_page')->first();
        return view("frontend.about_us", compact('page'));
    }

    public function services()
    {
        $page = Page::where('type', 'services_page')->first();
        return view("frontend.service", compact('page'));
    }

    public function partnership()
    {
        $page = Page::where('type', 'partnership_page')->first();
        return view("frontend.partnership", compact('page'));
    }

    public function partnership_request(Request $request)
    {
        $rules = [
            'name' => 'required',
            'surname' => 'required',
            'company' => 'required',
            'email' => 'required|email',
            'phone_code' => 'required',
            'phone' => 'required|numeric',
            'country' => 'required|numeric',
            'interests' => 'required',
            'message' => 'required',
            'agree_policies' => 'required',
            'g-recaptcha-response' => 'required|google_captcha',
        ];

        if ($request->country == '54') {
            $rules['city'] = 'required|numeric';
        } else {
            $rules['city_name'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);


        if ($validator->fails()) {
            return redirect()->route('partnership')->withErrors($validator)->withInput(
                array(
                    'opened_partnership' => true,
                    'name' => $request->name,
                    'surname' => $request->surname,
                    'company' => $request->company,
                    'email' => $request->email,
                    'country' => $request->country,
                    'city' => $request->city,
                    'city_name' => $request->city_name,
                    'phone_code' => $request->phone_code,
                    'phone' => $request->phone,
                    'interests' => $request->interests,
                    'message' => $request->message
                )
            );
        }

        $user_exist = false;
        $ex_user = User::where('email', $request->email)->first();
        $ex_part_user = PartnershipUser::where('email', $request->email)->first();
//      if($ex_user!=null || $ex_part_user!=null) {
        if ($ex_part_user != null) {
            $user_exist = true;
            flash(translate('A user or a user request already exist with this email'))->error();
            return redirect()->route('partnership');
        }

        if (!$user_exist) {
            $user = new PartnershipUser;
            $user->name = $request->name . ' ' . $request->surname;
            $user->company = $request->company;
            $user->email = $request->email;
            $user->country = $request->country;
            $user->city = $request->city ?? $request->city_name;
            $user->phone_code = $request->phone_code;
            $user->phone = $request->phone;
            $user->interests = implode(",", $request->interests);
            $user->registered_customer = (auth()->check() ? auth()->user()->id : null);
            $user->save();

            $array['view'] = 'emails.partnership_request';
            $array['subject'] = 'You have a new partnership request from ' . $request->email;
            $array['from'] = env('MAIL_USERNAME');
            $array['name'] = $request->name . ' ' . $request->surname;
            $array['company'] = $request->company;
            $array['country'] = $request->country;
            $array['city'] = $request->city ?? $request->city_name;
            $array['phone'] = '+' . $request->phone_code . ' ' . $request->phone;
            $array['interests'] = $request->interests;
            $array['content'] = $request->message;
            $array['sender'] = $request->email;
            $array['details'] = $request->message;

            $sender = config('app.contact_email');

            try {
                Mail::to($sender)->queue(new PartnershipRequestMailManager($array));
                flash(translate('The Request has been send to admin'))->success();
                return redirect()->route('partnership');
            } catch (\Exception $e) {
                return redirect()->route('partnership');
            }
        }
    }

    public function partnership_request_ajax(Request $request)
    {
        $rules = [
            'name' => 'required',
            'surname' => 'required',
            'company' => 'required',
            'email' => 'required|email',
            'phone_code' => 'required',
            'phone' => 'required|numeric',
            'country' => 'required|numeric',
            'interests' => 'required',
            'message' => 'required',
            'agree_policies' => 'required',
            'g-recaptcha-response' => 'required|google_captcha',
        ];

        if ($request->country == '54') {
            $rules['city'] = 'required|numeric';
        } else {
            $rules['city_name'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);


        if ($validator->fails()) {
            return array('status' => 3, 'validator' => $validator->getMessageBag()->toArray());
        }

        $user_exist = false;
        $ex_user = User::where('email', $request->email)->first();
        $ex_part_user = PartnershipUser::where('email', $request->email)->first();
//      if($ex_user!=null || $ex_part_user!=null) {
        if ($ex_part_user != null) {
            $user_exist = true;
            return array('status' => 4);
        }

        if (!$user_exist) {
            $user = new PartnershipUser;
            $user->name = $request->name . ' ' . $request->surname;
            $user->company = $request->company;
            $user->email = $request->email;
            $user->country = $request->country;
            $user->city = $request->city ?? $request->city_name;
            $user->phone_code = $request->phone_code;
            $user->phone = $request->phone;
            $user->interests = implode(",", $request->interests);
            $user->registered_customer = (auth()->check() ? auth()->user()->id : null);
            $user->save();

            $array['view'] = 'emails.partnership_request';
            $array['subject'] = 'You have a new partnership request from ' . $request->email;
            $array['from'] = env('MAIL_USERNAME');
            $array['name'] = $request->name . ' ' . $request->surname;
            $array['company'] = $request->company;
            $array['country'] = $request->country;
            $array['city'] = $request->city ?? $request->city_name;
            $array['phone'] = '+' . $request->phone_code . ' ' . $request->phone;
            $array['interests'] = $request->interests;
            $array['content'] = $request->message;
            $array['sender'] = $request->email;
            $array['details'] = $request->message;

            $sender = config('app.contact_email');

            try {
                Mail::to($sender)->queue(new PartnershipRequestMailManager($array));
                return array('status' => 1);
            } catch (\Exception $e) {
                return array('status' => 2);
            }
        }
    }

    public function stores_page()
    {
        $cities = DB::table('store_cities')
            ->join('stores', 'store_cities.id', '=', 'stores.city_id')
            ->select('store_cities.*')
            ->orderBy('store_cities.order_level', 'desc')
            ->distinct()
            ->get();

        return view("frontend.stores", compact('cities'));
    }

    public function brand_page($slug)
    {
        $brand = Brand::where('slug', $slug)->first();
        $categories = getMainCategoriesByBrand($brand->id);
        return view("frontend.brand_listing", compact('categories', 'brand'));
    }

    public function used_page()
    {
        $land_products = filter_products(\App\Product::where('published', 1)->where('type_id', '1')->where('used', '1'))->get();
        $water_products = filter_products(\App\Product::where('published', 1)->where('type_id', '2')->where('used', '1'))->get();
        return view("frontend.used_listing", compact('land_products', 'water_products'));
    }

    public function contact()
    {
        $page = Page::where('type', 'contact_page')->first();
        $store = Store::where('show_contact', '1')->first();
        return view("frontend.contact_form", compact('page', 'store'));
    }

    public function contact_send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
            'agree_policies' => 'required',
            'g-recaptcha-response' => 'required|google_captcha',
        ]);


        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput(
                array(
                    'error_in_contact' => true,
                    'name' => $request->name,
                    'email' => $request->email,
                    'message' => $request->message
                )
            );
        }

        $array['view'] = 'emails.contact';
        $array['subject'] = 'You have new email from ' . $request->email;
        $array['from'] = env('MAIL_USERNAME');
        $array['name'] = $request->name;
        $array['content'] = $request->message;
        $array['sender'] = $request->email;
        $array['details'] = $request->message;

        $sender = config('app.contact_email');

        try {
            Mail::to($sender)->queue(new ContactMailManager($array));
            flash(translate('Message has been send to admin'))->success();
            return back();
        } catch (\Exception $e) {
            //return back();
        }
    }

    public function contact_send_ajax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
            'agree_policies' => 'required',
            'g-recaptcha-response' => 'required|google_captcha',
        ]);


        if ($validator->fails()) {
            return array('status' => 3, 'validator' => $validator->getMessageBag()->toArray());
        }

        $array['view'] = 'emails.contact';
        $array['subject'] = 'You have new email from ' . $request->email;
        $array['from'] = env('MAIL_USERNAME');
        $array['name'] = $request->name;
        $array['content'] = $request->message;
        $array['sender'] = $request->email;
        $array['details'] = $request->message;

        $sender = config('app.contact_email');

        try {
            Mail::to($sender)->queue(new ContactMailManager($array));
        } catch (\Exception $e) {
            return array('status' => 2);
        }

        return array('status' => 1);
    }

    public function request_product(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'product' => 'required|numeric',
            'quantity' => 'required|numeric',
            'country' => 'required',
            'comments' => 'required',
            'agree_policies' => 'required',
            'g-recaptcha-response' => 'required|google_captcha',
        ]);

        $product = Product::find($request->product);
        $color = '';
        $attributes = array();
        if (count(json_decode($product->colors)) > 0 && $request->has('color')) {
            $color = $request->color;
        }
        if ($product->choice_options != null) {
            foreach (json_decode($product->choice_options) as $key => $choice) {
                $attributes['attribute_id_' . $choice->attribute_id] = $request->{'attribute_id_' . $choice->attribute_id};
            }
        }

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput(
                array(
                    'opened_request' => true,
                    'color' => $color,
                    'attributes' => $attributes,
                    'quantity' => $request->quantity,
                    'name' => $request->name,
                    'email' => $request->email,
                    'country' => $request->country,
                    'comments' => $request->comments
                )
            );
        }

        $array['view'] = 'emails.product_request';
        $array['subject'] = 'You have new product request from ' . $request->email;
        $array['from'] = env('MAIL_USERNAME');
        $array['name'] = $request->name;
        $array['product'] = $request->product;
        $array['quantity'] = $request->quantity;
        $array['country'] = $request->country;
        $array['content'] = $request->comments;
        $array['sender'] = $request->email;
        $array['details'] = $request->comments;
        $array['color'] = $color;
        $array['attributes'] = $attributes;

        $sender = config('app.contact_email');

        try {
            Mail::to($sender)->queue(new ProductRequestMailManager($array));
            flash(translate('The Request has been send to admin'))->success();
            return back();
        } catch (\Exception $e) {
            //return back();
        }
    }

    public function spare_parts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand' => 'required',
            'model_code' => 'required',
            'model_year' => 'required',
            'chassis_no' => 'required',
            'color_code' => 'required',
            'part_name' => 'required',
            'part_email' => 'required|email',
            'part_country' => 'required',
            'part_comments' => 'required',
            'agree_policies_parts' => 'required',
            'g-recaptcha-response' => 'required|google_captcha',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput(
                array(
                    'opened_spare_parts' => true,
                    'brand' => $request->brand,
                    'model_code' => $request->model_code,
                    'model_year' => $request->model_year,
                    'chassis_no' => $request->chassis_no,
                    'color_code' => $request->color_code,
                    'part_name' => $request->part_name,
                    'part_email' => $request->part_email,
                    'part_country' => $request->part_country,
                    'part_comments' => $request->part_comments
                )
            );
        }

        $array['view'] = 'emails.spare_parts';
        $array['subject'] = 'You have a new request for spare parts from ' . $request->part_email;
        $array['from'] = env('MAIL_USERNAME');
        $array['brand'] = $request->brand;
        $array['model_code'] = $request->model_code;
        $array['model_year'] = $request->model_year;
        $array['chassis_no'] = $request->chassis_no;
        $array['color_code'] = $request->color_code;
        $array['name'] = $request->part_name;
        $array['country'] = $request->part_country;
        $array['content'] = $request->part_comments;
        $array['sender'] = $request->part_email;
        $array['details'] = $request->part_comments;

        $sender = config('app.contact_email');

        try {
            Mail::to($sender)->queue(new SparePartsMailManager($array));
            flash(translate('The Request for Spare Parts has been send to admin'))->success();
            return back();
        } catch (\Exception $e) {
            //return back();
        }
    }

    public function used_request_product(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'product' => 'required|numeric',
            'country' => 'required',
            'comments' => 'required',
            'agree_policies' => 'required',
            'g-recaptcha-response' => 'required|google_captcha',
        ]);


        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput(
                array(
                    'opened_interested' => true,
                    'name' => $request->name,
                    'email' => $request->email,
                    'country' => $request->country,
                    'comments' => $request->comments
                )
            );
        }

        $array['view'] = 'emails.used_product_request';
        $array['subject'] = 'You have a request for a used product from ' . $request->email;
        $array['from'] = env('MAIL_USERNAME');
        $array['name'] = $request->name;
        $array['product'] = $request->product;
        $array['country'] = $request->country;
        $array['content'] = $request->comments;
        $array['sender'] = $request->email;
        $array['details'] = $request->comments;

        $sender = config('app.contact_email');

        try {
            Mail::to($sender)->queue(new UsedProductRequestMailManager($array));
            flash(translate('The Request has been send to admin'))->success();
            return back();
        } catch (\Exception $e) {
            //return back();
        }
    }

    public function get_pick_ip_points(Request $request)
    {
        $pick_up_points = PickupPoint::all();
        return view('frontend.partials.pick_up_points', compact('pick_up_points'));
    }

    public function get_category_items(Request $request)
    {
        $category = Category::findOrFail($request->id);
        return view('frontend.partials.category_elements', compact('category'));
    }

    public function premium_package_index()
    {
        $customer_packages = CustomerPackage::all();
        return view('frontend.user.customer_packages_lists', compact('customer_packages'));
    }

    public function seller_digital_product_list(Request $request)
    {
        $products = Product::where('user_id', Auth::user()->id)->where('digital', 1)->orderBy('created_at', 'desc')->paginate(10);
        return view('frontend.user.seller.digitalproducts.products', compact('products'));
    }

    public function show_digital_product_upload_form(Request $request)
    {
        if (\App\Addon::where('unique_identifier', 'seller_subscription')->first() != null && \App\Addon::where('unique_identifier', 'seller_subscription')->first()->activated) {
            if (Auth::user()->seller->remaining_digital_uploads > 0) {
                $business_settings = BusinessSetting::where('type', 'digital_product_upload')->first();
                $categories = Category::where('digital', 1)->get();
                return view('frontend.user.seller.digitalproducts.product_upload', compact('categories'));
            } else {
                flash(translate('Upload limit has been reached. Please upgrade your package.'))->warning();
                return back();
            }
        }

        $business_settings = BusinessSetting::where('type', 'digital_product_upload')->first();
        $categories = Category::where('digital', 1)->get();
        return view('frontend.user.seller.digitalproducts.product_upload', compact('categories'));
    }

    public function show_digital_product_edit_form(Request $request, $id)
    {
        $categories = Category::where('digital', 1)->get();
        $lang = $request->lang;
        $product = Product::find($id);
        return view('frontend.user.seller.digitalproducts.product_edit', compact('categories', 'product', 'lang'));
    }

    // Ajax call
    public function new_verify(Request $request)
    {
        $email = $request->email;
        if (isUnique($email) == '0') {
            $response['status'] = 2;
            $response['message'] = 'Email already exists!';
            return json_encode($response);
        }

        $response = $this->send_email_change_verification_mail($request, $email);
        return json_encode($response);
    }


    // Form request
    public function update_email(Request $request)
    {
        $email = $request->email;
        if (isUnique($email)) {
            $this->send_email_change_verification_mail($request, $email);
            flash(translate('A verification mail has been sent to the mail you provided us with.'))->success();
            return back();
        }

        flash(translate('Email already exists!'))->warning();
        return back();
    }

    public function send_email_change_verification_mail($request, $email)
    {
        $response['status'] = 0;
        $response['message'] = 'Unknown';

        $verification_code = Str::random(32);

        $array['subject'] = 'Email Verification';
        $array['from'] = env('MAIL_USERNAME');
        $array['content'] = 'Verify your account';
        $array['link'] = route('email_change.callback') . '?new_email_verificiation_code=' . $verification_code . '&email=' . $email;
        $array['sender'] = Auth::user()->name;
        $array['details'] = "Email Second";

        $user = Auth::user();
        $user->new_email_verificiation_code = $verification_code;
        $user->save();

        try {
            Mail::to($email)->queue(new SecondEmailVerifyMailManager($array));

            $response['status'] = 1;
            $response['message'] = translate("Your verification mail has been Sent to your email.");

        } catch (\Exception $e) {
            // return $e->getMessage();
            $response['status'] = 0;
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    public function email_change_callback(Request $request)
    {
        if ($request->has('new_email_verificiation_code') && $request->has('email')) {
            $verification_code_of_url_param = $request->input('new_email_verificiation_code');
            $user = User::where('new_email_verificiation_code', $verification_code_of_url_param)->first();

            if ($user != null) {

                $user->email = $request->input('email');
                $user->new_email_verificiation_code = null;
                $user->save();

                auth()->login($user, true);

                flash(translate('Email Changed successfully'))->success();
                return redirect()->route('dashboard');
            }
        }

        flash(translate('Email was not verified. Please resend your mail!'))->error();
        return redirect()->route('dashboard');

    }

    public function reset_password_with_code(Request $request)
    {
        if (($user = User::where('email', $request->email)->where('verification_code', $request->code)->first()) != null) {
            if ($request->password == $request->password_confirmation) {
                $user->password = Hash::make($request->password);
                $user->email_verified_at = date('Y-m-d h:m:s');
                $user->save();
                event(new PasswordReset($user));
                auth()->login($user, true);

                flash(translate('Password updated successfully'))->success();

                if (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff') {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('home');
            } else {
                flash("Password and confirm password didn't match")->warning();
                return redirect()->route('password.request');
            }
        } else {
            flash("Verification code mismatch")->error();
            return redirect()->route('password.request');
        }
    }

    public function reset_password_form()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        } else {
            return redirect()->route('password.request');
        }
    }


    public function all_flash_deals()
    {
        $today = strtotime(date('Y-m-d H:i:s'));

        $data['all_flash_deals'] = FlashDeal::where('status', 1)
            ->where('start_date', "<=", $today)
            ->where('end_date', ">", $today)
            ->orderBy('created_at', 'desc')
            ->get();

        return view("frontend.flash_deal.all_flash_deal_list", $data);
    }

    public function all_seller(Request $request)
    {
        $shops = Shop::whereIn('user_id', verified_sellers_id())
            ->paginate(15);

        return view('frontend.shop_listing', compact('shops'));
    }

    public function close_account()
    {
        $array = array(
            'subject' => env('APP_NAME') . ': Account Closure',
            'user' => Auth::user(),
        );
        $sender = config('app.contact_email');
        try {
            Mail::to($sender)->queue(new AccountClosureMailManager($array));
            flash(translate('Your account closure request has been sent successfully'))->success();
            return back();
        } catch (\Exception $e) {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function rfid_search(Request $request)
    {

        $rfid_no = $request->search;

        if (!$request->has('search')) {
            return redirect()->back();
        }

        if ($request->search == '' || $request->search == null) {
            $organisation = null;
            $card = null;
            $user = null;
            $valid_subscriptions = [];
            flash(translate('This card does not exist.'))->error();
            return view('backend.rfid_no_search', compact('organisation', 'card', 'user', 'valid_subscriptions'));
        }


        $card = Card::where('rfid_no', $rfid_no)->first();

        if ($card == null) {
            $card = Card::where('required_field_value', $request->search)->first();

            if ($card == null) {

                $card = Card::where('rfid_no_dec', $request->search)->first();

                if ($card == null) {

                    $organisation = null;
                    $card = null;
                    $user = null;
                    $valid_subscriptions = [];
                    flash(translate('This card does not exist.'))->error();
                    return view('backend.rfid_no_search', compact('organisation', 'card', 'user', 'valid_subscriptions'));
                }

            }
        }


        $organisation = $card->organisation;

        $user = null;

        if ($card->user_id != null) {
            $user = User::findorfail($card->user_id);
        }

        $purchaseController = new CateringPlanPurchaseController;

        $response = $purchaseController->find_valid_subscription($card->id, null);
//

        $subscription_status = $response['status'] ?? 'No Subscription';

        $valid_subscriptions = CateringPlanPurchase::join('catering_plans', 'catering_plans.id', '=', 'catering_plan_purchases.catering_plan_id')
            ->where('catering_plan_purchases.card_id', '=', $card->id)
            ->select('catering_plan_purchases.*')->paginate(10);


        $general_search = $request->search;


        return view('backend.rfid_no_search', compact('organisation', 'card', 'user', 'valid_subscriptions', 'subscription_status', 'general_search'));

    }

    public function app_order_details(Request $request)
    {
//        return response()->json(['status' => 1,$request->all()]);

        $order = AppOrder::find($request->order_id);

        if($order==null){
            return response()->json(['status' => 0, 'msg' => translate('Order does not exists')]);
        }

        $canteen_user = CanteenAppUser::find($order->user_id);
        if($canteen_user==null || $canteen_user->user_id != auth()->user()->id){
            return response()->json(['status' => 0, 'msg' => translate('Unauthorized information')]);
        }


        $order_details_ids = $order->orderDetails->pluck('id');
        $canteen_purchases = \App\Models\CanteenPurchase::whereIn('canteen_order_detail_id', $order_details_ids)->get();
//        $refunds = [];
        $refunds = AppRefundDetail::select('app_refund_details.app_order_code', 'app_refund_details.items_refunded_quantity as quantity ', 'canteen_purchases.date', 'canteen_purchases.break_num', 'canteen_purchases.price',
            'canteen_purchases.canteen_product_id as product_id', 'canteen_purchases.break_hour_from')
            ->join('app_order_details', 'app_order_details.id', '=', 'app_refund_details.app_order_detail_id')
            ->join('canteen_purchases', 'app_order_details.id', '=', 'canteen_purchases.canteen_order_detail_id')
            ->where('app_refund_details.app_order_id', $order->id)
            ->where(function ($refund) {
                $refund
                    ->where('canteen_purchases.deleted_at', null)
                    ->orWhere('canteen_purchases.deleted_at', '!=', null);
            })
            ->orderBy('canteen_purchases.date', 'asc')->get();


        return response()->json(['status' => 1, 'view' => view('frontend.partials.canteen_order_details', compact('canteen_user','order', 'canteen_purchases', 'refunds', 'order_details_ids'))->render()]);
    }

    public function debug()
    {

        if (request()->ip() == '82.102.76.201' || request()->ip() == '127.0.0.1' ) {

            $export_data = Session::get('export_data');

            $data =json_decode(json_encode(end($export_data)), true);

            dd($data);

//            $array = explode('/', \Illuminate\Support\Facades\Session::get('_previous')['url']);
//
//            $last_page = end($array);
//            dd(explode('/', \Illuminate\Support\Facades\Session::get('_previous')['url']),$last_page);
//
//            dd(Carbon::create("2023-12-01 00:00:00")->format('Y-m-d'));

//           dd(day_name('mon'));

        }

        abort(409);

    }
}
