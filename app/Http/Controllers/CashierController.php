<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Models\OrganisationCanteenCashier;
use App\Models\OrganisationCashier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CashierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cashiers = User::where('user_type', 'cashier')->paginate(15);

        if ($request->has('search')) {
            $sort_search = $request->search;
            $cashiers = User::where('user_type', 'cashier');
            $cashiers = $cashiers->where('name', 'like', '%' . $sort_search . '%')->orWhere('phone', 'like', '%' . $sort_search . '%')->paginate(15);;
        }


        return view('backend.cashier.index', compact('cashiers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $organisations = Organisation::select('id', 'name')->get();

        return view('backend.cashier.create', compact('organisations'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validator($request->all())->validate();

        $cashier = new User();

        $cashier->name = $request->name . ' ' . $request->surname;

        $cashier->username = $request->username;

        $cashier->phone = $request->phone;

        $cashier->country = 'Cyprus';

        $cashier->city = $request->city;

        $cashier->user_type = 'cashier';

        $cashier->password = Hash::make($request->password);

        if ($request->has('active_cashier')) {
            $cashier->active = 1;
        } else {
            $cashier->active = 0;
        }

//        return $request->organisations;


        if ($cashier->save()) {

            foreach ($request->organisations as $organisation) {

                //create row in organisation cashier
                $organisation_cashier = new OrganisationCashier();
                $organisation_cashier->user_id = $cashier->id;
                $organisation_cashier->organisation_id = $organisation;
                $organisation_cashier->save();
            }
            flash(translate('Cashier has been inserted successfully'))->success();
        } else {
            flash(translate('Sorry! Something went wrong!'))->error();
        }

        return redirect()->route('cashiers.index');


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

        $cashier = User::findorfail($id);

        if($cashier->user_type != 'cashier'){
            return redirect()->route('cashiers.index');
        }

        $organisations = Organisation::select('id', 'name')->get();
        $my_organisations = OrganisationCashier::where('user_id', $id)->select('organisation_id')->get();

        $checked_organisations = array();

        foreach($my_organisations as $my_organisations){
            $checked_organisations[] = $my_organisations->organisation_id;
        }

//        return $checked_organisations;

        list($name, $surname) = explode(' ', $cashier->name);

        return view('backend.cashier.edit', compact('cashier', 'organisations', 'checked_organisations', 'name', 'surname'));
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
        $cashier = User::findorfail($id);

        if($cashier->user_type != 'cashier'){
            return redirect()->route('cashiers.index');
        }


        if( strlen($request->password)>0){
            $this->validator($request->all())->validate();

        }else{

            $this->validate($request, [
                'name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'username' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users')->ignore($id), // Assuming you are using the authenticated user's ID
                ],
            ]);

        }

        $cashier->name = $request->name . ' ' . $request->surname;

        $cashier->username = $request->username;

        $cashier->phone = $request->phone;

        $cashier->country = 'Cyprus';

        $cashier->city = $request->city;

        $cashier->user_type = 'cashier';

        if( strlen($request->password)>0) {

            $cashier->password = Hash::make($request->password);

        }

        if ($request->has('active_cashier')) {
            $cashier->active = 1;
        } else {
            $cashier->active = 0;
        }

        if ($cashier->save()) {

            $old_organisations = OrganisationCashier::where('user_id', $cashier->id)->get();

            foreach($old_organisations as $old){
                $old->delete();
            }

            foreach ($request->organisations as $organisation) {

                //create row in organisation cashier
                $organisation_cashier = new OrganisationCashier();
                $organisation_cashier->user_id = $cashier->id;
                $organisation_cashier->organisation_id = $organisation;
                $organisation_cashier->save();
            }

            flash(translate('Cashier has been updated successfully'))->success();


        } else {
            flash(translate('Sorry! Something went wrong!'))->error();
        }

        return redirect()->route('cashiers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $cashier = User::findorfail($id);

        if($cashier->user_type != 'cashier'){
            flash(translate('Sorry! Something went wrong!'))->error();
            return redirect()->route('cashiers.index');
        }

        if ($cashier->delete()) {

            $temp = OrganisationCashier::where('user_id', $id)->get();

            foreach ($temp as $t){
                $t->delete();
            }

            flash(translate('Cashier has been deleted successfully'))->success();
        } else {
            flash(translate('Sorry! Something went wrong!'))->error();
        }

        return redirect()->back();
    }


    public function buffet_scanning()
    {

        $organisation_id = Session::get('organisation_id');

        $organisation = Organisation::findorfail($organisation_id);

        $organisation_setting = $organisation->currentSettings();
        return view('frontend.user.cashier.buffet_scanning', compact('organisation_setting'));
    }

    public function buffet_serving($type)
    {
        return view('frontend.user.cashier.buffet_serving' , compact('type'));
    }






}
