<?php

namespace App\Http\Controllers;

use App\Models\CanteenAppUser;
use App\Models\Gateways\AppViva;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use App\CanteenCustomersExport;

class CanteenCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $customers_with_no_purchase = false;
        $date = $request->date;
        $selected_organisations = [];
        $org_settings = [];

        $temp_filename = '';

        if ($request->has('organisation')) {
            $selected_organisations = $request->organisation;
        }


        $canteen_users = CanteenAppUser::join('cards', 'canteen_app_users.card_id', '=', 'cards.id')
            ->join('users', 'users.id', '=', 'canteen_app_users.user_id')->orderBy('canteen_app_users.created_at', 'desc');


        if ($request->has('search') && $request->search != null) {
            $sort_search = $request->search;

            $user_ids = $canteen_users
                ->where(function ($user) use ($sort_search) {
                    $user
                        ->where('canteen_app_users.username', 'like', '%' . $sort_search . '%')
                        ->orWhere('users.email', 'like', '%' . $sort_search . '%')
                        ->orWhere('users.name', 'like', '%' . $sort_search . '%')
                        ->orWhere('cards.rfid_no', 'like', '%' . $sort_search . '%')
                        ->orWhere('cards.rfid_no_dec', 'like', '%' . $sort_search . '%');
                })->pluck('canteen_app_users.id')->toArray();


            $canteen_users = $canteen_users->where(function ($customer) use ($user_ids) {
                $customer->whereIn('canteen_app_users.id', $user_ids);
            });
        }

        $user_ids = CanteenAppUser::join('cards', 'canteen_app_users.card_id', '=', 'cards.id');

        if ($request->has('organisation')) {
            $user_ids = $user_ids->where(function ($card) use ($selected_organisations) {
                $card->whereIn('cards.organisation_id', $selected_organisations);
            })->groupBy('canteen_app_users.id');
        }

        if ($date != null) {
            $start_date = Carbon::create(date('Y-m-d', strtotime(explode(" to ", $date)[0])))->format('Y-m-d H:i:s');
            $end_date = Carbon::create(date('Y-m-d', strtotime(explode(" to ", $date)[1])))->endOfDay()->format('Y-m-d H:i:s');

            $canteen_users = $canteen_users->where('canteen_app_users.created_at', '>=', $start_date)->where('canteen_app_users.created_at', '<=', $end_date);

        }

        $user_ids = $user_ids->pluck('canteen_app_users.id')->toArray();

        $canteen_users = $canteen_users->where(function ($canteen_user) use ($user_ids) {
            $canteen_user->whereIn('canteen_app_users.id', $user_ids);
        });

        if ($request->has('customers_with_no_purchase')) {
            $customers_with_no_purchase = true;
            $user_ids = AppViva::select('*')->pluck('user_id')->toArray();
            $canteen_users = $canteen_users->whereNotIn('canteen_app_users.id', $user_ids);
        }

        $total_customers = count($canteen_users->get());

        if (!$request->has('form_type') || $request->form_type == 'filter') {

            $canteen_users = $canteen_users->select('canteen_app_users.*', 'cards.id as card_id', 'cards.rfid_no', 'cards.rfid_no_dec', 'users.email as parent_email', 'users.name as parent_name')->paginate(15);

            return view('backend.customer.canteen_customers.index', compact('canteen_users', 'sort_search', 'total_customers', 'selected_organisations', 'date', 'customers_with_no_purchase'));

        } elseif ($request->form_type == 'export') {

            $filename = 'Canteen Customers Export ' . Carbon::now();
            $canteen_users = $canteen_users->select('canteen_app_users.*', 'cards.id as card_id', 'cards.rfid_no', 'cards.rfid_no_dec', 'users.email as parent_email', 'users.name as parent_name', 'cards.organisation_id')->get();

            return Excel::download(new CanteenCustomersExport($canteen_users), $filename . '.xlsx');


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
    //
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
     //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $id = decrypt($id);

        $canteen_user = CanteenAppUser::find($id);

        if($canteen_user != null){

            if($canteen_user->delete()){
                flash(translate('Canteen Customer deleted successfully!'))->success();
                return redirect()->route('canteen_customers.index');
            }

        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();

    }

    public function login($id)
    {

//        dd('login canteen user');
        $id = decrypt($id);
        $canteen_user = CanteenAppUser::find($id);

        auth()->guard('application')->login($canteen_user, true);

        return redirect()->route('application.home');
    }


}
