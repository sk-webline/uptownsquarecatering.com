<?php

namespace App\Http\Controllers;

use App\Mail\SecondEmailVerifyMailManager;
use App\Models\Btms\Customers;
use App\Models\Card;
use App\Models\CateringPlanPurchase;
use App\OrdersRfidFilteredExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Customer;
use App\User;
use App\Order;
use App\PartnershipUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use App\CustomersExport;
use App\CustomersRfidFilteredExport;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $date = $request->date;
        $selected_organisations = [];
        $org_settings = [];

        $temp_filename = '';

        if ($request->has('organisation')) {
            $selected_organisations = $request->organisation;
        }


        $customers = Customer::select('customers.*')->join('users', 'users.id', '=', 'customers.user_id')->orderBy('customers.created_at', 'desc');


        if ($request->has('search') && $request->search != null) {
            $sort_search = $request->search;

            $user_ids = User::where('users.user_type', 'customer')
                ->join('cards', 'cards.user_id', '=', 'users.id')
                ->where(function ($user) use ($sort_search) {
                    $user
                        ->where('users.name', 'like', '%' . $sort_search . '%')->orWhere('users.email', 'like', '%' . $sort_search . '%')
                        ->orWhere('cards.rfid_no', 'like', '%' . $sort_search . '%')
                        ->orWhere('cards.rfid_no_dec', 'like', '%' . $sort_search . '%');
                })->pluck('users.id')->toArray();


            $customers = $customers->where(function ($customer) use ($user_ids) {
                $customer->whereIn('customers.user_id', $user_ids);
            });
        }



            $user_ids = User::where('users.user_type', 'customer')->join('cards', 'cards.user_id', '=', 'users.id');

            if ($request->has('organisation')) {
                $user_ids = $user_ids->where(function ($card) use ($selected_organisations) {
                    $card->whereIn('cards.organisation_id', $selected_organisations);
                })->groupBy('user_id');
            }


            if ($request->has('customers_with_no_purchase')) {

                $user_ids_with_purchases = CateringPlanPurchase::groupBy('user_id')->pluck('user_id')->toArray();

                $user_ids = $user_ids->whereNotIn('users.id', $user_ids_with_purchases)->groupBy('users.id');

                $date = null;

                $request->session()->put('customers_with_no_purchase', 1);

                $temp_filename = ' - Customers without Plan Purchase';

            } else {

                $request->session()->forget('customers_with_no_purchase');

                if ($date != null) {
                    $start_date = Carbon::create(date('Y-m-d', strtotime(explode(" to ", $date)[0])))->format('Y-m-d H:i:s');

                    $end_date = Carbon::create(date('Y-m-d', strtotime(explode(" to ", $date)[1])))->endOfDay()->format('Y-m-d H:i:s');

                    $card_ids_purchases = CateringPlanPurchase::where('created_at', '>=', $start_date)->where('created_at', '<=', $end_date)
                        ->pluck('card_id')->toArray();

                    $user_ids = $user_ids->where(function ($card) use ($card_ids_purchases) {
                        $card->whereIn('cards.id', $card_ids_purchases);
                    });

                }

            }

            $user_ids = $user_ids->pluck('users.id')->toArray();


            $customers = $customers->where(function ($customer) use ($user_ids) {
                $customer->whereIn('customers.user_id', $user_ids);
            });


        $total_customers = count($customers->get());

        if (!$request->has('form_type') || $request->form_type == 'filter') {
            $customers = $customers->paginate(15);

            return view('backend.customer.customers.index', compact('customers', 'sort_search', 'total_customers', 'selected_organisations', 'date'));

        } elseif ($request->form_type == 'export') {

            $filename = 'Customers Export ' . Carbon::now();
            $customers = $customers->get();

            if ($request->has('rfid_filtered')) {

                $filename = $filename . $temp_filename;

                return Excel::download(new CustomersRfidFilteredExport($customers), $filename . '.xlsx');

            }else{

                $filename = $filename . $temp_filename;

                return Excel::download(new CustomersExport($customers), $filename . '.xlsx');

            }


        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'phone' => 'required|unique:users',
        ]);

        $response['status'] = 'Error';

        $user = User::create($request->all());

        $customer = new Customer;

        $customer->user_id = $user->id;
        $customer->save();

        if (isset($user->id)) {
            $html = '';
            $html .= '<option value="">
                        ' . translate("Walk In Customer") . '
                    </option>';
            foreach (Customer::all() as $key => $customer) {
                if ($customer->user) {
                    $html .= '<option value="' . $customer->user->id . '" data-contact="' . $customer->user->email . '">
                                ' . $customer->user->name . '
                            </option>';
                }
            }

            $response['status'] = 'Success';
            $response['html'] = $html;
        }

        echo json_encode($response);
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
    public function edit($id)
    {
        $customer = Customer::findOrFail(decrypt($id));
        $partner_user = PartnershipUser::where('email', $customer->user->email)->first();

        if (!$customer->user->accept_partner_request || $partner_user == null) {
            flash('The customer ' . $customer->user->name . ' is not active partner')->warning();
            return redirect()->route('customers.index');
        }
        return view('backend.customer.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail(decrypt($id));
        $user = $customer->user;
        $partner_request = PartnershipUser::where('user_id', $user->id)->first();
        if ($partner_request != null && $request->has('customer_code') && $request->customer_code !== '') {
            $customer = new Customers;
            $customerTableName = $customer->getTable();

            $btmsCustomerRow = DB::connection('sqlsrv')->table($customerTableName)
                ->select(DB::raw('[Customer Code], [Discount Code]'))
                ->where('Customer Code', $request->customer_code)
                ->first();
            if ($btmsCustomerRow != null) {
                flash(translate('User has been find in BTMS and updated successfully'))->success();
                $user->btms_customer_code = $request->customer_code;
                $user->btms_discount_group = $btmsCustomerRow->{'Discount Code'};

                if ($user->insert_btms_code_for_first_time == 0) {
                    if ($partner_request->customer_and_after_partner) {
                        $array['content'] = "";
                    } else {
                        $password = getUniqueCode(10);
                        $user->password = Hash::make($password);
                        $array['content'] = translate('Your password is ') . $password;
                    }

                    $array['view'] = 'emails.verification';
                    $array['from'] = env('MAIL_USERNAME');
                    $array['subject'] = 'You have been accepted as partner from ' . env('APP_NAME');

                    try {
                        Mail::to($user->email)->queue(new SecondEmailVerifyMailManager($array));
                        $user->insert_btms_code_for_first_time = 1;
                    } catch (\Exception $e) {
                        dd($e->getMessage());
                    }
                    $user->save();
                }
            } else {
                flash(translate(' The customer code "' . $request->customer_code . '" has not been found in BTMS'))->success();
                $user->btms_customer_code = "";
                $user->btms_discount_group = "";

            }
        } else {
            $user->btms_customer_code = "";
            $user->btms_discount_group = "";
        }
        $user->save();

        return redirect()->route('customers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Order::where('user_id', Customer::findOrFail($id)->user->id)->delete();


        $partner_user = PartnershipUser::where('email', Customer::findOrFail($id)->user->email)->first();
        if ($partner_user) {
            PartnershipUser::destroy($partner_user->id);
        }
        User::destroy(Customer::findOrFail($id)->user->id);
        if (Customer::destroy($id)) {
            flash(translate('Customer has been deleted successfully'))->success();
            return redirect()->route('customers.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    public function login($id)
    {
        $customer = Customer::findOrFail(decrypt($id));

        $user = $customer->user;

        auth()->login($user, true);

        return redirect()->route('dashboard');
    }

    public function ban($id)
    {
        $customer = Customer::findOrFail($id);

        if ($customer->user->banned == 1) {
            $customer->user->banned = 0;
            flash(translate('Customer UnBanned Successfully'))->success();
        } else {
            $customer->user->banned = 1;
            flash(translate('Customer Banned Successfully'))->success();
        }

        $customer->user->save();

        return back();
    }

    public function updatePayOnCredit(Request $request)
    {
        $customer = Customer::findOrFail($request->id);
        $customer->user->pay_on_credit = $request->status;
        if ($customer->user->save()) {
            return 1;
        }
        return 0;
    }

    public function updatePayOnDelivery(Request $request)
    {
        $customer = Customer::findOrFail($request->id);
        $customer->user->pay_on_delivery = $request->status;
        if ($customer->user->save()) {
            return 1;
        }
        return 0;
    }

    public function excluded_vat(Request $request)
    {
        $customer = Customer::findOrFail($request->id);
        $customer->user->excluded_vat = $request->status;
        if ($customer->user->save()) {
            return 1;
        }
        return 0;
    }

    public function view_catering_plans($id)
    {
        $user = User::find(decrypt($id));

        // ola ta agorasmena plana autou tou user
        $catering_plan_purchases = CateringPlanPurchase::where('catering_plan_purchases.user_id', $user->id)
            ->select('catering_plan_purchases.id as purchase_id', 'catering_plan_purchases.catering_plan_id',
                'catering_plan_purchases.card_id', 'catering_plan_purchases.from_date', 'catering_plan_purchases.to_date', 'catering_plan_purchases.snack_quantity',
                'catering_plan_purchases.meal_quantity', 'catering_plan_purchases.price', 'catering_plan_purchases.num_of_days', 'catering_plan_purchases.created_at',
                'catering_plans.name', 'cards.rfid_no')
            ->join('catering_plans', 'catering_plan_purchases.catering_plan_id', '=', 'catering_plans.id')
            ->join('cards', 'catering_plan_purchases.card_id', '=', 'cards.id')
            ->join('organisations', 'organisations.id', '=', 'cards.organisation_id')
            ->get();


        $cards = Card::where('user_id', $user->id)
            ->select('cards.id', 'cards.rfid_no', 'cards.name', 'organisations.id as organisation_id', 'organisations.name as organisation_name')
            ->join('organisations', 'organisations.id', '=', 'cards.organisation_id')
            ->groupBy('cards.id')
            ->get();

        return view('backend.customer.customers.view_catering_plans', compact('user', 'catering_plan_purchases', 'cards'));

    }
}
